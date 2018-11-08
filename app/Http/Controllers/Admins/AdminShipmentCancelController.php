<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\ShipmentsJourneyController;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipment;

use Auth;

use Carbon\Carbon;

class AdminShipmentCancelController extends Controller
{
    public function __construct() {
      $this->middleware('auth:admin');

      $this->middleware('Permission');
    }

    static public function cancel() {
        $settings = GlobalSettings::where('type', 'shipment_cancellation_cut_off_days')->first();

        $days = $settings->setting_value;

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
        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        $cities = City::where('status', 1)->select('id', 'name')->get();
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        $service_type = BookingType::all();
        $products = Product::select('id', 'product_name')->get();
        $payment_status = ShipmentPaymentStatus::all();

        return view('admin.cancelled_shipments')->with(['cities' => $cities, 'shippers' => $shippers, 'shipment_status' => $shipment_status, 'service_type' => $service_type, 'products' => $products, 'payment_status' => $payment_status]);
    }

    public function list(Request $request) {
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
        ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
        ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
        ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
        ->join('cities as h' , 'dc.hub_id', '=' , 'h.id')
        ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
        ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
        ->leftJoin('shipments_journey', function ($join) {
            $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
            ->where('shipments_journey.created_at', '=', DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
        })
        ->join('shipment_status as ss', 'ss.id', '=', 'shipments_journey.shipper_status_id')
        ->leftjoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
        ->leftjoin('shipment_items as si', 'si.shipment_id', '=', 'shipments.id')
        ->leftjoin('products as p', 'p.id', '=', 'si.product_type_id')
        ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
        ->select(['shipments.id', 'shipments.tracking_number as tracking_number', 'shipments.order_id', 'u.id as account_number', 'u.name as shipper', 'bt.booking_type as service_type', 'ss.name as status', 'ssr.name as reason', 'sps.name as payment_status', 'oc.name as origin', 'dc.name as destination', 'shipments.consignee_name', 'shipments.consignee_phone_number_1', 'shipments.consignee_phone_number_2', 'shipments.consignee_address', 'shipments.amount as collection_amount', 'p.product_name as product_type', 'shipments.created_at as booking_date', 'shipments.special_instructions as instructions'])
        ->where('shipments.shipper_status_id', '=', 17);

        if (session('role_id') != 1) {
            $shipments = $shipments->where(function ($query) {
                $query->whereIn('oc.hub_id', session('hubs'));
            });
        }

        $datatables = Datatables::of($shipments)
        ->addColumn('tracking_number_hyperlink', function ($shipment) {
            return '<u><a href=' . route('admin.tracking.index') . '?tracking_number=' . $shipment->tracking_number . ' class="tracking" target="_blank"></a></u>';
        })
        ->addColumn('consignee_contact', function ($shipment) {
            $consignee_contact = $shipment->consignee_phone_number_1;

            if ($shipment->consignee_phone_number_2) {
                $consignee_contact = ' | ' . $shipment->consignee_phone_number_2;
            }

            return $consignee_contact;
        })
        ->addColumn('action', function($shipment) {
            if (session('role_id') == 1 || in_array(118, session('permissions'))) {
                $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                        <button type="button" class="dropdown-item revert"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Revert</div></button>
                    </div>
                  </div>
                ';

                return $dropdown;
            }
            else {
                '';
            }
        })
        ->editColumn('account_number', function ($shipment) {
            return str_pad($shipment->account_number, 6, '0', STR_PAD_LEFT);
        })
        ->filterColumn('u.id', function ($query, $keyword) {
            return $query->where('u.id', '=', $keyword);
        })
        ->filterColumn('consignee_contact', function ($query, $keyword) {
            $keyword = str_replace('-', '', strtolower($keyword));

            if ($keyword != '') {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('shipments.consignee_phone_number_1', 'like', '%' . $keyword . '%')
                    ->orWhere('shipments.consignee_phone_number_2', 'like', '%' . $keyword . '%');
                });
            }

            else {
                $query->whereRaw('false');
            }
        })
        ->filterColumn('status', function ($query, $keyword) {
            if ($keyword != '') {
                $query->where('ss.id', $keyword);
            }
            else {
                $query->whereRaw('false');
            }
        })
        ->filterColumn('service_type',function ($query, $keyword) {
            if ($keyword != '') {
                $query->where('bt.id', $keyword);
            }
            else {
                $query->whereRaw('false');
            }
        })
        ->filterColumn('payment_status',function ($query, $keyword) {
            if ($keyword != '') {
                $query->where('sps.id', $keyword);
            }
            else {
                $query->whereRaw('false');
            }
        })
        ->filterColumn('product_type',function ($query, $keyword) {
            if ($keyword != '') {
                $query->where('p.id', $keyword);
            }
            else {
                $query->whereRaw('false');
            }
        })
        ->orderColumn('phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')

        return $datatables->make(true);
    }

    public function revert(Request $request) {}

}