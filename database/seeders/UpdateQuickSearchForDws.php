<?php

use Illuminate\Database\Seeder;

class UpdateQuickSearchForDws extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > DWS Report', 'url'=>'admin.reports.dws_report.index', 'permission_id' => 642)
        );
    }
}
