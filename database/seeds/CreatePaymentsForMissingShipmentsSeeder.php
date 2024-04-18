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
            34695727,
            34699052,
            34687136,
            34568946,
            34695606,
            34687301,
            34687571,
            34687862,
            34698901,
            34687386,
            34695813,
            34711381,
            33854837,
            33854996,
            33855042,
            33867086,
            34691942,
            34670401,
            34670629,
            34670632,
            34687197,
            34687413,
            34687875,
            34691307,
            34693043,
            34693227,
            34688566,
            34688695,
            34693514,
            34652254,
            34687440,
            34688923,
            34689488,
            34691295,
            34936111,
            34688271,
            34690322,
            34692370,
            34690409,
            34685669,
            34691353,
            33818008,
            34689071,
            34689150,
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
