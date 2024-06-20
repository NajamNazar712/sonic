<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Logistic\TraxLogisticBooking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ShipperLogisticBookingCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logistic:shipper-bookings';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'This CRON job sends an email to shippers with logistic booking details for the previous day.';

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
        $date=Carbon::now()->subDay()->toDateString();
//        $bookings=TraxLogisticBooking::join('users as u','u.id','trax_logistic_bookings.shipper_id')
//            ->join('trax_stations as ts','ts.id','trax_logistic_bookings.destination_id')
//            ->select('u.id AS shipper_id','u.name AS shipper_name','u.email AS shipper_email','trax_logistic_bookings.shipper_reference as order_reference','trax_logistic_bookings.booking_date','trax_logistic_bookings.cn_number AS tracking_number','ts.name AS destination','trax_logistic_bookings.total_booking_weight','trax_logistic_bookings.total_pieces')
//            ->where('trax_logistic_bookings.booking_date',$date);
        $shipper_ids=TraxLogisticBooking::where('trax_logistic_bookings.booking_date',$date)->groupBy('trax_logistic_bookings.shipper_id');
        if($shipper_ids->exists()){
            $shipper_ids=$shipper_ids->pluck('trax_logistic_bookings.shipper_id');
            NotificationsController::send(232,$shipper_ids,$date);
        }
    }
}
