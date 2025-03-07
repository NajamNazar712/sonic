<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ODRNatureTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

    	DB::table('odr_natures')->truncate();

        DB::table('odr_natures')->insert(array(
            array('id' => 1,  'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Flyer Change'),
            array('id' => 2,  'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Flyer/ Shipment Open'),
            array('id' => 3,  'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Flyer/ Shipment Re-tape'),
            array('id' => 4,  'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Flyer/ Shipment Empty'),
            array('id' => 5,  'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Content Change'),
            array('id' => 6,  'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Short Contents'),
            array('id' => 7,  'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Damaged'),
            array('id' => 8,  'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'AWB/ Label Change'),
        ));
    }
}
