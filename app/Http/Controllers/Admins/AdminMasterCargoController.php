<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentOpenBoxJourneyController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\MasterCargo\Bag;
use App\Http\Models\Admin\MasterCargo\BagShipment;
use App\Http\Models\Admin\MasterCargo\BagStatus;
use App\Http\Models\Admin\MasterCargo\MasterCargo;
use App\Http\Models\Admin\MasterCargo\MasterCargoBag;
use App\Http\Models\Admin\MasterCargo\MasterCargoBagExcel;
use App\Http\Models\Admin\MasterCargo\MasterCargoExcel;
use App\Http\Models\Admin\MasterCargo\MasterCargoJunctionReceival;
use App\Http\Models\Admin\MasterCargo\MasterCargoStatus;
use App\Http\Models\Admin\ShipmentOnHold;
use App\Http\Models\BookingType;
use App\Http\Models\CargoConsignmentStatus;
use App\Http\Models\City;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\JunctionMapping;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\SelfCollectionShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShippingMode;
use App\Http\Models\TransportMode;
use App\Http\Models\TransportModeVendor;
use App\Http\Models\WarehouseStockRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Fleet;
use App\Http\Models\Admin\RouteManagement;
use App\Http\Models\Admin\RouteManagementJunction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\Datatables\Datatables;
use SnappyPDF;

class AdminMasterCargoController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function pending_index() {
        return redirect()->route('admin.access_denied');
        ActivityTrailController::createActivityTrailLog(Auth::id(),297);
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $service_type = BookingType::all();
        $shipping_mode = ShippingMode::all();
        return view('admin.master_cargo.bag.pending')->with(['shipment_status'=>$shipment_status,'service_type'=>$service_type,'shipping_mode'=>$shipping_mode]);
    }

    public function pending_list(Request $request) {

        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),298);
        }
        $today = Carbon::today();
        $on_hold_shipments = ShipmentOnHold::whereDate('dispatch_date', '>', $today)->where('status', 1)->pluck('shipment_id')->toArray();
        $shipments = Shipment::leftJoin('booking_types as bt', 'shipments.booking_type_id', '=', 'bt.id')
            ->join('shipment_status as ss', 'shipments.shipper_status_id', '=', 'ss.id')
            ->join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
            ->leftJoin('shipping_modes as sm', 'shipments.shipping_mode_id', '=', 'sm.id')
//            ->leftjoin('user_shipping_infos as rsi', 'shipments.return_address_id', '=', 'rsi.id')
            ->leftjoin('user_shipping_infos as rsi', function ($join) {
                $join->on('shipments.return_address_id', '=', 'rsi.id')
                    ->whereNotNull('shipments.return_address_id')
                    ->where('shipments.shipper_status_id', '!=', 30);
            })
            ->leftjoin('cities as rc', 'rsi.city_id', '=', 'rc.id')
            ->join('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->on('shipments_journey.shipper_status_id', '=', DB::raw(2));
            })
            ->leftJoin('shipments_journey as csj', function ($join) {
                $join->on('csj.shipment_id', '=', 'shipments.id')
                    ->where('csj.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftjoin('misrouted_history as mh', function ($join) {
                $join->on('mh.shipment_id', '=', 'shipments.id')
                    ->on('shipments.shipper_status_id', '=', DB::raw(49))
                    ->where('mh.id', '=',
                        DB::raw('(select max(id) from misrouted_history where misrouted_history.shipment_id = shipments.id)'));
            })
            ->leftjoin('cities as olddc', 'olddc.id', '=', 'mh.old_consignee_city_id')
            ->leftjoin('intercept_re_book_request_histories as irbrh', function ($join) {
                $join->on('irbrh.shipment_id', '=', 'shipments.id')
                    ->on('shipments.shipper_status_id', '=', DB::raw(55));
            })
            ->leftjoin('cities as olddci', 'olddci.id', '=', 'irbrh.old_consignee_city_id')
            ->join('cities as dc', function($join) {
                $join->on('shipments.consignee_city_id', '=', 'dc.id')
                    ->where(function ($query) {
                        $query->where(function ($sub_query) {
                            $sub_query->where('shipments.shipper_status_id', 20)
                                ->where(function ($sub_sub_query) {
                                    $sub_sub_query->whereNull('shipments.return_address_id')
                                        ->where('oc.hub_id', '!=', DB::raw('dc.hub_id'));
                                })
                                ->orWhere(function ($sub_sub_query) {
                                    $sub_sub_query->whereNotNull('shipments.return_address_id')
                                        ->where('rc.hub_id', '!=', DB::raw('dc.hub_id'));
                                });
                            })
                            ->orWhere(function ($sub_query) {
                                $sub_query->whereIn('shipments.shipper_status_id', [2, 30, 37])
                                    ->where('oc.hub_id', '!=', DB::raw('dc.hub_id'));
                            })
                            ->orWhere(function ($sub_query) {
                                $sub_query->where('shipments.shipper_status_id', '=', 49)
                                    ->where('mh.old_consignee_city_id', '!=', DB::raw('dc.hub_id'));
                            })
                            ->orWhere(function ($sub_query) {
                                $sub_query->where('shipments.shipper_status_id', '=', 55)
                                    ->where('irbrh.old_consignee_city_id', '!=', DB::raw('dc.hub_id'));
                            });
                    });
            })
            ->leftjoin('crm_requests as crm', function ($join) {
                $join->on('crm.shipment_id', '=', 'shipments.id')
                    ->whereIn('crm.status_id', [2, 3, 5])
                    ->where('crm.case_nature_id', 1);
            })

            ->select('shipments.shipper_status_id', 'shipments.tracking_number', 'shipments.tracking_number as tracking', 'shipments.order_id', 'bt.booking_type as service_type', 'ss.name as status', 'oc.name as origin', 'dc.name as destination', 'u.name as shipper', 'shipments.amount', 'sm.mode as shipping_mode', 'shipments.created_at as booked_at', 'shipments_journey.created_at as arrival_at', 'shipments.booking_type_id', 'usi.poc','csj.created_at as current_status', 'olddc.name as old_destination', 'olddci.name as old_destination_intercept','crm.id as complaint', 'shipments.return_address_id','rc.name as return_city_name')->whereNotIn('shipments.id', $on_hold_shipments);

        if (session('role_id') != 1) {
            $shipments = $shipments->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->whereIn('shipments.shipper_status_id', [20, 30, 37])
                        ->whereIn('dc.hub_id', session('hubs'));
                })
                    ->orWhere(function ($sub_query) {
                        $sub_query->where('shipments.shipper_status_id', 2)
                            ->whereIn('oc.hub_id', session('hubs'));
                    })
                    ->orWhere(function ($sub_query) {
                        $sub_query->where('shipments.shipper_status_id', 49)
                            ->whereIn('olddc.hub_id', session('hubs'));
                    })
                    ->orWhere(function ($sub_query) {
                        $sub_query->where('shipments.shipper_status_id', 55)
                            ->whereIn('olddci.hub_id', session('hubs'));
                    });
            });
        }

        $datatables = Datatables::of($shipments)
            ->setRowAttr([
                'class' => function ($shipments) {
                    if ($shipments->complaint != null) {
                        return 'complaint_row';
                    }
                    else if($shipments->booking_type_id == 3){
                        return "tnb_row";
                    } else {
                        return '';
                    }
                },
            ])
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('origin', function ($shipments) {
                if (in_array($shipments->shipper_status_id, [20, 30, 37])) {
                    return $shipments->destination;
                }
                else if ($shipments->shipper_status_id == 49) {
                    return $shipments->old_destination;
                }
                else if ($shipments->shipper_status_id == 55) {
                    return $shipments->old_destination_intercept;
                }
                else {
                    return $shipments->origin;
                }
            })
            ->editColumn('destination', function ($shipments) {
                if (in_array($shipments->shipper_status_id, [30, 37])) {
                    return $shipments->origin;
                }
                else if($shipments->shipper_status_id == 20){
                    if($shipments->return_address_id != NULL){
                        return $shipments->return_city_name;
                    }
                    else{
                        return $shipments->origin;
                    }
                }
                else {
                    return $shipments->destination;
                }
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
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->orderColumn('u.name', 'u.name $1, usi.poc $1')
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('service_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('bt.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('shipping_mode',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('sm.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('oc.name', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->whereIn('shipments.shipper_status_id', [20, 30, 37])
                        ->where('dc.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.shipper_status_id', 2)
                            ->where('oc.name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.shipper_status_id', 49)
                            ->where('olddc.name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.shipper_status_id', 55)
                            ->where('olddci.name', 'like', '%' . $keyword . '%');
                    });
            })
            ->filterColumn('dc.name', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                $query->where(function ($sub_query) use ($keyword) {

                    $sub_query->whereIn('shipments.shipper_status_id', [30, 37])
                        ->where('oc.name', 'like', '%' . $keyword . '%');
                })
                ->orWhere(function ($sub_query) use ($keyword) {

                    $sub_query->where(function ($sub_query) use ($keyword) {

                        $sub_query->where('shipments.shipper_status_id', 20)
                            ->where(function ($sub_sub_query) use ($keyword) {
                                $sub_sub_query->whereNull('shipments.return_address_id')
                                    ->where('oc.name', 'like', '%' . $keyword . '%');
                            })
                            ->orWhere(function ($sub_sub_query) use ($keyword) {
                                $sub_sub_query->whereNotNull('shipments.return_address_id')
                                    ->where('rc.name', 'like', '%' . $keyword . '%');
                            });

                    });
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->whereIn('shipments.shipper_status_id', [2, 49, 55])
                            ->where('dc.name', 'like', '%' . $keyword . '%');
                    });
            })
            ->orderColumn('oc.name', DB::raw('IF (shipments.shipper_status_id IN (20, 30, 37), dc.name, IF (shipments.shipper_status_id = 49, olddc.name, IF (shipments.shipper_status_id = 55, olddci.name, oc.name)))') . ' $1')
            ->orderColumn('dc.name', DB::raw('IF (shipments.shipper_status_id IN (20, 30, 37), oc.name, dc.name)') . ' $1');

        if ($shipment_type = $request->get('shipment_type')) {
            if ($shipment_type == 0) {
                $datatables->whereIn('shipments.shipper_status_id', [2, 20, 30, 37, 49, 55]);
            }
            else if ($shipment_type == 1) {
                $datatables->whereIn('shipments.shipper_status_id', [2, 49, 55]);
            }
            else if ($shipment_type == 2) {
                $datatables->whereIn('shipments.shipper_status_id', [20, 30, 37]);
            }
        }
        else {
            $datatables->whereIn('shipments.shipper_status_id', [2, 20, 30, 37, 49, 55]);
        }
        if($mode = $request->get('search_shipping_mode')){
            $datatables->where('sm.id', '=', $mode);
        }

        return $datatables->make(true);
    }

    public function create_index() {
        return redirect()->route('admin.access_denied');
//        return view('admin.master_cargo.bag.create')->with('print', session('print'));
    }

    public function create_shipment_details(Request $request) {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $on_hold_shipment = ShipmentOnHold::where('shipment_id', $shipment->id)->where('status', 1);
            if($on_hold_shipment->exists()){
                if(!in_array(Auth::id(), [10, 288, 423])){
                    $on_hold_shipment = $on_hold_shipment->first();
                    $dispatch_date = Carbon::parse($on_hold_shipment->dispatch_date);
                    $today = Carbon::today();
                    if($dispatch_date > $today){
                        $dispatch_date = $dispatch_date->toFormattedDateString();
                        return ['status' => 1, 'error' => 'Shipment is marked as On-Hold until ' . $dispatch_date];
                    }
                }
            }
            if($shipment->packaging_material_request == 1){


                $packaging_material_request = PackagingMaterialRequest::where('tracking_number',$shipment->tracking_number)->first();

                if($packaging_material_request != null){
                    if($packaging_material_request->status_id != 3){
                        return ['status' => 1, 'error' => 'Packaging Material Request is not dispatched yet!'];
                    }
                }
                $packaging_material_request_stock = WarehouseStockRequest::where('tracking_number',$shipment->tracking_number)->first();
                if($packaging_material_request_stock != null){
                    if($packaging_material_request_stock->status_id != 3){
                        return ['status' => 1, 'error' => 'Warehouse Stock Request is not dispatched yet!'];
                    }
                }
            }


            if (in_array($shipment->shipper_status_id, [2, 20, 30, 37, 49, 55])) {
                if ($shipment->shipper_status_id == 2) {
                    $hub_id = $shipment->pickup_address->city->hub_id;
                }
                else if ($shipment->shipper_status_id == 49) {
                    $shipment_details = $shipment->misrouted_history()->latest()->first();
                    $city_details = City::find($shipment_details->old_consignee_city_id);
                    $hub_id = $city_details->hub_id;
                }
                else if ($shipment->shipper_status_id == 55) {
                    $shipment_details = $shipment->intercept_history;
                    $city_details = City::find($shipment_details->old_consignee_city_id);
                    $hub_id = $city_details->hub_id;
                }
                else {
                    $hub_id = $shipment->consignee_city->hub_id;

                }

                $allowed = FALSE;

                if (session('role_id') == 1) {
                    $allowed = TRUE;
                }
                else if (in_array($hub_id, session('hubs'))) {
                    $allowed = TRUE;
                }
                else if ($shipment->shipper_status_id == 49 && in_array($shipment->pickup_address->city->hub_id, session('hubs'))) {
                    $allowed = TRUE;
                }

                if ($allowed) {
                    if (($shipment->pickup_address->city->hub_id != $shipment->consignee_city->hub_id) || (in_array($shipment->shipper_status_id, [49, 55]) && ($shipment->consignee_city->hub_id != $hub_id)) || ($shipment->shipper_status_id == 20)) {
                        if($shipment->return_address_id != NULL){
                            if(($shipment->shipper_status_id == 20) && ($shipment->consignee_city->hub_id == $shipment->return_address->city->hub_id)){
                                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to same Origin and Destination Hub'];
                            }
                        }

                        if ($request->bag_type != 0) {
                            if (in_array($shipment->shipper_status_id, [2, 49, 55])) {
                                $hub_id = $shipment->consignee_city->hub_id;
                            }
                            else {
                                if(in_array($shipment->shipper_status_id, [20])){
                                    if($shipment->return_address_id != null){
                                        $hub_id = $shipment->return_address->city->hub_id;
                                    }
                                    else{
                                        $hub_id = $shipment->pickup_address->city->hub_id;
                                    }
                                }
                                else{
                                    $hub_id = $shipment->pickup_address->city->hub_id;
                                }
                            }
                        }
                        else {
                            $hub_id = 0;
                        }

                        if ($request->hub_id == 0 || $request->hub_id == $hub_id) {
//                            if ($request->shipping_mode_id == 0 || $request->shipping_mode_id == $shipment->shipping_mode->id) {
                            $details = array();

                            if ($request->bag_type != 0) {
                                if ($request->bag_type == 1) {
                                    if (!in_array($shipment->shipper_status_id, [2, 49, 55])) {
                                        return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of Return Type while the Cargo is Normal Type'];
                                    }

                                    $bag_type = 1;
                                }
                                else {
                                    if (!in_array($shipment->shipper_status_id, [20, 30, 37])) {
                                        return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of Normal Type while the Cargo is Return Type'];
                                    }

                                    $bag_type = 2;
                                }
                            }
                            else {
                                if (in_array($shipment->shipper_status_id, [2, 49, 55])) {
                                    $details['bag_type'] = 1;

                                    $bag_type = 1;
                                }
                                else {
                                    $details['bag_type'] = 2;

                                    $bag_type = 2;
                                }
                            }

                            if ($bag_type == 1) {
                                $destination = $shipment->consignee_city;
                            }
                            else {
                                if($shipment->return_address_id != NULL){
                                    $destination = $shipment->return_address->city;
                                }
                                else{
                                    $destination = $shipment->pickup_address->city;
                                }

                            }
                            if(!$request->has('pieces_confirm')){
                                if($shipment->booking_type_id == 1 && $shipment->pieces > 1){
                                    $details = array();
                                    $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                    $details['id'] = $shipment->id;
                                    $details['tracking_number'] = $shipment->tracking_number;
                                    $details['pieces_count'] = $shipment->pieces;
                                    $details['pieces_tracking_numbers'] = $shipment_pieces;
                                    return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                                }
                            }
                            $details['id'] = $shipment->id;
                            $details['tracking_number'] = $shipment->tracking_number;
                            $details['order_id'] = $shipment->order_id;
                            $details['service_type'] = $shipment->booking_type->booking_type;
                            $details['destination'] = $destination->name;
                            $details['amount'] = number_format($shipment->amount);
                            $hub = $destination->hub_city;

                            $details['hub']['id'] = $hub->id;
                            $details['hub']['name'] = $hub->name;

                            $shipping_mode_id = $request->shipping_mode_id;

                            if ($shipping_mode_id == 0) {
                                $shipping_mode_id = $shipment->shipping_mode->id;

                                $details['shipping_mode']['id'] = $shipment->shipping_mode->id;
                                $details['shipping_mode']['name'] = $shipment->shipping_mode->mode;
                            }

                            if ($request->hub_id == 0) {
                                if ($bag_type == 1) {
                                    $shipments = Shipment::join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
                                        ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
                                        ->join('cities as dc', function($join) {
                                            $join->on('shipments.consignee_city_id', '=', 'dc.id')
                                                ->on('oc.hub_id', '!=', 'dc.hub_id');
                                        })
                                        ->select(DB::raw('count(shipments.id) as count'))
                                        ->where('dc.hub_id', $hub->id)
                                        ->whereIn('shipments.shipper_status_id', [2, 49, 55])
                                        ->where('shipments.shipping_mode_id', $shipping_mode_id);
                                }
                                else {

                                    $shipments = Shipment::leftjoin('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
                                        ->leftjoin('cities as dc', 'usi.city_id', '=', 'dc.id')
                                        ->leftjoin('user_shipping_infos as rsi', 'shipments.return_address_id', '=', 'rsi.id')
                                        ->leftjoin('cities as rc', 'rsi.city_id', '=', 'rc.id')
                                        ->join('cities as oc', function($join) use ($hub) {
                                            $join->on('shipments.consignee_city_id', '=', 'oc.id')
                                                ->where(function($query) use ($hub) {
                                                    $query->where(function($sub_query) use ($hub) {
                                                        $sub_query->whereIn('shipments.shipper_status_id', [30, 37])
                                                            ->where('dc.hub_id', '!=', 'oc.hub_id')
                                                            ->where('dc.hub_id', $hub->id);
                                                    })
                                                        ->orWhere(function($sub_query) use ($hub) {
                                                            $sub_query->where('shipments.shipper_status_id', 20)
                                                                ->where(function($sub_sub_query) use ($hub) {
                                                                    $sub_sub_query->where(function($sub_sub_sub_query) use ($hub) {
                                                                        $sub_sub_sub_query->whereNull('return_address_id')
                                                                            ->where('dc.hub_id', '!=', 'oc.hub_id')
                                                                            ->where('dc.hub_id', $hub->id);
                                                                    })
                                                                        ->orWhere(function($sub_sub_sub_query) use ($hub) {
                                                                            $sub_sub_sub_query->whereNotNull('return_address_id')
                                                                                ->where('rc.hub_id', '!=', 'oc.hub_id')
                                                                                ->where('rc.hub_id', $hub->id);
                                                                        });
                                                                });
                                                        });
                                                });
                                        })
                                        ->select(DB::raw('count(shipments.id) as count'))
                                        ->where('shipments.shipping_mode_id', $shipping_mode_id);

                                }

                                if (session('role_id') != 1) {
                                    $shipments = $shipments->whereIn('oc.hub_id', session('hubs'));
                                }

                                $shipments = $shipments->first();

                                $details['total'] = $shipments->count;
                            }
                            ShipmentScanningJourneyController::add($shipment->id ,2,1,Auth::id(),NULL,NULL,NULL,NULL, session('latitude'), session('longitude'), NULL);
                            return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
//                            }
//                            else {
//                                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment\'s Shipment Mode is different'];
//                            }
                        }
                        else {
                            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to another Hub'];
                        }
                    }
                    else {
                        return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to same Origin and Destination Hub'];
                    }
                }
                else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment does not belong to any of your assigned Hub\'s Cities'];
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

    public function bag_piece_details(Request $request){
        $shipment_id = $request->shipment_id;
        $shipment_piece_id = $request->piece_id;

        $shipment_piece = ShipmentPiece::where('tracking_number',$shipment_piece_id);
        if($shipment_piece->exists()){
            $shipment_piece = $shipment_piece->first();
            if($shipment_piece->shipment_id == $shipment_id){
                $scanned_shipment_piece = $shipment_piece->tracking_number;
                return ['status' => 0, 'success' => 'Shipment Piece found!', 'scanned_shipment_piece' => $scanned_shipment_piece];
            }
            else{
                return ['status' => 1, 'error' => 'Given Item ID does not belong here'];
            }

        }
        else{
            return ['status' => 1, 'error' => 'No Shipment Item with given Item ID is present'];
        }
    }

    public function create_bag_details(Request $request) {
        $shipment = Shipment::find(current($request->shipment_ids));

        if ($shipment->shipper_status_id == 49) {
            $shipment_details = $shipment->misrouted_history()->latest()->first();
            $city_details = City::find($shipment_details->old_consignee_city_id);
            $origin = $city_details->hub_city;
        }
        else if ($shipment->shipper_status_id == 55) {
            $shipment_details = $shipment->intercept_history;
            $city_details = City::find($shipment_details->old_consignee_city_id);
            $origin = $city_details->hub_city;
        }
        else {
            if($shipment->shipper_status_id == 20){
                if($shipment->return_address_id != null){
                    $origin = $shipment->return_address->city->hub_city;
                }
                else{
                    $origin = $shipment->pickup_address->city->hub_city;
                }
            }
            else{
                $origin = $shipment->pickup_address->city->hub_city;
            }
        }

        $origin_details = array();

        $origin_details['id'] = $origin->id;
        $origin_details['name'] = $origin->name;

        $destination = $shipment->consignee_city->hub_city;

        $destination_details = array();

        $destination_details['id'] = $destination->id;
        $destination_details['name'] = $destination->name;

        $details = array();

        $details['junctions'] = City::select(['id', 'name'])->where('business_category_id', 1)->where('hub', 1)->get();

        $details['transport_modes'] = TransportMode::all();

        $details['transport_mode_vendors'] = TransportModeVendor::get()->groupBy('transport_mode_id');

        $details['shipping_modes'] = ShippingMode::whereIn('id',[1,2])->get();

        $details['receivers'] = Admin::where('status', 1)->whereHas('hubs', function ($query) use($destination_details) {
            $query->where('hub_id',  $destination_details['id']);
        })->select(['id', 'name'])->get();

        if ($request->bag_type == 1) {
            $details['origin'] = $origin_details;
            $details['destination'] = $destination_details;
        }
        else {
            $details['origin'] = $destination_details;
            $details['destination'] = $origin_details;
        }
        $mapping = JunctionMapping::where(['origin_id' => $origin_details['id'], 'destination_id' => $destination_details['id']])->first();
        if($mapping){
            $details['junction_1'] = $mapping['junction_1'];
            $details['junction_2'] = $mapping['junction_2'];
            $details['receiver'] = $mapping['receiver'];
        }
        $details['actual_weight'] = 0;

        foreach ($request->shipment_ids as $shipment_id){
            $shipment_actual_weight = Shipment::find($shipment_id);
            $details['actual_weight'] = $details['actual_weight'] + $shipment_actual_weight->actual_weight;
        }

        return $details;
    }

    public function create_bag_seal_number(Request $request) {
        if ($request->filled('seal_number')) {
            $seal_number = Bag::where('seal_number', $request->input('seal_number'));

            if ($request->has('id')) {
                $seal_number = $seal_number->where('id', '!=', $request->input('id'));
            }

            if (!$seal_number->exists()) {
                return 'true';
            }
            else {
                return 'false';
            }
        }
        else {
            return 'false';
        }
    }

    public function create_store(Request $request) {
        $shipments = 0;
        $quantity = 0;
        $shipments_weight = 0;

        $shipment_ids = explode(',', $request->input('shipment_ids'));
        $open_box_ids = explode(',', $request->input('open_box_ids'));
        foreach ($shipment_ids as $key => $shipment_id) {
            $shipment = Shipment::find($shipment_id);

            if (in_array($shipment->shipper_status_id, [2, 20, 30, 37, 49, 55])) {
                $shipments++;
                $shipments_weight += $shipment->actual_weight;
                $quantity = $quantity + count($shipment->items);

            }
            else {
                unset($shipment_ids[$key]);
            }
        }

        if (!empty($shipment_ids)) {
            $bag = new Bag();

            $bag->origin_hub_id = $request->input('origin_hub_id');
            $bag->destination_hub_id = $request->input('destination_hub_id');
            $bag->junction_hub_1_id = $request->input('junction_1');
            $bag->junction_hub_2_id = $request->input('junction_2');
            $bag->seal_number = $request->input('seal_number');
            $bag->shipping_mode_id = $request->input('shipping_mode_id');
            $bag->transport_mode_id = $request->input('transport_mode');

            if ($request->input('transport_mode_vendor') == 0) {
                $transport_mode_vendor = new TransportModeVendor();

                $transport_mode_vendor->transport_mode_id = $request->input('transport_mode');
                $transport_mode_vendor->name = $request->input('vendor_name');

                $transport_mode_vendor->save();

                $bag->transport_mode_vendor_id = $transport_mode_vendor->id;
            }
            else {
                $bag->transport_mode_vendor_id = $request->input('transport_mode_vendor');
            }

            $bag->shipments = $shipments;
            $bag->quantity = $quantity;
            $bag->shipments_weight = $shipments_weight;

            $bag->actual_weight = $request->input('actual_weight');
            $bag->created_by = Auth::id();

            $bag->type = $request->input('bag_type');

            $bag->status_id = 1;

            $bag->save();

            MasterCargoBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), NULL, NULL);

            $id = $bag->id;

            foreach ($shipment_ids as $shipment_id) {
                $bag_shipment = new BagShipment();

                $bag_shipment->bag_id = $id;
                $bag_shipment->shipment_id = $shipment_id;

                $bag_shipment->save();

                $shipment = Shipment::find($shipment_id);

                $shipper_status_id = NULL;
                $consignee_status_id = NULL;

                if ($request->input('bag_type') == 1) {
                    $shipper_status_id = 3;
                    $consignee_status_id = 3;
                }
                else {
                    $shipper_status_id = 21;
                    $consignee_status_id = 21;

                    if ($shipment->shipper_status_id != 20) {
                        if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4 || $shipment->booking_type_id == 5) {
                            $shipper_status_id = 21;
                            $consignee_status_id = 21;
                        }
                        else if ($shipment->booking_type_id == 2) {
                            $shipper_status_id = 26;
                            $consignee_status_id = 26;
                        }
                        else if ($shipment->booking_type_id == 3) {
                            $shipper_status_id = 32;
                            $consignee_status_id = 32;
                        }
                        else {
                            $shipper_status_id = 21;
                            $consignee_status_id = 21;
                        }
                    }
                }

                $shipment->shipper_status_id = $shipper_status_id;
                $shipment->consignee_status_id = $consignee_status_id;

                $shipment->save();


                if(in_array($shipment_id, $open_box_ids)){
                    $shipment->open_box = 1;
                    $shipment->save();
                    ShipmentOpenBoxJourneyController::add($shipment_id,1,Auth::id());
                }

                ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $consignee_status_id, NULL, NULL, NULL, Auth::id(), $bag->id);
            }

            return redirect()->route('admin.master_cargo.bag.create.index')->with(['success' => 'Bag Created with Bag Number: ' . $bag->seal_number]);
        }
        else {
            return back()->withErrors('All Shipments have already been added to another Bag!');
        }
    }

    public function create_open_bag_index() {
        return redirect()->route('admin.access_denied');
//        return view('admin.master_cargo.bag.create_open_bag')->with('print', session('print'));
    }

    public function create_open_bag_shipment_details(Request $request) {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $bag = Bag::where('seal_number', $shipment->tracking_number);
            if(!$bag->exists()){
                $on_hold_shipment = ShipmentOnHold::where('shipment_id', $shipment->id)->where('status', 1);
                if($on_hold_shipment->exists()){
                    if(!in_array(Auth::id(), [10, 288, 423])){
                        $on_hold_shipment = $on_hold_shipment->first();
                        $dispatch_date = Carbon::parse($on_hold_shipment->dispatch_date);
                        $today = Carbon::today();
                        if($dispatch_date > $today){
                            $dispatch_date = $dispatch_date->toFormattedDateString();
                            return ['status' => 1, 'error' => 'Shipment is marked as On-Hold until ' . $dispatch_date];
                        }
                    }
                }
                if($shipment->packaging_material_request == 1){


                    $packaging_material_request = PackagingMaterialRequest::where('tracking_number',$shipment->tracking_number)->first();

                    if($packaging_material_request != null){
                        if($packaging_material_request->status_id != 3){
                            return ['status' => 1, 'error' => 'Packaging Material Request is not dispatched yet!'];
                        }
                    }
                    $packaging_material_request_stock = WarehouseStockRequest::where('tracking_number',$shipment->tracking_number)->first();
                    if($packaging_material_request_stock != null){
                        if($packaging_material_request_stock->status_id != 3){
                            return ['status' => 1, 'error' => 'Warehouse Stock Request is not dispatched yet!'];
                        }
                    }
                }


                if (in_array($shipment->shipper_status_id, [2, 20, 30, 37, 49, 55])) {
                    if ($shipment->shipper_status_id == 2) {
                        $hub_id = $shipment->pickup_address->city->hub_id;
                    }
                    else if ($shipment->shipper_status_id == 49) {
                        $shipment_details = $shipment->misrouted_history()->latest()->first();
                        $city_details = City::find($shipment_details->old_consignee_city_id);
                        $hub_id = $city_details->hub_id;
                    }
                    else if ($shipment->shipper_status_id == 55) {
                        $shipment_details = $shipment->intercept_history;
                        $city_details = City::find($shipment_details->old_consignee_city_id);
                        $hub_id = $city_details->hub_id;
                    }
                    else {

                        $hub_id = $shipment->consignee_city->hub_id;

                    }

                    $allowed = FALSE;

                    if (session('role_id') == 1) {
                        $allowed = TRUE;
                    }
                    else if (in_array($hub_id, session('hubs'))) {
                        $allowed = TRUE;
                    }
                    else if ($shipment->shipper_status_id == 49 && in_array($shipment->pickup_address->city->hub_id, session('hubs'))) {
                        $allowed = TRUE;
                    }

                    if ($allowed) {
                        if (($shipment->pickup_address->city->hub_id != $shipment->consignee_city->hub_id) || (in_array($shipment->shipper_status_id, [49, 55]) && ($shipment->consignee_city->hub_id != $hub_id)) || ($shipment->shipper_status_id == 20)) {
                            if($shipment->return_address_id != NULL){
                                if(($shipment->shipper_status_id == 20) && ($shipment->consignee_city->hub_id == $shipment->return_address->city->hub_id)){
                                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to same Origin and Destination Hub'];
                                }
                            }
                            if ($request->bag_type != 0) {
                                if (in_array($shipment->shipper_status_id, [2, 49, 55])) {
                                    $hub_id = $shipment->consignee_city->hub_id;
                                }
                                else {
                                    if(in_array($shipment->shipper_status_id, [20])){
                                        if($shipment->return_address_id != null){
                                            $hub_id = $shipment->return_address->city->hub_id;
                                        }
                                        else{
                                            $hub_id = $shipment->pickup_address->city->hub_id;
                                        }
                                    }
                                    else{
                                        $hub_id = $shipment->pickup_address->city->hub_id;
                                    }
                                }
                            }
                            else {
                                $hub_id = 0;
                            }

                            if ($request->hub_id == 0 || $request->hub_id == $hub_id) {
//                            if ($request->shipping_mode_id == 0 || $request->shipping_mode_id == $shipment->shipping_mode->id) {
                                $details = array();

                                if ($request->bag_type != 0) {
                                    if ($request->bag_type == 1) {
                                        if (!in_array($shipment->shipper_status_id, [2, 49, 55])) {
                                            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of Return Type while the Cargo is Normal Type'];
                                        }

                                        $bag_type = 1;
                                    }
                                    else {
                                        if (!in_array($shipment->shipper_status_id, [20, 30, 37])) {
                                            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of Normal Type while the Cargo is Return Type'];
                                        }

                                        $bag_type = 2;
                                    }
                                }
                                else {
                                    if (in_array($shipment->shipper_status_id, [2, 49, 55])) {
                                        $details['bag_type'] = 1;

                                        $bag_type = 1;
                                    }
                                    else {
                                        $details['bag_type'] = 2;

                                        $bag_type = 2;
                                    }
                                }

                                if ($bag_type == 1) {
                                    $destination = $shipment->consignee_city;
                                }
                                else {
                                    if($shipment->return_address_id != NULL){
                                        $destination = $shipment->return_address->city;
                                    }
                                    else{
                                        $destination = $shipment->pickup_address->city;
                                    }
                                }
                                if(!$request->has('pieces_confirm')){
                                    if($shipment->booking_type_id == 1 && $shipment->pieces > 1){
                                        $details = array();
                                        $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                        $details['id'] = $shipment->id;
                                        $details['tracking_number'] = $shipment->tracking_number;
                                        $details['pieces_count'] = $shipment->pieces;
                                        $details['pieces_tracking_numbers'] = $shipment_pieces;
                                        return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                                    }
                                }
                                $details['id'] = $shipment->id;
                                $details['tracking_number'] = $shipment->tracking_number;
                                $details['order_id'] = $shipment->order_id;
                                $details['service_type'] = $shipment->booking_type->booking_type;
                                $details['destination'] = $destination->name;
                                $details['amount'] = number_format($shipment->amount);
                                $hub = $destination->hub_city;

                                $details['hub']['id'] = $hub->id;
                                $details['hub']['name'] = $hub->name;

                                $shipping_mode_id = $request->shipping_mode_id;

                                if ($shipping_mode_id == 0) {
                                    $shipping_mode_id = $shipment->shipping_mode->id;

                                    $details['shipping_mode']['id'] = $shipment->shipping_mode->id;
                                    $details['shipping_mode']['name'] = $shipment->shipping_mode->mode;
                                }

                                if ($request->hub_id == 0) {
                                    if ($bag_type == 1) {
                                        $shipments = Shipment::join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
                                            ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
                                            ->join('cities as dc', function($join) {
                                                $join->on('shipments.consignee_city_id', '=', 'dc.id')
                                                    ->on('oc.hub_id', '!=', 'dc.hub_id');
                                            })
                                            ->select(DB::raw('count(shipments.id) as count'))
                                            ->where('dc.hub_id', $hub->id)
                                            ->whereIn('shipments.shipper_status_id', [2, 49, 55])
                                            ->where('shipments.shipping_mode_id', $shipping_mode_id);
                                    }
                                    else {
                                        $shipments = Shipment::leftjoin('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
                                            ->leftjoin('cities as dc', 'usi.city_id', '=', 'dc.id')
                                            ->leftjoin('user_shipping_infos as rsi', 'shipments.return_address_id', '=', 'rsi.id')
                                            ->leftjoin('cities as rc', 'rsi.city_id', '=', 'rc.id')
                                            ->join('cities as oc', function($join) use ($hub) {
                                                $join->on('shipments.consignee_city_id', '=', 'oc.id')
                                                    ->where(function($query) use ($hub) {
                                                        $query->where(function($sub_query) use ($hub) {
                                                            $sub_query->whereIn('shipments.shipper_status_id', [30, 37])
                                                                ->where('dc.hub_id', '!=', 'oc.hub_id')
                                                                ->where('dc.hub_id', $hub->id);
                                                        })
                                                            ->orWhere(function($sub_query) use ($hub) {
                                                                $sub_query->where('shipments.shipper_status_id', 20)
                                                                    ->where(function($sub_sub_query) use ($hub) {
                                                                        $sub_sub_query->where(function($sub_sub_sub_query) use ($hub) {
                                                                            $sub_sub_sub_query->whereNull('return_address_id')
                                                                                ->where('dc.hub_id', '!=', 'oc.hub_id')
                                                                                ->where('dc.hub_id', $hub->id);
                                                                        })
                                                                            ->orWhere(function($sub_sub_sub_query) use ($hub) {
                                                                                $sub_sub_sub_query->whereNotNull('return_address_id')
                                                                                    ->where('rc.hub_id', '!=', 'oc.hub_id')
                                                                                    ->where('rc.hub_id', $hub->id);
                                                                            });
                                                                    });
                                                            });
                                                    });
                                            })
                                            ->select(DB::raw('count(shipments.id) as count'))
                                            ->where('shipments.shipping_mode_id', $shipping_mode_id);
                                    }

                                    if (session('role_id') != 1) {
                                        $shipments = $shipments->whereIn('oc.hub_id', session('hubs'));
                                    }

                                    $shipments = $shipments->first();

                                    $details['total'] = $shipments->count;
                                }
                                ShipmentScanningJourneyController::add($shipment->id ,2,1,Auth::id(),NULL,NULL,NULL,NULL, session('latitude'), session('longitude'), NULL);
                                return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
//                            }
//                            else {
//                                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment\'s Shipment Mode is different'];
//                            }
                            }
                            else {
                                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to another Hub'];
                            }
                        }
                        else {
                            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to same Origin and Destination Hub'];
                        }
                    }
                    else {
                        return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment does not belong to any of your assigned Hub\'s Cities'];
                    }
                }
                else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
                }
            }
            else{
                return ['status' => 1, 'error' => 'Same Tracking Number is already used as Bag Seal Number'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }

    public function create_open_bag_store(Request $request) {
        $bag_numbers = '';
        $shipment_ids = explode(',', $request->input('shipment_ids'));
        $open_box_ids = explode(',', $request->input('open_box_ids'));
        foreach ($shipment_ids as $key => $shipment_id) {
            $shipment = Shipment::find($shipment_id);

            if (!in_array($shipment->shipper_status_id, [2, 20, 30, 37, 49, 55])) {
                unset($shipment_ids[$key]);
            }
            $bag = Bag::where('seal_number', $shipment->seal_number);
            if($bag->exists()){
                unset($shipment_ids[$key]);
            }
        }

        if (!empty($shipment_ids)) {
            foreach ($shipment_ids as $shipment_id) {
                $shipment = Shipment::find($shipment_id);
                $bag = new Bag();

                $bag->origin_hub_id = $request->input('origin_hub_id');
                $bag->destination_hub_id = $request->input('destination_hub_id');
                $bag->junction_hub_1_id = $request->input('junction_1');
                $bag->junction_hub_2_id = $request->input('junction_2');
                $bag->seal_number = $shipment->tracking_number;
                $bag->shipping_mode_id = $request->input('shipping_mode_id');
                $bag->transport_mode_id = $request->input('transport_mode');

                if ($request->input('transport_mode_vendor') == 0) {
                    $transport_mode_vendor = new TransportModeVendor();

                    $transport_mode_vendor->transport_mode_id = $request->input('transport_mode');
                    $transport_mode_vendor->name = $request->input('vendor_name');

                    $transport_mode_vendor->save();

                    $bag->transport_mode_vendor_id = $transport_mode_vendor->id;
                }
                else {
                    $bag->transport_mode_vendor_id = $request->input('transport_mode_vendor');
                }

                $bag->shipments = 1;
                $bag->quantity = count($shipment->items);
                $bag->shipments_weight = $shipment->actual_weight;

                $bag->actual_weight = $shipment->actual_weight;
                $bag->created_by = Auth::id();

                $bag->type = $request->input('bag_type');

                $bag->status_id = 1;

                $bag->save();

                MasterCargoBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), NULL, NULL);

                $id = $bag->id;

                $bag_shipment = new BagShipment();

                $bag_shipment->bag_id = $id;
                $bag_shipment->shipment_id = $shipment_id;

                $bag_shipment->save();

                $shipper_status_id = NULL;
                $consignee_status_id = NULL;

                if ($request->input('bag_type') == 1) {
                    $shipper_status_id = 3;
                    $consignee_status_id = 3;
                }
                else {
                    $shipper_status_id = 21;
                    $consignee_status_id = 21;

                    if ($shipment->shipper_status_id != 20) {
                        if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4 || $shipment->booking_type_id == 5) {
                            $shipper_status_id = 21;
                            $consignee_status_id = 21;
                        }
                        else if ($shipment->booking_type_id == 2) {
                            $shipper_status_id = 26;
                            $consignee_status_id = 26;
                        }
                        else if ($shipment->booking_type_id == 3) {
                            $shipper_status_id = 32;
                            $consignee_status_id = 32;
                        }
                        else {
                            $shipper_status_id = 21;
                            $consignee_status_id = 21;
                        }
                    }
                }

                $shipment->shipper_status_id = $shipper_status_id;
                $shipment->consignee_status_id = $consignee_status_id;

                $shipment->save();


                if(in_array($shipment_id, $open_box_ids)){
                    $shipment->open_box = 1;
                    $shipment->save();
                    ShipmentOpenBoxJourneyController::add($shipment_id,1,Auth::id());
                }

                ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $consignee_status_id, NULL, NULL, NULL, Auth::id(), $bag->id);

                if($bag_numbers == ''){
                    $bag_numbers = $bag->seal_number;
                }
                else{
                    $bag_numbers = $bag_numbers . ', ' . $bag->seal_number;
                }
            }

            return redirect()->route('admin.master_cargo.bag.create.open_bag.index')->with(['success' => 'Open Bag Created with Bag Numbers: ' . $bag_numbers]);
        }
        else {
            return back()->withErrors('All Shipments have already been added to another Bag!');
        }
    }

    public  function history_index(){
        return redirect()->route('admin.access_denied');
        $shipping_mode = ShippingMode::all();
        $bag_statuses = BagStatus::all();
        $transport_vendor = TransportModeVendor::all();
        $transport_mode = TransportMode::all();
        return view('admin.master_cargo.bag.history')->with(['shipping_mode'=>$shipping_mode,'bag_statuses'=>$bag_statuses,'transport_mode'=>$transport_mode,'transport_vendor'=>$transport_vendor]);
    }

    public function history_list(Request $request) {

        $bags = Bag::join('cities as oh', 'bags.origin_hub_id', '=', 'oh.id')
            ->join('cities as dh', 'bags.destination_hub_id', '=', 'dh.id')
            ->leftJoin('shipping_modes as sm', 'bags.shipping_mode_id', '=', 'sm.id')
            ->join('admins as a', 'bags.created_by', '=', 'a.id')
            ->leftjoin('cities as jh1', 'bags.junction_hub_1_id', '=', 'jh1.id')
            ->join('bag_statuses as bs', 'bags.status_id', '=', 'bs.id')
            ->leftjoin('cities as jh2', 'bags.junction_hub_2_id', '=', 'jh2.id')
            ->join('transport_modes as tm', 'bags.transport_mode_id', '=', 'tm.id')
            ->join('transport_mode_vendors as tmv', 'bags.transport_mode_vendor_id', '=', 'tmv.id')
            ->select('bags.id', 'bags.status_id', 'oh.id as origin_id', 'oh.name as origin', 'dh.id as destination_id', 'dh.name as destination', 'bags.shipments', 'sm.mode as shipping_mode', 'jh1.name as junction_1', 'jh2.name as junction_2', 'tm.name as transport_mode', 'tmv.name as vendor', 'bags.shipments_weight', DB::raw('(SELECT SUM(`s`.`chargeable_weight`) FROM `shipments` AS `s` INNER JOIN `bag_shipments` AS `bss` ON `s`.`id` = `bss`.`shipment_id` WHERE `bss`.`bag_id` = `bags`.`id`) AS `chargeable_weight`'), 'bags.actual_weight', 'bags.created_at as transit_at', 'a.name as transitted_by', 'oh.hub_id as origin_hub_id', 'dh.hub_id as destination_hub_id', 'bags.type as bag_type','bags.seal_number', 'bs.name as status', 'bags.short_received');

        if (session('role_id') != 1) {
            $bags = $bags->where(function ($query) {
                $query->whereIn('oh.hub_id', session('hubs'))->orWhereIn('dh.hub_id', session('hubs'))->orWhereIn('bags.junction_hub_1_id', session('hubs'))->orWhereIn('bags.junction_hub_1_id', session('hubs'));
            });
        }

        $datatables = Datatables::of($bags)
            ->addColumn('aging',function ($bag){

                $days = Carbon::now()->diffInDays($bag->transit_at);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            })
            ->addColumn('shipments_count', function ($bag) {
                return $bag->shipments;
            })
            ->addColumn('short_received_shipments', function ($master_cargo) {
                if($master_cargo->short_received > 0){
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $master_cargo->short_received . '</button>';
                }
                else{
                    return '-';
                }
            })
            ->editColumn('bag_type',function ($bag){
                if($bag->bag_type == 1){
                    return 'Normal';
                }else{
                    return 'Return';
                }
            })
            ->addColumn('shipments', function ($bag) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $bag->shipments . '</button>';
            })
            ->filterColumn('bags.seal_number', function ($query, $keyword) {
                return $query->where('bags.seal_number', '=', $keyword);
            })
            ->filterColumn('shipping_mode',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('sm.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('status',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where('bs.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            });

        if ($bag_type = $request->get('bag_type')) {
            if ($bag_type != 0) {
                $datatables->where('bags.type', $bag_type);
            }
        }

        if ($tracking_number = $request->get('tracking_number')) {
            dd("asd2");
            $datatables->join('bag_shipments as bssh', 'bags.id', '=', 'bssh.bag_id')
                ->join('shipments as s', 'bssh.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
        }

        if ($bag_number = $request->get('bag_number')) {
            dd("asd");
            $datatables->where('bag_number', '=', $bag_number);

        }

        return $datatables->make(true);
    }

    public function master_cargo_pending_index() {
        return redirect()->route('admin.access_denied');
        ActivityTrailController::createActivityTrailLog(Auth::id(),299);
        $shipping_mode = ShippingMode::all();
        $transport_vendor = TransportModeVendor::all();
        $transport_mode = TransportMode::all();
        $bag_statuses = BagStatus::all();
        return view('admin.master_cargo.pending')->with(['shipping_mode'=>$shipping_mode,'transport_mode'=>$transport_mode,'transport_vendor'=>$transport_vendor,'bag_statuses'=>$bag_statuses]);
    }

    public function master_cargo_pending_list(Request $request) {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),300);
        }
        $bags = Bag::join('cities as oh', 'bags.origin_hub_id', '=', 'oh.id')
            ->join('cities as dh', 'bags.destination_hub_id', '=', 'dh.id')
            ->leftJoin('shipping_modes as sm', 'bags.shipping_mode_id', '=', 'sm.id')
            ->join('admins as a', 'bags.created_by', '=', 'a.id')
            ->leftjoin('cities as jh1', 'bags.junction_hub_1_id', '=', 'jh1.id')
            ->leftjoin('cities as jh2', 'bags.junction_hub_2_id', '=', 'jh2.id')
            ->join('transport_modes as tm', 'bags.transport_mode_id', '=', 'tm.id')
            ->join('transport_mode_vendors as tmv', 'bags.transport_mode_vendor_id', '=', 'tmv.id')
            ->join('bag_statuses as bs', 'bags.status_id', '=', 'bs.id')
            ->select('bags.id', 'bags.status_id', 'oh.id as origin_id', 'oh.name as origin', 'dh.id as destination_id', 'dh.name as destination', 'bags.shipments', 'bags.quantity', 'sm.mode as shipping_mode', 'jh1.name as junction_1', 'jh2.name as junction_2', 'tm.name as transport_mode', 'tmv.name as vendor', 'bags.shipments_weight', DB::raw('(SELECT SUM(`s`.`chargeable_weight`) FROM `shipments` AS `s` INNER JOIN `bag_shipments` AS `bss` ON `s`.`id` = `bss`.`shipment_id` WHERE `bss`.`bag_id` = `bags`.`id`) AS `chargeable_weight`'), 'bags.actual_weight', 'bags.created_at as transit_at', 'a.name as transitted_by', 'oh.hub_id as origin_hub_id', 'dh.hub_id as destination_hub_id', 'bags.type as bag_type','bags.seal_number', 'bs.name as status')
            ->whereIn('bags.status_id', [1, 3, 5, 6]);

        if (session('role_id') != 1) {
            $bags = $bags->where(function ($query) {
                $query->whereIn('oh.hub_id', session('hubs'))->orWhereIn('dh.hub_id', session('hubs'))->orWhereIn('bags.junction_hub_1_id', session('hubs'))->orWhereIn('bags.junction_hub_1_id', session('hubs'));
            });
        }

        $datatables = Datatables::of($bags)
            ->addColumn('aging',function ($bag){

                $days = Carbon::now()->diffInDays($bag->transit_at);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            })
            ->addColumn('shipments_count', function ($bag) {
                return $bag->shipments;
            })
            ->editColumn('bag_type',function ($bag){
                if($bag->bag_type == 1){
                    return 'Normal';
                }else{
                    return 'Return';
                }
            })
            ->addColumn('shipments', function ($bag) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $bag->shipments . '</button>';
            })
            ->filterColumn('bags.seal_number', function ($query, $keyword) {
                return $query->where('bags.seal_number', '=', $keyword);
            })
            ->filterColumn('shipping_mode',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('sm.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('status',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where('bs.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action', function($pickup_request) {
                if (session('role_id') == 1 || in_array(453, session('permissions'))) {
                    return '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                      <button type="button" class="dropdown-item edit_seal_number"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Seal Number</div></button>
                    </div>
                  </div>
          ';
                }
                else {
                    return '';
                }
            });

        if ($bag_type = $request->get('bag_type')) {
            if ($bag_type != 0) {
                $datatables->where('bags.type', $bag_type);
            }
        }

        if ($tracking_number = $request->get('tracking_number')) {
            $datatables->join('bag_shipments as bssh', 'bags.id', '=', 'bssh.bag_id')
                ->join('shipments as s', 'bssh.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
        }

        if ($bag_number = $request->get('bag_number')) {
            $datatables->where('bags.seal_number', '=', $bag_number);
        }

        return $datatables->make(true);
    }

    public function master_cargo_pending_shipments(Request $request) {
        $tracking_numbers = array();

        $bag_shipments = BagShipment::where('bag_id', $request->id)->get();

        foreach ($bag_shipments as $bag_shipment) {
            $shipment = $bag_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function master_cargo_create_index(Request $request, $id = NULL) {
        return redirect()->route('admin.access_denied');
        if($id == NULL){
            $id = 0;
        }
        return view('admin.master_cargo.create')->with(['id' => $id]);
    }

    public function create_master_cargo_bag_details(Request $request) {
        $bag = Bag::where('seal_number', $request->bag_number);

        if ($bag->exists()) {
            $bag = $bag->first();

            if (in_array($bag->status_id, [1, 3, 5, 6])) {
                $hub_id = $bag->origin_hub_id;
                $junction_hub_1_id = $bag->junction_hub_1_id;
                $junction_hub_2_id = $bag->junction_hub_2_id;
                $destination_hub_id = $bag->destination_hub_id;

                $allowed = FALSE;

                if (session('role_id') == 1) {
                    $allowed = TRUE;
                }
                else if (in_array($hub_id, session('hubs')) || in_array($junction_hub_1_id, session('hubs')) || in_array($junction_hub_2_id, session('hubs'))) {
                    $allowed = TRUE;
                }

                if ($allowed) {
                    if ($request->hub_id == 0 || $request->hub_id == $destination_hub_id) {
//                            if ($request->shipping_mode_id == 0 || $request->shipping_mode_id == $bag->shipping_mode->id) {
                        $details = array();

                        $origin = $bag->origin_hub;
                        $destination = $bag->destination_hub;

                        $details['id'] = $bag->id;
                        $details['bag_number'] = $bag->seal_number;
                        $details['shipments'] = $bag->shipments;
                        $details['origin'] = $origin->name;
                        $details['destination'] = $destination->name;
                        $details['actual_weight'] = $bag->actual_weight;
                        $hub = $destination->hub_city;

                        $details['hub']['id'] = $hub->id;
                        $details['hub']['name'] = $hub->name;

                        $shipping_mode_id = $request->shipping_mode_id;

                        if ($shipping_mode_id == 0) {
                            $shipping_mode_id = $bag->shipping_mode->id;

                            $details['shipping_mode']['id'] = $bag->shipping_mode->id;
                            $details['shipping_mode']['name'] = $bag->shipping_mode->mode;
                        }

                        if ($request->hub_id == 0) {
                            $bags = Bag::join('cities as oc', 'bags.origin_hub_id', '=', 'oc.id')
                                ->join('cities as dc', 'bags.destination_hub_id', '=', 'dc.id')
                                ->select(DB::raw('count(bags.id) as count'))
                                ->where('bags.status_id', 1)
                                ->where('bags.destination_hub_id', $destination_hub_id)
                                ->where('bags.shipping_mode_id', $shipping_mode_id);

                            if (session('role_id') != 1) {
                                $bags = $bags->whereIn('oc.hub_id', session('hubs'));
                            }

                            $bags = $bags->first();

                            $details['total'] = $bags->count;
                        }
                        return ['status' => 0, 'success' => 'Bag has been added', 'details' => $details];
//                            }
//                            else {
//                                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment\'s Shipment Mode is different'];
//                            }
                    }
                    else {
                        return ['status' => 1, 'error' => 'Given Bag Number\'s belongs to another Hub'];
                    }
                }
                else {
                    return ['status' => 1, 'error' => 'Given Bag Number\'s does not belong to any of your assigned Hub\'s Cities'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'Given Bag Number\'s has already been modified'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Bag with given Bag Number is present'];
        }
    }

    public function create_master_cargo_details(Request $request){
        $bag = Bag::find(current($request->bag_ids));

        $origin = $bag->origin_hub->hub_city;

        $origin_details = array();

        $origin_details['id'] = $origin->id;
        $origin_details['name'] = $origin->name;


        
        $destination = $bag->destination_hub->hub_city;

        $destination_details = array();

        $destination_details['id'] = $destination->id;
        $destination_details['name'] = $destination->name;

        $details = array();

        $details['junctions'] = City::select(['id', 'name'])->where('business_category_id', 1)->where('hub', 1)->get();

        $details['transport_modes'] = TransportMode::all();

        $details['routes'] = RouteManagement::where('starting_point_id',$origin->id)->get();
        // $details['fleets'] = Fleet::leftjoin('master_cargoes as mc','mc.fleet_id','<>','fleets.id')
        //                     ->where('fleets.status',1)
        //                     ->where('mc.fleet_id','<>','fleets.id')
        //                     ->whereIn('mc.status_id',[2,3,4,5])
        //                     ->groupBy('fleets.id')
        //                     ->get();
        $details['fleets'] = Fleet::where('status',1)->get();
        

        $details['transport_mode_vendors'] = TransportModeVendor::get()->groupBy('transport_mode_id');

        $details['shipping_modes'] = ShippingMode::whereIn('id',[1,2])->get();

        $details['receivers'] = Admin::where('status', 1)->whereHas('hubs', function ($query) use($destination_details) {
            $query->where('hub_id',  $destination_details['id']);
        })->select(['id', 'name'])->get();

        $details['origin'] = $origin_details;
        $details['destination'] = $destination_details;

        $mapping = JunctionMapping::where(['origin_id' => $origin_details['id'], 'destination_id' => $destination_details['id']])->first();
        if($mapping){
            $details['junction_1'] = $mapping['junction_1'];
            $details['junction_2'] = $mapping['junction_2'];
            $details['receiver'] = $mapping['receiver'];
        }
        $details['actual_weight'] = 0;

        foreach ($request->bag_ids as $bag_id){
            $bag_actual_weight = Bag::find($bag_id);
            $details['actual_weight'] = $details['actual_weight'] + $bag_actual_weight->actual_weight;
        }

        return $details;
    }

    public function master_cargo_create_store(Request $request){
        $bags = 0;
        $shipments = 0;
        $quantity = 0;
        $bags_weight = 0;

        $bag_ids = explode(',', $request->input('bag_ids'));
        foreach ($bag_ids as $key => $bag_id) {
            $bag = Bag::find($bag_id);

            if (in_array($bag->status_id, [1, 3, 5, 6])) {
                $bags++;
                $bags_weight += $bag->actual_weight;
                $shipments += $bag->shipments;
                $quantity += $bag->quantity;

            }
            else {
                unset($bag_ids[$key]);
            }
        }

        if (!empty($bag_ids)) {
            $master_cargo = new MasterCargo();

            $master_cargo->origin_hub_id = $request->input('origin_hub_id');
            $master_cargo->destination_hub_id = $request->input('destination_hub_id');
//            $master_cargo->route_management_id = $request->input('route_management_id');
//            $master_cargo->fleet_id = $request->input('fleet_id');
            
            // $master_cargo->junction_hub_1_id = $request->input('junction_1');
            // $master_cargo->junction_hub_2_id = $request->input('junction_2');
            $master_cargo->shipping_mode_id = $request->input('shipping_mode_id');
            $master_cargo->transport_mode_id = $request->input('transport_mode');
            $master_cargo->driver_name = $request->input('driver_name');
            // $master_cargo->vehicle = $request->input('vehicle');
            $master_cargo->phone_number = $request->input('phone_number');
//            if($request->has('cnic')){
//                $master_cargo->cnic = $request->input('cnic');
//            }

            if ($request->input('transport_mode_vendor') == 0) {
                $transport_mode_vendor = new TransportModeVendor();

                $transport_mode_vendor->transport_mode_id = $request->input('transport_mode');
                $transport_mode_vendor->name = $request->input('vendor_name');

                $transport_mode_vendor->save();

                $master_cargo->transport_mode_vendor_id = $transport_mode_vendor->id;
            }
            else {
                $master_cargo->transport_mode_vendor_id = $request->input('transport_mode_vendor');
            }

            $master_cargo->bags = $bags;
            $master_cargo->shipments = $shipments;
            $master_cargo->quantity = $quantity;
            $master_cargo->bags_weight = $bags_weight;

            $master_cargo->actual_weight = $request->input('actual_weight');
            $master_cargo->created_by = Auth::id();
            if($request->onward_forwarding == 1){
                $master_cargo_status_id = 6;
                $master_cargo->onward_forwarding = 1;
            }
            else{
                $master_cargo_status_id = 1;
            }

            $master_cargo->status_id = $master_cargo_status_id;
            $master_cargo->save();

            $master_cargo_id = $master_cargo->id;

            foreach ($bag_ids as $bag_id) {
                $mater_cargo_bags= new MasterCargoBag();

                $mater_cargo_bags->master_cargo_id = $master_cargo_id;
                $mater_cargo_bags->bag_id = $bag_id;

                $mater_cargo_bags->save();

                $bag = Bag::find($bag_id);

                $bag->status_id = 2;

                $bag->save();

                MasterCargoBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), $master_cargo_id, $master_cargo_status_id);
            }

            if ($request->filled('submit_and_print_form')) {
                $print = $master_cargo_id;
            }
            else {
                $print = FALSE;
            }
            $path = $this::master_cargo_print($master_cargo_id, 1);
            NotificationsController::send(87, $master_cargo->destination_hub_id, url('/') . '/' . 'reports/master_cargo_'. str_pad($master_cargo_id, 6, '0', STR_PAD_LEFT) .'.pdf');
            if($master_cargo_status_id == 6){
                $text = 'Onward Forwarding';
            }
            else{
                $text = 'Master';
            }
            if($request->onward_forwarding == 1){
                return redirect()->route('admin.master_cargo.create.index')->with(['success' => ' Cargo Created with Onward Forwarded Master Cargo Number: ' . str_pad($master_cargo_id, 6, '0', STR_PAD_LEFT), 'print' => $print]);
            }
            else{
                return redirect()->route('admin.master_cargo.create.index')->with(['success' => ' Cargo Created with Master Cargo Number: ' . str_pad($master_cargo_id, 6, '0', STR_PAD_LEFT), 'print' => $print]);
            }
        }
        else {
            return back()->withErrors('All Bags have already been added to another Master Cargo!');
        }
    }

    public function master_cargo_in_transit_index(){
        return redirect()->route('admin.access_denied');
        $shipping_mode = ShippingMode::all();
        $cargo_status = MasterCargoStatus::all();
        $transport_vendor = TransportModeVendor::all();
        $transport_mode = TransportMode::all();
        return view('admin.master_cargo.in_transit')->with(['shipping_mode'=>$shipping_mode,'cargo_status'=>$cargo_status,'transport_mode'=>$transport_mode,'transport_vendor'=>$transport_vendor]);
    }

    public function master_cargo_in_transit_list(Request $request){
        $receive_cargo = MasterCargo::join('cities as oh', 'master_cargoes.origin_hub_id', '=', 'oh.id')
            ->join('cities as dh', 'master_cargoes.destination_hub_id', '=', 'dh.id')
            ->leftJoin('shipping_modes as sm', 'master_cargoes.shipping_mode_id', '=', 'sm.id')
            ->join('admins as a', 'master_cargoes.created_by', '=', 'a.id')
            ->join('master_cargo_statuses as mcs', 'master_cargoes.status_id', '=', 'mcs.id')
            ->leftjoin('fleets as f', 'master_cargoes.fleet_id', '=', 'f.id')
            ->leftjoin('route_managements as rm', 'master_cargoes.route_management_id', '=', 'rm.id')
            ->leftjoin('transport_modes as tm', 'master_cargoes.transport_mode_id', '=', 'tm.id')
            ->leftjoin('transport_mode_vendors as tmv', 'master_cargoes.transport_mode_vendor_id', '=', 'tmv.id')
            ->select('master_cargoes.id', 'master_cargoes.status_id', 'oh.id as origin_id', 'oh.name as origin', 'dh.id as destination_id', 'dh.name as destination', 'master_cargoes.shipments', 'master_cargoes.quantity', 'master_cargoes.bags', 'master_cargoes.short_received_bags', 'master_cargoes.driver_name', 'f.reg_number as vehicle', 'master_cargoes.phone_number', 'sm.mode as shipping_mode', 'tm.name as transport_mode', 'tmv.name as vendor','master_cargoes.bags_weight', 'master_cargoes.actual_weight', 'master_cargoes.created_at as transit_at', 'a.name as transitted_by', 'mcs.name as status', 'oh.hub_id as origin_hub_id', 'dh.hub_id as destination_hub_id')
            ->whereIn('master_cargoes.status_id', [1, 3, 6]);

        if (session('role_id') != 1) {
            $receive_cargo = $receive_cargo->leftjoin('route_management_junctions as rmj', 'rm.id', '=', 'rmj.route_management_id')->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->whereIn('oh.hub_id', session('hubs'))
                        ->orWhereIn('dh.hub_id', session('hubs'))
                        ->orWhereIn('rmj.junction_id', session('hubs'))
                        ->orWhere('a.id', Auth::id());
                });
            });
        }

        $datatables = Datatables::of($receive_cargo)
            ->addColumn('aging',function ($master_cargo){

                $days = Carbon::now()->diffInDays($master_cargo->transit_at);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            })
            ->addColumn('id_padded', function ($master_cargo) {
                return str_pad($master_cargo->id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('id_padded_link', function ($master_cargo) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($master_cargo->id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->addColumn('bags_count', function ($master_cargo) {
                return $master_cargo->bags;
            })
            ->addColumn('shipments_count', function ($master_cargo) {
                return $master_cargo->shipments;
            })
            ->addColumn('short_received_bags_count', function ($master_cargo) {
                return $master_cargo->short_received_bags;
            })
            ->addColumn('bags', function ($master_cargo) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $master_cargo->bags . '</button>';
            })
            ->addColumn('shipments', function ($master_cargo) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $master_cargo->shipments . '</button>';
            })
            ->addColumn('short_received_bags', function ($master_cargo) {
                if($master_cargo->short_received_bags > 0){
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $master_cargo->short_received_bags . '</button>';
                }
                else{
                    return '-';
                }
            })
            ->addColumn('excel_junctions', function ($master_cargo) {
                $master_cargo = MasterCargo::find($master_cargo->id);
                $junctions = '';
                
                if($master_cargo->route_management_id){
                    foreach ($master_cargo->route_management->junctions as $value) {
                        $junctions .=  $value->junction->name.'  ';
                    }
                }
                    

                    return $junctions;
                
            })
            ->addColumn('junctions', function ($receive_cargo) {
                $master_cargo = MasterCargo::find($receive_cargo->id);
                if($master_cargo->route_management_id){
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . count($master_cargo->route_management->junctions) . '</button>';
                }
            })
            
            ->filterColumn('master_cargoes.id', function ($query, $keyword) {
                return $query->where('master_cargoes.id', '=', $keyword);
            })
            ->filterColumn('shipping_mode',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('sm.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('mcs.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            });

//            ->addColumn('action', function($master_cargo) {
//
//                $print_button = '<button type="button" class="dropdown-item print"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-printer"></i></div><div class="col-9 offset-1">Print</div></button>';
////                $add_forwarding_details_button = '<button type="button" class="dropdown-item add_forwarding_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update Forwarding Details</div></button>';
////                $view_forwarding_details_button = '<button type="button" class="dropdown-item view_forwarding_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Forwarding Details</div></button>';
//                $launch_dispute_button = '<button type="button" class="dropdown-item launch_dispute"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Launch Dispute</div></button>';
//                $lost_button = '<button type="button" class="dropdown-item lost"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Lost</div></button>';
//                $receive_button = '<button type="button" class="dropdown-item receive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Receive</div></button>';
//
//                $dropdown = '
//          <div class="btn-group">
//            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
//            <div class="dropdown-menu dropdown-menu-sm">
//        ';
//
//                $dropdown .= $print_button;
//
////                if (($master_cargo->status_id == 1) && (session('role_id') == 1 || (in_array(28, session('permissions')) && in_array($master_cargo->origin_hub_id, session('hubs'))))) {
////                    $dropdown .= $add_forwarding_details_button;
////                }
//
////                $dropdown .= $view_forwarding_details_button;
//
//                if (session('role_id') == 1 || in_array(29, session('permissions'))) {
//                    $dropdown .= $launch_dispute_button;
//                }
//
//                if (session('role_id') == 1 || in_array(222, session('permissions'))) {
//                    $dropdown .= $lost_button;
//                }
//
//                if (session('role_id') == 1 || (in_array(31, session('permissions')) && in_array($master_cargo->destination_hub_id, session('hubs')))) {
//                    $dropdown .= $receive_button;
//                }
//
//                $dropdown .= '
//            </div>
//          </div>
//        ';
//
//                return $dropdown;
//            });
        if($request->get('tracking_number') || $bag_number = $request->get('bag_number')){
            $datatables->join('master_cargo_bags as mcb', 'master_cargoes.id', '=', 'mcb.master_cargo_id')
                ->join('bags as b', 'b.id', '=', 'mcb.bag_id');

            if ($tracking_number = $request->get('tracking_number')) {
                $datatables->join('bag_shipments as bs', 'b.id', '=', 'bs.bag_id')
                    ->join('shipments as s', 'bs.shipment_id', '=', 's.id')
                    ->where('s.tracking_number', '=', $tracking_number);
            }

            if ($bag_number = $request->get('bag_number')) {
                $datatables->where('b.seal_number', '=', $bag_number);
            }
        }

        return $datatables->make(true);
    }

    public function master_cargo_in_transit_bags(Request $request) {
        $bag_numbers = array();
        $master_cargo = MasterCargo::find($request->id);
        $master_cargo_bags = $master_cargo->master_bags;
        foreach ($master_cargo_bags as $master_cargo_bag){
            $seal_number = $master_cargo_bag->bag->seal_number;
            $bag_numbers[]  = $seal_number;
        }

        return $bag_numbers;
    }

    public function master_cargo_in_transit_short_received_bags(Request $request) {
        $bag_numbers = array();
        $tracking_numbers = array();
        $master_cargo = MasterCargo::find($request->id);
        $master_cargo_bags = $master_cargo->master_bags;
        foreach ($master_cargo_bags as $master_cargo_bag){
            if($master_cargo_bag->status == 0){
                $seal_number = $master_cargo_bag->bag->seal_number;
                $bag_numbers[]  = $seal_number;
                foreach ($master_cargo_bag->bag->shipment as $bag_shipment){
                    $shipment = $bag_shipment->shipment;
                    $tracking_numbers[$seal_number][] = $shipment->tracking_number;
                }
            }
        }

        return response()->json(['bag_numbers' => $bag_numbers, 'tracking_numbers' => $tracking_numbers]);
    }

    public function master_cargo_in_transit_shipments(Request $request) {
        $tracking_numbers = array();
        $master_cargo = MasterCargo::find($request->id);
        $master_cargo_bags = $master_cargo->master_bags;
        $master_cargo_bags_shipment_ids = array();
        foreach ($master_cargo_bags as $master_cargo_bag){
            $master_cargo_bag_shipments = $master_cargo_bag->bag->shipment;
            foreach ($master_cargo_bag_shipments as $master_cargo_bag_shipment) {
                $master_cargo_bags_shipment_ids[] = $master_cargo_bag_shipment->shipment_id;
            }
        }
        $trackings = Shipment::whereIn('id',$master_cargo_bags_shipment_ids)->select('tracking_number')->get();
        foreach ($trackings as $number){
            $tracking_numbers[]  =$number->tracking_number;
        }

        return $tracking_numbers;
    }

    public function master_cargo_in_transit_all_junctions(Request $request) {
        $junctions = array();
        $master_cargo = MasterCargo::find($request->id);
        if($master_cargo->route_management_id){
            foreach ($master_cargo->route_management->junctions as $value){
                $junctions[]  =$value->junction->name;
            }
        }
        

        return $junctions;
    }
    

    public function master_cargo_in_transit_lost(Request $request) {
        $master_cargo_id = $request->input('cargo_id');

        $master_cargo = MasterCargo::find($master_cargo_id);

        if (in_array($master_cargo->status_id, [1, 3, 6])) {
            $master_cargo->status_id = 5;

            $master_cargo->save();

            $master_cargo_bags = MasterCargoBag::where('master_cargo_id', $master_cargo->id)->where('status', 0);

            if ($master_cargo_bags->exists()) {
                foreach ($master_cargo_bags->get() as $master_cargo_bag) {
                    $bag = $master_cargo_bag->bag;
                    if($bag->status_id == 2){
                        $bag_shipments = BagShipment::where('bag_id', $bag->id);
                        if ($bag_shipments->exists()) {
                            foreach ($bag_shipments->get() as $bag_shipment){
                                $shipment = Shipment::find($bag_shipment->shipment_id);
                                if (in_array($shipment->shipper_status_id, [3, 21, 26, 32])) {
                                    $shipment->shipper_status_id = 18;
                                    $shipment->save();
                                    ShipmentsJourneyController::add($shipment->id, 18, 18, NULL, NULL, NULL, Auth::id());
                                }
                            }
                        }
                        $bag->status_id = 8;
                        $bag->save();
                        MasterCargoBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), $master_cargo_id, 5);
                    }
                }
            }

            return ['status' => 0, 'success' => 'Master Cargo Number #' . $master_cargo_id . ' has been Updated as Lost'];
        }
        else {
            return ['status' => 1, 'error' => 'Master Cargo Number #' . $master_cargo_id . ' could not be Updated as Lost'];
        }
    }

    public function master_cargo_in_transit_print(Request $request) {
        $html = $this::master_cargo_print($request->id);
        return $html;
    }

    public static function master_cargo_print($master_cargo_id, $type = NULL) {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $master_cargo = MasterCargo::find($master_cargo_id);


        $jucntion_names = '';
        if($master_cargo->route_management_id){
            foreach ($master_cargo->route_management->junctions as $value) {
                $jucntion_names .= ' - '.$value->junction['name'].' - ';
            }
        }
        $sender = $master_cargo->sender;
        $receiver = ($master_cargo->received_by) ? $master_cargo->receiver : NULL;

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Cargo Slip & Checklist</title>

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
                    </style>';

        if ($type == 'pdf') {
            $html .= '
                    <style>
                      body {
                        font-size: 0.75rem !important;
                        font-weight: bold !important;
                      }

                      td.replacement span {
                        width: auto !important;
                      }

                      .border.twice {
                        border-width: 1px !important;
                      }

                      .border.twice-top {
                        border-top-width: 1px !important;
                      }

                      .border.twice-bottom {
                        border-bottom-width: 1px !important;
                      }

                      .border.twice-left {
                        border-left-width: 1px !important;
                      }

                      .border.twice-right {
                        border-right-width: 1px !important;
                      }

                      .font-small {
                        font-size: 0.65rem !important;
                      }
                    </style>
                ';
        }
        $html .= '</head>
                  <body>
                    <div>
                      <div class="cargo_slip">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Cargo Slip</strong></td>
                              <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                              </tr>
                            <tr>
                              <td class="color secondary"><strong>Destination Hub</strong></td>
                              <td>' . $master_cargo->destination_hub->name . '</td>
                              <td rowspan="9" class="text-center align-middle">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($master_cargo->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . str_pad($master_cargo->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                              </td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transit Date</strong></td>
                              <td>' . $master_cargo->created_at . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Shipping Mode</strong></td>
                              <td>' . $master_cargo->shipping_mode->mode . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transport Mode</strong></td>
                              <td>' . $master_cargo->transport_mode->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Vendor</strong></td>
                              <td>' . $master_cargo->transport_mode_vendor->name . '</td>
                            </tr>
                            <tr>
                              ';
                            if($master_cargo->onward_forwarding == 1){
                                $html .= '<td class="color secondary"><strong>Onward Forwarding Cargo No.</strong></td>';
                            }
                            else{
                                $html .= '<td class="color secondary"><strong>Master Cargo No.</strong></td>';
                            }
                  $html .= '
                              <td>' . str_pad($master_cargo->id, 6, '0', STR_PAD_LEFT) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>No. of Bags</strong></td>
                              <td>' . $master_cargo->bags . '</td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td colspan="2" class="color primary"><strong>Sender Information</strong></td>
                              <td colspan="2" class="color primary"><strong>Receiver Information</strong></td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Name</strong></td>
                              <td>' . $sender->name . '</td>
                              <td class="color secondary"><strong>Name</strong></td>
                              <td>' . (($receiver) ? $receiver['name'] : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Role</strong></td>
                              <td>' . $sender->role->name  . ' - ' . $sender->role->department->name . '</td>
                              <td class="color secondary"><strong>Role</strong></td>
                              <td>' . (($receiver) ? $receiver->role->department->name : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Phone No.</strong></td>
                              <td>' . $sender['phone_number'] . '</td>
                              <td class="color secondary"><strong>Phone No.</strong></td>
                              <td>' . (($receiver) ? $receiver['phone_number'] : '') . '</td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="color primary"><strong>Route Information</strong></td>
                            </tr>
                            <tr>
                              <td class="text-center">' . $master_cargo->origin_hub->name . $jucntion_names . $master_cargo->destination_hub->name . '</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>

                      <div class="cargo_checklist">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Cargo Checklist</strong></td>
                              <td colspan="4" class="text-center align-middle  color secondary">Printed at ' . Carbon::now() . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Origin Hub</strong></td>
                              <td>' . $master_cargo->origin_hub->name . '</td>
                              <td class="color secondary"><strong>Driver Name</strong></td>
                              <td>' . $master_cargo->driver_name . '</td>
                              <td rowspan="6" class="text-center align-middle">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($master_cargo->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . str_pad($master_cargo->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                              </td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Destination Hub</strong></td>
                              <td>' . $master_cargo->destination_hub->name . '</td>
                              <td class="color secondary"><strong>Vehicle Number</strong></td>
                              <td>' . (($master_cargo->fleet_id) ? $master_cargo->fleet->reg_number :  '-') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transit Date</strong></td>
                              <td>' . $master_cargo->created_at . '</td>
                              <td class="color secondary"><strong>Contact Phone</strong></td>
                              <td>' . $master_cargo->phone_number . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>No. of Bags</strong></td>
                              <td>' . $master_cargo->bags . '</td>
                              <td colspan="2"></td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>No. of Shipments</strong></td>
                              <td>' . $master_cargo->shipments . '</td>
                              <td colspan="2"></td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Total Weight</strong></td>
                              <td>' . $master_cargo->actual_weight . '</td>
                              <td colspan="2"></td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="color primary"><strong>S. No.</strong></td>
                              <td class="color primary"><strong>Bag No.</strong></td>
                              <td class="color primary"><strong>No. of Shipments</strong></td>
                              <td class="color primary"><strong>Quantity</strong></td>
                              <td class="color primary"><strong>Origin</strong></td>
                              <td class="color primary"><strong>Destination</strong></td>
                              <td class="color primary"><strong>Actual Weight</strong></td>
      ';

        $serial_number = 1;

        foreach ($master_cargo->master_bags as $master_cargo_bag) {
            $bag = $master_cargo_bag->bag;


            $html .= '
                            <tr>
                              <td>' . $serial_number . '</td>
                              <td>' . $bag->seal_number . '</td>
                              <td>' . $bag->shipments . '</td>
                              <td>' . $bag->quantity . '</td>
                              <td>' . $bag->origin_hub->name . '</td>
                              <td>' . $bag->destination_hub->name . '</td>
                              <td>' . $bag->actual_weight . '</td>
                            </tr>
        ';

            $serial_number++;
        }

        if($type == 1){
            $html .= '
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </body>
                </html>
      ';
            $pdf = SnappyPDF::loadHTML($html)->save('reports/master_cargo_'. str_pad($master_cargo->id, 6, '0', STR_PAD_LEFT) .'.pdf');
            return $pdf;
        }
        else{
            $html .= '
                          </tbody>
                        </table>
                      </div>
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
    }

    public function master_cargo_in_transit_junctions(Request $request) {
        $city_ids = array();

        $cargo_consignments = MasterCargo::whereIn('id', $request->ids)->get();

        foreach ($cargo_consignments as $cargo_consignment) {
            if($cargo_consignment->route_management_id){
                foreach ($cargo_consignment->route_management->junctions as $value) {
                
                    if($cargo_consignment->origin_hub_id != $value->junction->id && $cargo_consignment->destination_hub_id != $value->junction->id && !in_array($value->junction->id, $city_ids)){
                        $city_ids[] = $value->junction->id;
                    }
                }
            }
            
            // if ($cargo_consignment->origin_hub_id != $cargo_consignment->junction_hub_1_id && $cargo_consignment->destination_hub_id != $cargo_consignment->junction_hub_1_id && !in_array($cargo_consignment->junction_hub_1_id, $city_ids)) {
            //     $city_ids[] = $cargo_consignment->junction_hub_1_id;
            // }

            // if ($cargo_consignment->junction_hub_2_id && $cargo_consignment->origin_hub_id != $cargo_consignment->junction_hub_2_id && $cargo_consignment->destination_hub_id != $cargo_consignment->junction_hub_2_id && !in_array($cargo_consignment->junction_hub_2_id, $city_ids)) {
            //     $city_ids[] = $cargo_consignment->junction_hub_2_id;
            // }
        }

        $cities = City::select(['id', 'name'])->where('business_category_id', 1)->whereIn('id', $city_ids);

        if (session('role_id') != 1) {
            $cities = $cities->whereIn('id', session('hubs'));
        }

        return $cities->get();
    }

    public function master_cargo_in_transit_details(Request $request) {
        $cargo_consignment = MasterCargo::where('id', $request->cargo_number);
        if ($cargo_consignment->exists()) {
            $cargo_consignment = $cargo_consignment->first();
            $master_cargo_junctions=array();
            if($cargo_consignment->route_management_id){
                foreach ($cargo_consignment->route_management->junctions as  $value) {
                    array_push($master_cargo_junctions,$value->junction_id);
                }
            }
            
            // dd(array_intersect($master_cargo_junctions,session('hubs')));
            if (session('role_id') == 1 || (array_intersect($master_cargo_junctions,session('hubs')) )) {
            // if (session('role_id') == 1 || (in_array($cargo_consignment->junction_hub_1_id, session('hubs')) || in_array($cargo_consignment->junction_hub_2_id, session('hubs')))) {
                if ($cargo_consignment->status_id == 1 || $cargo_consignment->status_id == 3 || $cargo_consignment->status_id == 6) {
                    $details = array();

                    $details['id'] = $cargo_consignment->id;
                    $details['master_cargo_number'] = str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT);
                    $details['origin'] = $cargo_consignment->origin_hub->name;
                    $details['destination'] = $cargo_consignment->destination_hub->name;
                    $details['no_of_bags'] = $cargo_consignment->bags;
                    $details['no_of_shipments'] = $cargo_consignment->shipments;

                    return ['status' => 0, 'success' => 'Master Cargo has been scanned', 'details' => $details];
                }
                else {
                    return ['status' => 1, 'error' => 'Given Master Cargo has already been modified'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'Given Master Cargo does not have any of your assigned Hub\'s Cities as it\'s Junctions'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Master Cargo with given Number is present'];
        }
    }

//    public function master_cargo_in_transit_receive_at_link(Request $request) {
//        if(is_array($request->master_cargo_consignment_ids)){
//            foreach ($request->master_cargo_consignment_ids as $cargo_consignment_id) {
//                $cargo_consignment_junction_receival = new MasterCargoJunctionReceival();
//
//                $cargo_consignment_junction_receival->master_cargo_id = $cargo_consignment_id;
//                $cargo_consignment_junction_receival->junction_id = $request->junction;
//                $cargo_consignment_junction_receival->receiver_id = Auth::id();
//
//                $cargo_consignment_junction_receival->save();
//
//                $cargo_consignment = MasterCargo::find($cargo_consignment_id);
//
//                $master_bags = $cargo_consignment->master_bags;
//
//                if ($cargo_consignment->junction_hub_1_id == $request->junction) {
//                    foreach ($master_bags as $master_bag){
//                        $master_bag->status = 1;
//                        $master_bag->save();
//                        $bag = $master_bag->bag;
//                        $bag->status_id = 5;
//                        $bag->save();
//                        MasterCargoBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), $cargo_consignment->id, 2);
//                    }
//                }
//                else if ($cargo_consignment->junction_hub_2_id == $request->junction) {
//                    foreach ($master_bags as $master_bag){
//                        $master_bag->status = 1;
//                        $master_bag->save();
//                        $bag = $master_bag->bag;
//                        $bag->status_id = 6;
//                        $bag->save();
//                        MasterCargoBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), $cargo_consignment->id, 2);
//                    }
//                }
//                else {
//                    foreach ($master_bags as $master_bag){
//                        $master_bag->status = 1;
//                        $master_bag->save();
//                        $bag = $master_bag->bag;
//                        $bag->status_id = 3;
//                        $bag->save();
//                        MasterCargoBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), $cargo_consignment->id, 2);
//                    }
//                }
//
//                $cargo_consignment->status_id = 2;
//                $cargo_consignment->received_at = Carbon::now();
//                $cargo_consignment->received_by = Auth::id();
//                $cargo_consignment->save();
//            }
//
//            return ['status' => 0, 'success' => 'Master Cargo(s) has been received at Junction'];
//        }
//        else{
//            return ['status' => 1, 'success' => 'Master Cargo(s) Not Found'];
//        }
//    }

    public function master_cargo_in_transit_receive_at_link(Request $request) {
        $master_cargo_id = $request->cargo_ids;
        if (session('role_id') == 1 || (in_array($request->junction, session('hubs')))) {
            $total = MasterCargoBag::where('master_cargo_id', $master_cargo_id)->where('status', 0)->count();
            return view('admin.master_cargo.receive_at_junction')->with(['cargo_id' => $master_cargo_id, 'total' => $total, 'junction' => $request->junction]);
        }
        else {
            return back()->withErrors('Master Cargo doesn\'t belong to your assigned hub(s)!');
        }

    }
    public function master_cargo_in_transit_receive_at_link_store(Request $request) {
        $cargo_consignment_id = $request->cargo_consignment_id;
        $bag_ids = array_unique(explode(',', $request->bag_ids));
        $cargo_consignment_junction_receival = new MasterCargoJunctionReceival();

        $cargo_consignment_junction_receival->master_cargo_id = $cargo_consignment_id;
        $cargo_consignment_junction_receival->junction_id = $request->junction;
        $cargo_consignment_junction_receival->receiver_id = Auth::id();

        $cargo_consignment_junction_receival->save();


        $cargo_consignment = MasterCargo::find($cargo_consignment_id);

        $master_bags = $cargo_consignment->master_bags;
        $master_cargo_junctions=array();
        if($cargo_consignment->route_management_id){
            foreach ($cargo_consignment->route_management->junctions as  $value) {
                array_push($master_cargo_junctions,$value->junction_id);
            }
        }
        
        // dd(array_intersect($master_cargo_junctions,session('hubs')));
        // if (session('role_id') == 1 || (array_intersect($master_cargo_junctions,session('hubs')) )) {
            if (in_array($request->junction, $master_cargo_junctions)) {
                foreach ($master_bags as $master_bag){
                    if(in_array($master_bag->bag_id, $bag_ids)){
                        $master_bag->status = 1;
                        $master_bag->save();
                        $bag = $master_bag->bag;
                        $bag->status_id = 5;
                        $bag->save();
                        MasterCargoBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), $cargo_consignment->id, 2);
                    }
                }
            }
        // if ($cargo_consignment->junction_hub_1_id == $request->junction) {
        //     foreach ($master_bags as $master_bag){
        //         if(in_array($master_bag->bag_id, $bag_ids)){
        //             $master_bag->status = 1;
        //             $master_bag->save();
        //             $bag = $master_bag->bag;
        //             $bag->status_id = 5;
        //             $bag->save();
        //             MasterCargoBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), $cargo_consignment->id, 2);
        //         }
        //     }
        // }
        // else if ($cargo_consignment->junction_hub_2_id == $request->junction) {
        //     foreach ($master_bags as $master_bag){
        //         if(in_array($master_bag->bag_id, $bag_ids)) {
        //             $master_bag->status = 1;
        //             $master_bag->save();
        //             $bag = $master_bag->bag;
        //             $bag->status_id = 6;
        //             $bag->save();
        //             MasterCargoBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), $cargo_consignment->id, 2);
        //         }
        //     }
        // }
        else {
            foreach ($master_bags as $master_bag){
                if(in_array($master_bag->bag_id, $bag_ids)) {
                    $master_bag->status = 1;
                    $master_bag->save();
                    $bag = $master_bag->bag;
                    $bag->status_id = 3;
                    $bag->save();
                    MasterCargoBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), $cargo_consignment->id, 2);
                }
            }
        }

        $cargo_consignment->received_bags = MasterCargoBag::where('master_cargo_id', $cargo_consignment_id)->where('status', 1)->count();

        $short_received = MasterCargoBag::where('master_cargo_id', $cargo_consignment_id)->where('status', 0)->count();

        if ($short_received > 0) {
            $cargo_consignment->short_received_bags = $short_received;
            $status_id = 3;
        }
        else {
            $cargo_consignment->short_received_bags = 0;
            $status_id = 2;
        }
        $cargo_consignment->status_id = $status_id;

        $cargo_consignment->received_at = Carbon::now();
        $cargo_consignment->received_by = Auth::id();
        $cargo_consignment->save();

        //dispute for short received
        if($cargo_consignment->status_id == 3){
            $cargo_short_received_bags = MasterCargoBag::where(['master_cargo_id'=>$cargo_consignment_id,'status'=>0])->select('bag_id')->get();
            foreach ($cargo_short_received_bags as $cargo_short_received_bag){
                $bag_short_received_shipments = array();
                $short_received_bag = Bag::find($cargo_short_received_bag->bag_id);
                $short_received_bag_shipments = $short_received_bag->shipment;
                foreach ($short_received_bag_shipments as $short_received_bag_shipment){
                    $bag_short_received_shipments[] = $short_received_bag_shipment->shipment_id;
                }
                if(!empty($bag_short_received_shipments)){
                    DisputeController::add_cargo_short_received($short_received_bag->seal_number,$bag_short_received_shipments, 2);
                }
            }
        }
        return redirect()->route('admin.master_cargo.in_transit.index')->with('success', 'Master Cargo No# ' . $cargo_consignment_id . ' has been Received at Junction');
    }

    public function master_cargo_in_transit_receive(Request $request) {
        if ($request->has('master_cargo_number')) {
            $cargo_consignment = MasterCargo::find($request->get('master_cargo_number'));

            if ($cargo_consignment) {
                if (session('role_id') == 1 || (in_array($cargo_consignment->destination_hub->hub_id, session('hubs')))) {
                    if (in_array($cargo_consignment->status_id, [1, 3, 6])) {
                        return redirect()->route('admin.master_cargo.receive.index')->with('cargo_consignment_id', $cargo_consignment->id);
                    }
                    else {
                        return back()->withErrors('Given Master Cargo Number has already been modified!');
                    }
                }
                else {
                    return back()->withErrors('Master Cargo doesn\'t belong to your assigned hub(s)!');
                }
            }
            else {
                return back()->withErrors('Invalid Master Cargo Number!');
            }
        }
        else {
            return back()->withErrors('Missing Master Cargo Number!');
        }
    }

    public function master_cargo_receive_index() {
        if (session('cargo_consignment_id')) {
            $total = MasterCargoBag::where('master_cargo_id', session('cargo_consignment_id'))->where('status', 0)->count();

            return view('admin.master_cargo.receive')->with('total', $total);
        }
        else {
            return redirect()->route('admin.master_cargo.in_transit.index')->withErrors('Kindly reselect a Master Cargo Number!');
        }
    }

    public function master_cargo_receive_bag_details(Request $request) {
        $bag = Bag::where('seal_number', $request->bag_number);

        if ($bag->exists()) {
            $bag = $bag->first();

            $cargo_consignment_bag = MasterCargoBag::where('bag_id', $bag->id);

            if ($cargo_consignment_bag->exists()) {
                $cargo_consignment_bag = $cargo_consignment_bag->where('master_cargo_id', $request->cargo_consignment_id);

                if ($cargo_consignment_bag->exists()) {
                    $cargo_consignment_bag = $cargo_consignment_bag->where('status', 0);

                    if ($cargo_consignment_bag->exists()) {
                        $cargo_consignment_bag = $cargo_consignment_bag->first();

                        $details = array();

                        $details['id'] = $bag->id;
                        $details['bag_number'] = $bag->seal_number;
                        $details['origin'] = $bag->origin_hub->name;
                        $details['destination'] = $bag->destination_hub->name;
                        $details['shipping_mode'] = $bag->shipping_mode->mode;
                        $details['actual_weight'] = $bag->actual_weight;

                        return ['status' => 0, 'success' => 'Bag has been added', 'details' => $details];
                    }
                    else {
                        return ['status' => 1, 'error' => 'Given Bag Number\'s has already been Received'];
                    }
                }
                else {
                    return ['status' => 1, 'error' => 'Given Bag Number\'s does not belong to current Master Cargo Number'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'Given Bag Number\'s is not in any Master Cargo'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Bag with given Bag Number is present'];
        }
    }

    public function master_cargo_receive_short_received(Request $request) {
        $bag_ids = array_unique($request->input('bag_ids'));

        $cargo_consignment_bags= MasterCargoBag::where('master_cargo_id', $request->input('cargo_consignment_id'))->where('status', 0);

        if ($cargo_consignment_bags->count() != count($bag_ids)) {
            $cargo_consignment_bag_ids = $cargo_consignment_bags->pluck('bag_id')->toArray();

            $short_bag_ids = array_diff($cargo_consignment_bag_ids, $bag_ids);

            $short_bags = array();

            foreach ($short_bag_ids as $short_bag_id) {
                $bag = Bag::find($short_bag_id);

                $short_bags[] = $bag->seal_number;
            }

            return ['status' => 0, 'success' => 'Bags found Short Received', 'short_received' => $short_bags];
        }
        else {
            return ['status' => 0, 'success' => 'No Short Received Bags', 'short_received' => FALSE];
        }
    }

    public function master_cargo_receive_store(Request $request) {
        $cargo_consignment_id = $request->cargo_consignment_id;

        $bag_ids = array_unique(explode(',', $request->bag_ids));

        $cargo_consignment = MasterCargo::find($cargo_consignment_id);
        $valid_bag_ids = array();
        foreach ($bag_ids as $bag_id) {
            $cargo_consignment_bag = MasterCargoBag::where('master_cargo_id', $cargo_consignment_id)->where('bag_id', $bag_id)->first();
            if($cargo_consignment_bag->status != 1){
                $cargo_consignment_bag->status = 1;
                $cargo_consignment_bag->save();

                $bag = Bag::find($bag_id);
                $bag->status_id = 4;
                $bag->save();
                $valid_bag_ids[] = $bag->id;
            }
        }

        $cargo_consignment->received_bags = MasterCargoBag::where('master_cargo_id', $cargo_consignment_id)->where('status', 1)->count();

        $short_received = MasterCargoBag::where('master_cargo_id', $cargo_consignment_id)->where('status', 0)->count();

        if ($short_received > 0) {
            $cargo_consignment->short_received_bags = $short_received;
            $status_id = 3;
        }
        else {
            $cargo_consignment->short_received_bags = 0;
            $status_id = 2;
        }
        $cargo_consignment->status_id = $status_id;
        foreach ($valid_bag_ids as $valid_bag_id){
            $bag = Bag::find($valid_bag_id);
            MasterCargoBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), $cargo_consignment->id, $status_id);
        }

        $cargo_consignment->received_at = Carbon::now();
        $cargo_consignment->received_by = Auth::id();
        $cargo_consignment->save();

        //dispute for short received
        if($cargo_consignment->status_id == 3){
            $cargo_short_received_bags = MasterCargoBag::where(['master_cargo_id'=>$cargo_consignment_id,'status'=>0])->select('bag_id')->get();
            foreach ($cargo_short_received_bags as $cargo_short_received_bag){
                $bag_short_received_shipments = array();
                $short_received_bag = Bag::find($cargo_short_received_bag->bag_id);
                $short_received_bag_shipments = $short_received_bag->shipment;
                foreach ($short_received_bag_shipments as $short_received_bag_shipment){
                    $bag_short_received_shipments[] = $short_received_bag_shipment->shipment_id;
                }
                if(!empty($bag_short_received_shipments)){
                    DisputeController::add_cargo_short_received($short_received_bag->seal_number,$bag_short_received_shipments, 2);
                }
            }
        }

//        end dispute short received
//        dispute start for junction
//here                  
                        if($cargo_consignment->route_management_id){
                            foreach ($cargo_consignment->route_management->junctions as $value) {

                                if($cargo_consignment->origin_hub_id != $value->junction->id && $cargo_consignment->destination_hub_id != $value->junction->id) {
                                    $junction = MasterCargoJunctionReceival::where(['master_cargo_id'=>$cargo_consignment_id,'junction_id'=>$value->junction->id])->exists();
                                    if(!$junction){
                                        DisputeController::add_junction_dispute($cargo_consignment_id,$value->junction->id, 1);
                                    }
                                }
                            }  
                        }
                              
                            // $junction_hub_1_id = $cargo_consignment->junction_hub_1_id;
                            // $junction_hub_2_id = $cargo_consignment->junction_hub_2_id;
                            // if($junction_hub_1_id != null){
                            //     if($cargo_consignment->origin_hub_id != $junction_hub_1_id && $cargo_consignment->destination_hub_id != $junction_hub_1_id) {
                            //         $junction1 = MasterCargoJunctionReceival::where(['master_cargo_id'=>$cargo_consignment_id,'junction_id'=>$junction_hub_1_id])->exists();
                            //         if(!$junction1){
                            //             DisputeController::add_junction_dispute($cargo_consignment_id,$junction_hub_1_id, 1);
                            //         }
                            //     }
                            // }
                            // if($junction_hub_2_id != null) {
                            //     if ($junction_hub_2_id && $cargo_consignment->origin_hub_id != $junction_hub_2_id && $cargo_consignment->destination_hub_id != $junction_hub_2_id) {
                            //         $junction2 = MasterCargoJunctionReceival::where(['master_cargo_id' => $cargo_consignment_id, 'junction_id' => $junction_hub_2_id])->exists();
                            //         if (!$junction2) {
                            //             DisputeController::add_junction_dispute($cargo_consignment_id, $junction_hub_2_id, 1);
                            //         }
                            //     }
                            // }
        //dispute end for junction
        return redirect()->route('admin.master_cargo.in_transit.index')->with('success', 'Master Cargo No# ' . $cargo_consignment_id . ' has been Received');
    }

    public function master_cargo_bag_quick_receive_index(){
        return redirect()->route('admin.access_denied');
        return view('admin.master_cargo.bag.quick_receive');
    }

    public function master_cargo_bag_quick_receive_bag_details(Request $request) {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();

            if ($shipment->shipper_status_id != 3 && $shipment->shipper_status_id != 21) {
                return ['status' => 1, 'error' => 'Given Tracking Number has already been modified!'];
            }
            $bag_shipment = BagShipment::where('shipment_id', $shipment->id);

            if ($bag_shipment->exists()) {
                $bag_shipment = $bag_shipment->where('status', 0);

                if ($bag_shipment->exists()) {
                    $bag_shipment = $bag_shipment->latest()->first();
                    $bag = $bag_shipment->bag;
                    if($bag->status_id == 4 || $bag->status_id == 7){
                        if(session('role_id') != 1){
                            if (!in_array($bag->destination_hub->hub_id, session('hubs'))) {
                                return ['status' => 1, 'error' => 'Shipment Bag doesn\'t belong to your assigned hub(s)!'];
                            }
                        }
                        if(!$request->has('pieces_confirm')){
                            if($shipment->booking_type_id == 1 && $shipment->pieces > 1){
                                $details = array();
                                $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                $details['id'] = $shipment->id;
                                $details['tracking_number'] = $shipment->tracking_number;
                                $details['pieces_count'] = $shipment->pieces;
                                $details['pieces_tracking_numbers'] = $shipment_pieces;
                                ShipmentScanningJourneyController::add($shipment->id ,20,1,Auth::id(),NULL,NULL,NULL,NULL, session('latitude'), session('longitude'), NULL);

                                return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                            }
                        }
                        $details = array();

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['bag_number'] = $bag->seal_number;
                        $details['origin'] = $shipment->pickup_address->city->name;
                        $details['destination'] = $shipment->consignee_city->name;
                        $details['hub'] = $shipment->consignee_city->hub_city->name;
                        $details['consignee'] = $shipment->consignee_name;
                        $details['shipping_mode'] = $shipment->shipping_mode->mode;
                        $details['amount'] = number_format($shipment->amount);
                        $details['service_type'] = $shipment->booking_type->booking_type;
                        ShipmentScanningJourneyController::add($shipment->id ,20,1,Auth::id(),NULL,NULL,NULL,NULL, session('latitude'), session('longitude'), NULL);
                        return ['status' => 0, 'success' => 'Bag has been added', 'details' => $details];
                    }
                    else {
                        return ['status' => 1, 'error' => 'Given Tracking Number Bag is not received yet or already modified'];
                    }
                }
                else {
                    return ['status' => 1, 'error' => 'Given Bag Number\'s has already been Received'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'Given Tracking Number is not in any Bag'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'Invalid Tracking Number'];
        }
    }

    public function master_cargo_bag_quick_receive_store(Request $request){
        $shipment_ids = array_unique(explode(',', $request->shipment_ids));
        $bag_ids = array();
        foreach ($shipment_ids as $shipment_id) {
            $bag_shipment = BagShipment::where('shipment_id', $shipment_id)->where('status', 0);

            if ($bag_shipment->exists()) {
                $bag_shipment = $bag_shipment->first();

                $bag_shipment->status = 1;

                $bag_shipment->save();

                $shipment = Shipment::find($shipment_id);
                $bag = $bag_shipment->bag;

                $shipper_status_id = NULL;
                $consignee_status_id = NULL;

                if ($bag->type == 1) {
                    if ($shipment->booking_type_id == 4 && $shipment->walk_in_delivery_type_id == 2) {
                        ShipmentsJourneyController::add($shipment_id, 4, 4, NULL, NULL, NULL, Auth::id());
                        $shipper_status_id = 15;
                        $consignee_status_id = 15;
                    }
                    else {
                        $shipper_status_id = 4;
                        $consignee_status_id = 4;
                    }
                }
                else {
                    if ($shipment->booking_type_id == 1) {
                        $shipper_status_id = 22;
                        $consignee_status_id = 22;
                    }
                    else if ($shipment->booking_type_id == 2) {
                        if($shipment->shipper_status_id == 21){
                            $shipper_status_id = 22;
                            $consignee_status_id = 22;
                        }
                        else{
                            $shipper_status_id = 27;
                            $consignee_status_id = 27;
                        }

                    }
                    else if ($shipment->booking_type_id == 3) {
                        $shipper_status_id = 33;
                        $consignee_status_id = 33;
                    }
                    else if ($shipment->booking_type_id == 4) {
                        $shipper_status_id = 22;
                        $consignee_status_id = 22;
                    }
                    else {
                        $shipper_status_id = 22;
                        $consignee_status_id = 22;
                    }
                }

                $shipment->shipper_status_id = $shipper_status_id;
                $shipment->consignee_status_id = $consignee_status_id;
                $shipment->save();

                ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $consignee_status_id, NULL, NULL, NULL, Auth::id());

                if(!in_array($bag->id, $bag_ids)){
                    $bag_ids[] = $bag->id;
                }
            }
        }

        $all_bag_ids = '';
        foreach ($bag_ids as $bag_id){
            $bag = Bag::find($bag_id);
            $bag->received_shipments = BagShipment::where('bag_id', $bag_id)->where('status', 1)->count();

            $short_received = BagShipment::where('bag_id', $bag_id)->where('status', 0)->count();

            if ($short_received > 0) {
                $bag->short_received = $short_received;
                $status_id = 7;
            }
            else {
                $bag->short_received = 0;
                $status_id = 9;
            }
            $bag->status_id = $status_id;
            $bag->received_at = Carbon::now();
            $bag->receiver_id = Auth::id();
            $bag->save();

            //dispute for short received
            if($bag->status_id == 7){
                $bag_short_received_shipments = BagShipment::where(['bag_id'=>$bag_id,'status'=>0])->pluck('shipment_id')->toArray();

                if(!empty($bag_short_received_shipments)){
                    DisputeController::add_cargo_short_received($bag->seal_number,$bag_short_received_shipments, 2);
                }
            }

//        end dispute short received
            if($all_bag_ids == ''){
                $all_bag_ids = $all_bag_ids . $bag->seal_number;
            }
            else{
                $all_bag_ids = $all_bag_ids . ', ' .$bag->seal_number;
            }
        }
        return redirect()->back()->with('success', 'Selected Shipments of Bag Number(s)#' . $all_bag_ids . ' has been Received');
    }

    public function master_cargo_quick_receive_list_index(){
        return view('admin.master_cargo.quick_receive_index');
    }

    public function master_cargo_quick_receive_list_ajax(Request $request){
        $cargo_consignment_excel = MasterCargoExcel::leftjoin('master_cargo_bag_excels as mcbe', 'mcbe.master_cargo_excel_id', '=', 'master_cargo_excels.id')
            ->leftjoin('admins as a', 'a.id', '=', 'master_cargo_excels.created_by')
            ->select('master_cargo_excels.id', 'master_cargo_excels.created_at as created_at', 'a.name as created_by', 'master_cargo_excels.bags as bags', 'master_cargo_excels.cargoes as cargoes', 'master_cargo_excels.excel as excel_text')
            ->groupBy('master_cargo_excels.id');

        $datatables = Datatables::of($cargo_consignment_excel)
            ->addColumn('bags_button', function ($cargo_consignment) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $cargo_consignment->bags . '</button>';
            })
            ->addColumn('cargoes_button', function ($cargo_consignment) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $cargo_consignment->cargoes . '</button>';
            })
            ->addColumn('excel_button', function ($cargo_consignment) {
                $file = Storage::disk('s3')->temporaryUrl('cargo_consignment_excels/'.$cargo_consignment->excel_text, now()->addMinutes(60));

                return '<a href="' . $file . '" target="_blank"><button class="btn btn-sm btn-info align-middle">Download</button></a>';
            });

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatables->whereBetween('master_cargo_excels.created_at', [$from,$to]);
        }
        return $datatables->make(true);
    }

    public function master_cargo_quick_receive_list_details(Request $request){
        $cargo_excel_id = $request->input('cargo_excel_id');
        $status = $request->input('bags');
        $details = array();
        $cargo_bag_excels = MasterCargoBagExcel::where('master_cargo_excel_id', $cargo_excel_id)->groupBy('master_cargo_id')->get();
        if($status == 1){
            foreach ($cargo_bag_excels as $cargo_bag_excel){
                $cargo_consignment = MasterCargo::find($cargo_bag_excel->master_cargo_id);
                foreach ($cargo_consignment->master_bags as $cargo_bag) {
                    $details[] = $cargo_bag->bag->seal_number;
                }
            }
        }
        else{
            foreach ($cargo_bag_excels as $cargo_bag_excel){
                $details[] = str_pad($cargo_bag_excel->master_cargo_id, 6, '0', STR_PAD_LEFT);
            }
        }
        return ['status' => 0, 'success' => 'Master Cargo Excel Details', 'details' => $details];
    }

    public function master_cargo_received_index(){
        return redirect()->route('admin.access_denied');
        $shipping_mode = ShippingMode::all();
        $cargo_status = MasterCargoStatus::all();
        $transport_vendor = TransportModeVendor::all();
        $transport_mode = TransportMode::all();
        return view('admin.master_cargo.received')->with(['shipping_mode'=>$shipping_mode,'cargo_status'=>$cargo_status,'transport_mode'=>$transport_mode,'transport_vendor'=>$transport_vendor]);
    }

    public function master_cargo_received_list(Request $request){
        $receive_cargo = MasterCargo::join('cities as oh', 'master_cargoes.origin_hub_id', '=', 'oh.id')
            ->join('cities as dh', 'master_cargoes.destination_hub_id', '=', 'dh.id')
            ->leftJoin('shipping_modes as sm', 'master_cargoes.shipping_mode_id', '=', 'sm.id')
            ->join('admins as a', 'master_cargoes.created_by', '=', 'a.id')
            ->join('master_cargo_statuses as mcs', 'master_cargoes.status_id', '=', 'mcs.id')
            ->leftjoin('fleets as f', 'master_cargoes.fleet_id', '=', 'f.id')
            ->leftjoin('route_managements as rm', 'master_cargoes.route_management_id', '=', 'rm.id')
            // ->leftjoin('cities as jh1', 'master_cargoes.junction_hub_1_id', '=', 'jh1.id')
            // ->leftjoin('cities as jh2', 'master_cargoes.junction_hub_2_id', '=', 'jh2.id')
            ->leftjoin('transport_modes as tm', 'master_cargoes.transport_mode_id', '=', 'tm.id')
            ->leftjoin('transport_mode_vendors as tmv', 'master_cargoes.transport_mode_vendor_id', '=', 'tmv.id')
            ->select('master_cargoes.id', 'master_cargoes.status_id', 'oh.id as origin_id', 'oh.name as origin', 'dh.id as destination_id', 'dh.name as destination', 'master_cargoes.shipments', 'master_cargoes.bags', 'master_cargoes.short_received_bags', 'master_cargoes.driver_name', 'f.reg_number as vehicle', 'master_cargoes.phone_number', 'sm.mode as shipping_mode', 'tm.name as transport_mode', 'tmv.name as vendor', 'master_cargoes.bags_weight', 'master_cargoes.actual_weight', 'master_cargoes.created_at as transit_at', 'a.name as transitted_by', 'mcs.name as status', 'oh.hub_id as origin_hub_id', 'dh.hub_id as destination_hub_id', 'master_cargoes.route_management_id as route_management_id')
            // ->select('master_cargoes.id', 'master_cargoes.status_id', 'oh.id as origin_id', 'oh.name as origin', 'dh.id as destination_id', 'dh.name as destination', 'master_cargoes.shipments', 'master_cargoes.bags', 'master_cargoes.short_received_bags', 'master_cargoes.driver_name', 'master_cargoes.vehicle', 'master_cargoes.phone_number', 'sm.mode as shipping_mode', 'jh1.name as junction_1', 'jh2.name as junction_2', 'tm.name as transport_mode', 'tmv.name as vendor', 'master_cargoes.bags_weight', 'master_cargoes.actual_weight', 'master_cargoes.created_at as transit_at', 'a.name as transitted_by', 'mcs.name as status', 'oh.hub_id as origin_hub_id', 'dh.hub_id as destination_hub_id')
            ->where('master_cargoes.status_id', 2);

        if (session('role_id') != 1) {
            $receive_cargo = $receive_cargo->leftjoin('route_management_junctions as rmj', 'rm.id', '=', 'rmj.route_management_id')->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->whereIn('oh.hub_id', session('hubs'))
                        ->orWhereIn('dh.hub_id', session('hubs'))
                        ->orWhereIn('rmj.junction_id', session('hubs'))
                        ->orWhere('a.id', Auth::id());
                });
            });
        }

        $datatables = Datatables::of($receive_cargo)
            ->addColumn('id_padded', function ($master_cargo) {
                return str_pad($master_cargo->id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('id_padded_link', function ($master_cargo) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($master_cargo->id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->addColumn('bags_count', function ($master_cargo) {
                return $master_cargo->bags;
            })
            ->addColumn('shipments_count', function ($master_cargo) {
                return $master_cargo->shipments;
            })
            ->addColumn('short_received_bags_count', function ($master_cargo) {
                return $master_cargo->short_received_bags;
            })
            ->addColumn('bags', function ($master_cargo) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $master_cargo->bags . '</button>';
            })
            ->addColumn('short_received_bags', function ($master_cargo) {
                if($master_cargo->short_received_bags > 0){
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $master_cargo->short_received_bags . '</button>';
                }
                else{
                    return '-';
                }
            })
            ->addColumn('excel_junctions', function ($master_cargo) {
                $master_cargo = MasterCargo::find($master_cargo->id);
                $junctions = '';
                if($master_cargo->route_management_id){
                    foreach ($master_cargo->route_management->junctions as $value) {
                        $junctions .=  $value->junction->name.' , ';
                    }  
                }
                    

                    return $junctions;
                
            })
            ->addColumn('junctions', function ($master_cargo) {
                $master_cargo = MasterCargo::find($master_cargo->id);
                if($master_cargo->route_management_id){
                    
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . count($master_cargo->route_management->junctions) . '</button>';
                }
            
            })
            ->addColumn('shipments', function ($master_cargo) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $master_cargo->shipments . '</button>';
            })
            ->filterColumn('master_cargoes.id', function ($query, $keyword) {
                return $query->where('master_cargoes.id', '=', $keyword);
            })
            ->filterColumn('shipping_mode',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('sm.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('mcs.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            });

        if(($request->tracking_number != null && $request->tracking_number != '') || $request->bag_number != null && $request->bag_number != ''){
            $datatables->join('master_cargo_bags as mcb', 'master_cargoes.id', '=', 'mcb.master_cargo_id')
                ->join('bags as b', 'b.id', '=', 'mcb.bag_id');
            if ($tracking_number = $request->get('tracking_number')) {
                $datatables->join('bag_shipments as bs', 'b.id', '=', 'bs.bag_id')
                    ->join('shipments as s', 'bs.shipment_id', '=', 's.id')
                    ->where('s.tracking_number', '=', $tracking_number);
            }

            if ($bag_number = $request->get('bag_number')) {
                $datatables->where('b.seal_number', '=', $bag_number);
            }
        }

        return $datatables->make(true);
    }

    public function master_cargo_history_index(){
        return redirect()->route('admin.access_denied');
        $shipping_mode = ShippingMode::all();
        $cargo_status = MasterCargoStatus::all();
        $transport_vendor = TransportModeVendor::all();
        $transport_mode = TransportMode::all();
        return view('admin.master_cargo.history')->with(['shipping_mode'=>$shipping_mode,'cargo_status'=>$cargo_status,'transport_mode'=>$transport_mode,'transport_vendor'=>$transport_vendor]);
    }

    public function master_cargo_history_list(Request $request){
        $receive_cargo = MasterCargo::join('cities as oh', 'master_cargoes.origin_hub_id', '=', 'oh.id')
            ->join('cities as dh', 'master_cargoes.destination_hub_id', '=', 'dh.id')
            ->leftJoin('shipping_modes as sm', 'master_cargoes.shipping_mode_id', '=', 'sm.id')
            ->join('admins as a', 'master_cargoes.created_by', '=', 'a.id')
            ->join('master_cargo_statuses as mcs', 'master_cargoes.status_id', '=', 'mcs.id')
            ->leftjoin('fleets as f', 'master_cargoes.fleet_id', '=', 'f.id')
            ->leftjoin('route_managements as rm', 'master_cargoes.route_management_id', '=', 'rm.id')

            // ->leftjoin('cities as jh1', 'master_cargoes.junction_hub_1_id', '=', 'jh1.id')
            // ->leftjoin('cities as jh2', 'master_cargoes.junction_hub_2_id', '=', 'jh2.id')
            
            ->leftjoin('transport_modes as tm', 'master_cargoes.transport_mode_id', '=', 'tm.id')
            ->leftjoin('transport_mode_vendors as tmv', 'master_cargoes.transport_mode_vendor_id', '=', 'tmv.id')
            ->select('master_cargoes.id', 'master_cargoes.status_id', 'oh.id as origin_id', 'oh.name as origin', 'dh.id as destination_id', 'dh.name as destination', 'master_cargoes.shipments', 'master_cargoes.bags', 'master_cargoes.short_received_bags', 'master_cargoes.driver_name', 'f.reg_number as vehicle', 'master_cargoes.phone_number', 'sm.mode as shipping_mode', 'tm.name as transport_mode', 'tmv.name as vendor', 'master_cargoes.bags_weight', 'master_cargoes.actual_weight', 'master_cargoes.created_at as transit_at', 'a.name as transitted_by', 'mcs.name as status', 'oh.hub_id as origin_hub_id', 'dh.hub_id as destination_hub_id');
            // ->select('master_cargoes.id', 'master_cargoes.status_id', 'oh.id as origin_id', 'oh.name as origin', 'dh.id as destination_id', 'dh.name as destination', 'master_cargoes.shipments', 'master_cargoes.bags', 'master_cargoes.short_received_bags', 'master_cargoes.driver_name', 'master_cargoes.vehicle', 'master_cargoes.phone_number', 'sm.mode as shipping_mode', 'jh1.name as junction_1', 'jh2.name as junction_2', 'tm.name as transport_mode', 'tmv.name as vendor', 'master_cargoes.bags_weight', 'master_cargoes.actual_weight', 'master_cargoes.created_at as transit_at', 'a.name as transitted_by', 'mcs.name as status', 'oh.hub_id as origin_hub_id', 'dh.hub_id as destination_hub_id');

        // if (session('role_id') != 1) {
        //     $receive_cargo = $receive_cargo->where(function ($query) {
        //         $query->whereIn('oh.hub_id', session('hubs'))->orWhereIn('dh.hub_id', session('hubs'))->orWhereIn('master_cargoes.junction_hub_1_id', session('hubs'))->orWhereIn('master_cargoes.junction_hub_1_id', session('hubs'));
        //     });
        // }
        

        if (session('role_id') != 1) {
            $receive_cargo = $receive_cargo->leftjoin('route_management_junctions as rmj', 'rm.id', '=', 'rmj.route_management_id')->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->whereIn('oh.hub_id', session('hubs'))
                        ->orWhereIn('dh.hub_id', session('hubs'))
                        ->orWhereIn('rmj.junction_id', session('hubs'))
                        ->orWhere('a.id', Auth::id());
                });
            });
        }
        $datatables = Datatables::of($receive_cargo)
            ->addColumn('id_padded', function ($master_cargo) {
                return str_pad($master_cargo->id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('id_padded_link', function ($master_cargo) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($master_cargo->id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->addColumn('bags_count', function ($master_cargo) {
                return $master_cargo->bags;
            })
            ->addColumn('shipments_count', function ($master_cargo) {
                return $master_cargo->shipments;
            })
            ->addColumn('short_received_bags_count', function ($master_cargo) {
                return $master_cargo->short_received_bags;
            })
            ->addColumn('bags', function ($master_cargo) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $master_cargo->bags . '</button>';
            })
            ->addColumn('short_received_bags', function ($master_cargo) {
                if($master_cargo->short_received_bags > 0){
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $master_cargo->short_received_bags . '</button>';
                }
                else{
                    return '-';
                }
            })
            ->addColumn('shipments', function ($master_cargo) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $master_cargo->shipments . '</button>';
            })
            ->addColumn('excel_junctions', function ($master_cargo) {
                $master_cargo = MasterCargo::find($master_cargo->id);
                $junctions = '';
                if($master_cargo->route_management_id){
                    foreach ($master_cargo->route_management->junctions as $value) {
                        $junctions .=  $value->junction->name.'  ';
                    }  
                }
                    

                    return $junctions;
                
            })
            ->addColumn('junctions', function ($receive_cargo) {
                $master_cargo = MasterCargo::find($receive_cargo->id);
                if($master_cargo->route_management_id){
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . count($master_cargo->route_management->junctions) . '</button>';
                }
            })
            ->filterColumn('master_cargoes.id', function ($query, $keyword) {
                return $query->where('master_cargoes.id', '=', $keyword);
            })
            ->filterColumn('shipping_mode',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('sm.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('mcs.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            });

        if(($request->tracking_number != null && $request->tracking_number != '') || $request->bag_number != null && $request->bag_number != ''){
            $datatables->join('master_cargo_bags as mcb', 'master_cargoes.id', '=', 'mcb.master_cargo_id')
                ->join('bags as b', 'b.id', '=', 'mcb.bag_id');
            if ($tracking_number = $request->get('tracking_number')) {
                $datatables->join('bag_shipments as bs', 'b.id', '=', 'bs.bag_id')
                    ->join('shipments as s', 'bs.shipment_id', '=', 's.id')
                    ->where('s.tracking_number', '=', $tracking_number);
            }

            if ($bag_number = $request->get('bag_number')) {
                $datatables->where('b.seal_number', '=', $bag_number);
            }
        }

        return $datatables->make(true);
    }

    public function master_cargo_in_transit_bag_index() {
        return redirect()->route('admin.access_denied');

        $shipping_mode = ShippingMode::all();
        $transport_vendor = TransportModeVendor::all();
        $transport_mode = TransportMode::all();
        $bag_statuses = BagStatus::all();
        return view('admin.master_cargo.bag.in_transit')->with(['shipping_mode'=>$shipping_mode,'transport_mode'=>$transport_mode,'transport_vendor'=>$transport_vendor,'bag_statuses'=>$bag_statuses]);
    }

    public function master_cargo_in_transit_bag_list(Request $request) {
        $bags = Bag::join('master_cargo_bags as mcb', function ($join) {
            $join->on('mcb.bag_id', '=', 'bags.id')
                ->where('mcb.id', '=',
                    DB::raw('(select max(id) from master_cargo_bags where master_cargo_bags.bag_id = bags.id)'));
        })
            ->join('master_cargoes as mc', 'mc.id', '=', 'mcb.master_cargo_id')
            ->join('admins as ra', 'ra.id', '=', 'mc.received_by')
            ->join('cities as oh', 'bags.origin_hub_id', '=', 'oh.id')
            ->join('cities as dh', 'bags.destination_hub_id', '=', 'dh.id')
            ->leftJoin('shipping_modes as sm', 'bags.shipping_mode_id', '=', 'sm.id')
            ->join('admins as a', 'bags.created_by', '=', 'a.id')
            ->leftjoin('cities as jh1', 'bags.junction_hub_1_id', '=', 'jh1.id')
            ->leftjoin('cities as jh2', 'bags.junction_hub_2_id', '=', 'jh2.id')
            ->join('transport_modes as tm', 'bags.transport_mode_id', '=', 'tm.id')
            ->join('transport_mode_vendors as tmv', 'bags.transport_mode_vendor_id', '=', 'tmv.id')
            ->join('bag_statuses as bs', 'bags.status_id', '=', 'bs.id')
            // ->select('bags.id', 'bags.status_id', 'oh.id as origin_id', 'oh.name as origin', 'dh.id as destination_id', 'dh.name as destination', 'bags.shipments', 'bags.quantity', 'sm.mode as shipping_mode', 'tm.name as transport_mode', 'tmv.name as vendor','bags.shipments_weight', DB::raw('(SELECT SUM(`s`.`chargeable_weight`) FROM `shipments` AS `s` INNER JOIN `bag_shipments` AS `bss` ON `s`.`id` = `bss`.`shipment_id` WHERE `bss`.`bag_id` = `bags`.`id`) AS `chargeable_weight`'), 'bags.actual_weight', 'bags.created_at as transit_at', 'a.name as transitted_by', 'oh.hub_id as origin_hub_id', 'dh.hub_id as destination_hub_id', 'bags.type as bag_type','bags.seal_number', 'mc.id as master_cargo_id', 'mc.received_at as cargo_received_at', 'ra.name as received_by', 'bs.name as status', 'bags.short_received')
            ->select('bags.id', 'bags.status_id', 'oh.id as origin_id', 'oh.name as origin', 'dh.id as destination_id', 'dh.name as destination', 'bags.shipments', 'bags.quantity', 'sm.mode as shipping_mode', 'jh1.name as junction_1', 'jh2.name as junction_2', 'tm.name as transport_mode', 'tmv.name as vendor','bags.shipments_weight', DB::raw('(SELECT SUM(`s`.`chargeable_weight`) FROM `shipments` AS `s` INNER JOIN `bag_shipments` AS `bss` ON `s`.`id` = `bss`.`shipment_id` WHERE `bss`.`bag_id` = `bags`.`id`) AS `chargeable_weight`'), 'bags.actual_weight', 'bags.created_at as transit_at', 'a.name as transitted_by', 'oh.hub_id as origin_hub_id', 'dh.hub_id as destination_hub_id', 'bags.type as bag_type','bags.seal_number', 'mc.id as master_cargo_id', 'mc.received_at as cargo_received_at', 'ra.name as received_by', 'bs.name as status', 'bags.short_received')
            ->whereIn('bags.status_id', [3, 4, 5, 6, 7]);

        if (session('role_id') != 1) {
            $bags = $bags->where(function ($query) {
                $query->whereIn('oh.hub_id', session('hubs'))->orWhereIn('dh.hub_id', session('hubs'))->orWhereIn('bags.junction_hub_1_id', session('hubs'))->orWhereIn('bags.junction_hub_1_id', session('hubs'));
            });
        }

        $datatables = Datatables::of($bags)
            ->addColumn('aging',function ($bag){

                $days = Carbon::now()->diffInDays($bag->cargo_received_at);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            })
            ->addColumn('shipments_count', function ($bag) {
                return $bag->shipments;
            })
            ->editColumn('bag_type',function ($bag){
                if($bag->bag_type == 1){
                    return 'Normal';
                }else{
                    return 'Return';
                }
            })
            ->addColumn('short_received_shipments', function ($master_cargo) {
                if($master_cargo->short_received > 0){
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $master_cargo->short_received . '</button>';
                }
                else{
                    return '-';
                }
            })
            ->addColumn('id_padded', function ($bag) {
                return str_pad($bag->master_cargo_id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('id_padded_link', function ($bag) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($bag->master_cargo_id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->addColumn('shipments', function ($bag) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $bag->shipments . '</button>';
            })
            ->filterColumn('bags.seal_number', function ($query, $keyword) {
                return $query->where('bags.seal_number', '=', $keyword);
            })
            ->filterColumn('shipping_mode',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('sm.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('status',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where('bs.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            });

        if ($bag_type = $request->get('bag_type')) {
            if ($bag_type != 0) {
                $datatables->where('bags.type', $bag_type);
            }
        }

        if ($tracking_number = $request->get('tracking_number')) {
            $datatables->join('bag_shipments as bssh', 'bags.id', '=', 'bssh.bag_id')
                ->join('shipments as s', 'bssh.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
        }

        if ($bag_number = $request->get('bag_number')) {
            $datatables->where('bags.seal_number', '=', $bag_number);
        }

        return $datatables->make(true);
    }

    public function master_cargo_in_transit_bag_receive(Request $request){
        if ($request->has('bag_number')) {
            $bag = Bag::where('seal_number', $request->get('bag_number'))->first();

            if ($bag) {
                if (session('role_id') == 1 || (in_array($bag->destination_hub->hub_id, session('hubs')))) {
                    if (in_array($bag->status_id, [3, 4, 5, 6, 7])) {
                        return redirect()->route('admin.master_cargo.bag.receive.index')->with('bag_number', $bag->id);
                    }
                    else {
                        return back()->withErrors('Given bag Number has already been modified!');
                    }
                }
                else {
                    return back()->withErrors('Bag doesn\'t belong to your assigned hub(s)!');
                }
            }
            else {
                return back()->withErrors('Invalid Bag Number!');
            }
        }
        else {
            return back()->withErrors('Missing Bag Number!');
        }
    }

    public function master_cargo_bag_receive_index() {
        return redirect()->route('admin.access_denied');

        if (session('bag_number')) {
            $bag = Bag::find(session('bag_number'));
            $total = BagShipment::where('bag_id', session('bag_number'))->where('status', 0)->count();

            return view('admin.master_cargo.bag.receive')->with(['total' => $total, 'seal_number' => $bag->seal_number]);
        }
        else {
            return redirect()->route('admin.master_cargo.bag.in_transit.index')->withErrors('Kindly reselect a Bag Number!');
        }
    }

    public function master_cargo_bag_receive_shipment_details(Request $request) {

        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();

            $bag_shipment = BagShipment::where('shipment_id', $shipment->id);

            if ($bag_shipment->exists()) {
                $bag_shipment = $bag_shipment->where('bag_id', $request->bag_id);

                if ($bag_shipment->exists()) {
                    $bag_shipment = $bag_shipment->where('status', 0);

                    if ($bag_shipment->exists()) {
                        $bag_shipment = $bag_shipment->first();

                        $consignee_city = $shipment->consignee_city;

                        if(!$request->has('pieces_confirm')){
                            if($shipment->booking_type_id == 1 && $shipment->pieces > 1){
                                $details = array();
                                $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                $details['id'] = $shipment->id;
                                $details['tracking_number'] = $shipment->tracking_number;
                                $details['pieces_count'] = $shipment->pieces;
                                $details['pieces_tracking_numbers'] = $shipment_pieces;
                                return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                            }
                        }
                        $details = array();

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['origin'] = $shipment->pickup_address->city->name;
                        $details['destination'] = $consignee_city->name;
                        $details['hub'] = $consignee_city->hub_city->name;
                        $details['consignee'] = $shipment->consignee_name;
                        $details['shipping_mode'] = $shipment->shipping_mode->mode;
                        $details['amount'] = number_format($shipment->amount);
                        $details['service_type'] = $shipment->booking_type->booking_type;
                        ShipmentScanningJourneyController::add($shipment->id ,3,1,Auth::id(),NULL,NULL,NULL,NULL, session('latitude'), session('longitude'), NULL);
                        return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                    }
                    else {
                        return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been Received'];
                    }
                }
                else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment does not belong to current Bag Number'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is not in any Bag'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }

    public function master_cargo_bag_receive_short_received(Request $request) {
        $shipment_ids = array_unique($request->input('shipment_ids'));

        $bag_shipments = BagShipment::where('bag_id', $request->input('bag_id'))->where('status', 0);

        if ($bag_shipments->count() != count($shipment_ids)) {
            $bag_shipments = $bag_shipments->pluck('shipment_id')->toArray();

            $short_shipment_ids = array_diff($bag_shipments, $shipment_ids);

            $short_shipments = array();

            foreach ($short_shipment_ids as $short_shipment_id) {
                $shipment = Shipment::find($short_shipment_id);

                $short_shipments[] = $shipment->tracking_number;
            }

            return ['status' => 0, 'success' => 'Shipments found Short Received', 'short_received' => $short_shipments];
        }
        else {
            return ['status' => 0, 'success' => 'No Short Received Shipments', 'short_received' => FALSE];
        }
    }

    public function master_cargo_bag_receive_store(Request $request) {
        $bag_id = $request->bag_id;

        $shipment_ids = array_unique(explode(',', $request->shipment_ids));

        $open_box_ids = array_unique(explode(',', $request->open_box_ids));


        $bag = Bag::find($bag_id);

        foreach ($shipment_ids as $shipment_id) {
            $bag_shipment = BagShipment::where('bag_id', $bag_id)->where('shipment_id', $shipment_id)->first();
            if($bag_shipment->status != 1){
                $bag_shipment->status = 1;

                $bag_shipment->save();

                $shipment = Shipment::find($shipment_id);

                $shipper_status_id = NULL;
                $consignee_status_id = NULL;

                if ($bag->type == 1) {
                    if ($shipment->booking_type_id == 4 && $shipment->walk_in_delivery_type_id == 2) {
                        ShipmentsJourneyController::add($shipment_id, 4, 4, NULL, NULL, NULL, Auth::id());
                        $shipper_status_id = 15;
                        $consignee_status_id = 15;
                        NotificationsController::send(126,$shipment_id);
                    }
                    else {
                        $shipper_status_id = 4;
                        $consignee_status_id = 4;
                    }
                }
                else {
                    if ($shipment->booking_type_id == 1) {
                        $shipper_status_id = 22;
                        $consignee_status_id = 22;
                    }
                    else if ($shipment->booking_type_id == 2) {
                        if($shipment->shipper_status_id == 21){
                            $shipper_status_id = 22;
                            $consignee_status_id = 22;
                        }
                        else{
                            $shipper_status_id = 27;
                            $consignee_status_id = 27;
                        }
                    }
                    else if ($shipment->booking_type_id == 3) {
                        $shipper_status_id = 33;
                        $consignee_status_id = 33;
                    }
                    else if ($shipment->booking_type_id == 4) {
                        $shipper_status_id = 22;
                        $consignee_status_id = 22;
                    }
                    else {
                        $shipper_status_id = 22;
                        $consignee_status_id = 22;
                    }
                }

                $shipment->shipper_status_id = $shipper_status_id;
                $shipment->consignee_status_id = $consignee_status_id;

                $shipment->save();

                if(in_array($shipment_id, $open_box_ids)){
                    $shipment->open_box = 1;
                    $shipment->save();
                    ShipmentOpenBoxJourneyController::add($shipment_id,2,Auth::id());
                }

                ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $consignee_status_id, NULL, NULL, NULL, Auth::id());
                //Consolidated Shipments

                if ($bag->type == 1) {
                    $self_collection_shipment = SelfCollectionShipment::where('shipment_id', $shipment_id)->first();
                    if($self_collection_shipment) {
                        $shipment->shipper_status_id = 15;
                        $shipment->consignee_status_id = 15;

                        $shipment->save();
                        ShipmentsJourneyController::add($shipment_id, 15, 15, NULL, NULL, NULL, Auth::id());
                        NotificationsController::send(126,$shipment_id);
                    }
                    $consolidated_shipment = ConsolidationShipments::where('shipment_id', $shipment_id)->first();
                    if ($consolidated_shipment) {
                        $check_all_consolidation_shipments = true;

                        $shipment->shipper_status_id = 58;
                        $shipment->consignee_status_id = 58;
                        $shipment->save();

                        ShipmentsJourneyController::add($shipment_id, 58, 58, NULL, NULL, NULL, Auth::id());

                        $consolidation_id = $consolidated_shipment->consolidation_id;
                        $remaining_consolidated_shipments = ConsolidationShipments::where('consolidation_id', $consolidation_id)->get();

                        foreach ($remaining_consolidated_shipments as $remaining_consolidated_shipment) {
                            $check_remaining_consolidated_shipment = Shipment::find($remaining_consolidated_shipment->shipment_id);
                            if ($check_remaining_consolidated_shipment->shipper_status_id != 58) {
                                $check_all_consolidation_shipments = false;
                            }
                        }

                        if ($check_all_consolidation_shipments == true) {
                            foreach ($remaining_consolidated_shipments as $update_remaining_consolidated_shipment) {
                                $update_all_consolidated_shipment = Shipment::find($update_remaining_consolidated_shipment->shipment_id);

                                $update_all_consolidated_shipment->shipper_status_id = 59;
                                $update_all_consolidated_shipment->consignee_status_id = 59;

                                $update_all_consolidated_shipment->save();

                                ShipmentsJourneyController::add($update_remaining_consolidated_shipment->shipment_id, 59, 59, NULL, NULL, NULL, Auth::id());
                            }
                        }
                    }
                }
                //Consolidated Shipments
            }

        }

        $bag->received_shipments = BagShipment::where('bag_id', $bag_id)->where('status', 1)->count();

        $short_received = BagShipment::where('bag_id', $bag_id)->where('status', 0)->count();

        if ($short_received > 0) {
            $bag->short_received = $short_received;
            $bag->status_id = 7;
        }
        else {
            $bag->short_received = 0;
            $bag->status_id = 9;
        }

        $bag->receiver_id = Auth::id();
        $bag->save();

        MasterCargoBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), NULL, NULL);

        //dispute for short received
        if($bag->status_id == 7){
            $bag_short_received_shipments = BagShipment::where(['bag_id'=>$bag_id,'status'=>0])->pluck('shipment_id')->toArray();
            if(!empty($bag_short_received_shipments)){
                DisputeController::add_cargo_short_received($bag->seal_number,$bag_short_received_shipments,2);
            }
        }

//        end dispute short received
//        dispute start for junction

        //dispute end for junction
        return redirect()->route('admin.master_cargo.bag.in_transit.index')->with('success', 'Bag Number# ' . $bag->seal_number . ' has been Received');
    }

    public function master_cargo_in_transit_bag_short_received(Request $request) {
        $tracking_numbers = array();
        $bag = Bag::find($request->id);
        $bag_shipments = $bag->shipment;
        foreach ($bag_shipments as $bag_shipment){
            if($bag_shipment->status == 0){
                $tracking_numbers[] = $bag_shipment->shipment->tracking_number;
            }
        }

        return $tracking_numbers;
    }

    public function update_seal_number(Request $request){
        $existing = Bag::where('seal_number', $request->seal_number)->where('id', '!=', $request->id);
        if($existing->exists()){
            return ['status' => 0, 'error' => 'Seal Number must be unique!'];
        }
        else{
            $bag = Bag::find($request->id);
            $bag->seal_number = $request->seal_number;
            $bag->save();

            return ['status' => 1, 'success' => 'Seal Number updated successfully!'];
        }
    }

}
