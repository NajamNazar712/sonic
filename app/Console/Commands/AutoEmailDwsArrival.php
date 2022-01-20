<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\DwsDetail;
use App\Http\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoEmailDwsArrival extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:dwsarrival';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shipment Arrived at Origin from DWS';

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
        $startTime = Carbon::yesterday();
        $endTime = Carbon::now();
        $startTime->hour = 17;
        $startTime->minute = 1;
        $endTime->hour = 17;
        $endTime->minute = 00;
        $shipment_ids = DwsDetail::whereBetween('created_at', [$startTime, $endTime])->pluck('shipment_id')->toArray();
    //    dd($shipment_ids);
        NotificationsController::send(166,$shipment_ids);
    }
}
