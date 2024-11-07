<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CorporateRateTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now();
        DB::table('corporate_rate_types')->truncate();
        DB::table('corporate_rate_types')->insert(array(
            array('id' => 1, 'name'=>'Class Wise', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name'=>'Zone Wise', 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
