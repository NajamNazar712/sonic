<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChangeUserNameForTelenorBulkArrival extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('global_settings')->insert([
            'id' => 209,
            'setting_value' => 1,
            'type' => 'update_telenor_user_on_arrival',
            'text' => '7762, 10354, 37791, 38106, 33688, 14110, 37791',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
