<?php

use Illuminate\Database\Seeder;
use App\Http\Models\ShipmentStatus;


class UpdateShipmentStatusTableForReturnMisrouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shipment_status = ShipmentStatus::find(68);
        $shipment_status->name = 'Shipment - Misrouted';
        $shipment_status->save();

        DB::table('shipment_status')->insert(array(
            array('id' => 69, 'code' => 'TB-MR','name' => 'Try & Buy - Misrouted','description' => 'Try & Buy - Misrouted'),
            array('id' => 70, 'code' => 'TB-MF','name' => 'Try & Buy - Misroute Forwarded','description' => 'Try & Buy - Misroute Forwarded'),
            array('id' => 71, 'code' => 'TB-WM','name' => 'Try & Buy - Without Manifest','description' => 'Try & Buy - Without Manifest'),

            array('id' => 72, 'code' => 'RP-MR','name' => 'Replacement - Misrouted','description' => 'Replacement - Misrouted'),
            array('id' => 73, 'code' => 'RP-MF','name' => 'Replacement - Misroute Forwarded','description' => 'Replacement - Misroute Forwarded'),
            array('id' => 74, 'code' => 'RP-WM','name' => 'Replacement - Without Manifest','description' => 'Replacement - Without Manifest'),

            array('id' => 75, 'code' => 'RT-MR','name' => 'Return - Misrouted','description' => 'Return - Misrouted'),
            array('id' => 76, 'code' => 'RT-MF','name' => 'Return - Misroute Forwarded','description' => 'Return - Misroute Forwarded'),
            array('id' => 77, 'code' => 'RT-WM','name' => 'Return - Without Manifest','description' => 'Return - Without Manifest'),
        ));
    }
}
