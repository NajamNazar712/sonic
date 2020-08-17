<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForConsigneeInfoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 363, 'name' => 'Consignee Info - View', 'module_id' => 18),
        ));
    }
}
