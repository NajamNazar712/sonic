<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForPettyCashConsigneeSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 462, 'name' => 'Petty Cash Hub Assigning - View', 'module_id' => 14)
        ));
    }
}
