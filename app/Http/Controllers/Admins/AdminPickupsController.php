<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\Shipment;
use App\Http\Models\Pickup;
use App\Http\Models\PickupsJourney;

use Auth;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class AdminPickupsController extends Controller
{
    static public function generate($shipment_id) {
      $shipment = Shipment::find($shipment_id);

      $pickup = Pickup::whereDate('pickup_date', $shipment->pickup_date)->where('pickup_address_id', $shipment->pickup_address_id)->where('status_id', 0);

      if ($pickup->exists()) {
        $pickup = $pickup->first();

        $bookings = $pickup->bookings + 1;
        $total_estimated_weight = $pickup->total_estimated_weight + $shipment->estimated_weight;

        if ($total_estimated_weight < 10) {
          $pickup_type = 0;
        }
        else {
          $pickup_type = 1;
        }

        $pickup->bookings = $bookings;
        $pickup->total_estimated_weight = $total_estimated_weight;
        $pickup->pickup_type = $pickup_type;

        $pickup->save();

        PickupsJourneyController::add($pickup->id, 0, 'Another Shipment has been added to the Pickup!', Auth::id(), NULL);
      }
      else {
        $pickup = new Pickup();

        $pickup->shipper_id = Auth::id();
        $pickup->pickup_address_id = $shipment->pickup_address_id;
        $pickup->bookings = 1;
        $pickup->total_estimated_weight = $shipment->estimated_weight;

        if ($shipment->estimated_weight < 10) {
          $pickup->pickup_type = 0;
        }
        else {
          $pickup->pickup_type = 1;
        }

        $pickup->pickup_date = $shipment->pickup_date;
        $pickup->status_id = 0;

        $pickup->save();

        PickupsJourneyController::add($pickup->id, 0, 'Pickup has been Generated!', Auth::id(), NULL);
      }
    }

    public function __construct() {
      $this->middleware('auth:admin');
    }

    public function pending_index() {
      return view('admin.pickups.pending.index');
    }

    public function pending_list(Request $request) {
      $pickups = Pickup::join('users as u', 'pickups.shipper_id', '=', 'u.id')
      ->join('user_shipping_infos as usi', 'pickups.pickup_address_id', '=', 'usi.id')
      ->join('city_infos AS ci', 'usi.city_code', '=', 'ci.city_code')
      ->select('pickups.id', 'pickups.created_at as requested_at', 'u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.city_name AS city', 'pickups.bookings', 'pickups.pending_bookings', 'pickups.total_estimated_weight', 'pickups.pickup_type', 'pickups.pickup_date')
      ->where('pickups.status_id', 0);


      $datatables = Datatables::of($pickups)
      ->editColumn('requested_at', function($pickup) {
        return Carbon::parse($pickup->requested_at)->format('d/m/Y H:i A');
      })
      ->editColumn('total_estimated_weight', '{{ floatval($total_estimated_weight) }}')
      ->editColumn('pickup_type', function($pickup) {
        return ($pickup->pickup_type == 0) ? 'Light' : 'Heavy';
      })
      ->editColumn('pickup_date', function($pickup) {
        return Carbon::parse($pickup->pickup_date)->format('d/m/Y');
      })
      ->addColumn('action', function($receiving_sheet) {
        return '<button class="btn btn-sm btn-danger cancel">Cancel</button>';
      })
      ->filterColumn('pickup_type', function($query, $keyword) {
        $keyword = strtolower($keyword);

        if (strpos('light', $keyword) !== FALSE) {
          $query->where('pickups.pickup_type', '=', 0);
        }
        else if (strpos('heavy', $keyword) !== FALSE) {
          $query->where('pickups.pickup_type', '=', 1);
        }
        else {
          $query->whereRaw('false');
        }
      });

      return $datatables->make(true);
    }

    public function pending_store(Request $request) {
      $pickup_ids = $request->input('pickup_ids');
      $rider_id = $request->input('rider_id');

      foreach ($pickup_ids as $pickup_id) {
        $pickup = Pickup::find($pickup_id);

        if ($pickup->status_id == 0) {
          return ['status' => 1, 'error' => 'One of the Pickup(s) has already been modified'];
        }
      }

      foreach ($pickup_ids as $pickup_id) {
        $pickup = Pickup::find($pickup_id);

        $pickup->rider_id = $rider_id;
        //Assigned Date
        $pickup->status_id = 1;

        $pickup->save();
      }

      return ['status' => 0, 'success' => 'Pickup(s) has been Assigned to the Rider'];
    }

    public function pending_multiple_cancel(Request $request) {
      $pickup_ids = $request->input('pickup_ids');

      foreach ($pickup_ids as $pickup_id) {
        $pickup = Pickup::find($pickup_id);

        if ($pickup->status_id != 0) {
          return ['status' => 1, 'error' => 'One of the Pickup(s) has already been modified'];
        }
      }

      foreach ($pickup_ids as $pickup_id) {
        $pickup = Pickup::find($pickup_id);

        $pickup->status_id = 8; //Confirm for Cancel

        $pickup->save();
      }

      return ['status' => 0, 'success' => 'Pickup(s) has been Cancelled'];
    }

    public function pending_cancel(Request $request) {
      $pickup_id = $request->input('pickup_id');

      $pickup = Pickup::find($pickup_id);

      if ($pickup->status_id == 0) {
        $pickup->status_id = 8; //Confirm for Cancel

        $pickup->save();

        return ['status' => 0, 'success' => 'Pickup has been Cancelled'];
      }
      else {
        return ['status' => 1, 'error' => 'Selected Pickup has already been modified'];
      }
    }
}