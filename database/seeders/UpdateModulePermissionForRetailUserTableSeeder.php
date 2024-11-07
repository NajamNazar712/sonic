<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRetailUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 474 , 'name' => 'User - View', 'module_id' => 26),
            array('id' => 475 , 'name' => 'User - Action', 'module_id' => 26)
        ));
    }
}
