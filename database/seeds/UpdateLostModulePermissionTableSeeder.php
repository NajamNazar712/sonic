<?php

use Illuminate\Database\Seeder;

class UpdateLostModulePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 127, 'name' => 'Lost - View', 'module_id' => 6),
        ));
    }
}
