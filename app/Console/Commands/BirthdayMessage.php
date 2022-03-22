<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\HR\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BirthdayMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:birthdaymessage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Birthday Message';

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
        $date = Carbon::today()->toDateString();
        $employees = Employee::whereDate('date_of_birth', $date);
        if($employees->exists()){
            $employees = $employees->get();
            foreach ($employees as $employee){
                NotificationsController::send(171, $employee->name, $employee->phone_number);
            }
        }
    }
}
