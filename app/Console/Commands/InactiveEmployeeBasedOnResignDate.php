<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\Admin;
use App\Http\Models\FnfSectionEmployee;
use App\Http\Models\HR\Employee;
use App\Http\Models\Rider;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InactiveEmployeeBasedOnResignDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inactive_employee:resign_date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inactive Employee Based On Resign Date Of FNF';

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
        $today = Carbon::now()->toDateString();

        $data = FnfSectionEmployee::rightjoin('employees as e', 'e.id', 'fnf_section_employees.employee_id')
            ->where('fnf_section_employees.status_id', 4)
            ->where('fnf_section_employees.employee_inactive', 0)
            ->where('e.status_id', '!=', 2)
            ->where('fnf_section_employees.last_working_date', '<=', $today)
            ->select(['e.id as employee_id', 'fnf_section_employees.id as fnf'])
            ->get();

        $employee_ids = [];
        if (count($data) > 0) {
            foreach ($data as $datum) {
                array_push($employee_ids, $datum->employee_id);
                $fnf = FnfSectionEmployee::find($datum->fnf);
                if ($fnf) {
                    $fnf->employee_inactive = 1;
                    $fnf->update();
                }
            }


            $employees = Employee::whereIn('id', $employee_ids)->get();
            foreach ($employees as $employee) {
                $employee->status_id = 2;
                $employee->update();

                if ($employee->employee_type_id == 1) {
                    $admin = Admin::where('trax_id', $employee->trax_id)->where('trax_id', '!=', null)->first();
                    if ($admin) {
                        $admin->status = 0;
                        $admin->update();
                    }
                } else if ($employee->employee_type_id == 2) {
                    $rider = Rider::where('trax_id', $employee->trax_id)->where('trax_id', '!=', null)->first();
                    if ($rider) {
                        $rider->status = 0;
                        $rider->route_id = null;
                        $rider->update();
                    }
                }
            }
        }
    }
}
