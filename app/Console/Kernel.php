<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        '\App\Console\Commands\ReturnConfirmationPendingEmail',
        '\App\Console\Commands\ReturnConfirmEmail',
        '\App\Console\Commands\ShipmentReAttemptEmail'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('email:returnconfirmationpending')->dailyAt('10:00')->runInBackground();
        $schedule->command('email:returnconfirm')->dailyAt('15:00')->runInBackground();
        $schedule->command('email:shipmentreattempt')->dailyAt('08:00')->runInBackground();
        $schedule->command('shipment:cancel')->dailyAt('00:00')->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
