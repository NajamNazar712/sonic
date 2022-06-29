<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsPickupJourneyController;
use App\Http\Models\Admin\Retail\RetailCashDeposit;
use App\Http\Models\Admin\Retail\RetailCashDepositShipment;
use App\Http\Models\Admin\Retail\RetailParcelReceiving;
use App\Http\Models\Admin\Retail\RetailParcelReceivingShipment;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\ShipmentScanningScreenLocation;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use App\Http\Models\WMS\WmsCurrentStock;
use App\Http\Models\WMS\WmsPendingPicking;
use App\Http\Models\WMS\WmsShipmentProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Controllers\ShipmentsJourneyController;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Retail\OtherParcelReceiving;
use App\Http\Models\Admin\Retail\OtherParcelReceivingShipment;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipment;
use App\Http\Models\City;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\BookingType;
use App\Http\Models\Product;
use App\Http\Models\ShipmentPaymentStatus;
use App\Http\Models\WMS\WmsProductBarcode;
use Auth;
use DB;

use Carbon\Carbon;
use Yajra\Datatables\Datatables;

class AdminShipmentCancelController extends Controller
{
    public function __construct() {
      $this->middleware('auth:admin');

      $this->middleware('Permission');
    }

    static public function cancel() {
        $active_users = User::whereIn('status', [3,4])->whereNotIn('id', [3324, 7762, 5982, 10104, 14110])->get();
        if(count($active_users)){
            foreach ($active_users as $user){
                if($user->auto_shipment_cancellation_days == null){
                    $settings = GlobalSettings::where('type', 'shipment_cancellation_cut_off_days')->first();

                    $days = $settings->setting_value;

                    $date = Carbon::now()->subDays($days);
                }
                else{
                    $days = $user->auto_shipment_cancellation_days;
                    $date = Carbon::now()->subDays($days);
                }
                $shipments = Shipment::where('shipper_status_id', 1)->where('user_id', $user->id)->where('created_at', '<', $date)->groupBy('id');

                if ($shipments->exists()) {
                    foreach ($shipments->get() as $shipment) {
                        //cacel from warehouse

                        if($shipment->warehouse != 1){
                            // $shipment->warehouse_order_status = 9;
                        //cacel from warehouse end

                        $shipment->shipper_status_id = 17;
                        $shipment->consignee_status_id = 17;
                        $shipment->save();
                        ShipmentsPickupJourneyController::add($shipment->id, 4);

                        V2AdminPickupsController::cancel($shipment->id);

                        $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->latest()->first();
                        if($pickup_request_shipment){
                            $pickup_requests = V2PickupRequestShipment::where('pickup_request_id', $pickup_request_shipment->pickup_request_id);
                            if($pickup_requests->exists()){
                                $pickup_requests = $pickup_requests->get();
                                $flag = true;
                                foreach ($pickup_requests as $pickup_request){
                                    $is_shipment = Shipment::find($pickup_request->shipment_id);
                                    if($is_shipment->shipper_status_id != 17){
                                        $flag = false;
                                    }
                                }
                                if($flag == true){
                                    $pickup_request = V2PickupRequest::find($pickup_request_shipment->pickup_request_id);
                                    $pickup_request->status_id = 4;
                                    $pickup_request->save();
                                }
                            }
                        }
                        //cacel from warehouse
                        if($shipment->warehouse == 1){
                            $shipment_products = WmsShipmentProduct::where('shipment_id', $shipment->id)->where('courier_id', 1)->get();
                            if($shipment_products){
                                foreach ($shipment_products as $shipment_product){
                                    $current_stock_addition = WmsCurrentStock::where('product_id', $shipment_product->product_id)->where('warehouse_pickup_address_id', $shipment->pickup_address_id)->first();
                                    if($current_stock_addition){
                                        $current_stock_addition->stock = $current_stock_addition->stock + $shipment_product->quantity;
                                        $current_stock_addition->save();
                                    }
                                }
                            }

                            WmsProductBarcode::where('shipment_id', $shipment->id)->where('courier_id', 1)->update(['shipment_id' => null, 'courier_id' => null, 'picklist_id' => null]);
                        }
                        //cacel from warehouse end
                        
                        ShipmentsJourneyController::add($shipment->id, 17, 17, NULL, 'Auto Cancellation after ' . $days . ' Day(s)', $shipment->user_id, NULL);
                    }
                    }
                }
            }
        }
    }

    public function index(Request $request) {
        ActivityTrailController::createActivityTrailLog(Auth::id(),291);
        $shippers = User::where('status', 3)->where('blacklist', 0)->select('id', 'name')->get();
        $cities = City::select('id', 'name')->get();
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        $service_type = BookingType::all();
        $products = Product::select('id', 'product_name')->get();

        return view('admin.cancelled_shipments.index')->with(['cities' => $cities, 'shippers' => $shippers, 'shipment_status' => $shipment_status, 'service_type' => $service_type, 'products' => $products]);
    }

    public function list(Request $request) {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),292);
        }
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
        ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
        ->select(['shipments.id', 'shipments.tracking_number as tracking_number', 'shipments.order_id', 'u.id as account_number', 'u.name as shipper', 'bt.booking_type as service_type', 'shipments_journey.remarks', 'oc.name as origin', 'dc.name as destination', 'shipments.consignee_name', 'shipments.consignee_phone_number_1', 'shipments.consignee_phone_number_2', 'shipments.consignee_address', 'shipments.amount as collection_amount', 'shipments.created_at as booking_date', 'shipments.special_instructions as instructions', 'shipments.booking_type_id', 'usi.poc'])
        ->where('shipments.shipper_status_id', '=', 17);
        
        if(session('department_id') == 7){
            if(!in_array(session('id'), session('sale_users_bypass'))){
                $shipments = $shipments->where(function ($query) {
                    $query->whereIn('u.id', session('tagged_shippers'));
                });
            }
        }
        if (session('role_id') != 1) {
            $shipments = $shipments->where(function ($query) {
                $query->whereIn('oc.hub_id', session('hubs'));
            });
        }

        $datatables = Datatables::of($shipments)
        ->addColumn('tracking_number_hyperlink', function ($shipment) {
            return '<u><a href=' . route('admin.tracking.index') . '?tracking_number=' . $shipment->tracking_number . ' class="tracking" target="_blank">' . $shipment->tracking_number . '</a></u>';
        })
            ->editColumn('collection_amount', function($shipment){
                return number_format($shipment->collection_amount);
            })
        ->addColumn('consignee_contact', function ($shipment) {
            $consignee_contact = $shipment->consignee_phone_number_1;

            if ($shipment->consignee_phone_number_2) {
                $consignee_contact = ' | ' . $shipment->consignee_phone_number_2;
            }

            return $consignee_contact;
        })
        ->editColumn('shipper', function ($shipment) {
            if ($shipment->booking_type_id == 4) {
                return $shipment->shipper .' (' . $shipment->poc . ')';
            }
            else {
                return $shipment->shipper;
            }
        })
        ->filterColumn('u.name', function ($query, $keyword) {
            $query->where(function ($sub_query) use ($keyword) {
                $sub_query->where('shipments.booking_type_id', '!=', 4)
                    ->where('u.name', 'like', '%' . $keyword . '%');
            })
                ->orWhere(function ($sub_query) use ($keyword) {
                    $sub_query->where('shipments.booking_type_id', '=', 4)
                        ->where('usi.poc', 'like', '%' . $keyword . '%');
                });
        })
        ->orderColumn('u.name', 'u.name $1, usi.poc $1')
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
        ->filterColumn('product_type',function ($query, $keyword) {
            if ($keyword != '') {
                $query->where('p.id', $keyword);
            }
            else {
                $query->whereRaw('false');
            }
        })
        ->orderColumn('phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1');

        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatables->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        }

        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatables->whereBetween('shipments.created_at', [$from,$to]);
        }

        return $datatables->make(true);
    }

    public function bulk_revert(Request $request) {
        foreach ($request->shipment_ids as $shipment_id) {
            $shipment = Shipment::find($shipment_id);

            if ($shipment->shipper_status_id == 17 && $shipment->warehouse == 0) {
                $shipment->shipper_status_id = 1;
                $shipment->consignee_status_id = 1;

                $shipment->save();

                AdminPickupsController::generate($shipment_id);

                ShipmentsJourneyController::add($shipment_id, 1, 1, NULL, 'Shipment has been Reverted', NULL, Auth::id());
            }
        }

        return ['status' => 0, 'success' => 'Shipment(s) has been Reverted'];
    }


    static public function auto_shipment_cancel_days(Request $request)
    {
        $user_id = $request->shipper_id;
        $days = $request->days;
        $user = User::find($user_id);
        if($user){
            if($days > 0 && $days != null){
                $user->auto_shipment_cancellation_days = $days;
                $user->save();
                return response()->json(['status' => 1, 'success' => 'Auto shipment cancellation days updated successfully for ' . $user->name]);
            }
            else{
                return response()->json(['status' => 0, 'error' => 'Invalid days']);
            }
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Invalid Shipper']);
        }
    }

    public function revert(Request $request) {

            $shipment = Shipment::find($request->shipment_id);

            if ($shipment->shipper_status_id == 17 && $shipment->warehouse == 0) {
                $shipment->shipper_status_id = 1;
                $shipment->consignee_status_id = 1;

                $shipment->save();

                AdminPickupsController::generate($shipment->id);

                ShipmentsJourneyController::add($shipment->id, 1, 1, NULL, 'Shipment has been Reverted', NULL, Auth::id());
                return ['status' => 0, 'success' => 'Shipment(s) has been Reverted'];
            }
            else{
                return ['status' => 1, 'error' => 'Warehouse Shipment can not be reverted from Sonic!'];
            }

    }


}