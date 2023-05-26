<?php

use Illuminate\Database\Seeder;

class SubStatusCallFindingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('sub_status_call_findings')->insert(array(
            array('id' => 1, 'remark' => 'Invalid','created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'remark' => 'Not Pertain','created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'remark' => 'Powered Off','created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 4, 'remark' => 'Not Answered','created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 5, 'remark' => 'Hang up by Customer','created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 6, 'remark' => 'Number Busy','created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 7, 'remark' => 'Others','created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
