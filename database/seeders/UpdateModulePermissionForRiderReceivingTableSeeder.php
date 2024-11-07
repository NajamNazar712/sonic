<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRiderReceivingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 366, 'name' => 'Rider Receiving - View', 'module_id' => 3)
        ));
    }
}
