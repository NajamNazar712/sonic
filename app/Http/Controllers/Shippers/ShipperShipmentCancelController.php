<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\CancelledShipmentArrival;
use App\Http\Models\BookingType;
use App\Http\Models\City;
use App\Http\Models\Product;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\Shipper\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ShipperShipmentCancelController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function index(){
        $service_type = BookingType::all();

        return view('client.cancelled.cancelled_shipments')->with(['service_type' => $service_type]);
    }

    public function list(Request $request) {
        $from = Carbon::now()->subMonths(6)->startOfDay();
        $to   = Carbon::now()->endOfDay();

        $connection = 'reports';
        // First and last shipments_journey IDs within the window
        $sj_from_id = DB::connection($connection)
            ->table('shipments_journey')
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'asc')->orderBy('id', 'asc')
            ->limit(1)->value('id');

        $sj_to_id = DB::connection($connection)
            ->table('shipments_journey')
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'desc')->orderBy('id', 'desc')
            ->limit(1)->value('id');
        //
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' , 'dc.hub_id', '=' , 'h.id')
            ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->leftJoin('shipments_journey', function ($join) use ($sj_from_id, $sj_to_id) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->whereBetween('shipments_journey.id', [$sj_from_id, $sj_to_id])
                    ->where('shipments_journey.created_at', '=', DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->select(['shipments.id', 'shipments.tracking_number as tracking_number', 'shipments.order_id', 'u.id as account_number', 'u.name as shipper', 'bt.booking_type as service_type', 'shipments_journey.remarks', 'oc.name as origin', 'dc.name as destination', 'shipments.consignee_name', 'shipments.consignee_phone_number_1', 'shipments.consignee_phone_number_2', 'shipments.consignee_address', 'shipments.amount as collection_amount', 'shipments.created_at as booking_date', 'shipments.special_instructions as instructions', 'shipments.booking_type_id', 'usi.poc'])
//            ->where('shipments.shipper_status_id', '=', 17)
            ->where('shipments.user_id', session('user_id'));


        $datatables = Datatables::of($shipments)
            ->addColumn('tracking_number_hyperlink', function ($shipment) {
                return '<u><a href=' . route('cod.tracking.index') . '?tracking_number=' . $shipment->tracking_number . ' class="tracking" target="_blank">' . $shipment->tracking_number . '</a></u>';
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
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                        <button type="button" class="dropdown-item revert"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Revert</div></button>
                    </div>
                  </div>
                ';

                    return $dropdown;

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

        return $datatables->rawColumns(['tracking_number_hyperlink','action'])->make(true);
    }
    public function bulk_revert(Request $request) {
        foreach ($request->shipment_ids as $shipment_id) {
            $shipment = Shipment::find($shipment_id);

            if ($shipment->shipper_status_id == 17 && $shipment->warehouse == 0) {
                $shipment->shipper_status_id = 1;
                $shipment->consignee_status_id = 1;

                $shipment->save();

                AdminPickupsController::generate($shipment_id);

                ShipmentsJourneyController::add($shipment_id, 1, 1, NULL, 'Shipment has been Reverted', session('user_id'), NULL);
            }
        }

        return ['status' => 0, 'success' => 'Shipment(s) has been Reverted'];
    }

    public function revert(Request $request) {

        $shipment = Shipment::find($request->shipment_id);

        if ($shipment->shipper_status_id == 17 && $shipment->warehouse == 0) {
            $shipment->shipper_status_id = 1;
            $shipment->consignee_status_id = 1;

            $shipment->save();

            AdminPickupsController::generate($shipment->id);

            ShipmentsJourneyController::add($shipment->id, 1, 1, NULL, 'Shipment has been Reverted', session('user_id'), NULL);
            return ['status' => 0, 'success' => 'Shipment(s) has been Reverted'];
        }
        else{
            return ['status' => 1, 'error' => 'Warehouse Shipment can not be reverted from Sonic!'];
        }

    }

    public function cancelled_shipments_arrival_index()
    {
        $shipper = CancelledShipmentArrival::where('shipper_id',auth()->id())->first();
        return view('client.cancelled_shipments_arrival.cancelled_shipments_arrival')->with(['shipper' => $shipper]);;
    }
    public function cancelled_shipments_arrival(Request $request)
    {
        $id = $request->id;
        $shipper = auth()->id();

        if($id == 0)
        {
            $shipper = CancelledShipmentArrival::where('shipper_id',auth()->id());
            if ((!$shipper->exists()))
            {
//                dd('hi');
                $cancel_arrival_shippers = new CancelledShipmentArrival();
                $cancel_arrival_shippers->shipper_id = auth()->id();
                $cancel_arrival_shippers->save();

                return response()->json(['status' => '1', 'success' => 'Updated']);
            }
        }
        else
        {
            CancelledShipmentArrival::where('shipper_id',$shipper)->delete();

            return response()->json(['status' => '1', 'success' => 'Updated']);
        }
    }
}
