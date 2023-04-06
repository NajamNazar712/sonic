<?php

use Illuminate\Database\Seeder;

class SpecialRequestReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('special_request_reasons')->truncate();

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('special_request_reasons')->insert(array(
                array('id' => 1,'name' => 'Business Calls','created_at' => $timestamp, 'updated_at' => $timestamp),
                array('id' => 2,'name' => 'General Approval','created_at' => $timestamp, 'updated_at' => $timestamp),
                array('id' => 3,'name' => 'Social Media Pressure','created_at' => $timestamp, 'updated_at' => $timestamp),
            )
        );
    }
}
