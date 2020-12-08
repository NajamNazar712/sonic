<?php

use Illuminate\Database\Seeder;

class AdminSearchSonicSeeder extends Seeder
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
            array('id' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Disputes', 'url'=>'admin.dispute.index', 'permission_id' => 1),
            array('id' => 2, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Daily Visit Form', 'url'=>'admin.daily_visit.index', 'permission_id' => 265),
            array('id' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Pending Pickups', 'url'=>'admin.pickups.index', 'permission_id' => 17),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Receive Pickups', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 5, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Merged Accounts', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Edit Sister Accounts', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Receiving Sheet History', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Receive Pickups', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Receive Pickups', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Booked VS Received VS Delivered VS Returned', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Pickups History', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Pending Pickups', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Individual Arrival of Shipments', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Bulk Arrival (Without Weight)', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Rider Pickups', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Rider Pickup Action Logs', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Rider Receiving', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Pending Shipments for Cargo', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Draft Cargo', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Create Cargo', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Cargo in Transit', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Cargo Received Report', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Cargo History', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Mapping', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Quick Receive Cargo', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Quick Receive List', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain Management', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Pending Shipments for Bag', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Create Bag', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Master Cargo In Transit', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Receive Master Cargo', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Master Cargo History', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Master Cargo Received', 'url'=>'admin.receive.index', 'permission_id' => 23),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Quick Receive Master Cargo', 'url'=>'admin.receive.index', 'permission_id' => 23),
             array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Quick Receive Master Cargo List', 'url'=>'admin.receive.index', 'permission_id' => 23),
             array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Same-Day Delivery', 'url'=>'admin.receive.index', 'permission_id' => 23),
             array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Pending Deliveries', 'url'=>'admin.receive.index', 'permission_id' => 23),
             array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Create Delivery Note', 'url'=>'admin.receive.index', 'permission_id' => 23),
             array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Receive Deliveries', 'url'=>'admin.receive.index', 'permission_id' => 23),
             array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Completed Deliveries', 'url'=>'admin.receive.index', 'permission_id' => 23),
             array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Mapping', 'url'=>'admin.receive.index', 'permission_id' => 23),
             array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Mapping', 'url'=>'admin.receive.index', 'permission_id' => 23),
             array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Mapping', 'url'=>'admin.receive.index', 'permission_id' => 23),
             array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Mapping', 'url'=>'admin.receive.index', 'permission_id' => 23),
             array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Mapping', 'url'=>'admin.receive.index', 'permission_id' => 23),
             array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Mapping', 'url'=>'admin.receive.index', 'permission_id' => 23),
             array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Mapping', 'url'=>'admin.receive.index', 'permission_id' => 23),
             array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Mapping', 'url'=>'admin.receive.index', 'permission_id' => 23)

        ));
    }
}
