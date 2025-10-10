<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class walletChargesCommandRunOnSchedulerAddOntime extends Seeder
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
            array('type' => 'wallet_charges_updated', 'setting_value' => 0, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
