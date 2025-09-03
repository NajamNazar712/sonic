<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Shipment;
use App\Http\Traits\RvTrait;
use App\RvCronLog;
use App\RvShipmentTicket;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AgentSarNotification extends Command
{
    use RvTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:sarnotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends email notifications to shippers for "Shipper Advise Requested" shipments & updates to "Return Confirm" status after 24 hours if no response';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    // create Rv Cron Log
    public function createRvCronLog($message)
    {
        RvCronLog::create([
            'message' => $message,
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
           
//            Log::channel('cronJobLog')->info('s ' .'agent:sarnotification Initiated');
            $currentDateTime1 = Carbon::now()->toDateTimeString();
            $currentDateTime = Carbon::parse($currentDateTime1);

            $nowSub16Hours = Carbon::now()->subHours(16)->toDateTimeString();
            $nowSub24Hours = Carbon::now()->subHours(24)->toDateTimeString();
            $nowSub48Hours = Carbon::now()->subHours(48)->toDateTimeString();
            $date = Carbon::now()->subDay(3)->format('Y-m-d');
            // $dateTime = Carbon::createFromFormat('Y-m-d H:i:s', '2025-01-29 23:15:00');
            // $nowSub48Hours = $dateTime->subHours(48)->toDateTimeString();
            // $nowSub24Hours = $dateTime->subHours(24)->toDateTimeString();

            $nowSub48Hours = Carbon::parse($nowSub48Hours)->addMinutes(44)->format('Y-m-d H:i:s');
            // rv_assign_agent_status_id' 7 (Shipper Advised Request) and Check If State Is 2 (Unassign Assigned)
            $sendEmails = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 7)
                ->where('rv_state_id', 2)
                ->where('unresponsive_count', 3)
                //selects older records, i.e., records that were updated more than 16 hours ago.            
                ->where('updated_at', '>=', $nowSub16Hours)
                ->where('unresponsive_email_count', '<', 1);
            
            // rv_assign_agent_status_id' 8 (Refusal on call) and Check If State Is 2 (Unassign Assigned)
            $sendEmailofRefusalShipments = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 8)
            ->where('updated_at', '>=', $nowSub24Hours)
            ->where('rv_state_id', 2);

            //Combine the results for sending in single email
            $sendEmail = $sendEmails->union($sendEmailofRefusalShipments)->get();
            // If there are shipments that meet the conditions, send Email Notification to shipper for each shipment
            if ($sendEmail->isNotEmpty()) {

                foreach ($sendEmail as $shipment) {
                    // if shipment status is unresponsive Increment the unresponsive_email_count for each shipment after sending the email
                    if($shipment->rv_assign_agent_status_id == 7){
                        $shipment->increment('unresponsive_email_count');
                        $shipment->unresponsive_email_time = $currentDateTime;
                        $shipment->save();
                    }
                }
                NotificationsController::send(220, $sendEmail);
            }

            // When there is no response from the shipper within 24 hours of the "Shipper Advise Requested" status being set on the shipment, 
            // the system will automatically update the shipment status to "Return Confirm."
            $unresponsive_shipments = RvShipmentAssignAgent::join('shipments', function ($join){
                $join->on('rv_shipment_assign_agents.shipment_id', '=', 'shipments.id')
                    ->where('shipments.shipper_status_id','=' , 65);
                })
                ->whereNotIn('shipments.user_id', [49248, 49334])
                ->where('rv_shipment_assign_agents.rv_assign_agent_status_id', 7)
                ->where('rv_shipment_assign_agents.rv_state_id', 2)
                ->where('rv_shipment_assign_agents.unresponsive_count', 3)
                ->where('rv_shipment_assign_agents.unresponsive_email_count', '>', 0)
                ->where('rv_shipment_assign_agents.unresponsive_email_time', '<=', $nowSub48Hours)
                ->select('rv_shipment_assign_agents.*') // Select only columns from rv_shipment_assign_agents
                ->get();
                
            if ($unresponsive_shipments->isNotEmpty()) {
                foreach ($unresponsive_shipments as $shipment) {

                    // $shipment->update(['rv_assign_agent_status_id' => 1, 'rv_assign_agent_sub_status_id' => 4]);
                    $shipment->update(['rv_assign_agent_status_id' => 1, 'rv_assign_agent_sub_status_id' => null,'rv_state_id' => 4]);

                    $request = (object) [
                        'shipment_id' => $shipment->shipment_id,
                        'remarks' => $shipment->remarks,
                        'rv_assign_agent_sub_status_id' => Null,
                        'consignee_refused_reasons' => Null,
                    ];
                    $globalAdminId = 346;
                    $this->return_confirm($request,$globalAdminId);

                    $data = [
                        'rv_shipment_assign_agent_id' => $shipment->id,
                        'agent_id' => $shipment->agent_id,
                        'shipments_journey_id' => $shipment->shipments_journey_id,
                        'last_shipments_journey_id' => $shipment->last_shipments_journey_id,
                        'shipment_id' => $shipment->shipment_id,
                        'rv_assign_agent_status_id' => $shipment->rv_assign_agent_status_id,
                        'rv_assign_agent_sub_status_id' => Null,
                        'rv_state_id' => $shipment->rv_state_id,
                        'updated_type_id' => 1,
                        'updated_by_id' =>  Null,
                        'is_fake_status' => $shipment->is_fake_status,
                        'rv_fake_status_id' => $shipment->rv_fake_status_id,
                        'remarks' => $shipment->remarks,
                        'call_to_id' => $shipment->call_to_id,
                        'assigned_to_type_id' => $shipment->assigned_to_type_id,
                        'assigned_by' => $shipment->assigned_by,
                    ];
                    $this->data_rv_shipment_assign_agent_details($data);
                }
            }

            // When there is no response from the shipper within 24 hours of the "Shipper Advise Requested" status after refusal on call status, 
            // the system will automatically update the shipment status to "Return Confirm."
            
            $refusal_call_shipments = RvShipmentAssignAgent::join('shipments', function ($join) {
                $join->on('rv_shipment_assign_agents.shipment_id', '=', 'shipments.id')
                    ->where('shipments.shipper_status_id', '=', 65);
                })
                ->whereNotIn('shipments.user_id', [49248, 49334])
                ->where('rv_assign_agent_status_id', 8)
                ->where('rv_state_id', 2)
                ->where('rv_shipment_assign_agents.updated_at', '<=', $nowSub24Hours)
                ->get();
                
            if ($refusal_call_shipments->isNotEmpty()) {
                foreach ($refusal_call_shipments as $refusal_call_shipment) {
                    $refusal_call_shipment->update(['rv_assign_agent_status_id' => 1, 'rv_assign_agent_sub_status_id' => null,'rv_state_id' => 4]);

                    $request = (object) [
                        'shipment_id' => $refusal_call_shipment->shipment_id,
                        'remarks' => $refusal_call_shipment->remarks,
                        'rv_assign_agent_sub_status_id' => Null,
                        'consignee_refused_reasons' => Null,
                    ];
                    $globalAdminId = 346;
                    $this->return_confirm($request,$globalAdminId);

                    $data = [
                        'rv_shipment_assign_agent_id' => $refusal_call_shipment->id,
                        'agent_id' => $refusal_call_shipment->agent_id,
                        'shipments_journey_id' => $refusal_call_shipment->shipments_journey_id,
                        'last_shipments_journey_id' => $refusal_call_shipment->last_shipments_journey_id,
                        'shipment_id' => $refusal_call_shipment->shipment_id,
                        'rv_assign_agent_status_id' => $refusal_call_shipment->rv_assign_agent_status_id,
                        'rv_assign_agent_sub_status_id' => Null,
                        'rv_state_id' => $refusal_call_shipment->rv_state_id,
                        'updated_type_id' => 1,
                        'updated_by_id' =>  Null,
                        'is_fake_status' => $refusal_call_shipment->is_fake_status,
                        'rv_fake_status_id' => $refusal_call_shipment->rv_fake_status_id,
                        'remarks' => $refusal_call_shipment->remarks,
                        'call_to_id' => $refusal_call_shipment->call_to_id,
                        'assigned_to_type_id' => $refusal_call_shipment->assigned_to_type_id,
                        'assigned_by' => $refusal_call_shipment->assigned_by,
                    ];
                    $this->data_rv_shipment_assign_agent_details($data);
                }
            }
            $haltShipper = RvShipmentTicket::join('shipments_journey',function($join) use ($date){
                $join->on('rv_shipment_tickets.shipment_id', '=', 'shipments_journey.shipment_id')
                    ->where('shipments_journey.shipper_status_id', '=', 12)
                    ->where('shipments_journey.verification',1)
                    ->where('shipments_journey.created_at','>=',$date.' 00:00:00')
                    ->where('shipments_journey.updated_at','<=',date('Y-m-d').' 23:59:59');
                })
                ->where('disabled_shipper',1)
                ->where('permanent_disable',0)
                ->where('halt_shipper',0)
                ->where('call_count',0)
                ->whereNotIn('shipment_status_reason_id',[27,35])
                ->where('rv_shipment_tickets.updated_at','>=',$date.' 00:00:00')
                ->where('rv_shipment_tickets.updated_at','<=', date('Y-m-d').' 23:59:59')
                ->select('rv_shipment_tickets.*', 'shipments_journey.id as journeyId')->get();
                if($haltShipper->isNotEmpty()){
                    foreach($haltShipper->toArray() as $insertData){
                        $data = [
                            'agent_id' => 346, // testing purpose
                            'shipment_id' => $insertData['shipment_id'],
                            'shipments_journey_id' => $insertData['journeyId'],
                            'rv_assign_agent_status_id' => 7,
                            'rv_assign_agent_sub_status_id' => null,
                            'rv_assign_agent_sub_status_id' => null,
                            'assigned_to_type_id' => 0,
                            'assigned_by' => 0,
                            'rv_state_id' => 2,
                            'updated_by_id' => 346
                        ];
                        $this->rv_shipment_assign($data);
                        ShipmentsJourneyController::add($insertData['shipment_id'], 65, 65, $insertData['shipment_status_reason_id'], NULL, $insertData['shipment_user_id'], 346);
                    }
                    NotificationsController::send(220, $haltShipper);
                    Shipment::whereIn('id', array_column($haltShipper->toArray(), 'shipment_id'))->update(['shipper_status_id'=>65, 'consignee_status_id'=>65]);
                    RvShipmentTicket::whereIn('shipment_id', array_column($haltShipper->toArray(), 'shipment_id'))->update(['halt_shipper' => 1, 'deleted_at' => date('Y-m-d h:i:s')]);
                    RvShipmentAssignAgent::whereIn('shipment_id', array_column($haltShipper->toArray(), 'shipment_id'))
                    // ->whereDate('created_at',$date)
                    ->update(['unresponsive_count' => 3, 'unresponsive_email_count'=>1, 'unresponsive_email_time' => date('Y-m-d h:i:s')]);
                }

                /**
                 * Automatically updates eligible shipments as return-confirmed
                 * based on specific business criteria:
                 * - Shipment has >1 journey records with status 12
                 * - Associated ticket has disabled_shipper=1 and halt_shipper=1
                 * - Shipment is older than 48 hours
                 */
                $haltShipperReturn = RvShipmentTicket::select('rv_shipment_tickets.*')
                ->join('shipments', function ($join) {
                    $join->on('rv_shipment_tickets.shipment_id', '=', 'shipments.id')
                        ->where('shipments.shipper_status_id', 12);
                })
                ->join('shipments_journey', function ($join) {
                    $join->on('shipments.id', '=', 'shipments_journey.shipment_id')
                        ->where('shipments_journey.shipper_status_id', 12);
                })
                ->where([
                    ['rv_shipment_tickets.disabled_shipper', 1],
                    ['rv_shipment_tickets.halt_shipper', 1],
                    ['rv_shipment_tickets.permanent_disable', 0],
                    ['shipments.updated_at', '<=', $nowSub48Hours]
                ])
                ->groupBy('rv_shipment_tickets.id') // Group by primary key
                ->havingRaw('COUNT(shipments_journey.id) > 1')
                ->get();
                if($haltShipperReturn->isNotEmpty()){

                    foreach ($haltShipperReturn as $haltReturns) {
 
                        $request = (object) [
                            'shipment_id' => $haltReturns->shipment_id,
                            'remarks' => 'The shipment is automatically updated as return confirm',
                            'rv_assign_agent_sub_status_id' => Null,
                            'consignee_refused_reasons' => Null,
                        ];
                        $globalAdminId = 346;
                        $this->return_confirm($request, $globalAdminId);
                    }
                }
                

        } catch (\Throwable $th) {
            Log::channel('cronJobLog')->info('s ' .'agent:sarnotification Failed'. $th->getMessage());
            $this->createRvCronLog($th->getMessage());
        }
    }
}