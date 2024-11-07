<?php

use Illuminate\Database\Seeder;


class EnableShippersAWBinfo extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipper_air_waybill_settings')->update([
            'information' => '1',
        ]);
    }
}
