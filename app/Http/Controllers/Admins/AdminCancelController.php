<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\ShipmentsJourneyController;

use App\Http\Models\Shipment;

use Auth;

use Carbon\Carbon;

class AdminCancelController extends Controller
{
    public function __construct() {
      $this->middleware('auth:admin');
    }

    static public function cancel() {
        $days = 3;

        $date = Carbon::now()->subDays($days);

        $shipments = Shipment::where('shipper_status_id', 1)->where('created_at', '<', $date);

        if ($shipments->exists()) {
            foreach ($shipments->get() as $shipment) {
                $shipment->shipper_status_id = 17;
                $shipment->consignee_status_id = 17;

                $shipment->save();

                AdminPickupsController::cancel($shipment->id);

                ShipmentsJourneyController::add($shipment->id, 17, 17, NULL, 'Auto Cancellation after ' . $days . ' Day(s)', $shipment->user_id, NULL);
            }
        }
    }

    public function index(Request $request) {
    }

}