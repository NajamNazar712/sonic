<?php

use Illuminate\Database\Seeder;

class UpdateSubstituteUserModulePermissionForDarazMIS extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('substitute_user_module_permissions')->insert(array(
            array('id' => 15, 'name' => 'Daraz MIS Report')
        ));
    }
}
