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
        DB::table('admins_screen_list')->where('url','admin.settings.cancelled_shipments.index')->delete();
    }
}
