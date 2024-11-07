<?php

use Illuminate\Database\Seeder;

class AdminDepartmentSeederForWorkingDays extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('admin_departments')->where('id',1)->update(['working_days' => 2]);

        DB::table('leave_statuses')->insert(array(
            array('id' => 6, 'name' => "Approved by Line Manager", 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 7, 'name' => "Rejected by Line Manager", 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
        
    }
}
