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
use Illuminate\Support\Facades\Bus;

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
                    'rv_disable_shippers_all_shippers'
                ])
                ->get(['setting_value', 'type', 'text']);

            $isShipperDisabled = 0;
            $excludedShippers = [];
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
                }
            }

            if (in_array($this->shipment['shipment_user_id'], $excludedShippers)) { //Mark Shipper Not Disabled if It's user id found in Excluded Shippers
                $isShipperDisabled = 0;
            }

            if (in_array($this->shipment['shipment_user_id'], $onlyShippers)) { //Mark Shipper Disabled if It's user id found in Only Shippers
                $isShipperDisabled = 1;
            }

            // Log::channel('cronJobLog')->info('s ' . 'rv_shipment_ticket Saved');
            $isBot = ((array_key_exists($this->shipment['status_reason_id'], array_flip([8, 5, 1, 19, 38, 52, 60, 63])) && GlobalSettings::where(['type' => 'bot_call_enable_disable', 'setting_value' => 1])->exists() && $this->shipment['shipment_user_id'] == 10378) ? 1 : 0);
            
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
                    'delete_reason' => null
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
             
                // dispatch(new BotCallDispatch($this->shipment['shipment_id']));
                   
            }

        }
        // Log::channel('cronJobLog')->info('s ' . 'rv_shipment_ticket Failed');

    }
}
