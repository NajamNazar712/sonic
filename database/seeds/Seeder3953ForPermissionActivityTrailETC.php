<?php

use Illuminate\Database\Seeder;

class Seeder3953ForPermissionActivityTrailETC extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 598, 'name' => 'Corporate Reimbursement Setting', 'module_id' => 2),
            array('id' => 599, 'name' => 'Corporate Reimbursement Setting - Approve/Reject', 'module_id' => 2)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 438, 'screen_name' => 'Corporate Reimbursement Setting', 'action'=> 'View'),
        ));
    }
}
