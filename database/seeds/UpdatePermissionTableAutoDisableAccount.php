<?php

use Illuminate\Database\Seeder;

class UpdatePermissionTableAutoDisableAccount extends Seeder
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
            array('id' => 149, 'name' => 'Auto Account Disabled Days', 'module_id' => 14)
        ));
    }
}
