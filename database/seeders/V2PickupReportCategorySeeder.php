<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class V2PickupReportCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('v2_pickup_report_categories')->truncate();
        DB::table('v2_pickup_report_categories')->insert(array(
            array('id' => 1, 'name' => 'Attempted & Picked'),
            array('id' => 2, 'name' => 'Attempted & Not Picked'),
            array('id' => 3, 'name' => 'Attempted & Failed')
        ));
    }
}
