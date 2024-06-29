<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Logistic\TraxLogisticBooking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class HourlyShipperLogisticBookingEmailCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hourly-logistic:shipper-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This CRON job sends an email to shippers with logistic booking details hourly basis. check if email generated or not if not than send email';

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
        $date=Carbon::now()->toDateString();
        $shippers=TraxLogisticBooking::join('users as u','u.id','trax_logistic_bookings.shipper_id')
            ->where('trax_logistic_bookings.booking_date',$date)
            ->where('trax_logistic_bookings.is_email',0)
            ->groupBy('trax_logistic_bookings.shipper_id');

        if($shippers->exists())
        {
            $shippers=$shippers->pluck('trax_logistic_bookings.shipper_id','u.email');
            Log::channel('code_test_log')->info('logisticbooking-1'.json_encode($shippers));

            NotificationsController::send(233,$shippers,$date);
        }

    }
}
