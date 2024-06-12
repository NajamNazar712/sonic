<?php

use Illuminate\Database\Seeder;

class CreateAdminScreenListForLogiticCNIssueToRider extends Seeder
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
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Logistic > CN Issue to Rider', 'url' => 'admin.logistic.cn.issue_to_rider.index', 'permission_id' => 963),
        ));
    }
}
