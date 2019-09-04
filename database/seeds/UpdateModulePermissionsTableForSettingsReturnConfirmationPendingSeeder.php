<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionsTableForSettingsReturnConfirmationPendingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
			array('id' => 253, 'name' => 'Return Confirmation Pending Shipment Selection Time', 'module_id' => 14)
		));
    }
}
