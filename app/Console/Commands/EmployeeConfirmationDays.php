<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeConfirmation;
use App\Mail\Notifications;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EmployeeConfirmationDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:confirmation_days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for employees to make permanent after 85 days of time';

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
        $employees = Employee::where('confirmation_status',2)->where('employee_type_id',1)->whereIn('status_id',[1,3])->get();

        if($employees){
            foreach ($employees as $employee) {
                $now = Carbon::now();
                $difference = $now->diffInDays($employee->joining_date);
                if($difference >= 90) {
                    $check_employee_confirmation = EmployeeConfirmation::where('employee_id',$employee->id);
                    if(!$check_employee_confirmation->exists()){

                        $probation_end_date = Carbon::today()->toDateString();
                        $employee_confirmation = new EmployeeConfirmation();
                        $employee_confirmation->employee_id = $employee->id;
                        $employee_confirmation->probation_end_date = $probation_end_date;
                        $employee_confirmation->save();

                        NotificationsController::send(182, $employee);
                    }
                }
            }
        }
    }
}
