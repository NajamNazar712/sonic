<?php

namespace App\Console;

use App\Http\Models\Admin\GlobalSettings;
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
        '\App\Console\Commands\ShipmentReAttemptEmail',
        '\App\Console\Commands\AutoDisableShipperAccount',
        '\App\Console\Commands\DailyPickupSalesEmail',
        '\App\Console\Commands\GenerateInvoice'
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
        $schedule->command('shipper:disable')->dailyAt('00:00')->runInBackground();

        $settings = GlobalSettings::where('type', 'daily_pickup_sales_cron_time');

        if ($settings->exists()) {
            $settings = $settings->first();

            $time = $settings->setting_value . ':00';

            $schedule->command('email:dailypickupsalesreport')->dailyAt($time)->runInBackground();
        }

        $settings = GlobalSettings::where('type', 'auto_invoice_generation_time');

        if ($settings->exists()) {
            $settings = $settings->first();

            $time = $settings->setting_value . ':00';

            $schedule->command('invoice:generate')->dailyAt($time)->runInBackground();
        }
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
