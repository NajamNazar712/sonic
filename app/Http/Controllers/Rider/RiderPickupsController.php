<?php

namespace App\Http\Controllers\Rider;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;

use App\Http\Models\PickupNotPickReason;
use App\Http\Models\RiderPickup;
use App\Http\Models\RiderPickupShipment;
use App\Http\Models\PickupAction;
use App\Http\Models\RiderPickupActionLog;

use Auth;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class RiderPickupsController extends Controller {
	public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function pickups_index() {
        $pickup_types = [['id' => 0, 'text' => 'Not Pick'], ['id' => 1, 'text' => 'Pick']];
        $pickup_not_pick_reasons = PickupNotPickReason::all();

        return view('admin.pickups.rider.index')->with(['pickup_types' => $pickup_types, 'pickup_not_pick_reasons' => $pickup_not_pick_reasons]);
    }

    public function pickups_list(Request $request) {
    	$rider_pickups = RiderPickup::leftjoin('pickup_not_pick_reasons as pnpr', 'rider_pickups.pickup_not_pick_reason_id', 'pnpr.id')
    	->join('pickup_notes as pn', 'rider_pickups.pickup_note_id', 'pn.id')
    	->join('riders as r', 'pn.rider_id', 'r.id')
    	->join('pickup_requests as pr', 'rider_pickups.pickup_request_id', 'pr.id')
    	->join('users as u', 'pr.shipper_id', 'u.id')
    	->join('user_shipping_infos as usi', 'pr.pickup_address_id', 'usi.id')
    	->join('cities as c', 'usi.city_id', 'c.id')
    	->select('rider_pickups.id', 'rider_pickups.added_at', 'r.name as rider', 'u.name as shipper', 'usi.pickup_address', 'c.name as city', 'rider_pickups.pickup_type', 'rider_pickups.start_location_latitude', 'rider_pickups.start_location_longitude', 'rider_pickups.actual_location_latitude', 'rider_pickups.actual_location_longitude', 'rider_pickups.distance_from_start_to_actual', 'rider_pickups.current_location_latitude', 'rider_pickups.current_location_longitude', 'rider_pickups.distance_from_current_to_actual', 'rider_pickups.shipments', 'pnpr.name as reason', 'rider_pickups.picture_path', 'rider_pickups.pickup_note_id', 'rider_pickups.pickup_request_id');

        $datatables = Datatables::of($rider_pickups)
        ->editColumn('pickup_note_id', function ($rider_pickup) {
            return str_pad($rider_pickup->pickup_note_id, 6, '0', STR_PAD_LEFT);
        })
        ->editColumn('pickup_request_id', function ($rider_pickup) {
            return str_pad($rider_pickup->pickup_request_id, 6, '0', STR_PAD_LEFT);
        })
        ->editColumn('pickup_type', function ($rider_pickup) {
			if ($rider_pickup->pickup_type == 0) {
				return 'Not Pick';
			}
			else {
				return 'Pick';
			}
		})
		->editColumn('distance_from_start_to_actual', function ($rider_pickup) {
            $distance_from_start_to_actual = $rider_pickup->distance_from_start_to_actual;

            if ($distance_from_start_to_actual == 0) {
                $distance_from_start_to_actual = 0;
            }

			return '<a class="btn btn-sm btn-outline-info align-middle" href="http://maps.google.com/maps?saddr=' . $rider_pickup->start_location_latitude . ',' . $rider_pickup->start_location_longitude . '&daddr=' . $rider_pickup->actual_location_latitude . ',' . $rider_pickup->actual_location_longitude . '" target="_blank">' . $distance_from_start_to_actual . '</a>';
		})
        ->editColumn('distance_from_current_to_actual', function ($rider_pickup) {
            $distance_from_current_to_actual = $rider_pickup->distance_from_current_to_actual;

            if ($distance_from_current_to_actual == 0) {
                $distance_from_current_to_actual = 0;
            }

            if ($rider_pickup->current_location_latitude && $rider_pickup->current_location_longitude) {
                return '<a class="btn btn-sm btn-outline-info align-middle" href="http://maps.google.com/maps?saddr=' . $rider_pickup->current_location_latitude . ',' . $rider_pickup->current_location_longitude . '&daddr=' . $rider_pickup->actual_location_latitude . ',' . $rider_pickup->actual_location_longitude . '" target="_blank">' . $distance_from_current_to_actual . '</a>';
            }
            else {
                return $distance_from_current_to_actual;
            }
        })
        ->editColumn('shipments', function ($rider_pickup) {
            if ($rider_pickup->pickup_type == 1) {
                return '<a class="btn btn-sm btn-outline-info align-middle" href="#">' . $rider_pickup->shipments . '</a>';
            }
            else {
                return '';
            }
        })
		->editColumn('picture_path', function ($rider_pickup) {
			if ($rider_pickup->pickup_type == 0) {
				return '<a class="btn btn-sm btn-outline-info align-middle" href="' . asset('storage/' . $rider_pickup->picture_path) . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
			}
            else {
                return '';
            }
		});

    	return $datatables->make(true);
    }

    public function pickups_shipments(Request $request) {
        $rider_pickup_shipments = RiderPickupShipment::where('pickup_note_id', $request->rider_pickup_id);

        if ($rider_pickup_shipments->exists()) {
            $rider_pickup_shipments = $rider_pickup_shipments->get();

            $tracking_numbers = array();

            foreach ($rider_pickup_shipments as $rider_pickup_shipment) {
                $tracking_numbers[] = $rider_pickup_shipment->shipment->tracking_number;
            }

            return ['status' => 0, 'success' => 'Shipments Listed', 'tracking_numbers' => $tracking_numbers];
        }
        else {
            return ['status' => 1, 'error' => 'No Shipments'];
        }
    }

    public function pickups_action_log_index() {
        $pickup_actions = PickupAction::all();

        return view('admin.pickups.rider.action_log.index')->with(['pickup_actions' => $pickup_actions]);
    }

    public function pickups_action_log_list(Request $request) {
    	$rider_pickup_action_logs = RiderPickupActionLog::join('pickup_actions as pa', 'rider_pickup_action_logs.type_id', 'pa.id')
    	->join('pickup_notes as pn', 'rider_pickup_action_logs.pickup_note_id', 'pn.id')
    	->join('riders as r', 'pn.rider_id', 'r.id')
    	->join('pickup_requests as pr', 'rider_pickup_action_logs.pickup_request_id', 'pr.id')
    	->join('users as u', 'pr.shipper_id', 'u.id')
    	->join('user_shipping_infos as usi', 'pr.pickup_address_id', 'usi.id')
    	->join('cities as c', 'usi.city_id', 'c.id')
    	->select('rider_pickup_action_logs.id', 'rider_pickup_action_logs.logged_at', 'r.name as rider', 'u.name as shipper', 'usi.pickup_address', 'c.name as city', 'pa.name as type', 'rider_pickup_action_logs.pickup_note_id', 'rider_pickup_action_logs.pickup_request_id');

    	$datatables = Datatables::of($rider_pickup_action_logs)
        ->editColumn('pickup_note_id', function ($rider_pickup_action_log) {
            return str_pad($rider_pickup_action_log->pickup_note_id, 6, '0', STR_PAD_LEFT);
        })
        ->editColumn('pickup_request_id', function ($rider_pickup_action_log) {
            return str_pad($rider_pickup_action_log->pickup_request_id, 6, '0', STR_PAD_LEFT);
        });

    	return $datatables->make(true);
    }
}