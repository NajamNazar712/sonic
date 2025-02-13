<?php

namespace App\Jobs;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\ShipmentsJourney;
use App\Http\Traits\RvTrait;
use App\RvShipmentTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Shipment;
use App\RvShipmentAgent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class ProcessRvShipmentTicket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,RvTrait;
    protected $shipment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $shipment)
    {
        $this->queue = 'rv_shipment_ticket';
        $this->shipment = $shipment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Log::channel('cronJobLog')->info('s ' . 'rv_shipment_ticket Initiated');
        if (!in_array($this->shipment['status_reason_id'], [12, 27, 35])) { //only drop this shipment in rv_shipment_tickets if its status_reason_id is not in [12,27,35]
            // Log::channel('cronJobLog')->info('s ' . 'rv_shipment_ticket In');

            $globalSettings = GlobalSettings::where('setting_value', 1)
                ->whereIn('type', [
                    'rv_disable_shippers_excluded_shippers',
                    'rv_disable_shippers_only_shippers',
                    'rv_disable_shippers_all_shippers',
                    'bot_call_enable_disable'
                ])
                ->get(['setting_value', 'type', 'text']);
            $isShipperDisabled = 0;
            $botcallenable = 0;
            $excludedShippers = [];
            $botCallStatus = [];
            $onlyShippers = [];

            foreach ($globalSettings as $globalSetting) {
                switch ($globalSetting->type) {
                    case 'rv_disable_shippers_all_shippers':
                        $isShipperDisabled = 1;
                        break;
                    case 'rv_disable_shippers_excluded_shippers':
                        $excludedShippers = array_merge($excludedShippers, explode(',', $globalSetting->text));
                        break;
                    case 'rv_disable_shippers_only_shippers':
                        $onlyShippers = array_merge($onlyShippers, explode(',', $globalSetting->text));
                        break;
                    case 'bot_call_enable_disable':
                        $botCallStatus = array_merge($onlyShippers, explode(',', $globalSetting->text));
                        $botcallenable = 1;
                        break;
                }
            }
            if (in_array($this->shipment['shipment_user_id'], $excludedShippers)) { //Mark Shipper Not Disabled if It's user id found in Excluded Shippers
                $isShipperDisabled = 0;
            }

            if (in_array($this->shipment['shipment_user_id'], $onlyShippers)) { //Mark Shipper Disabled if It's user id found in Only Shippers
                $isShipperDisabled = 1;
                //This works on the halt shipper. If the first attempt is disabled, the second attempt will follow the current RVR process.T0-6980
                $rvShipmentTicket  = DB::table('rv_shipment_tickets')
                ->where('shipment_id', $this->shipment['shipment_id'])
                ->orWhereNotNull('deleted_at')
                ->limit(1)
                ->first();
                Log::channel('cronJobLog')->info('s ' . 'rv_shipment_ticket data Saved'.json_encode($rvShipmentTicket));
                
                if($rvShipmentTicket->halt_shipper == 1){
                    RvShipmentAssignAgent::where('shipment_id',$this->shipment['shipment_id'])->update(['unresponsive_count' => 0, 'unresponsive_email_count' => 0, 'rv_state_id'=>2, 'unresponsive_email_time'=>NULL, 'unresponsive_attempt_time'=>NULL]);
                    RvShipmentTicket::where('shipment_id',$this->shipment['shipment_id'])->update(['deleted_at' => NULL, 'halt_shipper' => 0]);
                    $isShipperDisabled = 0;
                }
            }
            
            // $userId = [2234, 23825, 13060, 1049];
            // Log::channel('cronJobLog')->info('s ' . 'rv_shipment_ticket Saved');
            $isBot = ((in_array($this->shipment['status_reason_id'], $botCallStatus) && $botcallenable && $isShipperDisabled == 0) ? 1 : 0);
            RvShipmentTicket::withTrashed()->updateOrCreate(
                ['shipment_id' => $this->shipment['shipment_id']],
                [
                    'shipment_shipper_status_id' => $this->shipment['shipper_status_id'],
                    'shipment_status_reason_id' => $this->shipment['status_reason_id'],
                    'shipment_user_id' => $this->shipment['shipment_user_id'],
                    'call_count' => $this->shipment['call_count'],
                    'is_bot' => $isBot,
                    'in_progress' => 0,
                    'is_completed' => 0,
                    'deleted_at' => null,
                    'disabled_shipper' => $isShipperDisabled,
                    'delete_reason' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]
            );
            //need to Continue This
            if($isBot)
            {
                $adminId = 4620;

                $shipmentJourneyId = ShipmentsJourney::where('shipment_id', $this->shipment['shipment_id'])->latest()->select('id')->first();
                $data = [
                    'agent_id' => $adminId, // testing purpose
                    'shipment_id' => $this->shipment['shipment_id'],
                    'shipments_journey_id' => $shipmentJourneyId->id,
                    'rv_assign_agent_status_id' => null,
                    'rv_assign_agent_sub_status_id' => null,
                    'assigned_to_type_id' => 0,
                    'assigned_by' => 0,
                    'rv_state_id' => 1,
                    'updated_by_id' => $adminId
                ];
                $this->rv_shipment_assign($data);
                if(RvShipmentAgent::where('agent_id', $adminId)->doesntExist()){
                    $new = new RvShipmentAgent();
                    $new->agent_id = $adminId;
                    $new->total_shipments = 0;
                    $new->actual_productivity = 0;
                    $new->save();
                }
                //Job implementation for the bot call.  
                // $job = new BotCallDispatch($this->shipment['shipment_id']); // Assuming SomeJob takes parameters
                // dispatchNow($job); 
                // Bus::dispatchNow(new BotCallDispatch($this->shipment['shipment_id']));
             
                dispatch(new BotCallDispatch($this->shipment['shipment_id']));
                   
            }

        }
        // Log::channel('cronJobLog')->info('s ' . 'rv_shipment_ticket Failed');

    }
}
