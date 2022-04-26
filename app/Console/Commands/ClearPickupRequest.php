<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\V2Pickup\V2PickupRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;


class ClearPickupRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pickuprequest:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Pickup Request';

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

        $settings = GlobalSettings::where('type', 'shipment_cancellation_cut_off_days')->first();

        $days = $settings->setting_value;

        $date = Carbon::now()->subDays($days);

        $pickup_requests = V2PickupRequest::whereIn('status', [1,3])->whereNotIn('shipper_id', [3324, 7762, 5982, 10104, 14110])->where('created_at', '<', $date);

        if ($pickup_requests->exists()) {
            $pickup_requests = $pickup_requests->get();

            foreach ($pickup_requests as $pickup_request) {
                $assigned_shipments = $pickup_request->pickup_request_shipments;

                if ($assigned_shipments->count() != 0) {
                    $clear = TRUE;

                    foreach ($assigned_shipments as $assigned_shipment) {
                        if ($assigned_shipment->shipment->shipper_status_id != 17) {
                            $clear = FALSE;

                            break;
                        }
                    }

                    if ($clear) {
                        $pickup_request->status = 4;

                        $pickup_request->save();
                    }
                }
            }
        }
    }
}
