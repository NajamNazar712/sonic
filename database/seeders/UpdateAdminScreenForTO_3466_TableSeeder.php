<?php

use Illuminate\Database\Seeder;

class UpdateAdminScreenForTO_3466_TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > Reporting Location', 'url'=>'admin.human_resource.reporting_location.index', 'permission_id' => 478));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > Designation', 'url'=>'admin.human_resource.designation.index', 'permission_id' => 481));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > Department', 'url'=>'admin.human_resource.department.index', 'permission_id' => 484));
    }
}
