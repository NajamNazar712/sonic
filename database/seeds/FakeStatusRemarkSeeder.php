<?php

use Illuminate\Database\Seeder;

class FakeStatusRemarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('fake_statuses_remark')->insert(array(
            array('id' => 1, 'name' => 'Courier Misbehaviour','created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => 'Delivery Committed but not Attempt','created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'name' => 'Misguided by rider','created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 4, 'name' => 'Rider ask to Collect from his desire pickup point','created_at' => $timestamp, 'updated_at' => $timestamp),
       
        ));
    }
}
