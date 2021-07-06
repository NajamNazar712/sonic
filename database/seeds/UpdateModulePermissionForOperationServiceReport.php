<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForOperationServiceReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 524, 'name' => 'Operation Service Report - View', 'module_id' => 9),
        ));
    }
}
