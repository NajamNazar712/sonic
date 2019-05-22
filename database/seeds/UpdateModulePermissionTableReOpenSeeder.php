<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableReOpenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 186, 'name' => 'Re-Open', 'module_id' => 18)
        ));
    }
}
