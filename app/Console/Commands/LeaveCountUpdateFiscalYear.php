<?php

namespace App\Console\Commands;

use App\Http\Models\HR\Employee;
use Illuminate\Console\Command;

class LeaveCountUpdateFiscalYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:leave_count_fiscal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update leave count for employee every year';

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
        $month = Carbon::now()->month;
        $employee_leave_log = EmployeeLeaveLog::where('month',$month);
        if(!$employee_leave_log->exists()){
            if($month == 7){
                Employee::where('employee_type_id',1)->where('confirmation_status',2)->update(['leave_count' => 1]);
                Employee::where('employee_type_id',1)->where('confirmation_status',1)->update(['leave_count' => 2]);
            }elseif($month == 6 || $month == 5){
                $one = 1;
                $two = 3;
                Employee::where('employee_type_id',1)->where('confirmation_status',2)->update(['leave_count' => DB::raw('leave_count + '.$one)]);
                Employee::where('employee_type_id',1)->where('confirmation_status',1)->update(['leave_count' => DB::raw('leave_count + '.$two)]);
            }else{
                $one = 1;
                $two = 2;
                Employee::where('employee_type_id',1)->where('confirmation_status',2)->update(['leave_count' => DB::raw('leave_count + '.$one)]);
                Employee::where('employee_type_id',1)->where('confirmation_status',1)->update(['leave_count' => DB::raw('leave_count + '.$two)]);
            }
            EmployeeLeaveLog::create([
                'month' => $month
            ]);

        }
    }
}
