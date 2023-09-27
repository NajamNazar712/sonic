<?php

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Models\Shipment;
use Illuminate\Database\Seeder;

class CreatePaymentsForMissingShipmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shipment_ids = [
            27272239,
            27607458,
            27654630,
            27697837,
            27698399,
            27716555,
            27729623,
            27744200,
            27754466,
            27763894,
            27767401,
            27768587,
            27781405,
            27782542,
            27782756,
            27787927,
            27790826,
            27791054,
            27792193,
            27796322,
            27799269,
            27799361,
            27800311,
            27802379,
            27804465,
            27805232,
            27808207,
            27808516,
            27810616,
            27811518,
            27819814,
            27819928,
            27821033,
            27822836,
            27826478,
            27838140,
            27848162,
            27850760,
            27853567,
            27857729
        ];

        foreach ($shipment_ids as $shipment_id){
            $shipment = Shipment::find($shipment_id);
            if($shipment){
                if (in_array($shipment->shipper_status_id, [14, 16, 30, 31, 36, 37])) {

                    if ($shipment->booking_type_id == 2) {
                        ShipmentChargesController::replacement($shipment_id);
                    } else if ($shipment->booking_type_id == 3) {
                        ShipmentChargesController::try_and_buy($shipment_id);
                    }

                    if ($shipment->booking_type_id != 4) {
                        AdminFinanceController::add_payment($shipment_id, 0);
                    } else {
                        AdminFinanceController::done_payment($shipment_id, 0);
                    }

                }
            }
        }
    }
}
