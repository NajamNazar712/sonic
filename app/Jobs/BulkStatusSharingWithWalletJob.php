<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\FingaIntegrationController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentServicesCharges;
use App\Jobs\CODAmountChangeSendToWallet;
use App\Models\FinjaLogSettlementRecord;
use Illuminate\Support\Str;
use App\Http\Traits\FinSurgentLogTrait;
use App\Models\StatusSharingWithWallet;


class BulkStatusSharingWithWalletJob implements ShouldQueue
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
        //$this->queue = 'shipment_status_sharing_with_wallet_bulk';
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
            36 => [
                'code' => 'TB-DE',
                'name' => 'Try & Buy - Delivered',
            ],
            37 => [
                'code' => 'TB-PD',
                'name' => 'Try & Buy - Partial Delivered',
            ],
            
            30 => [
                'code' => 'RP-EC',
                'name' => 'Replacement - Collected',
            ],
            21 => [
                'code' => 'RT-IT',
                'name' => 'Return - In Transit',
            ],
            22 => [
                'code' => 'RT-AOC',
                'name' => 'Return - Arrived at Origin',
            ],
            23 => [
                'code' => 'RT-DP',
                'name' => 'Return - Dispatched',
            ],
            24 => [
                'code' => 'RT-AF',
                'name' => 'Return - Delivery Unsuccessful',
            ],
            25 => [
                'code' => 'RT-DS',
                'name' => 'Return - Delivered to Shipper',
            ],

            44 => [
                'code' => 'R-RE',
                'name' => 'Return - Rider Exchange',
            ],
            47 => [
                'code' => 'R-NA',
                'name' => 'Return - Not Attempted',
            ],
            48 => [
                'code' => 'R-OH',
                'name' => 'Return - On Hold',
            ],
            57 => [
                'code' => 'RN-S',
                'name' => 'Return Note Shifted',
            ],
            60 => [
                'code' => 'RU-CS',
                'name' => 'Return - Unable to Return',
            ],
            75 => [
                'code' => 'RT-MR',
                'name' => 'Return - Misrouted',
            ],
            76 => [
                'code' => 'RT-MF',
                'name' => 'Return - Misroute Forwarded',
            ],
            77 => [
                'code' => 'RT-WM',
                'name' => 'Return - Without Manifest',
            ],
        ];
       
        try{

            $payload = []; 
            foreach ($this->data as $d) {
                $shipment_id = $d['shipment_id'];

                $shipment_log_not_sent = Shipment::leftJoin('finja_log_settlement_records as sac', 'shipments.id', '=', 'sac.shipment_id')
                ->join('wallet_users as u', function ($join) {
                    $join->on('u.user_id', '=', 'shipments.user_id')
                    ->where('u.substitute_user_id', '0');
                })
                ->where(function ($query) {
                    $query->whereNull('sac.id')
                        ->orWhere('sac.wallet_log_updated', 0);
                })->where('shipments.id', $shipment_id)
                ->select(['shipments.*', 'u.wallet_id'])
                ->first();

                if(!empty($shipment_log_not_sent)){
                    $logPayload = [
                        "client_id" => $shipment_log_not_sent->user_id,
                        "wallet_id" => $shipment_log_not_sent->wallet_id,
                        "reference_id" => (string)Str::uuid(),
                        "shipment_id" => $shipment_log_not_sent->tracking_number,
                        "amount" => $shipment_log_not_sent->amount,
                        "order_created_date" => $shipment_log_not_sent->created_at,
                    ];
                    $this->arrival_shipment_logs($logPayload, null,$shipment_log_not_sent->id,null);
                }

                $cod_charges = FinjaLogSettlementRecord::where('shipment_id', $shipment_id)
                ->first();
                $shipment = Shipment::find($shipment_id);
                if($cod_charges && $cod_charges->logged_cod_charges !=  $shipment->amount) {

                    $data = [
                        'shipment_id' => $shipment_id,
                        'tracking_number' => $shipment->tracking_number,
                        'client_id' =>  $shipment->user_id,
                        'wallet_id' => $shipment->user->wallet->wallet_id,
                        'amount' => $shipment->amount
                    ];
                    CODAmountChangeSendToWallet::dispatch($data);
                }


                $status = $d['status_id'];
                $tracking_number = $d['tracking_number'];
                
                $status_code = $status_mapping[$status]['code'];
                $status_name = $status_mapping[$status]['name'];
            
                $payload[] = [
                    'unique_id' => $d['id'],
                    'shipment_id' => $tracking_number,
                    'status_code' => $status_code,
                    'status_name' =>  $status_name
                ];
            
            }
            
            $api = config('app.FINGA_URL');
            $token = FingaIntegrationController::getToken($api);
            if($token) {
                $request_id = FingaIntegrationController::apiLog(15, 1, $payload ,null);
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'Authorization' => "Bearer " . $token,
                
                ])->post($api.'shipments/update/bulk', $payload);

                if($response->successful()) { 
                    
                    $body = $response->getBody();
                    $body = json_decode($body, true);
                    foreach ($body as $b) {
                        $uniqueId = $b['unique_id'];
                        $status = $b['status'];
                
                        // Find the related record in the database
                        $record = StatusSharingWithWallet::find($uniqueId);
                
                        if ($record) {
                            // Update is_send based on the status
                            $record->update([
                                'is_send' => $status == 'success' ? 1 : 0
                            ]);
                        }

                    }
                    FingaIntegrationController::apiLog(16, 'success',  $body ,null,$request_id);
    
                } else {
                    $body = $response->getBody();
                    $body = json_decode($body);
                    FingaIntegrationController::apiLog(16, 'error', $body ,null,$request_id);
                }
            }

        } catch (\Throwable $th) {
            // Log exception details
            $errorBody = [
                'error' => $th->getMessage(),
                'code' => $th->getCode()
            ];
            FingaIntegrationController::apiLog(16, 'exception', $errorBody, null,$request_id);
        }
       
    }
}
