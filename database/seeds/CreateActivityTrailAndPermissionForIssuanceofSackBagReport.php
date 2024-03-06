<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateActivityTrailAndPermissionForIssuanceofSackBagReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('module_permissions')->insert(array(
            array('id' => 937, 'name' => 'Issuance of Canvas Bag Report - View', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 745, 'screen_name' => 'Issuance Canvas Bag', 'action' => 'View'),
            array('id' => 746, 'screen_name' => 'Issuance Canvas Bag', 'action' => 'Excel'),

        ));

//        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
//        DB::table('admins_screen_list')->insert(array(
//            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Issuance of Canvas Bag', 'url' => 'admin.reports.issuance_sack_bag.index', 'permission_id' => 937),
//        ));
    }
}
