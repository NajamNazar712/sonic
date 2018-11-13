<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableSettingsShipmentCancellationCutOffDaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 116, 'name' => 'Shipment Cancellation Cut-Off Days', 'module_id' => 14)
        ));
    }
}
