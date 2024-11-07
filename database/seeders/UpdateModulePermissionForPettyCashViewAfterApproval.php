<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForPettyCashViewAfterApproval extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 461, 'name' => 'Petty Cash Statement - View (After Aprroval)', 'module_id' => 8),
        ));
    }
}
