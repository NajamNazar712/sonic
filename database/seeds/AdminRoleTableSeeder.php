<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class AdminRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('admin_roles')->truncate();

        DB::table('admin_roles')->insert(array(
            array('id' => 1, 'created_at' => $timestamp, 'name' => 'Super Administrator', 'department_id' => 1, 'updated_by' => 3),
            array('id' => 2, 'created_at' => $timestamp, 'name' => 'Department Head', 'department_id' => 4, 'updated_by' => 3),
            array('id' => 3, 'created_at' => $timestamp, 'name' => 'Department Head', 'department_id' => 6, 'updated_by' => 3),
            array('id' => 4, 'created_at' => $timestamp, 'name' => 'Department Head', 'department_id' => 7, 'updated_by' => 3),
            array('id' => 5, 'created_at' => $timestamp, 'name' => 'Department Head', 'department_id' => 2, 'updated_by' => 3),
            array('id' => 6, 'created_at' => $timestamp, 'name' => 'Department Head', 'department_id' => 3, 'updated_by' => 3),
            array('id' => 7, 'created_at' => $timestamp, 'name' => 'Officer', 'department_id' => 4, 'updated_by' => 3),
            array('id' => 8, 'created_at' => $timestamp, 'name' => 'Regional Manager', 'department_id' => 6, 'updated_by' => 3),
            array('id' => 9, 'created_at' => $timestamp, 'name' => 'Zonal Manager', 'department_id' => 6, 'updated_by' => 3),
            array('id' => 10, 'created_at' => $timestamp, 'name' => 'Station Manager', 'department_id' => 6, 'updated_by' => 3),
            array('id' => 11, 'created_at' => $timestamp, 'name' => 'Officer', 'department_id' => 6, 'updated_by' => 3),
            array('id' => 12, 'created_at' => $timestamp, 'name' => 'Business Development Manager', 'department_id' => 7, 'updated_by' => 3),
            array('id' => 13, 'created_at' => $timestamp, 'name' => 'Officer', 'department_id' => 3, 'updated_by' => 3),
            array('id' => 14, 'created_at' => $timestamp, 'name' => 'Officer', 'department_id' => 4, 'updated_by' => 3),
            array('id' => 15, 'created_at' => $timestamp, 'name' => 'Team Leader', 'department_id' => 3, 'updated_by' => 3),
            array('id' => 16, 'created_at' => $timestamp, 'name' => 'Business Development Officer', 'department_id' => 7, 'updated_by' => 3)
        ));
    }
}