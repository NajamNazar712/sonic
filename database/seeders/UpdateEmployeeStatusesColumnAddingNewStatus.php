<?php

use Illuminate\Database\Seeder;

class UpdateEmployeeStatusesColumnAddingNewStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employee_statuses')->insert(array(
            array('id' => 3, 'name' => 'Active-No info'),
        ));
    }
}
