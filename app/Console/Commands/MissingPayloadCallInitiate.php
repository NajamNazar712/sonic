<?php

namespace App\Console\Commands;

use App\Http\Models\Webhook\ApiCallLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\RvTrait;
use App\Jobs\BotCallDispatch;
use App\Jobs\BotCallDispatchSecod;
use App\Jobs\BotCallDispatchThird;

use function GuzzleHttp\json_encode;

class MissingPayloadCallInitiate extends Command
{
    use RvTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'missingpayload:call {startDate=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Missing Bot Payload Call';

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
        try{
            
            
            if($this->argument('startDate') != 0){
                $timeStart = $this->argument('startDate');
            }else{
                $timeStart = Carbon::now()->format('Y-m-d');
            }
            $shipments = ApiCallLog::join('shipments as s', 's.id', 'api_call_logs.shipment_id')->whereDate('api_call_logs.created_at','>=', $timeStart)->where('payload', 'null')->where('s.shipper_status_id', 12)->select("call_count_initiate", "shipment_id")->get();
            
            if(!empty($shipments)){
                
                foreach($shipments as $value){
                    if($value->call_count_initiate == 1){
                        dispatch(new BotCallDispatch($value->shipment_id));                 
                    }elseif($value->call_count_initiate == 2){
                        dispatch(new BotCallDispatchSecod($value->shipment_id));
                    }else{
                        dispatch(new BotCallDispatchThird($value->shipment_id));
                    }
                }
            }
          
        } catch (\Throwable $th) {
            Log::channel('botCallJobLog')->info($th->getMessage());

            // $this->createRvCronLog($th->getMessage() . ' Unresponsive Count ');
        }

    }
}
