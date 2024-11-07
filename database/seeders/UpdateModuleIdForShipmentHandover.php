<?php

use Illuminate\Database\Seeder;

class UpdateModuleIdForShipmentHandover extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert(array(
            array('id' => 22, 'name' => 'Shipment Handover')
        ));
    }
}
