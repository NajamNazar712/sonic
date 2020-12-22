<?php

use Illuminate\Database\Seeder;
use DB;
class UpdateModulePermissionForNewMonthClosingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 408, 'name' => 'Month Closing Pending - View', 'module_id' => 15),
            array('id' => 409, 'name' => 'Month Closing Resolved - View', 'module_id' => 15),
            array('id' => 410, 'name' => 'Month Closing Resolved - Action', 'module_id' => 15),
            array('id' => 411, 'name' => 'Month Closing Closing Type - Action', 'module_id' => 15),
            array('id' => 412, 'name' => 'Month Closing Assign Responsible - Action', 'module_id' => 15),
            array('id' => 413, 'name' => 'Month Closing Close - Action', 'module_id' => 15),
        ));

        
    }
}
