<?php

use Illuminate\Database\Seeder;

class UpdateAdminScreenListForReversePickupReport extends Seeder
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
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Reverse Pickup', 'url'=>'admin.reports.reverse_pickup.index', 'permission_id' => 624),
        ));
    }
}
