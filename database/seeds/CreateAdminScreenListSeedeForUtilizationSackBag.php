<?php

use Illuminate\Database\Seeder;
class CreateAdminScreenListSeedeForUtilizationSackBag extends Seeder
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
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Utilization of Canvas Bag', 'url' => 'admin.reports.sack_bag_utilization.index', 'permission_id' => 935),
        ));
    }
}
