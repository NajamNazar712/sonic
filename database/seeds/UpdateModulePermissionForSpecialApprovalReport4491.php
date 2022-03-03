<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForSpecialApprovalReport4491 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 688, 'name' => 'CRM Special Approval', 'module_id' => 9)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 513, 'screen_name' => 'CRM Special Approval', 'action'=> 'View'),
            array('id' => 514, 'screen_name' => 'CRM Special Approval', 'action'=> 'Excel Download'),
        ));
    }
}
