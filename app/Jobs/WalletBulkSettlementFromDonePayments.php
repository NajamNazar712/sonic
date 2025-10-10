<?php

namespace App\Jobs;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\ShipmentServicesCharges;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\FingaIntegrationController;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\DonePayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\FinjaLogSettlementRecord;
use App\Http\Controllers\ShipmentsPaymentJourneyController;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\CRM\CrmRequestTagging;
use App\Http\Controllers\CRM\CRMCommentController;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipment;
use App\ShipmentAdditionalCharges;
use App\Jobs\WalletLogDispatchJob;

class WalletBulkSettlementFromDonePayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payment_id;
    protected $id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payment_id,$id)
    {
        $this->queue = 'wallet_settlement_form_done_payment_bulk';
        $this->payment_id = $payment_id;
        $this->id = $id;
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
            ->leftJoin('wallet_users as wu', function ($join) {
                $join->on('wu.user_id', '=', 's.user_id')
                    ->whereRaw('wu.id = (SELECT MAX(id) FROM wallet_users WHERE user_id = s.user_id AND substitute_user_id = 0)');
            })
            ->leftjoin('finja_log_settlement_records as sac', 'done_payment_shipments.shipment_id', '=', 'sac.shipment_id')
            ->where('done_payment_shipments.done_payment_id', $this->payment_id)
            ->whereIn('done_payment_shipments.wallet_action_bid', [0,1,2])
            ->select(['done_payment_shipments.*', 'wu.user_id as user_id', 'wu.wallet_id as wallet_id', 's.tracking_number', 's.cash_handling_charges', 's.insurance_charges','s.replacement_charges','s.try_and_buy_charges','s.intercept_charges','s.nsa_osa_charges','s.esc_charges','s.return_charges', 'sac.wallet_settlement_updated', 's.weight_charges', 's.fuel_surcharge','sc.faf_charges', 'ssc.reverse_pickup_charges', 'sc.wallet_charges', 'sac.wallet_log_charges_updated','sac.wallet_log_updated','s.packaging_material_charges'])
            ->get();
        
        $api = config('app.FINGA_URL');
        $check_shipment = array();
        $done_payment_shipments->chunk(100)->each(function ($chunkedShipments) use (&$check_shipment, $api) {

            $settlementPayload = [];
            $logChargePayload = [];
            $batch_id = (string)Str::uuid();
            $id = $this->id;
            $payment_id = $this->payment_id;
            foreach ($chunkedShipments as $dps) {

                if (!isset($check_shipment[$dps->shipment_id])) {
                    $check_shipment[$dps->shipment_id] = true;

                    $LogChargeStatus = true;
                    $send_request = true;
                    $success = false;
                    $shipmentId = $dps->shipment_id;
                    $shipment = Shipment::find($shipmentId);
                    $service_charges = ShipmentServicesCharges::where('shipment_id', $shipmentId);
                    if ($service_charges->exists()) {
                        $service_charges = $service_charges->first();
                        $service_charges = $service_charges->reverse_pickup_charges;
                    } else {
                        $service_charges = 0;
                    }
                    if ($dps->wallet_action_bid == 1 && $dps->wallet_settlement_updated == 1) {
                        $dps->wallet_action_bid = self::run_log_and_settle($dps, $shipment);
                    }

                    if ($dps->wallet_action_bid == 1 && $dps->wallet_settlement_updated == 0) {

                        // $logCharged = !empty($dps->wallet_log_charges_updated) ? $dps->wallet_log_charges_updated : 0;
                        $logCharged = FinjaLogSettlementRecord::where('shipment_id', $shipmentId)->first();
                        $logCharged2 = !empty($logCharged) ? $logCharged->wallet_log_charges_updated : 0;

                        $check_arrival_paid_done = DonePaymentShipment::where('shipment_id', $shipmentId)->where('type', 3)->exists();
                        $check_arrival_paid_pending = PendingPaymentShipment::where('shipment_id', $shipmentId)->where('type', 3)->exists();
                        if ($logCharged2 == 0 && ($check_arrival_paid_done || $check_arrival_paid_pending)) {
                            $LogChargeStatus = false;
                        }


                        $charges = [];
                        if ($shipment->packaging_material_request == 1) {
                            if ($dps->type == 0) {
                                $charges = [
                                    'packaging_material_charges' => floatval($shipment->packaging_material_charges),
                                    'gst_charges' => floatval($dps->gst),
                                    'sms_charges' => floatval($dps->sms_charges),
                                ];
                            }else{
                                $charges = [
                                    'gst_charges' => floatval($dps->gst),
                                    'sms_charges' => floatval($dps->sms_charges),
                                ];
                            }
                        } else {
                            if ($dps->type == 0) {
                                $charges = [
                                    'faf_charges' => $LogChargeStatus ? floatval($dps->faf_charges) : 0,
                                    'weight_charges' => $LogChargeStatus ? floatval($dps->weight_charges) : 0,
                                    'fuel_surcharge' => $LogChargeStatus ? floatval($dps->fuel_surcharge) : 0,
                                    'cash_handling_charges' => floatval($dps->cash_handling_charges),
                                    'insurance_charges' => floatval($dps->insurance_charges),
                                    'replacement_charges' => floatval($dps->replacement_charges),
                                    'try_and_buy_charges' => floatval($dps->try_and_buy_charges),
                                    'intercept_charges' => floatval($dps->intercept_charges),
                                    'non_service_area_charges' => floatval($dps->nsa_osa_charges),
                                    'esc_charges' => floatval($dps->esc_charges),
                                    'gst_charges' => floatval($dps->gst),
                                    'sms_charges' => floatval($dps->sms_charges),
                                    'reverse_pickup_charges' => floatval($service_charges),
                                    'with_holding_tax' => floatval($dps->wht),
                                    'cod_sst' => floatval($dps->cod_sst)
                                ];
                            } elseif ($dps->type == 1) {
                                $charges = [
                                    'faf_charges' => $LogChargeStatus ? floatval($dps->faf_charges) : 0,
                                    'weight_charges' => $LogChargeStatus ? floatval($dps->weight_charges) : 0,
                                    'fuel_surcharge' => $LogChargeStatus ? floatval($dps->fuel_surcharge) : 0,
                                    'insurance_charges' => floatval($dps->insurance_charges),
                                    'return_charges' => floatval($dps->return_charges),
                                    'intercept_charges' => floatval($dps->intercept_charges),
                                    'non_service_area_charges' => floatval($dps->nsa_osa_charges),
                                    'gst_charges' => floatval($dps->gst),
                                    'sms_charges' => floatval($dps->sms_charges),
                                ];
                            }
                        }

                        $settlementPayload[$shipmentId] = [
                            "client_id" => $dps->user_id,
                            "wallet_id" => $dps->wallet_id,
                            "reference_id" => $dps->id,
                            "shipment_id" => $dps->tracking_number,
                            "amount" => $dps->type == 0 ? $dps->amount : 0,
                            "charges" => $charges,
                            'wallet_log_updated' => $dps->wallet_log_updated,
                            'dps_id' => $dps->id,
                            'dps_type' => $dps->type
                        ];

                    } elseif ($dps->wallet_action_bid == 2) {
                        $type = FinjaLogSettlementRecord::check_wallet_charges_type($shipmentId);
                        $payable = $dps->payable;
                        if ($type) {
                            $wallet_charges = ShipmentAdditionalCharges::fetch_wallet_charges($shipmentId);
                            $payable = $dps->payable - $wallet_charges;
                        }
                        $requestPayload = [
                            "client_id" => $dps->user_id,
                            "wallet_id" => $dps->wallet_id,
                            "reference_id" => $dps->id,
                            "shipment_id" => $dps->tracking_number,
                            "amount" => floatval($payable),
                        ];

                        if ($payable == 0) {
                            $send_request = false;
                        }

                        try {
                            $token = FingaIntegrationController::getToken($api);
                            if ($token && $send_request) {
                                FingaIntegrationController::apiLog(9, 1, $requestPayload, $shipmentId);
                                $response = Http::withHeaders([
                                    'accept' => 'application/json',
                                    'Authorization' => "Bearer " . $token,

                                ])->connectTimeout(120)->timeout(120)->post($api . 'transactions/log/adjustment', $requestPayload);
                                if ($response->successful()) {

                                    $body = $response->getBody();
                                    $body = json_decode($body);
                                    $success = true;
                                    FingaIntegrationController::apiLog(10, 'success', $body, $shipmentId);

                                } else {
                                    $body = $response->getBody();
                                    $body = json_decode($body, true);
                                    if (isset($body['error']) && str_contains($body['error'], 'Original transaction not found.') && $dps->wallet_log_updated == 1) {
                                        $success = true;
                                        FingaIntegrationController::apiLog(10, 'success (duplicate ignored)', $body, $shipmentId);
                                    } else {
                                        FingaIntegrationController::apiLog(10, 'error', $body, $shipmentId);
                                    }
                                }
                            }

                            if ($success || !$send_request) {
                                FinjaLogSettlementRecord::updateOrCreate(
                                // Condition to find the record
                                    ['shipment_id' => $shipmentId],
                                    // Data to update or create
                                    [
                                        'shipment_id' => $shipmentId,
                                        'wallet_adjustment_updated' => true,
                                        'wallet_adjustment_updated_at' => Carbon::now(),
                                    ]
                                );
                                self::updateShipmentPaymentStatus($shipmentId, $dps->id, $dps->type, $id, $payment_id);
                            }

                        } catch (\Throwable $th) {

                            $errorBody = [
                                'error' => $th->getMessage(),
                                'code' => $th->getCode()
                            ];
                            FingaIntegrationController::apiLog(10, 'exception', $errorBody, $shipmentId);
                        }

                    } elseif ($dps->wallet_action_bid == 0) {

                        $logChargePayload[$shipmentId] = [
                            "client_id" => $dps->user_id,
                            "wallet_id" => $dps->wallet_id,
                            "shipment_id" => $dps->tracking_number,
                            "charges" => [
                                'faf_charges' => floatval($dps->faf_charges),
                                'weight_charges' => floatval($dps->weight_charges),
                                'fuel_surcharge' => floatval($dps->fuel_surcharge),
                                'arrival_charges_gst' => floatval($dps->gst),
                            ],
                            'dps_id' => $dps->id,
                            'dps_type' => $dps->type
                        ];

                    }
                }
            }
            if(!empty($logChargePayload)) {
                self::run_log_charge_api($logChargePayload, $api, $batch_id, $id, $payment_id);
            }
            if(!empty($settlementPayload)) {
                self::run_settlement_api($settlementPayload, $api, $batch_id, $id, $payment_id);
            }
        });


        if (!DonePaymentShipment::where('done_payment_id',  $this->payment_id)
            ->whereIn('wallet_action_bid', [0,1,2])
            ->exists()) {
            DonePayment::where('id',  $this->payment_id)
                ->update([
                    'status' => 1,
                    'status_updated_at' => Carbon::now()
                ]);
        }else{
            DonePayment::where('id',  $this->payment_id)
                ->update([
                    'status' => 3,
                    'status_updated_at' => Carbon::now()
                ]);
        }
    }

    public static function run_log_and_settle($dps,$shipment){

        $api = config('app.FINGA_URL');
        $token = FingaIntegrationController::getToken($api);
        $log_bid = AdminFinanceController::isWalletLogUpdated($dps->shipment_id);
        $pending_logs = [];
        if(!$log_bid) {
            $cod_amount = $shipment->amount;
            $status_array = [14, 25, 31, 38, 37, 18, 20];
            if(in_array($shipment->shipper_status_id, $status_array) && $dps->type == 2) {
                $cod_amount = 0;
            }
            $pending_logs[$dps->shipment_id] = [
                "shipmentId" => $shipment->id,
                "wallet_id" => $shipment->user->wallet->wallet_id,
                "client_id" => $shipment->user->id,
                "reference_id" => (string) Str::uuid(),
                "shipment_id" => $shipment->tracking_number,
                "amount" => $cod_amount ,
                "order_created_date" => $shipment->created_at,
            ];
        }
        if($dps->type == 3) {
            $finja_status = 0;
        } elseif($dps->type == 0 || $dps->type == 1 ) {
            $settlement_bid = AdminFinanceController::isWalletSettlementUpdated($dps->shipment_id);
            if(!$log_bid) {
                $finja_status = 1;
            } elseif($settlement_bid) {
                $finja_status = 2;
            } else {
                $finja_status = 1;
            }
        } elseif($dps->type == 2) {
            $finja_status = 2;
        }

        WalletLogDispatchJob::dispatch($pending_logs);
        return $finja_status;
    }

    public static function run_settlement_api($settlementPayload, $api, $batch_id, $id, $payment_id){

        try {
            $token = FingaIntegrationController::getToken($api);
            
            if ($token) {
                $walletLogUpdates = [];
                $shipmentIds = [];
                $settlement = []; 
                foreach ($settlementPayload as $shipment_id => $payload) {

                    $walletLogUpdates[$shipment_id] = $payload['wallet_log_updated'];
                    //$shipmentIds[$payload['shipment_id']] = $shipment_id;
                    $shipmentIds[$payload['shipment_id']] = [
                        'shipment_id' => $shipment_id,
                        'dps_id' => $payload['dps_id'],
                        'dps_type' => $payload['dps_type']
                    ];
                    unset($settlementPayload[$shipment_id]['wallet_log_updated'],$settlementPayload[$shipment_id]['dps_id'],$settlementPayload[$shipment_id]['dps_type']);

                    FingaIntegrationController::apiLog(21, 1, $payload, $shipment_id, null, $batch_id);
                }
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'Authorization' => "Bearer " . $token,

                ])->connectTimeout(120)->timeout(120)->post($api.'transactions/log/settlement/bulk', $settlementPayload);
                if ($response->successful()) {

                    $body = $response->getBody();
                    $body = json_decode($body, true);

                    foreach ($body as $b) {

                        if(isset($b['shipment_id'])) {

                            $tracking_number = $b['shipment_id'];
                            $shipmentData = $shipmentIds[$tracking_number];
                            $shipmentId = $shipmentData['shipment_id'];
                            $dpsId = $shipmentData['dps_id'];
                            $dpsType = $shipmentData['dps_type'];
                            //$shipment = Shipment::where('tracking_number', $tracking_number)->first();
                            
                            if($b['status'] == 'success' || ($b['status'] == 'error' && str_contains($b['message'], 'Original transaction not found.') && $walletLogUpdates[$shipmentId] == 1) ) {
                                FingaIntegrationController::apiLog(22, 'success', $b, $shipmentId, null, $batch_id); 
                                $settlement[] = $shipmentId;
                                self::updateShipmentPaymentStatus($shipmentId, $dpsId, $dpsType, $id, $payment_id);
                            } else {
                                FingaIntegrationController::apiLog(22, 'error', $b, $shipmentId, null, $batch_id);
                            }
                        }
                    }

                } else {
                    $body = $response->getBody();
                    $body = json_decode($body,true);
                    FingaIntegrationController::apiLog(22, 'error', $body, null, null, $batch_id);
                }

                if(!empty($settlement)) {
                    FinjaLogSettlementRecord::whereIn('shipment_id', $settlement)
                    ->update([
                        'wallet_settlement_updated' => true,
                        'wallet_settlement_updated_at' => Carbon::now(),
                    ]);
                }
            }

        } catch (\Throwable $th) {

            $errorBody = [
                'error' => $th->getMessage(),
                'code' => $th->getCode()
            ];
            FingaIntegrationController::apiLog(22, 'exception', $errorBody, null, null, $batch_id);
        }
    }

    public static function run_log_charge_api($logChargePayload, $api, $batch_id, $id, $payment_id){

        try {
            $token = FingaIntegrationController::getToken($api);
            if ($token) {
                $shipmentIds = [];
                $logCharge = []; 
                foreach ($logChargePayload as $shipment_id => $payload) {
//                    $shipmentIds[$payload['shipment_id']] = $shipment_id;

                    $shipmentIds[$payload['shipment_id']] = [
                        'shipment_id' => $shipment_id,
                        'dps_id' => $payload['dps_id'],
                        'dps_type' => $payload['dps_type']
                    ];
                    unset($logChargePayload[$shipment_id]['dps_id'],$logChargePayload[$shipment_id]['dps_type']);
                    FingaIntegrationController::apiLog(19, 1, $payload, $shipment_id, null, $batch_id);
                }
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'Authorization' => "Bearer " . $token,

                ])->connectTimeout(120)->timeout(120)->post($api.'transactions/log/charge/bulk', $logChargePayload);
                if ($response->successful()) {

                    $body = $response->getBody();
                    $body = json_decode($body, true);

                    foreach ($body as $b) {

                        if(isset($b['shipment_id'])) {

                            $tracking_number = $b['shipment_id'];
                            //$shipment = Shipment::where('tracking_number', $tracking_number)->first();
                            $shipmentData = $shipmentIds[$tracking_number];
                            $shipmentId = $shipmentData['shipment_id'];
                            $dpsId = $shipmentData['dps_id'];
                            $dpsType = $shipmentData['dps_type'];

                            if($b['status'] == 'success' ) {
                                FingaIntegrationController::apiLog(20, 'success', $b, $shipmentId, null, $batch_id); 
                                $logCharge[] = $shipmentId;
                                self::updateShipmentPaymentStatus($shipmentId, $dpsId, $dpsType, $id, $payment_id);
 
                            } else {
                                FingaIntegrationController::apiLog(20, 'error', $b, $shipmentId, null, $batch_id);
                            }
                        }

                    }
                } else {
                    $body = $response->getBody();
                    $body = json_decode($body,true);
                    FingaIntegrationController::apiLog(20, 'error', $body , null, null, $batch_id);
                    
                }

                if(!empty($logCharge)) {
                    FinjaLogSettlementRecord::whereIn('shipment_id', $logCharge)
                    ->update([
                        'wallet_log_charges_updated' => true,
                        'wallet_log_charges_updated_at' => Carbon::now(),
                    ]);
                }
            }
        } catch (\Throwable $th) {

            $errorBody = [
                'error' => $th->getMessage(),
                'code' => $th->getCode()
            ];
            FingaIntegrationController::apiLog(20, 'exception', $errorBody, null, null, $batch_id);
        }
     
    }

    public static function updateShipmentPaymentStatus($shipmentId, $dpsId, $dpsType, $id, $payment_id)
    {
      
        DonePaymentShipment::where('id', $dpsId)->update(['wallet_action_bid' => 3]);
        
        switch ($dpsType) {
            case 1:
                Shipment::where('id', $shipmentId)->update(['payment_status_id' => 7]);
                ShipmentsPaymentJourneyController::add($shipmentId, 7, $id, '', $payment_id);
                break;

            case 3:
                Shipment::where('id', $shipmentId)->update(['payment_status_id' => 12]);
                ShipmentsPaymentJourneyController::add($shipmentId, 12, $id, '', $payment_id);
                break;

            default:
                Shipment::where('id', $shipmentId)->update(['payment_status_id' => 3]);
                ShipmentsPaymentJourneyController::add($shipmentId, 3, $id, '', $payment_id);

                //---------x-----------x-------------
                // Start Auto Close Complaints
                $crm_request = CrmRequest::where('shipment_id', $shipmentId)->where('status_id', 2)->first();

                if ($crm_request) {
                    $shipper = Shipment::where('id', $shipmentId)->select('user_id')->first();
                    $shipperName = User::find($shipper->user_id)->name;

                    if ($crm_request->status_id == 2) { // If CRM request is in process
                        CrmRequest::where('id', $crm_request->id)->update(['status_id' => 4]); // Closed status
                        CrmRequestStatusHistory::create([
                            'crm_request_id' => $crm_request->id,
                            'status_id' => 4,
                            'agent_id' => 346
                        ]);

                        CrmRequestTagging::where('crm_request_id', $crm_request->id)->delete();

                        $comment = 'Dear ' . $shipperName . ',
                        Thank you for reaching out!
                        Your complaint has been resolved, and the payment has been made. We appreciate your patience and understanding throughout this process. In case of any further queries regarding this shipment, you may reach out within 48 hrs.
                        Regards,
                        Team CRM
                        TRAX';

                        CRMCommentController::add($crm_request->id, 306, 0, 0, $comment, 0, 0);
                    }
                }
                // End Auto Close Complaints
                //---------x-----------x-------------
                break;
        }
    }

}
