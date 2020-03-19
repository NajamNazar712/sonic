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
        '\App\Console\Commands\GenerateInvoice',
        '\App\Console\Commands\ClearSMS',
        '\App\Console\Commands\OperationForecastHourlyUpdate',
        '\App\Console\Commands\ReturnNoteImageArchive',
        '\App\Console\Commands\StationDepositNoteImageArchive',
		'\App\Console\Commands\ReturnDeliveredToShipperEmail',
        '\App\Console\Commands\DebriefingEmail',
        '\App\Console\Commands\ClearPickupRequest',
        '\App\Console\Commands\ClearPickupNote',
        '\App\Console\Commands\SalePersonShipmentNumbers',
        '\App\Console\Commands\MonthAverageReportEmail',
        '\App\Console\Commands\HubWiseSplitEmail',
        '\App\Console\Commands\PettyCashImageArchive',
        '\App\Console\Commands\DailyFakeStatusReportEmail',
        '\App\Console\Commands\NegativeBalanceShipperSalesPerson',
        '\App\Console\Commands\ClearDefaultBankDuration',
        '\App\Console\Commands\OvernightCargoReport',
        '\App\Console\Commands\OverlandCargoReport',
		'\App\Console\Commands\AccountReconciliationReportFromStart',
        '\App\Console\Commands\AccountReconciliationReportCurrent'
];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sms:clear')->everyTenMinutes()->withoutOverlapping()->runInBackground();
        $schedule->command('email:returnconfirmationpending')->dailyAt('10:00')->runInBackground();
        $schedule->command('email:returnconfirm')->dailyAt('15:00')->runInBackground();
        $schedule->command('email:shipmentreattempt')->dailyAt('08:00')->runInBackground();
        $schedule->command('shipment:cancel')->dailyAt('00:00')->runInBackground();
        $schedule->command('shipper:disable')->dailyAt('00:00')->runInBackground();
        $schedule->command('email:dailyfakestatusreport')->dailyAt('06:00')->runInBackground();

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

        // $schedule->command('hourlyupdate:operationforecast')->cron('0 */2 * * *')->withoutOverlapping()->runInBackground();

        $schedule->command('archive:returnnoteimage')->dailyAt('00:00')->runInBackground();

        $schedule->command('archive:stationdepositnoteimage')->dailyAt('00:00')->runInBackground();

        $schedule->command('archive:pettycashimage')->dailyAt('00:00')->runInBackground();
		$schedule->command('email:debriefingemail')->dailyAt('00:00')->runInBackground();

        $settings = GlobalSettings::where('type', 'return_delivered_to_shipper_cut_off_time');

        if ($settings->exists()) {
            $settings = $settings->first();

            $rdts_time = $settings->setting_value . ':00';

            $schedule->command('email:returndeliveredtoshipper')->dailyAt($rdts_time)->runInBackground();
        }

        $schedule->command('pickuprequest:clear')->everyFifteenMinutes()->withoutOverlapping()->runInBackground();
        $schedule->command('pickupnote:clear')->everyThirtyMinutes()->withoutOverlapping()->runInBackground();
        $schedule->command('saleperson:numbers')->dailyAt('08:00')->runInBackground();
        $schedule->command('month:average')->dailyAt('08:00')->runInBackground();
        $schedule->command('hubwise:split')->dailyAt('08:00')->runInBackground();

        $schedule->command('email:negativebalanceshippersalesperson')->weeklyOn(1, '8:00')->runInBackground();

        $schedule->command('overnight:cargo_report')->dailyAt('00:00')->runInBackground();
        $schedule->command('overland:cargo_report')->dailyAt('00:00')->runInBackground();

//		$schedule->command('accounts:reconciliationcurrent')->monthly()->days([1,14,28])->runInBackground();
//      $schedule->command('accounts:reconciliationcurrent')->cron('0 0 1,14,28 * *'); //another solution
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
