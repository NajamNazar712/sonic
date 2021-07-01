<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForSpecialRequestButtonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 523, 'name' => 'Special Request - Action', 'module_id' => 18),
        ));
    }
}
