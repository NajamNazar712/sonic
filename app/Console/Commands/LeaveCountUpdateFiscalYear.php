<?php

namespace App\Console\Commands;

use App\Http\Models\HR\EmployeeLeaveLogFiscal;
use App\Http\Models\HR\Employee;
use Carbon\Carbon;
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
        $employee_leave_log = EmployeeLeaveLogFiscal::where('month',$month);
        if(!$employee_leave_log->exists()){
            Employee::where('employee_type_id',1)->where('confirmation_status',2)->update(['fiscal_leave_count' => 3]);
            Employee::where('employee_type_id',1)->where('confirmation_status',1)->update(['fiscal_leave_count' => 26]);
            
            EmployeeLeaveLogFiscal::create([
                'month' => $month
            ]);

        }
    }
}
