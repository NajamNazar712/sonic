<?php

use Illuminate\Database\Seeder;

class LeadReferenceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('lead_references')->truncate();

        DB::table('lead_references')->insert(array(
            array('id' => 1, 'name' => 'Social Media', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => 'Helpline', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'name' => 'Webchat', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 4, 'name' => 'Email', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 5, 'name' => 'Call', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 6, 'name' => 'Other', 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
