<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableFakeStatuses extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('module_permissions')->insert(array(
            array('id' => 169, 'name' => 'Fake Statuses', 'module_id' => 9)
        ));
    }
}
