<?php

use Illuminate\Database\Seeder;

class EmployeeModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employee_request_statuses')->truncate();
        DB::table('employee_request_statuses')->insert(array(
            array('id' => 1 ,'name'=>'Requested'),
            array('id' => 2 ,'name'=>'In Process'),
            array('id' => 3 ,'name'=>'Approved'),
            array('id' => 4 ,'name'=>'Rejected'),
        ));

        DB::table('employee_types')->truncate();
        DB::table('employee_types')->insert(array(
            array('id' => 1 ,'name'=>'Staff'),
            array('id' => 2 ,'name'=>'Rider'),
        ));

        DB::table('employee_genders')->truncate();
        DB::table('employee_genders')->insert(array(
            array('id' => 1 ,'name'=>'Male'),
            array('id' => 2 ,'name'=>'Female')
        ));

        DB::table('employee_statuses')->truncate();
        DB::table('employee_statuses')->insert(array(
            array('id' => 1 ,'name'=>'Active'),
            array('id' => 2 ,'name'=>'Inactive'),
        ));

        DB::table('employee_religions')->truncate();
        DB::table('employee_religions')->insert(array(
            array('id' => 1 ,'name'=>'Islam'),
            array('id' => 2 ,'name'=>'Christian'),
            array('id' => 3 ,'name'=>'Hinduism'),
            array('id' => 4 ,'name'=>'Other'),
        ));

        DB::table('employee_nationalities')->truncate();
        DB::table('employee_nationalities')->insert(array(
            array('id' => 1 ,'name'=>'Pakistani'),
            array('id' => 2 ,'name'=>'Indian'),
            array('id' => 3 ,'name'=>'Bangladeshi'),
            array('id' => 4 ,'name'=>'American'),
        ));

        DB::table('employee_marital_statuses')->truncate();
        DB::table('employee_marital_statuses')->insert(array(
            array('id' => 1 ,'name'=>'Single'),
            array('id' => 2 ,'name'=>'Married'),
            array('id' => 3 ,'name'=>'Divorced'),
            array('id' => 4 ,'name'=>'Widower'),
            array('id' => 5 ,'name'=>'Widowed'),
        ));

        /*DB::table('employee_departments')->truncate();

        DB::table('employee_departments')->insert(array(
            array('id' => 1, 'name' => 'Information Technology'),
            array('id' => 2, 'name' => 'Administration'),
            array('id' => 3, 'name' => 'Customer Experience'),
            array('id' => 4, 'name' => 'Finance'),
            array('id' => 5, 'name' => 'Quality Assurance'),
            array('id' => 6, 'name' => 'Operations'),
            array('id' => 7, 'name' => 'Sales'),
            array('id' => 8, 'name' => 'Commercial & International Business'),
            array('id' => 9, 'name' => 'Special Projects'),
            array('id' => 10, 'name' => 'Human Resource')
        ));*/

        DB::table('employee_designations')->truncate();
        DB::table('employee_designations')->insert(array(
            array('id' => 1 ,'name'=>'Manager'),
            array('id' => 2 ,'name'=>'Team Lead'),
            array('id' => 3 ,'name'=>'Software Engineer'),
        ));

        DB::table('employee_domiciles')->truncate();
        DB::table('employee_domiciles')->insert(array(
            array('id' => 1 ,'name'=>'Sindh'),
            array('id' => 2 ,'name'=>'Punjab'),
            array('id' => 3 ,'name'=>'Balochistan'),
            array('id' => 4 ,'name'=>'KPK'),
        ));

        DB::table('employee_relationships')->truncate();
        DB::table('employee_relationships')->insert(array(
            array('id' => 1 ,'name'=>'Father'),
            array('id' => 2 ,'name'=>'Mother'),
            array('id' => 3 ,'name'=>'Brother'),
            array('id' => 4 ,'name'=>'Sister'),
        ));
    }
}
