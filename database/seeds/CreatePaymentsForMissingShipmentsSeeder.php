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
        $shipment_ids = [27167527, 26933553, 27181539, 27309235, 27572271, 27322611, 27309235, 26933553, 27159236, 27160091, 27159992, 27160071, 27160028];

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
