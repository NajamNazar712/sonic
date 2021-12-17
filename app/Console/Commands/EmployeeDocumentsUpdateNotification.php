<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\HR\Employee;
use Illuminate\Console\Command;

class EmployeeDocumentsUpdateNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee_directory:documents_update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Employee Documents Update Email';

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
        $employees = Employee::where('attachment_update', 1)->pluck('id')->toArray();
        if(!empty($employees)){
            NotificationsController::send(164, $employees, 0);
        }
    }
}
