<?php

namespace App\Http\Controllers\Shippers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\Shipment;
use App\Http\Models\ReceivingSheet;
use App\Http\Models\ReceivingSheetShipment;
use App\Http\Models\ReceivingSheetReceived;

use Auth;
use DB;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class ShipperReceivingSheetHistoryController extends Controller
{
    public function __construct() {
      $this->middleware('auth:web,substitute_users');

      $this->middleware('Permission');
    }

    public function index() {
      return view('client.shipment.receiving_sheet_history.index');
    }

    public function list() {
      $receiving_sheet_received = ReceivingSheetReceived::leftjoin('receiving_sheet_shipments as rss', 'receiving_sheet_received.receiving_sheet_id', '=', 'rss.receiving_sheet_id')
      ->leftjoin('receiving_sheets as rs', 'receiving_sheet_received.receiving_sheet_id', '=', 'rs.id')
      ->join('user_shipping_infos as usi', 'receiving_sheet_received.pickup_address_id', '=', 'usi.id')
      ->join('cities as c', 'usi.city_id', '=', 'c.id')
      ->select('receiving_sheet_received.receiving_sheet_id as id', 'receiving_sheet_received.receiving_sheet_id as receiving_sheet', DB::raw('count(receiving_sheet_received.pickup_address_id) as received'), 'c.name as origin', 'rs.created_at AS booking_date', 'receiving_sheet_received.pickup_address_id')
      ->where('receiving_sheet_received.user_id', session('user_id'))
      ->groupBy('receiving_sheet_received.receiving_sheet_id')
      ->groupBy('receiving_sheet_received.pickup_address_id');

      return Datatables::of($receiving_sheet_received)
      ->editColumn('receiving_sheet', function($receiving_sheet_received) {
        if ($receiving_sheet_received->receiving_sheet) {
          return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle id">' . str_pad($receiving_sheet_received->receiving_sheet, 12, "0", STR_PAD_LEFT) . '</span></button>';
        }
        else {
          return '';
        }
      })
      ->addColumn('booked_count', function($receiving_sheet_received) {
        if ($receiving_sheet_received->receiving_sheet) {
          return ReceivingSheetShipment::where('receiving_sheet_id', $receiving_sheet_received->receiving_sheet)->count();
        }
        else {
          return 0;
        }
      })
      ->addColumn('booked', function($receiving_sheet_received) {
       if ($receiving_sheet_received->receiving_sheet) {
         $booked = ReceivingSheetShipment::where('receiving_sheet_id', $receiving_sheet_received->receiving_sheet)->count();

         return '<button class="btn btn-sm btn-outline-info align-middle">' . $booked . '</button>';
       }
       else {
         return '';
       }
     })
      ->addColumn('received_count', function($receiving_sheet_received) {
        if ($receiving_sheet_received->receiving_sheet) {
          return ReceivingSheetReceived::where('receiving_sheet_id', $receiving_sheet_received->receiving_sheet)->where('user_id', session('user_id'))->count();
        }
        else {
          return $receiving_sheet_received->received;
        }
      })
      ->editColumn('received', function($receiving_sheet_received) {
        if ($receiving_sheet_received->receiving_sheet) {
          $received = ReceivingSheetReceived::where('receiving_sheet_id', $receiving_sheet_received->receiving_sheet)->where('user_id', session('user_id'))->count();

          return '<button class="btn btn-sm btn-outline-info align-middle" data-id="' . $receiving_sheet_received->pickup_address_id . '">' . $received . '</button>';
        }
        else {
          return '<button class="btn btn-sm btn-outline-info align-middle" data-id="' . $receiving_sheet_received->pickup_address_id . '">' . $receiving_sheet_received->received . '</button>';
        }
      })
      ->editColumn('booking_date', function($receiving_sheet_received) {
        if ($receiving_sheet_received->booking_date) {
          return Carbon::parse($receiving_sheet_received->booking_date)->format('d/m/Y H:i A');
        }
        else {
          return '';
        }
      })
      ->addColumn('action', function($receiving_sheet_received) {
        if ($receiving_sheet_received->receiving_sheet) {

          if ($receiving_sheet_received->booked_count != $receiving_sheet_received->received_count) {
            return '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                      <button type="button" class="dropdown-item view_short_received"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">View Short Received</div></button>
                    </div>
                  </div>
            ';
          }
          else {
            return '';
          }
        }
        else {
          return '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                      <button type="button" class="dropdown-item create_receiving_sheet" data-id="' . $receiving_sheet_received->pickup_address_id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Create Receiving Sheet</div></button>
                    </div>
                  </div>
          ';
        }
      })
      ->filterColumn('receiving_sheet', function($query, $keyword) {
        $keyword = intval($keyword);

        if ($keyword != 0) {
          $query->where('receiving_sheet_received.receiving_sheet_id', '=', $keyword);
        }
        else {
          $query->whereNotNull('receiving_sheet_received.receiving_sheet_id');
        }
      })
      ->filterColumn('booked', function($query, $keyword) {
        $keyword = intval($keyword);

        if ($keyword != 0) {
          $query->where('receiving_sheet_received.receiving_sheet_id', '=', $keyword);
        }
        else {
          $query->whereNotNull('receiving_sheet_received.receiving_sheet_id');
        }
      })
      ->make(true);
    }

    public function booked_shipments(Request $request) {
      $receiving_sheet_shipments = ReceivingSheetShipment::where('receiving_sheet_id', $request->receiving_sheet_id)->get();

      $tracking_numbers = array();

      foreach ($receiving_sheet_shipments as $receiving_sheet_shipment) {
        $shipment = Shipment::find($receiving_sheet_shipment->shipment_id);

        $tracking_numbers[] = $shipment->tracking_number;
      }

      return $tracking_numbers;
    }

    public function received_shipments(Request $request) {
      if ($request->receiving_sheet_id != 'NaN') {
        $receiving_sheet_received = ReceivingSheetReceived::where('receiving_sheet_id', $request->receiving_sheet_id)->where('user_id', session('user_id'))->get();
      }
      else {
        $receiving_sheet_received = ReceivingSheetReceived::whereNull('receiving_sheet_id')->where('user_id', session('user_id'))->where('pickup_address_id', $request->pickup_address_id)->get();
      }

      $tracking_numbers = array();

      foreach ($receiving_sheet_received as $received) {
        $shipment = Shipment::find($received->shipment_id);

        $tracking_numbers[] = $shipment->tracking_number;
      }

      return $tracking_numbers;
    }

    public function short_received_shipments(Request $request) {
      $receiving_sheet_shipments = ReceivingSheetShipment::where('receiving_sheet_id', $request->receiving_sheet_id)->pluck('shipment_id')->toArray();
      $receiving_sheet_received = ReceivingSheetReceived::where('receiving_sheet_id', $request->receiving_sheet_id)->pluck('shipment_id')->toArray();

      $tracking_numbers = array();

      foreach (array_diff($receiving_sheet_shipments, $receiving_sheet_received) as $shipment_id) {
        $shipment = Shipment::find($shipment_id);

        $tracking_numbers[] = $shipment->tracking_number;
      }

      return $tracking_numbers;
    }

    public function void(Request $request) {
      $receiving_sheet_shipments = ReceivingSheetShipment::where('receiving_sheet_id', $request->receiving_sheet_id)->pluck('shipment_id')->toArray();
      $receiving_sheet_received = ReceivingSheetReceived::where('receiving_sheet_id', $request->receiving_sheet_id)->pluck('shipment_id')->toArray();

      $shipment_ids = array_diff($receiving_sheet_shipments, $receiving_sheet_received);

      foreach ($shipment_ids as $shipment_id) {
        $receiving_sheet_shipment = ReceivingSheetShipment::find($shipment_id);

        $receiving_sheet_shipment->delete();
      }

      return ['status' => 0, 'success' => 'Shipment(s) has been Voided'];
    }

    public function create(Request $request) {
      $shipment_ids = array();

      $receiving_sheet = new ReceivingSheet();

      $receiving_sheet->user_id = session('user_id');
      $receiving_sheet->status = 1;

      $receiving_sheet->save();

      $receiving_sheet_id = $receiving_sheet->id;

      foreach (ReceivingSheetReceived::whereNull('receiving_sheet_id')->where('user_id', session('user_id'))->where('pickup_address_id', $request->pickup_address_id)->get() as $receiving_sheet_received) {
        $shipment_ids[] = $receiving_sheet_received->shipment_id;

        $receiving_sheet_received->receiving_sheet_id = $receiving_sheet_id;

        $receiving_sheet_received->save();
      }

      foreach ($shipment_ids as $shipment_id) {
        $receiving_sheet_shipment = new ReceivingSheetShipment();

        $receiving_sheet_shipment->shipment_id = $shipment_id;
        $receiving_sheet_shipment->receiving_sheet_id = $receiving_sheet_id;
        $receiving_sheet_shipment->status = 1;

        $receiving_sheet_shipment->save();
      }

      return ['status' => 0, 'success' => 'Receiving Sheet has been created', 'receiving_sheet_id' => $receiving_sheet_id];
    }
}