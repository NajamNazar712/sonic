<?php

use Illuminate\Database\Seeder;

class EmployeeConfirmationStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employee_confirmation_statuses')->insert(array(
            array('id' => 1, 'name' => 'Waiting For Confirmation'),
            array('id' => 2, 'name' => 'Approve By Line Manager'),
            array('id' => 3, 'name' => 'Rejected By Line Manager'),
            array('id' => 4, 'name' => 'Approve By HOD'),
            array('id' => 5, 'name' => 'Rejected By HOD'),
            array('id' => 6, 'name' => 'Approve By HR'),
            array('id' => 7, 'name' => 'Rejected By HR'),
        ));
    }
}
