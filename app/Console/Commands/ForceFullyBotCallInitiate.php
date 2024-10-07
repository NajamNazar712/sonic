<?php

namespace App\Console\Commands;

use App\Http\Models\RvShipmentAssignAgent;
use App\Jobs\BotCallDispatchSecod;
use App\Jobs\BotCallDispatchThird;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ForceFullyBotCallInitiate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forcefullyInitiate:call {startData} {endDate} {unresponsive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'force Fully call initiated if in case of any number skips';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $timeStart = $this->argument('startData'); // Get the timestamp of two hours ago
        // Carbon::parse(now())->subHour(2)->format('Y-m-d H:i') 
        $timeEnd = $this->argument('endDate');
        
        //Unresponsive count
        $unresponsive = $this->argument('unresponsive');
        // Now, re-initiate process for the retrieved shipment_ids after unresponsive one
        $shipment = RvShipmentAssignAgent::where(['agent_id' => 4620, ['unresponsive_attempt_time', '>=', $timeStart], ['unresponsive_attempt_time', '<=', $timeEnd], 'unresponsive_count' => $unresponsive, 'rv_assign_agent_status_id' => 6])->pluck('shipment_id');

        Log::channel('cronJobLog')->info('s ' . 'Log after  shipmentSeconds call  record' . count($shipment));

        if (count($shipment) > 0) {
            Log::channel('cronJobLog')->info('s ' . 'Call initiate forcefully start second-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));
            if($unresponsive == 1){
                foreach ($shipment as $shipmentId) {
                    dispatch(new BotCallDispatchSecod($shipmentId));
                }
            }elseif($unresponsive == 2){
                Log::channel('cronJobLog')->info('s ' . 'Call initiate forcefully start third-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));

                foreach ($shipment as $shipmentId) {
                    dispatch(new BotCallDispatchThird($shipmentId));
                }
                Log::channel('cronJobLog')->info('s ' . 'Call initiate forcefully end third-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));
            }
            Log::channel('cronJobLog')->info('s ' . 'Call initiate forcefully end second-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));
        }
    }
}
