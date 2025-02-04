<?php

namespace App\Jobs;

use App\Http\Models\PendingPaymentShipment;
use App\Http\Traits\FinSurgentLogTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\FingaIntegrationController;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentServicesCharges;
use App\ShipmentAdditionalCharges;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\FinjaLogSettlementRecord;
use Illuminate\Support\Str;
use App\Jobs\CODAmountChangeSendToWallet;

class ShipmentStatusSharingWithWallet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use FinSurgentLogTrait;

    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        //
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        $status_mapping = [
            5 => [
                'code' => 'S-OD',
                'name' => 'Shipment - Out for Delivery',
            ],
            8 => [
                'code' => 'S-AF',
                'name' => 'Shipment - Delivery Unsuccessful',
            ],
            13 => [
                'code' => 'S-RA',
                'name' => 'Shipment - Re-Attempt',
            ],
            14 => [
                'code' => 'S-DE',
                'name' => 'Shipment - Delivered',
            ],
            18 => [
                'code' => 'S-LO',
                'name' => 'Shipment - Lost',
            ],
            20 => [
                'code' => 'RT-CO',
                'name' => 'Return - Confirm',
            ],
            25 => [
                'code' => 'RT-DS',
                'name' => 'Return - Delivered to Shipper',
            ],
            36 => [
                'code' => 'TB-DE',
                'name' => 'Try & Buy - Delivered',
            ],
            37 => [
                'code' => 'TB-PD',
                'name' => 'Try & Buy - Partial Delivered',
            ],
            38 => [
                'code' => 'TB-DS',
                'name' => 'Try & Buy - Delivered to Shipper',
            ],
            60 => [
                'code' => 'RU-CS',
                'name' => 'Return - Unable to Return',
            ],
        ];
        $shipment_id = $this->data['shipment_id'];
        try {

            $api = config('app.FINGA_URL');
            $token = FingaIntegrationController::getToken($api);
            
            $status =  $this->data['status'];
            $tracking_number = $this->data['tracking_number'];
            $status_code = $status_mapping[$status]['code'];
            $status_name = $status_mapping[$status]['name'];

            $shipment_log_not_sent = Shipment::leftJoin('finja_log_settlement_records as sac', 'shipments.id', '=', 'sac.shipment_id')
                ->join('wallet_users as u', function ($join) {
                    $join->on('u.user_id', '=', 'shipments.user_id')
                    ->where('u.substitute_user_id', '0');
                })
                ->where(function ($query) {
                    $query->whereNull('sac.id')
                        ->orWhere('sac.wallet_log_updated', 0);
                })
                ->select(['shipments.*', 'u.wallet_id'])
                ->first();

            if(!empty($shipment_log_not_sent)){
                $requestPayload = [
                    "client_id" => $shipment_log_not_sent->user_id,
                    "wallet_id" => $shipment_log_not_sent->wallet_id,
                    "reference_id" => (string)Str::uuid(),
                    "shipment_id" => $shipment_log_not_sent->tracking_number,
                    "amount" => $shipment_log_not_sent->amount,
                    "order_created_date" => $shipment_log_not_sent->created_at,
                ];
                $this->arrival_shipment_logs($requestPayload, null,$shipment_log_not_sent->id);
            }

            $cod_charges = FinjaLogSettlementRecord::where('shipment_id', $shipment_id)->first();
            if($cod_charges && $cod_charges->logged_cod_amount !=  $shipment_log_not_sent->amount) {

                $data = [
                    'shipment_id' => $shipment_id,
                    'tracking_number' => $shipment_log_not_sent->tracking_number,
                    'client_id' =>  $shipment_log_not_sent->user_id,
                    'wallet_id' => $shipment_log_not_sent->wallet_id,
                    'amount' => $shipment_log_not_sent->amount
                ];
                CODAmountChangeSendToWallet::dispatch($data);

            }

            $requestPayload = [
                'shipment_id' => $tracking_number,
                'status_code' => $status_code,
                'status_name' =>  $status_name
            ];
            if($token) {
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'Authorization' => "Bearer " . $token,
                
                ])->post($api.'shipments/update/', $requestPayload);
    
                FingaIntegrationController::apiLog('shipment-status-request', 1, $requestPayload ,$shipment_id);
    
                if($response->successful()) { 
                    
                    $body = $response->getBody();
                    $body = json_decode($body);
    
                    FingaIntegrationController::apiLog('shipment-status-response', 'success', $body ,$shipment_id);
    
                } else {
                    $body = $response->getBody();
                    $body = json_decode($body);
                    FingaIntegrationController::apiLog('shipment-status-response', 'error', $body ,$shipment_id);
                }
            }

        } catch (\Throwable $th) {
            // Log exception details
            $errorBody = [
                'error' => $th->getMessage(),
                'code' => $th->getCode()
            ];
            FingaIntegrationController::apiLog('shipment-status-response', 'exception', $errorBody, $shipment_id);
        }
    }
}
