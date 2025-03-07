<?php

namespace App\Jobs;

use App\Http\Models\Admin\GlobalSettings;
use App\RvShipmentTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
<<<<<<< HEAD
=======
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Shipment;
use App\RvShipmentAgent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
>>>>>>> sprint_130

class ProcessRvShipmentTicket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
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
                ->first();
                $rvShipmentAgent = RvShipmentAssignAgent::where(['shipment_id' => $this->shipment['shipment_id']])->first();
                if(isset($rvShipmentTicket->halt_shipper)){     
                    if($rvShipmentAgent->call_count <= 0){
                        $rvShipmentAgent->unresponsive_count = 0;
                        $rvShipmentAgent->unresponsive_email_count = 0;
                        $rvShipmentAgent->unresponsive_email_time = NULL;
                        $rvShipmentAgent->unresponsive_attempt_time = NULL;
                        $isShipperDisabled = 0;
                    }
                    $rvShipmentAgent->rv_state_id = 2;
                    $rvShipmentAgent->save();
                }
                if (isset($rvShipmentAgent) && $rvShipmentAgent->call_count > 0) {
                    $isShipperDisabled = 0;
                    $rvShipmentAgent->rv_state_id = 2;
                    $rvShipmentAgent->save();
                }
                
            }
<<<<<<< HEAD
            // Log::channel('cronJobLog')->info('s ' . 'rv_shipment_ticket Saved');

=======
            
            // $userId = [2234, 23825, 13060, 1049];
            // Log::channel('cronJobLog')->info('s ' . 'rv_shipment_ticket Saved');
            $isBot = ((in_array($this->shipment['status_reason_id'], $botCallStatus) && $botcallenable && $isShipperDisabled == 0) ? 1 : 0);
>>>>>>> sprint_130
            RvShipmentTicket::withTrashed()->updateOrCreate(
                ['shipment_id' => $this->shipment['shipment_id']],
                [
                    'shipment_shipper_status_id' => $this->shipment['shipper_status_id'],
                    'shipment_status_reason_id' => $this->shipment['status_reason_id'],
                    'shipment_user_id' => $this->shipment['shipment_user_id'],
                    'call_count' => $this->shipment['call_count'],
                    'in_progress' => 0,
                    'is_completed' => 0,
                    'deleted_at' => null,
                    'halt_shipper' => 0,
                    'disabled_shipper' => $isShipperDisabled,
                    'delete_reason' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]
            );
            // Log::channel('cronJobLog')->info('s ' . 'rv_shipment_ticket Saved1');

        }
        // Log::channel('cronJobLog')->info('s ' . 'rv_shipment_ticket Failed');

    }
}
