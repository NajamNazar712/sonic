<?php

namespace App\Http\Traits;

use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeLeave;
use Carbon\Carbon;

trait CommonTrait
{
    public function getAvailedLeaves($employee_id)
    {
        $employee = Employee::find($employee_id);
        $weekend_days = [];
        if ($employee->department->working_days == 1) {
            // Sunday is off
            $weekend_days = [Carbon::SUNDAY];
        } else {
            // Saturday and Sunday are off
            $weekend_days = [Carbon::SATURDAY, Carbon::SUNDAY];
        }
        $availed_leaves = EmployeeLeave::selectRaw("SUM(
            CASE
                WHEN DATEDIFF(`to`, `from`) >= 0 THEN
                    DATEDIFF(`to`, `from`) + 1 -
                    (FLOOR((DATEDIFF(`to`, `from`) + WEEKDAY(`from`) + 1) / 7) * COUNT(?)) -
                    SUM(WEEKDAY(`date`) IN (?))
                ELSE 0
            END
        ) as leaves_availed", [$weekend_days, $weekend_days])
        ->where('employee_id', $employee->id)
        ->whereIn('status', [6])
        ->whereIn('leave_type', [1,2,3,4])
        ->value('leaves_availed'); 
        return $availed_leaves;
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
