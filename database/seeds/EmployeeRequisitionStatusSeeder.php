<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EmployeeRequisitionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employee_requisition_statuses')->truncate();
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('employee_requisition_statuses')->insert(array(
            array('id' => 1, 'name' => 'Requested', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'Approved by HOD','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 3, 'name' => 'Approved by CEO','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 4, 'name' => 'Approved','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 5, 'name' => 'Rejected','created_at'=>$timestamp,'updated_at'=>$timestamp),
        ));
    }
}
