<?php

use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Shipment;
use App\Jobs\BotCallDispatch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\RvTrait;

class ForceFullyFirstCallInitiate extends Seeder
{
    use RvTrait;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timeStart = '2024-09-29 00:00:00'; // Get the timestamp of two hours ago
        // Carbon::parse(now())->subHour(2)->format('Y-m-d H:i') 
        $timeEnd = '2024-10-02 23:59:59';

        // Now, re-initiate process for the retrieved shipment_ids after unresponsive one
        // $shipmentfirst = RvShipmentAssignAgent::join('shipments as s', 's.id', 'rv_shipment_assign_agents.shipment_id')->where(['agent_id' => 4620, ['unresponsive_attempt_time', '>=', $timeStart], ['unresponsive_attempt_time', '<=', $timeEnd], 'call_count' => 0])->whereIn('s.shipper_status_id',[12,52,65,66])->whereIn('shipment_id',[43323534,  43388122,  43474185,  43555308,  43531728,  43137455,  43223268,  43493274,  43002406,  43373279,  43424776,  43259711,  43536984,  43665770,  43043131,  43296517,  43393437,  43559470,  43561801,  43536251,  43616950,  42978503,  43400250,  43303589,  43393430,  43557953])->pluck('shipment_id');
       $shipmentId = Shipment::whereIn('id', [43323534,  43388122,  43474185,  43555308,  43531728,  43137455,  43223268,  43493274,  43002406,  43373279,  43424776,  43259711,  43536984,  43665770,  43043131,  43296517,  43393437,  43559470,  43561801,  43536251,  43616950,  42978503,  43400250,  43303589,  43393430,  43557953])->whereIn('shipper_status_id', [12, 52, 65, 66])->get();
        
        // Log::channel('cronJobLog')->info('s ' . 'Log after  shipmentSeconds call  record' . count($shipmentfirst));
        if (count($shipmentId) > 0) {
            Log::channel('cronJobLog')->info('s ' . 'Call initiate forcefully start first-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));

            foreach ($shipmentId as $shipmentId) {
                if(RvShipmentAssignAgent::where('shipment_id', $shipmentId->id)->exists()){
                    dispatch(new BotCallDispatch($shipmentId));
                }else{
                    $this->rvshipmentticketInsert($shipmentId->id, $shipmentId->shipper_status_id, 8, $shipmentId->user_id);
                }
                // dispatch(new BotCallDispatch($shipmentId));
            }
            Log::channel('cronJobLog')->info('s ' . 'Call initiate forcefully end first-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));
        }
    }
}
