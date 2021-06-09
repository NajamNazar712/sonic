<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EmployeeRegistrationStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employee_registration_statuses')->truncate();
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('employee_registration_statuses')->insert(array(
            array('id' => 1, 'name' => 'Request', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'Approved by HOD','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 3, 'name' => 'Approved by CEO','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 4, 'name' => 'Partially Approved','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 5, 'name' => 'Approved','created_at'=>$timestamp,'updated_at'=>$timestamp),
        ));
    }
}
