<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShipperDisplayRemarksNSAOSA extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('global_settings')->insert(array(
            array('type' => 'spec_shipper_remarks_nsa_osa', 'setting_value' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'text' => 13060)
        ));
    }
}
