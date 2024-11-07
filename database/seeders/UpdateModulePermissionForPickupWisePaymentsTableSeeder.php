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
            array('id' => 391, 'name' => 'Pcikup Wise Payment Accounts - Setting', 'module_id' => 2),
            array('id' => 396, 'name' => 'Make Payments - Pickup Wise', 'module_id' => 8)
        ));
    }
}
