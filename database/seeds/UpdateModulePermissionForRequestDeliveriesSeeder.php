<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRequestDeliveriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 610, 'screen_name' => 'Pending Delivery Note Requests', 'action'=> 'View'),
            array('id' => 611, 'screen_name' => 'Pending Delivery Note Requests', 'action'=> 'Excel Download')
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Delivery > Pending Delivery Note Requests', 'url'=>'admin.delivery.rider_request.index', 'permission_id' => 35)
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 816, 'name' => 'Pending Delivery Note Requests - Approve', 'module_id' => 6),
            array('id' => 817, 'name' => 'Pending Delivery Note Requests - Reject', 'module_id' => 6),
            array('id' => 818, 'name' => 'Pending Delivery Note Requests - Edit', 'module_id' => 6),
        ));
    }
}
