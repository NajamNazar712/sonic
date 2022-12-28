<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\ReversionDeliveredShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReversionDeliveredShipments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ReversionDelivered:Report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shipment reverted through adjust in payments';

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
        //3, 15 overall
        //
        $date = Carbon::yesterday()->format('Y-m-d');
        $reversion_shipments = ReversionDeliveredShipment::whereDate('created_at', $date);
        $destination_shipments = array();
        if($reversion_shipments->exists()){
            $reversion_shipments = $reversion_shipments->get();
            foreach ($reversion_shipments as $reversion_shipment){
                $shipment = Shipment::find($reversion_shipment->shipment_id);
                $delivered_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->whereIn('shipper_status_id', [14, 30, 36, 37])->latest('id')->first();
                $reverted_by = Admin::find($reversion_shipment->reverted_by)->name;
                $destination_shipments[$shipment->destination_city->hub_id][$shipment->id]['tracking_number'] = $shipment->tracking_number;
                $destination_shipments[$shipment->destination_city->hub_id][$shipment->id]['consignee_name'] = $shipment->consignee_name;
                $destination_shipments[$shipment->destination_city->hub_id][$shipment->id]['consignee_phone'] = $shipment->consignee_phone_number_1;
                $destination_shipments[$shipment->destination_city->hub_id][$shipment->id]['consignee_address'] = $shipment->consignee_address;
                $destination_shipments[$shipment->destination_city->hub_id][$shipment->id]['consignee_city'] = $shipment->destination_city->name;
                $destination_shipments[$shipment->destination_city->hub_id][$shipment->id]['amount'] = $shipment->amount;
                $destination_shipments[$shipment->destination_city->hub_id][$shipment->id]['dncc'] = $reversion_shipment;
                $destination_shipments[$shipment->destination_city->hub_id][$shipment->id]['delivered_at'] = $delivered_journey->created_at;
                $destination_shipments[$shipment->destination_city->hub_id][$shipment->id]['reverted_at'] = $reversion_shipment->created_at;
                $destination_shipments[$shipment->destination_city->hub_id][$shipment->id]['reverted_by'] = $reverted_by;
            }

            if(count($destination_shipments) > 0){
                $admins = Admin::whereIn('role_id', [3, 8, 9, 10, 15]);
                if($admins->exists()){
                    $admins = $admins->get();
                    foreach ($admins as $admin){
                        $shipment_details = array();
                        $assigned_hubs = AdminHub::where('admin_id', $admin->id)->pluck('hub_id')->toArray();
                        foreach ($destination_shipments as $hub_id => $destination_shipment){
                            if(in_array($hub_id, $assigned_hubs)){
                                foreach ($destination_shipments as $shipment_id => $shipment){
                                    dd($shipment);
                                    $shipment_details[$shipment_id]['tracking_number'] = $shipment['tracking_number'];
                                    $shipment_details[$shipment_id]['consignee_name'] = $shipment['consignee_name'];
                                    $shipment_details[$shipment_id]['consignee_phone'] = $shipment['consignee_phone'];
                                    $shipment_details[$shipment_id]['consignee_address'] = $shipment['consignee_address'];
                                    $shipment_details[$shipment_id]['consignee_city'] = $shipment['consignee_city'];
                                    $shipment_details[$shipment_id]['amount'] = $shipment['amount'];
                                    $shipment_details[$shipment_id]['dncc'] = $shipment['dncc'];
                                    $shipment_details[$shipment_id]['delivered_at'] = $shipment['delivered_at'];
                                    $shipment_details[$shipment_id]['reverted_at'] = $shipment['reverted_at'];
                                    $shipment_details[$shipment_id]['reverted_by'] = $shipment['reverted_by'];
                                }
                            }
                        }
                        if(count($shipment_details) > 0){
                            NotificationsController::send(205, $shipment_details, $admin);
                        }
                    }
                }
            }
        }
    }
}
