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
use App\Http\Controllers\ShipmentsPaymentJourneyController;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\CRM\CrmRequestTagging;
use App\Http\Controllers\CRM\CRMCommentController;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipment;

class WalletSettlementFromDonePayments implements ShouldQueue
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
        $this->queue = 'wallet_settlement_form_done_payment';
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
        ->leftjoin('wallet_users as wu', function ($join) {
            $join->on('wu.user_id', '=', 's.user_id')
                ->where('wu.substitute_user_id', '0');
        })
        ->leftjoin('finja_log_settlement_records as sac', 'done_payment_shipments.shipment_id', '=', 'sac.shipment_id')
        ->where('done_payment_shipments.done_payment_id', $this->payment_id)
        ->whereIn('done_payment_shipments.wallet_action_bid', [0,1,2])
        // ->where(function ($query) {
        //     $query->where('sac.wallet_settlement_updated', 0);
        // })
        ->select(['done_payment_shipments.*', 'wu.user_id as user_id', 'wu.wallet_id as wallet_id', 's.tracking_number', 's.cash_handling_charges', 's.insurance_charges','s.replacement_charges','s.try_and_buy_charges','s.intercept_charges','s.nsa_osa_charges','s.esc_charges','s.return_charges', 'sac.wallet_settlement_updated', 's.weight_charges', 's.fuel_surcharge','sc.faf_charges', 'ssc.reverse_pickup_charges', 'sc.wallet_charges', 'sac.wallet_log_charges_updated'])->get();
        //dd($done_payment_shipments);
        $successfull_record = [];
        $api = config('app.FINGA_URL');
        $token = FingaIntegrationController::getToken($api);
        foreach($done_payment_shipments as $dps) {
            $shipmentId = $dps->shipment_id;
            $shipment = Shipment::find($shipmentId);
            if($dps->wallet_action_bid == 1 && $dps->wallet_settlement_updated == 0) {

                //$logCharged = FinjaLogSettlementRecord::where('shipment_id', $shipmentId)->first();
                $logCharged = !empty($dps->wallet_log_charges_updated) ? $dps->wallet_log_charges_updated : 0;
                
                $requestPayload = [
                    "client_id" => $dps->user_id,
                    "wallet_id" => $dps->wallet_id,
                    "reference_id" => $dps->id,
                    "shipment_id" =>  $dps->tracking_number,
                    "amount" => $dps->type == 0 ? $dps->amount : 0,
                    "charges" => [
                        'faf_charges' => $logCharged == 1 ? floatval($dps->faf_charges) : 0,
                        'weight_charges' =>$logCharged == 1 ?  floatval($dps->weight_charges) : 0,
                        'fuel_surcharge' =>$logCharged == 1 ? floatval($dps->fuel_surcharge): 0,
                        'cash_handling_charges' => $dps->type == 0 ? floatval($dps->cash_handling_charges) : 0,
                        'insurance_charges' => floatval($dps->insurance_charges),
                        'replacement_charges' => floatval($dps->replacement_charges), 
                        'try_and_buy_charges' => floatval($dps->try_and_buy_charges),
                        'intercept_charges' => floatval($dps->intercept_charges),
                        'non_service_area_charges' => floatval($dps->nsa_osa_charges),
                        'esc_charges' => floatval($dps->esc_charges),
                        'reverse_pickup_charges' => floatval($dps->reverse_pickup_charges),
                        'return_charges' => floatval($dps->return_charges),
                        'gst_charges' => floatval($dps->gst),
                        'sms_charges' => floatval($dps->sms_charges),
                    ]
                ];
                $request_nature = 'settlement-request';
                $response_nature = 'settlement-response';
                $url = $api.'transactions/log/settlement';
                $data = [
                    'shipment_id' => $dps->shipment_id,
                    'wallet_settlement_updated' => true,
                    'wallet_settlement_updated_at' => Carbon::now(),
                ];

            } elseif($dps->wallet_action_bid == 2) {
                $requestPayload = [
                    "client_id" => $dps->user_id,
                    "wallet_id" => $dps->wallet_id,
                    "reference_id" => $dps->id,
                    "shipment_id" =>  $dps->tracking_number,
                    "amount" => floatval($dps->payable),
                ];
                $request_nature = 'adjustment-request';
                $response_nature = 'adjustment-response';
                $url = $api.'transactions/log/adjustment';
                $data = [
                    'shipment_id' => $dps->shipment_id,
                    'wallet_adjustment_updated' => true,
                    'wallet_adjustment_updated_at' => Carbon::now(),
                ];
            } elseif($dps->wallet_action_bid == 0) {
                $requestPayload = [
                    "client_id" => $dps->user_id,
                    "wallet_id" => $dps->wallet_id,
                    "shipment_id" =>  $dps->tracking_number,
                    "charges" => [
                        'faf_charges' =>  floatval($dps->faf_charges),
                        'weight_charges' =>  floatval($dps->weight_charges),
                        'fuel_surcharge' =>  floatval($dps->fuel_surcharge),
                        'arrival_charges_gst' => floatval($dps->gst),
                    ]
                ];
                $request_nature = 'log-charge-request';
                $response_nature = 'log-charge-response';
                $url = $api.'transactions/log/charge';
                $data = [
                    'shipment_id' => $dps->shipment_id,
                    'wallet_log_charges_updated' => true,
                    'wallet_log_charges_updated_at' => Carbon::now(),
                ];
            }

            try {

                if($token) {
                    $response = Http::withHeaders([
                        'accept' => 'application/json',
                        'Authorization' => "Bearer " . $token,
                    
                    ])->post($url, $requestPayload);
        
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
                        if ($dps->type == 1) {
                            $shipment->payment_status_id = 7;
    
                            $shipment->save();
    
                            ShipmentsPaymentJourneyController::add($dps->shipment_id, 7, $this->id, '', $this->payment_id);
                        } else if ($dps->type == 3) {
                            $shipment->payment_status_id = 12;
    
                            $shipment->save();
    
                            ShipmentsPaymentJourneyController::add($dps->shipment_id, 12, $this->id, '', $this->payment_id);
                        } else {
                            $shipment->payment_status_id = 3;
    
                            $shipment->save();
    
                            //---------x-----------x-------------
                            // Start Auto Close Complaints
                            $crm_request = CrmRequest::where('shipment_id', $dps->shipment_id)->where('status_id', 2)->first();
                            
                            if ($crm_request) { 
                                $shipperName = User::find(Shipment::where('id', $dps->shipment_id)->select('user_id')->first()->user_id)->name;
                                
                                if ($crm_request->status_id == 2) {//if crm request is in_process
                                    CrmRequest::where('id', $crm_request->id)->update([
                                        'status_id' => 4 // Closed status
                                    ]);
                                    CrmRequestStatusHistory::create([
                                        'crm_request_id' => $crm_request->id,
                                        'status_id' => 4,
                                        'agent_id' => Auth::id()
                                    ]);
                                    
                                    CrmRequestTagging::where('crm_request_id', $crm_request->id)->delete();
    
                                    $comment = 'Dear '.$shipperName.',
                                            Thank you for reaching us out! 
                                            Your complaint has been resolved, and the payment has been paid. We appreciate your patience and understanding throughout this process. In case of any further query regarding this shipment you may reach us out within 48 hrs.
                                            Regards,
                                            Team CRM
                                            TRAX';
                                    
                                    CRMCommentController::add($crm_request->id, 306, 0, 0, $comment, 0, 0);
                                }
                            }
                            // End Auto Close Complaints
                            //---------x-----------x-------------
    
                            ShipmentsPaymentJourneyController::add($dps->shipment_id, 3, $this->id, '', $this->payment_id);
                        }
        
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
