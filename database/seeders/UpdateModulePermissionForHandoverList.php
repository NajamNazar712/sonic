<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForHandoverList extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 342, 'name' => 'Handover List', 'module_id' => 22)
        ));
    }
}
