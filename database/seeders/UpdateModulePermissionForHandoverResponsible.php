<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForHandoverResponsible extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 340, 'name' => 'Handover Responsible', 'module_id' => 22)
        ));
    }
}
