<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\FingaIntegrationController;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\DonePayment;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\FinjaLogSettlementRecord;

class WalletSettlementFromDonePayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payment_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payment_id)
    {
        $this->payment_id = $payment_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $done_payment_shipments = DonePaymentShipment::join('shipments as s','s.id', 'done_payment_shipments.shipment_id')
        ->join('wallet_users as wu', 'wu.user_id', 's.user_id')
        ->join('finja_log_settlement_records as sac', 'done_payment_shipments.shipment_id', '=', 'sac.shipment_id')
        ->where('done_payment_shipments.done_payment_id', $this->payment_id)
        ->where('done_payment_shipments.wallet_action_bid', 1)
        ->where(function ($query) {
            $query->where('sac.wallet_settlement_updated', 0);
        })
        ->select(['done_payment_shipments.*', 'wu.user_id as user_id', 'wu.wallet_id as wallet_id', 's.tracking_number', 's.cash_handling_charges', 's.insurance_charges','s.replacement_charges','s.try_and_buy_charges','s.intercept_charges','s.nsa_osa_charges','s.esc_charges','s.return_charges'])->get();

        $successfull_record = [];

        foreach($done_payment_shipments as $dps) {
            $shipmentId = $dps->shipment_id;

            $requestPayload = [
                "client_id" => $dps->user_id,
                "wallet_id" => $dps->wallet_id,
                "reference_id" => (string) Str::uuid(),
                "shipment_id" =>  $dps->tracking_number,
                "amount" => $dps->amount,
                "charges" => [
                    'arrival_charges' =>  intval($shipment->weight_charges),
                    'fuel_surcharge' =>  intval($shipment->fuel_surcharge),
                    'cash_handling_charges' => intval($dps->cash_handling_charges),
                    'insurance_charges' => intval($dps->insurance_charges),
                    'replacement_charges' => intval($dps->replacement_charges), 
                    'try_and_buy_charges' => intval($dps->try_and_buy_charges),
                    'intercept_charges' => intval($dps->intercept_charges),
                    'non_service_area_charges' => intval($dps->nsa_osa_charges),
                    'esc_charges' => intval($dps->esc_charges),
                    'reverse_pickup_charges' => 0,
                    'return_charges' => intval($dps->return_charges),
                    'gst_charges' => intval($dps->gst),
                    'sms_charges' => intval($dps->sms_charges)
                ]
            ];

            try {

                $api = config('app.FINGA_URL');
                $token = FingaIntegrationController::getToken($api);
    
                if($token) {
                    $response = Http::withHeaders([
                        'accept' => 'application/json',
                        'Authorization' => "Bearer " . $token,
                    
                    ])->post($api.'transactions/log/settlement', $requestPayload);
        
                    FingaIntegrationController::apiLog('settlement-request', 1, $requestPayload ,$shipmentId);
        
                    if($response->successful()) {
                        
                        $body = $response->getBody();
                        $body = json_decode($body);
        
                        FingaIntegrationController::apiLog('settlement-response', 'success', $body ,$shipmentId);
    
                        FinjaLogSettlementRecord::updateOrCreate(
                            // Condition to find the record
                            ['shipment_id' => $shipmentId],
                            // Data to update or create
                            [
                                'shipment_id' => $shipmentId,
                                'wallet_settlement_updated' => true,
                                'wallet_settlement_updated_at' => Carbon::now(),
                            ]
                        );

                        DonePaymentShipment::where('id', $dps->id)->update(['wallet_action_bid' => 3 ]);

                        $successfull_record[] = $dps->id;
        
                    } else {
                        $body = $response->getBody();
                        $body = json_decode($body);
                        FingaIntegrationController::apiLog('settlement-response', 'error', $body ,$shipmentId);
                    }
                }
    
            } catch (\Throwable $th) {
                
                $errorBody = [
                    'error' => $th->getMessage(),
                    'code' => $th->getCode()
                ];
                FingaIntegrationController::apiLog('settlement-response', 'exception', $errorBody, $shipmentId);
            }

        }

        $actual_count = $done_payment_shipments->count();
        if($actual_count > 0 &&  count($successfull_record) == $actual_count ) {

            DonePayment::where('id',  $this->payment_id)->update(['status' => 1, 'status_updated_at' => Carbon::now()]);

        }
    }
}
