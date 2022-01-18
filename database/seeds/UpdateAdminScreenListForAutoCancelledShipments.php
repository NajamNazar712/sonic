<?php

use Illuminate\Database\Seeder;

class UpdateAdminScreenListForAutoCancelledShipments extends Seeder
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
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Auto Cancellation Shipments', 'url'=>'admin.settings.cancelled_shipments.index', 'permission_id' => 660),
        ));
    }
}
