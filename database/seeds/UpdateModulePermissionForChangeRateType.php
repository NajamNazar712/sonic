<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForChangeRateType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 487, 'name' => ' Active - Change Rate Type', 'module_id' => 2),
        ));
    }
}
