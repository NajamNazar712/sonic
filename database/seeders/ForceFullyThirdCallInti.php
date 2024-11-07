<?php

namespace Database\Seeders;

use App\Http\Models\RvShipmentAssignAgent;
use App\Jobs\BotCallDispatchThird;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ForceFullyThirdCallInti extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timeStart = '2024-10-03 00:00:00'; // Get the timestamp of two hours ago
        // Carbon::parse(now())->subHour(2)->format('Y-m-d H:i') 
        $timeEnd = '2024-10-04 23:56:59';

        $shipmentThirds = RvShipmentAssignAgent::where(['agent_id' => 4620, ['unresponsive_attempt_time', '>=', $timeStart], ['unresponsive_attempt_time', '<=', $timeEnd], 'unresponsive_count' => 2, 'rv_assign_agent_status_id' => 6])->pluck('shipment_id');
        Log::channel('cronJobLog')->info('s ' . 'Log after  shipmentThirds call  record' . count($shipmentThirds));

        // $third_count =1;
        if (count($shipmentThirds) > 0) {
            Log::channel('cronJobLog')->info('s ' . 'Call initiate forcefully start third-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));

            foreach ($shipmentThirds as $shipmentId) {
                dispatch(new BotCallDispatchThird($shipmentId));
            }
            Log::channel('cronJobLog')->info('s ' . 'Call initiate forcefully end third-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));
        }
    }
}
