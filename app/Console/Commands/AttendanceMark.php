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
        $day_carbon = Carbon::yesterday();
        $yesterday = $day_carbon->copy()->format('Y-m-d');
        $today_date = $day_carbon->copy()->format('d');
        $today_month = $day_carbon->copy();
        $today_year = $day_carbon->copy()->format('Y');

        $admins = Admin::where('status', '!=', 0);
        if($admins->exists())
        {
            $admins = $admins->pluck('id')->toArray();

            $admins_with_attendance = EmployeeAttendance::whereDate('attendance_date',$yesterday)->whereIn('employee_id',$admins)->where('employee_type', 1)->pluck('employee_id')->toArray();
            $new_admins = array_diff($admins,$admins_with_attendance);
            if($today_date < 26) {
                $prev_month = $today_month->copy()->subMonth(1)->month;
                $from = new \DateTime(Carbon::createFromDate($today_year,$prev_month,26)->toDateString());
                $to = new \DateTime($day_carbon->copy()->toDateString());
                $to = $to->modify( '+1 day' );
                $interval = new \DateInterval('P1D');
                $daterange = new \DatePeriod($from, $interval ,$to);
                foreach ($new_admins as $admin) {
                    foreach ($daterange as $date) {
                        $check = EmployeeAttendance::whereDate('attendance_date', $date)->where('employee_type', 1)->where('employee_id',$admin);
                        if($check->doesntExist()) {
                            $attendance = new EmployeeAttendance();
                            $attendance->employee_id = $admin;
                            $attendance->employee_type = 1;
                            $attendance->attendance_date = $date;
                            $attendance->save();
                        }
                    }
                }
            }
            else if($today_date > 26)
            {
                $month = $today_month->copy()->month;
                $from = new \DateTime(Carbon::createFromDate($today_year,$month,26)->toDateString());
                $to = new \DateTime($day_carbon->copy()->toDateString());
                $to = $to->modify( '+1 day' );
                $interval = new \DateInterval('P1D');
                $daterange = new \DatePeriod($from, $interval ,$to);
                foreach ($new_admins as $admin) {
                    foreach ($daterange as $date) {
                        $check = EmployeeAttendance::whereDate('attendance_date', $date)->where('employee_type', 1)->where('employee_id',$admin);
                        if($check->doesntExist()) {
                            $attendance = new EmployeeAttendance();
                            $attendance->employee_id = $admin;
                            $attendance->employee_type = 1;
                            $attendance->attendance_date = $date;
                            $attendance->save();
                        }
                    }
                }
            }
        }

        $riders = Rider::where('status', 1);
        if($riders->exists())
        {
            $riders = $riders->pluck('id')->toArray();

            $riders_with_attendance = EmployeeAttendance::whereDate('attendance_date',$yesterday)->whereIn('employee_id',$riders)->where('employee_type', 2)->pluck('employee_id')->toArray();
            $new_riders = array_diff($riders,$riders_with_attendance);
            if($today_date < 26) {
                $prev_month = $today_month->copy()->subMonth(1)->month;
                $from = new \DateTime(Carbon::createFromDate($today_year,$prev_month,26)->toDateString());
                $to = new \DateTime($day_carbon->copy()->toDateString());
                $to = $to->modify( '+1 day' );
                $interval = new \DateInterval('P1D');
                $daterange = new \DatePeriod($from, $interval ,$to);
                foreach ($new_riders as $rider) {
                    foreach ($daterange as $date) {
                        $check = EmployeeAttendance::whereDate('attendance_date', $date)->where('employee_type', 2)->where('employee_id',$rider);
                        if($check->doesntExist()) {
                            $attendance = new EmployeeAttendance();
                            $attendance->employee_id = $rider;
                            $attendance->employee_type = 2;
                            $attendance->attendance_date = $date;
                            $attendance->save();
                        }
                    }
                }
            }
            else if($today_date > 26)
            {
                $month = $today_month->copy()->month;
                $from = new \DateTime(Carbon::createFromDate($today_year,$month,26)->toDateString());
                $to = new \DateTime($day_carbon->copy()->toDateString());
                $to = $to->modify( '+1 day' );
                $interval = new \DateInterval('P1D');
                $daterange = new \DatePeriod($from, $interval ,$to);
                foreach ($new_riders as $rider) {
                    foreach ($daterange as $date) {
                        $check = EmployeeAttendance::whereDate('attendance_date', $date)->where('employee_type', 2)->where('employee_id',$rider);
                        if($check->doesntExist()) {
                            $attendance = new EmployeeAttendance();
                            $attendance->employee_id = $rider;
                            $attendance->employee_type = 2;
                            $attendance->attendance_date = $date;
                            $attendance->save();
                        }
                    }
                }
            }
        }

        $admin_ids = [];
        $rider_ids = [];
        $today = Carbon::now()->format('Y-m-d');
        $admin_attendance = EmployeeAttendance::whereDate('attendance_date', $today)->where('employee_type', 1);
        $rider_attendance = EmployeeAttendance::whereDate('attendance_date', $today)->where('employee_type', 2);
        if ($admin_attendance->exists()) {
            $admin_ids = $admin_attendance->pluck('employee_id')->toArray();
            $admins = Admin::whereNotIn('id', $admin_ids)->where('status', '!=', 0);
        }
        else {
            $admins = Admin::where('status', '!=', 0);
        }

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

        if ($rider_attendance->exists()) {
            $rider_ids = $rider_attendance->pluck('employee_id')->toArray();
            $riders = Rider::whereNotIn('id', $rider_ids)->where('status', 1);
        }
        else {
            $riders = Rider::where('status', 1);
        }
        
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
