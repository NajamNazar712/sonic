<?php

use App\Http\Models\ShipmentStatus;
use Illuminate\Database\Seeder;

class UpdateShipmentStatusForMisroutedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ShipmentStatus::where('id', 49)->delete();

        DB::table('shipment_status')->insert(array(
            array('id' => 49, 'code' => 'S-MF', 'name' => 'Shipment - Misroute Forwarded', 'description' => 'Shipment is ready to forward to new destination'),
        ));
    }
}
