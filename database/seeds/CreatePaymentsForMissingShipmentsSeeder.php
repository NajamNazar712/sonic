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
            27260472,
            27260135,
            27260308,
            27259188,
            27312511,
            27291853,
            27292143,
            27292274,
            27296065,
            27296285,
            27331754,
            27331835,
            27331847,
            27331908,
            27331980,
            27332086,
            27703066,
            27721297,
            27749432,
            27749441,
            27749235,
            27749334,
            27787195,
            28024250
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
