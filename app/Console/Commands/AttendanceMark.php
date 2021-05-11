<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Rider;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AttendanceMark extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:markabsent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark Everyone Absent On the day start so that they can mark themselves present later';

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
        $admin_ids = [];
        $rider_ids = [];
        $today = Carbon::now()->format('Y-m-d');
        $admin_attendance = EmployeeAttendance::whereDate('attendance_date', $today)->where('employee_type', 1);
        $rider_attendance = EmployeeAttendance::whereDate('attendance_date', $today)->where('employee_type', 2);
        if ($admin_attendance->exists()) {
            $admin_ids = $admin_attendance->pluck('employee_id')->toArray();
            $admins = Admin::whereNotIn('id',$admin_ids)->where('status','!=',0);
            if($admins->exists())
            {
                $admins = $admins->get();
                foreach ($admins as $admin)
                {
                    $attendance = new EmployeeAttendance();
                    $attendance->employee_id = $admin->id;
                    $attendance->employee_type = 1;
                    $attendance->attendance_date = $today;
                    $attendance->save();
                }
            }
        }

        if ($rider_attendance->exists()) {
            $rider_ids = $rider_attendance->pluck('employee_id')->toArray();
            $riders = Rider::whereNotIn('id',$rider_ids)->where('status',1);
            if($riders->exists())
            {
                $riders = $riders->get();
                foreach ($riders as $rider)
                {
                    $attendance = new EmployeeAttendance();
                    $attendance->employee_id = $rider->id;
                    $attendance->employee_type = 2;
                    $attendance->attendance_date = $today;
                    $attendance->save();
                }
            }
        }
        
    }
}
