<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeLeave;


trait CommonTrait
{
    public function getAvailedLeaves($employee_id)
    {
        $employee = Employee::find($employee_id);
        if($employee){
            $weekend_days = [];
            $working_days = 1;
            if (isset($employee->department) && $employee->department->working_days == 1) {
                $working_days = 1;
                // Sunday is off
                $weekend_days = [Carbon::SUNDAY];
            } else if (isset($employee->department) && $employee->department->working_days == 2){
                $working_days = 2;
                // Saturday and Sunday are off
                $weekend_days = [Carbon::SATURDAY, Carbon::SUNDAY];
            }
            $leaves_availed = EmployeeLeave::where('employee_id', $employee->id)
            ->whereIn('status', [2,4,6])
            ->whereIn('leave_type', [1,2,3,4])
            ->get()
            ->filter(function ($leave) {
                return $leave->from <= $leave->to;
            })
            ->sum(function ($leave) use ($weekend_days, $working_days) {
                $leave_days = Carbon::parse($leave->from)->diffInDaysFiltered(function (Carbon $date) use ($weekend_days) {
                    return !in_array($date->dayOfWeek, $weekend_days);
                }, $leave->to);
                if ($working_days == 2) {
                    $leave_days -= Carbon::parse($leave->from)->isWeekend() ? 1 : 0;
                }
                return $leave_days + 1;
            });
            return $leaves_availed;
        }
        return 0;
    }

    public function calculateToDateLeaves($employee, $toDate)
    {
        try {
            $start = Carbon::now()->startOfMonth();
            $to_date = Carbon::parse($toDate)->startOfMonth();
            $to_date_month = Carbon::parse($toDate)->month;
            $difference = $to_date->diffInMonths($start);
            // If Leaves apply for 2 or more than 2 days
            if($difference >= 2) {
                // If month is june, add 6 as per last months of fiscal year
                if($to_date_month == 6){
    
                    $nd = $difference - 2;
                    $result = $nd * 2;
                    $result = $result+6;
                } 
                // If month is may, add 3 as per second last month of fiscal year
                else if($to_date_month == 5){
    
                    $nd = $difference - 1;
                    $result = $nd * 2;
                    $result = $result+3;
                } else {
                    $result = $difference * 2;
                }
            }
            else if ($difference == 1){
                // If month is may or june, add 3 as per last month of fiscal year
                if($to_date_month == 5 || $to_date_month == 6){
                    $result = 3;
                }  else {
                    $result = 2;
                }
            } else {
                $result = $employee->leave_count;
            }
            return ['status' => 1, 'data' => $result];
        } catch (\Throwable $th) {
            return ['status' => 0, 'msg' => $th->getMessage()];
        }

    }
}
