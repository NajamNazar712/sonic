<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableForTrackingSalesDepartment extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 273, 'name' => 'Sales Person Tracking', 'module_id' => 19)
        ));
    }
}
