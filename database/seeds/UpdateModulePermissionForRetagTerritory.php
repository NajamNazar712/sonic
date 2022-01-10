<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRetagTerritory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 658, 'name' => 'Re-Tag Territory - View', 'module_id' => 2),
        ));
    }
}
