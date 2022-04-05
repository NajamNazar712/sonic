<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Rider;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2RiderPickup;
use App\Http\Models\WMS\WmsPickupRun;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RiderDeactivateAutomatically extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:RiderDeactivateAutomaticallyAndGenerateEmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rider Deactivate Automatically After Week which are not active';

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
        $date_week_age = Carbon::now()->subDays(3)->toDateTimeString();
        $today = Carbon::now()->toDateTimeString();
        $rider_data = '';
        $rider_ids = Rider::where('status', 1)->whereDate('created_at', '<', $date_week_age)->pluck('id')->toArray();
        $deliveries  = DeliveryNote::whereBetween('created_at', [$date_week_age, $today])->pluck('rider_id')->toArray();
        $v2_pickups  = V2PickupNote::whereBetween('created_at', [$date_week_age, $today])->pluck('rider_id')->toArray();
        $return_notes  = ReturnNote::whereBetween('created_at', [$date_week_age, $today])->pluck('rider_id')->toArray();
        $wms_pickup_run  = WmsPickupRun::whereBetween('created_at', [$date_week_age, $today])->pluck('wms_rider_id')->toArray();
        $rider_active = array_unique(array_merge($deliveries, $v2_pickups, $return_notes, $wms_pickup_run));
        $data = array_diff($rider_ids, $rider_active);
        if ($data != null){
            NotificationsController::send(155, $data);
        }
    }
}
