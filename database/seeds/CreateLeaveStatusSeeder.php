<?php

use Illuminate\Database\Seeder;

class CreateLeaveStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('leave_statuses')->insert(array(
            array('id' => 1, 'name' => "Requested", 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => "Approved by HOD", 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'name' => "Rejected by HOD", 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 4, 'name' => "Approved by HR", 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 5, 'name' => "Rejected by HR", 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
