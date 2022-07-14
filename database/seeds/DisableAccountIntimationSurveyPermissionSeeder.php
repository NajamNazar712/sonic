<?php

use Illuminate\Database\Seeder;

class DisableAccountIntimationSurveyPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 548,549
        DB::table('module_permissions')->insert(array(
            array('id' => 762, 'name' => 'Disable Account Intimation SURVEY Questions - View', 'module_id' => 2),
            array('id' => 763, 'name' => 'Disable Account Intimation SURVEY Questions - Add - View', 'module_id' => 2),
            array('id' => 764, 'name' => 'Disable Account Intimation SURVEY Questions - Edit - Action', 'module_id' => 2),
            array('id' => 765, 'name' => 'Disable Account Intimation SURVEY Questions - Status - Action', 'module_id' => 2),
            array('id' => 767, 'name' => 'Disable Account Intimation SURVEY Send Survey - View', 'module_id' => 2),
            array('id' => 768, 'name' => 'Disable Account Intimation SURVEY Report - View', 'module_id' => 2),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 557, 'screen_name' => 'Disable Account Intimation SURVEY Questions View', 'action'=> 'View'),
            array('id' => 558, 'screen_name' => 'Disable Account Intimation SURVEY Questions', 'action'=> 'Excel Download'),
            array('id' => 559, 'screen_name' => 'Disable Account Intimation SURVEY Report View', 'action'=> 'View'),
            array('id' => 560, 'screen_name' => 'Disable Account Intimation SURVEY Report', 'action'=> 'Excel Download ')
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shippers > Accounts > Disable Account Intimation SURVEY > Send Survey', 'url'=>'admin.accounts.disable.account.intimation.survey.index', 'permission_id' => 762),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shippers > Accounts > Disable Account Intimation SURVEY > Survey Report', 'url'=>'admin.accounts.disable.account.intimation.survey.report', 'permission_id' => 768)
        ));
    }
}
