<?php

use Illuminate\Database\Seeder;

class AdminRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_roles')->truncate();

        DB::table('admin_roles')->insert(array(
            array('id' => 1, 'name' => 'Super Administrator', 'department_id' => 1, 'updated_by' => 1),
            array('id' => 2, 'name' => 'Department Head', 'department_id' => 4, 'updated_by' => 1),
            array('id' => 3, 'name' => 'Department Head', 'department_id' => 6, 'updated_by' => 1),
            array('id' => 4, 'name' => 'Department Head', 'department_id' => 7, 'updated_by' => 1),
            array('id' => 5, 'name' => 'Department Head', 'department_id' => 2, 'updated_by' => 1),
            array('id' => 6, 'name' => 'Department Head', 'department_id' => 3, 'updated_by' => 1),
            array('id' => 7, 'name' => 'Officer', 'department_id' => 4, 'updated_by' => 1),
            array('id' => 8, 'name' => 'Regional Manager', 'department_id' => 6, 'updated_by' => 1),
            array('id' => 9, 'name' => 'Zonal Manager', 'department_id' => 6, 'updated_by' => 1),
            array('id' => 10, 'name' => 'Station Manager', 'department_id' => 6, 'updated_by' => 1),
            array('id' => 11, 'name' => 'Officer', 'department_id' => 6, 'updated_by' => 1),
            array('id' => 12, 'name' => 'Business Development Manager', 'department_id' => 7, 'updated_by' => 1),
            array('id' => 13, 'name' => 'Officer', 'department_id' => 3, 'updated_by' => 1),
            array('id' => 14, 'name' => 'Officer', 'department_id' => 4, 'updated_by' => 1)
        ));
    }
}