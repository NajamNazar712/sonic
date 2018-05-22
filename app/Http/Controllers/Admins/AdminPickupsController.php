<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\ReceivingSheet;

use DB;
use Auth;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class AdminPickupsController extends Controller
{
    public function __construct() {
      $this->middleware('auth:admin');
    }

    public function pending_index() {
      return view('admin.pickups.pending.index');
    }

    public function pending_list(Request $request) {
      $receiving_sheets = ReceivingSheet::join('receiving_sheet_shipments AS rss', 'receiving_sheets.id', '=', 'rss.receiving_sheet_id')
      ->join('shipments AS s', 'rss.shipment_id' , '=', 's.id')
      ->join('users AS u', 's.user_id', '=', 'u.id')
      ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
      ->join('city_infos AS ci', 'usi.city_code', '=', 'ci.city_code')
      ->select('receiving_sheets.id AS id', 'u.name AS shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.city_name AS city', DB::raw('count(rss.receiving_sheet_id) AS no_of_bookings'), DB::raw('sum(s.estimated_weight) AS pickup_type'), 'receiving_sheets.created_at AS booking_date')
      ->groupBy('receiving_sheets.id');

      $datatables = Datatables::of($receiving_sheets)
      ->editColumn('pickup_type', function($receiving_sheet) {
        return (floatval($receiving_sheet->pickup_type) < 10) ? 'Light' : 'Heavy';
      })
      ->editColumn('booking_date', function($receiving_sheet) {
        return Carbon::parse($receiving_sheet->booking_date)->format('d/m/Y H:i A');
      })
      ->addColumn('action', function($receiving_sheet) {
        return '<button class="btn btn-sm btn-danger cancel">Cancel</button>';
      })
      ->filterColumn('no_of_bookings', function($query, $keyword) {
        //Empty to Remove Auto Filter Where Clause which is not Required
      })
      ->filterColumn('pickup_type', function($query, $keyword) {
        //Empty to Remove Auto Filter Where Clause which is not Required
      });

      $no_of_bookings = $datatables->request->get('columns')[7]['search']['value'];

      if (isset($no_of_bookings) && !empty($no_of_bookings)) {
        $datatables->havingRaw('count(rss.receiving_sheet_id) = ' . $no_of_bookings);
      }

      $pickup_type = $datatables->request->get('columns')[8]['search']['value'];

      if (isset($pickup_type) && !empty($pickup_type)) {
        $pickup_type = strtolower($pickup_type);

        if (strpos('light', $pickup_type) !== FALSE) {
          $datatables->havingRaw('sum(s.estimated_weight) < 10');
        }
        else if (strpos('heavy', $pickup_type) !== FALSE) {
          $datatables->havingRaw('sum(s.estimated_weight) >= 10');
        }
        else {
          $datatables->havingRaw('false');
        }
      }

      return $datatables->make(true);
    }

    public function pending_store(Request $request) {
    }
}