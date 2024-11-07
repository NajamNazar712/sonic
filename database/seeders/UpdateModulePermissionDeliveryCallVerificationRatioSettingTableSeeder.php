<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionDeliveryCallVerificationRatioSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 231, 'name' => 'Delivery Call Verification Ratio - View', 'module_id' => 14),
        ));
    }
}
