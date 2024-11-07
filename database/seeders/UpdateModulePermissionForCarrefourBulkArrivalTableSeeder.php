<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForCarrefourBulkArrivalTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 579, 'name' => 'Carrefour Bulk Arrival - View', 'module_id' => 3),
            array('id' => 580, 'name' => 'Carrefour Accounts - Update', 'module_id' => 14),
        ));
    }
}
