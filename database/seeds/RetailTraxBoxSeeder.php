<?php

use Illuminate\Database\Seeder;

class RetailTraxBoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('retail_trax_boxes')->truncate();

        DB::table('retail_trax_boxes')->insert(array(
            array('id' => 1, 'name' => '2kg', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => '5kg', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'name' => '10kg', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 4, 'name' => '15kg', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 5, 'name' => '20kg', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 6, 'name' => '30kg', 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
