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
    protected $signature = 'employee:attendenceadjustment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Employee Attendence Adjustment Command';

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
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        $yesterdayname = Carbon::yesterday();
        $dayname =  $yesterdayname->format('l');
        $dateAndDay =  'Date:'. $yesterday.' & '.$dayname;
        $check_employee_attendences = Employee::join('employee_attendances as ea','ea.employee_id','=','employees.id')->where('ea.attendance_date',$yesterday)->select('employees.official_email as email','ea.employee_id as employee_attendence_id','ea.clock_in as clock_in')->get();
        foreach($check_employee_attendences as $check_employee_attendence)
        {
            if($check_employee_attendence->clock_in == null)
            {   
                if($check_employee_attendence->email)
                {   
                    NotificationsController::send(211,$check_employee_attendence->email,$dateAndDay);
                }
            }
        }
    }
}
