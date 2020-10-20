<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForPickupWisePaymentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 391, 'name' => 'Pcikup Wise Payment Accounts', 'module_id' => 2)
        ));
    }
}
