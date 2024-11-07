<?php

use Illuminate\Database\Seeder;

class UpdateGlobalSettingsForAutoCancelledShipments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now();

        DB::table('global_settings')->where('type','cancelled_shipments')->delete();
    }
}
