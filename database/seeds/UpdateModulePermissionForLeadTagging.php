<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForLeadTagging extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 661, 'name' => 'Lead Management - Auto Tagging', 'module_id' => 14),
            array('id' => 662, 'name' => 'Lead Management - Auto Tagging - Add', 'module_id' => 14),
            array('id' => 663, 'name' => 'Lead Management - Auto Tagging - Edit', 'module_id' => 14),
            array('id' => 664, 'name' => 'Lead Management - Zone Tagging - ', 'module_id' => 14),
            array('id' => 665, 'name' => 'Lead Management - Zone Tagging - Add', 'module_id' => 14),
            array('id' => 666, 'name' => 'Lead Management - Zone Tagging - Edit', 'module_id' => 14),
        ));
    }
}
