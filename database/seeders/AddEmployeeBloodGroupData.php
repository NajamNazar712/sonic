<?php

use Illuminate\Database\Seeder;

class AddEmployeeBloodGroupData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employee_blood_groups')->insert(array(
            array('id' => 1, 'name' => 'A+'),
            array('id' => 2, 'name' => 'A-'),
            array('id' => 3, 'name' => 'B+'),
            array('id' => 4, 'name' => 'B-'),
            array('id' => 5, 'name' => 'AB+'),
            array('id' => 6, 'name' => 'AB-'),
            array('id' => 7, 'name' => 'O+'),
            array('id' => 8, 'name' => 'O-'),
        ));
    }
}
