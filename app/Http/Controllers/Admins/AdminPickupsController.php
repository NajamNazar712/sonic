<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\Shipment;
use App\Http\Models\PickupRequest;

// use DB;
use Auth;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class AdminPickupsController extends Controller
{
    static public function generate_pickup_request($shipment_id) {
      $shipment = Shipment::find($shipment_id);

      $pickup_request = PickupRequest::whereDate('pickup_date', $shipment->pickup_date)->where('pickup_address_id', $shipment->pickup_address_id)->where('status_id', 0);

      if ($pickup_request->exists()) {
        $pickup_request = $pickup_request->first();

        $no_of_bookings = $pickup_request->no_of_bookings + 1;
        $shipments_estimated_weight = $pickup_request->shipments_estimated_weight + $shipment->estimated_weight;

        if (shipments_estimated_weight < 10) {
          $pickup_type = 0;
        }
        else {
          $pickup_type = 1;
        }

        $pickup_request->no_of_bookings = $no_of_bookings;
        $pickup_request->shipments_estimated_weight = $shipments_estimated_weight;
        $pickup_request->pickup_type = $pickup_type;

        $pickup_request->save();
      }
      else {
        $pickup_request = new PickupRequest();

        $pickup_request->shipper_id = Auth::id();
        $pickup_request->pickup_address_id = $shipment->pickup_address_id;
        $pickup_request->no_of_bookings = 1;
        $pickup_request->shipments_estimated_weight = $shipment->estimated_weight;

        if ($shipment->estimated_weight < 10) {
          $pickup_request->pickup_type = 0;
        }
        else {
          $pickup_request->pickup_type = 1;
        }

        $pickup_request->pickup_date = $shipment->pickup_date;
        $pickup_request->status_id = 0;

        $pickup_request->save();
      }
    }

    public function __construct() {
      $this->middleware('auth:admin');
    }

    public function pending_index() {
      return view('admin.pickups.pending.index');
    }

    public function pending_list(Request $request) {
      $pickup_requests = PickupRequest::join('users as u', 'pickup_requests.shipper_id', '=', 'u.id')
      ->join('user_shipping_infos as usi', 'pickup_requests.pickup_address_id', '=', 'usi.id')
      ->join('city_infos AS ci', 'usi.city_code', '=', 'ci.city_code')
      ->select('pickup_requests.id', 'pickup_requests.created_at as requested_at', 'u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.city_name AS city', 'pickup_requests.bookings', 'pickup_requests.pending_bookings', 'pickup_requests.total_estimated_weight', 'pickup_requests.pickup_type', 'pickup_requests.pickup_date')
      ->where('pickup_requests.status_id', 0);


      $datatables = Datatables::of($pickup_requests)
      ->editColumn('requested_at', function($pickup_request) {
        return Carbon::parse($pickup_request->requested_at)->format('d/m/Y H:i A');
      })
      ->editColumn('total_estimated_weight', '{{ floatval($total_estimated_weight) }}')
      ->editColumn('pickup_type', function($pickup_request) {
        return ($pickup_request->pickup_type == 0) ? 'Light' : 'Heavy';
      })
      ->editColumn('pickup_date', function($pickup_request) {
        return Carbon::parse($pickup_request->pickup_date)->format('d/m/Y');
      })
      ->addColumn('action', function($receiving_sheet) {
        return '<button class="btn btn-sm btn-danger cancel">Cancel</button>';
      })
      ->filterColumn('pickup_type', function($query, $keyword) {
        $keyword = strtolower($keyword);

        if (strpos('light', $keyword) !== FALSE) {
          $query->where('pickup_requests.pickup_type', '=', 0);
        }
        else if (strpos('heavy', $keyword) !== FALSE) {
          $query->where('pickup_requests.pickup_type', '=', 1);
        }
        else {
          $query->whereRaw('false');
        }
      });

      return $datatables->make(true);
    }

    public function pending_store(Request $request) {
      $pickup_request_ids = $request->input('pickup_request_ids');
      $rider_id = $request->input('rider_id');

      foreach ($pickup_request_ids as $pickup_request_id) {
        $pickup_request = PickupRequest::find($pickup_request_id);

        if ($pickup_request->status_id == 0) {
          return ['status' => 1, 'error' => 'One of the Pickup Request(s) has already been modified'];
        }
      }

      foreach ($pickup_request_ids as $pickup_request_id) {
        $pickup_request = PickupRequest::find($pickup_request_id);

        $pickup_request->rider_id = $rider_id;
        //Assigned Date
        $pickup_request->status_id = 1;

        $pickup_request->save();
      }

      return ['status' => 0, 'success' => 'Pickup Request(s) has been Assigned to the Rider'];
    }

    public function pending_multiple_cancel(Request $request) {
      $pickup_request_ids = $request->input('pickup_request_ids');

      foreach ($pickup_request_ids as $pickup_request_id) {
        $pickup_request = PickupRequest::find($pickup_request_id);

        if ($pickup_request->status_id != 0) {
          return ['status' => 1, 'error' => 'One of the Pickup Request(s) has already been modified'];
        }
      }

      foreach ($pickup_request_ids as $pickup_request_id) {
        $pickup_request = PickupRequest::find($pickup_request_id);

        $pickup_request->status_id = 8; //Confirm for Cancel

        $pickup_request->save();
      }

      return ['status' => 0, 'success' => 'Pickup Request(s) has been Cancelled'];
    }

    public function pending_cancel(Request $request) {
      $pickup_request_id = $request->input('pickup_request_id');

      $pickup_request = PickupRequest::find($pickup_request_id);

      if ($pickup_request->status_id == 0) {
        $pickup_request->status_id = 8; //Confirm for Cancel

        $pickup_request->save();

        return ['status' => 0, 'success' => 'Pickup Request has been Cancelled'];
      }
      else {
        return ['status' => 1, 'error' => 'Selected Pickup Request has already been modified'];
      }
    }
}