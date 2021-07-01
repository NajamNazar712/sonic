<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForWorkCodeMasterReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 532, 'name' => 'Work Code Master Report - View', 'module_id' => 9),
        ));
    }
}
