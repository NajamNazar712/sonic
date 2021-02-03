<?php

use Illuminate\Database\Seeder;

class InternationalRatesMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now();
        DB::table('international_user_rates')->truncate();
        DB::table('international_user_rates')->insert(array(
            array('user_id' => 1,'margin' => 100,'updated_by' => 6,'rates_updated_at' => $timestamp, 'created_at' => $timestamp, 'updated_at' => $timestamp),

        ));
    }
}
