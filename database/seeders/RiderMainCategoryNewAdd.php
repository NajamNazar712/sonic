<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiderMainCategoryNewAdd extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('rider_main_categories')->insert(array(
            array('name' => 'Freight Agent'),
        ));
    }
}