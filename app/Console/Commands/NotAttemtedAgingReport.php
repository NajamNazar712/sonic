<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotAttemtedAgingReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:notattemptedagingreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Not Attempted Aging Report';

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
        $sixtyEight_notification = Notification::find(68);
        $sixtyNine_notification = Notification::find(69);
        $seventy_notification = Notification::find(70);
        if ($sixtyEight_notification || $sixtyNine_notification || $seventy_notification) {
            if ($sixtyEight_notification->status == 1 || $sixtyNine_notification->status == 1 || $seventy_notification->status == 1) {
                $date = Carbon::today()->format('Y-m-d');
                $response = AdminReportsEmailController::not_attempted_aging($date);
            }

        }

    }
}
