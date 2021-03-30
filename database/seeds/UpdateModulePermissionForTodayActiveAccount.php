<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForTodayActiveAccount extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 470, 'name' => 'Today Active Accounts - View', 'module_id' => 2),
        ));
    }
}
