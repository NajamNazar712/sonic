<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionsForRetailPayments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 454, 'name' => 'Retail Make Payments - View', 'module_id' => 26),
            array('id' => 455, 'name' => 'Retail Done Payments - View', 'module_id' => 26),
            array('id' => 456, 'name' => 'Retail Make Payments - Make', 'module_id' => 26),
            array('id' => 457, 'name' => 'Retail Done Payments - Paid', 'module_id' => 26),
            array('id' => 458, 'name' => 'Retail Done Payments - Reverted', 'module_id' => 26),
            array('id' => 459, 'name' => 'Retail Done Payments - Excel', 'module_id' => 26),
            array('id' => 460, 'name' => 'Retail Make Payments - Action', 'module_id' => 26),
        ));
    }
}
