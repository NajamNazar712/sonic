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
            array('id' => 141, 'name' => 'Month Closing - View', 'module_id' => 15),
            array('id' => 142, 'name' => 'Month Closing - Confirm', 'module_id' => 15),
            array('id' => 143, 'name' => 'Month Closing - Re-Attempt', 'module_id' => 15),
            array('id' => 144, 'name' => 'Month Closing - Add', 'module_id' => 15),
        ));
    }
}
