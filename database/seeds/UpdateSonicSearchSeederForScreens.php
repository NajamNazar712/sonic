<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateSonicSearchSeederForScreens extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Telenor > Telenor Bulk Return', 'url'=>'admin.telenor.return.bulk_return', 'permission_id' => 525));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Delivery > DN ByPass Request', 'url'=>'admin.delivery.note.request_index', 'permission_id' => 531));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Work Code Master Report', 'url'=>'admin.reports.work_code_master.index', 'permission_id' => 532));
    }
}
