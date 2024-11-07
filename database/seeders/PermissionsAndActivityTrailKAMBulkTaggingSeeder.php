<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsAndActivityTrailKAMBulkTaggingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('module_permissions')->insert(array(
            array('id' => 991, 'name' => 'KAM Bulk Tagging - View', 'module_id' => 2),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 798, 'screen_name' => 'KAM Bulk Tagging', 'action'=> 'View'),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 799, 'screen_name' => 'KAM Bulk Tagging', 'action'=> 'Excel Upload'),
        ));


        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shippers > Accounts > KAM Bulk Tagging', 'url'=>'admin.accounts.kam_bulk_tagging.index', 'permission_id' => 991),
        ));
    }
}
