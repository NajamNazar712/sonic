<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AccountBlockageEmailDraftController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class NonComplianceOfRequirements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:NonComplianceOfRequirements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'NonComplianceOfRequirements';

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
        $date = Carbon::today()->subDays(7);
        $response = AccountBlockageEmailDraftController::non_compliance($date);
        NotificationsController::send(93, $date, $response);
    }
}
