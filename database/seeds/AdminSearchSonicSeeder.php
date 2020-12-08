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
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Receive Pickups', 'url'=>'admin.receive.index', 'permission_id' => 23)

        ));
    }
}
