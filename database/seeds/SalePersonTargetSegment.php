<?php

use Illuminate\Database\Seeder;

class SalePersonTargetSegment extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('sale_person_target_segments')->truncate();
        DB::table('sale_person_target_segments')->insert(array(
            array('id' => 1, 'name' => 'E.com', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => 'International', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'name' => 'Packaging', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 4, 'name' => 'Warehouse', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 5, 'name' => 'Express', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 6, 'name' => 'Overland', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 7, 'name' => 'Gifting', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 8, 'name' => 'Saas', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 9, 'name' => 'Special Project', 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
