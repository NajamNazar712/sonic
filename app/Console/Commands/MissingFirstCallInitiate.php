<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipment;
use App\Http\Models\Webhook\ApiCallLog;
use App\RvShipmentTicket;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MissingFirstCallInitiate extends Command
{
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
        if($this->argument('startDate') != 0 && $this->argument('endDate') != 0 ){
            $timeEnd = $this->argument('endDate');
            $timeStart = $this->argument('startDate');
        }else{
            $timeEnd = Carbon::now()->subHour(1)->format('Y-m-d H') . ':59';
            $timeStart = Carbon::now()->subHour(1)->format('Y-m-d H') . ':00';
        }

        $globalSettings = GlobalSettings::where('setting_value', 1)
        ->whereIn('type',[ 'rv_disable_shippers_only_shippers', 'bot_call_enable_disable'])
        ->pluck('text','type');

        $shipments = Shipment::join('shipments_journey as sj', function ($join) use ($timeStart, $timeEnd,$globalSettings) {
            $join->on('sj.shipment_id', '=', 'shipments.id')
            ->whereIn('sj.shipper_status_id', [12, 52, 66])
            ->where([['sj.updated_at', '>=', $timeStart], ['sj.updated_at', '<=', $timeEnd]])
            ->whereIn('sj.status_reason_id',[$globalSettings['bot_call_enable_disable']]);
        })
        ->where([['shipments.updated_at', '>=', $timeStart], ['shipments.updated_at', '<=', $timeEnd]])
        ->whereIn('shipments.shipper_status_id', [12, 52, 66])
        // ->whereNotIn('shipments.user_id', [$globalSettings['rv_disable_shippers_only_shippers']])
        ->select('shipments.id', 'shipments.shipper_status_id', 'shipments.user_id', 'sj.status_reason_id')
        ->get()
        ->toArray();

        $shipmentIds = array_column($shipments, 'id');
        // Convert pluck() result to an array
        $rvShipmentTickets = RvShipmentTicket::whereIn('shipment_id', $shipmentIds)
        ->whereNull('deleted_at')
        ->pluck('shipment_id')
        ->toArray();  // Ensure this returns an array
        // $apicallLogs = ApiCallLog::whereIn('shipment_id', $rvShipmentTickets)
        // ->pluck('shipment_id')
        // ->toArray(); 
        

        // Use array_diff() to find IDs not in $rvShipmentTickets
        $rvShipmentInsert = array_diff($shipmentIds, $rvShipmentTickets);
        if(!empty(array_unique($rvShipmentInsert))){
            foreach($shipments as $value){
                if(in_array($value['id'], array_unique($rvShipmentInsert))){
                    $this->rvshipmentticketInsert($value['id'], $value['shipper_status_id'], $value['status_reason_id'], $value['user_id']);
                }
            }
        }
            // Debugging step: check the result
        // $apiCallogsInsert = array_diff($apicallLogs, $rvShipmentTickets);
        // error_log('apiCallogsInsert'.print_r($apiCallogsInsert,true));

    }
}
