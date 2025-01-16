<?php

namespace Database\Seeders;

use App\CronDonePayment;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Models\Shipment;
use Illuminate\Database\Seeder;
use \App\ShipmentsArchieve;

class CreatePaymentsForMissingShipmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tracking_number = CronDonePayment::where('status',1)->pluck('tracking_number')->toArray();
        $shipment_ids = Shipment::whereIn('tracking_number',$tracking_number)->select('id')->pluck('id')->toArray();
        if(count($shipment_ids) == 0){
            $shipment_ids = ShipmentsArchieve::whereIn('tracking_number',$tracking_number)->select('id')->pluck('id')->toArray();
        }
        foreach ($shipment_ids as $shipment_id){
            $shipment = Shipment::find($shipment_id);
            if(empty($shipment)){
                $shipment = ShipmentsArchieve::find($shipment_id);
            }
            if($shipment){
                if (in_array($shipment->shipper_status_id, [14, 16, 30, 31, 36, 37])) {

                    if ($shipment->booking_type_id == 2) {
                        ShipmentChargesController::replacement($shipment_id,$shipment);
                    } else if ($shipment->booking_type_id == 3) {
                        ShipmentChargesController::try_and_buy($shipment_id,$shipment);
                    }

                    if ($shipment->booking_type_id != 4) {
                        AdminFinanceController::add_payment($shipment_id, 0,$shipment);
                    } else {
                        AdminFinanceController::done_payment($shipment_id, 0,$shipment);
                    }

                }
            }
        }
        if(count($tracking_number) > 0){
            CronDonePayment::whereIn('tracking_number',$tracking_number)->update(['status'=>0]);
        }
    }
}
