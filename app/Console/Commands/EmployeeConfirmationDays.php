<?php

namespace App\Console\Commands;

use App\Http\Models\HR\Employee;
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
        $employees = Employee::where('confirmation_status',2)->where('status',1)->get();

        if($employees){
            foreach ($employees as $employee) {
                
            }
        }
    }
}
