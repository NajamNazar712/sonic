<?php

use App\Http\Models\RvShipmentAssignAgent;
use App\Jobs\BotCallDispatch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ForceFullyFirstCallInitiate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timeStart = '2024-09-24 00:00:00'; // Get the timestamp of two hours ago
        // Carbon::parse(now())->subHour(2)->format('Y-m-d H:i') 
        $timeEnd = '2024-09-27 23:59:59';

        // Now, re-initiate process for the retrieved shipment_ids after unresponsive one
        $shipmentfirst = RvShipmentAssignAgent::join('shipments as s', 's.id', 'rv_shipment_assign_agents.shipment_id')->where(['agent_id' => 4620, ['unresponsive_attempt_time', '>=', $timeStart], ['unresponsive_attempt_time', '<=', $timeEnd], 'call_count' => 0])->whereIn('s.shipper_status_id',[12,52,65,66])->pluck('shipment_id');
        Log::channel('cronJobLog')->info('s ' . 'Log after  shipmentSeconds call  record' . count($shipmentfirst));
        if (count($shipmentfirst) > 0) {
            Log::channel('cronJobLog')->info('s ' . 'Call initiate forcefully start first-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));

            foreach ($shipmentfirst as $shipmentId) {
                dispatch(new BotCallDispatch($shipmentId));
            }
            Log::channel('cronJobLog')->info('s ' . 'Call initiate forcefully end first-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));
        }
    }
}
