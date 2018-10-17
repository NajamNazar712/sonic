<?php

use App\Http\Models\ShipmentStatus;

use Illuminate\Database\Seeder;

class UpdateShipmentStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shipment_status = ShipmentStatus::find(12);

        $shipment_status->code = 'R-CP';
        $shipment_status->name = 'Return - Confirmation Pending';

        $shipment_status->save();
    }
}
