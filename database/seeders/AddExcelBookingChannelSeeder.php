<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddExcelBookingChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('channels')->insert(array(
            array('id' => 6, 'name' => 'Sonic Excel','created_at'=>now(), 'updated_at'=>now()),
        ));
    }
}
