<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\ShipmentOnHold;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OnHoldShipmentEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:onholdshipments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch and delivery Date email of On-Hold Shipments';

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
        $today = Carbon::today()->toDateString();
        $dispatch_on_hold_shipments = ShipmentOnHold::whereDate('dispatch_date', $today)->where('status', 1)->where('email_status', 0);
        if($dispatch_on_hold_shipments->exists()){
            $dispatch_on_hold_shipments = $dispatch_on_hold_shipments->get();
            if(count($dispatch_on_hold_shipments) > 0){
                $details = array();
                foreach ($dispatch_on_hold_shipments as $index => $dispatch_shipment){
                    $shipment = Shipment::find($dispatch_shipment->shipment_id);
                    $last_status = ShipmentsJourney::where('shipment_id', $shipment->id)->orderBy('id', 'desc')->first();
                    $last_status_date = Carbon::parse($last_status->created_at)->toDateTimeString();
                    $arrival_status = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', DB::raw(2))->orderBy('id', 'desc')->first();
                    $arrival_status_date = Carbon::parse($arrival_status->created_at)->toDateTimeString();;
                    $details[$index]['tracking_number'] = $shipment->tracking_number;
                    $details[$index]['origin'] = $shipment->pickup_address->city->name;
                    $details[$index]['destination'] = $shipment->consignee_city->name;
                    $details[$index]['shipper'] = $shipment->user->name;
                    $details[$index]['status'] = $shipment->status_shipper->name;
                    $details[$index]['last_status_date'] = $last_status_date;
                    $details[$index]['arrival_status_date'] = $arrival_status_date;

                    $dispatch_shipment->email_status = 1;
                    $dispatch_shipment->save();
                }
                NotificationsController::send(108, $today, $details);
            }
        }


        $deliver_on_hold_shipments = ShipmentOnHold::whereDate('delivery_date', $today)->where('status', 1)->where('email_status', 1);
        if($deliver_on_hold_shipments->exists()){
            $deliver_on_hold_shipments = $deliver_on_hold_shipments->get();
            if(count($deliver_on_hold_shipments) > 0){
                $details = array();
                foreach ($deliver_on_hold_shipments as $index => $deliver_shipment){
                    $shipment = Shipment::find($deliver_shipment->shipment_id);
                    $last_status = ShipmentsJourney::where('shipment_id', $shipment->id)->orderBy('id', 'desc')->first();
                    $last_status_date = Carbon::parse($last_status->created_at)->toDateTimeString();
                    $arrival_status = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', DB::raw(2))->orderBy('id', 'desc')->first();
                    $arrival_status_date = Carbon::parse($arrival_status->created_at)->toDateTimeString();;
                    $details[$index]['tracking_number'] = $shipment->tracking_number;
                    $details[$index]['origin'] = $shipment->pickup_address->city->name;
                    $details[$index]['destination'] = $shipment->consignee_city->name;
                    $details[$index]['shipper'] = $shipment->user->name;
                    $details[$index]['status'] = $shipment->status_shipper->name;
                    $details[$index]['last_status_date'] = $last_status_date;
                    $details[$index]['arrival_status_date'] = $arrival_status_date;

                    $deliver_shipment->email_status = 2;
                    $deliver_shipment->save();
                }
                NotificationsController::send(109, $today, $details);
            }
        }
    }
}
