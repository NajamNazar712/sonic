<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForHumanResourse extends Seeder
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
            array('id' => 449, 'name' => 'Human Resource', 'module_id' => 11),
        ));
    }
}
