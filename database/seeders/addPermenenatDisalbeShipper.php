<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class addPermenenatDisalbeShipper extends Seeder
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
            array('type' => 'rv_permanent_disable_shippers', 'setting_value' => 1, 'text' => '', 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
