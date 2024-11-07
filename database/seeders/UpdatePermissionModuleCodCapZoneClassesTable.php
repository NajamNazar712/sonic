<?php

use Illuminate\Database\Seeder;

class UpdatePermissionModuleCodCapZoneClassesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 197, 'name' => 'Cod Cap For Zone Classes', 'module_id' => 14)
        ));
    }
}
