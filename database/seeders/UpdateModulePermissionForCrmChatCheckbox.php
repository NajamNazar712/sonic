<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForCrmChatCheckbox extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 543, 'name' => 'Shipper Chat Checkbox', 'module_id' => 18),
        ));
    }
}
