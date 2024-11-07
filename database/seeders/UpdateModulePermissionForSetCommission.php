<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForSetCommission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('module_permissions')->insert(array(
            array('id' => 332, 'name' => 'Set Commision Percentage', 'module_id' => 14)
        ));
    }
}
