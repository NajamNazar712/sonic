<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForBypassDeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       	DB::table('module_permissions')->insert(array(
            array('id' => 267, 'name' => 'Delivery Note Creation Bypass', 'module_id' => 6),
        ));
    }
}
