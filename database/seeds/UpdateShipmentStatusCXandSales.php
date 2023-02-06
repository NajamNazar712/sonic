<?php

use Illuminate\Database\Seeder;
use App\Http\Models\ShipmentStatus;

class UpdateShipmentStatusCXandSales extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $shipment_status = ShipmentStatus::find(60);
        $shipment_status->name = 'Return - Unable to Return';
        // Return Unsuccessful for CX and Sales
        $shipment_status->save();
    }
}
