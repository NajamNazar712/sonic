<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusForReplacementToRegularTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 56, 'code' => 'S-RR', 'name' => 'Replacement-Not Collected', 'description' => 'Shipment is not delivered to the consignee and its replacement could not be collected from him/her')
        ));
    }
}
