<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForDnBypassAddRequestPermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 538, 'name' => 'DN ByPass Request - Add Request', 'module_id' => 6),
        ));
    }
}
