<?php

use Illuminate\Database\Seeder;

class AddPermissionsInSubstituteUserModelPermissionsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('substitute_user_module_permissions')->insert(array(
            array('id' => 20, 'name' => 'MMS Report', 'status' => 1),
            array('id' => 21,'name' => 'OrderManagement', 'status' => 1),
            array('id' => 22,'name' => 'Dashboard Screen', 'status' => 1),
        ));
    }
}
