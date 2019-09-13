<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Models\PickupRequest;

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
        $pickup_requests = PickupRequest::where('status', 0);

        if ($pickup_requests->exists()) {
            $pickup_requests = $pickup_requests->get();

            foreach ($pickup_requests as $pickup_request) {
                $assigned_shipments = $pickup_request->pickup_request_assigned_shipments;

                if ($assigned_shipments->count() != 0) {
                    $clear = TRUE;

                    foreach ($assigned_shipments as $assigned_shipment) {
                        if ($assigned_shipment->shipment->shipper_status_id == 1) {
                            $clear = FALSE;

                            break;
                        }
                    }

                    if ($clear) {
                        $pickup_request->status = 3;

                        $pickup_request->save();
                    }
                }
            }
        }
    }
}
