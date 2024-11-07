<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForConsigneeDetailsChangedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 258, 'name' => 'Consignee Details History', 'module_id' => 9),
        ));
    }
}
