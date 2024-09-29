<?php

use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Shipment;
use App\Jobs\BotCallDispatchSecod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ForceFullySecondCallIntiate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timeStart = '2024-09-23 00:00:00'; // Get the timestamp of two hours ago
        // Carbon::parse(now())->subHour(2)->format('Y-m-d H:i') 
        $timeEnd = '2024-09-25 23:56:59';

        // Now, re-initiate process for the retrieved shipment_ids after unresponsive one
        $shipmentSeconds = RvShipmentAssignAgent::where(['agent_id' => 4620, ['unresponsive_attempt_time', '>=', $timeStart], ['unresponsive_attempt_time', '<=', $timeEnd], 'unresponsive_count' => 1, 'rv_assign_agent_status_id' => 6])->pluck('shipment_id');
        Log::channel('cronJobLog')->info('s ' . 'Log after  shipmentSeconds call  record' . count($shipmentSeconds));

        if (count($shipmentSeconds) > 0) {
            Log::channel('cronJobLog')->info('s ' . 'Call initiate forcefully start second-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));
            foreach ($shipmentSeconds as $shipmentId) {
                dispatch(new BotCallDispatchSecod($shipmentId));
            }
            Log::channel('cronJobLog')->info('s ' . 'Call initiate forcefully end second-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));
        }
    }
}
