<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\BookingType;
use App\Http\Models\CargoConsignmentStatus;
use App\Http\Models\DraftCargo;
use App\Http\Models\DraftCargoShipment;
use App\Http\Models\JunctionMapping;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\ShipmentStatus;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\NotificationsController;

use App\Http\Models\Shipment;
use App\Http\Models\City;
use App\Http\Models\ShippingMode;
use App\Http\Models\TransportMode;
use App\Http\Models\TransportModeVendor;
use App\Http\Models\Admin\Admin;
use App\Http\Models\CargoConsignment;
use App\Http\Models\CargoConsignmentShipment;
use App\Http\Models\CargoConsignmentJunctionReceival;

use Auth;
use DB;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class AdminCargoController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function pending_index() {
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $service_type = BookingType::all();
        $shipping_mode = ShippingMode::all();
        return view('admin.cargo.pending')->with(['shipment_status'=>$shipment_status,'service_type'=>$service_type,'shipping_mode'=>$shipping_mode]);
    }

    public function pending_list(Request $request) {
        $shipments = Shipment::join('booking_types as bt', 'shipments.booking_type_id', '=', 'bt.id')
            ->join('shipment_status as ss', 'shipments.shipper_status_id', '=', 'ss.id')
            ->join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
            ->join('shipping_modes as sm', 'shipments.shipping_mode_id', '=', 'sm.id')
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
                            $sub_query->whereIn('shipments.shipper_status_id', [2, 20, 30, 36, 37])
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

            ->select('shipments.shipper_status_id', 'shipments.tracking_number', 'shipments.tracking_number as tracking', 'shipments.order_id', 'bt.booking_type as service_type', 'ss.name as status', 'oc.name as origin', 'dc.name as destination', 'u.name as shipper', 'shipments.amount', 'sm.mode as shipping_mode', 'shipments.created_at as booked_at', 'shipments_journey.created_at as arrival_at', 'shipments.booking_type_id', 'usi.poc','csj.created_at as current_status', 'olddc.name as old_destination', 'olddci.name as old_destination_intercept','crm.id as complaint');

        if (session('role_id') != 1) {
            $shipments = $shipments->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->whereIn('shipments.shipper_status_id', [20, 30, 36, 37])
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
                if (in_array($shipments->shipper_status_id, [20, 30, 36, 37])) {
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
                if (in_array($shipments->shipper_status_id, [20, 30, 36, 37])) {
                    return $shipments->origin;
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
                    $sub_query->whereIn('shipments.shipper_status_id', [20, 30, 36, 37])
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
                    $sub_query->whereIn('shipments.shipper_status_id', [20, 30, 36, 37])
                        ->where('oc.name', 'like', '%' . $keyword . '%');
                })
                ->orWhere(function ($sub_query) use ($keyword) {
                    $sub_query->whereIn('shipments.shipper_status_id', [2, 49, 55])
                        ->where('dc.name', 'like', '%' . $keyword . '%');
                });
            })
            ->orderColumn('oc.name', DB::raw('IF (shipments.shipper_status_id IN (20, 30, 36, 37), dc.name, IF (shipments.shipper_status_id = 49, olddc.name, IF (shipments.shipper_status_id = 55, olddci.name, oc.name)))') . ' $1')
            ->orderColumn('dc.name', DB::raw('IF (shipments.shipper_status_id IN (20, 30, 36, 37), oc.name, dc.name)') . ' $1');

        if ($shipment_type = $request->get('shipment_type')) {
            if ($shipment_type == 0) {
                $datatables->whereIn('shipments.shipper_status_id', [2, 20, 30, 36, 37, 49, 55]);
            }
            else if ($shipment_type == 1) {
                $datatables->whereIn('shipments.shipper_status_id', [2, 49, 55]);
            }
            else if ($shipment_type == 2) {
                $datatables->whereIn('shipments.shipper_status_id', [20, 30, 36, 37]);
            }
        }
        else {
            $datatables->whereIn('shipments.shipper_status_id', [2, 20, 30, 36, 37, 49, 55]);
        }

        return $datatables->make(true);
    }

    public function create_index() {
        return view('admin.cargo.create')->with('print', session('print'));
    }

    public function create_shipment_details(Request $request) {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();


            $packaging_material_request = PackagingMaterialRequest::where('tracking_number',$shipment->tracking_number)->first();
            if($packaging_material_request != null){
                if($packaging_material_request->status_id != 3){
                    return ['status' => 1, 'error' => 'Packaging Material Request is not dispatched yet!'];
                }
            }

            if (in_array($shipment->shipper_status_id, [2, 20, 30, 36, 37, 49, 55])) {
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
                    if (($shipment->pickup_address->city->hub_id != $shipment->consignee_city->hub_id) || (in_array($shipment->shipper_status_id, [49, 55]) && ($shipment->consignee_city->hub_id != $hub_id) )) {
                        if ($request->cargo_type != 0) {
                            if (in_array($shipment->shipper_status_id, [2, 49, 55])) {
                                $hub_id = $shipment->consignee_city->hub_id;
                            }
                            else {
                                $hub_id = $shipment->pickup_address->city->hub_id;
                            }
                        }
                        else {
                            $hub_id = 0;
                        }

                        if ($request->hub_id == 0 || $request->hub_id == $hub_id) {
                            if ($request->shipping_mode_id == 0 || $request->shipping_mode_id == $shipment->shipping_mode->id) {
                                $details = array();

                                if ($request->cargo_type != 0) {
                                    if ($request->cargo_type == 1) {
                                        if (!in_array($shipment->shipper_status_id, [2, 49, 55])) {
                                            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of Return Type while the Cargo is Normal Type'];
                                        }

                                        $cargo_type = 1;
                                    }
                                    else {
                                        if (!in_array($shipment->shipper_status_id, [20, 30, 36, 37])) {
                                            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of Normal Type while the Cargo is Return Type'];
                                        }

                                        $cargo_type = 2;
                                    }
                                }
                                else {
                                    if (in_array($shipment->shipper_status_id, [2, 49, 55])) {
                                        $details['cargo_type'] = 1;

                                        $cargo_type = 1;
                                    }
                                    else {
                                        $details['cargo_type'] = 2;

                                        $cargo_type = 2;
                                    }
                                }

                                if ($cargo_type == 1) {
                                    $destination = $shipment->consignee_city;
                                }
                                else {
                                    $destination = $shipment->pickup_address->city;
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
                                    if ($cargo_type == 1) {
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
                                        $shipments = Shipment::join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
                                            ->join('cities as dc', 'usi.city_id', '=', 'dc.id')
                                            ->join('cities as oc', function($join) {
                                                $join->on('shipments.consignee_city_id', '=', 'oc.id')
                                                    ->on('dc.hub_id', '!=', 'oc.hub_id');
                                            })
                                            ->select(DB::raw('count(shipments.id) as count'))
                                            ->where('dc.hub_id', $hub->id)
                                            ->whereIn('shipments.shipper_status_id', [20, 30, 36, 37])
                                            ->where('shipments.shipping_mode_id', $shipping_mode_id);
                                    }

                                    if (session('role_id') != 1) {
                                        $shipments = $shipments->whereIn('oc.hub_id', session('hubs'));
                                    }

                                    $shipments = $shipments->first();

                                    $details['total'] = $shipments->count;
                                }

                                return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                            }
                            else {
                                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment\'s Shipment Mode is different'];
                            }
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

    public function create_consignment_details(Request $request) {

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
            $origin = $shipment->pickup_address->city->hub_city;
        }

        $origin_details = array();

        $origin_details['id'] = $origin->id;
        $origin_details['name'] = $origin->name;

        $destination = $shipment->consignee_city->hub_city;

        $destination_details = array();

        $destination_details['id'] = $destination->id;
        $destination_details['name'] = $destination->name;

        $details = array();

        $details['junctions'] = City::select(['id', 'name'])->where('hub', 1)->where('status', 1)->get();

        $details['transport_modes'] = TransportMode::all();

        $details['transport_mode_vendors'] = TransportModeVendor::get()->groupBy('transport_mode_id');

        $sender = Auth::user();

        $details['sender']['id'] = $sender->id;
        $details['sender']['name'] = $sender->name;

        $details['receivers'] = Admin::where('status', 1)->whereHas('hubs', function ($query) use($destination_details) {
            $query->where('hub_id',  $destination_details['id']);
        })->select(['id', 'name'])->get();

        if ($request->cargo_type == 1) {
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

    public function create_consignment_seal_number(Request $request) {
        if ($request->filled('seal_number')) {
            $seal_number = CargoConsignment::where('seal_number', $request->input('seal_number'));

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
        $shipments_weight = 0;

        $shipment_ids = explode(',', $request->input('shipment_ids'));

        foreach ($shipment_ids as $key => $shipment_id) {
            $shipment = Shipment::find($shipment_id);

            if (in_array($shipment->shipper_status_id, [2, 20, 30, 36, 37, 49, 55])) {
                $shipments++;
                $shipments_weight += $shipment->actual_weight;
            }
            else {
                unset($shipment_ids[$key]);
            }
        }

        if (!empty($shipment_ids)) {
            $cargo_consignment = new CargoConsignment();

            $cargo_consignment->origin_hub_id = $request->input('origin_hub_id');
            $cargo_consignment->destination_hub_id = $request->input('destination_hub_id');
            $cargo_consignment->junction_hub_1_id = $request->input('junction_1');
            $cargo_consignment->junction_hub_2_id = $request->input('junction_2');
            $cargo_consignment->seal_number = $request->input('seal_number');
            $cargo_consignment->shipping_mode_id = $request->input('shipping_mode_id');
            $cargo_consignment->transport_mode_id = $request->input('transport_mode');

            if ($request->input('transport_mode_vendor') == 0) {
                $transport_mode_vendor = new TransportModeVendor();

                $transport_mode_vendor->transport_mode_id = $request->input('transport_mode');
                $transport_mode_vendor->name = $request->input('vendor_name');

                $transport_mode_vendor->save();

                $cargo_consignment->transport_mode_vendor_id = $transport_mode_vendor->id;
            }
            else {
                $cargo_consignment->transport_mode_vendor_id = $request->input('transport_mode_vendor');
            }

            $cargo_consignment->shipments = $shipments;
            $cargo_consignment->shipments_weight = $shipments_weight;

            $cargo_consignment->actual_weight = $request->input('actual_weight');
            $cargo_consignment->sender_id = $request->input('sender_id');

            if ($request->filled('receiver_id')) {
                $cargo_consignment->receiver_id = $request->input('receiver_id');
            }

            $cargo_consignment->type = $request->input('cargo_type');

            $cargo_consignment->status_id = 1;

            $cargo_consignment->save();

            $id = $cargo_consignment->id;

            foreach ($shipment_ids as $shipment_id) {
                $cargo_consignment_shipment = new CargoConsignmentShipment();

                $cargo_consignment_shipment->cargo_consignment_id = $id;
                $cargo_consignment_shipment->shipment_id = $shipment_id;

                $cargo_consignment_shipment->save();

                $shipment = Shipment::find($shipment_id);

                $shipper_status_id = NULL;
                $consignee_status_id = NULL;

                if ($request->input('cargo_type') == 1) {
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

                ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $consignee_status_id, NULL, NULL, NULL, Auth::id(), $cargo_consignment->id, $cargo_consignment->builty_number);

                self::check_draft_shipments($shipment_id,null);

                NotificationsController::send(5, $id, $shipment_id);

                NotificationsController::send(6, $id, $shipment_id);
            }

            NotificationsController::send(9, $id);

            if ($request->filled('submit_and_print_form')) {
                $print = $id;
            }
            else {
                $print = FALSE;
            }

            return redirect()->route('admin.cargo.create.index')->with(['success' => 'Cargo Booked with Number: ' . $id, 'print' => $print]);
        }
        else {
            return back()->withErrors('All Shipments have already been added to another Cargo!');
        }
    }

    public function in_transit_index() {
        $shipping_mode = ShippingMode::all();
        $cargo_status = CargoConsignmentStatus::all();
        $transport_vendor = TransportModeVendor::all();
        $transport_mode = TransportMode::all();
        return view('admin.cargo.in_transit')->with(['shipping_mode'=>$shipping_mode,'cargo_status'=>$cargo_status,'transport_mode'=>$transport_mode,'transport_vendor'=>$transport_vendor]);
    }

    public function in_transit_list(Request $request) {
        $cargo_consignments = CargoConsignment::join('cities as oh', 'cargo_consignments.origin_hub_id', '=', 'oh.id')
            ->join('cities as dh', 'cargo_consignments.destination_hub_id', '=', 'dh.id')
            ->join('shipping_modes as sm', 'cargo_consignments.shipping_mode_id', '=', 'sm.id')
            ->join('admins as a', 'cargo_consignments.sender_id', '=', 'a.id')
            ->join('cargo_consignment_status as ccs', 'cargo_consignments.status_id', '=', 'ccs.id')
            ->join('cities as jh1', 'cargo_consignments.junction_hub_1_id', '=', 'jh1.id')
            ->leftjoin('cities as jh2', 'cargo_consignments.junction_hub_2_id', '=', 'jh2.id')
            ->join('transport_modes as tm', 'cargo_consignments.transport_mode_id', '=', 'tm.id')
            ->join('transport_mode_vendors as tmv', 'cargo_consignments.transport_mode_vendor_id', '=', 'tmv.id')
            ->select('cargo_consignments.id', 'cargo_consignments.status_id', 'oh.id as origin_id', 'oh.name as origin', 'dh.id as destination_id', 'dh.name as destination', 'cargo_consignments.shipments', 'sm.mode as shipping_mode', 'jh1.name as junction_1', 'jh2.name as junction_2', 'tm.name as transport_mode', 'tmv.name as vendor', 'cargo_consignments.builty_number', 'cargo_consignments.shipments_weight', DB::raw('(SELECT SUM(`s`.`chargeable_weight`) FROM `shipments` AS `s` INNER JOIN `cargo_consignment_shipments` AS `css` ON `s`.`id` = `css`.`shipment_id` WHERE `css`.`cargo_consignment_id` = `cargo_consignments`.`id`) AS `chargeable_weight`'), 'cargo_consignments.actual_weight', 'cargo_consignments.vendor_weight', 'cargo_consignments.created_at as transit_at', 'a.name as transitted_by', 'ccs.name as status', 'oh.hub_id as origin_hub_id', 'dh.hub_id as destination_hub_id', 'cargo_consignments.type as cargo_type','cargo_consignments.seal_number')
            ->whereIn('cargo_consignments.status_id', [1, 2, 4, 6, 7]);

        if (session('role_id') != 1) {
            $cargo_consignments = $cargo_consignments->where(function ($query) {
                $query->whereIn('oh.hub_id', session('hubs'))->orWhereIn('dh.hub_id', session('hubs'))->orWhereIn('cargo_consignments.junction_hub_1_id', session('hubs'))->orWhereIn('cargo_consignments.junction_hub_1_id', session('hubs'));
            });
        }

        $datatables = Datatables::of($cargo_consignments)
            ->addColumn('aging',function ($cargo_consignment){

                $days = Carbon::now()->diffInDays($cargo_consignment->transit_at);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            })
            ->addColumn('id_padded', function ($cargo_consignment) {
                return str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('id_padded_link', function ($cargo_consignment) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->addColumn('shipments_count', function ($cargo_consignment) {
                return $cargo_consignment->shipments;
            })
            ->editColumn('cargo_type',function ($cargo_received){
                if($cargo_received->cargo_type == 1){
                    return 'Normal';
                }else{
                    return 'Return';
                }
            })
            ->addColumn('shipments', function ($cargo_consignment) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $cargo_consignment->shipments . '</button>';
            })
            ->filterColumn('cargo_consignments.id', function ($query, $keyword) {
                return $query->where('cargo_consignments.id', '=', $keyword);
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
                    $query->where('ccs.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })

            ->addColumn('action', function($cargo_consignment) {

                $print_button = '<button type="button" class="dropdown-item print"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-printer"></i></div><div class="col-9 offset-1">Print</div></button>';
                $add_forwarding_details_button = '<button type="button" class="dropdown-item add_forwarding_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update Forwarding Details</div></button>';
                $view_forwarding_details_button = '<button type="button" class="dropdown-item view_forwarding_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Forwarding Details</div></button>';
                $launch_dispute_button = '<button type="button" class="dropdown-item launch_dispute"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Launch Dispute</div></button>';
                $receive_button = '<button type="button" class="dropdown-item receive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Receive</div></button>';

                $dropdown = '
          <div class="btn-group">
            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
            <div class="dropdown-menu dropdown-menu-sm">
        ';

                $dropdown .= $print_button;

                if (($cargo_consignment->status_id == 1) && (session('role_id') == 1 || (in_array(28, session('permissions')) && in_array($cargo_consignment->origin_hub_id, session('hubs'))))) {
                    $dropdown .= $add_forwarding_details_button;
                }

                $dropdown .= $view_forwarding_details_button;

                if (session('role_id') == 1 || in_array(29, session('permissions'))) {
                    $dropdown .= $launch_dispute_button;
                }

                if (session('role_id') == 1 || (in_array(31, session('permissions')) && in_array($cargo_consignment->destination_hub_id, session('hubs')))) {
                    $dropdown .= $receive_button;
                }

                $dropdown .= '
            </div>
          </div>
        ';

                return $dropdown;
            });

        if ($cargo_type = $request->get('cargo_type')) {
            if ($cargo_type != 0) {
                $datatables->where('cargo_consignments.type', $cargo_type);
            }
        }

        if ($tracking_number = $request->get('tracking_number')) {
            $datatables->join('cargo_consignment_shipments as cssh', 'cargo_consignments.id', '=', 'cssh.cargo_consignment_id')
                ->join('shipments as s', 'cssh.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
        }

        if ($seal_number = $request->get('seal_number')) {
            $datatables->where('cargo_consignments.seal_number', '=', $seal_number);
        }

        return $datatables->make(true);
    }

    public function in_transit_print(Request $request) {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $cargo_consignment = CargoConsignment::find($request->id);

        $sender = $cargo_consignment->sender;
        $receiver = ($cargo_consignment->receiver_id) ? $cargo_consignment->receiver : NULL;

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

                      .cargo_checklist {
                        page-break-before: always;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
                      <div class="cargo_slip">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Cargo Slip</strong></td>
                              <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                              </tr>
                            <tr>
                              <td class="color secondary"><strong>Destination Hub</strong></td>
                              <td>' . $cargo_consignment->destination_hub->name . '</td>
                              <td rowspan="9" class="text-center align-middle">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                              </td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transit Date</strong></td>
                              <td>' . $cargo_consignment->created_at . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Shipping Mode</strong></td>
                              <td>' . $cargo_consignment->shipping_mode->mode . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transport Mode</strong></td>
                              <td>' . $cargo_consignment->transport_mode->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Vendor</strong></td>
                              <td>' . $cargo_consignment->transport_mode_vendor->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Builty Number</strong></td>
                              <td>' . $cargo_consignment->builty_number . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Seal Number</strong></td>
                              <td>' . $cargo_consignment->seal_number . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Expected Arrival Date</strong></td>
                              <td>' . (($cargo_consignment->expected_arrival_date) ? Carbon::parse($cargo_consignment->expected_arrival_date)->format('d/m/Y') : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>No. of Parcels</strong></td>
                              <td>' . $cargo_consignment->shipments . '</td>
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
                              <td>' . $sender['name'] . '</td>
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
                              <td>' . $cargo_consignment->origin_hub->name . ' - ' . $cargo_consignment->junction_hub_1->name . ' - ' . (($cargo_consignment->junction_hub_2_id) ? ($cargo_consignment->junction_hub_2->name . ' - ') : '') . $cargo_consignment->destination_hub->name . '</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>

                      <div class="cargo_checklist">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Cargo Checklist</strong></td>
                              <td class="text-center align-middle  color secondary">Printed at ' . Carbon::now() . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Origin Hub</strong></td>
                              <td>' . $cargo_consignment->origin_hub->name . '</td>
                              <td rowspan="5" class="text-center align-middle">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                              </td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Destination Hub</strong></td>
                              <td>' . $cargo_consignment->destination_hub->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transit Date</strong></td>
                              <td>' . $cargo_consignment->created_at . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Expected Arrival Date</strong></td>
                              <td>' . (($cargo_consignment->expected_arrival_date) ? Carbon::parse($cargo_consignment->expected_arrival_date)->format('d/m/Y') : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>No. of Parcels</strong></td>
                              <td>' . $cargo_consignment->shipments . '</td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="color primary"><strong>S. No.</strong></td>
                              <td class="color primary"><strong>Tracking No.</strong></td>
                              <td class="color primary"><strong>Consignee Name</strong></td>
                              <td class="color primary"><strong>Consignee Phone</strong></td>
                              <td class="color primary"><strong>Consignee City</strong></td>
                              <td class="color primary"><strong>Amount</strong></td>
      ';

        $serial_number = 1;

        foreach ($cargo_consignment->cargo_consignment_shipments as $cargo_consignment_shipment) {
            $shipment = $cargo_consignment_shipment->shipment;


            $html .= '
                            <tr>
                              <td>' . $serial_number . '</td>
                              <td>' . $shipment->tracking_number . '</td>
                              <td>' . $shipment->consignee_name . '</td>
                              <td>' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                              <td>' . $shipment->consignee_city->name . '</td>
                              <td>' . number_format($shipment->amount) . '</td>
                            </tr>
        ';

            $serial_number++;
        }

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

    public function in_transit_junctions(Request $request) {
        $city_ids = array();

        $cargo_consignments = CargoConsignment::whereIn('id', $request->ids)->get();

        foreach ($cargo_consignments as $cargo_consignment) {
            if ($cargo_consignment->origin_hub_id != $cargo_consignment->junction_hub_1_id && $cargo_consignment->destination_hub_id != $cargo_consignment->junction_hub_1_id && !in_array($cargo_consignment->junction_hub_1_id, $city_ids)) {
                $city_ids[] = $cargo_consignment->junction_hub_1_id;
            }

            if ($cargo_consignment->junction_hub_2_id && $cargo_consignment->origin_hub_id != $cargo_consignment->junction_hub_2_id && $cargo_consignment->destination_hub_id != $cargo_consignment->junction_hub_2_id && !in_array($cargo_consignment->junction_hub_2_id, $city_ids)) {
                $city_ids[] = $cargo_consignment->junction_hub_2_id;
            }
        }

        $cities = City::select(['id', 'name'])->whereIn('id', $city_ids);

        if (session('role_id') != 1) {
            $cities = $cities->whereIn('id', session('hubs'));
        }

        return $cities->get();
    }

    public function in_transit_details(Request $request) {
        $cargo_consignment = CargoConsignment::where('seal_number', $request->seal_number);

        if ($cargo_consignment->exists()) {
            $cargo_consignment = $cargo_consignment->first();

            if (session('role_id') == 1 || (in_array($cargo_consignment->junction_hub_1_id, session('hubs')) || in_array($cargo_consignment->junction_hub_2_id, session('hubs')))) {
                if (in_array($cargo_consignment->status_id, [1, 2, 6, 7])) {
                    $details = array();

                    $details['cargo_number'] = str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT);
                    $details['origin'] = $cargo_consignment->origin_hub->name;
                    $details['destination'] = $cargo_consignment->destination_hub->name;
                    $details['seal_number'] = $cargo_consignment->seal_number;

                    return ['status' => 0, 'success' => 'Cargo has been scanned', 'details' => $details];
                }
                else {
                    return ['status' => 1, 'error' => 'Given Seal Number\'s Cargo has already been modified'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'Given Seal Number\'s Cargo does not have any of your assigned Hub\'s Cities as it\'s Junctions'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Cargo with given Seal Number is present'];
        }
    }

    public function in_transit_receive_at_link(Request $request) {
        foreach ($request->cargo_consignment_ids as $cargo_consignment_id) {
            $cargo_consignment_junction_receival = new CargoConsignmentJunctionReceival();

            $cargo_consignment_junction_receival->cargo_consignment_id = $cargo_consignment_id;
            $cargo_consignment_junction_receival->junction_id = $request->junction;
            $cargo_consignment_junction_receival->receiver_id = Auth::id();

            $cargo_consignment_junction_receival->save();

            $cargo_consignment = CargoConsignment::find($cargo_consignment_id);

            if ($cargo_consignment->junction_hub_1_id == $request->junction) {
                $cargo_consignment->status_id = 6;
            }
            else if ($cargo_consignment->junction_hub_2_id == $request->junction) {
                $cargo_consignment->status_id = 7;
            }
            else {
                $cargo_consignment->status_id = 2;
            }

            $cargo_consignment->save();
        }

        return ['status' => 0, 'success' => 'Cargo(s) has been received at Junction'];
    }

    public function in_transit_forwarding_details(Request $request) {
        $cargo_consignment = CargoConsignment::find($request->cargo_consignment_id);

        $details = array();

            $details['cargo_consignment']['seal_number'] = $cargo_consignment->seal_number;
      $details['cargo_consignment']['builty_number'] = $cargo_consignment->builty_number;
      $details['cargo_consignment']['vendor_weight'] = $cargo_consignment->vendor_weight;
      $details['cargo_consignment']['weight_charges_per_kg'] = number_format($cargo_consignment->weight_charges_per_kg);
      $details['cargo_consignment']['extra_charges'] = number_format($cargo_consignment->extra_charges);
      $details['cargo_consignment']['total_weight_charges'] = number_format($cargo_consignment->total_weight_charges);
      $details['cargo_consignment']['sender_name'] = Admin::find($cargo_consignment->sender_id)->name;

        if ($request->add) {
            $details['cargo_consignment']['junction_hub_1_id'] = $cargo_consignment->junction_hub_1_id;
            $details['cargo_consignment']['junction_hub_2_id'] = $cargo_consignment->junction_hub_2_id;
            $details['cargo_consignment']['expected_arrival_date'] = $cargo_consignment->expected_arrival_date;
            $details['cargo_consignment']['shipping_mode_id'] = $cargo_consignment->shipping_mode_id;
            $details['cargo_consignment']['transport_mode_id'] = $cargo_consignment->transport_mode_id;
            $details['cargo_consignment']['transport_mode_vendor_id'] = $cargo_consignment->transport_mode_vendor_id;
            $details['cargo_consignment']['receiver_id'] = $cargo_consignment->receiver_id;

            $details['junctions'] = City::select(['id', 'name'])->where('hub', 1)->where('status', 1)->get();

            $details['shipping_modes'] = ShippingMode::where('id', '!=', 4)->get();

            $details['transport_modes'] = TransportMode::all();

            $details['transport_mode_vendors'] = TransportModeVendor::get()->groupBy('transport_mode_id');

            $destination_hub_id = $cargo_consignment->destination_hub_id;

            $details['receivers'] = Admin::where('status', 1)->whereHas('hubs', function ($query) use($destination_hub_id) {
                $query->where('hub_id',  $destination_hub_id);
            })->select(['id', 'name'])->get();
        }
        else {
            $details['cargo_consignment']['junction_hub_1'] = $cargo_consignment->junction_hub_1->name;
            $details['cargo_consignment']['junction_hub_2'] = ($cargo_consignment->junction_hub_2_id) ? $cargo_consignment->junction_hub_2->name : '';
            $details['cargo_consignment']['expected_arrival_date'] = Carbon::parse($cargo_consignment->expected_arrival_date)->format('d/m/Y');
            $details['cargo_consignment']['shipping_mode'] = $cargo_consignment->shipping_mode->mode;
            $details['cargo_consignment']['transport_mode'] = $cargo_consignment->transport_mode->name;
            $details['cargo_consignment']['transport_mode_vendor'] = $cargo_consignment->transport_mode_vendor->name;
            $details['cargo_consignment']['shipments_weight'] = $cargo_consignment->shipments_weight;
            $details['cargo_consignment']['actual_weight'] = $cargo_consignment->actual_weight;
            $details['cargo_consignment']['receiver_name'] = ($cargo_consignment->receiver_id) ? Admin::find($cargo_consignment->receiver_id)->name : '';
        }

        return $details;
    }

    public function in_transit_update(Request $request) {
        $cargo_consignment = CargoConsignment::find($request->input('cargo_consignment_id'));

        $cargo_consignment->junction_hub_1_id = $request->input('junction_1');

        if ($request->filled('junction_2')) {
            $cargo_consignment->junction_hub_2_id = $request->input('junction_2');
        }
        else {
            $cargo_consignment->junction_hub_2_id = NULL;
        }

        $cargo_consignment->seal_number = $request->input('seal_number');
        $cargo_consignment->transport_mode_id = $request->input('transport_mode');

        if ($request->input('transport_mode_vendor') == 0) {
            $transport_mode_vendor = new TransportModeVendor();

            $transport_mode_vendor->transport_mode_id = $request->input('transport_mode');
            $transport_mode_vendor->name = $request->input('vendor_name');

            $transport_mode_vendor->save();

            $cargo_consignment->transport_mode_vendor_id = $transport_mode_vendor->id;
        }
        else {
            $cargo_consignment->transport_mode_vendor_id = $request->input('transport_mode_vendor');
        }

        $cargo_consignment->builty_number = $request->input('builty_number');
        $cargo_consignment->vendor_weight = $request->input('vendor_weight');
        $cargo_consignment->expected_arrival_date = $request->input('expected_arrival_date_formatted');
        $cargo_consignment->weight_charges_per_kg = $request->input('weight_charges_per_kg');
        $cargo_consignment->extra_charges = $request->input('extra_charges');
        $cargo_consignment->total_weight_charges = $request->input('total_weight_charges');

        if ($request->filled('receiver_id')) {
            $cargo_consignment->receiver_id = $request->input('receiver_id');
        }
        else {
            $cargo_consignment->receiver_id = NULL;
        }

        $cargo_consignment->save();

        return redirect()->back()->with('success', 'Cargo Number #' . $request->input('cargo_consignment_id') . ' has been Updated');
    }

    public function in_transit_receive(Request $request) {
        if ($request->has('cargo_number')) {
            $cargo_consignment = CargoConsignment::find($request->get('cargo_number'));

            if ($cargo_consignment) {
                if (session('role_id') == 1 || (in_array($cargo_consignment->destination_hub->hub_id, session('hubs')))) {
                    if (in_array($cargo_consignment->status_id, [1, 2, 4, 6, 7])) {
                        return redirect()->route('admin.cargo.receive.index')->with('cargo_consignment_id', $cargo_consignment->id);
                    }
                    else {
                        return back()->withErrors('Given Cargo Number has already been modified!');
                    }
                }
                else {
                    return back()->withErrors('Cargo doesn\'t belong to your assigned hub(s)!');
                }
            }
            else {
                return back()->withErrors('Invalid Cargo Number!');
            }
        }
        else {
            return back()->withErrors('Missing Cargo Number!');
        }
    }

    public function in_transit_shipments(Request $request) {
        $tracking_numbers = array();

        $cargo_consignments_shipments = CargoConsignmentShipment::where('cargo_consignment_id', $request->id)->get();

        foreach ($cargo_consignments_shipments as $cargo_consignments_shipment) {
            $shipment = $cargo_consignments_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function receive_index() {
        if (session('cargo_consignment_id')) {
            $total = CargoConsignmentShipment::where('cargo_consignment_id', session('cargo_consignment_id'))->where('status', 0)->count();

            return view('admin.cargo.receive')->with('total', $total);
        }
        else {
            return redirect()->route('admin.cargo.in_transit.index')->withErrors('Kindly reselect a Cargo Number!');
        }
    }

    public function receive_shipment_details(Request $request) {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();

            $cargo_consignment_shipment = CargoConsignmentShipment::where('shipment_id', $shipment->id);

            if ($cargo_consignment_shipment->exists()) {
                $cargo_consignment_shipment = $cargo_consignment_shipment->where('cargo_consignment_id', $request->cargo_consignment_id);

                if ($cargo_consignment_shipment->exists()) {
                    $cargo_consignment_shipment = $cargo_consignment_shipment->where('status', 0);

                    if ($cargo_consignment_shipment->exists()) {
                        $cargo_consignment_shipment = $cargo_consignment_shipment->first();

                        $consignee_city = $shipment->consignee_city;

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
                        return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                    }
                    else {
                        return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been Received'];
                    }
                }
                else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment does not belong to current Cargo Number'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is not in any Cargo'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }

    public function receive_short_received(Request $request) {
        $shipment_ids = array_unique($request->input('shipment_ids'));

        $cargo_consignment_shipments = CargoConsignmentShipment::where('cargo_consignment_id', $request->input('cargo_consignment_id'))->where('status', 0);

        if ($cargo_consignment_shipments->count() != count($shipment_ids)) {
            $cargo_consignment_shipment_ids = $cargo_consignment_shipments->pluck('shipment_id')->toArray();

            $short_shipment_ids = array_diff($cargo_consignment_shipment_ids, $shipment_ids);

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

    public function receive_store(Request $request) {
        $cargo_consignment_id = $request->cargo_consignment_id;

        $shipment_ids = array_unique(explode(',', $request->shipment_ids));

        $cargo_consignment = CargoConsignment::find($cargo_consignment_id);

        foreach ($shipment_ids as $shipment_id) {
            $cargo_consignment_shipment = CargoConsignmentShipment::where('cargo_consignment_id', $cargo_consignment_id)->where('shipment_id', $shipment_id)->first();

            $cargo_consignment_shipment->status = 1;

            $cargo_consignment_shipment->save();

            $shipment = Shipment::find($shipment_id);

            $shipper_status_id = NULL;
            $consignee_status_id = NULL;

            if ($cargo_consignment->type == 1) {
                if ($shipment->booking_type_id == 4 && $shipment->walk_in_delivery_type_id == 2) {
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
                    $shipper_status_id = 27;
                    $consignee_status_id = 27;
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

            NotificationsController::send(7, $cargo_consignment_id, $shipment_id);

            NotificationsController::send(8, $cargo_consignment_id, $shipment_id);
        }

        $cargo_consignment->received_shipments = CargoConsignmentShipment::where('cargo_consignment_id', $cargo_consignment_id)->where('status', 1)->count();

        $short_received = CargoConsignmentShipment::where('cargo_consignment_id', $cargo_consignment_id)->where('status', 0)->count();

        if ($short_received > 0) {
            $cargo_consignment->status_id = 4;
        }
        else {
            $cargo_consignment->status_id = 3;
        }

        $cargo_consignment->receiver_id = Auth::id();
        $cargo_consignment->save();

        //dispute for short received
        if($cargo_consignment->status_id == 4){
            $cargo_short_received_shipments = CargoConsignmentShipment::where(['cargo_consignment_id'=>$cargo_consignment_id,'status'=>0])->select('shipment_id')->get();
            if(!empty($cargo_short_received_shipments)){
                DisputeController::add_cargo_short_received($cargo_consignment_id,$cargo_short_received_shipments);
            }
        }

//        end dispute short received
//        dispute start for junction
        $junction_hub_1_id = $cargo_consignment->junction_hub_1_id;
        $junction_hub_2_id = $cargo_consignment->junction_hub_2_id;

        if($cargo_consignment->origin_hub_id != $junction_hub_1_id && $cargo_consignment->destination_hub_id != $junction_hub_1_id) {
            $junction1 = CargoConsignmentJunctionReceival::where(['cargo_consignment_id'=>$cargo_consignment_id,'junction_id'=>$junction_hub_1_id])->exists();
            if(!$junction1){
                DisputeController::add_junction_dispute($cargo_consignment_id,$junction_hub_1_id);
            }
        }
        if($junction_hub_2_id && $cargo_consignment->origin_hub_id != $junction_hub_2_id && $cargo_consignment->destination_hub_id != $junction_hub_2_id) {
            $junction2 = CargoConsignmentJunctionReceival::where(['cargo_consignment_id'=>$cargo_consignment_id,'junction_id'=>$junction_hub_2_id])->exists();
            if(!$junction2){
                DisputeController::add_junction_dispute($cargo_consignment_id,$junction_hub_2_id);
            }
        }


        //dispute end for junction
        return redirect()->route('admin.cargo.in_transit.index')->with('success', 'Cargo No# ' . $cargo_consignment_id . ' has been Received');
    }
    public function history_index(){
        $shipping_mode = ShippingMode::all();
        $cargo_status = CargoConsignmentStatus::all();
        $transport_vendor = TransportModeVendor::all();
        $transport_mode = TransportMode::all();
        return view('admin.cargo.history')->with(['shipping_mode'=>$shipping_mode,'cargo_status'=>$cargo_status,'transport_mode'=>$transport_mode,'transport_vendor'=>$transport_vendor]);
    }
    public function history_list(Request $request){
        $cargo_consignments = CargoConsignment::join('cities as oh', 'cargo_consignments.origin_hub_id', '=', 'oh.id')
            ->join('cities as dh', 'cargo_consignments.destination_hub_id', '=', 'dh.id')
            ->join('shipping_modes as sm', 'cargo_consignments.shipping_mode_id', '=', 'sm.id')
            ->join('admins as a', 'cargo_consignments.sender_id', '=', 'a.id')
            ->join('admins as ri','ri.id','=','cargo_consignments.receiver_id')
            ->join('cargo_consignment_status as ccs', 'cargo_consignments.status_id', '=', 'ccs.id')
            ->join('cities as jh1', 'cargo_consignments.junction_hub_1_id', '=', 'jh1.id')
            ->leftjoin('cities as jh2', 'cargo_consignments.junction_hub_2_id', '=', 'jh2.id')
            ->join('transport_modes as tm', 'cargo_consignments.transport_mode_id', '=', 'tm.id')
            ->join('transport_mode_vendors as tmv', 'cargo_consignments.transport_mode_vendor_id', '=', 'tmv.id')
            ->select('cargo_consignments.id', 'cargo_consignments.status_id', 'oh.id as origin_id', 'oh.name as origin', 'dh.id as destination_id', 'dh.name as destination', 'cargo_consignments.shipments', 'sm.mode as shipping_mode', 'jh1.name as junction_1', 'jh2.name as junction_2', 'tm.name as transport_mode', 'tmv.name as vendor', 'cargo_consignments.builty_number', 'cargo_consignments.shipments_weight', DB::raw('(SELECT SUM(`s`.`chargeable_weight`) FROM `shipments` AS `s` INNER JOIN `cargo_consignment_shipments` AS `css` ON `s`.`id` = `css`.`shipment_id` WHERE `css`.`cargo_consignment_id` = `cargo_consignments`.`id`) AS `chargeable_weight`'), 'cargo_consignments.actual_weight', 'cargo_consignments.vendor_weight', 'cargo_consignments.created_at as transit_at', 'a.name as transitted_by', 'ccs.name as status','cargo_consignments.type as cargo_type','ri.name as received_by','cargo_consignments.updated_at as received_at','cargo_consignments.seal_number');

        if (session('role_id') != 1) {
            $cargo_consignments = $cargo_consignments->where(function ($query) {
                $query->whereIn('oh.hub_id', session('hubs'))->orWhereIn('dh.hub_id', session('hubs'))->orWhereIn('cargo_consignments.junction_hub_1_id', session('hubs'))->orWhereIn('cargo_consignments.junction_hub_1_id', session('hubs'));
            });
        }

        $datatables = Datatables::of($cargo_consignments)
            ->addColumn('id_padded', function ($cargo_consignment) {
                return str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('id_padded_link', function ($cargo_consignment) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->addColumn('shipments_count', function ($cargo_consignment) {
                return $cargo_consignment->shipments;
            })
            ->addColumn('shipments', function ($cargo_consignment) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $cargo_consignment->shipments . '</button>';
            })
            ->filterColumn('cargo_consignments.id', function ($query, $keyword) {
                return $query->where('cargo_consignments.id', '=', $keyword);
            })
            ->filterColumn('shipping_mode',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('sm.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('cargo_type',function ($cargo_received){
                if($cargo_received->cargo_type == 1){
                    return 'Normal';
                }else{
                    return 'Return';
                }
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ccs.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            });
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatables->whereBetween('cargo_consignments.created_at', [$from,$to]);
        }

        return $datatables->make(true);
    }
    public function history_shipments(Request $request) {
        $tracking_numbers = array();

        $cargo_consignments_shipments = CargoConsignmentShipment::where('cargo_consignment_id', $request->id)->get();

        foreach ($cargo_consignments_shipments as $cargo_consignments_shipment) {
            $shipment = $cargo_consignments_shipment->shipment;

            $tracking_number = array();

            $tracking_number['tracking_number'] = $shipment->tracking_number;
            $tracking_number['estimated_weight'] = $shipment->estimated_weight;
            $tracking_number['actual_weight'] = $shipment->actual_weight;
            $tracking_number['chargeable_weight'] = ($shipment->chargeable_weight) ? $shipment->chargeable_weight : '';

            $tracking_numbers[] = $tracking_number;
        }

        return $tracking_numbers;
    }
    public function history_cargo_print(Request $request) {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $cargo_consignment = CargoConsignment::find($request->id);

        $sender = $cargo_consignment->sender;
        $receiver = ($cargo_consignment->receiver_id) ? $cargo_consignment->receiver : NULL;

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

                      .cargo_checklist {
                        page-break-before: always;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
                      <div class="cargo_slip">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Cargo Slip</strong></td>
                              <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                              </tr>
                            <tr>
                              <td class="color secondary"><strong>Destination Hub</strong></td>
                              <td>' . $cargo_consignment->destination_hub->name . '</td>
                              <td rowspan="8" class="text-center align-middle">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                              </td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transit Date</strong></td>
                              <td>' . $cargo_consignment->created_at . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Shipping Mode</strong></td>
                              <td>' . $cargo_consignment->shipping_mode->mode . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transport Mode</strong></td>
                              <td>' . $cargo_consignment->transport_mode->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Vendor</strong></td>
                              <td>' . $cargo_consignment->transport_mode_vendor->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Builty Number</strong></td>
                              <td>' . $cargo_consignment->builty_number . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Expected Arrival Date</strong></td>
                              <td>' . (($cargo_consignment->expected_arrival_date) ? Carbon::parse($cargo_consignment->expected_arrival_date)->format('d/m/Y') : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>No. of Parcels</strong></td>
                              <td>' . $cargo_consignment->shipments . '</td>
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
                              <td>' . $sender['name'] . '</td>
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
                              <td>' . $cargo_consignment->origin_hub->name . ' - ' . $cargo_consignment->junction_hub_1->name . ' - ' . (($cargo_consignment->junction_hub_2_id) ? ($cargo_consignment->junction_hub_2->name . ' - ') : '') . $cargo_consignment->destination_hub->name . '</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>

                      <div class="cargo_checklist">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Cargo Checklist</strong></td>
                              <td class="text-center align-middle  color secondary">Printed at ' . Carbon::now() . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Origin Hub</strong></td>
                              <td>' . $cargo_consignment->origin_hub->name . '</td>
                              <td rowspan="5" class="text-center align-middle">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                              </td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Destination Hub</strong></td>
                              <td>' . $cargo_consignment->destination_hub->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transit Date</strong></td>
                              <td>' . $cargo_consignment->created_at . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Expected Arrival Date</strong></td>
                              <td>' . (($cargo_consignment->expected_arrival_date) ? Carbon::parse($cargo_consignment->expected_arrival_date)->format('d/m/Y') : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>No. of Parcels</strong></td>
                              <td>' . $cargo_consignment->shipments . '</td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="color primary"><strong>S. No.</strong></td>
                              <td class="color primary"><strong>Tracking No.</strong></td>
                              <td class="color primary"><strong>Consignee Name</strong></td>
                              <td class="color primary"><strong>Consignee Phone</strong></td>
                              <td class="color primary"><strong>Consignee City</strong></td>
                              <td class="color primary"><strong>Amount</strong></td>
      ';

        $serial_number = 1;

        foreach ($cargo_consignment->cargo_consignment_shipments as $cargo_consignment_shipment) {
            $shipment = $cargo_consignment_shipment->shipment;


            $html .= '
                            <tr>
                              <td>' . $serial_number . '</td>
                              <td>' . $shipment->tracking_number . '</td>
                              <td>' . $shipment->consignee_name . '</td>
                              <td>' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                              <td>' . $shipment->consignee_city->name . '</td>
                              <td>' . number_format($shipment->amount) . '</td>
                            </tr>
        ';

            $serial_number++;
        }

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

    public function draft_index(){
        return view('admin.cargo.draft');
    }
    public function draft_add(Request $request){
        $shipment_ids = $request->shipment_ids;
        $destination_id = $request->hub_id;
        $cargo_type = $request->cargo_type;
        $shipping_mode_id = $request->shipping_mode_id;
        $shipment_count = 0;
        $shipment = Shipment::find(current($shipment_ids));
        foreach ($shipment_ids as $shipments){
            $shipment_details = Shipment::find($shipments);
            if($shipment_details){
                $shipment_count++;
                static::check_draft_shipments($shipments);
            }
        }

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
            $origin = $shipment->pickup_address->city->hub_city;
        }


        $draftcargo = new DraftCargo();
        $draftcargo->origin_id = $origin->id;
        $draftcargo->destination_id = $destination_id;
        $draftcargo->destination_id = $destination_id;
        $draftcargo->cargo_type = $cargo_type;
        $draftcargo->shipping_mode_id = $shipping_mode_id;
        $draftcargo->shipments_count = $shipment_count;
        $draftcargo->added_by = Auth::id();
        $draftcargo->save();
        $draft_cargo_id = $draftcargo->id;

        foreach ($shipment_ids as $shipment_id){
            $draft_shipments = new DraftCargoShipment();
            $draft_shipments->draft_cargo_id = $draft_cargo_id;
            $draft_shipments->shipment_id = $shipment_id;
            $draft_shipments->save();
        }
        return response()->json(['status' => 1, 'success' => 'Shipments Added to Draft # '.$draft_cargo_id]);
    }
    public function draft_list(){
        $draft = DraftCargo::join('cities as oc','oc.id','=','draft_cargos.origin_id')
            ->join('cities as dc', 'dc.id', '=', 'draft_cargos.destination_id')
            ->select('draft_cargos.id as id','oc.name as origin','dc.name as destination','draft_cargos.shipments_count as shipments_count','draft_cargos.shipments_count as shipments_count_link','draft_cargos.cargo_type as cargo_type');
        return Datatables::of($draft)
            ->addColumn('shipments_count_link', function ($cargo_id) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $cargo_id->shipments_count . '</button>';
            })
            ->editColumn('cargo_type', function ($draft){
                if($draft->cargo_type == 1){
                    return "Normal";
                }else if($draft->cargo_type == 2){
                    return "Return";
                }
            })
            ->addColumn('action', function($draft) {
                $route = route('admin.cargo.draft.edit.index', ['draft' => $draft->id]);
                $dropdown = '<div class="btn-group">
            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
            <div class="dropdown-menu dropdown-menu-sm">';
                $button = "<a href='{$route}' class='dropdown-item edit'>Edit</a>";
                $dropdown .= $button;

                $dropdown .= '
            </div>
          </div>
        ';
                return $dropdown;
            })
            ->make(true);
    }
    public function draft_shipments(Request $request) {
        $tracking_numbers = array();

        $draft_cargo_shipments = DraftCargoShipment::where('draft_cargo_id', $request->id)->get();

        foreach ($draft_cargo_shipments as $draft_cargo_shipment) {
            $shipment = $draft_cargo_shipment->shipment_id;
            $shipment = Shipment::find($shipment);
            $tracking_numbers[] = $shipment->tracking_number;
        }

        return response()->json(['status' => 1, 'tracking_numbers' => $tracking_numbers]);
    }

    public function edit_draft_index(Request $request, $id){
        $draft = DraftCargo::find($id);
        if($draft) {
            $hub_name = $draft->destination->name;
            $mode = $draft->shipping_mode->mode;

            $cargo_type = $draft->cargo_type;
            $shipment_ids = array();
            foreach ($draft->draft_cargo_shipment as $draft_cargo_ids){
                $shipment_ids[] = $draft_cargo_ids->shipment_id;
            }

            if ($cargo_type == 1) {
                $shipments = Shipment::join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
                    ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
                    ->join('cities as dc', function ($join) {
                        $join->on('shipments.consignee_city_id', '=', 'dc.id')
                            ->on('oc.hub_id', '!=', 'dc.hub_id');
                    })
                    ->select(DB::raw('count(shipments.id) as count'))
                    ->where('dc.hub_id', $draft->destination_id)
                    ->whereIn('shipments.shipper_status_id', [2, 49, 55])
                    ->where('shipments.shipping_mode_id', $draft->shipping_mode_id);
            } else {
                $shipments = Shipment::join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
                    ->join('cities as dc', 'usi.city_id', '=', 'dc.id')
                    ->join('cities as oc', function ($join) {
                        $join->on('shipments.consignee_city_id', '=', 'oc.id')
                            ->on('dc.hub_id', '!=', 'oc.hub_id');
                    })
                    ->select(DB::raw('count(shipments.id) as count'))
                    ->where('dc.hub_id', $draft->destination_id)
                    ->whereIn('shipments.shipper_status_id', [20, 30, 36, 37])
                    ->where('shipments.shipping_mode_id', $draft->shipping_mode_id);
            }

            if (session('role_id') != 1) {
                $shipments = $shipments->whereIn('oc.hub_id', session('hubs'));
            }

            $shipments = $shipments->first();

            $total = $shipments->count;
            return view('admin.cargo.create_draft_cargo')->with(['draft' => $draft, 'hub' => $hub_name, 'mode' => $mode, 'total_shipments' => $total, 'shipment_ids' => $shipment_ids]);
        }
        return redirect()->back()->with(['error'=>'Draft Not found']);
    }

    public function edit_draft_list(Request $request, $draft){
        $shipments =  Shipment::join('draft_cargo_shipments as dcs','shipments.id', '=', 'dcs.shipment_id')
            ->join('draft_cargos as drc', 'drc.id', '=', 'dcs.draft_cargo_id')
            ->join('cities as oc','oc.id', '=', 'drc.origin_id')
            ->join('cities as dc','dc.id', '=', 'drc.destination_id')
            ->join('booking_types as bt', 'shipments.booking_type_id', '=', 'bt.id')
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
            ->select('shipments.id as shipment_id','shipments.shipper_status_id', 'shipments.tracking_number', 'shipments.order_id', 'bt.booking_type as service_type', 'oc.name as origin','dc.name as destination', 'shipments.amount', 'olddc.name as old_destination', 'olddci.name as old_destination_intercept')
            ->where('drc.id', $draft);
        if (session('role_id') != 1) {
            $shipments = $shipments->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->whereIn('shipments.shipper_status_id', [20, 30, 36, 37])
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
        return Datatables::of($shipments)
            ->editColumn('origin', function ($shipments) {
                if (in_array($shipments->shipper_status_id, [20, 30, 36, 37])) {
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
                if (in_array($shipments->shipper_status_id, [20, 30, 36, 37])) {
                    return $shipments->origin;
                }
                else {
                    return $shipments->destination;
                }
            })
            ->filterColumn('oc.name', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->whereIn('shipments.shipper_status_id', [20, 30, 36, 37])
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
                    $sub_query->whereIn('shipments.shipper_status_id', [20, 30, 36, 37])
                        ->where('oc.name', 'like', '%' . $keyword . '%');
                })
                ->orWhere(function ($sub_query) use ($keyword) {
                    $sub_query->whereIn('shipments.shipper_status_id', [2, 49, 55])
                        ->where('dc.name', 'like', '%' . $keyword . '%');
                });
            })
            ->orderColumn('oc.name', DB::raw('IF (shipments.shipper_status_id IN (20, 30, 36, 37), dc.name, IF (shipments.shipper_status_id = 49, olddc.name, IF (shipments.shipper_status_id = 55, olddci.name, oc.name)))') . ' $1')
            ->orderColumn('dc.name', DB::raw('IF (shipments.shipper_status_id IN (20, 30, 36, 37), oc.name, dc.name)') . ' $1')
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->addColumn('action',function ($shipments){
                $dropdown = '<a href="javascript:void(0);" class="btn btn-icon btn-danger cargo_remove"><i class="la la-close"></i></a>';
                return $dropdown;

            })
            ->make(true);

    }

    static public function check_draft_shipments($shipment_id,$draft_id = null){
        if($draft_id != null){
            $drafts = DraftCargoShipment::where('shipment_id', $shipment_id)->where('draft_cargo_id','!=',$draft_id);
        }else{
            $drafts = DraftCargoShipment::where('shipment_id', $shipment_id);
        }
        if($drafts->exists()){
            $drafts = $drafts->get();

            foreach ($drafts as $draft) {
                $draft_details = DraftCargo::find($draft->draft_cargo_id);
                if ($draft_details) {
                    $new_shipments_count = $draft_details->shipments_count - 1;
                    $draft_details->shipments_count = $new_shipments_count;
                    $draft_details->save();
                    if($draft_details->shipments_count == 0){
                        $draft_details->delete();
                    }
                }
                DraftCargoShipment::where('shipment_id', $shipment_id)->where('draft_cargo_id',$draft->draft_cargo_id)->delete();
            }
        }
    }
    public function draft_update(Request $request){
        $shipment_ids = $request->shipment_ids;

        $draft_cargo_id = $request->draft_cargo_id;
        $shipment_count = 0;
        $new_array = array();
        $purged_array = array();
        foreach ($shipment_ids as $shipments){
            $shipment_details = Shipment::find($shipments);
            if($shipment_details){
                $purged_array[] = $shipments;
                self::check_draft_shipments($shipments,$draft_cargo_id);
                $draft_shipments = DraftCargoShipment::where('draft_cargo_id',$draft_cargo_id)->where('shipment_id', $shipments);

                if(!$draft_shipments->exists()){
                    $new_array[] = $shipments;

                }
                $shipment_count++;
            }
        }

        DraftCargoShipment::where('draft_cargo_id',$draft_cargo_id)->whereNotIn('shipment_id',$purged_array)->delete();
        $draft = DraftCargo::find($draft_cargo_id);
        $draft->shipments_count = $shipment_count;
        $draft->added_by = Auth::id();
        $draft->save();
        if(!empty($new_array)){
            foreach ($new_array as $shipment_id){
                $draft_shipments = new DraftCargoShipment();
                $draft_shipments->draft_cargo_id = $draft_cargo_id;
                $draft_shipments->shipment_id = $shipment_id;
                $draft_shipments->save();
            }
        }

        return response()->json(['status' => 1, 'success' => 'Shipments Added to Draft # '.$draft_cargo_id]);
    }

    public function mapping_index()
    {
        $junctions = City::select(['id', 'name'])->where('hub', 1)->where('status', 1)->get();
        $cities = City::all();
        $admins = Admin::where('status', 1)->select(['id', 'name'])->get();
        return view('admin.cargo.mapping')->with(['cities' => $cities, 'junctions' => $junctions, 'admins' => $admins]);
    }

    public function mapping_list()
    {
        $mapping =  JunctionMapping::join('cities as oc','oc.id', '=', 'junction_mappings.origin_id')
            ->join('cities as dc','dc.id', '=', 'junction_mappings.destination_id')
            ->join('cities as jc1','jc1.id', '=', 'junction_mappings.junction_1')
            ->leftjoin('cities as jc2','jc2.id', '=', 'junction_mappings.junction_2')
            ->join('admins as a','a.id', '=', 'junction_mappings.updated_by')
            ->leftjoin('admins as ar','ar.id', '=', 'junction_mappings.receiver')
            ->select('junction_mappings.id as id', 'junction_mappings.updated_at as updated_at','oc.name as origin','dc.name as destination','jc1.name as junction_1', 'jc2.name as junction_2', 'ar.name as receiver', 'a.name as updated_by');

        return Datatables::of($mapping)
            ->editColumn('junction_2',function ($mapping){
                if($mapping->junction_2 != null){
                    return $mapping->junction_2;
                }
                else{
                    return '-';
                }
            })
            ->editColumn('receiver',function ($mapping){
                if($mapping->receiver != null){
                    return $mapping->receiver;
                }
                else{
                    return '-';
                }
            })
            ->addColumn('action',function ($mapping) {
                    $dropdown = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                                <button type="button" class="dropdown-item edit_mapping"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></div></button>
                            </div>
                        </div>
                    ';

                    return $dropdown;
            })
            ->make(true);
    }

    public function mapping_store(Request $request){
        $check = JunctionMapping::where(['origin_id' => $request->origin, 'destination_id' => $request->destiination_id])->first();
        if(!$check) {
            $mapping = new JunctionMapping();

            $mapping->origin_id = $request->origin;
            $mapping->destination_id = $request->destination;
            $mapping->junction_1 = $request->junction_1;
            $mapping->junction_2 = $request->junction_2;
            $mapping->receiver = $request->receiver_id;
            $mapping->updated_by = Auth::id();

            $mapping->save();

            return redirect()->back()->with('success', 'Mapping added successfully.');
        }
        else{
            return redirect()->back()->with('error', 'Mapping against these hubs already exists!');
        }
    }

    public function mapping_edit(Request $request){
        $mapping = JunctionMapping::where('id', $request->mapping_id)->first();
        $origin = City::where('id', $mapping['origin_id'])->first();
        $destination = City::where('id', $mapping['destination_id'])->first();
        return response()->json(['details' => $mapping, 'origin' => $origin, 'destination' => $destination]);
    }

    public function mapping_edit_update(Request $request){
        $mapping = JunctionMapping::where('id', $request->mapping_id);
        if($mapping) {
            $mapping = $mapping->first();

            $mapping->junction_1 = $request->junction_1;
            $mapping->junction_2 = $request->junction_2;
            $mapping->receiver = $request->receiver_id;
            $mapping->updated_by = Auth::id();

            $mapping->save();

            return redirect()->back()->with('success', 'Mapping added successfully.');
        }
        else{
            return redirect()->back()->with('error', 'Mapping against these hubs already exists!');
        }
    }
}