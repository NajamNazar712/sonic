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
use Illuminate\Support\Facades\DB;
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
        ->leftjoin('shipment_additional_charges as sc', 'sc.shipment_id', 's.id')
        ->leftjoin('shipment_services_charges as ssc', 'ssc.shipment_id', 's.id')
        ->leftjoin('wallet_users as wu', function ($join) {
            $join->on('wu.user_id', '=', 's.user_id')
                ->where('wu.substitute_user_id', '0');
        })
        ->leftjoin('finja_log_settlement_records as sac', 'done_payment_shipments.shipment_id', '=', 'sac.shipment_id')
        ->where('done_payment_shipments.done_payment_id', $this->payment_id)
        ->whereIn('done_payment_shipments.wallet_action_bid', [1,2])
        // ->where(function ($query) {
        //     $query->where('sac.wallet_settlement_updated', 0);
        // })
        ->select(['done_payment_shipments.*', 'wu.user_id as user_id', 'wu.wallet_id as wallet_id', 's.tracking_number', 's.cash_handling_charges', 's.insurance_charges','s.replacement_charges','s.try_and_buy_charges','s.intercept_charges','s.nsa_osa_charges','s.esc_charges','s.return_charges', 'sac.wallet_settlement_updated', 's.weight_charges', 's.fuel_surcharge','sc.faf_charges', 'ssc.reverse_pickup_charges'])->get();
        //Log::info($done_payment_shipments);
        $successfull_record = [];
        $api = config('app.FINGA_URL');
        $token = FingaIntegrationController::getToken($api);
        foreach($done_payment_shipments as $dps) {
            $shipmentId = $dps->shipment_id;
            if($dps->wallet_action_bid == 1 && $dps->wallet_settlement_updated == 0) {
                $requestPayload = [
                    "client_id" => $dps->user_id,
                    "wallet_id" => $dps->wallet_id,
                    "reference_id" => $dps->id,
                    "shipment_id" =>  $dps->tracking_number,
                    "amount" => $dps->amount,
                    "charges" => [
                        'faf_charges' =>  intval($dps->faf_charges),
                        'weight_charges' =>  intval($dps->weight_charges),
                        'fuel_surcharge' =>  intval($dps->fuel_surcharge),
                        'cash_handling_charges' => intval($dps->cash_handling_charges),
                        'insurance_charges' => intval($dps->insurance_charges),
                        'replacement_charges' => intval($dps->replacement_charges), 
                        'try_and_buy_charges' => intval($dps->try_and_buy_charges),
                        'intercept_charges' => intval($dps->intercept_charges),
                        'non_service_area_charges' => intval($dps->nsa_osa_charges),
                        'esc_charges' => intval($dps->esc_charges),
                        'reverse_pickup_charges' => intval($dps->reverse_pickup_charges),
                        'return_charges' => intval($dps->return_charges),
                        'gst_charges' => intval($dps->gst),
                        'sms_charges' => intval($dps->sms_charges)
                    ]
                ];
                $request_nature = 'settlement-request';
                $response_nature = 'settlement-response';
                $api .= 'transactions/log/settlement';
                $data = [
                    'shipment_id' => $dps->shipment_id,
                    'wallet_settlement_updated' => true,
                    'wallet_settlement_updated_at' => Carbon::now(),
                ];

            } else {
                $requestPayload = [
                    "client_id" => $dps->user_id,
                    "wallet_id" => $dps->wallet_id,
                    "reference_id" => (string) Str::uuid(),
                    "shipment_id" =>  $dps->tracking_number,
                    "amount" => intval($dps->payable),
                ];
                $request_nature = 'adjustment-request';
                $response_nature = 'adjustment-response';
                $api .= 'transactions/log/adjustment';
                $data = [
                    'shipment_id' => $dps->shipment_id,
                    'wallet_adjustment_updated' => true,
                    'wallet_adjustment_updated_at' => Carbon::now(),
                ];
            }

            try {

                if($token) {
                    $response = Http::withHeaders([
                        'accept' => 'application/json',
                        'Authorization' => "Bearer " . $token,
                    
                    ])->post($api, $requestPayload);
        
                    FingaIntegrationController::apiLog($request_nature, 1, $requestPayload ,$shipmentId);
        
                    if($response->successful()) {
                        
                        $body = $response->getBody();
                        $body = json_decode($body);
        
                        FingaIntegrationController::apiLog($response_nature, 'success', $body ,$shipmentId);

                        FinjaLogSettlementRecord::updateOrCreate(
                            // Condition to find the record
                            ['shipment_id' => $shipmentId],
                            // Data to update or create
                            $data
                        );
                        DonePaymentShipment::where('id', $dps->id)->update(['wallet_action_bid' => 3]);

                        $successfull_record[] = $dps->id;
        
                    } else {
                        $body = $response->getBody();
                        $body = json_decode($body);
                        FingaIntegrationController::apiLog($response_nature, 'error', $body ,$shipmentId);
                    }
                }
    
            } catch (\Throwable $th) {
                
                $errorBody = [
                    'error' => $th->getMessage(),
                    'code' => $th->getCode()
                ];
                FingaIntegrationController::apiLog($response_nature, 'exception', $errorBody, $shipmentId);
            }

        }

        $actual_count = $done_payment_shipments->count();
        if($actual_count > 0 &&  count($successfull_record) == $actual_count ) {

            DonePayment::where('id',  $this->payment_id)->update(['status' => 1, 'status_updated_at' => Carbon::now()]);

        }
    }
}
