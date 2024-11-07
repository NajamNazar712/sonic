<?php

use Illuminate\Database\Seeder;

class UpdateAdminsScreenForEmployeeShiftsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource  > Employee Shifts', 'url'=>'admin.human_resource.employee_shifts.index', 'permission_id' => 592));
    }
}
