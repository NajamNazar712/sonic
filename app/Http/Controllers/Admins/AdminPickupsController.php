<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\City;
use App\Http\Models\RiderCategory;
use App\Http\Models\Shipper\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\ShipmentsPickupJourneyController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Admins\ShipmentChargesController;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Notification;
use App\Http\Models\Dispute;
use App\Http\Models\DisputeShipment;
use App\Http\Models\Rider;
use App\Http\Models\Shipment;
use App\Http\Models\ReceivingSheet;
use App\Http\Models\ReceivingSheetShipment;
use App\Http\Models\ReceivingSheetReceived;
use App\Http\Models\PickupRequest;
use App\Http\Models\PickupRequestAssignedShipment;
use App\Http\Models\PickupRequestReceivedShipment;
use App\Http\Models\PickupRequestShortReceivedShipment;
use App\Http\Models\PickupNote;
use App\Http\Models\PickupNoteRequest;
use App\Http\Models\PickupNoteStatus;

use Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class AdminPickupsController extends Controller
{
    public function __construct() {
      $this->middleware('auth:admin');


      $this->middleware('Permission');
    }

    static public function generate($shipment_id) {
      $defined_pickup_weight = GlobalSettings::where('type', 'pickup_weight');

      if ($defined_pickup_weight->exists()) {
        $defined_pickup_weight = $defined_pickup_weight->first();

        $defined_pickup_weight = $defined_pickup_weight->setting_value;
      }
      else {
        $defined_pickup_weight = 10;
      }

      $shipment = Shipment::find($shipment_id);

      $pickup_request = PickupRequest::whereDate('pickup_date', $shipment->pickup_date)->where('pickup_address_id', $shipment->pickup_address_id)->where('status', 0);

      if ($pickup_request->exists()) {
        $pickup_request = $pickup_request->orderBy('id', 'DESC')->first();

        $bookings = $pickup_request->bookings + 1;
        $total_estimated_weight = $pickup_request->total_estimated_weight + $shipment->estimated_weight;

        if ($total_estimated_weight < $defined_pickup_weight) {
          $pickup_type = 0;
        }
        else {
          $pickup_type = 1;
        }

        $pickup_request->bookings = $bookings;
        $pickup_request->total_estimated_weight = $total_estimated_weight;
        $pickup_request->pickup_type = $pickup_type;

        $pickup_request->save();
      }
      else {
        $pickup_request = new PickupRequest();

        $pickup_request->shipper_id = $shipment->user_id;
        $pickup_request->pickup_address_id = $shipment->pickup_address_id;
        $pickup_request->bookings = 1;
        $pickup_request->total_estimated_weight = $shipment->estimated_weight;

        if ($shipment->estimated_weight < $defined_pickup_weight) {
          $pickup_request->pickup_type = 0;
        }
        else {
          $pickup_request->pickup_type = 1;
        }

        $pickup_request->pickup_date = $shipment->pickup_date;
        $pickup_request->status = 0;

        $pickup_request->save();
      }

      ShipmentsPickupJourneyController::add($shipment_id, 1, NULL, $pickup_request->id);

      $pickup_request_assigned_shipment = PickupRequestAssignedShipment::where('shipment_id', $shipment_id);

      if (!$pickup_request_assigned_shipment->exists()) {
        $pickup_request_assigned_shipment = new PickupRequestAssignedShipment();

        $pickup_request_assigned_shipment->pickup_request_id = $pickup_request->id;
        $pickup_request_assigned_shipment->shipment_id = $shipment_id;
        $pickup_request_assigned_shipment->status = 0;

        $pickup_request_assigned_shipment->save();
      }
      else {
        $pickup_request_assigned_shipment = $pickup_request_assigned_shipment->first();

        $pickup_request_assigned_shipment->pickup_request_id = $pickup_request->id;
        $pickup_request_assigned_shipment->status = 0;

        $pickup_request_assigned_shipment->save();
      }
    }

    static public function cancel($shipment_id) {
      $pickup_request_assigned_shipment = PickupRequestAssignedShipment::where('shipment_id', $shipment_id)->whereIn('status', [0, 1]);

      if ($pickup_request_assigned_shipment->exists()) {
        $pickup_request_assigned_shipment = $pickup_request_assigned_shipment->first();

        $pickup_request = $pickup_request_assigned_shipment->pickup_request;

        $bookings = $pickup_request->bookings - 1;

        $pickup_request->bookings = $bookings;

        if ($pickup_request_assigned_shipment->status == 1) {
          $pickup_request->pending_bookings = $pickup_request->pending_bookings - 1;
        }

        $pickup_request->save();

        ShipmentsPickupJourneyController::add($shipment_id, 6, NULL, $pickup_request->id);

        $pickup_request_assigned_shipment->delete();

        if ($bookings == 0) {
          $pickup_request->total_estimated_weight = 0;
          $pickup_request->pickup_type = 0;
          $pickup_request->status = 3;

          $pickup_request->save();

          if ($pickup_request->pickup_note_request) {
            $pickup_note = $pickup_request->pickup_note_request->pickup_note;

            if ($bookings == 0) {
              PickupNoteRequest::where('pickup_note_id', $pickup_note->id)->where('pickup_request_id', $pickup_request->id)->delete();
            }

            $pickup_note_requests = $pickup_note->pickup_note_requests;

            if ($pickup_note_requests) {
              if ($bookings == 0) {
                $pickup_note->pickups = $pickup_note->pickups - 1;
              }

              $pickup_note->bookings = $pickup_note->bookings - 1;

              $shipment = Shipment::find($shipment_id);

              $weight = $pickup_request->total_estimated_weight - $shipment->estimated_weight;

              $pickup_note->total_estimated_weight = $weight;

              $defined_pickup_weight = GlobalSettings::where('type', 'pickup_weight');

              if ($defined_pickup_weight->exists()) {
                $defined_pickup_weight = $defined_pickup_weight->first();

                $defined_pickup_weight = $defined_pickup_weight->setting_value;
              }
              else {
                $defined_pickup_weight = 10;
              }

              if ($weight < $defined_pickup_weight) {
                $pickup_note->pickup_type = 0;
              }
              else {
                $pickup_note->pickup_type = 1;
              }

              $pickup_note->save();
            }
            else {
              $pickup_note->pickups = 0;
              $pickup_note->bookings = 0;
              $pickup_note->total_estimated_weight = 0;
              $pickup_note->pickup_type = 0;
              $pickup_note->status_id = 5;

              $pickup_note->save();
            }
          }
        }
        else {
          $shipment = Shipment::find($shipment_id);

          $weight = $pickup_request->total_estimated_weight - $shipment->estimated_weight;

          $pickup_request->total_estimated_weight = $weight;

          $defined_pickup_weight = GlobalSettings::where('type', 'pickup_weight');

          if ($defined_pickup_weight->exists()) {
            $defined_pickup_weight = $defined_pickup_weight->first();

            $defined_pickup_weight = $defined_pickup_weight->setting_value;
          }
          else {
            $defined_pickup_weight = 10;
          }

          if ($weight < $defined_pickup_weight) {
            $pickup_request->pickup_type = 0;
          }
          else {
            $pickup_request->pickup_type = 1;
          }

          $pickup_request->save();
        }
      }
    }

    public function pending_index() {
      $riders = Rider::where('status',1)->select(['id', 'name']);

      if (session('role_id') != 1) {
        $riders = $riders->whereHas('city', function ($query) {
          $query->whereIn('hub_id', session('hubs'));
        });
      }

      $riders = $riders->get();

      return view('admin.pickups.pending.index')->with(['riders' => $riders]);
    }

    public function pending_list(Request $request) {
          $pickup_requests = PickupRequest::join('users as u', 'pickup_requests.shipper_id', '=', 'u.id')
          ->join('user_shipping_infos as usi', 'pickup_requests.pickup_address_id', '=', 'usi.id')
          ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
          ->select('pickup_requests.id', 'pickup_requests.created_at as requested_at', 'u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'pickup_requests.bookings', 'pickup_requests.bookings as bookings_link' , 'pickup_requests.pending_bookings','pickup_requests.pending_bookings as pending_bookings_link', 'pickup_requests.total_estimated_weight', 'pickup_requests.pickup_type', 'pickup_requests.pickup_date')
          ->where('pickup_requests.status', 0);

          if (session('role_id') != 1) {
            $pickup_requests = $pickup_requests->whereIn('ci.hub_id', session('hubs'));
          }
        if(session('department_id') == 7){
            if(session('role_id') != 4 ){
                $pickup_requests = $pickup_requests->whereIn('u.id', session('tagged_shippers'));
            }
        }
      $datatables = Datatables::of($pickup_requests)
      ->editColumn('total_estimated_weight', '{{ floatval($total_estimated_weight) }}')
      ->editColumn('pickup_type', function($pickup_request) {
        return ($pickup_request->pickup_type == 0) ? 'Light' : 'Heavy';
      })
      ->filterColumn('pickup_type', function($query, $keyword) {
          if($keyword == 0 || $keyword == 1){
              $query->where('pickup_requests.pickup_type','=',$keyword);
          }else{
              $query->whereRaw('false');
          }
      })
      ->editColumn('pickup_date', function($pickup_request) {
        return Carbon::parse($pickup_request->pickup_date)->format('Y-m-d');
      })
      ->editColumn('bookings_link', function($pickup_request) {
          if ($pickup_request->bookings != 0) {
              return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->bookings . '</button>';
          }
          else {
              return 0;
          }
      })
      ->editColumn('pending_bookings_link', function($pickup_request) {
      if ($pickup_request->pending_bookings != 0) {
          return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->pending_bookings . '</button>';
      }
      else {
          return 0;
      }
      })
      ->addColumn('action', function($pickup_request) {
        if (session('role_id') == 1 || in_array(18, session('permissions'))) {
          return '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                      <button type="button" class="dropdown-item cancel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Cancel</div></button>
                    </div>
                  </div>
          ';
        }
        else {
          return '';
        }
      });
//      ->filterColumn('pickup_type', function($query, $keyword) {
//        $keyword = strtolower($keyword);
//
//        if (strpos('light', $keyword) !== FALSE) {
//          $query->where('pickup_requests.pickup_type', '=', 0);
//        }
//        else if (strpos('heavy', $keyword) !== FALSE) {
//          $query->where('pickup_requests.pickup_type', '=', 1);
//        }
//        else {
//          $query->whereRaw('false');
//        }
//      });

      return $datatables->make(true);
    }

    public function pending_assign(Request $request) {
      $pickup_request_ids = $request->input('pickup_request_ids');
      $rider_id = $request->input('rider_id');

      foreach ($pickup_request_ids as $pickup_request_id) {
        $pickup_request = PickupRequest::find($pickup_request_id);

        if ($pickup_request->status != 0) {
          return ['status' => 1, 'error' => 'One of the Pickup Request(s) has already been modified'];
        }
      }

      $pickups = 0;
      $bookings = 0;
      $total_estimated_weight = 0;

      foreach ($pickup_request_ids as $pickup_request_id) {
        $pickup_request = PickupRequest::find($pickup_request_id);

        $pickup_request->status = 1;

        $pickup_request->save();

        $pickups++;
        $bookings += $pickup_request->bookings;
        $total_estimated_weight += $pickup_request->total_estimated_weight;
      }

      $defined_pickup_weight = GlobalSettings::where('type', 'pickup_weight');

      if ($defined_pickup_weight->exists()) {
        $defined_pickup_weight = $defined_pickup_weight->first();

        $defined_pickup_weight = $defined_pickup_weight->setting_value;
      }
      else {
        $defined_pickup_weight = 10;
      }

      $existing_pickup_note = FALSE;

      $pickup_note = PickupNote::where('rider_id', $rider_id)->where('status_id', '=', 1);

      if ($pickup_note->exists()) {
        $pickup_note = $pickup_note->first();

        $pickup_note->pickups += $pickups;
        $pickup_note->bookings += $bookings;

        $pickup_note->total_estimated_weight += $total_estimated_weight;

        if ($total_estimated_weight < $defined_pickup_weight) {
          $pickup_note->pickup_type = 0;
        }
        else {
          $pickup_note->pickup_type = 1;
        }

        $pickup_note->updated_by = Auth::id();

        $pickup_note->save();

        $pickup_note_id = $pickup_note->id;
      }
      else {
        $pickup_note = new PickupNote();

        $pickup_note->rider_id = $rider_id;
        $pickup_note->pickups = $pickups;
        $pickup_note->bookings = $bookings;
        $pickup_note->total_estimated_weight = $total_estimated_weight;

        if ($total_estimated_weight < $defined_pickup_weight) {
          $pickup_note->pickup_type = 0;
        }
        else {
          $pickup_note->pickup_type = 1;
        }

        $pickup_note->assigned_by_user_id = Auth::id();
        $pickup_note->status_id = 1;

        $pickup_note->city_id = Rider::find($rider_id)->city_id;

        $pickup_note->save();

        $pickup_note_id = $pickup_note->id;
      }

      foreach ($pickup_request_ids as $pickup_request_id) {
        $pickup_note_request = new PickupNoteRequest();

        $pickup_note_request->pickup_note_id = $pickup_note_id;
        $pickup_note_request->pickup_request_id = $pickup_request_id;

        $pickup_note_request->save();

        $pickup_request = PickupRequest::find($pickup_request_id);

        $assigned_shipments = $pickup_request->pickup_request_assigned_shipments;

        if ($assigned_shipments) {
          foreach ($assigned_shipments as $assigned_shipment) {
            $shipment = $assigned_shipment->shipment;

            if ($shipment->shipper_status_id == 1) {
              ShipmentsPickupJourneyController::add($shipment->id, 2, Auth::id(), $pickup_note_id, $rider_id);
            }
          }
        }
      }

      return ['status' => 0, 'success' => 'Pickup Request(s) has been Assigned to the Rider'];
    }

    public function pending_multiple_cancel(Request $request) {
      $pickup_request_ids = $request->input('pickup_request_ids');

      foreach ($pickup_request_ids as $pickup_request_id) {
        $pickup_request = PickupRequest::find($pickup_request_id);

        if ($pickup_request->status != 0) {
          return ['status' => 1, 'error' => 'One of the Pickup Request(s) has already been modified'];
        }
      }

      foreach ($pickup_request_ids as $pickup_request_id) {
        $pickup_request = PickupRequest::find($pickup_request_id);

        $pickup_request->status = 3;

        $pickup_request->save();

        $assigned_shipments = $pickup_request->pickup_request_assigned_shipments;

        if ($assigned_shipments) {
          foreach ($assigned_shipments as $assigned_shipment) {
            $shipment = $assigned_shipment->shipment;

            if ($shipment->shipper_status_id == 1) {
              ShipmentsPickupJourneyController::add($shipment->id, 6, Auth::id(), $pickup_request->id);
            }
          }
        }
      }

      return ['status' => 0, 'success' => 'Pickup Request(s) has been Cancelled'];
    }

    public function pending_cancel(Request $request) {
      $pickup_request_id = $request->input('pickup_request_id');

      $pickup_request = PickupRequest::find($pickup_request_id);

      if ($pickup_request->status == 0) {
        $pickup_request->status = 3;

        $pickup_request->save();

        $assigned_shipments = $pickup_request->pickup_request_assigned_shipments;

        if ($assigned_shipments) {
          foreach ($assigned_shipments as $assigned_shipment) {
            $shipment = $assigned_shipment->shipment;

            if ($shipment->shipper_status_id == 1) {
              ShipmentsPickupJourneyController::add($shipment->id, 6, Auth::id(), $pickup_request->id);
            }
          }
        }

        return ['status' => 0, 'success' => 'Pickup Request has been Cancelled'];
      }
      else {
        return ['status' => 1, 'error' => 'Selected Pickup Request has already been modified'];
      }
    }

    public function assigned_index() {
        $rider_category = RiderCategory::all();
      return view('admin.pickups.assigned.index')->with(['rider_category'=>$rider_category]);
    }

    public function assigned_list(Request $request) {
      $pickup_notes = PickupNote::join('riders as r', 'pickup_notes.rider_id', '=', 'r.id')
      ->join('rider_categories as rc', 'r.rider_category_id', '=', 'rc.id')
      ->join('routes as ro', 'r.route_id', '=', 'ro.id')
      ->join('cities as c', 'r.city_id', '=', 'c.id')
      ->join('admins as a', 'pickup_notes.assigned_by_user_id', '=', 'a.id')
      ->join('pickup_note_statuses as pns', 'pickup_notes.status_id', '=', 'pns.id')
      ->select('pickup_notes.id', 'r.name as rider_name', 'r.phone as rider_phone', 'rc.name as rider_type', 'ro.code as route_code', 'ro.start as route_start', 'ro.end as route_end', 'c.name as city', 'pickup_notes.rider_id', 'pickup_notes.pickups', 'pickup_notes.pickups as pickups_link', 'pickup_notes.bookings', 'pickup_notes.bookings as bookings_link', 'pickup_notes.total_estimated_weight', 'pickup_notes.pickup_type', 'pickup_notes.created_at as assigned_date', 'a.name as assigned_by', 'pickup_notes.id as pickup_note_no')
      ->where('pickup_notes.status_id', '=', 1);

      if (session('role_id') != 1) {
        $pickup_notes = $pickup_notes->whereIn('c.hub_id', session('hubs'));
      }

      $datatables = Datatables::of($pickup_notes)
      ->editColumn('pickup_note_no', function ($pickup_note) {
            return str_pad($pickup_note->pickup_note_no, 6, '0', STR_PAD_LEFT);
        })
        ->filterColumn('pickup_notes.id', function ($query, $keyword) {
            return $query->where('pickup_notes.id', '=', $keyword);
        })
      ->editColumn('bookings_link', function($pickup_notes) {
          if ($pickup_notes->bookings != 0) {
              return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_notes->bookings . '</button>';
          }
          else {
              return 0;
          }
      })
      ->editColumn('pickups_link', function($pickup_notes) {
          if ($pickup_notes->pickups != 0) {
              return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_notes->pickups . '</button>';
          }
          else {
              return 0;
          }
      })
      ->addColumn('rider', function($pickup_note) {
        return $pickup_note->rider_name . '<br/>' . $pickup_note->rider_phone;
      })
      ->addColumn('route', function($pickup_note) {
        return $pickup_note->route_code . ' (' . $pickup_note->route_start . ' to ' . $pickup_note->route_end . ')';
      })
      ->editColumn('total_estimated_weight', '{{ floatval($total_estimated_weight) }}')
      ->editColumn('pickup_type', function($pickup_note) {
        return ($pickup_note->pickup_type == 0) ? 'Light' : 'Heavy';
      })
      ->filterColumn('pickup_type', function($query, $keyword) {
          if($keyword == 0 || $keyword == 1){
              $query->where('pickup_requests.pickup_type','=',$keyword);
          }else{
              $query->whereRaw('false');
          }
      })
          ->filterColumn('rider_type',function ($query,$keyword){

              if ($keyword != '') {
                  $query->where('rc.id',$keyword);
              }
              else {
                  $query->whereRaw('false');
              }
          })
      ->addColumn('action', function($pickup_note) {
        $cancel_button = '<button type="button" class="dropdown-item cancel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Cancel</div></button>';
        $view_details_button = '<button type="button" class="dropdown-item view_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Details</div></button>';
        $print_pickup_note_button = '<button type="button" class="dropdown-item print_pickup_note"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-printer"></i></div><div class="col-9 offset-1">Print Pickup Note</div></button>';
        $sms_rider_button = '<button type="button" class="dropdown-item sms_rider"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-message-circle"></i></div><div class="col-9 offset-1">SMS Rider</div></button>';

        $dropdown = '
          <div class="btn-group">
            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
            <div class="dropdown-menu dropdown-menu-sm">
        ';

        if (session('role_id') == 1 || in_array(21, session('permissions'))) {
          $dropdown .= $cancel_button;
        }

        $dropdown .= $view_details_button;

        if (session('role_id') == 1 || in_array(22, session('permissions'))) {
          $dropdown .= $print_pickup_note_button;

          if (Notification::find(22)->status) {
            $dropdown .= $sms_rider_button;
          }
        }

        $dropdown .= '
            </div>
          </div>
        ';

        return $dropdown;
      })
      ->filterColumn('rider', function($query, $keyword) {
        $query->where('r.name', 'like', '%' . $keyword . '%')->orWhere('r.phone', 'like', '%' . $keyword . '%');
      })
      ->filterColumn('route', function($query, $keyword) {
        $query->where('ro.code', 'like', '%' . $keyword . '%')->orWhere('ro.start', 'like', '%' . $keyword . '%')->orWhere('ro.end', 'like', '%' . $keyword . '%');
      })

//      ->filterColumn('pickup_type', function($query, $keyword) {
//        $keyword = strtolower($keyword);
//
//        if (strpos('light', $keyword) !== FALSE) {
//          $query->where('pickup_notes.pickup_type', '=', 0);
//        }
//        else if (strpos('heavy', $keyword) !== FALSE) {
//          $query->where('pickup_notes.pickup_type', '=', 1);
//        }
//        else {
//          $query->whereRaw('false');
//        }
//      })
      ->filterColumn('pickup_note_no', function($query, $keyword) {
        $query->where('pickup_notes.id', '=', $keyword);
      })
      ->orderColumn('rider', 'r.name $1, r.phone $1')
      ->orderColumn('route', 'ro.code $1, ro.start $1, ro.end $1');

      return $datatables->make(true);
    }

    public function assigned_cancel(Request $request) {
      $pickup_note_id = $request->input('pickup_note_id');

      $pickup_note = PickupNote::find($pickup_note_id);

      if ($pickup_note->status_id == 1) {
        $pickup_note->status_id = 5;
        $pickup_note->updated_by = Auth::id();

        $pickup_note->save();

        foreach (PickupNote::find($pickup_note_id)->pickup_note_requests as $pickup_note_request) {
          $pickup_request = $pickup_note_request->pickup_request;

          $pickup_request->status = 0;

          $pickup_request->pending_bookings = $pickup_request->bookings;

          $pickup_request->save();

          $assigned_shipments = $pickup_request->pickup_request_assigned_shipments;

          if ($assigned_shipments) {
            foreach ($assigned_shipments as $assigned_shipment) {
              $assigned_shipment->status = 1;

              $assigned_shipment->save();

              $shipment = $assigned_shipment->shipment;

              if ($shipment->shipper_status_id == 1) {
                ShipmentsPickupJourneyController::add($shipment->id, 7, Auth::id(), $pickup_note_id);
              }
            }
          }
        }

        return ['status' => 0, 'success' => 'Pickup has been Cancelled'];
      }
      else {
        return ['status' => 1, 'error' => 'Selected Pickup has already been modified'];
      }
    }

    public function assigned_view_details(Request $request) {
      $pickup_note_id = $request->input('pickup_note_id');

      $pickup_note = PickupNote::find($pickup_note_id);

      $pickup_note_requests = $pickup_note->pickup_note_requests;

      $details = array();

      foreach ($pickup_note_requests as $pickup_note_request) {
        $pickup_request = $pickup_note_request->pickup_request;

        $shipper = $pickup_request->shipper;
        $pickup_address = $pickup_request->pickup_address;

        $detail = array();

        $detail['shipper'] = $shipper->name;
        $detail['contact_person'] = $pickup_address['poc'];
        $detail['contact_number'] = $pickup_address['phone'];
        $detail['address'] = $pickup_address['pickup_address'];
        $detail['bookings'] = $pickup_request['bookings'];
        $detail['total_estimated_weight'] = floatval($pickup_request['total_estimated_weight']);
        $detail['pickup_type'] = ($pickup_request['pickup_type'] == 0) ? 'Light' : 'Heavy';

        $details[] = $detail;
      }

      return $details;
    }

    public function assigned_print(Request $request) {
      $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

      $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Pickup Note</title>

                    <style>
                      @page {
                        size: A4 portrait;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        color: #09262e !important;
                        font-size: 0.9rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }

                      table.table-bordered {
                        page-break-inside: avoid;
                      }

                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
      ';

      foreach($request->ids as $id) {
        $pickup_note = PickupNote::find($id);

        if ($request->dispatch) {
          $pickup_note->status_id = 2;
          $pickup_note->updated_by = Auth::id();

          $pickup_note->save();

          foreach ($pickup_note->pickup_note_requests as $pickup_note_request) {
            $pickup_request = $pickup_note_request->pickup_request;

            $assigned_shipments = $pickup_request->pickup_request_assigned_shipments;

            if ($assigned_shipments) {
              foreach ($assigned_shipments as $assigned_shipment) {
                $shipment = $assigned_shipment->shipment;

                if ($shipment->shipper_status_id == 1) {
                  ShipmentsPickupJourneyController::add($shipment->id, 3, Auth::id(), $pickup_note->id);
                }
              }
            }
          }
        }

        $rider = Rider::find($pickup_note->rider_id);
        $route = $rider->route;

        $html .= '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Pickup Note</strong></td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider->name . '</td>
                            <td rowspan="7" class="text-center align-middle pl-1 pr-1">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $rider->rider_category->name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td> ' . $route->code . ' (' . $route->start . ' to ' . $route->end . ')</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td> ' . $pickup_note->rider->city->name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Pickups</strong></td>
                            <td>' . $pickup_note->pickups . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';

        $html .= '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Company Name</strong></td>
                            <td class="color primary"><strong>Contact Person</strong></td>
                            <td class="color primary"><strong>Contact Number</strong></td>
                            <td class="color primary"><strong>Pickup Address</strong></td>
                            <td class="color primary"><strong>Bookings</strong></td>
                            <td class="color primary"><strong>Pickup Date</strong></td>
                          </tr>
        ';

        $serial_number = 1;

        $pickup_note_requests = $pickup_note->pickup_note_requests;

        foreach ($pickup_note_requests as $pickup_note_request) {
          $pickup_request = $pickup_note_request->pickup_request;

          $shipper = $pickup_request->shipper;
          $pickup_address = $pickup_request->pickup_address;

          $html .= '
                          <tr>
                            <td>' . $serial_number . '</td>
                            <td>' . $shipper->name . '</td>
                            <td>' . $pickup_address['poc'] . '</td>
                            <td>' . $pickup_address['phone'] . '</td>
                            <td>' . $pickup_address['pickup_address'] . '</td>
                            <td>' . $pickup_request['bookings'] . '</td>
                            <td>' . Carbon::parse($pickup_request['pickup_date'])->format('Y-m-d') . '</td>
                          </tr>
          ';

          $serial_number++;
        }

        $html .= '
                        </tbody>
                      </table>

                      <hr>
        ';
      }

      $html .= '
                    </div>

                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                  </body>
                </html>
      ';

      return $html;
    }

    public function assigned_sms(Request $request) {
      $pickup_note = PickupNote::find($request->pickup_note_id);

      $pickup_note->status_id = 2;
      $pickup_note->updated_by = Auth::id();

      $pickup_note->save();

      foreach ($pickup_note->pickup_note_requests as $pickup_note_request) {
        $pickup_request = $pickup_note_request->pickup_request;

        $assigned_shipments = $pickup_request->pickup_request_assigned_shipments;

        if ($assigned_shipments) {
          foreach ($assigned_shipments as $assigned_shipment) {
            $shipment = $assigned_shipment->shipment;

            if ($shipment->shipper_status_id == 1) {
              ShipmentsPickupJourneyController::add($shipment->id, 3, Auth::id(), $pickup_note->id);
            }
          }
        }
      }

      NotificationsController::send(22, $pickup_note->id);

      return ['status' => 0, 'success' => 'Pickup has been Dispatched'];
    }

    public function receive_index() {
        $rider_category = RiderCategory::all();
        $pickup_status = PickupNoteStatus::all();
      return view('admin.pickups.receive.index')->with(['rider_category'=>$rider_category,'pickup_status'=>$pickup_status]);
    }

    public function receive_list(Request $request) {
      $pickup_notes = PickupNote::join('riders as r', 'pickup_notes.rider_id', '=', 'r.id')
      ->join('rider_categories as rc', 'r.rider_category_id', '=', 'rc.id')
      ->join('routes as ro', 'r.route_id', '=', 'ro.id')
      ->join('cities as c', 'r.city_id', '=', 'c.id')
      ->join('admins as a', 'pickup_notes.assigned_by_user_id', '=', 'a.id')
      ->join('pickup_note_statuses as pns', 'pickup_notes.status_id', '=', 'pns.id')
      ->select('pickup_notes.id', 'r.name as rider_name', 'r.phone as rider_phone', 'rc.name as rider_type', 'ro.code as route_code', 'ro.start as route_start', 'ro.end as route_end', 'c.name as city', 'pickup_notes.pickups', 'pickup_notes.bookings', 'pickup_notes.bookings as bookings_link', 'pickup_notes.pickup_type', 'pickup_notes.created_at as assigned_date', 'a.name as assigned_by', 'pickup_notes.id as pickup_note_no','pickup_notes.id as pickup_note_id', 'pickup_notes.status_id', 'pns.name as status')
      ->whereIn('pickup_notes.status_id', [2, 3]);

      if (session('role_id') != 1) {
        $pickup_notes = $pickup_notes->whereIn('c.hub_id', session('hubs'));
      }

      $datatables = Datatables::of($pickup_notes)
      ->addColumn('rider', function($pickup_note) {
        return $pickup_note->rider_name . '<br/>' . $pickup_note->rider_phone;
      })
      ->addColumn('route', function($pickup_note) {
        return $pickup_note->route_code . ' (' . $pickup_note->route_start . ' to ' . $pickup_note->route_end . ')';
      })
      ->editColumn('pickup_type', function($pickup_note) {
        return ($pickup_note->pickup_type == 0) ? 'Light' : 'Heavy';
      })
      ->editColumn('bookings_link', function($pickup_notes) {
          if ($pickup_notes->bookings != 0) {
              return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_notes->bookings . '</button>';
          }
          else {
              return 0;
          }
      })
      ->filterColumn('pickup_type', function($query, $keyword) {
          if($keyword == 0 || $keyword == 1){
              $query->where('pickup_requests.pickup_type','=',$keyword);
          }else{
              $query->whereRaw('false');
          }
      })
          ->filterColumn('rider_type',function ($query,$keyword){

              if ($keyword != '') {
                  $query->where('rc.id',$keyword);
              }
              else {
                  $query->whereRaw('false');
              }
          })
          ->filterColumn('status',function ($query,$keyword){

              if ($keyword != '') {
                  $query->where('pns.id',$keyword);
              }
              else {
                  $query->whereRaw('false');
              }
          })
      ->editColumn('pickup_note_no', function($pickup_note) {
        return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($pickup_note->pickup_note_no, 6, '0', STR_PAD_LEFT) . '</span></button>';
      })
      ->editColumn('pickup_note_id', function($pickup_note) {
        return str_pad($pickup_note->pickup_note_id, 6, '0', STR_PAD_LEFT);
      })
      ->filterColumn('pickup_notes.id', function ($query, $keyword) {
          return $query->where('pickup_notes.id', '=', $keyword);
      })
      ->addColumn('action', function($pickup_note) {
        $receive_button = '<button type="button" class="dropdown-item receive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Receive</div></button>';
        $view_button = '<button type="button" class="dropdown-item summary"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-target"></i></div><div class="col-9 offset-1">Summary</div></button>';

        if (session('role_id') == 1 || in_array(24, session('permissions'))) {
          return '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                      ' . $receive_button . $view_button . '
                    </div>
                  </div>
          ';
        }
        else {
          return '';
        }
      })
      ->filterColumn('rider', function($query, $keyword) {
        $query->where('r.name', 'like', '%' . $keyword . '%')->orWhere('r.phone', 'like', '%' . $keyword . '%');
      })
      ->filterColumn('route', function($query, $keyword) {
        $query->where('ro.code', 'like', '%' . $keyword . '%')->orWhere('ro.start', 'like', '%' . $keyword . '%')->orWhere('ro.end', 'like', '%' . $keyword . '%');
      })
      ->filterColumn('pickup_type', function($query, $keyword) {
        $keyword = strtolower($keyword);

        if (strpos('light', $keyword) !== FALSE) {
          $query->where('pickup_notes.pickup_type', '=', 0);
        }
        else if (strpos('heavy', $keyword) !== FALSE) {
          $query->where('pickup_notes.pickup_type', '=', 1);
        }
        else {
          $query->whereRaw('false');
        }
      })
      ->filterColumn('pickup_note_no', function($query, $keyword) {
        $query->where('pickup_notes.id', '=', $keyword);
      })
      ->orderColumn('rider', 'r.name $1, r.phone $1')
      ->orderColumn('route', 'ro.code $1, ro.start $1, ro.end $1');

      if ($tracking_number = $request->get('tracking_number')) {
        $datatables->join('pickup_note_requests as pnr', 'pickup_notes.id', '=', 'pnr.pickup_note_id')
        ->join('pickup_requests as pr', 'pnr.pickup_request_id', '=', 'pr.id')
        ->join('shipments as s', 'pr.pickup_address_id', '=', 's.pickup_address_id')
        ->where('s.tracking_number', '=', $tracking_number);
      }

      return $datatables->make(true);
    }

    public function receive_pickup_note(Request $request) {
      if ($request->has('pickup_note_no')) {
        $pickup_note = PickupNote::find($request->get('pickup_note_no'));

        if ($pickup_note) {
          if (in_array($pickup_note->status_id, [2, 3])) {
            if ($request->get('type') == 0) {
              return redirect()->route('admin.pickups.receive.arrival_of_shipments.index')->with('pickup_receive_pickup_note_id', $request->get('pickup_note_no'));
            }
            else {
               return redirect()->route('admin.pickups.receive.summary.index')->with('pickup_receive_pickup_note_id', $request->get('pickup_note_no'));
            }
          }
          else {
            return back()->withErrors('Given Pickup Note has already been modified!');
          }
        }
        else {
          return back()->withErrors('Invalid Pickup Note!');
        }
      }
      else {
        return back()->withErrors('Missing Pickup Note ID!');
      }
    }

    public function receive_arrival_of_shipments_index() {
      if (session('pickup_receive_pickup_note_id')) {
        return view('admin.pickups.receive.arrival_of_shipments');
      }
      else {
        return redirect()->route('admin.pickups.receive.index')->withErrors('Kindly reselect a Pickup Note!');
      }
    }

    public function receive_shipment_details(Request $request) {
      $shipment = Shipment::where('tracking_number', $request->tracking_number);

      if ($shipment->exists()) {
        $shipment = $shipment->first();

        if ($shipment->shipper_status_id == 1) {
          $exists = FALSE;

          $pickup_note = PickupNote::find($request->pickup_receive_pickup_note_id);

          foreach ($pickup_note->pickup_note_requests as $pickup_note_request) {
            $pickup_request = $pickup_note_request->pickup_request;

            if ($pickup_request->shipper_id == $shipment->user_id && $pickup_request->pickup_address_id == $shipment->pickup_address_id) {
              $exists = TRUE;

              break;
            }
          }

          if ($exists) {
            if (empty($request->weight)) {
              $shipment->actual_weight = (($request->length * $request->breadth * $request->height) / 5000);
              $shipment->length = $request->length;
              $shipment->breadth = $request->breadth;
              $shipment->height = $request->height;
            }
            else {
              $shipment->actual_weight = $request->weight;
            }

            $shipment->save();

            $details = array();

            $details['id'] = $shipment->id;
            $details['tracking_number'] = $shipment->tracking_number;
            $details['receiving_sheet_no'] = ($shipment->receiving_sheet_shipment) ? str_pad($shipment->receiving_sheet_shipment->receiving_sheet_id, 6, '0', STR_PAD_LEFT) : '';
            $details['order_id'] = $shipment->order_id;
            $details['destination'] = $shipment->consignee_city->name;
            $details['cod_amount'] = number_format($shipment->amount);
            $details['estimated_weight'] = floatval($shipment->estimated_weight);
            $details['actual_weight'] = floatval($shipment->actual_weight);

            return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
          }
          else {
            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment does not belong to the Selected Pickup Note'];
          }
        }
        else {
          return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
        }
      }
      else {
        return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
      }
    }

    public function receive_shipment_remove(Request $request) {
      $shipment = Shipment::find($request->id);

      if ($shipment) {
        if ($shipment->shipper_status_id == 1) {
          $shipment->actual_weight = NULL;
          $shipment->length = NULL;
          $shipment->breadth = NULL;
          $shipment->height = NULL;

          $shipment->save();

          return ['status' => 0, 'success' => 'Shipment has been removed'];
        }
        else {
          return ['status' => 1, 'error' => 'Given Shipment ID has already been modified'];
        }
      }
      else {
        return ['status' => 1, 'error' => 'No Shipment with given ID is present'];
      }
    }

    public function receive_arrival_of_shipments_store(Request $request) {
      $pickup_note = PickupNote::find($request->pickup_receive_pickup_note_id);
      $shipment_ids = explode(',', $request->shipment_ids);

      $reference_1_id = $pickup_note;

      $pickup_request_ids = array();
      $pickup_request_shipper_wise_ids = array();

      foreach ($pickup_note->pickup_note_requests as $pickup_note_request) {
        $pickup_request = $pickup_note_request->pickup_request;

        $pickup_request_ids[] = $pickup_request->id;

        $pickup_request_shipper_wise_ids[$pickup_request->shipper_id] = $pickup_request->id;
      }

      $pickup_note->status_id = 3;
      $pickup_note->updated_by = Auth::id();

      $pickup_note->save();

      $receiving_sheet_ids = array();

      foreach ($shipment_ids as $shipment_id) {
        $shipment = Shipment::find($shipment_id);

        if ($receiving_sheet_shipment = $shipment->receiving_sheet_shipment) {
          $receiving_sheet_shipment->status = 1;
          $receiving_sheet_shipment->save();

          $receiving_sheet_id = $receiving_sheet_shipment->receiving_sheet_id;

          $receiving_sheet = $receiving_sheet_shipment->receiving_sheet;

          $receiving_sheet->received = $receiving_sheet->received + 1;

          if (!in_array($receiving_sheet_id, $receiving_sheet_ids)) {
            $receiving_sheet->status = 1;

            $receiving_sheet_ids[] = $receiving_sheet_id;
          }

          $receiving_sheet->save();

          if (!ReceivingSheetReceived::where('shipment_id', $shipment_id)->exists()) {
            $receiving_sheet_received = new ReceivingSheetReceived();

            $receiving_sheet_received->receiving_sheet_id = $receiving_sheet_id;
            $receiving_sheet_received->user_id = $shipment->user_id;
            $receiving_sheet_received->pickup_address_id = $shipment->pickup_address_id;
            $receiving_sheet_received->shipment_id = $shipment_id;

            $receiving_sheet_received->save();
          }

          $reference_2_id = $receiving_sheet_id;
        }
        else {
          if (!ReceivingSheetReceived::where('shipment_id', $shipment_id)->exists()) {
            $receiving_sheet_received = new ReceivingSheetReceived();

            $receiving_sheet_received->user_id = $shipment->user_id;
            $receiving_sheet_received->pickup_address_id = $shipment->pickup_address_id;
            $receiving_sheet_received->shipment_id = $shipment_id;

            $receiving_sheet_received->save();
          }

          $reference_2_id = NULL;
        }

        $shipment->shipper_status_id = 2;
        $shipment->consignee_status_id = 2;

        $shipment->save();

        ShipmentsJourneyController::add($shipment_id, 2, 2, NULL, NULL, NULL, Auth::id(), $reference_1_id, $reference_2_id);

        NotificationsController::send(3, $shipment_id);

        ShipmentChargesController::weight($shipment_id);
        ShipmentChargesController::cash_handling($shipment_id);
        ShipmentChargesController::insurance($shipment_id);
        ShipmentChargesController::fuel_surcharge($shipment_id);
      }

      $done_receiving_sheet_ids = array();

      foreach ($shipment_ids as $shipment_id) {
        $shipment = Shipment::find($shipment_id);

        $pickup_request_assigned_shipment = PickupRequestAssignedShipment::where('shipment_id', $shipment_id)->whereIn('pickup_request_id', $pickup_request_ids);

        if ($pickup_request_assigned_shipment->exists()) {
          $pickup_request_assigned_shipment = $pickup_request_assigned_shipment->first();

          $pickup_request = $pickup_request_assigned_shipment->pickup_request;

          $pickup_request->received = $pickup_request->received + 1;

          $short_received_shipment = PickupRequestShortReceivedShipment::where('pickup_request_id', $pickup_request->id)->where('shipment_id', $shipment_id);

          if ($short_received_shipment->exists()) {
            $short_received_shipment->delete();

            $short_received = $pickup_request->short_received - 1;

            if ($short_received == 0) {
              $short_received = NULL;
            }

            $pickup_request->short_received = $short_received;

            $pickup_request->save();
          }

          if (!empty($receiving_sheet_ids)) {
            if ($shipment->receiving_sheet_shipment) {
              $receiving_sheet_id = $shipment->receiving_sheet_shipment->receiving_sheet_id;

              if (!in_array($receiving_sheet_id, $done_receiving_sheet_ids)) {
                $short_received_shipments = ReceivingSheetShipment::where('receiving_sheet_id', $receiving_sheet_id)->where('status', 0);

                $pickup_request_short_received_shipments = PickupRequestShortReceivedShipment::where('pickup_request_id', $pickup_request->id);

                if ($pickup_request_short_received_shipments->exists()) {
                  $pickup_request_short_received_shipments = $pickup_request_short_received_shipments->pluck('shipment_id')->toArray();

                  $short_received_shipments = $short_received_shipments->whereNotIn('shipment_id', $pickup_request_short_received_shipments);
                }

                $pickup_request->short_received = $pickup_request->short_received + $short_received_shipments->count();

                foreach ($short_received_shipments->get() as $short_received_shipment) {
                  $pickup_request_short_received_shipment = new PickupRequestShortReceivedShipment();

                  $pickup_request_short_received_shipment->pickup_request_id = $pickup_request->id;
                  $pickup_request_short_received_shipment->shipment_id = $short_received_shipment->shipment_id;

                  $pickup_request_short_received_shipment->save();
                }

                $done_receiving_sheet_ids[] = $receiving_sheet_id;
              }
            }
          }

          if (!$shipment->receiving_sheet_shipment) {
            $pickup_request->over_received = $pickup_request->over_received + 1;
          }

          $pickup_request->save();

          $pickup_request_assigned_shipment->status = 2;

          $pickup_request_assigned_shipment->save();

          $pickup_request_received_shipment = new PickupRequestReceivedShipment();

          $pickup_request_received_shipment->pickup_request_id = $pickup_request->id;
          $pickup_request_received_shipment->shipment_id = $shipment_id;

          if ($shipment->receiving_sheet_shipment) {
            $pickup_request_received_shipment->over_received = 0;
          }
          else {
            $pickup_request_received_shipment->over_received = 1;
          }

          $pickup_request_received_shipment->save();
        }
        else {
          $pickup_request_assigned_shipment = PickupRequestAssignedShipment::where('shipment_id', $shipment_id)->whereIn('status', [0, 1])->first();

          $pickup_request = PickupRequest::find($pickup_request_shipper_wise_ids[$pickup_request_assigned_shipment->pickup_request->shipper_id]);

          $pickup_request->received = $pickup_request->received + 1;

          $short_received_shipment = PickupRequestShortReceivedShipment::where('pickup_request_id', $pickup_request->id)->where('shipment_id', $shipment_id);

          if ($short_received_shipment->exists()) {
            $short_received_shipment->delete();

            $short_received = $pickup_request->short_received - 1;

            if ($short_received == 0) {
              $short_received = NULL;
            }

            $pickup_request->short_received = $short_received;

            $pickup_request->save();
          }

          if (!empty($receiving_sheet_ids)) {
            if ($shipment->receiving_sheet_shipment) {
              $receiving_sheet_id = $shipment->receiving_sheet_shipment->receiving_sheet_id;

              if (!in_array($receiving_sheet_id, $done_receiving_sheet_ids)) {
                $short_received_shipments = ReceivingSheetShipment::where('receiving_sheet_id', $receiving_sheet_id)->where('status', 0);

                $pickup_request_short_received_shipments = PickupRequestShortReceivedShipment::where('pickup_request_id', $pickup_request->id);

                if ($pickup_request_short_received_shipments->exists()) {
                  $pickup_request_short_received_shipments = $pickup_request_short_received_shipments->pluck('shipment_id')->toArray();

                  $short_received_shipments = $short_received_shipments->whereNotIn('shipment_id', $pickup_request_short_received_shipments);
                }

                $pickup_request->short_received = $pickup_request->short_received + $short_received_shipments->count();

                foreach ($short_received_shipments->get() as $short_received_shipment) {
                  $pickup_request_short_received_shipment = new PickupRequestShortReceivedShipment();

                  $pickup_request_short_received_shipment->pickup_request_id = $pickup_request->id;
                  $pickup_request_short_received_shipment->shipment_id = $short_received_shipment->shipment_id;

                  $pickup_request_short_received_shipment->save();
                }

                $done_receiving_sheet_ids[] = $receiving_sheet_id;
              }
            }
          }

          if (!$shipment->receiving_sheet_shipment) {
            $pickup_request->over_received = $pickup_request->over_received + 1;
          }

          $pickup_request->save();

          $pickup_request_received_shipment = new PickupRequestReceivedShipment();

          $pickup_request_received_shipment->pickup_request_id = $pickup_request->id;
          $pickup_request_received_shipment->shipment_id = $shipment_id;

          if ($shipment->receiving_sheet_shipment) {
            $pickup_request_received_shipment->over_received = 0;
          }
          else {
            $pickup_request_received_shipment->over_received = 1;
          }

          $pickup_request_received_shipment->save();

          $pickup_request = $pickup_request_assigned_shipment->pickup_request;

          $bookings = $pickup_request->bookings - 1;

          $pickup_request->bookings = $bookings;

          if ($pickup_request_assigned_shipment->status == 1) {
            $pickup_request->pending_bookings = $pickup_request->pending_bookings - 1;
          }

          $pickup_request_assigned_shipment->status = 3;

          $pickup_request_assigned_shipment->save();

          $weight = $pickup_request->total_estimated_weight - $shipment->estimated_weight;

          $pickup_request->total_estimated_weight = $weight;

          $defined_pickup_weight = GlobalSettings::where('type', 'pickup_weight');

          if ($defined_pickup_weight->exists()) {
            $defined_pickup_weight = $defined_pickup_weight->first();

            $defined_pickup_weight = $defined_pickup_weight->setting_value;
          }
          else {
            $defined_pickup_weight = 10;
          }

          if ($weight < $defined_pickup_weight) {
            $pickup_request->pickup_type = 0;
          }
          else {
            $pickup_request->pickup_type = 1;
          }

          if ($bookings == 0) {
            $pickup_request->total_estimated_weight = 0;
            $pickup_request->pickup_type = 0;
            $pickup_request->status = 3;
          }

          $pickup_request->save();

          if ($pickup_request->pickup_note_request) {
            $pickup_note = $pickup_request->pickup_note_request->pickup_note;

            if ($bookings == 0) {
              PickupNoteRequest::where('pickup_note_id', $pickup_note->id)->where('pickup_request_id', $pickup_request->id)->delete();
            }

            $pickup_note_requests = $pickup_note->pickup_note_requests;

            if ($pickup_note_requests) {
              if ($bookings == 0) {
                $pickup_note->pickups = $pickup_note->pickups - 1;
              }

              $pickup_note->bookings = $pickup_note->bookings - 1;

              $weight = $pickup_note->total_estimated_weight - $weight;

              $pickup_note->total_estimated_weight = $weight;

              if ($weight < $defined_pickup_weight) {
                $pickup_note->pickup_type = 0;
              }
              else {
                $pickup_note->pickup_type = 1;
              }

              $pickup_note->updated_by = Auth::id();

              $pickup_note->save();
            }
            else {
              $pickup_note->pickups = 0;
              $pickup_note->bookings = 0;
              $pickup_note->total_estimated_weight = 0;
              $pickup_note->pickup_type = 0;
              $pickup_note->status_id = 5;
              $pickup_note->updated_by = Auth::id();

              $pickup_note->save();
            }
          }
        }
      }

      foreach ($shipment_ids as $shipment_id) {
        ShipmentsPickupJourneyController::add($shipment_id, 4, Auth::id(), $pickup_note);
      }

      NotificationsController::send(4, $request->pickup_receive_pickup_note_id, $shipment_ids);

      return redirect()->route('admin.pickups.receive.summary.index')->with('pickup_receive_pickup_note_id', $request->pickup_receive_pickup_note_id);
    }

    public function receive_summary_index() {
      if (session('pickup_receive_pickup_note_id')) {
        return view('admin.pickups.receive.summary');
      }
      else {
        return redirect()->route('admin.pickups.receive.index')->withErrors('Kindly reselect a Pickup Note!');
      }
    }

    public function receive_summary_list(Request $request) {
      $pickup_requests = PickupRequest::join('users as u', 'pickup_requests.shipper_id', '=', 'u.id')
      ->join('user_shipping_infos as usi', 'pickup_requests.pickup_address_id', '=', 'usi.id')
      ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
      ->join('pickup_note_requests as pnr', 'pickup_requests.id', '=', 'pnr.pickup_request_id')
      ->join('pickup_notes as pn', 'pnr.pickup_note_id', '=', 'pn.id')
      ->join('admins as a', 'pn.assigned_by_user_id', '=', 'a.id')
      ->select('pickup_requests.id', 'u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'pickup_requests.bookings', 'pickup_requests.bookings as bookings_link', 'pickup_requests.received', 'pickup_requests.received as received_link', 'pickup_requests.short_received', 'pickup_requests.over_received', 'pickup_requests.pickup_type', 'pickup_requests.created_at as booking_date', 'pn.created_at as assigned_date', 'a.name as assigned_by', 'pn.id as pickup_note_no', 'pickup_requests.status')
      ->where('pn.id', $request->pickup_receive_pickup_note_id);

      $datatables = Datatables::of($pickup_requests)
      ->editColumn('pickup_note_no', function ($pickup_request) {
          return str_pad($pickup_request->pickup_note_no, 6, '0', STR_PAD_LEFT);
      })
      ->filterColumn('pn.id', function ($query, $keyword) {
          return $query->where('pn.id', '=', $keyword);
      })
      ->editColumn('short_received', function($pickup_request) {
          if ($pickup_request->short_received != 0) {
              return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->short_received . '</button>';
          }
          else {
              return 0;
          }
      })
      ->editColumn('over_received', function($pickup_request) {
          if ($pickup_request->over_received != 0) {
              return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->over_received . '</button>';
          }
          else {
              return 0;
          }
      })
      ->editColumn('bookings_link', function($pickup_notes) {
          if ($pickup_notes->bookings != 0) {
              return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_notes->bookings . '</button>';
          }
          else {
              return 0;
          }
      })
      ->editColumn('received_link', function($pickup_notes) {
          if ($pickup_notes->received != 0) {
              return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_notes->received . '</button>';
          }
      })
      ->editColumn('pickup_type', function($pickup_request) {
        return ($pickup_request->pickup_type == 0) ? 'Light' : 'Heavy';
      })
      ->addColumn('action', function($pickup_request) {
        $receive_button = '<button type="button" class="dropdown-item receive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Receive</div></button>';
        $done_button = '<button type="button" class="dropdown-item done"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Done</div></button>';
        $not_done_button = '<button type="button" class="dropdown-item not_done"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Not Done</div></button>';

        if ($pickup_request->status == 1) {
          if ($pickup_request->received > 0) {
            return '<div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">
                        ' . $receive_button . $done_button . '
                      </div>
                    </div>
            ';
          }
          else {
            return '<div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">
                        ' . $receive_button . $not_done_button . '
                      </div>
                    </div>
            ';
          }
        }
        else {
          return '';
        }
      })
      ->filterColumn('pickup_type',function ($query,$keyword){
              if ($keyword != '') {
                  $query->where('pickup_requests.pickup_type',$keyword);
              }
              else {
                  $query->whereRaw('false');
              }
      });

      return $datatables->make(true);
    }

    public function receive_summary_request_over_short_received(Request $request){
        $pickup_request_id = $request->input('pickup_request_id');

        $pickup_request = PickupRequest::find($pickup_request_id);

        $pickup_request_short_received_shipments = $pickup_request->pickup_request_short_received_shipments;

        $short_shipments = array();
        $voided_short_shipments = array();

        if ($pickup_request_short_received_shipments->count() != 0) {

            foreach ($pickup_request_short_received_shipments as $pickup_request_short_received_shipment) {
                $shipment = $pickup_request_short_received_shipment->shipment;

                if ($shipment->receiving_sheet_shipment) {
                    $short_shipments[str_pad($shipment->receiving_sheet_shipment->receiving_sheet_id, 6, '0', STR_PAD_LEFT)][] = $shipment->tracking_number;
                }
                else {
                  $voided_short_shipments[] = $shipment->tracking_number;
                }
            }

//            return ['status' => 0, 'success' => 'Shipments found Short Received', 'short_received' => $short_shipments];
        }
        $pickup_request_over_received_shipments = PickupRequestReceivedShipment::where('pickup_request_id', $pickup_request_id)->where('over_received', 1);

        $over_received_shipments = array();
        if ($pickup_request_over_received_shipments->exists()) {
            $pickup_request_over_received_shipments = $pickup_request_over_received_shipments->get();


            foreach ($pickup_request_over_received_shipments as $pickup_request_over_received_shipment) {
                $shipment = $pickup_request_over_received_shipment->shipment;

                $over_received_shipments[] = $shipment->tracking_number;
            }

        }

        if (empty($short_shipments)) {
            $short_shipments = FALSE;
        }

        if (empty($voided_short_shipments)) {
            $voided_short_shipments = FALSE;
        }

        if (empty($over_received_shipments)) {
            $over_received_shipments = FALSE;
        }


        if ($short_shipments || $voided_short_shipments || $over_received_shipments) {
            return ['status' => 0, 'success' => 'Shipments found Short or Over Received', 'short_received' => $short_shipments, 'voided_short_received' => $voided_short_shipments, 'over_received' => $over_received_shipments];
        }else{
            return ['status' => 0, 'success' => 'No Short or Over Received Shipments', 'short_received' => FALSE, 'voided_short_received' => FALSE, 'over_received' => FALSE];

        }

    }
    public function receive_summary_request_over_received(Request $request) {
      $pickup_request_id = $request->input('pickup_request_id');

      $pickup_request_over_received_shipments = PickupRequestReceivedShipment::where('pickup_request_id', $pickup_request_id)->where('over_received', 1);

      if ($pickup_request_over_received_shipments->exists()) {
        $pickup_request_over_received_shipments = $pickup_request_over_received_shipments->get();

        $over_received_shipments = array();

        foreach ($pickup_request_over_received_shipments as $pickup_request_over_received_shipment) {
          $shipment = $pickup_request_over_received_shipment->shipment;

          $over_received_shipments[] = $shipment->tracking_number;
        }

        return ['status' => 0, 'success' => 'Shipments found Over Received', 'over_received' => $over_received_shipments];
      }
      else {
        return ['status' => 0, 'success' => 'No Over Received Shipments', 'over_received' => FALSE];
      }
    }

    public function receive_summary_request_short_received(Request $request) {
      $pickup_request_id = $request->input('pickup_request_id');

      $pickup_request = PickupRequest::find($pickup_request_id);

      $pickup_request_short_received_shipments = $pickup_request->pickup_request_short_received_shipments;

      if ($pickup_request_short_received_shipments->count() != 0) {
        $short_shipments = array();
        $voided_short_shipments = array();

        foreach ($pickup_request_short_received_shipments as $pickup_request_short_received_shipment) {
          $shipment = $pickup_request_short_received_shipment->shipment;

          if ($shipment->receiving_sheet_shipment) {
            $short_shipments[str_pad($shipment->receiving_sheet_shipment->receiving_sheet_id, 6, '0', STR_PAD_LEFT)][] = $shipment->tracking_number;
          }
          else {
            $voided_short_shipments[] = $shipment->tracking_number;
          }
        }

        if (empty($short_shipments)) {
          $short_shipments = FALSE;
        }

        if (empty($voided_short_shipments)) {
          $voided_short_shipments = FALSE;
        }

        return ['status' => 0, 'success' => 'Shipments found Short Received', 'short_received' => $short_shipments, 'voided_short_received' => $voided_short_shipments];
      }
      else {
        return ['status' => 0, 'success' => 'No Short Received Shipments', 'short_received' => FALSE];
      }
    }

    public function receive_summary_request_done(Request $request) {
      $pickup_request_id = $request->input('pickup_request_id');

      $pickup_request = PickupRequest::find($pickup_request_id);

      if ($pickup_request->status == 1) {
        $pickup_request->status = 2;

        $pickup_request->save();

        $completed = TRUE;

        $pickup_note = $pickup_request->pickup_note_request->pickup_note;

        foreach ($pickup_note->pickup_note_requests as $pickup_note_request) {
          $pickup_request = $pickup_note_request->pickup_request;

          if ($pickup_request->status < 2) {
            $completed = FALSE;

            break;
          }
        }

        $pickup_request = PickupRequest::find($pickup_request_id);

        //for dispute start
        $pickup_request_short_received_shipments = $pickup_request->pickup_request_short_received_shipments;

        if ($pickup_request_short_received_shipments->count() != 0) {
          $short_shipments = array();

          foreach ($pickup_request->pickup_request_short_received_shipments as $pickup_request_short_received_shipment) {
            $shipment = $pickup_request_short_received_shipment->shipment;

              if ($shipment->receiving_sheet_shipment) {
                  $short_shipments[str_pad($shipment->receiving_sheet_shipment->receiving_sheet_id, 6, '0', STR_PAD_LEFT)][] = $shipment->id;
                }
          }

          if (!empty($short_shipments)) {
            foreach ($short_shipments as $receiving_sheet_id => $short_shipment_ids) {
              DisputeController::add_short_received_shipments($receiving_sheet_id, $short_shipment_ids);
            }
          }
        }
          $pickup_request_over_received_shipments = PickupRequestReceivedShipment::where('pickup_request_id', $pickup_request_id)->where('over_received', 1);

          if ($pickup_request_over_received_shipments->exists()) {
              $pickup_request_over_received_shipments = $pickup_request_over_received_shipments->get();

              $over_received_shipments = array();

              foreach ($pickup_request_over_received_shipments as $pickup_request_over_received_shipment) {
                  $shipment = $pickup_request_over_received_shipment->shipment;

                  $over_received_shipments[] = $shipment->id;
              }
              DisputeController::add_over_received_shipments($over_received_shipments);

          }

        //dispute end

        foreach ($pickup_request->pickup_request_assigned_shipments as $pickup_request_assigned_shipment) {
          if ($pickup_request_assigned_shipment->status < 2) {
            $this->generate($pickup_request_assigned_shipment->shipment_id);
          }
        }

        $received_shipments = $pickup_request->pickup_request_received_shipments;

        if ($received_shipments) {
          foreach ($received_shipments as $received_shipment) {
            $shipment = $received_shipment->shipment;
            ShipmentsPickupJourneyController::add($shipment->id, 5, Auth::id(), $pickup_note->id);
          }
        }

        if ($completed) {
          $pickup_note->status_id = 4;
          $pickup_note->updated_by = Auth::id();
          $pickup_note->save();

          return ['status' => 0, 'success' => 'Pickup has been marked Done & Pickup Note has been Completed', 'complete' => TRUE];
        }
        else {
          return ['status' => 0, 'success' => 'Pickup has been marked Done', 'complete' => FALSE];
        }
      }
      else {
        return ['status' => 1, 'error' => 'Selected Pickup has already been modified'];
      }
    }

    public function receive_summary_request_not_done(Request $request) {
      $pickup_request_id = $request->input('pickup_request_id');

      $pickup_request = PickupRequest::find($pickup_request_id);

      if ($pickup_request->status == 1) {
        $pickup_request->status = 0;

        $pickup_request->save();

        $completed = TRUE;

        $pickup_note = $pickup_request->pickup_note_request->pickup_note;

        PickupNoteRequest::where('pickup_note_id', $pickup_note->id)->where('pickup_request_id', $pickup_request_id)->delete();

        foreach ($pickup_note->pickup_note_requests as $pickup_note_request) {
          $pickup_request = $pickup_note_request->pickup_request;

          if ($pickup_request->status < 2) {
            $completed = FALSE;

            break;
          }
        }

        if ($completed) {
          $pickup_note->status_id = 4;
          $pickup_note->updated_by = Auth::id();
          $pickup_note->save();

          return ['status' => 0, 'success' => 'Pickup has been marked Not Done and is moved to Pending & Pickup Note has been Completed', 'complete' => TRUE];
        }
        else {
          return ['status' => 0, 'success' => 'Pickup has been marked Not Done and is moved to Pending', 'complete' => FALSE];
        }
      }
      else {
        return ['status' => 1, 'error' => 'Selected Pickup has already been modified'];
      }
    }

    public function bookedvsreceived_index(){
        if (session('role_id') == 1){
            $cities = City::select('id','name')->where('pickup',1)->get();

        }else{
            $cities = City::select('id','name')->where('pickup',1)->whereIn('hub_id',session('hubs'))->get();
        }
        return view('admin.pickups.bookedvsreceived.index')->with('cities',$cities);
    }

    public function bookedvsreceived_list(Request $request){

        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftJoin('shipments_journey as srec', function ($join) {
                $join->on('srec.shipment_id', '=', 'shipments.id')
                    ->where('srec.shipper_status_id','=',2);
            })
            ->select('u.name as shipper','u.id as shipper_id',DB::raw('count(shipments.id) as booked'),DB::raw('count(srec.shipment_id) as received'))
            ->where('usi.city_id',$request->city_select)
            ->where('shipments.shipper_status_id','!=',17)
            ->whereBetween('shipments.created_at',[$request->search_from,$request->search_to])
            ->groupBy('u.id')->get();
        return response()->json(['status'=>1,'shipments'=>$shipments]);

    }
    public function bookedvsreceived_booked_list(Request $request){
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->select('shipments.tracking_number')
            ->where('usi.city_id',$request->city_id)
            ->where('u.id',$request->shipper_id)
            ->where('shipments.shipper_status_id','!=',17)
            ->whereBetween('shipments.created_at',[$request->search_from,$request->search_to])
            ->get();
        return response()->json(['status'=>1,'shipments'=>$shipments]);
    }
    public function bookedvsreceived_received_list(Request $request){
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftJoin('shipments_journey as srec', function ($join) {
                $join->on('srec.shipment_id', '=', 'shipments.id')
                    ->where('srec.shipper_status_id','=',2);
            })
            ->select('shipments.tracking_number')
            ->where('usi.city_id',$request->city_id)
            ->where('u.id',$request->shipper_id)
            ->where('srec.shipper_status_id',2)
            ->where('shipments.shipper_status_id','!=',17)
            ->whereBetween('shipments.created_at',[$request->search_from,$request->search_to])
            ->get();
        return response()->json(['status'=>1,'shipments'=>$shipments]);
    }
    public function pending_all_bookings(Request $request){
        $pickup_request_id = $request->input('pickup_request_id');

        $pickup_request = PickupRequest::find($pickup_request_id);

        $pickup_request_all_booked_shipments = $pickup_request->pickup_request_assigned_shipments;

        if ($pickup_request_all_booked_shipments->count() != 0) {
            $bookings = array();
            foreach ($pickup_request_all_booked_shipments as $all_shipments) {
                $shipment = $all_shipments->shipment_id;
                $shipment_details = Shipment::find($shipment);
                $bookings[] = $shipment_details->tracking_number;
            }

            return ['status' => 0, 'success' => 'Booked Shipments', 'booked' => $bookings];
        }
        else {
            return ['status' => 0, 'success' => 'No Booked Shipments', 'booked' => FALSE];
        }
    }
    public function pending_bookings(Request $request){
        $pickup_request_id = $request->input('pickup_request_id');

        $pickup_request = PickupRequest::find($pickup_request_id);

        $pickup_request_all_booked_shipments = $pickup_request->pickup_request_assigned_shipments()->where('status',1)->get();

        if ($pickup_request_all_booked_shipments->count() != 0) {
            $bookings = array();
            foreach ($pickup_request_all_booked_shipments as $all_shipments) {
                $shipment = $all_shipments->shipment_id;
                $shipment_details = Shipment::find($shipment);
                $bookings[] = $shipment_details->tracking_number;
            }

            return ['status' => 0, 'success' => 'Pending Booked Shipments', 'booked' => $bookings];
        }
        else {
            return ['status' => 0, 'success' => 'No Pending Booked Shipments', 'booked' => FALSE];
        }
    }
    public function assigned_all_bookings(Request $request){
        $pickup_note_id = $request->input('pickup_note_id');

        $pickup_note = PickupNote::find($pickup_note_id);
        $pickup_note_requests = $pickup_note->pickup_note_requests;
        if($pickup_note_requests->count() != 0){
            $bookings = array();
            $shipments = array();
            foreach ($pickup_note_requests as $pickup_note_request) {
                $pickup_request = PickupRequest::find($pickup_note_request->pickup_request_id);
                $shipper = $pickup_request->shipper->name;
               $bookings [$shipper]= PickupRequestAssignedShipment::where('pickup_request_id',$pickup_note_request->pickup_request_id)->select('shipment_id')->get();
                foreach ($bookings[$shipper] as $shipment) {
                    $shipment_details = Shipment::find($shipment->shipment_id);
                    $shipments [$shipper][] = $shipment_details->tracking_number;
               }
            }

            return ['status' => 0, 'success' => 'Booked Shipments', 'booked' => $shipments];
        }else {
            return ['status' => 0, 'success' => 'No Booked Shipments', 'booked' => FALSE];
        }

    }
    public function assigned_pickups(Request $request){
        $pickup_note_id = $request->input('pickup_note_id');

        $pickup_note = PickupNote::find($pickup_note_id);
        $pickup_note_requests = $pickup_note->pickup_note_requests;
        if($pickup_note_requests->count() != 0){
            $pickups = array();
            foreach ($pickup_note_requests as $pickup_note_request) {
              $pickup_details =  PickupRequest::find($pickup_note_request->pickup_request_id);
              $pickups[$pickup_details->id]['name'] = $pickup_details->shipper->name;
              $pickups[$pickup_details->id]['bookings'] = ($pickup_details->bookings != '')? $pickup_details->bookings:0;
              $pickups[$pickup_details->id]['pending_bookings'] = ($pickup_details->pending_bookings != '')? $pickup_details->pending_bookings:0;
            }
            return ['status' => 0, 'success' => 'Pickup Requests', 'pickups' => $pickups];
        }else {
            return ['status' => 0, 'success' => 'No Pickup Requests', 'pickups' => FALSE];
        }

    }
    public function receive_all_bookings(Request $request){
        $pickup_note_id = $request->input('pickup_note_id');

        $pickup_note = PickupNote::find($pickup_note_id);
        $pickup_note_requests = $pickup_note->pickup_note_requests;
        if($pickup_note_requests->count() != 0){
            $bookings = array();
            $shipments = array();
            foreach ($pickup_note_requests as $pickup_note_request) {
                $pickup_request = PickupRequest::find($pickup_note_request->pickup_request_id);
                $shipper = $pickup_request->shipper->name;
                $bookings [$shipper]= PickupRequestAssignedShipment::where('pickup_request_id',$pickup_note_request->pickup_request_id)->select('shipment_id')->get();
                foreach ($bookings[$shipper] as $shipment) {
                    $shipment_details = Shipment::find($shipment->shipment_id);
                    $shipments [$shipper][] = $shipment_details->tracking_number;
                }
            }

            return ['status' => 0, 'success' => 'Booked Shipments', 'booked' => $shipments];
        }else {
            return ['status' => 0, 'success' => 'No Booked Shipments', 'booked' => FALSE];
        }

    }
    public function summary_receive_all_bookings(Request $request){
        $pickup_request_id = $request->input('pickup_request_id');
        $pickup_request = PickupRequest::find($pickup_request_id);
        $pickup_request_all_booked_shipments = $pickup_request->pickup_request_assigned_shipments;

        if ($pickup_request_all_booked_shipments->count() != 0) {
            $bookings = array();
            foreach ($pickup_request_all_booked_shipments as $all_shipments) {
                $shipment = $all_shipments->shipment_id;
                $shipment_details = Shipment::find($shipment);
                $bookings[] = $shipment_details->tracking_number;
            }

            return ['status' => 0, 'success' => 'Booked Shipments', 'booked' => $bookings];
        }
        else {
            return ['status' => 0, 'success' => 'No Booked Shipments', 'booked' => FALSE];
        }
    }
    public function summary_receive_shipments(Request $request){
        $pickup_request_id = $request->input('pickup_request_id');
        $pickup_request = PickupRequest::find($pickup_request_id);
        $pickup_request_all_received_shipments = $pickup_request->pickup_request_received_shipments;

        if ($pickup_request_all_received_shipments->count() != 0) {
            $bookings = array();
            foreach ($pickup_request_all_received_shipments as $all_shipments) {
                $shipment = $all_shipments->shipment_id;
                $shipment_details = Shipment::find($shipment);
                $bookings[] = $shipment_details->tracking_number;
            }

            return ['status' => 0, 'success' => 'Booked Shipments', 'booked' => $bookings];
        }
        else {
            return ['status' => 0, 'success' => 'No Booked Shipments', 'booked' => FALSE];
        }
    }

    //History Section starts
    public function history_index(){
        $pickup_status = PickupNoteStatus::all();
        return view('admin.pickups.history.index')->with(['pickup_status'=>$pickup_status]);
    }
    public function history_list(Request $request){
        $pickup_note = PickupNote::join('cities','cities.id','=','pickup_notes.city_id')
            ->leftjoin('riders','riders.id','=','pickup_notes.rider_id')
            ->leftjoin('admins as ab','ab.id','=','pickup_notes.assigned_by_user_id')
            ->leftjoin('admins as up','up.id','=','pickup_notes.updated_by')
            ->leftjoin('pickup_note_statuses as pns','pickup_notes.status_id','pns.id')
            ->select(['pickup_notes.id as pn_id','pickup_notes.id as pickup_note_no','pns.name as status','cities.name as city','pickup_notes.pickups','pickup_notes.bookings','pickup_notes.bookings as bookings_link','riders.name as rider','pickup_notes.created_at as assigned_date','ab.name as assigned_by','pickup_notes.updated_at as completed_date','up.name as completed_by']);
        if (session('role_id') != 1) {
            $pickup_note = $pickup_note->whereIn('cities.hub_id', session('hubs'));
        }
        $pickup_note = Datatables::of($pickup_note)
            ->editColumn('pn_id', function ($pickup_note) {
                return str_pad($pickup_note->pn_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('pickup_note_no', function($pickup_note) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($pickup_note->pickup_note_no, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->editColumn('bookings_link', function($pickup_notes) {
                if ($pickup_notes->bookings != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_notes->bookings . '</button>';
                }
                else {
                    return 0;
                }
            });
        return $pickup_note->make(true);
    }
    public function history_bookings(Request $request){
        $pickup_note_id = $request->input('pickup_note_id');

        $pickup_note = PickupNote::find($pickup_note_id);
        $pickup_note_requests = $pickup_note->pickup_note_requests;
        if($pickup_note_requests->count() != 0){
            $bookings = array();
            $shipments = array();
            foreach ($pickup_note_requests as $pickup_note_request) {
                $pickup_request = PickupRequest::find($pickup_note_request->pickup_request_id);
                $shipper = $pickup_request->shipper->name;
                $bookings [$shipper]= PickupRequestAssignedShipment::where('pickup_request_id',$pickup_note_request->pickup_request_id)->select('shipment_id')->get();
                foreach ($bookings[$shipper] as $shipment) {
                    $shipment_details = Shipment::find($shipment->shipment_id);
                    $shipments [$shipper][] = $shipment_details->tracking_number;
                }
            }

            return ['status' => 0, 'success' => 'Booked Shipments', 'booked' => $shipments];
        }else {
            return ['status' => 0, 'success' => 'No Booked Shipments', 'booked' => FALSE];
        }

    }

}