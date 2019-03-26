<?php

use Illuminate\Database\Seeder;

class AdminDepartmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_departments')->truncate();

        DB::table('admin_departments')->insert(array(
            array('id' => 1, 'name' => 'Super Administration'),
            array('id' => 2, 'name' => 'Administration'),
            array('id' => 3, 'name' => 'Customer Experience'),
            array('id' => 4, 'name' => 'Finance'),
            array('id' => 5, 'name' => 'Quality Assurance'),
            array('id' => 6, 'name' => 'Operations'),
            array('id' => 7, 'name' => 'Sales')
        ));
    }
}