<?php

namespace App\Console\Commands;

use App\Http\Models\RvShipmentAssignAgent;
use App\Jobs\BotCallDispatchSecod;
use App\Jobs\BotCallDispatchThird;
use App\RvCronLog;
use App\RvShipmentTicket;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BotCallInitiate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:botcallunresponsive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To open status RVR shipment for the agents to get ticket';

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
            
            $timeEnd = Carbon::parse(now())->subHour(2)->format('Y-m-d H:i'). ':59';
            // Carbon::parse(now())->subHour(2)->format('Y-m-d H:i') 
            $timeStart = Carbon::parse($timeEnd)->subMinute(14)->format('Y-m-d H:i') . ':00'; // Get the timestamp of two hours ago
           
            // Now, re-initiate process for the retrieved shipment_ids after unresponsive one
            $shipmentSeconds = RvShipmentAssignAgent::join('shipments as s', 's.id', 'rv_shipment_assign_agents.shipment_id')->where(['agent_id' => 4620, ['unresponsive_attempt_time', '>=', $timeStart], ['unresponsive_attempt_time', '<=', $timeEnd], 'call_count' => 1, 'rv_assign_agent_status_id' => 6])->whereIn('s.shipper_status_id', [12, 52, 66])->pluck('shipment_id');
           
            // Log::channel('botCallJobLog')->info('s ' . 'Log after  shipmentSeconds call  record' . count($shipmentSeconds) .'Start time'. $timeStart .'End Time'. $timeEnd);
            
            // dd($shipmentSeconds);
            //  Now, re-initiate process for the retrieved shipment_ids after unresponsive two            
            // $shipmentThirds = RvShipmentAssignAgent::where(['agent_id' => 4620, ['unresponsive_attempt_time', '>=', $timeStart], ['unresponsive_attempt_time', '<=', $timeEnd], 'unresponsive_count' => 2, 'rv_assign_agent_status_id' => 6])->pluck('shipment_id');
            // $second_count = 1;
            if (count($shipmentSeconds) > 0) {
                // Log::channel('botCallJobLog')->info('s ' . 'Call initiate start second-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));
                foreach($shipmentSeconds as $shipmentId){
                    dispatch(new BotCallDispatchSecod($shipmentId));
                }
                // Log::channel('botCallJobLog')->info('s ' . 'Call initiate end second-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));
            }

            // $shipmentThirds = RvShipmentAssignAgent::join('shipments as s', 's.id', 'rv_shipment_assign_agents.shipment_id')->where(['agent_id' => 4620,['unresponsive_attempt_time', '>=', $timeStart], ['unresponsive_attempt_time', '<=', $timeEnd], 'call_count' => 2, 'rv_assign_agent_status_id' => 6])->whereNotIn('s.user_id',[16344,26249,13060,5553,30230,49055,50475,27425,31794,31902,31538,51353,19943,8732,44233,37631,48558,35049,25244,49028,1049])->pluck('shipment_id');
            // Log::channel('botCallJobLog')->info('s ' . 'Log after  shipmentThirds call  record' . count($shipmentThirds));

            // // $third_count =1;
            // if(count($shipmentThirds) > 0){
            //     // Log::channel('botCallJobLog')->info('s ' . 'Call initiate start third-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));
            //     foreach($shipmentThirds as $shipmentId){
            //         dispatch(new BotCallDispatchThird($shipmentId));
            //     }
            //     // Log::channel('botCallJobLog')->info('s ' . 'Call initiate end third-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));
            // }
            $timeEnd = Carbon::parse(now())->subHour(1)->format('Y-m-d H:i') . ':59';
            // Carbon::parse(now())->subHour(2)->format('Y-m-d H:i') 
            $timeStart = Carbon::parse($timeEnd)->subMinute(14)->format('Y-m-d H:i') . ':00'; // Get the timestamp of two hours ago
            $shipmentSpecificThirds = RvShipmentAssignAgent::join('shipments as s', 's.id', 'rv_shipment_assign_agents.shipment_id')->where([['unresponsive_attempt_time', '>=', $timeStart], ['unresponsive_attempt_time', '<=', $timeEnd], 'unresponsive_count' => 2, 'rv_assign_agent_status_id' => 6])->pluck('shipment_id');
            if (count($shipmentSpecificThirds) > 0) {
                // Log::channel('botCallJobLog')->info('s ' . 'Call initiate start third-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));
                RvShipmentAssignAgent::whereIn('shipment_id', $shipmentSpecificThirds)->update(['rv_state_id' => 3]);
                RvShipmentTicket::whereIn('shipment_id', $shipmentSpecificThirds)->update(['is_bot' => 1]);
                foreach ($shipmentSpecificThirds as $shipmentId) {
                    dispatch(new BotCallDispatchThird($shipmentId));
                }
                // Log::channel('botCallJobLog')->info('s ' . 'Call initiate end third-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));
            }
        } catch (\Throwable $th) {
            // Log::channel('botCallJobLog')->info(' Unresponsive Count ');

            $this->createRvCronLog($th->getMessage().' Unresponsive Count ');
        }
    }
}
