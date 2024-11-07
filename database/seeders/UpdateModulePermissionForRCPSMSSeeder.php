<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRCPSMSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 700, 'name' => 'RCP SMS Manual - Action', 'module_id' => 7),
        ));
    }
}
