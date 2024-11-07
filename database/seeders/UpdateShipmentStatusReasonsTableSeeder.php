<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusReasonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 56, 'name' => 'Consignee not responding'),
            array('id' => 57, 'name' => 'Consignee wants open shipment'),
            array('id' => 58, 'name' => 'Consignee wants later'),
        ));
    }
}
