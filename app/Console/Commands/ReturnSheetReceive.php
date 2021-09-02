<?php

namespace App\Console\Commands;

use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\ReturnSheet;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnSheetReceive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'returnsheet:receive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Receive Return Sheet For Shipper';

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
        $current_date = Carbon::now();
        $return_sheets = ReturnSheet::where('status_id', 0);
        if($return_sheets->exists()){
            $return_sheets= $return_sheets->get();
            foreach ($return_sheets as $return_sheet){
                $shipment_journey = ShipmentsJourney::where('shipment_id', $return_sheet->shipment_id)->where('shipper_status_id', DB::raw(25))->where('verification', 1);
                if($shipment_journey->exists()){
                    $shipment_journey = $shipment_journey->first();
                    $delivered_date = Carbon::parse($shipment_journey->created_at);
                    $total_hours = $delivered_date->diffInHours($current_date);
                    if($total_hours > 23){
                        $return_sheet->status_id = 2;
                        $return_sheet->received_at = Carbon::now();
                        $return_sheet->remarks = 'Auto Received after One Day';
                        $return_sheet->save();
                    }
                }
            }
        }
    }
}
