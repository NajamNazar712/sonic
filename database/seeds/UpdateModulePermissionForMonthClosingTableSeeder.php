<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForMonthClosingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 141, 'name' => 'View', 'module_id' => 16),
            array('id' => 142, 'name' => 'Confirm', 'module_id' => 16),
            array('id' => 143, 'name' => 'Re-Attempt', 'module_id' => 16),
            array('id' => 144, 'name' => 'Add', 'module_id' => 16),
        ));
    }
}
