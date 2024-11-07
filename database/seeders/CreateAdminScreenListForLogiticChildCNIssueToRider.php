<?php

use Illuminate\Database\Seeder;

class CreateAdminScreenListForLogiticChildCNIssueToRider extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Logistic > Child CN Issue to Rider', 'url' => 'admin.logistic.cn.child_issue_to_rider.index', 'permission_id' => 969),
        ));
    }
}
