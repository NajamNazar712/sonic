<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RiderTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rider_types')->truncate();

        DB::table('rider_types')->insert(array(
            array('id' => 1, 'name' => 'Permanent'),
            array('id' => 2, 'name' => 'Incentive')
        ));
    }
}
