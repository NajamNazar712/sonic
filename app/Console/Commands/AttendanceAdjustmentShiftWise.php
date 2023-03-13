<?php

namespace App\Console\Commands;

use App\Http\Models\HR\Employee;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Http\Controllers\NotificationsController;


class AttendanceAdjustmentShiftWise extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:attendanceadjustment {shift_id} {notify_for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Employee Attendance Adjustment Command';

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
        $shift_id = $this->argument('shift_id');
        $notify_for = $this->argument('notify_for');
        $today = Carbon::now()->format('Y-m-d');
        $todayname = Carbon::now();
        $dayname =  $todayname->format('l');
        $dateAndDay =  $today.' on '.$dayname;
        $check_employee_attendences = Employee::join('employee_attendances as ea','ea.employee_id','=','employees.id')
        ->where('ea.attendance_date',"!=",$today)
        ->whereNotNull('official_email')
        ->whereNull('clock_in')
        ->where('shift_id', $shift_id)
        ->distinct()
        ->select('employees.official_email as email','ea.employee_id as employee_attendence_id','ea.clock_in as clock_in')->get();
        foreach($check_employee_attendences as $check_employee_attendence)
        {
            if($notify_for == 'web')
                NotificationsController::send(211,$check_employee_attendence->email,$dateAndDay);
            elseif ($notify_for == 'app')
                // NotificationsController::app_notification(211,$check_employee_attendence->email,$dateAndDay);
        }
    }
}
