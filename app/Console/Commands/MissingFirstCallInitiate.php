<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Webhook\ApiCallLog;
use App\RvShipmentTicket;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\RvTrait;
use App\Jobs\BotCallDispatch;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

use function GuzzleHttp\json_encode;

class MissingFirstCallInitiate extends Command
{
    use RvTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'missingfirst:call {startDate=0} {endDate=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Missing Bot First Call';

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

            // $shipmentId = ApiCallLog::where('created_at','>=','2025-06-23 16:30:00')->where('status_code', 403)->groupBy('shipment_id')->pluck('shipment_id')->toArray();
            // foreach ($shipmentId as $ids) {
            //     dispatch(new BotCallDispatch($ids));
            // }
            // return true;  
        if($this->argument('startDate') != 0 && $this->argument('endDate') != 0 ){
            $timeEnd = $this->argument('endDate');
            $timeStart = $this->argument('startDate');
        }else{
            $timeEnd = Carbon::now()->subHour(1)->format('Y-m-d H') . ':59:59';
            $timeStart = Carbon::now()->subHour(1)->format('Y-m-d H') . ':00:00';
        }
        $upshipments = RvShipmentTicket::where('in_progress', 0)->where('is_bot', 0)->get();
        // ->update(['in_progress' => 0,'rv_shipment_tickets.updated_at'=> 'rv_shipment_tickets.created_ats']);
        foreach ($upshipments as $shipment) {
            $shipment->updated_at = $shipment->created_at;
            $shipment->save();
        }
        // Log::channel('botCallJobLog')->info('s ' . 'bot call misisng entry ' . $timeStart . ' bot call time End '. $timeEnd);
        $globalSettings = GlobalSettings::where('setting_value', 1)
        ->whereIn('type',[ 'rv_disable_shippers_only_shippers', 'bot_call_enable_disable'])
        ->pluck('text','type');

        $shipments = Shipment::where([['shipments.updated_at', '>=', $timeStart], ['shipments.updated_at', '<=', $timeEnd]])
        ->whereIn('shipments.shipper_status_id', [12, 52, 66])
        // ->whereNotIn('shipments.user_id', [$globalSettings['rv_disable_shippers_only_shippers']])
        ->select('shipments.id', 'shipments.shipper_status_id', 'shipments.user_id')
        // ->groupBy('sj.shipment_id')
        ->get()
        ->toArray();
        
        $shipmentIds = array_column($shipments, 'id');
        // Convert pluck() result to an array
        $rvShipmentTickets = RvShipmentTicket::whereIn('shipment_id', $shipmentIds)
        // ->whereNull('deleted_at')
        ->pluck('shipment_id')
        ->toArray();  // Ensure this returns an array
                
        $rvShipmentInsert = array_diff($shipmentIds, $rvShipmentTickets);
        // Log::channel('botCallJobLog')->info('s ' . 'call missing entry check' .json_encode(($rvShipmentInsert)));

            if(!empty($rvShipmentInsert)){
                foreach($shipments as $value){
                    if(in_array($value['id'], array_unique($rvShipmentInsert))){
                        $journey = ShipmentsJourney::where('shipment_id', $value['id'])->whereIn('shipper_status_id', [12, 52, 66])->select('status_reason_id')->latest()->first();
                        // Log::channel('botCallJobLog')->info('s ' . 'call missing entry check' . $value['id']);
                        $this->rvshipmentticketInsert($value['id'], $value['shipper_status_id'], $journey['status_reason_id'], $value['user_id']);
                    }
                }
            }
            // $failedJobs = DB::table('failed_jobs')
            //     ->get();
            // if ($failedJobs->isNotEmpty()) {
            //     foreach ($failedJobs as $job) {
            //         Artisan::call('queue:retry', ['id' => $job->id]);
            //     }
            // }

            
            // if ($this->argument('startDate') != 0 && $this->argument('endDate') != 0) {
            //     $rvShipments = RvShipmentTicket::where([['rv_shipment_tickets.updated_at', '>=', $timeStart], ['rv_shipment_tickets.updated_at', '<=', $timeEnd]])->where('is_bot',1)
            //         ->where('in_progress',1)
            //         ->where('disabled_shipper',0)
            //         ->pluck('shipment_id')
            //         ->toArray();

            //     $apicallLogs = ApiCallLog::where([['api_call_logs.updated_at', '>=', $timeStart], ['api_call_logs.updated_at', '<=', $timeEnd]])
            //         ->pluck('shipment_id')
            //         ->toArray();

            //     // Debugging step: check the result
            //     $apiCallogsInsert = array_diff( $rvShipments,$apicallLogs);
            //     foreach($apiCallogsInsert as $ids){
            //         dispatch(new BotCallDispatch($ids));
            //     }
            // }
        } catch (\Throwable $th) {
            Log::channel('botCallJobLog')->info($th->getMessage());

            // $this->createRvCronLog($th->getMessage() . ' Unresponsive Count ');
        }

    }
}
