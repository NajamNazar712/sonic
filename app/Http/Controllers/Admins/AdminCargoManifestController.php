<?php

namespace App\Http\Controllers\Admins;


use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\CargoManifest\CargoManifest;
use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagStatus;
use App\Http\Models\Admin\CargoManifest\CargoManifestDraftBags;
use App\Http\Models\Admin\CargoManifest\ManifestBag;
use App\Http\Models\Admin\CargoManifest\V2JunctionMapping;
use App\Http\Models\Admin\CargoManifest\V2JunctionRoutes;
use App\Http\Models\Admin\CargoManifest\V2Junctions;
use App\Http\Models\Admin\CargoManifest\V2JunctionVehicles;
use App\Http\Controllers\CargoManifestBagJourneyController;
use App\Http\Controllers\ShipmentOpenBoxJourneyController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\CargoManifestBagRemarks;
use App\Http\Models\Admin\DeliveryLocationMappingKeyword;
use App\Http\Models\Admin\Fleet;
use App\Http\Models\Admin\MasterCargo\BagShipment;
use App\Http\Models\Admin\ShipmentOnHold;
use App\Http\Models\BookingType;
use App\Http\Models\City;
use App\Http\Models\FleetDriver;
use App\Http\Models\FleetVendor;
use App\Http\Models\InterceptReBookRequest;
use App\Http\Models\InterceptReBookRequestHistory;
use App\Http\Models\ManifestBagLostShipment;
use App\Http\Models\MisroutedHistory;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\SelfCollectionCities;
use App\Http\Models\SelfCollectionShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentDetail;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShippingMode;
use App\Http\Models\TransportMode;
use App\Http\Models\WarehouseStockRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use SnappyPDF;

class AdminCargoManifestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');

        $this->bag_can_be_received_statuses = [2, 4, 6, 8, 9, 10];
    }

    public function manifest_mapping_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 396);
        $cities = City::select(['id', 'name'])->where('hub', 1)->get();
        $vehicles = Fleet::where('status', 1)->select(['id', 'reg_number'])->get();
        return view('admin.cargo.manifest.mapping')->with(['cities' => $cities, 'vehicles' => $vehicles]);
    }

    public function manifest_mapping_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 397);
        }

        $mapping = V2JunctionMapping::join('cities as oc', 'oc.id', '=', 'v2_junction_mappings.origin_id')
            ->join('cities as dc', 'dc.id', '=', 'v2_junction_mappings.destination_id')
            ->join('admins as a', 'a.id', '=', 'v2_junction_mappings.updated_by')
            ->select('v2_junction_mappings.id as id', 'v2_junction_mappings.status as status', 'v2_junction_mappings.updated_at as updated_at', 'oc.name as origin', 'oc.hub_location_latitude as ohllat', 'oc.hub_location_longitude as ohllng', 'dc.name as destination', 'dc.hub_location_latitude as dhllat', 'dc.hub_location_longitude as dhllng', 'a.name as updated_by');

        return Datatables::of($mapping)
            ->addColumn('origin_display', function ($mapping) {
                return "<a href='https://www.google.com/maps/?q=" . $mapping->ohllat . "," . $mapping->ohllng . "' target='_blank' class='btn btn-sm btn-outline-info align-middle'><i class='ft-map-pin'></i></a> " . $mapping->origin;
            })
            ->addColumn('destination_display', function ($mapping) {
                return "<a href='https://www.google.com/maps/?q=" . $mapping->dhllat . "," . $mapping->dhllng . "' target='_blank' class='btn btn-sm btn-outline-info align-middle'><i class='ft-map-pin'></i></a> " . $mapping->destination;
            })
            ->addColumn('junctions_display', function ($mapping) {
                $junctions = V2Junctions::where('junction_mapping_id', $mapping->id)
                    ->join('cities as j', 'j.id', '=', 'v2_junctions.junction_id')
                    ->select(['j.name as junction', 'j.hub_location_latitude as jhllat', 'j.hub_location_longitude as jhllng'])
                    ->get();

                $junction_data = '';
                foreach ($junctions as $j) {
                    $junction_data .= "<a href='https://www.google.com/maps/?q=" . $j->jhllat . "," . $j->jhllng . "' target='_blank' class='btn btn-sm btn-outline-info align-middle'><i class='ft-map-pin'></i></a> " . $j->junction . "<br><br>";
                }

                return $junction_data;
            })
            ->addColumn('junctions', function ($mapping) {
                $junctions = V2Junctions::where('junction_mapping_id', $mapping->id)
                    ->join('cities as j', 'j.id', '=', 'v2_junctions.junction_id')
                    ->select(['j.name as junction', 'j.hub_location_latitude as jhllat', 'j.hub_location_longitude as jhllng'])
                    ->get();

                $junction_data = '';
                foreach ($junctions as $j) {
                    $junction_data .= $j->junction . ",";
                }

                $junction_data = substr(trim($junction_data), 0, -1);
                if (!$junction_data) {
                    $junction_data = "";
                }
                return $junction_data;
            })
            ->editColumn('status', function ($mapping) {
                if ($mapping->status == 1) {
                    return "Enabled";
                } else {
                    return "Disabled";
                }
            })
            ->addColumn('action', function ($mapping) {
                $dropdown = "";

                if (session('role_id') == 1 || count(array_intersect([549, 550], session('permissions'))) !== 0) {
                    $dropdown .= '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">';

                    if (session('role_id') == 1 || in_array(549, session('permissions'))) {
                        $edit_route = route('admin.cargo.mapping.manifest.edit', $mapping->id);
                        $dropdown .= '<a href="' . $edit_route . '" class="dropdown-item edit_mapping"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></div></a>';
                    }
                    if (session('role_id') == 1 || in_array(550, session('permissions'))) {
                        if ($mapping->status == 1) {
                            $text = "Disable";
                        } else {
                            $text = "Enable";
                        }
                        $dropdown .= '<button type="button" class="dropdown-item status_mapping"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">' . $text . '</div></div></button>';
                    }
                    $dropdown .= '</div>
                        </div>
                    ';
                }

                return $dropdown;
            })
            ->make(true);
    }

    public function manifest_mapping_store(Request $request)
    {

        if (count($request->junctions) > 1 && $request->junctions[1] == null) {
            return redirect()->back()->with('error', 'Please Select Junction 1');
        }

        $check = V2JunctionMapping::where([['origin_id', $request->origin], ['destination_id', $request->destination], ['status', 1]]);
        if ($check->exists()) {
            return redirect()->back()->with('error', 'Mapping against these hubs already exists!');
        }

        $mapping = new V2JunctionMapping();

        $mapping->origin_id = $request->origin;
        $mapping->destination_id = $request->destination;
        $mapping->updated_by = Auth::id();
        $mapping->save();

        foreach ($request->junctions as $j) {
            if ($j != null) {
                $junction = new V2Junctions();
                $junction->junction_mapping_id = $mapping->id;
                $junction->junction_id = $j;
                $junction->save();
            }
        }

        $previous = $request->origin;
        foreach ($request->route_junctions as $key => $rj) {
            $route_junction = new V2JunctionRoutes();
            $route_junction->junction_mapping_id = $mapping->id;
            $route_junction->starting_hub_id = $previous;
            $route_junction->ending_hub_id = $rj;
            $route_junction->save();

            $previous = $rj;

            foreach ($request->vehicles[$key] as $vehicle) {
                $route_vehicle = new V2JunctionVehicles();
                $route_vehicle->junction_route_id = $route_junction->id;
                $route_vehicle->vehicle_id = $vehicle;
                $route_vehicle->save();
            }
        }

        return redirect()->back()->with('success', 'Mapping added successfully.');
    }

    public function manifest_mapping_status(Request $request)
    {
        $mapping = V2JunctionMapping::where('id', $request->id);
        if ($mapping->doesntExist()) {
            return response()->json(['status' => 0, 'error' => 'Mapping Doesn\'t Exists..']);
        }

        $mapping = $mapping->first();

        if ($mapping->status == 1) {
            $mapping->status = 0;
            $mapping->updated_by = Auth::id();
            $mapping->update();
            return response()->json(['status' => 1, 'success' => 'Mapping Disabled Successfully..']);
        } else if ($mapping->status == 0) {
            $mapping->status = 1;
            $mapping->updated_by = Auth::id();
            $mapping->update();
            return response()->json(['status' => 1, 'success' => 'Mapping Enabled Successfully..']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Invalid Status..']);
        }
    }

    public function manifest_mapping_edit($id)
    {

        $mapping = V2JunctionMapping::where('id', $id);
        if ($mapping->doesntExist()) {
            return back()->with(['error' => 'Invalid Mapping ID']);
        }

        $mapping = $mapping->with(['junctions', 'routes', 'routes.vehicles', 'routes.starting', 'routes.ending'])
            ->first();
        $cities = City::select(['id', 'name'])->where('business_category_id', 1)->where('hub', 1)->get();
        $vehicles = Fleet::where('status', 1)->select(['id', 'reg_number'])->get();
        return view('admin.cargo.manifest.edit')->with(['mapping' => $mapping, 'cities' => $cities, 'vehicles' => $vehicles]);
    }

    public function manifest_mapping_edit_update($id, Request $request)
    {
        if (count($request->junctions) > 1 && $request->junctions[1] == null) {
            return redirect()->back()->with('error', 'Please Select Junction 1');
        }

        $check = V2JunctionMapping::where([['origin_id', $request->origin], ['destination_id', $request->destination], ['status', 1], ['id', '!=', $id]]);
        if ($check->exists()) {
            return redirect()->back()->with('error', 'Mapping against these hubs already exists!');
        }

        $mapping = V2JunctionMapping::find($id);

        if (!$mapping) {
            return redirect()->back()->with('error', 'Mapping ot found!');
        }

        $mapping->origin_id = $request->origin;
        $mapping->destination_id = $request->destination;
        $mapping->updated_by = Auth::id();
        $mapping->update();

        $mapping->junctions()->delete();
        foreach ($mapping->routes as $routes) {
            $routes->vehicles()->delete();
        }
        $mapping->routes()->delete();

        foreach ($request->junctions as $j) {
            if ($j != null) {
                $junction = new V2Junctions();
                $junction->junction_mapping_id = $mapping->id;
                $junction->junction_id = $j;
                $junction->save();
            }
        }

        $previous = $request->origin;
        foreach ($request->route_junctions as $key => $rj) {
            $route_junction = new V2JunctionRoutes();
            $route_junction->junction_mapping_id = $mapping->id;
            $route_junction->starting_hub_id = $previous;
            $route_junction->ending_hub_id = $rj;
            $route_junction->save();

            $previous = $rj;

            foreach ($request->vehicles[$key] as $vehicle) {
                $route_vehicle = new V2JunctionVehicles();
                $route_vehicle->junction_route_id = $route_junction->id;
                $route_vehicle->vehicle_id = $vehicle;
                $route_vehicle->save();
            }
        }

        return redirect()->back()->with('success', 'Mapping added successfully.');
    }

    public function pending_bag_index()
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 398);
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        $service_type = BookingType::all();
        $shipping_mode = ShippingMode::all();
        return view('admin.cargo.manifest.bags.pending')->with(['shipment_status' => $shipment_status, 'service_type' => $service_type, 'shipping_mode' => $shipping_mode]);
    }

    public function pending_bag_list(Request $request)
    {

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 399);
        }
        $today = Carbon::today();
        $on_hold_shipments = ShipmentOnHold::whereDate('dispatch_date', '>', $today)->where('status', 1)->pluck('shipment_id')->toArray();
        $shipments = Shipment::join('booking_types as bt', 'shipments.booking_type_id', '=', 'bt.id')
            ->join('shipment_status as ss', 'shipments.shipper_status_id', '=', 'ss.id')
            ->join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities as ohc', 'oc.hub_id', '=', 'ohc.id')
            ->leftJoin('shipping_modes as sm', 'shipments.shipping_mode_id', '=', 'sm.id')
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
            ->leftjoin('cities as olddhc', 'olddc.hub_id', '=', 'olddhc.id')
            ->leftjoin('intercept_re_book_request_histories as irbrh', function ($join) {
                $join->on('irbrh.shipment_id', '=', 'shipments.id')
                    ->on('shipments.shipper_status_id', '=', DB::raw(55));
            })
            ->leftjoin('cities as olddci', 'olddci.id', '=', 'irbrh.old_consignee_city_id')
            ->leftjoin('cities as olddhci', 'olddci.hub_id', '=', 'olddhci.id')
            ->join('cities as dc', function ($join) {
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
            ->join('cities as dhc', 'dc.hub_id', '=', 'dhc.id')
            ->leftjoin('crm_requests as crm', function ($join) {
                $join->on('crm.shipment_id', '=', 'shipments.id')
                    ->whereIn('crm.status_id', [2, 3, 5])
                    ->where('crm.case_nature_id', 1);
            })
            ->leftjoin('star_shippers as sts','sts.user_id','=','u.id')
            ->select('shipments.shipper_status_id', 'shipments.tracking_number', 'shipments.tracking_number as tracking', 'shipments.order_id', 'bt.booking_type as service_type', 'ss.name as status', 'oc.name as origin', 'dc.name as destination', 'u.name as shipper', 'shipments.amount', 'sm.mode as shipping_mode', 'shipments.created_at as booked_at', 'shipments_journey.created_at as arrival_at', 'shipments.booking_type_id', 'usi.poc', 'csj.created_at as current_status', 'olddc.name as old_destination', 'olddci.name as old_destination_intercept', 'crm.id as complaint', 'shipments.return_address_id', 'rc.name as return_city_name', 'ohc.name as origin_hub', 'dhc.name as destination_hub', 'olddhci.name as old_destination_intercept_hub', 'olddhc.name as old_destination_hub','shipments.consignee_address','dc.id as destination_city_id','sts.status as star_status')
            ->whereNotIn('shipments.id', $on_hold_shipments);

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

        if (session('department_id') == 8) {
            $shipments = $shipments->where('shipments.shipment_type',2);
        }

        $datatables = Datatables::of($shipments)
            ->setRowAttr([
                'class' => function ($shipments) {
                    if ($shipments->complaint != null) {
                        return 'complaint_row';
                    } else if ($shipments->booking_type_id == 3) {
                        return "tnb_row";
                    } else {
                        return '';
                    }
                },
            ])
              ->addColumn('sub_station', function ($shipments) {
                $check = DeliveryLocationMappingKeyword::join('delivery_location_mappings as dlm', 'delivery_location_mapping_keywords.mapping_id', '=', 'dlm.id')
                ->where('dlm.city_id', $shipments->destination_city_id)
                ->select('dlm.city_id','delivery_location_mapping_keywords.keyword', 'dlm.area_name as area_name');

                if($check->exists()){
                 $check = $check->get();   
                 $delivery_area = null;
                    $msg_string = null;
                    $str_arr = null;
                    $str_arr = preg_split('/[\s.,-,_,*,?,<,>,!,@,#,$,%,^,&,(,)]+/', $shipments->consignee_address);                    
                    foreach ($check as $nsa) {
                        foreach ($str_arr as $arr_value) {
                            if (strtolower($nsa->keyword) == strtolower($arr_value)) {
                                $msg_string = $arr_value;
                                $delivery_area = $nsa->area_name;
                            }
                        }
                    }
                }
           
//                 $delivery_area = null;
//                 if ($msg_string != null) {
//                     $found = DeliveryLocationMappingKeyword::join('delivery_location_mappings as dlm', 'delivery_location_mapping_keywords.mapping_id', '=', 'dlm.id')
//                         ->where('delivery_location_mapping_keywords.keyword', $msg_string)
//                         ->where('dlm.city_id', $shipments->destination_city_id)
// //                        ->orderBy('delivery_location_mapping_keywords.created_at','desc')
//                         ->select('dlm.area_name as area_name', 'dlm.id', 'delivery_location_mapping_keywords.mapping_id');

//                     if ($found->exists()) {
//                         $found = $found->first();
//                         $delivery_area = $found->area_name;
//                     }
//                 }
                return isset($delivery_area) ? $delivery_area : '-';
            })
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('admin.tracking.index');
                if ($shipments->star_status)
                {
                    return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'><i class='star_shippers_icon'></i>$shipments->tracking_number</a></u>";
                }
                else
                {
                    return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
                }
            })
            ->editColumn('origin', function ($shipments) {
                if (in_array($shipments->shipper_status_id, [20, 30, 37])) {
                    return $shipments->destination;
                } else if ($shipments->shipper_status_id == 49) {
                    return $shipments->old_destination;
                } else if ($shipments->shipper_status_id == 55) {
                    return $shipments->old_destination_intercept;
                } else {
                    return $shipments->origin;
                }
            })
            ->editColumn('origin_hub', function ($shipments) {
                if (in_array($shipments->shipper_status_id, [20, 30, 37])) {
                    return $shipments->destination_hub;
                } else if ($shipments->shipper_status_id == 49) {
                    return $shipments->old_destination_hub;
                } else if ($shipments->shipper_status_id == 55) {
                    return $shipments->old_destination_intercept_hub;
                } else {
                    return $shipments->origin_hub;
                }
            })
            ->editColumn('destination', function ($shipments) {
                if (in_array($shipments->shipper_status_id, [30, 37])) {
                    return $shipments->origin;
                } else if ($shipments->shipper_status_id == 20) {
                    if ($shipments->return_address_id != NULL) {
                        return $shipments->return_city_name;
                    } else {
                        return $shipments->origin;
                    }
                } else {
                    return $shipments->destination;
                }
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper . ' (' . $shipment->poc . ')';
                } else {
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
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->orderColumn('u.name', 'u.name $1, usi.poc $1')
            ->filterColumn('status', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('ss.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('service_type', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('bt.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('shipping_mode', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('sm.id', $keyword);
                } else {
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
            ->filterColumn('ohc.name', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->whereIn('shipments.shipper_status_id', [20, 30, 37])
                        ->where('dhc.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.shipper_status_id', 2)
                            ->where('ohc.name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.shipper_status_id', 49)
                            ->where('olddhc.name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.shipper_status_id', 55)
                            ->where('olddhci.name', 'like', '%' . $keyword . '%');
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
            ->orderColumn('ohc.name', DB::raw('IF (shipments.shipper_status_id IN (20, 30, 37), dhc.name, IF (shipments.shipper_status_id = 49, olddhc.name, IF (shipments.shipper_status_id = 55, olddhci.name, oc.name)))') . ' $1')
            ->orderColumn('dc.name', DB::raw('IF (shipments.shipper_status_id IN (20, 30, 37), oc.name, dc.name)') . ' $1');
          

        if ($shipment_type = $request->get('shipment_type')) {
            if ($shipment_type == 0) {
                $datatables->whereIn('shipments.shipper_status_id', [2, 20, 30, 37, 49, 55]);
            } else if ($shipment_type == 1) {
                $datatables->whereIn('shipments.shipper_status_id', [2, 49, 55]);
            } else if ($shipment_type == 2) {
                $datatables->whereIn('shipments.shipper_status_id', [20, 30, 37]);
            }
        } else {
            $datatables->whereIn('shipments.shipper_status_id', [2, 20, 30, 37, 49, 55]);
        }
        if ($mode = $request->get('search_shipping_mode')) {
            $datatables->where('sm.id', '=', $mode);
        }

        if($request->get('star_shipper_filter') == 1)
        {
            $datatables->where('sts.status',1);
        }

        return $datatables->make(true);
    }

    public function create_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 400);
        return view('admin.cargo.manifest.bags.create')->with('print', session('print'));
    }

    public function create_bag_details(Request $request)
    {

        $shipment = Shipment::find(current($request->shipment_ids));

        $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
        if(!$dispute_check){
            return ['status' => 1, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
        }

        if ($shipment->shipper_status_id == 49) {
            $shipment_details = $shipment->misrouted_history()->latest()->first();
            $city_details = City::find($shipment_details->old_consignee_city_id);
            $origin = $city_details->hub_city;
        } else if ($shipment->shipper_status_id == 55) {
            $shipment_details = $shipment->intercept_history;
            $city_details = City::find($shipment_details->old_consignee_city_id);
            $origin = $city_details->hub_city;
        } else {
            if ($shipment->shipper_status_id == 20) {
                if ($shipment->return_address_id != null) {
                    $origin = $shipment->return_address->city->hub_city;
                } else {
                    $origin = $shipment->pickup_address->city->hub_city;
                }
            } else {
                $origin = $shipment->pickup_address->city->hub_city;
            }
        }

        $origin_details = array();

        if ($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 49 || $shipment->shipper_status_id == 55 || $shipment->shipper_status_id == 35 || $shipment->shipper_status_id == 37 || $shipment->shipper_status_id == 30) {
            $origin_details['id'] = $origin->id;
        } else {
            $origin_details['id'] = Auth::user()->default_hub_id;
        }

        $origin_details['name'] = $origin->name;

        $destination = $shipment->consignee_city->hub_city;

        $destination_details = array();

        $destination_details['id'] = $destination->id;
        $destination_details['name'] = $destination->name;

        $details = array();

        if ($request->bag_type == 1) {
            $details['origin'] = $origin_details;
            $details['destination'] = $destination_details;
        } else {
            $details['origin'] = $destination_details;
            $details['destination'] = $origin_details;
        }

        $details['actual_weight'] = 0;

        foreach ($request->shipment_ids as $shipment_id) {
            $shipment_actual_weight = Shipment::find($shipment_id);
            $details['actual_weight'] = $details['actual_weight'] + $shipment_actual_weight->actual_weight;
        }

        return $details;
    }

    public function create_bag_seal_number(Request $request)
    {
        if ($request->filled('seal_number')) {
            $seal_number = CargoManifestBag::where('seal_number', $request->input('seal_number'))->where('open_bag', 0);

            if ($request->has('id')) {
                $seal_number = $seal_number->where('id', '!=', $request->input('id'));
            }

            if (!$seal_number->exists()) {
                return 'true';
            } else {
                return 'false';
            }
        } else {
            return 'false';
        }
    }

    public function create_shipment_details(Request $request)
    {
        $misrouted_history_hub = 0;
        $intercept_re_book_history_hub = 0;

        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();

          /*  if($shipment->shipper_status_id == 49){
                return ['status' => 1, 'error' => 'Misroute-Forwarded Shipments not allowed'];
            }*/
            
           /* if(CargoManifestBag::where('seal_number',$shipment->tracking_number)->where('status_id',1)->exists()){
                return ['status' => 1, 'error' => 'Bag Already Created'];
            }


            if($shipment->consignee_city->hub_city->id ==  Auth::user()->default_hub_id){
                return ['status' => 1, 'error' => 'Cannot create bag with same origin and destination'];
            }*/

            $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
            if(!$dispute_check){
                return ['status' => 1, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
            }

            if ($shipment->shipper_status_id == 55) {

                $intercept_rebook_history = InterceptReBookRequestHistory::where('shipment_id', $shipment->id)->latest()->first();
                if ($intercept_rebook_history) {
                    $intercept_re_book_history_hub = $intercept_rebook_history->old_consignee_city->hub_id;

                    if($intercept_re_book_history_hub == $intercept_rebook_history->new_consignee_city->hub_id){
                        return ['status' => 1, 'error' => 'Cannot create bag for same hub'];
                    }
                }

            }

            if ($shipment->shipper_status_id == 49) {
                $misrouted_history = MisroutedHistory::where('shipment_id', $shipment->id)->latest()->first();
                if ($misrouted_history) {
                    $city = City::where('id', $misrouted_history->old_consignee_city_id)->first();
                    $misrouted_history_hub = $city->hub_id;
                }
            }


            if (
                (($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 35 || $shipment->shipper_status_id == 37 || $shipment->shipper_status_id == 30) && in_array($shipment->consignee_city->hub_id, session('hubs')))

                || (($shipment->shipper_status_id != 35 && $shipment->shipper_status_id != 37 && $shipment->shipper_status_id != 20 && $shipment->shipper_status_id != 30 && $shipment->shipper_status_id != 55 && $shipment->shipper_status_id != 49) && in_array($shipment->pickup_address->city->hub_id, session('hubs')))

                || (($shipment->shipper_status_id == 55 && in_array($intercept_re_book_history_hub, session('hubs'))) || ($shipment->shipper_status_id == 49 && in_array($misrouted_history_hub, session('hubs'))))
            ) {


                $on_hold_shipment = ShipmentOnHold::where('shipment_id', $shipment->id)->where('status', 1);
                if ($on_hold_shipment->exists()) {
                    if (!in_array(Auth::id(), [10, 288, 423])) {
                        $on_hold_shipment = $on_hold_shipment->first();
                        $dispatch_date = Carbon::parse($on_hold_shipment->dispatch_date);
                        $today = Carbon::today();
                        if ($dispatch_date > $today) {
                            $dispatch_date = $dispatch_date->toFormattedDateString();
                            return ['status' => 1, 'error' => 'Shipment is marked as On-Hold until ' . $dispatch_date];
                        }
                    }
                }
                if ($shipment->packaging_material_request == 1) {


                    $packaging_material_request = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number)->first();

                    if ($packaging_material_request != null) {
                        if ($packaging_material_request->status_id != 3) {
                            return ['status' => 1, 'error' => 'Packaging Material Request is not dispatched yet!'];
                        }
                    }
                    $packaging_material_request_stock = WarehouseStockRequest::where('tracking_number', $shipment->tracking_number)->first();
                    if ($packaging_material_request_stock != null) {
                        if ($packaging_material_request_stock->status_id != 3) {
                            return ['status' => 1, 'error' => 'Warehouse Stock Request is not dispatched yet!'];
                        }
                    }
                }


                if (in_array($shipment->shipper_status_id, [2, 20, 30, 37, 49, 55])) {
                    if ($shipment->shipper_status_id == 2) {
                        $hub_id = $shipment->pickup_address->city->hub_id;
                    } else if ($shipment->shipper_status_id == 49) {
                        $shipment_details = $shipment->misrouted_history()->latest()->first();
                        $city_details = City::find($shipment_details->old_consignee_city_id);
                        $hub_id = $city_details->hub_id;
                    } else if ($shipment->shipper_status_id == 55) {
                        $shipment_details = $shipment->intercept_history;
                        $city_details = City::find($shipment_details->old_consignee_city_id);
                        $hub_id = $city_details->hub_id;
                    } else {
                        $hub_id = $shipment->consignee_city->hub_id;
                    }

                    $allowed = FALSE;

                    if (session('role_id') == 1) {
                        $allowed = TRUE;
                    } else if (in_array($hub_id, session('hubs'))) {
                        $allowed = TRUE;
                    } else if ($shipment->shipper_status_id == 49 && in_array($shipment->pickup_address->city->hub_id, session('hubs'))) {
                        $allowed = TRUE;
                    }

                    if ($allowed) {
                        if (($shipment->pickup_address->city->hub_id != $shipment->consignee_city->hub_id) || (in_array($shipment->shipper_status_id, [49, 55]) && ($shipment->consignee_city->hub_id != $hub_id)) || ($shipment->shipper_status_id == 20)) {
                            if ($shipment->return_address_id != NULL) {
                                if (($shipment->shipper_status_id == 20) && ($shipment->consignee_city->hub_id == $shipment->return_address->city->hub_id)) {
                                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to same Origin and Destination Hub'];
                                }
                            }
                            else if(($shipment->shipper_status_id == 20) && ($shipment->consignee_city->hub_id == $shipment->pickup_address->city->hub_id)) {
                                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to same Origin and Destination Hub'];
                            }

                            if ($request->bag_type != 0) {
                                if (in_array($shipment->shipper_status_id, [2, 49, 55])) {
                                    $hub_id = $shipment->consignee_city->hub_id;
                                } else {
                                    if (in_array($shipment->shipper_status_id, [20])) {
                                        if ($shipment->return_address_id != null) {
                                            $hub_id = $shipment->return_address->city->hub_id;
                                        } else {
                                            $hub_id = $shipment->pickup_address->city->hub_id;
                                        }
                                    } else {
                                        $hub_id = $shipment->pickup_address->city->hub_id;
                                    }
                                }
                            } else {
                                $hub_id = 0;
                            }

                            if ($request->hub_id == 0 || $request->hub_id == $hub_id) {
                                /*  if ($request->shipping_mode_id == 0 || $request->shipping_mode_id == $shipment->shipping_mode->id) {*/
                                $details = array();

                                if ($request->bag_type != 0) {
                                    if ($request->bag_type == 1) {
                                        if (!in_array($shipment->shipper_status_id, [2, 49, 55])) {
                                            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of Return Type while the Cargo is Normal Type'];
                                        }

                                        $bag_type = 1;
                                    } else {
                                        if (!in_array($shipment->shipper_status_id, [20, 30, 37])) {
                                            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of Normal Type while the Cargo is Return Type'];
                                        }

                                        $bag_type = 2;
                                    }
                                } else {
                                    if (in_array($shipment->shipper_status_id, [2, 49, 55])) {
                                        $details['bag_type'] = 1;

                                        $bag_type = 1;
                                    } else {
                                        $details['bag_type'] = 2;

                                        $bag_type = 2;
                                    }
                                }

                                if ($bag_type == 1) {
                                    $destination = $shipment->consignee_city;
                                } else {
                                    if ($shipment->return_address_id != NULL) {
                                        $destination = $shipment->return_address->city;
                                    } else {
                                        $destination = $shipment->pickup_address->city;
                                    }

                                }
                                if (!$request->has('pieces_confirm')) {
                                    if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
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
                                            ->join('cities as dc', function ($join) {
                                                $join->on('shipments.consignee_city_id', '=', 'dc.id')
                                                    ->on('oc.hub_id', '!=', 'dc.hub_id');
                                            })
                                            ->select(DB::raw('count(shipments.id) as count'))
                                            ->where('dc.hub_id', $hub->id)
                                            ->whereIn('shipments.shipper_status_id', [2, 49, 55])
                                            ->where('shipments.shipping_mode_id', $shipping_mode_id);
                                    } else {

                                        $shipments = Shipment::leftjoin('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
                                            ->leftjoin('cities as dc', 'usi.city_id', '=', 'dc.id')
                                            ->leftjoin('user_shipping_infos as rsi', 'shipments.return_address_id', '=', 'rsi.id')
                                            ->leftjoin('cities as rc', 'rsi.city_id', '=', 'rc.id')
                                            ->join('cities as oc', function ($join) use ($hub) {
                                                $join->on('shipments.consignee_city_id', '=', 'oc.id')
                                                    ->where(function ($query) use ($hub) {
                                                        $query->where(function ($sub_query) use ($hub) {
                                                            $sub_query->whereIn('shipments.shipper_status_id', [30, 37])
                                                                ->where('dc.hub_id', '!=', 'oc.hub_id')
                                                                ->where('dc.hub_id', $hub->id);
                                                        })
                                                            ->orWhere(function ($sub_query) use ($hub) {
                                                                $sub_query->where('shipments.shipper_status_id', 20)
                                                                    ->where(function ($sub_sub_query) use ($hub) {
                                                                        $sub_sub_query->where(function ($sub_sub_sub_query) use ($hub) {
                                                                            $sub_sub_sub_query->whereNull('return_address_id')
                                                                                ->where('dc.hub_id', '!=', 'oc.hub_id')
                                                                                ->where('dc.hub_id', $hub->id);
                                                                        })
                                                                            ->orWhere(function ($sub_sub_sub_query) use ($hub) {
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
                                ShipmentScanningJourneyController::add($shipment->id, 2, 1, Auth::id(), null, null);

                                return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                                /*}
                                else {
                                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment\'s Shipment Mode is different'];
                                     }*/
                            } else {
                                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to another Hub'];
                            }
                        } else {
                            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to same Origin and Destination Hub'];
                        }
                    } else {
                        return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment does not belong to any of your assigned Hub\'s Cities'];
                    }
                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
                }
            } else {
                return ['status' => 1, 'error' => 'Shipment belongs to another hub'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }

    public function create_store(Request $request)
    {

        $shipments = 0;
        $quantity = 0;
        $shipments_weight = 0;

        $shipment_ids = explode(',', $request->input('shipment_ids'));
        $open_box_ids = explode(',', $request->input('open_box_ids'));
        foreach ($shipment_ids as $key => $shipment_id) {
            $shipment = Shipment::find($shipment_id);

            if ($shipment->shipper_status_id == 20) {
                $lost_shipment = ManifestBagLostShipment::where('shipment_id', $shipment_id)->latest()->first();
                if ($lost_shipment) {
                    $bag = CargoManifestBag::find($lost_shipment->bag_id);
                    if ($bag->lost_shipments > 0) {
                        $bag->lost_shipments--;
                        $bag->save();
                        $lost_shipment->delete();
                    }
                }
            }


            if (in_array($shipment->shipper_status_id, [2, 20, 30, 37, 49, 55])) {
                $shipments++;
                $shipments_weight += $shipment->actual_weight;
                $quantity = $quantity + count($shipment->items);
            } else {
                unset($shipment_ids[$key]);
            }
        }

        if (!empty($shipment_ids)) {

            $bag = new CargoManifestBag();
            $bag->seal_number = $request->input('seal_number');
            $bag->origin_hub_id = $request->input('origin_hub_id');
            $bag->destination_hub_id = $request->input('destination_hub_id');
            $bag->shipments = $shipments;
            $bag->quantity = $quantity;
            $bag->shipments_weight = $shipments_weight;
            $bag->actual_weight = $request->input('actual_weight');
            $bag->created_by = Auth::id();
            $bag->type = $request->input('bag_type');
            $bag->transport_mode_id = 2;
            $bag->status_id = 1;
            $bag->completed = 0;
            $bag->current_hub_id = Auth::user()->default_hub_id;
            $bag->save();

            CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), NULL, NULL,1);

            $id = $bag->id;

            foreach ($shipment_ids as $shipment_id) {

                $bag_shipment = new CargoManifestBagShipments();

                $bag_shipment->cargo_manifest_bag_id = $id;
                $bag_shipment->shipment_id = $shipment_id;

                $bag_shipment->save();

                $shipment = Shipment::find($shipment_id);

                $shipper_status_id = NULL;
                $consignee_status_id = NULL;

                if ($request->input('bag_type') == 1) {
                    $shipper_status_id = 3;
                    $consignee_status_id = 3;
                } else {
                    $shipper_status_id = 21;
                    $consignee_status_id = 21;

                    if ($shipment->shipper_status_id != 20) {
                        if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4 || $shipment->booking_type_id == 5) {
                            $shipper_status_id = 21;
                            $consignee_status_id = 21;
                        } else if ($shipment->booking_type_id == 2) {
                            $shipper_status_id = 26;
                            $consignee_status_id = 26;
                        } else if ($shipment->booking_type_id == 3) {
                            $shipper_status_id = 32;
                            $consignee_status_id = 32;
                        } else {
                            $shipper_status_id = 21;
                            $consignee_status_id = 21;
                        }
                    }
                }

                $shipment->shipper_status_id = $shipper_status_id;
                $shipment->consignee_status_id = $consignee_status_id;

                $shipment->save();


                if (in_array($shipment_id, $open_box_ids)) {
                    $shipment->open_box = 1;
                    $shipment->save();
                    ShipmentOpenBoxJourneyController::add($shipment_id, 1, Auth::id());
                }

                ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $consignee_status_id, NULL, NULL, NULL, Auth::id(), $bag->id);
            }

            return redirect()->route('admin.cargo_manifest.bags.create.index')->with(['success' => 'Bag Created with Bag Number: ' . $bag->seal_number]);
        } else {
            return back()->withErrors('All Shipments have already been added to another Bag!');
        }
    }

    public function create_open_bag_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 413);
        return view('admin.cargo.manifest.bags.create_open_bag')->with('print', session('print'));
    }

    public function create_open_bag_store(Request $request)
    {
        $shipments = 0;
        $quantity = 0;
        $shipments_weight = 0;

        $shipment_ids = explode(',', $request->input('shipment_ids'));
        $open_box_ids = explode(',', $request->input('open_box_ids'));
        foreach ($shipment_ids as $key => $shipment_id) {
            $shipment = Shipment::find($shipment_id);

            if (!in_array($shipment->shipper_status_id, [2, 20, 30, 37, 49, 55])) {
                unset($shipment_ids[$key]);
            }
        }

        $bag_numbers = '';
        if (!empty($shipment_ids)) {
            foreach ($shipment_ids as $shipment_id) {
                $shipment = Shipment::find($shipment_id);
                if (in_array($shipment->shipper_status_id, [2, 49, 55])) {
                    $bag_type = 1;
                    $destination = $shipment->consignee_city->hub_id;
                    $origin = Auth::user()->default_hub_id;
                } else {
                    $bag_type = 2;
                    if ($shipment->shipper_status_id == 20) {
                        if ($shipment->return_address_id != null) {
                            $destination = $shipment->return_address->city->hub_city->id;
                        } else {
                            $destination = $shipment->pickup_address->city->hub_city->id;
                        }

                        $origin = $shipment->consignee_city->hub_city->hub_id;
                    } else {
                        $destination = $shipment->pickup_address->city->hub_city->id;
                        $origin = Auth::user()->default_hub_id;
                    }
                }

                $bag = new CargoManifestBag();
                $bag->seal_number = $shipment->tracking_number;
                $bag->origin_hub_id = $origin;
                $bag->destination_hub_id = $destination;
                $bag->shipments = 1;
                $bag->quantity = count($shipment->items);
                $bag->shipments_weight = $shipment->actual_weight;
                $bag->actual_weight = $shipment->actual_weight;
                $bag->created_by = Auth::id();
                $bag->type = $bag_type;
                $bag->transport_mode_id = 2;
                $bag->status_id = 1;
                $bag->open_bag = 1;
                $bag->completed = 0;
                $bag->current_hub_id = Auth::user()->default_hub_id;
                $bag->save();

                CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), NULL, NULL,2);

                $id = $bag->id;
                $bag_shipment = new CargoManifestBagShipments();

                $bag_shipment->cargo_manifest_bag_id = $id;
                $bag_shipment->shipment_id = $shipment_id;

                $bag_shipment->save();


                $shipper_status_id = NULL;
                $consignee_status_id = NULL;

                if ($bag_type == 1) {
                    $shipper_status_id = 3;
                    $consignee_status_id = 3;
                } else {
                    $shipper_status_id = 21;
                    $consignee_status_id = 21;

                    if ($shipment->shipper_status_id != 20) {
                        if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4 || $shipment->booking_type_id == 5) {
                            $shipper_status_id = 21;
                            $consignee_status_id = 21;
                        } else if ($shipment->booking_type_id == 2) {
                            $shipper_status_id = 26;
                            $consignee_status_id = 26;
                        } else if ($shipment->booking_type_id == 3) {
                            $shipper_status_id = 32;
                            $consignee_status_id = 32;
                        } else {
                            $shipper_status_id = 21;
                            $consignee_status_id = 21;
                        }
                    }
                }

                $shipment->shipper_status_id = $shipper_status_id;
                $shipment->consignee_status_id = $consignee_status_id;

                $shipment->save();


                if (in_array($shipment_id, $open_box_ids)) {
                    $shipment->open_box = 1;
                    $shipment->save();
                    ShipmentOpenBoxJourneyController::add($shipment_id, 1, Auth::id());
                }

                ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $consignee_status_id, NULL, NULL, NULL, Auth::id(), $bag->id);

                if ($bag_numbers == '') {
                    $bag_numbers = $bag->seal_number;
                } else {
                    $bag_numbers = $bag_numbers . ', ' . $bag->seal_number;
                }
            }

            return redirect()->route('admin.cargo_manifest.bags.create.open_bag.index')->with(['success' => 'Open Bag Created with Bag Numbers: ' . $bag_numbers]);
        } else {
            return back()->withErrors('All Shipments have already been added to another Bag!');
        }
    }

    public function history_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 401);
        $transport_mode = TransportMode::all();
        $bag_statuses = CargoManifestBagStatus::all();
        $shipping_mode = ShippingMode::all();
        return view('admin.cargo.manifest.bags.history')->with(['bag_statuses' => $bag_statuses, 'transport_mode' => $transport_mode, 'shipping_mode' => $shipping_mode]);
    }

    public function history_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 402);
        }
        $bags = CargoManifestBag::join('cities as oh', 'cargo_manifest_bags.origin_hub_id', '=', 'oh.id')
            ->Leftjoin('manifest_bags as mcb', function ($join) {
                $join->on('mcb.cargo_manifest_bag_id', '=', 'cargo_manifest_bags.id')
                    ->where('mcb.id', '=',
                        DB::raw('(select max(id) from manifest_bags where manifest_bags.cargo_manifest_bag_id = cargo_manifest_bags.id)'));
            })
            ->leftjoin('cargo_manifests as cm', 'cm.id', '=', 'mcb.cargo_manifest_id')
            ->leftjoin('shipping_modes as sm', 'sm.id', '=', 'cm.shipping_mode_id')
            ->join('cities as dh', 'cargo_manifest_bags.destination_hub_id', '=', 'dh.id')
            ->join('admins as a', 'cargo_manifest_bags.created_by', '=', 'a.id')
            ->join('cargo_manifest_bag_statuses as bs', 'cargo_manifest_bags.status_id', '=', 'bs.id')
            ->join('transport_modes as tm', 'cargo_manifest_bags.transport_mode_id', '=', 'tm.id')
            ->select('cargo_manifest_bags.id', 'cargo_manifest_bags.status_id', 'oh.id as origin_id', 'oh.name as origin', 'dh.id as destination_id', 'dh.name as destination', 'cargo_manifest_bags.shipments', 'tm.name as transport_mode', 'cargo_manifest_bags.shipments_weight', DB::raw('(SELECT SUM(`s`.`chargeable_weight`) FROM `shipments` AS `s` INNER JOIN `cargo_manifest_bag_shipments` AS `bss` ON `s`.`id` = `bss`.`shipment_id` WHERE `bss`.`cargo_manifest_bag_id` = `cargo_manifest_bags`.`id`) AS `chargeable_weight`'), 'cargo_manifest_bags.actual_weight', 'a.name as transitted_by', 'oh.hub_id as origin_hub_id', 'dh.hub_id as destination_hub_id', 'cargo_manifest_bags.type as bag_type', 'cargo_manifest_bags.seal_number', 'bs.name as status', 'cm.id as manifest_id', 'cargo_manifest_bags.junction_mapping_id as junction_mapping_id', 'cargo_manifest_bags.created_at as transitted_at', 'cm.id as manifest', 'sm.mode as shipping_mode', 'cargo_manifest_bags.short_received_shipments as short_received_shipments', 'cargo_manifest_bags.short_received_shipments as short_received', 'cargo_manifest_bags.lost_shipments as lost_shipments', 'cargo_manifest_bags.lost_shipments as ls', 'cargo_manifest_bags.received_at as received_at');


        if (session('role_id') != 1) {
            $bags = $bags->where(function ($query) {
                $query->whereIn('oh.hub_id', session('hubs'))->orWhereIn('dh.hub_id', session('hubs'));
            });
        }

        $datatables = Datatables::of($bags)
            ->addColumn('shipments_count', function ($bag) {
                return $bag->shipments;
            })
            ->editColumn('lost_shipments', function ($bag) {
                if ($bag->lost_shipments > 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $bag->lost_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('manifest_id', function ($bag) {
                if ($bag->manifest_id) {
                    return '<button class="btn btn-sm btn-outline-info align-middle print "><i class="la la-lg la-print align-middle "></i> <span class="align-middle id">' . str_pad($bag->manifest_id, 6, '0', STR_PAD_LEFT) . '</span></button>';
                }

            })
            ->addColumn('short_received_shipments', function ($bag) {
                if ($bag->short_received_shipments > 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $bag->short_received_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('bag_type', function ($bag) {
                if ($bag->bag_type == 1) {
                    return 'Normal';
                } else {
                    return 'Return';
                }
            })
            ->addColumn('shipments', function ($bag) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $bag->shipments . '</button>';
            })
            ->addColumn('junctions', function ($bag) {
                $junctions = V2Junctions::where('junction_mapping_id', $bag->junction_mapping_id)->get();
                $junction_data = '';


                foreach ($junctions as $junction) {
                    $city = City::find($junction->junction_id);
                    $junction_data .= "<a href='https://www.google.com/maps/?q=" . $city->hub_location_latitude . "," . $city->hub_location_longitude . "' target='_blank' class='btn btn-sm btn-outline-info align-middle'><i class='ft-map-pin'></i></a> " . $city->name . "<br><br>";

                }
                return $junction_data;
                // return $route_management->id;
            })
            ->filterColumn('cargo_manifest_bags.seal_number', function ($query, $keyword) {
                return $query->where('cargo_manifest_bags.seal_number', '=', $keyword);
            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword != '') {
                    $query->where('bs.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            });

        if ($bag_type = $request->get('bag_type')) {
            if ($bag_type != 0) {
                $datatables->where('cargo_manifest_bags.type', $bag_type);
            }
        }

        if ($tracking_number = $request->get('tracking_number')) {
            $datatables->join('cargo_manifest_bag_shipments as bssh', 'cargo_manifest_bags.id', '=', 'bssh.cargo_manifest_bag_id')
                ->join('shipments as s', 'bssh.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
        }

        if ($bag_number = $request->get('bag_number')) {
            $datatables->where('cargo_manifest_bags.seal_number', '=', $bag_number);
        }

        return $datatables->make(true);
    }

    public function create_manifest()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 403);
        if (Auth::user()->default_hub_id == null) {
            return back()->with(['error' => 'Default Hub not set for this admin.']);
        }
        $shipping_modes = ShippingMode::all();
        //$draft_bags = CargoManifestDraftBags::where('added_by',Auth::id())->pluck('bag_id','weight')->toArray();
        $draft_bags = CargoManifestDraftBags::where('added_by', Auth::id())->get();
        $bag_destinations = array();
        $total_bags = 0;
        $scanned_bags = count($draft_bags);
        if(count($draft_bags) > 0){
            foreach ($draft_bags as $draft_bag){
                if(!in_array($draft_bag->destination_id, $bag_destinations)){
                    $bag_destinations[] = $draft_bag->destination_id;
                }
            }
            $total_bags = CargoManifestBag::whereIn('destination_hub_id', $bag_destinations)->whereIn('status_id', [1, 3, 5])->where('current_hub_id', Auth::user()->default_hub_id)->count();
        }
        return view('admin.cargo.manifest.create', compact('shipping_modes', 'draft_bags', 'bag_destinations', 'total_bags', 'scanned_bags'));
    }

    public function bag_details(Request $request)
    {
        $bag = CargoManifestBag::with('shipment','shipment.shipment')->where('seal_number', $request->bag_number);
        $bag_destinations = $request->bag_destinations;

        if ($bag->exists()) {
            $bag = $bag->latest()->first();

            ShipmentScanningJourneyController::seal_number_add($bag->id, 1, Auth::id());

            if (in_array($bag->status_id, [1, 3, 5])) {
                $origin_id = Auth::user()->default_hub_id;
                $destination_hub_id = $bag->destination_hub_id;
                $mapping = V2JunctionMapping::where('origin_id', $origin_id)->where('destination_id', $destination_hub_id)->where('status', 1);

                $allowed = FALSE;

                if ($mapping->exists()) {
                    $allowed = TRUE;
                } else if ($bag->junction_mapping_id != null) {
                    $allowed = TRUE;
                }

                if ($allowed) {
                    if (!CargoManifestDraftBags::where('bag_id', $bag->id)->exists()) {
                        $bag->actual_weight = $bag->shipments_weight;
                        $bag->update();
                        $details = array();

                        $origin = City::find($origin_id);
                        $destination = $bag->destination_hub;

                        $details['id'] = $bag->id;
                        $details['bag_number'] = $bag->seal_number;
                        $details['shipments'] = $bag->shipments;
                        $details['origin'] = $origin->name;
                        $details['destination'] = $destination->name;
                        $details['bag_weight'] = $bag->shipments_weight;

                        $pieces_sum= collect($bag->shipment->pluck('shipment')->toArray())->sum('pieces');
                        $details['pieces_count'] = $pieces_sum;

                        $draft_bags = CargoManifestDraftBags::where('added_by', Auth::id())->get();
                        $bag_destinations = array();
                        if(count($draft_bags) > 0){
                            foreach ($draft_bags as $draft_bag){
                                if(!in_array($draft_bag->destination_id, $bag_destinations)){
                                    $bag_destinations[] = $draft_bag->destination_id;
                                }
                            }
                        }
                        if(is_array($bag_destinations)){
                            if(!in_array($bag->destination_hub_id, $bag_destinations)){
                                $bag_destinations[] = $bag->destination_hub_id;
                            }
                        }
                        else{
                            $bag_destinations[] = $bag->destination_hub_id;
                        }
                        $total_bags = CargoManifestBag::whereIn('destination_hub_id', $bag_destinations)->whereIn('status_id', [1, 3, 5])->where('current_hub_id', Auth::user()->default_hub_id)->count();
                        $details['bag_destinations'] = $bag_destinations;
                        $details['total_bags'] = $total_bags;


                        CargoManifestDraftBags::create(['bag_id' => $bag->id, 'seal_number' => $bag->seal_number, 'shipments_count' => $bag->shipments, 'origin_id' => $origin->id, 'destination_id' => $destination->id, 'added_by' => Auth::id(), 'weight' => $bag->shipments_weight,'pieces_count'=>$pieces_sum]);

                        return ['status' => 0, 'success' => 'Bag has been added', 'details' => $details];
                    } else {
                        return ['status' => 1, 'error' => 'Bag Number has already been added by other admin'];
                    }
                } else {
                    return ['status' => 1, 'error' => 'Given Bag Number\'s does not have any mapping'];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Bag Number\'s has already been modified'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Bag with given Bag Number is present'];
        }
    }

    public function total_bags_info(Request $request){
        $draft_bags = CargoManifestDraftBags::where('added_by', Auth::id())->get();
        $bag_destinations = array();
        $bag_ids = array();
        if(count($draft_bags) > 0){
            foreach ($draft_bags as $draft_bag){
                if(!in_array($draft_bag->destination_id, $bag_destinations)){
                    $bag_destinations[] = $draft_bag->destination_id;
                }
                $bag_ids[] = $draft_bag->bag_id;
            }
        }
        $total_bags = CargoManifestBag::whereIn('destination_hub_id', $bag_destinations)->whereIn('status_id', [1, 3, 5])->where('current_hub_id', Auth::user()->default_hub_id)->orderBy('destination_hub_id')->get();
        $details = array();
        foreach ($total_bags as $total_bag){
            $details[$total_bag->id]['seal_number'] = $total_bag->seal_number;
            $details[$total_bag->id]['origin'] = $total_bag->origin_hub->name;
            $details[$total_bag->id]['destination'] = $total_bag->destination_hub->name;
            $details[$total_bag->id]['status'] = $total_bag->status->name;

            if(in_array($total_bag->id, $bag_ids)){
                $details[$total_bag->id]['class'] = 'bg-success white';
            }
            else{
                $details[$total_bag->id]['class'] = '';
            }

        }
        return ['status' => 0, 'details' => $details];
    }

    public function cargo_details(Request $request)
    {
        $bags = CargoManifestBag::with('shipment','shipment.shipment')->whereIn('id', $request->bag_ids);

        if ($bags->exists() && $bags->count() == count($request->bag_ids)) {
            $bags = $bags->get();
            $origin_id = Auth::user()->default_hub_id;
            $details = [];
            $pieces_sum = 0;
            $remarks = $request->remarks;
            foreach ($bags as $bag) {
                $cargo_draft_bag = CargoManifestDraftBags::where('bag_id', $bag->id);
                if($cargo_draft_bag->exists()){
                    $cargo_draft_bag = $cargo_draft_bag->first();
                    $remark = isset($remarks[$bag->id]) ? $remarks[$bag->id] : null;
                    $pieces_sum+= $cargo_draft_bag->pieces_count;
                    $cargo_draft_bag->remarks_created_at = !empty($cargo_draft_bag->created_at) ? $cargo_draft_bag->created_at  : Carbon::now();
                    $cargo_draft_bag->remarks_added_by = !empty($cargo_draft_bag->remarks_added_by) ? $cargo_draft_bag->remarks_added_by  : Auth::id();
                    if($cargo_draft_bag->remarks != $remark) {
                        $cargo_draft_bag->remarks_updated_at = Carbon::now();
                        $cargo_draft_bag->remarks_added_by = Auth::id();
                    }
                    $cargo_draft_bag->remarks = $remark;
                    $cargo_draft_bag->save();
                }
//                $details[$bag->destination_hub_id]['origin_id'] = $origin_id;
                if (!isset($details[$bag->destination_hub_id]["bag_ids"])) {
                    $details[$bag->destination_hub_id]["bag_ids"] = array();
                }
                array_push($details[$bag->destination_hub_id]["bag_ids"], $bag->id);
                $details[$bag->destination_hub_id]["number_of_bags"] = isset($details[$bag->destination_hub_id]["number_of_bags"]) ? $details[$bag->destination_hub_id]["number_of_bags"] + 1 : 1;
//                $details[$bag->destination_hub_id]['shipments'] = isset($details[$bag->destination_hub_id]["shipments"]) ? $details[$bag->destination_hub_id]["shipments"] + $bag->shipments : $bag->shipments;
//                $details[$bag->destination_hub_id]["destination_id"] = $bag->destination_hub_id;
                $details[$bag->destination_hub_id]["destination"] = $bag->destination_hub->name;

                $mapping = V2JunctionMapping::where([['origin_id', $origin_id], ['destination_id', $bag->destination_hub_id], ['status', 1]]);
                if ($mapping->exists()) {
                    $mapping = $mapping->first();
                } else if ($bag->junction_mapping_id != null) {
                    $mapping = V2JunctionMapping::find($bag->junction_mapping_id);
                } else {
                    return ['status' => 1, 'error' => 'One or More Bag does not have any mapping..'];
                }
                $details[$bag->destination_hub_id]["junctions"] = array();
                foreach ($mapping->junctions as $junction) {
                    array_push($details[$bag->destination_hub_id]["junctions"], City::where('id', $junction->junction_id)->first()->name);
                }
                $details[$bag->destination_hub_id]["junctions"] = array_unique($details[$bag->destination_hub_id]["junctions"]);

                $details[$bag->destination_hub_id]['vehicles'] = array();
                foreach ($mapping->routes as $routes) {
                    foreach ($routes->vehicles as $vehicles) {
                        array_push($details[$bag->destination_hub_id]['vehicles'], $vehicles->vehicle_id);
                    }
                }

            }
            $previous_flag = false;
            $previous_array = array();
            foreach ($details as $detail) {

                if ($previous_flag) {
                    $previous_array = array_intersect($previous_array, $detail['vehicles']);
                } else {
                    $previous_flag = true;
                    $previous_array = $detail['vehicles'];
                }
            }
            $vehicles = Fleet::leftjoin('fleet_vendors as fv', 'fv.id', 'fleets.vendor_id')
                ->leftjoin('fleet_drivers as fd', 'fd.id', 'fleets.driver_id')
                ->select(['fleets.id as id', 'fv.name as vendor_name', 'fd.name as driver_name', 'fd.phone_no as driver_phone', 'fleets.reg_number as reg_number'])
                ->whereIn('fleets.id', $previous_array)
                ->get()
                ->toArray();
            $data['vehicles'] = $vehicles;
            $data['details'] = $details;
            $data['pieces'] = $pieces_sum;

            return ['status' => 0, 'details' => $data];

        } else {
            return ['status' => 1, 'error' => 'Bag Not Found..'];
        }
    }

    public function store_manifest(Request $request)
    {
        $error_hubs = array();
        $success_cargo_ids = array();
        foreach ($request->bag_ids as $hub_id => $bag_ids_array) {
            $bags = 0;
            $bags_weight = 0;
            $actual_weight = 0;
            $shipments = 0;
            $bag_ids = explode(',', $bag_ids_array);
            foreach ($bag_ids as $key => $bag_id) {
                $bag = CargoManifestBag::find($bag_id);
                $cargo_manifest_draft_bag = CargoManifestDraftBags::where('bag_id',$bag_id);
                if($cargo_manifest_draft_bag->exists()){
                    $cargo_manifest_draft_bag = $cargo_manifest_draft_bag->latest()->first();

                    $cargo_manifest_bag_remarks = new CargoManifestBagRemarks();
                    $cargo_manifest_bag_remarks->bag_id = $bag_id;
                    $cargo_manifest_bag_remarks->remarks = $cargo_manifest_draft_bag->remarks;
                    $cargo_manifest_bag_remarks->added_by = $cargo_manifest_draft_bag->remarks_added_by;
                    $cargo_manifest_bag_remarks->created_at = $cargo_manifest_draft_bag->remarks_created_at;
                    $cargo_manifest_bag_remarks->updated_at = $cargo_manifest_draft_bag->remarks_updated_at;
                    $cargo_manifest_bag_remarks->pieces_count = $cargo_manifest_draft_bag->pieces_count;
                    $cargo_manifest_bag_remarks->save();
                }

                if (in_array($bag->status_id, [1, 3, 5])) {
                    $bags++;
                    $bags_weight += $bag->shipments_weight;
                    $actual_weight += $bag->shipments_weight;
                    $shipments += $bag->shipments;

                } else {
                    unset($bag_ids[$key]);
                }
            }

            if (!empty($bag_ids)) {
                $master_cargo = new CargoManifest();

                $master_cargo->origin_hub_id = Auth::user()->default_hub_id;
                $master_cargo->destination_hub_id = $hub_id;
                $master_cargo->shipping_mode_id = $request->input('shipping_mode');
                $master_cargo->transport_mode_id = 2;

                $master_cargo->bags = $bags;
                $master_cargo->shipments = $shipments;
                $master_cargo->bags_weight = $bags_weight;
                $master_cargo->actual_weight = $actual_weight;
                $master_cargo->created_by = Auth::id();

                $master_cargo->vehicle_seal_number = $request->vehicle_seal[$hub_id];

                if($request->has('vehicle_type'))
                {
                    $master_cargo->vehicle_type = 1;
                    $master_cargo->vehicle_id = $request->vehicle_number;
                } else {
                    $master_cargo->vehicle_type = 2;
                    $master_cargo->vehicle_number = $request->vehicle_number_text;
                }

                $master_cargo->driver_name = $request->driver_name;
                $master_cargo->driver_phone = $request->driver_phone;
                $master_cargo->vendor_name = $request->vendor_name;
                $master_cargo->route_name = $request->route_name;

                $master_cargo->status_id = 1;
                $master_cargo->save();

                $master_cargo_id = $master_cargo->id;

                foreach ($bag_ids as $bag_id) {
                    $mater_cargo_bags = new ManifestBag();

                    $mater_cargo_bags->cargo_manifest_id = $master_cargo_id;
                    $mater_cargo_bags->cargo_manifest_bag_id = $bag_id;

                    $mater_cargo_bags->save();

                    $bag = CargoManifestBag::find($bag_id);

                    if ($bag->status_id == 1) {
                        $bag->status_id = 2;
                    } else if ($bag->status_id == 3) {
                        $bag->status_id = 4;
                    } else if ($bag->status_id == 5) {
                        $bag->status_id = 6;
                        foreach ($bag->shipment as $shipment) {
                            ShipmentsJourneyController::add($shipment->shipment_id, 49, 49, null, null, null, Auth::id(), $bag->seal_number);
                            $shipment_table = Shipment::find($shipment->shipment_id);
                            $shipment_table->shipper_status_id = 49;
                            $shipment_table->consignee_status_id = 49;
                            $shipment_table->update();

                            MisroutedHistory::create([
                                'shipment_id' => $shipment_table->id,
                                'old_consignee_city_id' => Auth::user()->default_hub_id,
                                'old_consignee_name' => $shipment_table->consignee_name,
                                'old_consignee_address' => $shipment_table->consignee_address,
                                'old_consignee_phone_number_1' => $shipment_table->consignee_phone_number_1,
                                'old_consignee_phone_number_2' => $shipment_table->consignee_phone_number_2,
                                'old_consignee_email' => $shipment_table->consignee_email,
                                'new_consignee_city_id' => $shipment_table->consignee_city_id,
                                'new_consignee_name' => $shipment_table->consignee_name,
                                'new_consignee_address' => $shipment_table->consignee_address,
                                'new_consignee_phone_number_1' => $shipment_table->consignee_phone_number_1,
                                'new_consignee_phone_number_2' => $shipment_table->consignee_phone_number_2,
                                'new_consignee_email' => $shipment_table->consignee_email,
                                'admin_id' => Auth::id()
                            ]);
                        }
                    }

                    if ($bag->junction_mapping_id == null) {
                        $mapping = V2JunctionMapping::where([['origin_id', Auth::user()->default_hub_id], ['destination_id', $hub_id], ['status', 1]])->first();
                        $bag->junction_mapping_id = $mapping->id;
                        $master_cargo->junction_mapping_id = $mapping->id;
                        $master_cargo->update();
                    } else {
                        $master_cargo->junction_mapping_id = $bag->junction_mapping_id;
                        $master_cargo->update();
                    }
                    $bag->current_hub_id = Auth::user()->default_hub_id;
                    $bag->update();

                    CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), $master_cargo_id, 1,3);
                }

                $success_cargo_ids[$hub_id] = $master_cargo_id;

            } else {
                array_push($error_hubs, $hub_id);
            }
        }

        if (count($error_hubs) > 0) {
            $error = "Every Bag in following Hubs Is Already In Some Cargo Manifest. <br><ul>";
            foreach ($error_hubs as $error_hub) {
                $error .= "<li>" . City::find($error_hub)->name . "</li>";
            }
            $error .= "</ul>";
        } else {
            $error = FALSE;
        }

        if (count($success_cargo_ids) > 0) {
            $success = "Cargo Manifest Created Successfully. <br><ul>";
            foreach ($success_cargo_ids as $hub_id => $cargo_id) {
                $success .= "<li>For " . City::find($hub_id)->name . " with cargo manifest id " . str_pad($cargo_id, 6, '0', STR_PAD_LEFT) . "</li>";
            }
            $success .= "</ul>";
        } else {
            $success = FALSE;
        }


        foreach ($success_cargo_ids as $cargo_id) {
            $cargo_array = array($cargo_id);
            $cargo_array = implode(',', $cargo_array);
            $path = self::print($cargo_array, 1);
            $manifest = CargoManifest::find($cargo_id);
            NotificationsController::send(148, $manifest->destination_hub_id, url('/') . '/' . 'reports/cargo_manifest_' . str_pad($manifest->id, 6, '0', STR_PAD_LEFT) . '.pdf');
        }

        if ($request->filled('submit_and_print_form')) {

            $print = implode(',', $success_cargo_ids);

        } else {
            $print = FALSE;
        }

        CargoManifestDraftBags::where('added_by', Auth::id())->delete();

        return redirect()->route('admin.cargo_manifest.create')->with(['success_html' => $success, 'error_html' => $error, 'print' => $print]);
    }

    public static function print($cargo_manifest_ids, $type = NULL)
    {

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Manifest Slip & Checklist</title>

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
        $manifest_ids = explode(',', $cargo_manifest_ids);

        foreach ($manifest_ids as $id) {
            $cargo = CargoManifest::find($id);
            if ($cargo) {
                $sender = $cargo->sender;
                $receiver = ($cargo->received_by) ? $cargo->receiver : NULL;
                $html .= '</head>
                  <body>
                    <div>
                      <div class="cargo_slip">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Manifest Slip</strong></td>
                              <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                              </tr>
                            <tr>
                              <td class="color secondary"><strong>Destination Hub</strong></td>
                              <td>' . $cargo->destination_hub->name . '</td>
                              <td rowspan="9" class="text-center align-middle">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($cargo->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . str_pad($cargo->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                              </td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transit Date</strong></td>
                              <td>' . $cargo->created_at . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Shipping Mode</strong></td>
                              <td>' . $cargo->shipping_mode->mode . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transport Mode</strong></td>
                              <td>' . $cargo->transport_mode->name . '</td>
                            </tr>
                          
                         
                              ';
                /*  if($master_cargo->onward_forwarding == 1){
                      $html .= '<td class="color secondary"><strong>Onward Forwarding Cargo No.</strong></td>';
                  }
                  else{
                      $html .= '<td class="color secondary"><strong>Master Cargo No.</strong></td>';
                  }*/
                $html .= '
                            
                            <tr>
                              <td class="color secondary"><strong>No. of Bags</strong></td>
                              <td>' . $cargo->bags . '</td>
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
                              <td>' . $sender->role->name . ' - ' . $sender->role->department->name . '</td>
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
                              <td class="text-center">' . $cargo->origin_hub->name . '-' . $cargo->destination_hub->name . '</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>

                      <div class="cargo_checklist">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Manifest Checklist</strong></td>
                              <td colspan="4" class="text-center align-middle  color secondary">Printed at ' . Carbon::now() . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Origin Hub</strong></td>
                              <td>' . $cargo->origin_hub->name . '</td>
                              <td class="color secondary"><strong>Driver Name</strong></td>
                              <td>' . $cargo->driver_name . '</td>
                              <td rowspan="6" class="text-center align-middle">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($cargo->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . str_pad($cargo->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                              </td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Destination Hub</strong></td>
                              <td>' . $cargo->destination_hub->name . '</td>
                              <td class="color secondary"><strong>Vehicle Number</strong></td>
                              <td>' . (($cargo->vehicle_id) ? $cargo->fleet->reg_number : $cargo->vehicle_number) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transit Date</strong></td>
                              <td>' . $cargo->created_at . '</td>
                              <td class="color secondary"><strong>Contact Phone</strong></td>
                              <td>' . (($cargo->vehicle_id) ? $cargo->fleet->driver->phone_no : $cargo->driver_phone) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>No. of Bags</strong></td>
                              <td>' . $cargo->bags . '</td>';

                          if($cargo->vehicle_seal_number != null)
                          {
                              $html .= '<td class="color secondary"><strong>Vehicle Seal Number</strong></td>
                            <td>' . $cargo->vehicle_seal_number . '</td>';
                          }
                          else{
                              $html .= '<td colspan="2"></td>';
                          }

                            $html .='</tr>
                            <tr>
                              <td class="color secondary"><strong>No. of Shipments</strong></td>
                              <td>' . $cargo->shipments . '</td>
                              <td colspan="2"></td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Total Weight</strong></td>
                              <td>' . $cargo->actual_weight . '</td>
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

                foreach ($cargo->manifest_bags as $manifest_cargo_bag) {
                    $bag = $manifest_cargo_bag->bag;


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

                $html .= '
                          </tbody>
                        </table>
                        <hr>';

                if ($type == 1) {

                    $html .= '
                        
                      </div>
                    </div>
                  </body>
                </html>
      ';
                    $pdf = SnappyPDF::loadHTML($html)->save('reports/cargo_manifest_' . str_pad($cargo->id, 6, '0', STR_PAD_LEFT) . '.pdf');
                    return $pdf;
                }
            }
        }

        $html .= '
                   
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

    public function manifest_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 404);
        $shipping_mode = ShippingMode::all();
        $bag_status = CargoManifestBagStatus::all();
        $hubs = City::where('status','1')->where('business_category_id','1')->where('hub','1')->select('id','name')->get();

        return view('admin.cargo.manifest.index')->with(['shipping_mode' => $shipping_mode, 'bag_status' => $bag_status, 'hubs'=>$hubs]);
    }

    public function manifest_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 405);
        }
        $bags = CargoManifestBag::leftjoin('manifest_bags as mcb', function ($join) {
            $join->on('mcb.cargo_manifest_bag_id', '=', 'cargo_manifest_bags.id')
                ->where('mcb.id', '=',
                    DB::raw('(select max(id) from manifest_bags where manifest_bags.cargo_manifest_bag_id = cargo_manifest_bags.id)'));
        })
            ->leftjoin('cargo_manifests as cm', 'cm.id', '=', 'mcb.cargo_manifest_id')
            ->leftjoin('cargo_manifest_bag_journeys as cmbj', function ($join) {
                $join->on('cmbj.cargo_manifest_bag_id', '=', 'cargo_manifest_bags.id')
                    ->where('cmbj.id', '=', DB::raw('(select max(id) from cargo_manifest_bag_journeys where cargo_manifest_bag_journeys.cargo_manifest_bag_id = cargo_manifest_bags.id)'));
            })
            ->leftjoin('admins as status_editor', 'status_editor.id', '=', 'cmbj.admin_id')
            ->leftjoin('cities as sedh', 'sedh.id', '=', 'status_editor.default_hub_id')
            ->join('cities as oh', 'cargo_manifest_bags.origin_hub_id', '=', 'oh.id')
            ->join('cities as dh', 'cargo_manifest_bags.destination_hub_id', '=', 'dh.id')
            ->leftjoin('shipping_modes as sm', 'cm.shipping_mode_id', '=', 'sm.id')
            ->leftjoin('admins as a', 'cargo_manifest_bags.created_by', '=', 'a.id')
            ->leftjoin('admins as ah', 'cargo_manifest_bags.updated_by', '=', 'ah.id')
            ->join('transport_modes as tm', 'cargo_manifest_bags.transport_mode_id', '=', 'tm.id')
            ->join('cargo_manifest_bag_statuses as bs', 'cargo_manifest_bags.status_id', '=', 'bs.id')
            ->select('cargo_manifest_bags.id', 'cargo_manifest_bags.status_id as status_id', 'oh.id as origin_id', 'oh.name as origin', 'dh.id as destination_id', 'dh.name as destination', 'cargo_manifest_bags.shipments', 'cargo_manifest_bags.quantity', 'tm.name as transport_mode', 'cargo_manifest_bags.shipments_weight', 'cargo_manifest_bags.actual_weight', 'cargo_manifest_bags.shipments as bag_shipments', 'a.name as transitted_by', 'oh.hub_id as origin_hub_id', 'dh.hub_id as destination_hub_id', 'cargo_manifest_bags.type as bag_type', 'cargo_manifest_bags.seal_number', 'bs.name as status', 'sm.mode as shipping_mode', 'cm.id as manifest_id', 'cargo_manifest_bags.seal_number', 'cm.created_at as manifest_created_at', 'cargo_manifest_bags.origin_hub_id as origin_hub_id', 'cargo_manifest_bags.destination_hub_id as destination_hub_id', 'cm.id as manifest', 'ah.name as updated_by', 'cargo_manifest_bags.created_at as transitted_date', 'cargo_manifest_bags.short_received_shipments as short_received_shipments', 'cargo_manifest_bags.short_received_shipments as short_shipments', 'cargo_manifest_bags.received_shipments', 'cmbj.created_at as status_updated_at', 'status_editor.name as status_updated_by', 'sedh.name as status_location',
                DB::raw('(select count(id) from cargo_manifest_bag_remarks where bag_id = cargo_manifest_bags.id) as remarks')
            )
            ->where(function ($query) {
                $query->where('cargo_manifest_bags.shipments', '!=', DB::raw('(select(received_shipments) from cargo_manifest_bags as cmb where cmb.id =cargo_manifest_bags.id)'))
                    ->orWhere('cargo_manifest_bags.completed', 0);
            });


        $datatables = Datatables::of($bags)
            ->setRowAttr([
                'class' => function ($bags) {
                    if ($bags->status_id == 7) {
                        return 'green';
                    } else if ($bags->status_id == 5 || $bags->status_id == 8 || $bags->status_id == 9) {
                        return 'red';
                    }
                },
            ])
            ->editColumn('bag_type', function ($bag) {
                if ($bag->bag_type == 1) {
                    return 'Normal';
                } else {
                    return 'Return';
                }
            })
            ->editColumn('manifest_id', function ($bag) {
                if ($bag->manifest_id) {
                    return '<button class="btn btn-sm btn-outline-info align-middle print "><i class="la la-lg la-print align-middle "></i> <span class="align-middle id">' . str_pad($bag->manifest_id, 6, '0', STR_PAD_LEFT) . '</span></button>';
                }

            })
            ->addColumn('action', function ($bag) {
                if ((session('role_id') == 1 || in_array(453, session('permissions'))) && $bag->status_id == 1) {
                    return '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                      <button type="button" class="dropdown-item edit_seal_number"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit Seal Number</div></button>
                    </div>
                  </div>
          ';
                } else {
                    return '';
                }
            })
            ->addColumn('bag_shipments', function ($bag) {
                if ($bag->bag_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $bag->shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('short_received_shipments', function ($bag) {
                if ($bag->short_received_shipments != 0) {

                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $bag->short_received_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('junctions', function ($bag) {
                $junction_data = '';
                $mapping = V2JunctionMapping::where('origin_id', $bag->origin_hub_id)->where('destination_id', $bag->destination_hub_id)->first();
                if ($mapping) {
                    $count = count($mapping->junctions);
                    if ($count > 0) {

                        $junction_data .= "<button class='btn btn-sm btn-outline-info align-middle'>" . $count . "</button>";
                    } else {
                        $junction_data = 0;
                    }
                } else {
                    $junction_data = 0;
                }
                return $junction_data;

            })
            ->addColumn('vehicles', function ($bag) {
                $vehicle_data = '';
                $manifest = CargoManifest::find($bag->manifest_id);
                if ($manifest) {
                    if ($manifest->vehicle_id != null) {
                        $fleet = Fleet::find($manifest->vehicle_id);
                        if($fleet)
                        {
                            $vehicle_data .= "<button class='btn btn-sm btn-outline-info align-middle'>" . $fleet->reg_number . "</button>";
                        }
                    } else {
                        $vehicle_data .= "<button class='btn btn-sm btn-outline-info align-middle'>" . $manifest->vehicle_number . "</button>";
                    }
                }
                return $vehicle_data;
            })
            ->editColumn('remarks', function ($remarks) {
                if(!empty($remarks->remarks)) {
                    return "<button ref='$remarks->id' class='btn btn-sm btn-outline-info align-middle remarks'>" . $remarks->remarks . "</button>";
                }else{
                    return  '-';
                }

            })
            ->filterColumn('vehicles', function ($query, $keyword) {
                $fleet = Fleet::where('reg_number', $keyword)->first();
                if ($fleet) {
                    $query->where('cm.vehicle_id', $fleet->id);
                } else {
                    $query->where('cm.vehicle_number', 'like', '%' . $keyword . '%');
                }
            });

        if ($tracking_number = $request->get('tracking_number')) {
            $datatables->join('cargo_manifest_bag_shipments as bssh', 'cargo_manifest_bags.id', '=', 'bssh.cargo_manifest_bag_id')
                ->join('shipments as s', 'bssh.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
        }

        if ($bag_number = $request->get('bag_number')) {
            $datatables->where('cargo_manifest_bags.seal_number', '=', $bag_number);
        }
        if ($vehicle_number = $request->get('vehicle_number')) {
            /*$datatables->where('cm.vehicle_id', '=', $vehicle_number);*/
            $fleet = Fleet::where('reg_number', $vehicle_number)->first();
            if ($fleet) {
                $datatables->where('cm.vehicle_id', 'like', '%' . $fleet->id . '%');
            } else {
                $datatables->where('cm.vehicle_number', 'like', '%' . $vehicle_number . '%');
            }
        }

        if ($manifest_id = $request->get('manifest_number')) {
            $datatables->where('cm.id', '=', $manifest_id);
        }

        if ($request->get('search_date_from') != null && $request->get('search_date_to') != null) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $stop_date = Carbon::createFromFormat('Y-m-d', $to)->endOfDay()->toDateTimeString();
            $datatables->whereBetween('cmbj.created_at', [$from, $stop_date]);
        }

        if ($search_origin = $request->get('search_origin')) {
            $datatables = $datatables->where('oh.id', '=', $search_origin);
        }

        if ($search_destination = $request->get('search_destination')) {
            $datatables = $datatables->where('dh.id', '=', $search_destination);
        }


		

        return $datatables->make(true);
    }

    public function update_seal_number(Request $request)
    {

        $existing = CargoManifestBag::where('seal_number', $request->seal_number)->where('id', '!=', $request->id);
        if ($existing->exists()) {
            return ['status' => 0, 'error' => 'Seal Number must be unique!'];
        } else {
            $bag = CargoManifestBag::find($request->id);
            $bag->seal_number = $request->seal_number;
            $bag->updated_by = Auth::id();
            $bag->save();
            return ['status' => 1, 'success' => 'Seal Number updated successfully!'];
        }
    }

    public function junctions_info(Request $request)
    {
        $bag = CargoManifestBag::find($request->id);
        if ($bag) {
            $received_date = '';
            if ($bag->status_id == 3) {
                $received_date = $bag->updated_at;
            }
            $mapping = V2JunctionMapping::where('origin_id', $bag->origin_hub_id)->where('destination_id', $bag->destination_hub_id);
            if ($mapping->exists()) {
                $junction_name = array();
                $mapping = $mapping->first();
                if ($mapping->junctions) {
                    $junctions = $mapping->junctions->pluck('junction_id')->toArray();
                    foreach ($junctions as $junction_id) {
                        $junction_name[] = City::find($junction_id)->name;
                    }
                    return ['status' => 1, 'junction_name' => $junction_name, 'received_date' => $received_date];
                } else {
                    return ['status' => 0, 'error' => 'Seal Number updated successfully!'];
                }
            }
        }
    }

    public function vehicle_info(Request $request)
    {

        if ($request->manifest_id) {
            $vehicle_data = array();
            $manifest = CargoManifest::find($request->manifest_id);
            if ($manifest) {
                if ($manifest->vehicle_id != null) {
                    $fleet = Fleet::find($manifest->vehicle_id);
                    $vehicle_data['vehicle_number'] = $fleet->reg_number;
                    $vehicle_data['driver_phone_no'] = $fleet->driver->phone_no;
                    $vehicle_data['driver_name'] = $fleet->driver->name;
                } else {
                    $vehicle_data['vehicle_number'] = $manifest->vehicle_number;
                    $vehicle_data['driver_phone_no'] = $manifest->driver_phone;
                    $vehicle_data['driver_name'] = $manifest->driver_name;
                }
            }
            return $vehicle_data;
        }
    }

    public function transitted_shipments(Request $request)
    {

        $tracking_numbers = array();
        $bag = CargoManifestBag::where('seal_number', $request->seal_number)->latest()->first();
        $cargo_manifest_shipment_ids = array();

        foreach ($bag->shipment as $shipments) {
            $cargo_manifest_shipment_ids[] = $shipments->shipment_id;
        }

        $trackings = Shipment::whereIn('id', $cargo_manifest_shipment_ids)->select('tracking_number')->get();
        foreach ($trackings as $number) {
            $tracking_numbers[] = $number->tracking_number;
        }

        return $tracking_numbers;
    }

    public function short_received_shipments(Request $request)
    {

        $tracking_numbers = array();
        $bag = CargoManifestBag::where('seal_number', $request->seal_number)->latest()->first();
        $cargo_manifest_shipment_ids = array();
        $short_received_shipments = CargoManifestBagShipments::where('cargo_manifest_bag_id', $bag->id)->where('status', 0)->get();
        foreach ($short_received_shipments as $shipments) {
            $cargo_manifest_shipment_ids[] = $shipments->shipment_id;
        }

        $trackings = Shipment::whereIn('id', $cargo_manifest_shipment_ids)->select('tracking_number')->get();
        foreach ($trackings as $number) {
            $tracking_numbers[] = $number->tracking_number;
        }

        return $tracking_numbers;
    }

    public function receive_bag_index()
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 408);
        if (Auth::user()->default_hub_id == null) {
            return back()->with(['error' => 'Default Hub not set for this admin.']);
        }

        $total = 0;
        $bags = CargoManifestBag::leftjoin('v2_junction_mappings as vjm', 'vjm.id', 'cargo_manifest_bags.junction_mapping_id')
            ->leftjoin('v2_junctions as vj', 'vj.junction_mapping_id', 'vjm.id')
            ->select(['vjm.destination_id as destination', 'vj.junction_id as junction', 'cargo_manifest_bags.status_id as status', 'cargo_manifest_bags.id as id'])
            ->whereIn('cargo_manifest_bags.status_id', $this->bag_can_be_received_statuses)
            ->get()
            ->groupBy('id');

        foreach ($bags as $key => $bag) {
            $flag = false;
            foreach ($bag as $locations) {
                if ($locations->destination == Auth::user()->default_hub_id || $locations->junction == Auth::user()->default_hub_id) {
                    $flag = true;
                }
            }

            if ($flag) {
                $total++;
            }
        }

        return view('admin.cargo.manifest.receive', compact('total'));
    }

    public function receive_bag_details(Request $request)
    {
        $bag = CargoManifestBag::where('seal_number', $request->bag_number);

        if ($bag->exists()) {
            $bag = $bag->whereIn('status_id', $this->bag_can_be_received_statuses);
            if ($bag->exists()) {
                $bag = $bag->latest()->first();

                ShipmentScanningJourneyController::seal_number_add($bag->id, 2, Auth::id());

                $cargo_bag = CargoManifest::leftjoin('manifest_bags as mb', function ($join) use ($bag) {
                    $join->on('mb.cargo_manifest_id', 'cargo_manifests.id');
                })
                    ->select(['cargo_manifests.*', 'mb.cargo_manifest_bag_id'])
                    ->where('cargo_manifests.status_id', 1)
                    ->where('mb.cargo_manifest_bag_id', $bag->id);

                if ($cargo_bag->exists()) {
                    $cargo_bag = $cargo_bag->latest()->first();

                    $mapping = V2JunctionMapping::where('id', $bag->junction_mapping_id);

                    if ($mapping->exists()) {
                        $mapping = $mapping->first();
                        $misroute = 1;
                        $last_junction = "-";
                        if (in_array($mapping->destination_id, session('hubs'))) {
                            $misroute = 0;
                            $last_junction = $mapping->junctions->sortByDesc('id')->first()->city->name ?? "-";
                        }
                        if ($misroute == 1) {
                            $previous_junction = "-";
                            foreach ($mapping->junctions as $junction) {
                                if (in_array($junction->junction_id, session('hubs'))) {
                                    $misroute = 0;
                                    $last_junction = $previous_junction;
                                    break;
                                }

                                $previous_junction = $junction->city->name;
                            }
                        }

                        $details = array();

                        $details['misroute'] = $misroute;
                        $details['bag_id'] = $bag->id;
                        $details['bag_number'] = $request->bag_number;
                        $details['manifest_id'] = str_pad($cargo_bag->id, 6, '0', STR_PAD_LEFT);
                        $details['origin'] = $cargo_bag->origin_hub->name;
                        $details['destination'] = $cargo_bag->destination_hub->name;
                        $details['last_junction'] = $last_junction;
                        $details['actual_weight'] = $bag->actual_weight;
                        $details['shipping_mode'] = $cargo_bag->shipping_mode->mode;

                        return ['status' => 0, 'success' => 'Bag has been added', 'details' => $details];
                    } else {
                        return ['status' => 1, 'error' => 'Bag Number is not associated with any mapping'];
                    }

                } else {
                    return ['status' => 1, 'error' => 'Given Bag Number is not in any Cargo Manifest'];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Bag Number is already received or created'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Bag with given Bag Number is present'];
        }

    }

    public function receive_bag_store(Request $request)
    {
        $bag_exists = array();
        $bag_misroute = array();
        $bag_not_exists = array();
        $bag_not_exists_in_manifest = array();
        $bag_not_exists_in_mapping = array();
        $bag_short_received = array();
        $request_bag_ids = explode(',', $request->bag_ids);
        foreach ($request_bag_ids as $bag_id) {
            $bag = CargoManifestBag::where('id', $bag_id)
                ->whereIn('status_id', $this->bag_can_be_received_statuses);

            if ($bag->exists()) {
                $bag = $bag->latest()->first();
                $cargo_bag = CargoManifest::leftjoin('manifest_bags as mb', function ($join) use ($bag) {
                    $join->on('mb.cargo_manifest_id', 'cargo_manifests.id');
                })
                    ->select(['cargo_manifests.*'])
                    ->where('cargo_manifests.status_id', 1)
                    ->where('mb.cargo_manifest_bag_id', $bag->id);

                if ($cargo_bag->exists()) {
                    $cargo_bag = $cargo_bag->latest()->first();
                    $mapping = V2JunctionMapping::where('id', $bag->junction_mapping_id);

                    if ($mapping->exists()) {
                        $mapping = $mapping->first();
                        $misroute = 1;
                        if (in_array($mapping->destination_id, session('hubs'))) {
                            $misroute = 0;
                            $bag->status_id = 7;
                            $bag->short_received_shipments = 0;
                        }
                        if ($misroute == 1) {
                            foreach ($mapping->junctions as $junction) {
                                if (in_array($junction->junction_id, session('hubs'))) {
                                    $misroute = 0;
                                    $bag->status_id = 3;
                                }
                            }
                        }

                        if ($misroute == 1) {
                            $short_received_count = 0;
                            $received_count = 0;
                            $bag->status_id = 5;
                            $bag->junction_mapping_id = null;
                            foreach ($bag->shipment as $shipment) {
                                $shipment_table = Shipment::find($shipment->shipment_id);
                                if(in_array($shipment_table->shipper_status_id, [3,21,26,32,49])){
                                    ShipmentsJourneyController::add($shipment->shipment_id, 11, 11, null, null, null, Auth::id(), $bag->seal_number);
                                    $shipment_table->shipper_status_id = 11;
                                    $shipment_table->consignee_status_id = 11;
                                    $shipment_table->update();
                                    $short_received_count++;
                                }
                                else{
                                    $received_count++;
                                }
                            }

                            $bag->short_received_shipments = $short_received_count;
                            $bag->received_shipments = $received_count;
                        }
                        $bag->current_hub_id = Auth::user()->default_hub_id;
                        $bag->updated_by = Auth::id();
                        $bag->update();

                        ManifestBag::where('cargo_manifest_bag_id', $bag->id)
                            ->where('cargo_manifest_id', $cargo_bag->id)->update(['status' => 1]);

                        if ($misroute == 0) {
                            array_push($bag_exists, $bag->seal_number);
                        } else {
                            array_push($bag_misroute, $bag->seal_number);
                        }
                        CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(),NULL,NULL,4);
                    } else {
                        array_push($bag_not_exists_in_mapping, $bag_id);
                    }
                } else {
                    array_push($bag_not_exists_in_manifest, $bag_id);
                }
            } else {
                array_push($bag_not_exists, $bag_id);
            }
        }
        foreach ($bag_exists as $bag_id) {
            $bag = CargoManifestBag::where('seal_number', $bag_id)->latest()->first();
            $cargo_bag = CargoManifest::leftjoin('manifest_bags as mb', function ($join) use ($bag) {
                $join->on('mb.cargo_manifest_id', 'cargo_manifests.id');
            })
                ->select(['cargo_manifests.*', 'mb.cargo_manifest_bag_id'])
                ->where('cargo_manifests.status_id', 1)
                ->where('mb.cargo_manifest_bag_id', $bag->id);

            if ($cargo_bag->exists()) {
                $cargo_bag = $cargo_bag->latest()->first();
                $manifest_bags = ManifestBag::where('cargo_manifest_id',$cargo_bag->id)->where('status',0)->get();
                $bag_short_received_count = 0;
                $cargo_short_received = array();
                foreach ($manifest_bags as $manifest_bag) {
                    if ($manifest_bag->status == 0) {
                        if (!in_array($manifest_bag->cargo_manifest_bag_id, $bag_short_received)) {
                            $short_received_bag = CargoManifestBag::find($manifest_bag->cargo_manifest_bag_id);
                            $short_received_bag->status_id = 9;
                            $bag->current_hub_id = Auth::user()->default_hub_id;
                            $short_received_bag->update();
                            CargoManifestBagJourneyController::add($short_received_bag->id, $short_received_bag->seal_number, $short_received_bag->status_id, Auth::id(),NULL,NULL,4);
                            $bag_short_received_count++;
                            array_push($bag_short_received, $manifest_bag->cargo_manifest_bag_id);
                            array_push($cargo_short_received, $short_received_bag->id);
                        } else {
                            $bag_short_received_count++;
                        }
                    }
                }

                if ($bag_short_received_count == 0) {
                    CargoManifest::find($cargo_bag->id)->update(['status_id' => 2]);
                }

                if (count($cargo_short_received) > 0) {
                    foreach ($cargo_short_received as $cargo_short) {
                        $bag_shipments = CargoManifestBagShipments::where('cargo_manifest_bag_id', $cargo_short)->get(['shipment_id']);
                        $shipments = array();
                        foreach ($bag_shipments as $shipment) {
                            array_push($shipments, $shipment->shipment_id);
                        }

                        DisputeController::add_cargo_short_received($cargo_bag->id, $shipments, null, 1);
                    }
                }
            }
        }
        foreach ($bag_misroute as $bag_id) {
            $bag = CargoManifestBag::where('seal_number', $bag_id)->latest()->first();
            $cargo_bag = CargoManifest::leftjoin('manifest_bags as mb', function ($join) use ($bag) {
                $join->on('mb.cargo_manifest_id', 'cargo_manifests.id');
            })
                ->select(['cargo_manifests.*', 'mb.cargo_manifest_bag_id'])
                ->where('cargo_manifests.status_id', 1)
                ->where('mb.cargo_manifest_bag_id', $bag->id);

            if ($cargo_bag->exists()) {
                $cargo_bag = $cargo_bag->latest()->first();
                $manifest_bags = ManifestBag::where('cargo_manifest_id',$cargo_bag->id)->where('status',0)->get();
                $bag_short_received_count = 0;
                $cargo_short_received = array();
                foreach ($manifest_bags as $manifest_bag) {
                    if ($manifest_bag->status == 0) {
                        if (!in_array($manifest_bag->cargo_manifest_bag_id, $bag_short_received)) {
                            $short_received_bag = CargoManifestBag::find($manifest_bag->cargo_manifest_bag_id);
                            $short_received_bag->status_id = 9;
                            $bag->current_hub_id = Auth::user()->default_hub_id;
                            $bag->updated_by = Auth::id();
                            $short_received_bag->update();
                            CargoManifestBagJourneyController::add($short_received_bag->id, $short_received_bag->seal_number, $short_received_bag->status_id, Auth::id(),NULL,NULL,4);
                            $bag_short_received_count++;
                            array_push($bag_short_received, $manifest_bag->cargo_manifest_bag_id);
                            array_push($cargo_short_received, $short_received_bag->id);
                        } else {
                            $bag_short_received_count++;
                        }
                    }
                }

                if ($bag_short_received_count == 0) {
                    CargoManifest::find($cargo_bag->id)->update(['status_id' => 2]);
                }

                if (count($cargo_short_received) > 0) {
                    foreach ($cargo_short_received as $cargo_short) {
                        $bag_shipments = CargoManifestBagShipments::where('cargo_manifest_bag_id', $cargo_short)->get(['shipment_id']);
                        $shipments = array();
                        foreach ($bag_shipments as $shipment) {
                            array_push($shipments, $shipment->shipment_id);
                        }

                        DisputeController::add_cargo_short_received($cargo_bag->id, $shipments, null, 1);
                    }
                }
            }
        }

        $success_html = false;
        $misroute_html = false;
        $bag_not_exists_error = false;
        $bag_not_exists_in_manifest_error = false;
        $bag_not_exists_in_mapping_error = false;
        $bag_short_received_error = false;
        if (count($bag_not_exists) > 0) {
            $bag_not_exists_error = "Following Bag(s) already received or doesn't exists.<br><ul>";
            foreach ($bag_not_exists as $v) {
                $bag_not_exists_error .= "<li>" . CargoManifestBag::find($v)->seal_number ?? $v . "</li>";
            }
            $bag_not_exists_error .= "</ul>";
        }
        if (count($bag_not_exists_in_manifest) > 0) {
            $bag_not_exists_in_manifest_error = "Following Bag(s) doesn't exists in any manifest.<br><ul>";
            foreach ($bag_not_exists_in_manifest as $v) {
                $bag_not_exists_in_manifest_error .= "<li>" . CargoManifestBag::find($v)->seal_number . "</li>";
            }
            $bag_not_exists_in_manifest_error .= "</ul>";
        }
        if (count($bag_not_exists_in_mapping) > 0) {
            $bag_not_exists_in_mapping_error = "Following Bag(s) doesn't associated with any mapping.<br><ul>";
            foreach ($bag_not_exists_in_mapping as $v) {
                $bag_not_exists_in_mapping_error .= "<li>" . CargoManifestBag::find($v)->seal_number . "</li>";
            }
            $bag_not_exists_in_mapping_error .= "</ul>";
        }
        if (count($bag_short_received) > 0) {
            $bag_short_received_error = "Following Bag(s) are short received.<br><ul>";
            foreach ($bag_short_received as $bag_id) {
                $bag_short_received_error .= "<li>" . CargoManifestBag::find($bag_id)->seal_number . "</li>";
            }
            $bag_short_received_error .= "</ul>";
        }
        if (count($bag_exists) > 0) {
            $success_html = "Following Bag(s) are received successfully.<br><ul>";
            foreach ($bag_exists as $v) {
                $success_html .= "<li>" . $v . "</li>";
            }
            $success_html .= "</ul>";
        }
        if (count($bag_misroute) > 0) {
            $misroute_html = "Following Bag(s) are received as misrouted successfully.<br><ul>";
            foreach ($bag_misroute as $v) {
                $misroute_html .= "<li>" . $v . "</li>";
            }
            $misroute_html .= "</ul>";
        }

        return back()->with(['success_html' => $success_html, 'misroute_html' => $misroute_html, 'bag_short_received_error' => $bag_short_received_error, 'bag_not_exists_in_mapping_error' => $bag_not_exists_in_mapping_error, 'bag_not_exists_in_manifest_error' => $bag_not_exists_in_manifest_error, 'bag_not_exist_error' => $bag_not_exists_error]);

    }

    public function manifest_history()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 409);
        $shipping_mode = ShippingMode::all();
        $transport_vendor = FleetVendor::all();
        $transport_mode = TransportMode::all();
        $cities = City::select('id', 'name')->where('status', 1)->get();
        return view('admin.cargo.manifest.history')->with(['shipping_mode' => $shipping_mode, 'transport_mode' => $transport_mode, 'transport_vendor' => $transport_vendor, 'cities' => $cities]);
    }

    public function manifest_history_list(Request $request)
    {
//        dd($request->get('transit_from_date') , $request->get('transit_to_date') , $request->get('search_filter_origin') , $request->get('search_filter_destination'));
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 410);
        }
        $receive_cargo = CargoManifest::join('cities as oh', 'cargo_manifests.origin_hub_id', '=', 'oh.id')
            ->join('cities as dh', 'cargo_manifests.destination_hub_id', '=', 'dh.id')
            ->leftJoin('shipping_modes as sm', 'cargo_manifests.shipping_mode_id', '=', 'sm.id')
            ->join('admins as a', 'cargo_manifests.created_by', '=', 'a.id')
            ->leftjoin('fleets as f', 'cargo_manifests.vehicle_id', '=', 'f.id')
            ->leftjoin('transport_modes as tm', 'cargo_manifests.transport_mode_id', '=', 'tm.id')
//            ->select('cargo_manifests.id as manifest_id','cargo_manifests.route_name', 'cargo_manifests.status_id', 'oh.id as origin_id', 'oh.name as origin', 'dh.id as destination_id', 'dh.name as destination', 'cargo_manifests.shipments', 'cargo_manifests.bags', 'cargo_manifests.driver_name', 'f.reg_number as vehicle', 'cargo_manifests.driver_phone', 'sm.mode as shipping_mode', 'tm.name as transport_mode', 'cargo_manifests.bags_weight', 'cargo_manifests.actual_weight', 'cargo_manifests.created_at as transit_at', 'a.name as transitted_by', 'oh.hub_id as origin_hub_id', 'dh.hub_id as destination_hub_id','cargo_manifests.vendor_name as vendor' ,'cargo_manifests.driver_phone as phone_number', 'cargo_manifests.status_id as status','cargo_manifests.id as manifest','cargo_manifests.short_received_bags as short_received_bags');
            ->select('cargo_manifests.id as manifest_id', 'cargo_manifests.status_id', 'oh.id as origin_id', 'oh.name as origin', 'dh.id as destination_id', 'dh.name as destination', 'cargo_manifests.shipments', 'cargo_manifests.bags', 'cargo_manifests.driver_name', 'f.reg_number as vehicle', 'cargo_manifests.driver_phone', 'sm.mode as shipping_mode', 'tm.name as transport_mode', 'cargo_manifests.bags_weight', 'cargo_manifests.actual_weight', 'cargo_manifests.created_at as transit_at', 'a.name as transitted_by', 'oh.hub_id as origin_hub_id', 'dh.hub_id as destination_hub_id', 'cargo_manifests.vendor_name as vendor', 'cargo_manifests.driver_phone as phone_number', 'cargo_manifests.status_id as status', 'cargo_manifests.id as manifest', 'cargo_manifests.short_received_bags as short_received_bags');


        $datatables = Datatables::of($receive_cargo)
            ->editColumn('status', function ($master_cargo) {
                if ($master_cargo->status == 1) {
                    return 'Created';
                } else {
                    return 'Completed';
                }
            })
            ->addColumn('id_padded', function ($master_cargo) {
                return str_pad($master_cargo->id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('manifest_id', function ($bag) {
                if ($bag->manifest_id) {
                    return '<button class="btn btn-sm btn-outline-info align-middle print "><i class="la la-lg la-print align-middle "></i> <span class="align-middle id">' . str_pad($bag->manifest_id, 6, '0', STR_PAD_LEFT) . '</span></button>';
                }

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
                if ($master_cargo->short_received_bags > 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $master_cargo->short_received_bags . '</button>';
                } else {
                    return '-';
                }
            })
            ->addColumn('vehicles', function ($bag) {
                $vehicle_data = '';
                $manifest = CargoManifest::find($bag->manifest_id);
                if ($manifest) {
                    if ($manifest->vehicle_id != null) {
                        $fleet = Fleet::find($manifest->vehicle_id);
                        if ($fleet)
                        {
                            $vehicle_data = $fleet->reg_number;
                        }
                    } else {
                        $vehicle_data = $manifest->vehicle_number;
                    }
                }
                return $vehicle_data;
            })
            ->addColumn('shipments', function ($master_cargo) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $master_cargo->shipments . '</button>';
            })
            ->filterColumn('master_cargoes.id', function ($query, $keyword) {
                return $query->where('master_cargoes.id', '=', $keyword);
            })
            ->filterColumn('shipping_mode', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('sm.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('vehicles', function ($query, $keyword) {
                $fleet = Fleet::where('reg_number', $keyword)->first();
                if ($fleet) {
                    $query->where('cargo_manifests.vehicle_id', $fleet->id);
                } else {
                    $query->where('cargo_manifests.vehicle_number', 'like', '%' . $keyword . '%');
                }
            });

        if (($request->tracking_number != null && $request->tracking_number != '') || $request->bag_number != null && $request->bag_number != '') {
            $datatables->join('manifest_bags as mb', 'cargo_manifests.id', '=', 'mb.cargo_manifest_id')
                ->join('cargo_manifest_bags as b', 'b.id', '=', 'mb.cargo_manifest_bag_id');

            if ($tracking_number = $request->get('tracking_number')) {
                $datatables->join('cargo_manifest_bag_shipments as bs', 'b.id', '=', 'bs.cargo_manifest_bag_id')
                    ->join('shipments as s', 'bs.shipment_id', '=', 's.id')
                    ->where('s.tracking_number', '=', $tracking_number);
            }

            if ($bag_number = $request->get('bag_number')) {
                $datatables->where('b.seal_number', '=', $bag_number);
            }
        }

        if ($request->get('transit_from_date') && $request->get('transit_to_date')) {
            $from = $request->get('transit_from_date');
            $to = $request->get('transit_to_date');

            $stop_date = date('Y-m-d H:i:s', strtotime($to . ' +1 day'));
            $datatables->whereBetween('cargo_manifests.created_at', [$from, $stop_date]);
        }
        if(($request->search_filter_origin != null) && ($request->search_filter_destination != null) )
        {
            $origin = $request->get('search_filter_origin');
            $destination = $request->get('search_filter_destination');
            $datatables->where('oh.name', '=', $origin)
                ->where('dh.name', '=', $destination);
        }

        if($request->search_filter_origin != null)
        {
            $origin = $request->get('search_filter_origin');
            $datatables->where('oh.name', '=', $origin);
        }
        if($request->search_filter_destination != null)
        {
            $destination = $request->get('search_filter_destination');
            $datatables->where('dh.name', '=', $destination);
        }

        return $datatables->make(true);
    }

    public function receive_bag_shipments_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 411);
        return view('admin.cargo.manifest.receive_shipments');
    }

    public function receive_bag_shipments_details(Request $request)
    {
        
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();

            $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
            if(!$dispute_check){
                return ['status' => 1, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
            }

            if ($shipment->shipper_status_id != 3 && $shipment->shipper_status_id != 21 && $shipment->shipper_status_id != 26 && $shipment->shipper_status_id != 32 && $shipment->shipper_status_id != 49) {
                return ['status' => 1, 'error' => 'Given Tracking Number has already been modified!'];
            }
            $bag_shipment = CargoManifestBagShipments::where('shipment_id', $shipment->id);

            if ($bag_shipment->exists()) {
                $bag_shipment = $bag_shipment->where('status', 0);

                if ($bag_shipment->exists()) {
                    $bag_shipment = $bag_shipment->latest()->first();
                    $bag = $bag_shipment->bag;
                    if ($bag) {

                        if($bag->type != $request->bag_type){
                            return ['status' => 1, 'error' => 'Shipment bag type is not same as selected bag type'];
                        }

                        $cargo_manifest_bag = ManifestBag::where('cargo_manifest_bag_id', $bag->id)->latest()->first();
                        if (!$cargo_manifest_bag) {
                            return ['status' => 1, 'error' => 'No Bag exists for the following shipment'];
                        }
                    }

                    if (!in_array($bag->destination_hub->hub_id, session('hubs'))) {
                        return ['status' => 1, 'error' => 'Shipment Bag doesn\'t belong to your assigned hub(s)!'];
                    }

                    if (!$request->has('pieces_confirm')) {
                        if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                            $details = array();
                            $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                            $details['id'] = $shipment->id;
                            $details['tracking_number'] = $shipment->tracking_number;
                            $details['pieces_count'] = $shipment->pieces;
                            $details['pieces_tracking_numbers'] = $shipment_pieces;
                            ShipmentScanningJourneyController::add($shipment->id, 20, 1, Auth::id(), null, null);
                            return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                        }
                    }
                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['bag_number'] = $bag->seal_number;

                    if ($shipment->shipper_status_id == 21) {
                        $details['origin'] = $shipment->consignee_city->name;
                        if ($shipment->return_address_id != NULL) {
                            $details['destination'] = $shipment->return_address->city->name;
                            $details['hub'] = $shipment->return_address->city->hub_city->name;
                        } else {
                            $details['destination'] = $shipment->pickup_address->city->name;
                            $details['hub'] = $shipment->pickup_address->city->hub_city->name;
                        }
                    } else {
                        $details['origin'] = $shipment->pickup_address->city->name;
                        $details['destination'] = $shipment->consignee_city->name;
                        $details['hub'] = $shipment->consignee_city->hub_city->name;
                    }

                    $details['consignee'] = $shipment->consignee_name;
                    $details['shipping_mode'] = $shipment->shipping_mode->mode;
                    $details['amount'] = number_format($shipment->amount);
                    $details['service_type'] = $shipment->booking_type->booking_type;
                    ShipmentScanningJourneyController::add($shipment->id, 20, 1, Auth::id(), null, null);
                    return ['status' => 0, 'success' => 'Shipment has been Added!', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Bag Number\'s has already been Received'];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number is not in any Bag'];
            }
        } else {
            return ['status' => 1, 'error' => 'Invalid Tracking Number'];
        }
    }

    public function bag_piece_details(Request $request)
    {

        $shipment_id = $request->shipment_id;
        $shipment_piece_id = $request->piece_id;

        $shipment_piece = ShipmentPiece::where('tracking_number', $shipment_piece_id);
        if ($shipment_piece->exists()) {
            $shipment_piece = $shipment_piece->first();
            if ($shipment_piece->shipment_id == $shipment_id) {
                $scanned_shipment_piece = $shipment_piece->tracking_number;
                ShipmentScanningJourneyController::add($shipment_id, $request->screen_location_id, 1, Auth::id(), null, null, $shipment_piece->id);
                return ['status' => 0, 'success' => 'Shipment Piece found!', 'scanned_shipment_piece' => $scanned_shipment_piece];
            } else {
                return ['status' => 1, 'error' => 'Given Item ID does not belong here'];
            }

        } else {
            return ['status' => 1, 'error' => 'No Shipment Item with given Item ID is present'];
        }
    }

    public function receive_bag_shipments_store(Request $request)
    {
        
        $shipment_status_array = [3, 21, 26, 32, 49];
        $shipment_ids = array_unique(explode(',', $request->shipment_ids));
        $open_box_ids = explode(',', $request->open_box_ids);
        $bag_ids = array();
        $shipment_ids_array = array();
        $short_received_shipments_array = array();
        $shipments_already_marked_received_array = array();
//        todo : open box-work
        if (count($open_box_ids) > 0) {

            foreach ($shipment_ids as $index => $shipment) {
                if (in_array($shipment, $open_box_ids)) {

                    $shipment_detail = ShipmentDetail::where('shipment_id', $shipment)->where('is_open', '=', 0)->first();
                    if ($shipment_detail) {
                        $shipment_detail->is_open = 1;
                        $shipment_detail->save();
                    }

                    $shipment_data = Shipment::find($shipment);
                    $shipment_data->open_box = 1;
                    $shipment_data->save();

                ShipmentOpenBoxJourneyController::add($shipment, 2, Auth::id());
                }

            }

        }
//        todo : open box-work end

        foreach ($shipment_ids as $shipment_id) {
            $bag_shipment = CargoManifestBagShipments::where('shipment_id', $shipment_id)/*->where('status', 0)*/
            ;

            if ($bag_shipment->exists()) {
                $bag_shipment = $bag_shipment->latest()->first();

                $shipment = Shipment::find($shipment_id);
                if (in_array($shipment->shipper_status_id, $shipment_status_array)) {

                    if ($bag_shipment->status == 0) {
                        $bag_shipment->status = 1;
                        $bag_shipment->save();
                    }

                    $bag = $bag_shipment->bag;

                    array_push($shipment_ids_array, $shipment->tracking_number);

                    $shipper_status_id = NULL;
                    $consignee_status_id = NULL;

                    if ($bag->type == 1) {
                        if ($shipment->booking_type_id == 4 && $shipment->walk_in_delivery_type_id == 2) {
                            //ShipmentsJourneyController::add($shipment_id, 4, 4, NULL, NULL, NULL, Auth::id());
                            $shipper_status_id = 15;
                            $consignee_status_id = 15;
                        } else {
                            $shipper_status_id = 4;
                            $consignee_status_id = 4;

                            $self_collection = SelfCollectionShipment::where('shipment_id',$shipment_id)->first();

                            if($self_collection)
                            {
                                $consignee_city = $shipment->consignee_city_id;
                                $user_city = $shipment->user->city_id;

                                if($consignee_city == '202' || $consignee_city == '223')
                                {
                                        NotificationsController::send(178, $shipment_id);
                                }
                                else
                                {
                                    $city_id = SelfCollectionCities::where('city_id', $consignee_city)->select('city_id','address')->first();
                                    if($city_id){
                                        NotificationsController::send(75, $shipment_id, $city_id->address);
                                    }
                                }
                            }
                        }
                    } else {
                        if ($shipment->booking_type_id == 1) {
                            $shipper_status_id = 22;
                            $consignee_status_id = 22;
                        } else if ($shipment->booking_type_id == 2) {
                            if ($shipment->shipper_status_id == 21) {
                                $shipper_status_id = 22;
                                $consignee_status_id = 22;
                            } else {
                                $shipper_status_id = 27;
                                $consignee_status_id = 27;
                            }

                        } else if ($shipment->booking_type_id == 3) {
                            $shipper_status_id = 33;
                            $consignee_status_id = 33;
                        } else if ($shipment->booking_type_id == 4) {
                            $shipper_status_id = 22;
                            $consignee_status_id = 22;
                        } else {
                            $shipper_status_id = 22;
                            $consignee_status_id = 22;
                        }
                    }
                    $shipment->shipper_status_id = $shipper_status_id;
                    $shipment->consignee_status_id = $consignee_status_id;
                    $shipment->save();

                    ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $consignee_status_id, NULL, NULL, NULL, Auth::id());

                    if (!in_array($bag->id, $bag_ids)) {
                        $bag_ids[] = $bag->id;
                    }
                } else {
                    array_push($shipments_already_marked_received_array, $shipment->tracking_number);
                }
            }
        }


        foreach ($bag_ids as $bag_id) {
            $bag = CargoManifestBag::find($bag_id);
            $bag->received_shipments = CargoManifestBagShipments::where('cargo_manifest_bag_id', $bag_id)->where('status', 1)->count();

            $short_received = CargoManifestBagShipments::where('cargo_manifest_bag_id', $bag_id)->where('status', 0)->count();

            if ($short_received > 0) {
                $bag->short_received_shipments = $short_received;
                $status_id = 8;
            } else {
                $bag->short_received_shipments = 0;
                $status_id = 7;
                $bag->completed = 1;
            }
            $bag->status_id = $status_id;
            $bag->received_at = Carbon::now();
            $bag->receiver_id = Auth::id();
            $bag->current_hub_id = Auth::user()->default_hub_id;
            $bag->save();

            ManifestBag::where('cargo_manifest_bag_id', $bag->id)->update(['status' => 1]);

            //dispute for short received
            if ($bag->status_id == 8) {
                $bag_short_received_shipments = CargoManifestBagShipments::where(['cargo_manifest_bag_id' => $bag_id, 'status' => 0])->pluck('shipment_id')->toArray();

                if (!empty($bag_short_received_shipments)) {
                    DisputeController::add_cargo_short_received($bag->seal_number, $bag_short_received_shipments, null, 2);
                }
            }

//        end dispute short received
            /* if($all_bag_ids == ''){
                 $all_bag_ids = $all_bag_ids . $bag->seal_number;
             }
             else{
                 $all_bag_ids = $all_bag_ids . ', ' .$bag->seal_number;
             }*/
        }

        foreach ($bag_ids as $bag_id) {
            $bag = CargoManifestBag::find($bag_id);
            $manifest_id = ManifestBag::where('cargo_manifest_bag_id', $bag->id)->latest()->first()->cargo_manifest_id;
            $manifest = CargoManifest::find($manifest_id);
            $manifest->received_bags = ManifestBag::where('cargo_manifest_id', $manifest_id)->where('status', 1)->count();
            $short_received_bags = ManifestBag::where('cargo_manifest_id', $manifest_id)->where('status', 0)->count();

            if ($short_received_bags > 0) {
                $manifest->short_received_bags = $short_received_bags;
            } else {
                $manifest->short_received_bags = 0;
                $manifest->status_id = 2;
            }

            $manifest->update();
        }

        $bag_shipments = CargoManifestBagShipments::whereIn('cargo_manifest_bag_id', $bag_ids)->where('status', 0);
        if ($bag_shipments->exists()) {
            $shipment_ids = $bag_shipments->pluck('shipment_id')->toArray();
            foreach ($shipment_ids as $shipment_id) {
                $shipment = Shipment::find($shipment_id);
                if (!in_array($shipment->tracking_number, $short_received_shipments_array)) {
                    array_push($short_received_shipments_array, $shipment->tracking_number);
                }
            }

        }

        $received_html = '';
        $sr_html = '';
        $already_received_shipments_html = '';

        if (count($short_received_shipments_array) > 0) {
            $sr_html = "Following Shipments(s) are marked as short received.<br><ul>";
            foreach ($short_received_shipments_array as $v) {
                $sr_html .= "<li>" . $v . "</li>";
            }
            $sr_html .= "</ul>";
        }

        if (count($shipments_already_marked_received_array) > 0) {
            $already_received_shipments_html = "Following Shipments(s) are already marked as received or processed .<br><ul>";
            foreach ($shipments_already_marked_received_array as $v) {
                $already_received_shipments_html .= "<li>" . $v . "</li>";
            }
            $already_received_shipments_html .= "</ul>";
        }

        if (count($shipment_ids_array) > 0) {
            $received_html = "Following Shipments(s) are marked as received. .<br><ul>";
            foreach ($shipment_ids_array as $v) {
                $received_html .= "<li>" . $v . "</li>";
            }
            $received_html .= "</ul>";
        }


        //return redirect()->back()->with('success', 'Selected Shipments of Bag Number(s)#' . $all_bag_ids . ' has been Received');
        return back()->with(['sr_html' => $sr_html, 'received_html' => $received_html, 'already_received_shipments_html' => $already_received_shipments_html]);


    }

    public function manifest_bags(Request $request)
    {
        $cargo = CargoManifest::find($request->manifest_id);
        $cargo_bags = $cargo->manifest_bags;
        foreach ($cargo_bags as $cargo_bag) {
            $seal_number = $cargo_bag->bag->seal_number;
            $bag_numbers[] = $seal_number;
        }


        return $bag_numbers;
    }

    public function cargo_short_received_bags(Request $request)
    {

        $tracking_numbers = array();
        $cargo = CargoManifest::find($request->manifest_id);
        $cargo_bags = $cargo->manifest_bags->where('status', 0);
        if ($cargo_bags) {
            foreach ($cargo_bags as $cargo_bag) {
                $seal_number = $cargo_bag->bag->seal_number;
                $bags = $cargo_bag->bag;
                foreach ($bags->shipment as $bag_shipment) {
                    $shipment = $bag_shipment->shipment;
                    $tracking_numbers[$seal_number][] = $shipment->tracking_number;
                }
            }

            return response()->json(['status' => 1, 'tracking_numbers' => $tracking_numbers]);
        } else {
            return response()->json(['status' => 0]);
        }
    }

    public function cargo_bag_shipments(Request $request)
    {
        $tracking_numbers = array();
        $cargo = CargoManifest::find($request->manifest_id);
        $cargo_bags = $cargo->manifest_bags;
        $cargo_bags_shipment_ids = array();
        foreach ($cargo_bags as $cargo_bag) {
            $cargo_bag_shipments = $cargo_bag->bag->shipment;
            foreach ($cargo_bag_shipments as $cargo_bag_shipment) {
                $cargo_bags_shipment_ids[] = $cargo_bag_shipment->shipment_id;
            }
        }
        $trackings = Shipment::whereIn('id', $cargo_bags_shipment_ids)->select('tracking_number')->get();
        foreach ($trackings as $number) {
            $tracking_numbers[] = $number->tracking_number;
        }

        return $tracking_numbers;
    }

    public function cargo_manifest_in_transit_print(Request $request)
    {
        $html = self::print($request->cargo_manifest_ids, 2);
        return $html;
    }

    public function history_lost_shipments(Request $request)
    {
        $bag = CargoManifestBag::where('seal_number', $request->seal_number)->first();
        $tracking_numbers = array();
        if ($bag) {
            $shipment_ids = ManifestBagLostShipment::where('bag_id', $bag->id);
            if ($shipment_ids->exists()) {
                $shipment_ids = $shipment_ids->pluck('shipment_id')->toArray();

                foreach ($shipment_ids as $shipment_id) {
                    $shipment = Shipment::find($shipment_id);
                    $tracking_numbers[] = $shipment->tracking_number;
                }
            }
            return $tracking_numbers;
        }
    }

    public function manifest_draft()
    {
        $draft = CargoManifestDraftBags::join('cities as c', 'c.id', '=', 'cargo_manifest_draft_bags.origin_id')
            ->join('cities as d', 'd.id', '=', 'cargo_manifest_draft_bags.destination_id')
            ->select('cargo_manifest_draft_bags.bag_id as bag_id', 'cargo_manifest_draft_bags.seal_number as bag_number', 'cargo_manifest_draft_bags.shipments_count', 'd.name as destination', 'c.name as origin','cargo_manifest_draft_bags.pieces_count','cargo_manifest_draft_bags.remarks')
            ->where('cargo_manifest_draft_bags.added_by', Auth::id())
            ->orderby('cargo_manifest_draft_bags.created_at', 'desc');

        return Datatables::of($draft)
            ->editColumn('remarks',function ($shipments){
                $remarks = '<textarea maxlength="250" type="text" id="remarks" ref="'.$shipments->bag_id.'" class="form-control form-control-sm remarks">'.$shipments->remarks.'</textarea>';
                return $remarks;
            })
            ->addColumn('action', function ($shipments) {
                $dropdown = '<a href="javascript:void(0);" class="btn btn-icon btn-danger bag_remove"><i class="la la-close"></i></a>';
                return $dropdown;

            })
            ->make(true);
    }

    public function manifest_draft_delete(Request $request)
    {
        $bag_id = $request->bag_id;
        $draft = CargoManifestDraftBags::where('bag_id', $bag_id)->where('added_by', Auth::id())->first();
        if ($draft) {
            $draft->delete();
            $draft_bags = CargoManifestDraftBags::where('added_by', Auth::id())->get();
            $bag_destinations = array();
            $total_bags = 0;
            $scanned_bags = count($draft_bags);
            if(count($draft_bags) > 0){
                foreach ($draft_bags as $draft_bag){
                    if(!in_array($draft_bag->destination_id, $bag_destinations)){
                        $bag_destinations[] = $draft_bag->destination_id;
                    }
                }
                $total_bags = CargoManifestBag::whereIn('destination_hub_id', $bag_destinations)->whereIn('status_id', [1, 3, 5])->where('current_hub_id', Auth::user()->default_hub_id)->count();
            }
            return response()->json(['status' => 1, 'total_bags' => $total_bags, 'scanned_bags' => $scanned_bags, 'bag_destinations' => $bag_destinations]);
        } else {
            return response()->json(['status' => 0]);
        }
    }

    public function manifest_draft_setting(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),509);
        $users = Admin::join('admin_roles as ar','ar.id','=','admins.role_id')
            ->select(['admins.id','admins.trax_id','admins.name'])
            ->where('ar.department_id',6)
            ->where('admins.status',1);

        if(session('role_id') != 1)
        {
            $users = $users->where('default_hub_id', Auth::user()->default_hub_id);
        }

        $users = $users->get();
        return view('admin.cargo.manifest.draft_setting',compact('users'));
    }

    public function manifest_draft_setting_list(Request $request)
    {
        $cargo = CargoManifestDraftBags::join('cities as o', 'cargo_manifest_draft_bags.origin_id', '=', 'o.id')
            ->join('cities as d', 'cargo_manifest_draft_bags.destination_id', '=', 'd.id')
            ->join('admins as u', 'cargo_manifest_draft_bags.added_by', '=', 'u.id')
            ->select('cargo_manifest_draft_bags.id as id','cargo_manifest_draft_bags.seal_number as seal_number', 'cargo_manifest_draft_bags.shipments_count as shipment_count', 'o.id as origin_id', 'o.name as origin', 'd.id as destination_id', 'd.name as destination', 'u.name as assigned_to', 'cargo_manifest_draft_bags.created_at as created_at');


        if(session('role_id') != 1)
        {
            $cargo->where('cargo_manifest_draft_bags.origin_id',Auth::user()->default_hub_id);
        }

        $datatables = Datatables::of($cargo);

        return $datatables->make(true);
    }

    public function manifest_draft_update (Request $request)
    {
        $request->validate([
            'ids' => 'required',
            'user_id' => 'required',
        ]);

        $ids = explode(',', $request->ids);
        CargoManifestDraftBags::whereIn('id',$ids)->update(['added_by'=>$request->user_id]);
        return back()->with(['success' => 'Bag Transfered Successfully']);
    }

    public function remarks_info(Request $request)
    {
        if ($request->bag_id) {
            $remarks = CargoManifestBagRemarks::join('admins as a','a.id','cargo_manifest_bag_remarks.added_by')
                ->leftjoin('cargo_manifest_bags as cmb','cmb.id','cargo_manifest_bag_remarks.bag_id')
                ->where('cargo_manifest_bag_remarks.bag_id',$request->bag_id)
                ->select(['cargo_manifest_bag_remarks.*', 'a.name as added_by_name', 'cmb.seal_number'])
                ->orderby('cargo_manifest_bag_remarks.id','desc');
            if ($remarks->exists()) {
                $remarks = $remarks->get();
                return $remarks;
            }else{
                return [];
            }
        }else{
            return [];
        }
    }

}
