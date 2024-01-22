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

        DB::table('employee_shifts')->truncate();
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        EmployeeShift::insert([
            ['shift_type_id' => '2', 'name' => 'Contractual Shift A', 'start_time' => '10:00:00', 'end_time' => '22:00:00', 'status' => '1', 'extension_minutes' => '30',  'created_at' => $timestamp, 'updated_at' => $timestamp],
        ]);
    }
}
