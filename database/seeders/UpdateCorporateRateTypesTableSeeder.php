<?php

use Illuminate\Database\Seeder;

class UpdateCorporateRateTypesTableSeeder extends Seeder
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
            array('id' => 1, 'name'=>'Flat/KG', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name'=>'Zone Wise', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'name'=>'Default', 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
