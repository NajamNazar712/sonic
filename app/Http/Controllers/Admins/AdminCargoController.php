<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\Shipment;

use Auth;
use DB;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class AdminCargoController extends Controller
{
    public function __construct() {
      $this->middleware('auth:admin');
    }

    public function pending_index() {
      return view('admin.cargo.pending');
    }

    public function pending_list(Request $request) {
      $shipments = Shipment::join('booking_types as bt', 'shipments.booking_type_id', '=', 'bt.id')
      ->join('shipment_status as ss', 'shipments.shipper_status_id', '=', 'ss.id')
      ->join('user_shipping_infos as usi', function($join) {
        $join->on('shipments.pickup_address_id', '=', 'usi.id')
        ->on('shipments.consignee_city_id', '!=', 'usi.city_id');
      })
      ->join('users as u', 'shipments.user_id', '=', 'u.id')
      ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
      ->join('cities as dc', 'shipments.consignee_city_id', '=', 'dc.id')
      ->join('shipping_modes as sm', 'shipments.shipping_mode_id', '=', 'sm.id')
      ->join('shipments_journey', function ($join) {
        $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
        ->on('shipments_journey.shipper_status_id', '=', DB::raw(2));
      })
      ->select('shipments.tracking_number', 'shipments.order_id', 'bt.booking_type as service_type', 'ss.name as status', 'oc.name as origin', 'dc.name as destination', 'u.name as shipper', 'shipments.amount', 'sm.mode as shipping_mode', 'shipments.created_at as booked_at', 'shipments_journey.created_at as arrival_at');
      // ->whereIn('shipments.shipper_status_id', [2, 14, 17, 20, 26]);

      $datatables = Datatables::of($shipments);

      return $datatables->make(true);
    }

    public function create_index() {
      return view('admin.cargo.create');
    }

}