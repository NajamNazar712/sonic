<?php

use Illuminate\Database\Seeder;

class PackagingMaterialRequestStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now();
        DB::table('packaging_material_request_statuses')->truncate();
        DB::table('packaging_material_request_statuses')->insert(array(
            array('id'=>1, 'name'=>'Requested', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id'=>2, 'name'=>'Confirmed', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id'=>3, 'name'=>'Dispatched', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id'=>4, 'name'=>'Completed', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id'=>5, 'name'=>'Replenished', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id'=>6, 'name'=>'Canceled', 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
