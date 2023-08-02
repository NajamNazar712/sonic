<?php

use App\Http\Models\EmployeeShift;
use Illuminate\Database\Seeder;

class AddContractualEmployeeShifts extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        EmployeeShift::insert([
            ['shift_type_id' => '2', 'name' => 'Contractual Shift A', 'start_time' => '10:00:00', 'end_time' => '14:00:00', 'status' => '1', 'extension_minutes' => '30',  'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['shift_type_id' => '2', 'name' => 'Contractual Shift B', 'start_time' => '14:00:00', 'end_time' => '18:00:00', 'status' => '1', 'extension_minutes' => '30',  'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['shift_type_id' => '2', 'name' => 'Contractual Shift C', 'start_time' => '18:00:00', 'end_time' => '22:00:00', 'status' => '1', 'extension_minutes' => '30', 'created_at' => $timestamp, 'updated_at'=> $timestamp],
        ]);
    }
}
