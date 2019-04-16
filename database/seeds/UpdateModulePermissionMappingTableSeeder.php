<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionMappingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 198, 'name' => 'Mapping', 'module_id' => 4),
        ));
    }
}
