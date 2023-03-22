<?php

namespace App\Console\Commands;

use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeLate;
use App\Http\Models\HR\EmployeePenalty;
use Carbon\Carbon;
use Illuminate\Console\Command;

class LateEmployeePenalty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:penalty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Late Employee Penalty';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $startMonth = Carbon::now()->subMonth()->startOfMonth()->addDays(20)->format('Y-m-d');
        $endMonth = Carbon::now()->startOfMonth()->addDays(19)->format('Y-m-d');
        $counter = 0;
        $existing_late_ids = EmployeeLate::whereBetween('attendence_date',[$startMonth, $endMonth])->pluck('attendence_id')->toArray();
        $employees_attendances = Employee::join('employee_attendances as ea','ea.employee_id','=','employees.id')
            ->join('employee_shifts as es','es.id','=','employees.shift_id')
            ->select('ea.id as attendence_id','ea.employee_id as employee_id','ea.attendance_date','es.start_time','es.extension_minutes','ea.clock_in_datetime')
            ->whereBetween('ea.attendance_date',[$startMonth, $endMonth])
            ->whereNotIn('ea.id', $existing_late_ids)
            ->whereNotNull('ea.clock_in_datetime')
            ->orderBy('ea.employee_id')
            ->get();
            
            foreach($employees_attendances as $employees_attendance)
            {
                $expected_clockin = Carbon::createFromFormat('Y-m-d H:i:s', $employees_attendance->attendance_date.$employees_attendance->start_time)->addMinutes((int)$employees_attendance->extension_minutes);
                $clock_in = Carbon::parse($employees_attendance->clock_in_datetime);
                $time_diff = $expected_clockin->diffInMinutes(Carbon::parse($clock_in), false);
                if($time_diff > 0)
                {
                    EmployeeLate::create(
                        [
                            'attendence_id' => $employees_attendance->attendence_id,
                            'attendence_date' => $employees_attendance->attendance_date,
                            'date' => $endMonth,
                        ]);
                    $counter++;
                }
                $no_of_late = EmployeeLate::whereBetween('attendance_date',[$startMonth, $endMonth])
                ->count();
                // $no_of_late = EmployeeLate::join('employee_attendances as ea','ea.id','=','employee_lates.attendence_id')
                // ->whereBetween('ea.attendance_date',[$startMonth, $endMonth])
                // ->count();
                $employee_penalties = EmployeePenalty::where('employee_id',$employees_attendance->employee_id)->first(); 
                if($employee_penalties != null)
                {
                    if($counter == 3)
                    {
                        $employee_penalties->deduction_count += 1;
                        $counter = 0;
                    }
                    $employee_penalties->no_of_late = $no_of_late;
                    $employee_penalties->update();
                }
                else
                {

                    if($counter == 3)
                    { 
                        EmployeePenalty::create(
                        [
                            'employee_id' => $employees_attendance['employee_id'],
                            'attendence_id' => $employees_attendance['attendence_id'],
                            'date' => $endMonth,
                            'status' => 1,
                            'no_of_late' => $no_of_late,
                            'deduction_count' => 1,
                        ]);
                    $counter = 0;
                    }
                }
            }
    }
}
