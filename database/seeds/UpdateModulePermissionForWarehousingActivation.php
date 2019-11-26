<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForWarehousingActivation extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 270, 'name' => 'Warehousing Enable/Disable - Action', 'module_id' => 2),
        ));
    }
}
