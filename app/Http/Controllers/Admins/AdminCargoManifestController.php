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
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShippingMode;
use App\Http\Models\TransportMode;
use App\Http\Models\WarehouseStockRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\CargoManifest\IssueSackBagOrigin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use SnappyPDF;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\ShipmentsEstimatedWeight;
use App\Http\Models\Admin\WalkInInternationalStandardWeightCharge;
use App\Http\Models\Admin\WalkInInternationalStandardWeightChargeHub;
use App\Http\Models\InternationalShipment;
use App\Http\Models\Admin\WalkInStandardWeightCharge;
use App\Http\Controllers\Webhook\InitialChargesWebhookController;

class AdminCargoManifestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');

        $this->bag_can_be_received_statuses = [1, 2, 4, 5, 6, 8, 9, 10];
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
            ->select('v2_junction_mappings.id as id', 'v2_junction_mappings.status as status', 'v2_junction_mappings.updated_at as updated', 'oc.name as origin', 'oc.hub_location_latitude as ohllat', 'oc.hub_location_longitude as ohllng', 'dc.name as destination', 'dc.hub_location_latitude as dhllat', 'dc.hub_location_longitude as dhllng', 'a.name as updated_by');

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
            ->rawColumns(['origin_display','junctions_display','destination_display','action'])
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

            if(isset($request->vehicles[$key])){ // TO-6836 (Entry Of Vehicles Made Optional)
                foreach ($request->vehicles[$key] as $vehicle) {
                    $route_vehicle = new V2JunctionVehicles();
                    $route_vehicle->junction_route_id = $route_junction->id;
                    $route_vehicle->vehicle_id = $vehicle;
                    $route_vehicle->save();
                }
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

            if(isset($request->vehicles[$key])){ // TO-6836 (Entry Of Vehicles Made Optional)
                foreach ($request->vehicles[$key] as $vehicle) {
                    $route_vehicle = new V2JunctionVehicles();
                    $route_vehicle->junction_route_id = $route_junction->id;
                    $route_vehicle->vehicle_id = $vehicle;
                    $route_vehicle->save();
                }
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
        $origin_hubs = City::all();
        return view('admin.cargo.manifest.bags.pending')->with(['origin_hubs' => $origin_hubs, 'shipment_status' => $shipment_status, 'service_type' => $service_type, 'shipping_mode' => $shipping_mode]);
    }

    public function pending_bag_list(Request $request)
    {

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 399);
        }
        $today = Carbon::today();
        $on_hold_shipments = ShipmentOnHold::whereDate('dispatch_date', '>', $today)->where('status', 1)->pluck('shipment_id')->toArray();
        $shipments = DB::connection('reports_2')
            ->table('shipments')
            ->leftjoin('booking_types as bt', 'shipments.booking_type_id', '=', 'bt.id')
            ->leftjoin('shipment_status as ss', 'shipments.shipper_status_id', '=', 'ss.id')
            ->leftjoin('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->leftjoin('users as u', 'shipments.user_id', '=', 'u.id')
            ->leftjoin('cities as oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('cities as ohc', 'oc.hub_id', '=', 'ohc.id')
            ->leftjoin('zones as z', 'ohc.zone_id', '=', 'z.id')
            ->leftjoin('shipping_modes as sm', 'shipments.shipping_mode_id', '=', 'sm.id')
            ->leftjoin('user_shipping_infos as rsi', function ($join) {
                $join->on('shipments.return_address_id', '=', 'rsi.id')
                    ->whereNotNull('shipments.return_address_id')
                    ->where('shipments.shipper_status_id', '!=', 30);
            })
            ->leftjoin('cities as rc', 'rsi.city_id', '=', 'rc.id')
            ->leftjoin('zones as rcz', 'rc.zone_id', '=', 'rcz.id')
            ->leftjoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->on('shipments_journey.shipper_status_id', '=', DB::raw(2));
            })
            ->leftJoin('shipments_journey as csj', function ($join) {
                $join->on('csj.shipment_id', '=', 'shipments.id')
                    ->where(
                        'csj.id',
                        '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)')
                    );
            })
            ->leftjoin('misrouted_history as mh', function ($join) {
                $join->on('mh.shipment_id', '=', 'shipments.id')
                    ->on(function ($query) {
                        $query->where('shipments.shipper_status_id', '=', DB::raw(49));
                    })
                    // ->on('shipments.shipper_status_id', '=', DB::raw(49))
                    ->where(
                        'mh.id',
                        '=',
                        DB::raw('(select max(id) from misrouted_history where misrouted_history.shipment_id = shipments.id)')
                    );
            })
            ->leftjoin('misrouted_history as gmh', function ($join) {
                $join->on('gmh.shipment_id', '=', 'shipments.id')
                    ->on('shipments.shipper_status_id', '=', DB::raw(11))
                    ->where(
                        'gmh.id',
                        '=',
                        DB::raw('(select max(id) from misrouted_history where misrouted_history.shipment_id = shipments.id)')
                    );
            })
            ->leftJoin('misrouted_history as gmhh', function ($join) {
                $join->on('gmhh.shipment_id', '=', 'shipments.id')
                    ->whereIn('shipments.shipper_status_id', [68, 69, 72, 75])
                    ->whereRaw('gmhh.id = (SELECT MAX(id) FROM misrouted_history WHERE misrouted_history.shipment_id = shipments.id)');
            })
            ->leftjoin('cities as olddc', 'olddc.id', '=', 'mh.old_consignee_city_id')
            ->leftjoin('cities as olddhc', 'olddc.hub_id', '=', 'olddhc.id')
            ->leftjoin('zones as olddhcz', 'olddhc.zone_id', '=', 'olddhcz.id')
            ->leftjoin('intercept_re_book_request_histories as irbrh', function ($join) {
                $join->on('irbrh.shipment_id', '=', 'shipments.id')
                    ->on('shipments.shipper_status_id', '=', DB::raw(55));
            })
            ->leftjoin('cities as olddci', 'olddci.id', '=', 'irbrh.old_consignee_city_id')
            ->leftjoin('cities as olddhci', 'olddci.hub_id', '=', 'olddhci.id')
            ->leftjoin('zones as olddhciz', 'olddhci.zone_id', '=', 'olddhciz.id')
            ->leftjoin('cities as dc', function ($join) {
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
                                $sub_query->where('shipments.shipper_status_id', '=', 11)
                                    ->where('gmh.old_consignee_city_id', '!=', DB::raw('dc.hub_id'))
                                    ->whereNotExists(function ($sub_sub_query) {
                                        $sub_sub_query->select(DB::raw(1))
                                            ->from('cargo_manifest_bag_shipments as cmbs')
                                            ->join('cargo_manifest_bags as cmb', 'cmbs.cargo_manifest_bag_id', '=', 'cmb.id')
                                            ->whereColumn('cmbs.shipment_id', 'shipments.id')
                                            ->whereIn('cmb.status_id', [1,5]);
                                    });
                            })
                            ->orWhere(function ($sub_query) {
                                $sub_query->where('shipments.shipper_status_id', '=', 68)
                                    ->where('gmhh.old_consignee_city_id', '!=', DB::raw('dc.hub_id'));
                            })
                            ->orWhere(function ($sub_query) {
                                $sub_query->where('shipments.shipper_status_id', '=', 69)
                                    ->where('gmhh.old_consignee_city_id', '!=', DB::raw('dc.hub_id'))
                                    ->whereNotExists(function ($sub_sub_query) {
                                        $sub_sub_query->select(DB::raw(1))
                                            ->from('cargo_manifest_bag_shipments as cmbs')
                                            ->join('cargo_manifest_bags as cmb', 'cmbs.cargo_manifest_bag_id', '=', 'cmb.id')
                                            ->whereColumn('cmbs.shipment_id', 'shipments.id')
                                            ->whereIn('cmb.status_id', [1,5]);
                                    });
                            })
                            ->orWhere(function ($sub_query) {
                                $sub_query->where('shipments.shipper_status_id', '=', 72)
                                    ->where('gmhh.old_consignee_city_id', '!=', DB::raw('dc.hub_id'))
                                    ->whereNotExists(function ($sub_sub_query) {
                                        $sub_sub_query->select(DB::raw(1))
                                            ->from('cargo_manifest_bag_shipments as cmbs')
                                            ->join('cargo_manifest_bags as cmb', 'cmbs.cargo_manifest_bag_id', '=', 'cmb.id')
                                            ->whereColumn('cmbs.shipment_id', 'shipments.id')
                                            ->whereIn('cmb.status_id', [1,5]);
                                    });
                            })
                            ->orWhere(function ($sub_query) {
                                $sub_query->where('shipments.shipper_status_id', '=', 75)
                                    //->where('gmhh.old_consignee_city_id', '!=', DB::raw('dc.hub_id')) // commented because shipment was not showing again incase of return misrouted
                                    ->whereNotExists(function ($sub_sub_query) {
                                        $sub_sub_query->select(DB::raw(1))
                                            ->from('cargo_manifest_bag_shipments as cmbs')
                                            ->join('cargo_manifest_bags as cmb', 'cmbs.cargo_manifest_bag_id', '=', 'cmb.id')
                                            ->whereColumn('cmbs.shipment_id', 'shipments.id')
                                            ->whereIn('cmb.status_id', [1,5]);
                                    });
                            })
                            ->orWhere(function ($sub_query) {
                                $sub_query->where('shipments.shipper_status_id', '=', 55)
                                    ->where('irbrh.old_consignee_city_id', '!=', DB::raw('dc.hub_id'));
                            });
                    });
            })
            ->leftjoin('cities as dhc', 'dc.hub_id', '=', 'dhc.id')
            ->leftjoin('zones as dest_zone', 'dhc.zone_id', '=', 'dest_zone.id')
            ->leftjoin('crm_requests as crm', function ($join) {
                $join->on('crm.shipment_id', '=', 'shipments.id')
                    ->whereIn('crm.status_id', [2, 3, 5])
                    ->where('crm.case_nature_id', 1);
            })
            ->leftjoin('star_shippers as sts', 'sts.user_id', '=', 'u.id')
            ->leftJoin(DB::raw("
                    (
                      SELECT
                        dlm.city_id,
                        JSON_ARRAYAGG(JSON_OBJECT('k', LOWER(dlk.keyword), 'a', dlm.area_name)) AS city_kw
                      FROM delivery_location_mapping_keywords AS dlk
                      JOIN delivery_location_mappings AS dlm ON dlk.mapping_id = dlm.id
                      WHERE dlm.status = 1
                      GROUP BY dlm.city_id
                    ) AS citykw"), 'citykw.city_id', '=', 'dc.id')
            ->select('z.name as zone_name', 'shipments.shipper_status_id', 'shipments.tracking_number', 'shipments.tracking_number as tracking', 'shipments.order_id', 'bt.booking_type as service_type', 'ss.name as status', 'oc.name as origin', 'dc.name as destination', 'u.name as shipper', 'shipments.amount', 'sm.mode as shipping_mode', 'shipments.created_at as booked_at', 'shipments_journey.created_at as arrival_at', 'shipments.booking_type_id', 'usi.poc', 'csj.created_at as current_status', 'olddc.name as old_destination', 'olddci.name as old_destination_intercept', 'crm.id as complaint', 'shipments.return_address_id', 'rc.name as return_city_name', 'ohc.name as origin_hub', 'dhc.name as destination_hub', 'olddhci.name as old_destination_intercept_hub', 'olddhc.name as old_destination_hub', 'shipments.consignee_address', 'dc.id as destination_city_id', 'sts.status as star_status', 'rcz.name as return_zone_name', 'dest_zone.name as dest_zone', 'olddhcz.name as old_destination_hub_zone','olddhciz.name as old_destination_intercept_hub_zone', DB::raw('citykw.city_kw as city_kw'))
            ->whereNotIn('shipments.id', $on_hold_shipments)
            ->whereNotNull('shipments.tracking_number');


        if (session('role_id') != 1) {
            $shipments = $shipments->where(function ($query) {
                $hubs = session('hubs');

                $query->where(function ($sub_query) use ($hubs) {
                    // Group conditions by the same hub_id or city_id
                    $sub_query->whereIn('shipments.shipper_status_id', [20, 30, 37])
                        ->whereIn('dc.hub_id', $hubs)
                        ->orWhere(function ($q) use ($hubs) {
                            $q->where('shipments.shipper_status_id', 2)
                                ->whereIn('oc.hub_id', $hubs);
                        })
                        ->orWhere(function ($q) use ($hubs) {
                            $q->where('shipments.shipper_status_id', 49)
                                ->whereIn('olddc.hub_id', $hubs);
                        })
                        ->orWhere(function ($q) use ($hubs) {
                            $q->whereIn('shipments.shipper_status_id', [11, 68, 69, 72, 75])
                                ->whereIn('csj.city_id', $hubs);
                        })
                        ->orWhere(function ($q) use ($hubs) {
                            $q->where('shipments.shipper_status_id', 55)
                                ->whereIn('olddci.hub_id', $hubs);
                        });
                });
            });
        }


        if (session('department_id') == 8) {
            $shipments = $shipments->where('shipments.shipment_type', 2);
        }

        if ($request->get('search_date_from')) {
            if ($request->get('search_date_to')) {
                $from = $request->get('search_date_from') . ' 00:00:00';
                $to = $request->get('search_date_to') . ' 23:59:59';
                //$shipments->whereBetween('shipments_journey.created_at', [$from, $to]);
                $shipments->whereBetween('csj.created_at', [$from, $to]);
            } else {
                $from = $request->get('search_date_from');
                //$shipments->whereDate('shipments_journey.created_at', $from);
                $shipments->whereDate('csj.created_at', $from);
            }
        }
        if ($shipment_type = $request->get('shipment_type')) {
            if ($shipment_type == 0) {
                $shipments->whereIn('shipments.shipper_status_id', [2, 20, 30, 37, 49, 55,11,68,69,70,72,73,75,76]);
            } else if ($shipment_type == 1) {
                $shipments->whereIn('shipments.shipper_status_id', [2, 49, 55,11,68]);
            } else if ($shipment_type == 2) {
                $shipments->whereIn('shipments.shipper_status_id', [20, 30, 37,69,70,72,73,75,76]);
            }
        } else {
            $shipments->whereIn('shipments.shipper_status_id', [2, 20, 30, 37, 49, 55,11,68,69,70,72,73,75,76]);
        }
        if ($mode = $request->get('search_shipping_mode')) {
            $shipments->where('sm.id', '=', $mode);
        }

        if ($request->get('star_shipper_filter') == 1) {
            $shipments->where('sts.status', 1);
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
            ->addColumn('sub_station', function ($row) {
                // No keywords for this city? bail.
                if (empty($row->city_kw)) {
                    return '-';
                }

                // Decode once per row: [ {'k': 'keyword-lc', 'a': 'Area Name'}, ... ]
                $pairs = json_decode($row->city_kw, true);
                if (!is_array($pairs) || empty($pairs)) {
                    return '-';
                }

                // Build a quick hash: keyword(lower) => area_name
                $kwMap = [];
                foreach ($pairs as $p) {
                    // guard against malformed rows
                    if (isset($p['k'], $p['a'])) {
                        $kwMap[$p['k']] = $p['a'];
                    }
                }
                if (empty($kwMap)) {
                    return '-';
                }

                // Tokenize consignee address (lowercase, unicode-aware)
                $tokens = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($row->consignee_address ?? ''));
                if (!$tokens) {
                    return '-';
                }

                // Return the first matching area
                foreach ($tokens as $t) {
                    if ($t === '') continue;
                    if (isset($kwMap[$t])) {
                        return $kwMap[$t];
                    }
                }

                return '-';
            })
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('admin.tracking.index');
                if ($shipments->star_status) {
                    return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'><i class='star_shippers_icon'></i>$shipments->tracking_number</a></u>";
                } else {
                    return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
                }
            })
            ->editColumn('origin', function ($shipments) {
                if (in_array($shipments->shipper_status_id, [20, 30, 37, 75, 69, 72])) {
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
                if (in_array($shipments->shipper_status_id, [20, 30, 37, 75, 69, 72])) {
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
                } else if (in_array($shipments->shipper_status_id, [20, 75, 69, 72])) {
                    if ($shipments->return_address_id != NULL) {
                        return $shipments->return_city_name;
                    } else {
                        return $shipments->origin;
                    }
                } else {
                    return $shipments->destination;
                }
            })
            ->editColumn('zone_name', function ($shipments) {
                if (in_array($shipments->shipper_status_id, [20, 30, 37, 75, 69, 72])) {
                    return $shipments->dest_zone;
                } else if ($shipments->shipper_status_id == 49) {
                    return $shipments->old_destination_hub_zone;
                } else if ($shipments->shipper_status_id == 55) {
                    return $shipments->old_destination_intercept_hub_zone;
                } else {
                    return $shipments->zone_name;
                }
            })
            // ->editColumn('zone_name', function ($shipments) {
            //     if ($shipments->shipper_status_id == 20) {
            //         if ($shipments->return_address_id != NULL) {
            //             return $shipments->return_zone_name;
            //         } else {
            //             return $shipments->zone_name;
            //         }
            //     }else{
            //         return $shipments->zone_name;
            //     }
                
            // })
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
            ->filterColumn('z.name', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('z.name', 'like', '%' . $keyword . '%');
                } else {
                    $query->whereRaw('false');
                }
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
            ->orderColumn('dc.name', DB::raw('IF (shipments.shipper_status_id IN (20, 30, 37), oc.name, dc.name)') . ' $1')
            ->rawColumns(['tracking_number']);

        return $datatables->make(true);
    }

    public function pending_bag_list_old(Request $request)
    {

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 399);
        }
        $today = Carbon::today();
        $on_hold_shipments = ShipmentOnHold::whereDate('dispatch_date', '>', $today)->where('status', 1)->pluck('shipment_id')->toArray();
        $shipments = DB::connection('reports')->table('shipments')->leftJoin('booking_types as bt', 'shipments.booking_type_id', '=', 'bt.id')
            ->join('shipment_status as ss', 'shipments.shipper_status_id', '=', 'ss.id')
            ->join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities as ohc', 'oc.hub_id', '=', 'ohc.id')
            ->join('zones as z', 'ohc.zone_id', '=', 'z.id')
            ->join('shipping_modes as sm', 'shipments.shipping_mode_id', '=', 'sm.id')
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
            ->leftjoin('star_shippers as sts', 'sts.user_id', '=', 'u.id')
            ->select('z.name as zone_name','shipments.shipper_status_id', 'shipments.tracking_number', 'shipments.tracking_number as tracking', 'shipments.order_id', 'bt.booking_type as service_type', 'ss.name as status', 'oc.name as origin', 'dc.name as destination', 'u.name as shipper', 'shipments.amount', 'sm.mode as shipping_mode', 'shipments.created_at as booked_at', 'shipments_journey.created_at as arrival_at', 'shipments.booking_type_id', 'usi.poc', 'csj.created_at as current_status', 'olddc.name as old_destination', 'olddci.name as old_destination_intercept', 'crm.id as complaint', 'shipments.return_address_id', 'rc.name as return_city_name', 'ohc.name as origin_hub', 'dhc.name as destination_hub', 'olddhci.name as old_destination_intercept_hub', 'olddhc.name as old_destination_hub', 'shipments.consignee_address', 'dc.id as destination_city_id', 'sts.status as star_status')
            ->whereNotIn('shipments.id', $on_hold_shipments)
            ->whereNotNull('shipments.tracking_number');

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
            $shipments = $shipments->where('shipments.shipment_type', 2);
        }

        if ($request->get('search_date_from')) {
            if ($request->get('search_date_to')) {
                $from = $request->get('search_date_from') . ' 00:00:00';
                $to = $request->get('search_date_to') . ' 23:59:59';
                $shipments->whereBetween('shipments_journey.created_at', [$from, $to]);
            } else {
                $from = $request->get('search_date_from');
                $shipments->whereDate('shipments_journey.created_at', $from);
            }
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
                    ->select('dlm.city_id', 'delivery_location_mapping_keywords.keyword', 'dlm.area_name as area_name');

                if ($check->exists()) {
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
                if ($shipments->star_status) {
                    return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'><i class='star_shippers_icon'></i>$shipments->tracking_number</a></u>";
                } else {
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
            ->filterColumn('z.name', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('z.name', 'like', '%'.$keyword.'%');
                }
                else {
                    $query->whereRaw('false');
                }
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

        if ($request->get('star_shipper_filter') == 1) {
            $datatables->where('sts.status', 1);
        }

        return $datatables->make(true);
    }

    public function create_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 400);
        return view('admin.cargo.manifest.bags.create')->with('print', session('print'));
    }

    public function create_bag_details(Request $request) // create bag -> bag details
    {

        $shipment = Shipment::find(current($request->shipment_ids));

        $cargo_manifest_already_shipments_error = CargoManifestBagShipments::whereIn('shipment_id', $request->shipment_ids)
            ->whereIn('id', function ($query) use ($request) {
                $query->selectRaw('MAX(id)')
                    ->from('cargo_manifest_bag_shipments')
                    ->whereIn('shipment_id', $request->shipment_ids)
                    ->groupBy('shipment_id');
            })
            ->whereHas('bag', function ($query) {
                $query->whereIn('status_id', [1, 2]);
            })
            ->pluck('shipment_id')
            ->toArray();

        if (!empty($cargo_manifest_already_shipments_error)) {
            $tracking_numbers = Shipment::whereIn('id', $cargo_manifest_already_shipments_error)
                ->pluck('tracking_number')
                ->toArray();

            return response()->json([
                'status' => 1,
                'message' => 'These Shipments: ' . implode(', ', $tracking_numbers) . ' are already in other bags'
            ]);
        }

        $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
        if (!$dispute_check) {
            return ['status' => 1, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
        }

        if ($shipment->shipper_status_id == 49 || $shipment->shipper_status_id == 70 || $shipment->shipper_status_id == 73 ||$shipment->shipper_status_id == 76) {
            $shipment_details = $shipment->misrouted_history()->latest()->first();
            $city_details = City::find($shipment_details->old_consignee_city_id);
            $origin = $city_details->hub_city;
        } else if ($shipment->shipper_status_id == 55) {
            $shipment_details = $shipment->intercept_history;
            $city_details = City::find($shipment_details->old_consignee_city_id);
            $origin = $city_details->hub_city;
        } else {
            if ($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 69 || $shipment->shipper_status_id == 72 || $shipment->shipper_status_id == 75) {
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

        if ($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 49 || $shipment->shipper_status_id == 55 || $shipment->shipper_status_id == 35 || $shipment->shipper_status_id == 37 || $shipment->shipper_status_id == 30 || $shipment->shipper_status_id == 69 || $shipment->shipper_status_id == 72 || $shipment->shipper_status_id == 75) {
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

    public function create_bag_seal_number(Request $request) // create bag -> fetch seal no
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

    public function create_shipment_details(Request $request) //create bag -> shipment details
    {
        $misrouted_history_hub = 0;
        $intercept_re_book_history_hub = 0;

        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();

            $cargo_bag_shipments = CargoManifestBagShipments::where('shipment_id', $shipment->id);
            if($cargo_bag_shipments->exists()) {
                $cargo_bag_shipments =  $cargo_bag_shipments->latest()->pluck('cargo_manifest_bag_id')->first();
                $bag = CargoManifestBag::where('id', $cargo_bag_shipments)->whereIn('status_id' , [1,2]);
                if($bag->exists()) {
                    return ['status' => 1, 'error' => 'Shipment is already in other bag..!'];
                }
            }
            

            $misroute_history_count = $shipment->shipment_journey->where('shipper_status_id', 49)->count();

            if ($misroute_history_count == 0) //for support screen misroute
            {
                if (($shipment->pickup_address->city->id == $shipment->destination_city->id) && ($shipment->intercepted != 1) && $shipment->return_address_id == null) {
                    return ['status' => 1, 'error' => 'Shipment`s origin and destination are same !'];
                }
            }

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
            if (!$dispute_check) {
                return ['status' => 1, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
            }

            if ($shipment->shipper_status_id == 55) {

                $intercept_rebook_history = InterceptReBookRequestHistory::where('shipment_id', $shipment->id)->latest()->first();
                if ($intercept_rebook_history) {
                    $intercept_re_book_history_hub = $intercept_rebook_history->old_consignee_city->hub_id;

                    if ($intercept_re_book_history_hub == $intercept_rebook_history->new_consignee_city->hub_id) {
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

                || (($shipment->shipper_status_id == 55 && in_array($intercept_re_book_history_hub, session('hubs'))) || ($shipment->shipper_status_id == 49 && in_array($misrouted_history_hub, session('hubs')))) || 1
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


                if (in_array($shipment->shipper_status_id, [2, 20, 30, 37, 49, 55, 68, 69, 72, 70, 73,75,76])) {
                    if ($shipment->shipper_status_id == 2) {
                        $hub_id = $shipment->pickup_address->city->hub_id;
                    } else if ($shipment->shipper_status_id == 49 || $shipment->shipper_status_id == 70 || $shipment->shipper_status_id == 73 ) {
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
                    
                    if ($allowed || 1) {
                        if (($shipment->pickup_address->city->hub_id != $shipment->consignee_city->hub_id) || (in_array($shipment->shipper_status_id, [49, 55,70, 73]) && ($shipment->consignee_city->hub_id != $hub_id)) || ($shipment->shipper_status_id == 20) || 1) {  
                            if ($shipment->return_address_id != NULL) {
                                if (($shipment->shipper_status_id == 20) && ($shipment->consignee_city->hub_id == $shipment->return_address->city->hub_id)) {
                                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to same Origin and Destination Hub'];
                                }
                            } else if (($shipment->shipper_status_id == 20) && ($shipment->consignee_city->hub_id == $shipment->pickup_address->city->hub_id)) {
                                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to same Origin and Destination Hub'];
                            }

                            if ($request->bag_type != 0) {
                                if (in_array($shipment->shipper_status_id, [2, 49, 55, 68, 69,70,72,73])) {
                                    $hub_id = $shipment->consignee_city->hub_id;
                                } else {
                                    if (in_array($shipment->shipper_status_id, [20,75,76])) {
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
                            //                            dd($request->hub_id , $hub_id);
                            if ($request->hub_id == 0 || $request->hub_id == $hub_id) {
                                /*  if ($request->shipping_mode_id == 0 || $request->shipping_mode_id == $shipment->shipping_mode->id) {*/
                                $details = array();

                                if ($request->bag_type != 0) {
                                    if ($request->bag_type == 1) {
                                        if (!in_array($shipment->shipper_status_id, [2, 49, 55,68])) {
                                            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of Return Type while the Cargo is Normal Type'];
                                        }

                                        $bag_type = 1;
                                    } else {
                                        if (!in_array($shipment->shipper_status_id, [20, 30, 37, 75, 76,69,70,72,73])) {
                                            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of Normal Type while the Cargo is Return Type'];
                                        }

                                        $bag_type = 2;
                                    }
                                } else {
                                    if (in_array($shipment->shipper_status_id, [2, 49, 55, 68])) {
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
                                    $details['total'] = 0;
                                }
                                ShipmentScanningJourneyController::add($shipment->id, 2, 1, Auth::id(), null, null, null, null, null, null, null,  $request->action);

                                return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                                /*}
                                else {
                                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment\'s Shipment Mode is different'];
                                     }*/
                            } else {
                                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to another Hub']; // ye destination check karrahahe shipment ki is ko nahi remove krna h
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

    public function count_per_hub(Request $request) //create bag -> shipment details
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();
            if (in_array($shipment->shipper_status_id, [2, 20, 30, 37, 49, 55, 68, 69, 72, 70, 73,75,76])) {
                if ($shipment->shipper_status_id == 2) {
                    $hub_id = $shipment->pickup_address->city->hub_id;
                } else if ($shipment->shipper_status_id == 49 || $shipment->shipper_status_id == 70 || $shipment->shipper_status_id == 73 ) {
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
                if ($allowed || 1) {
                    if (($shipment->pickup_address->city->hub_id != $shipment->consignee_city->hub_id) || (in_array($shipment->shipper_status_id, [49, 55,70, 73]) && ($shipment->consignee_city->hub_id != $hub_id)) || ($shipment->shipper_status_id == 20) || 1) {
                        if ($shipment->return_address_id != NULL) {
                            if (($shipment->shipper_status_id == 20) && ($shipment->consignee_city->hub_id == $shipment->return_address->city->hub_id)) {
                                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to same Origin and Destination Hub'];
                            }
                        } else if (($shipment->shipper_status_id == 20) && ($shipment->consignee_city->hub_id == $shipment->pickup_address->city->hub_id)) {
                            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to same Origin and Destination Hub'];
                        }

                        if ($request->bag_type != 0) {
                            if (in_array($shipment->shipper_status_id, [2, 49, 55, 68, 69,70,72,73])) {
                                $hub_id = $shipment->consignee_city->hub_id;
                            } else {
                                if (in_array($shipment->shipper_status_id, [20,75,76])) {
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
                            $details = array();
                            if ($request->bag_type != 0) {
                                if ($request->bag_type == 1) {
                                    if (!in_array($shipment->shipper_status_id, [2, 49, 55,68])) {
                                        return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of Return Type while the Cargo is Normal Type'];
                                    }

                                    $bag_type = 1;
                                } else {
                                    if (!in_array($shipment->shipper_status_id, [20, 30, 37, 75, 76,69,70,72,73])) {
                                        return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of Normal Type while the Cargo is Return Type'];
                                    }

                                    $bag_type = 2;
                                }
                            } else {
                                if (in_array($shipment->shipper_status_id, [2, 49, 55, 68])) {
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
                            $startDate = Carbon::now()->subMonth(2)->format('Y-m-d 00:00:00');
                            $endDate = Carbon::now()->format('Y-m-d 23:59:59');

                            if ($request->hub_id == 0) {
                                if ($bag_type == 1) {
                                    $shipments = Shipment::leftjoin('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
                                        ->leftjoin('cities as oc', 'usi.city_id', '=', 'oc.id')
                                        ->leftjoin('cities as dc', function ($join) {
                                            $join->on('shipments.consignee_city_id', '=', 'dc.id')
                                                ->on('oc.hub_id', '!=', 'dc.hub_id');
                                        })
                                        ->select(DB::raw('count(shipments.id) as count'))
                                        ->where('dc.hub_id', $hub->id)
                                        ->whereBetween('shipments.created_at', [$startDate, $endDate])
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
                                        ->whereBetween('shipments.created_at', [$startDate, $endDate])
                                        ->where('shipments.shipping_mode_id', $shipping_mode_id);
                                }

                                if (session('role_id') != 1) {
                                    $shipments = $shipments->whereIn('oc.hub_id', session('hubs'));
                                }

                                $shipments = $shipments->first();

                                $details['total'] = $shipments->count;
                            }
                            return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                        } else {
                            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to another Hub']; // ye destination check karrahahe shipment ki is ko nahi remove krna h
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
    }

    public function create_shipment_details_old(Request $request)
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
            if (!$dispute_check) {
                return ['status' => 1, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
            }

            if ($shipment->shipper_status_id == 55) {

                $intercept_rebook_history = InterceptReBookRequestHistory::where('shipment_id', $shipment->id)->latest()->first();
                if ($intercept_rebook_history) {
                    $intercept_re_book_history_hub = $intercept_rebook_history->old_consignee_city->hub_id;

                    if ($intercept_re_book_history_hub == $intercept_rebook_history->new_consignee_city->hub_id) {
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
                            } else if (($shipment->shipper_status_id == 20) && ($shipment->consignee_city->hub_id == $shipment->pickup_address->city->hub_id)) {
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

                                $startDate = Carbon::now()->subMonth()->format('Y-m-d 00:00:00');
                                $endDate = Carbon::now()->format('Y-m-d 23:59:59');
                                if ($request->hub_id == 0) {
                                    if ($bag_type == 1) {
                                        $shipments = Shipment::join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
                                            ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
                                            ->join('cities as dc', function ($join) {
                                                $join->on('shipments.consignee_city_id', '=', 'dc.id')
                                                    ->on('oc.hub_id', '!=', 'dc.hub_id');
                                            })
                                            ->select(DB::raw('count(shipments.id) as count'))
                                            ->whereBetween('shipments.created_at', [$startDate, $endDate])
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
                                            ->whereBetween('shipments.created_at', [$startDate, $endDate])
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

    // public function create_shipment_details_old(Request $request)
    // {
    //     $misrouted_history_hub = 0;
    //     $intercept_re_book_history_hub = 0;

    //     $shipment = Shipment::where('tracking_number', $request->tracking_number);
    //     if ($shipment->exists()) {
    //         $shipment = $shipment->first();

    //         /*  if($shipment->shipper_status_id == 49){
    //               return ['status' => 1, 'error' => 'Misroute-Forwarded Shipments not allowed'];
    //           }*/

    //         /* if(CargoManifestBag::where('seal_number',$shipment->tracking_number)->where('status_id',1)->exists()){
    //              return ['status' => 1, 'error' => 'Bag Already Created'];
    //          }


    //          if($shipment->consignee_city->hub_city->id ==  Auth::user()->default_hub_id){
    //              return ['status' => 1, 'error' => 'Cannot create bag with same origin and destination'];
    //          }*/

    //         $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
    //         if (!$dispute_check) {
    //             return ['status' => 1, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
    //         }

    //         if ($shipment->shipper_status_id == 55) {

    //             $intercept_rebook_history = InterceptReBookRequestHistory::where('shipment_id', $shipment->id)->latest()->first();
    //             if ($intercept_rebook_history) {
    //                 $intercept_re_book_history_hub = $intercept_rebook_history->old_consignee_city->hub_id;

    //                 if ($intercept_re_book_history_hub == $intercept_rebook_history->new_consignee_city->hub_id) {
    //                     return ['status' => 1, 'error' => 'Cannot create bag for same hub'];
    //                 }
    //             }

    //         }

    //         if ($shipment->shipper_status_id == 49) {
    //             $misrouted_history = MisroutedHistory::where('shipment_id', $shipment->id)->latest()->first();
    //             if ($misrouted_history) {
    //                 $city = City::where('id', $misrouted_history->old_consignee_city_id)->first();
    //                 $misrouted_history_hub = $city->hub_id;
    //             }
    //         }


    //         if (
    //             (($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 35 || $shipment->shipper_status_id == 37 || $shipment->shipper_status_id == 30) && in_array($shipment->consignee_city->hub_id, session('hubs')))

    //             || (($shipment->shipper_status_id != 35 && $shipment->shipper_status_id != 37 && $shipment->shipper_status_id != 20 && $shipment->shipper_status_id != 30 && $shipment->shipper_status_id != 55 && $shipment->shipper_status_id != 49) && in_array($shipment->pickup_address->city->hub_id, session('hubs')))

    //             || (($shipment->shipper_status_id == 55 && in_array($intercept_re_book_history_hub, session('hubs'))) || ($shipment->shipper_status_id == 49 && in_array($misrouted_history_hub, session('hubs'))))
    //         ) {


    //             $on_hold_shipment = ShipmentOnHold::where('shipment_id', $shipment->id)->where('status', 1);
    //             if ($on_hold_shipment->exists()) {
    //                 if (!in_array(Auth::id(), [10, 288, 423])) {
    //                     $on_hold_shipment = $on_hold_shipment->first();
    //                     $dispatch_date = Carbon::parse($on_hold_shipment->dispatch_date);
    //                     $today = Carbon::today();
    //                     if ($dispatch_date > $today) {
    //                         $dispatch_date = $dispatch_date->toFormattedDateString();
    //                         return ['status' => 1, 'error' => 'Shipment is marked as On-Hold until ' . $dispatch_date];
    //                     }
    //                 }
    //             }
    //             if ($shipment->packaging_material_request == 1) {


    //                 $packaging_material_request = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number)->first();

    //                 if ($packaging_material_request != null) {
    //                     if ($packaging_material_request->status_id != 3) {
    //                         return ['status' => 1, 'error' => 'Packaging Material Request is not dispatched yet!'];
    //                     }
    //                 }
    //                 $packaging_material_request_stock = WarehouseStockRequest::where('tracking_number', $shipment->tracking_number)->first();
    //                 if ($packaging_material_request_stock != null) {
    //                     if ($packaging_material_request_stock->status_id != 3) {
    //                         return ['status' => 1, 'error' => 'Warehouse Stock Request is not dispatched yet!'];
    //                     }
    //                 }
    //             }


    //             if (in_array($shipment->shipper_status_id, [2, 20, 30, 37, 49, 55])) {
    //                 if ($shipment->shipper_status_id == 2) {
    //                     $hub_id = $shipment->pickup_address->city->hub_id;
    //                 } else if ($shipment->shipper_status_id == 49) {
    //                     $shipment_details = $shipment->misrouted_history()->latest()->first();
    //                     $city_details = City::find($shipment_details->old_consignee_city_id);
    //                     $hub_id = $city_details->hub_id;
    //                 } else if ($shipment->shipper_status_id == 55) {
    //                     $shipment_details = $shipment->intercept_history;
    //                     $city_details = City::find($shipment_details->old_consignee_city_id);
    //                     $hub_id = $city_details->hub_id;
    //                 } else {
    //                     $hub_id = $shipment->consignee_city->hub_id;
    //                 }

    //                 $allowed = FALSE;

    //                 if (session('role_id') == 1) {
    //                     $allowed = TRUE;
    //                 } else if (in_array($hub_id, session('hubs'))) {
    //                     $allowed = TRUE;
    //                 } else if ($shipment->shipper_status_id == 49 && in_array($shipment->pickup_address->city->hub_id, session('hubs'))) {
    //                     $allowed = TRUE;
    //                 }

    //                 if ($allowed) {
    //                     if (($shipment->pickup_address->city->hub_id != $shipment->consignee_city->hub_id) || (in_array($shipment->shipper_status_id, [49, 55]) && ($shipment->consignee_city->hub_id != $hub_id)) || ($shipment->shipper_status_id == 20)) {
    //                         if ($shipment->return_address_id != NULL) {
    //                             if (($shipment->shipper_status_id == 20) && ($shipment->consignee_city->hub_id == $shipment->return_address->city->hub_id)) {
    //                                 return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to same Origin and Destination Hub'];
    //                             }
    //                         } else if (($shipment->shipper_status_id == 20) && ($shipment->consignee_city->hub_id == $shipment->pickup_address->city->hub_id)) {
    //                             return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to same Origin and Destination Hub'];
    //                         }

    //                         if ($request->bag_type != 0) {
    //                             if (in_array($shipment->shipper_status_id, [2, 49, 55])) {
    //                                 $hub_id = $shipment->consignee_city->hub_id;
    //                             } else {
    //                                 if (in_array($shipment->shipper_status_id, [20])) {
    //                                     if ($shipment->return_address_id != null) {
    //                                         $hub_id = $shipment->return_address->city->hub_id;
    //                                     } else {
    //                                         $hub_id = $shipment->pickup_address->city->hub_id;
    //                                     }
    //                                 } else {
    //                                     $hub_id = $shipment->pickup_address->city->hub_id;
    //                                 }
    //                             }
    //                         } else {
    //                             $hub_id = 0;
    //                         }

    //                         if ($request->hub_id == 0 || $request->hub_id == $hub_id) {
    //                             /*  if ($request->shipping_mode_id == 0 || $request->shipping_mode_id == $shipment->shipping_mode->id) {*/
    //                             $details = array();

    //                             if ($request->bag_type != 0) {
    //                                 if ($request->bag_type == 1) {
    //                                     if (!in_array($shipment->shipper_status_id, [2, 49, 55])) {
    //                                         return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of Return Type while the Cargo is Normal Type'];
    //                                     }

    //                                     $bag_type = 1;
    //                                 } else {
    //                                     if (!in_array($shipment->shipper_status_id, [20, 30, 37])) {
    //                                         return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of Normal Type while the Cargo is Return Type'];
    //                                     }

    //                                     $bag_type = 2;
    //                                 }
    //                             } else {
    //                                 if (in_array($shipment->shipper_status_id, [2, 49, 55])) {
    //                                     $details['bag_type'] = 1;

    //                                     $bag_type = 1;
    //                                 } else {
    //                                     $details['bag_type'] = 2;

    //                                     $bag_type = 2;
    //                                 }
    //                             }

    //                             if ($bag_type == 1) {
    //                                 $destination = $shipment->consignee_city;
    //                             } else {
    //                                 if ($shipment->return_address_id != NULL) {
    //                                     $destination = $shipment->return_address->city;
    //                                 } else {
    //                                     $destination = $shipment->pickup_address->city;
    //                                 }

    //                             }
    //                             if (!$request->has('pieces_confirm')) {
    //                                 if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
    //                                     $details = array();
    //                                     $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

    //                                     $details['id'] = $shipment->id;
    //                                     $details['tracking_number'] = $shipment->tracking_number;
    //                                     $details['pieces_count'] = $shipment->pieces;
    //                                     $details['pieces_tracking_numbers'] = $shipment_pieces;
    //                                     return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
    //                                 }
    //                             }
    //                             $details['id'] = $shipment->id;
    //                             $details['tracking_number'] = $shipment->tracking_number;
    //                             $details['order_id'] = $shipment->order_id;
    //                             $details['service_type'] = $shipment->booking_type->booking_type;
    //                             $details['destination'] = $destination->name;
    //                             $details['amount'] = number_format($shipment->amount);
    //                             $hub = $destination->hub_city;

    //                             $details['hub']['id'] = $hub->id;
    //                             $details['hub']['name'] = $hub->name;

    //                             $shipping_mode_id = $request->shipping_mode_id;

    //                             if ($shipping_mode_id == 0) {
    //                                 $shipping_mode_id = $shipment->shipping_mode->id;

    //                                 $details['shipping_mode']['id'] = $shipment->shipping_mode->id;
    //                                 $details['shipping_mode']['name'] = $shipment->shipping_mode->mode;
    //                             }

    //                             if ($request->hub_id == 0) {
    //                                 if ($bag_type == 1) {
    //                                     $shipments = Shipment::join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
    //                                         ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
    //                                         ->join('cities as dc', function ($join) {
    //                                             $join->on('shipments.consignee_city_id', '=', 'dc.id')
    //                                                 ->on('oc.hub_id', '!=', 'dc.hub_id');
    //                                         })
    //                                         ->select(DB::raw('count(shipments.id) as count'))
    //                                         ->where('dc.hub_id', $hub->id)
    //                                         ->whereIn('shipments.shipper_status_id', [2, 49, 55])
    //                                         ->where('shipments.shipping_mode_id', $shipping_mode_id);
    //                                 } else {

    //                                     $shipments = Shipment::leftjoin('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
    //                                         ->leftjoin('cities as dc', 'usi.city_id', '=', 'dc.id')
    //                                         ->leftjoin('user_shipping_infos as rsi', 'shipments.return_address_id', '=', 'rsi.id')
    //                                         ->leftjoin('cities as rc', 'rsi.city_id', '=', 'rc.id')
    //                                         ->join('cities as oc', function ($join) use ($hub) {
    //                                             $join->on('shipments.consignee_city_id', '=', 'oc.id')
    //                                                 ->where(function ($query) use ($hub) {
    //                                                     $query->where(function ($sub_query) use ($hub) {
    //                                                         $sub_query->whereIn('shipments.shipper_status_id', [30, 37])
    //                                                             ->where('dc.hub_id', '!=', 'oc.hub_id')
    //                                                             ->where('dc.hub_id', $hub->id);
    //                                                     })
    //                                                         ->orWhere(function ($sub_query) use ($hub) {
    //                                                             $sub_query->where('shipments.shipper_status_id', 20)
    //                                                                 ->where(function ($sub_sub_query) use ($hub) {
    //                                                                     $sub_sub_query->where(function ($sub_sub_sub_query) use ($hub) {
    //                                                                         $sub_sub_sub_query->whereNull('return_address_id')
    //                                                                             ->where('dc.hub_id', '!=', 'oc.hub_id')
    //                                                                             ->where('dc.hub_id', $hub->id);
    //                                                                     })
    //                                                                         ->orWhere(function ($sub_sub_sub_query) use ($hub) {
    //                                                                             $sub_sub_sub_query->whereNotNull('return_address_id')
    //                                                                                 ->where('rc.hub_id', '!=', 'oc.hub_id')
    //                                                                                 ->where('rc.hub_id', $hub->id);
    //                                                                         });
    //                                                                 });
    //                                                         });
    //                                                 });
    //                                         })
    //                                         ->select(DB::raw('count(shipments.id) as count'))
    //                                         ->where('shipments.shipping_mode_id', $shipping_mode_id);

    //                                 }

    //                                 if (session('role_id') != 1) {
    //                                     $shipments = $shipments->whereIn('oc.hub_id', session('hubs'));
    //                                 }

    //                                 $shipments = $shipments->first();

    //                                 $details['total'] = $shipments->count;
    //                             }
    //                             ShipmentScanningJourneyController::add($shipment->id, 2, 1, Auth::id(), NULL, NULL, NULL, NULL, session('latitude'), session('longitude'), NULL);


    //                             return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
    //                             /*}
    //                             else {
    //                                 return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment\'s Shipment Mode is different'];
    //                                  }*/
    //                         } else {
    //                             return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to another Hub'];
    //                         }
    //                     } else {
    //                         return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to same Origin and Destination Hub'];
    //                     }
    //                 } else {
    //                     return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment does not belong to any of your assigned Hub\'s Cities'];
    //                 }
    //             } else {
    //                 return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
    //             }
    //         } else {
    //             return ['status' => 1, 'error' => 'Shipment belongs to another hub'];
    //         }
    //     } else {
    //         return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
    //     }
    // }

    public function create_store(Request $request) //create bag -> store
    {
        $sack_bag_no = $request->input('sack_bag_no');
        $sack_bag = IssueSackBagOrigin::where('sack_bag_no', $sack_bag_no)->where('status', 1);
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

                if (in_array($shipment->shipper_status_id, [2, 20, 30, 37, 49, 55, 68, 69, 70, 72, 73,75,76 ])) {
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
                if ($sack_bag->exists()) {
                    $sack_bag = $sack_bag->first();
                    $bag->sack_bag_id = $sack_bag->id;
                    $bag->is_sack_bag = 1;
                    $sack_bag->save();
                } else {
                    $bag->is_sack_bag = 0;
                }

                $bag->save();
                CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), NULL, NULL, 1);
                $id = $bag->id;
                foreach ($shipment_ids as $shipment_id) {
                    $bag_shipment = new CargoManifestBagShipments();
                    $bag_shipment->cargo_manifest_bag_id = $id;
                    $bag_shipment->shipment_id = $shipment_id;
                    $bag_shipment->save();
                    $shipment = Shipment::find($shipment_id);
                    $shipper_status_id = NULL;
                    $consignee_status_id = NULL;
                    //journey of intransit stopped on bag creation / moved to manifest creation as per TO-6734 done by Najam Nazar
                    // if ($request->input('bag_type') == 1) {
                    //     $shipper_status_id = 3;
                    //     $consignee_status_id = 3;
                    // } else {
                    //     $shipper_status_id = 21;
                    //     $consignee_status_id = 21;

                    //     if ($shipment->shipper_status_id != 20) {
                    //         if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4 || $shipment->booking_type_id == 5) {
                    //             $shipper_status_id = 21;
                    //             $consignee_status_id = 21;
                    //         } else if ($shipment->booking_type_id == 2) {
                    //             $shipper_status_id = 26;
                    //             $consignee_status_id = 26;
                    //         } else if ($shipment->booking_type_id == 3) {
                    //             $shipper_status_id = 32;
                    //             $consignee_status_id = 32;
                    //         } else {
                    //             $shipper_status_id = 21;
                    //             $consignee_status_id = 21;
                    //         }
                    //     }
                    // }

                    // $shipment->shipper_status_id = $shipper_status_id;
                    // $shipment->consignee_status_id = $consignee_status_id;
                    // $shipment->save();

                    if (in_array($shipment_id, $open_box_ids)) {
                        $shipment->open_box = 1;
                        $shipment->save();
                        ShipmentOpenBoxJourneyController::add($shipment_id, 1, Auth::id());
                    }

                    //journey of intransit stopped on bag creation / moved to manifest creation as per TO-6734 done by Najam Nazar
                    //ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $consignee_status_id, NULL, NULL, NULL, Auth::id(), $bag->id);
                }

                return redirect()->route('admin.cargo_manifest.bags.create.index')->with(['success' => 'Bag Created with Bag Number: ' . $bag->seal_number]);
            } else {
                return back()->withErrors('All Shipments have already been added to another Bag!');
            }
        // }else{
        //     return back()->withErrors('No Sack Bag Available');
        // }


    }

    public function create_store_old(Request $request)
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

            CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), NULL, NULL, 1);

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

            if (!$shipment->actual_weight) {
                return back()->withErrors('Shipment actual weight is missing');
            }

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

                CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), NULL, NULL, 2);

                $id = $bag->id;
                $bag_shipment = new CargoManifestBagShipments();

                $bag_shipment->cargo_manifest_bag_id = $id;
                $bag_shipment->shipment_id = $shipment_id;

                $bag_shipment->save();


                $shipper_status_id = NULL;
                $consignee_status_id = NULL;

                // if ($bag_type == 1) {
                //     $shipper_status_id = 3;
                //     $consignee_status_id = 3;
                // } else {
                //     $shipper_status_id = 21;
                //     $consignee_status_id = 21;

                //     if ($shipment->shipper_status_id != 20) {
                //         if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4 || $shipment->booking_type_id == 5) {
                //             $shipper_status_id = 21;
                //             $consignee_status_id = 21;
                //         } else if ($shipment->booking_type_id == 2) {
                //             $shipper_status_id = 26;
                //             $consignee_status_id = 26;
                //         } else if ($shipment->booking_type_id == 3) {
                //             $shipper_status_id = 32;
                //             $consignee_status_id = 32;
                //         } else {
                //             $shipper_status_id = 21;
                //             $consignee_status_id = 21;
                //         }
                //     }
                // }

                // $shipment->shipper_status_id = $shipper_status_id;
                // $shipment->consignee_status_id = $consignee_status_id;

                // $shipment->save();


                if (in_array($shipment_id, $open_box_ids)) {
                    $shipment->open_box = 1;
                    $shipment->save();
                    ShipmentOpenBoxJourneyController::add($shipment_id, 1, Auth::id());
                }

                // ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $consignee_status_id, NULL, NULL, NULL, Auth::id(), $bag->id);

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
        $hubs = City::where('status', '1')->where('business_category_id', '1')->where('hub', '1')->select('id', 'name')->get();
        return view('admin.cargo.manifest.bags.history')->with(['bag_statuses' => $bag_statuses, 'transport_mode' => $transport_mode, 'shipping_mode' => $shipping_mode, 'hubs' => $hubs]);
    }

    public function history_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 402);
        }
        $bags = CargoManifestBag::join('cities as oh', 'cargo_manifest_bags.origin_hub_id', '=', 'oh.id')
            ->Leftjoin('manifest_bags as mcb', function ($join) {
                $join->on('mcb.cargo_manifest_bag_id', '=', 'cargo_manifest_bags.id')
                    ->where(
                        'mcb.id',
                        '=',
                        DB::raw('(select max(id) from manifest_bags where manifest_bags.cargo_manifest_bag_id = cargo_manifest_bags.id)')
                    );
            })
            ->leftjoin('cargo_manifests as cm', 'cm.id', '=', 'mcb.cargo_manifest_id')
            ->leftjoin('shipping_modes as sm', 'sm.id', '=', 'cm.shipping_mode_id')
            ->join('cities as dh', 'cargo_manifest_bags.destination_hub_id', '=', 'dh.id')
            ->join('admins as a', 'cargo_manifest_bags.created_by', '=', 'a.id')
            ->join('cargo_manifest_bag_statuses as bs', 'cargo_manifest_bags.status_id', '=', 'bs.id')
            ->join('transport_modes as tm', 'cargo_manifest_bags.transport_mode_id', '=', 'tm.id')

            // ->leftJoin('manifest_bags', 'manifest_bags.cargo_manifest_id', '=', 'cm.id')
            // ->leftJoin('cargo_manifest_bags as cmb', 'cmb.seal_number', '=', 'manifest_bags.id')
            ->leftJoin('cargo_manifest_bag_shipments as cargo_shipments', 'cargo_shipments.cargo_manifest_bag_id', '=', 'cargo_manifest_bags.id')
            ->leftJoin('shipments', 'shipments.id', '=', 'cargo_shipments.shipment_id')
            ->leftJoin('shipper_segment_logs', 'shipper_segment_logs.shipment_id', '=', 'cargo_shipments.shipment_id')
            ->leftJoin('sub_category_segments as sub_segment', 'shipper_segment_logs.sub_segment_id', '=', 'sub_segment.id')

            ->select(
                'cargo_manifest_bags.id',
                'cargo_manifest_bags.status_id',
                'oh.id as origin_id',
                'oh.name as origin',
                'dh.id as destination_id',
                'dh.name as destination',
                'cargo_manifest_bags.shipments',
                'tm.name as transport_mode',
                'cargo_manifest_bags.shipments_weight',
                DB::raw(
                    '(
                        SELECT SUM(`s`.`chargeable_weight`) 
                        FROM `shipments` AS `s`
                        INNER JOIN `cargo_manifest_bag_shipments` AS `bss` 
                        ON `s`.`id` = `bss`.`shipment_id`
                        WHERE `bss`.`cargo_manifest_bag_id` = `cargo_manifest_bags`.`id`) 
                        AS `chargeable_weight`'
                ),
                'cargo_manifest_bags.actual_weight',
                'a.name as transitted_by',
                'oh.hub_id as origin_hub_id',
                'dh.hub_id as destination_hub_id',
                'cargo_manifest_bags.type as bag_type',
                'cargo_manifest_bags.seal_number',
                'bs.name as status',
                'cm.id as manifest_id',
                'cargo_manifest_bags.junction_mapping_id as junction_mapping_id',
                'cargo_manifest_bags.created_at as transitted_at',
                'cm.id as manifest',
                'sm.mode as shipping_mode',
                'cargo_manifest_bags.short_received_shipments as short_received_shipments',
                'cargo_manifest_bags.short_received_shipments as short_received',
                'cargo_manifest_bags.lost_shipments as lost_shipments',
                'cargo_manifest_bags.lost_shipments as ls',
                'cargo_manifest_bags.received_at as received_at',
                DB::raw('GROUP_CONCAT(sub_segment.name) as segment_names'),
                DB::raw('COUNT(DISTINCT sub_segment.name) as segment_count'),
                DB::raw('GROUP_CONCAT(shipments.actual_weight) as segment_weights')
            )
            ->groupBy('cargo_manifest_bags.id');

        if (session('role_id') != 1) {
            $bags = $bags->where(function ($query) {
                $query->whereIn('oh.hub_id', session('hubs'))->orWhereIn('dh.hub_id', session('hubs'));
            });
        }

        if ($bag_type = $request->get('bag_type')) {
            if ($bag_type != 0) {
                $bags->where('cargo_manifest_bags.type', $bag_type);
            }
        }

        if ($tracking_number = $request->get('tracking_number')) {
            $bags->join('cargo_manifest_bag_shipments as bssh', 'cargo_manifest_bags.id', '=', 'bssh.cargo_manifest_bag_id')
                ->join('shipments as s', 'bssh.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
        }

        if ($bag_number = $request->get('bag_number'))
            $bags->where('cargo_manifest_bags.seal_number', $bag_number);


        if ($request->get('search_date_from') != null && $request->get('search_date_to') != null) {

            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $bags->whereBetween('cargo_manifest_bags.created_at', [$from, $to]);
        }

        if ($search_origin = $request->get('search_origin')) {
            $bags = $bags->where('oh.id', '=', $search_origin);
        }

        if ($search_destination = $request->get('search_destination')) {
            $bags = $bags->where('dh.id', '=', $search_destination);
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
            })
            
            ->editColumn('express_count', function ($row) {
                return substr_count($row->segment_names, 'Express');
            })
            ->editColumn('cod_count', function ($row) {
                return substr_count($row->segment_names, 'COD');
            })
            ->editColumn('logistics_count', function ($row) {
                return substr_count($row->segment_names, 'Logistics');
            })
            ->editColumn('warehouse_count', function ($row) {
                return substr_count($row->segment_names, 'Warehouse');
            })
            ->editColumn('international_count', function ($row) {
                return substr_count($row->segment_names, 'International');
            })
            ->editColumn('hyperlocal_count', function ($row) {
                return substr_count($row->segment_names, 'Hyperlocal');
            })
            ->editColumn('fod_count', function ($row) {
                return substr_count($row->segment_names, 'FOD');
            })
            ->editColumn('retail_count', function ($row) {
                return substr_count($row->segment_names, 'Retail');
            })

            ->editColumn('logistics_weight', function ($row) {
                $segmentNames = explode(',', $row->segment_names);
                $segmentWeights = explode(',', $row->segment_weights);
                $totalWeight = 0;
            
                foreach ($segmentNames as $index => $name) {
                    if (trim($name) === 'Logistics' && isset($segmentWeights[$index]) && !empty($segmentWeights[$index])) {
                        $totalWeight += (float)$segmentWeights[$index];
                    }
                }
                return $totalWeight;
            })
            ->editColumn('express_weight', function ($row) {
                $segmentNames = explode(',', $row->segment_names);
                $segmentWeights = explode(',', $row->segment_weights);
                $totalWeight = 0;
            
                foreach ($segmentNames as $index => $name) {
                    if (trim($name) === 'Express' && isset($segmentWeights[$index]) && !empty($segmentWeights[$index])) {
                        $totalWeight += (float)$segmentWeights[$index];
                    }
                }
                return $totalWeight;
            })
            ->editColumn('warehouse_weight', function ($row) {
                $segmentNames = explode(',', $row->segment_names);
                $segmentWeights = explode(',', $row->segment_weights);
                $totalWeight = 0;
            
                foreach ($segmentNames as $index => $name) {
                    if (trim($name) === 'Warehouse' && isset($segmentWeights[$index]) && !empty($segmentWeights[$index])) {
                        $totalWeight += (float)$segmentWeights[$index];
                    }
                }
                return $totalWeight;
            })
            ->editColumn('international_weight', function ($row) {
                $segmentNames = explode(',', $row->segment_names);
                $segmentWeights = explode(',', $row->segment_weights);
                $totalWeight = 0;
            
                foreach ($segmentNames as $index => $name) {
                    if (trim($name) === 'International' && isset($segmentWeights[$index]) && !empty($segmentWeights[$index])) {
                        $totalWeight += (float)$segmentWeights[$index];
                    }
                }
                return $totalWeight;
            })
            ->editColumn('cod_weight', function ($row) {
                $segmentNames = explode(',', $row->segment_names);
                $segmentWeights = explode(',', $row->segment_weights);
                $totalWeight = 0;
            
                foreach ($segmentNames as $index => $name) {
                    if (trim($name) === 'COD' && isset($segmentWeights[$index]) && !empty($segmentWeights[$index])) {
                        $totalWeight += (float)$segmentWeights[$index];
                    }
                }
                return $totalWeight;
            })
            ->editColumn('hyperlocal_weight', function ($row) {
                $segmentNames = explode(',', $row->segment_names);
                $segmentWeights = explode(',', $row->segment_weights);
                $totalWeight = 0;
            
                foreach ($segmentNames as $index => $name) {
                    if (trim($name) === 'Hyperlocal' && isset($segmentWeights[$index]) && !empty($segmentWeights[$index])) {
                        $totalWeight += (float)$segmentWeights[$index];
                    }
                }
                return $totalWeight;
            })
            ->editColumn('fod_weight', function ($row) {
                $segmentNames = explode(',', $row->segment_names);
                $segmentWeights = explode(',', $row->segment_weights);
                $totalWeight = 0;
            
                foreach ($segmentNames as $index => $name) {
                    if (trim($name) === 'FOD' && isset($segmentWeights[$index]) && !empty($segmentWeights[$index])) {
                        $totalWeight += (float)$segmentWeights[$index];
                    }
                }
                return $totalWeight;
            })
            ->editColumn('retail_weight', function ($row) {
                $segmentNames = explode(',', $row->segment_names);
                $segmentWeights = explode(',', $row->segment_weights);
                $totalWeight = 0;
            
                foreach ($segmentNames as $index => $name) {
                    if (trim($name) === 'Retail' && isset($segmentWeights[$index]) && !empty($segmentWeights[$index])) {
                        $totalWeight += (float)$segmentWeights[$index];
                    }
                }
                return $totalWeight;
            })

            ->rawColumns(['shipments','junctions','short_received_shipments','manifest_id','lost_shipments']);

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
        if (count($draft_bags) > 0) {
            foreach ($draft_bags as $draft_bag) {
                if (!in_array($draft_bag->destination_id, $bag_destinations)) {
                    $bag_destinations[] = $draft_bag->destination_id;
                }
            }
            $total_bags = CargoManifestBag::whereIn('destination_hub_id', $bag_destinations)->whereIn('status_id', [1, 3, 5])->where('current_hub_id', Auth::user()->default_hub_id)->count();
        }
        return view('admin.cargo.manifest.create', compact('shipping_modes', 'draft_bags', 'bag_destinations', 'total_bags', 'scanned_bags'));
    }

    public function bag_details(Request $request) // create manifest -> fetch bag details
    {
        $bag = CargoManifestBag::with('shipment', 'shipment.shipment')->where('seal_number', $request->bag_number);
        $bag_destinations = $request->bag_destinations;
        if ($bag->exists()) {
            $bag = $bag->latest()->first();

            ShipmentScanningJourneyController::seal_number_add($bag->id, 1, Auth::id(), $request->action);

            if (in_array($bag->status_id, [1, 3, 5])) {
                $origin_id = Auth::user()->default_hub_id;
                $destination_hub_id = $bag->destination_hub_id;
                $assigned_hubs = array_merge(session('hubs'), [$origin_id]);
                $mapping = V2JunctionMapping::whereIn('origin_id', $assigned_hubs)->where('destination_id', $destination_hub_id)->where('status', 1);
                //dd($assigned_hubs);
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

                        $pieces_sum = collect($bag->shipment->pluck('shipment')->toArray())->sum('pieces');
                        $details['pieces_count'] = $pieces_sum;

                        $draft_bags = CargoManifestDraftBags::where('added_by', Auth::id())->get();
                        $bag_destinations = array();
                        if (count($draft_bags) > 0) {
                            foreach ($draft_bags as $draft_bag) {
                                if (!in_array($draft_bag->destination_id, $bag_destinations)) {
                                    $bag_destinations[] = $draft_bag->destination_id;
                                }
                            }
                        }
                        if (is_array($bag_destinations)) {
                            if (!in_array($bag->destination_hub_id, $bag_destinations)) {
                                $bag_destinations[] = $bag->destination_hub_id;
                            }
                        } else {
                            $bag_destinations[] = $bag->destination_hub_id;
                        }
                        $total_bags = CargoManifestBag::whereIn('destination_hub_id', $bag_destinations)->whereIn('status_id', [1, 3, 5])->where('current_hub_id', Auth::user()->default_hub_id)->count();
                        $details['bag_destinations'] = $bag_destinations;
                        $details['total_bags'] = $total_bags;


                        CargoManifestDraftBags::create(['bag_id' => $bag->id, 'seal_number' => $bag->seal_number, 'shipments_count' => $bag->shipments, 'origin_id' => $origin->id, 'destination_id' => $destination->id, 'added_by' => Auth::id(), 'weight' => $bag->shipments_weight, 'pieces_count' => $pieces_sum]);

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

    public function total_bags_info(Request $request)
    {
        $draft_bags = CargoManifestDraftBags::where('added_by', Auth::id())->get();
        $bag_destinations = array();
        $bag_ids = array();
        if (count($draft_bags) > 0) {
            foreach ($draft_bags as $draft_bag) {
                if (!in_array($draft_bag->destination_id, $bag_destinations)) {
                    $bag_destinations[] = $draft_bag->destination_id;
                }
                $bag_ids[] = $draft_bag->bag_id;
            }
        }
        $total_bags = CargoManifestBag::whereIn('destination_hub_id', $bag_destinations)->whereIn('status_id', [1, 3, 5])->where('current_hub_id', Auth::user()->default_hub_id)->orderBy('destination_hub_id')->get();
        $details = array();
        foreach ($total_bags as $total_bag) {
            $details[$total_bag->id]['seal_number'] = $total_bag->seal_number;
            $details[$total_bag->id]['origin'] = $total_bag->origin_hub->name;
            $details[$total_bag->id]['destination'] = $total_bag->destination_hub->name;
            $details[$total_bag->id]['status'] = $total_bag->status->name;

            if (in_array($total_bag->id, $bag_ids)) {
                $details[$total_bag->id]['class'] = 'bg-success white';
            } else {
                $details[$total_bag->id]['class'] = '';
            }
        }
        return ['status' => 0, 'details' => $details];
    }

    public function cargo_details(Request $request) // create manifest -> on popup fetch cargo details
    {
        $bags = CargoManifestBag::with('shipment', 'shipment.shipment')->whereIn('id', $request->bag_ids);

        if ($bags->exists() && $bags->count() == count($request->bag_ids)) {
            $bags = $bags->get();
            $origin_id = Auth::user()->default_hub_id;
            $assigned_hubs = array_merge(session('hubs'), [$origin_id]);
            $details = [];
            $pieces_sum = 0;
            $remarks = $request->remarks;
            foreach ($bags as $bag) {
                $cargo_draft_bag = CargoManifestDraftBags::where('bag_id', $bag->id);
                if ($cargo_draft_bag->exists()) {
                    $cargo_draft_bag = $cargo_draft_bag->first();
                    $remark = isset($remarks[$bag->id]) ? $remarks[$bag->id] : null;
                    $pieces_sum += $cargo_draft_bag->pieces_count;
                    $cargo_draft_bag->remarks_created_at = !empty($cargo_draft_bag->created_at) ? $cargo_draft_bag->created_at  : Carbon::now();
                    $cargo_draft_bag->remarks_added_by = !empty($cargo_draft_bag->remarks_added_by) ? $cargo_draft_bag->remarks_added_by  : Auth::id();
                    if ($cargo_draft_bag->remarks != $remark) {
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

                $mapping = V2JunctionMapping::whereIn('origin_id', $assigned_hubs)->where([['destination_id', $bag->destination_hub_id], ['status', 1]]);
                
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

    public function store_manifest(Request $request) //create manifest -> store
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
                $cargo_manifest_draft_bag = CargoManifestDraftBags::where('bag_id', $bag_id);
                if ($cargo_manifest_draft_bag->exists()) {
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
                $master_cargo->shipping_mode_id = 0;
                $master_cargo->transport_mode_id = 2;

                $master_cargo->bags = $bags;
                $master_cargo->shipments = $shipments;
                $master_cargo->bags_weight = $bags_weight;
                $master_cargo->actual_weight = $actual_weight;
                $master_cargo->created_by = Auth::id();

                $master_cargo->vehicle_seal_number = $request->vehicle_seal[$hub_id];

                if ($request->has('vehicle_type')) {
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
                        //shipment journey of intransit moved to manifest creation as per TO-6734 done by Najam Nazar
                        foreach ($bag->shipment as $shipment) {
                            $shipment_table = Shipment::find($shipment->shipment_id);
                            if ($bag->type == 1) {
                                $shipper_status_id = 3;
                                $consignee_status_id = 3;
                            } else {
                                $shipper_status_id = 21;
                                $consignee_status_id = 21;
            
                                if ($shipment_table->shipper_status_id != 20) {
                                    if ($shipment_table->booking_type_id == 1 || $shipment_table->booking_type_id == 4 || $shipment_table->booking_type_id == 5) {
                                        $shipper_status_id = 21;
                                        $consignee_status_id = 21;
                                    } else if ($shipment_table->booking_type_id == 2 && ($shipment_table->shipper_status_id == 30 || $shipment_table->shipper_status_id == 72 ) ) {
                                        $shipper_status_id = 26;
                                        $consignee_status_id = 26;
                                    } else if ($shipment_table->booking_type_id == 3 && ($shipment_table->shipper_status_id == 37 || $shipment_table->shipper_status_id == 69) ) {
                                        $shipper_status_id = 32;
                                        $consignee_status_id = 32;
                                    } else {
                                        $shipper_status_id = 21;
                                        $consignee_status_id = 21;
                                    }
                                }
                            }
            
                            $shipment_table->shipper_status_id = $shipper_status_id;
                            $shipment_table->consignee_status_id = $consignee_status_id;
            
                            $shipment_table->save();
                            ShipmentsJourneyController::add($shipment->shipment_id, $shipper_status_id, $consignee_status_id, NULL, NULL, NULL, Auth::id(), $bag->id);
                        }
                        $bag->status_id = 2;
                    } else if ($bag->status_id == 3) {
                        $bag->status_id = 4;
                    } else if ($bag->status_id == 5) {
                        $bag->status_id = 6;
                        foreach ($bag->shipment as $shipment) {
                            $shipment_table = Shipment::find($shipment->shipment_id);
                            if (in_array($shipment_table->shipper_status_id, [3, 21, 26, 32, 11, 69, 72,75])) {
                                if($bag->type == 1) {
                                    $status = 49;
                                } elseif($bag->type == 2) {
                                    if($shipment_table->booking_type_id == 2 && $shipment_table->shipper_status_id == 72 ) {
                                        $status = 73;
                                    } elseif($shipment_table->booking_type_id == 3 &&  $shipment_table->shipper_status_id == 69) {
                                        $status = 70;
                                    } else {
                                        $status = 76;
                                    }
                                }

                                ShipmentsJourneyController::add($shipment->shipment_id, $status, $status, null, null, null, Auth::id(), $bag->seal_number);
                                $shipment_table->shipper_status_id = $status;
                                $shipment_table->consignee_status_id = $status;
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
                    }
                    $assigned_hubs = array_merge(session('hubs'), [Auth::user()->default_hub_id]);
                    if ($bag->junction_mapping_id == null) {
                        $mapping = V2JunctionMapping::whereIn('origin_id', $assigned_hubs)->where([['destination_id', $hub_id], ['status', 1]])->first();
                        $bag->junction_mapping_id = $mapping->id;
                        $master_cargo->junction_mapping_id = $mapping->id;
                        $master_cargo->update();
                    } else {
                        $master_cargo->junction_mapping_id = $bag->junction_mapping_id;
                        $master_cargo->update();
                    }
                    $bag->current_hub_id = Auth::user()->default_hub_id;
                    $bag->update();

                    CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), $master_cargo_id, 1, 3);
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
            //$manifest = CargoManifest::find($cargo_id);
            //NotificationsController::send(148, $manifest->destination_hub_id, url('/') . '/' . 'reports/cargo_manifest_' . str_pad($manifest->id, 6, '0', STR_PAD_LEFT) . '.pdf');
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

                if ($cargo->vehicle_seal_number != null) {
                    $html .= '<td class="color secondary"><strong>Vehicle Seal Number</strong></td>
                            <td>' . $cargo->vehicle_seal_number . '</td>';
                } else {
                    $html .= '<td colspan="2"></td>';
                }

                $html .= '</tr>
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
                    // $pdf = SnappyPDF::loadHTML($html)->save('reports/cargo_manifest_' . str_pad($cargo->id, 6, '0', STR_PAD_LEFT) . '.pdf');
                    // return $pdf;
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
        $hubs = City::where('status', '1')->where('business_category_id', '1')->where('hub', '1')->select('id', 'name')->get();

        return view('admin.cargo.manifest.index')->with(['shipping_mode' => $shipping_mode, 'bag_status' => $bag_status, 'hubs' => $hubs]);
    }

    public function manifest_list(Request $request)
    {

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 405);
        }
        $bags = DB::connection('reports')->table('cargo_manifest_bags')->leftjoin('manifest_bags as mcb', function ($join) {
            $join->on('mcb.cargo_manifest_bag_id', '=', 'cargo_manifest_bags.id')
                ->where(
                    'mcb.id',
                    '=',
                    DB::raw('(select max(id) from manifest_bags where manifest_bags.cargo_manifest_bag_id = cargo_manifest_bags.id)')
                );
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
            ->select(
                'cargo_manifest_bags.id',
                'cargo_manifest_bags.status_id as status_id',
                'oh.id as origin_id',
                'oh.name as origin',
                'dh.id as destination_id',
                'dh.name as destination',
                'cargo_manifest_bags.shipments',
                'cargo_manifest_bags.quantity',
                'tm.name as transport_mode',
                'cargo_manifest_bags.shipments_weight',
                'cargo_manifest_bags.actual_weight',
                'cargo_manifest_bags.shipments as bag_shipments',
                'a.name as transitted_by',
                'oh.hub_id as origin_hub_id',
                'dh.hub_id as destination_hub_id',
                'cargo_manifest_bags.type as bag_type',
                'cargo_manifest_bags.seal_number',
                'bs.name as status',
                'sm.mode as shipping_mode',
                'cm.id as manifest_id',
                'cargo_manifest_bags.seal_number',
                'cm.created_at as manifest_created_at',
                'cargo_manifest_bags.origin_hub_id as origin_hub_id',
                'cargo_manifest_bags.destination_hub_id as destination_hub_id',
                'cm.id as manifest',
                'ah.name as updated_by',
                'cargo_manifest_bags.created_at as transitted_date',
                'cargo_manifest_bags.short_received_shipments as short_received_shipments',
                'cargo_manifest_bags.short_received_shipments as short_shipments',
                'cargo_manifest_bags.received_shipments',
                'cmbj.created_at as status_updated_at',
                'status_editor.name as status_updated_by',
                'sedh.name as status_location',
                DB::raw('(select count(id) from cargo_manifest_bag_remarks where bag_id = cargo_manifest_bags.id) as remarks')
            )
            ->where(function ($query) {
                $query->where('cargo_manifest_bags.shipments', '!=', DB::raw('(select(received_shipments) from cargo_manifest_bags as cmb where cmb.id =cargo_manifest_bags.id)'))
                    ->orWhere('cargo_manifest_bags.completed', 0);
            });


        if ($tracking_number = $request->get('tracking_number')) {
            $bags->join('cargo_manifest_bag_shipments as bssh', 'cargo_manifest_bags.id', '=', 'bssh.cargo_manifest_bag_id')
                ->join('shipments as s', 'bssh.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
        }

        if ($bag_number = $request->get('bag_number')) {
            $bags->where('cargo_manifest_bags.seal_number', '=', $bag_number);
        }
        if ($vehicle_number = $request->get('vehicle_number')) {
            /*$datatables->where('cm.vehicle_id', '=', $vehicle_number);*/
            $fleet = Fleet::where('reg_number', $vehicle_number)->first();
            if ($fleet) {
                $bags->where('cm.vehicle_id', 'like', '%' . $fleet->id . '%');
            } else {
                $bags->where('cm.vehicle_number', 'like', '%' . $vehicle_number . '%');
            }
        }

        if ($manifest_id = $request->get('manifest_number')) {
            $bags->where('cm.id', '=', $manifest_id);
        }

        if ($request->get('search_date_from') != null && $request->get('search_date_to') != null) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $stop_date = Carbon::createFromFormat('Y-m-d', $to)->endOfDay()->toDateTimeString();
            $bags->whereBetween('cmbj.created_at', [$from, $stop_date]);
        }

        if ($search_origin = $request->get('search_origin')) {
            $bags = $bags->where('oh.id', '=', $search_origin);
        }

        if ($search_destination = $request->get('search_destination')) {
            $bags = $bags->where('dh.id', '=', $search_destination);
        }

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
                        if ($fleet) {
                            $vehicle_data .= "<button class='btn btn-sm btn-outline-info align-middle'>" . $fleet->reg_number . "</button>";
                        }
                    } else {
                        $vehicle_data .= "<button class='btn btn-sm btn-outline-info align-middle'>" . $manifest->vehicle_number . "</button>";
                    }
                }
                return $vehicle_data;
            })
            ->addColumn('remarks', function ($remarks) {
                if (!empty($remarks->remarks)) {
                    return "<button ref='$remarks->id' class='btn btn-sm btn-outline-info align-middle remarks'>" . $remarks->remarks . "</button>";
                } else {
                    return  '-';
                }
            })

            ->addColumn('remarks_excel', function ($remarks) {
                if (!empty($remarks->remarks)) {
                    return $remarks->remarks;
                } else {
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
            })
            ->rawColumns(['vehicles','junctions','short_received_shipments','bag_shipments','manifest_id','action', 'remarks']);
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

    public function receive_bag_index_old()
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

        return view('admin.cargo.manifest.receive_old', compact('total'));
    }

    public function receive_bag_details(Request $request) // receive bag -> scanne bag no
    {
        //        $bag = CargoManifestBag::where('seal_number', $request->bag_number);
        //
        //        if ($bag->exists()) {
        //            $bag = $bag->whereIn('status_id', $this->bag_can_be_received_statuses);
        //            if ($bag->exists()) {
        //                $bag = $bag->latest()->first();
        //
        //                ShipmentScanningJourneyController::seal_number_add($bag->id, 2, Auth::id());
        //
        //                $cargo_bag = CargoManifest::leftjoin('manifest_bags as mb', function ($join) use ($bag) {
        //                    $join->on('mb.cargo_manifest_id', 'cargo_manifests.id');
        //                })
        //                    ->select(['cargo_manifests.*', 'mb.cargo_manifest_bag_id'])
        //                    ->where('cargo_manifests.status_id', 1)
        //                    ->where('mb.cargo_manifest_bag_id', $bag->id);
        //
        //                if ($cargo_bag->exists()) {
        //                    $cargo_bag = $cargo_bag->latest()->first();
        //
        //                    $mapping = V2JunctionMapping::where('id', $bag->junction_mapping_id);
        //
        //                    if ($mapping->exists()) {
        //                        $mapping = $mapping->first();
        //                        $misroute = 1;
        //                        $last_junction = "-";
        //                        if (in_array($mapping->destination_id, session('hubs'))) {
        //                            $misroute = 0;
        //                            $last_junction = $mapping->junctions->sortByDesc('id')->first()->city->name ?? "-";
        //                        }
        //                        if ($misroute == 1) {
        //                            $previous_junction = "-";
        //                            foreach ($mapping->junctions as $junction) {
        //                                if (in_array($junction->junction_id, session('hubs'))) {
        //                                    $misroute = 0;
        //                                    $last_junction = $previous_junction;
        //                                    break;
        //                                }
        //
        //                                $previous_junction = $junction->city->name;
        //                            }
        //                        }
        //
        //                        $details = array();
        //
        //                        $details['misroute'] = $misroute;
        //                        $details['bag_id'] = $bag->id;
        //                        $details['bag_number'] = $request->bag_number;
        //                        $details['manifest_id'] = str_pad($cargo_bag->id, 6, '0', STR_PAD_LEFT);
        //                        $details['origin'] = $cargo_bag->origin_hub->name;
        //                        $details['destination'] = $cargo_bag->destination_hub->name;
        //                        $details['last_junction'] = $last_junction;
        //                        $details['actual_weight'] = $bag->actual_weight;
        //                        $details['shipping_mode'] = $cargo_bag->shipping_mode->mode;
        //
        //                        return ['status' => 0, 'success' => 'Bag has been added', 'details' => $details];
        //                    } else {
        //                        return ['status' => 1, 'error' => 'Bag Number is not associated with any mapping'];
        //                    }
        //
        //                } else {
        //                    return ['status' => 1, 'error' => 'Given Bag Number is not in any Cargo Manifest'];
        //                }
        //            } else {
        //                return ['status' => 1, 'error' => 'Given Bag Number is already received or created'];
        //            }
        //        } else {
        //            return ['status' => 1, 'error' => 'No Bag with given Bag Number is present'];
        //        }
        $bag_manifest_exists = 1;
        $admin_default_hub_id = Auth::user()->default_hub_id;


        $bag = CargoManifestBag::where('seal_number', $request->bag_number);
        if ($bag->exists()) {
            $bag = $bag->whereIn('status_id', $this->bag_can_be_received_statuses);
            if ($bag->exists()) {
                $bag = $bag->latest()->first();

                ShipmentScanningJourneyController::seal_number_add($bag->id, 2, Auth::id(), $request->action);

                $cargo_bag = CargoManifest::leftjoin('manifest_bags as mb', function ($join) use ($bag) {
                    $join->on('mb.cargo_manifest_id', 'cargo_manifests.id');
                })
                    ->select(['cargo_manifests.*', 'mb.cargo_manifest_bag_id'])
                    ->where('cargo_manifests.status_id', 1)
                    ->where('mb.cargo_manifest_bag_id', $bag->id);

                if (!$cargo_bag->exists()) {
                    $bag_manifest_exists = 0;
                }

                if ($bag_manifest_exists) {
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
                    } else {
                        $last_junction = "-";
                        if ($bag->destination_hub_id == $admin_default_hub_id)
                            $misroute = 0;
                        else
                            $misroute = 1;
                    }
                    $details = array();

                    $bag_return_type = '';
                    if ($bag->type == 1){
                        $bag_return_type = "Normal";
                    } elseif ($bag->type == 2) {
                        $bag_return_type = "Return";
                    }

                    $details['misroute'] = $misroute;
                    $details['bag_id'] = $bag->id;
                    $details['bag_number'] = $request->bag_number;
                    $details['manifest_id'] = str_pad($cargo_bag->id, 6, '0', STR_PAD_LEFT);
                    $details['bag_type'] = $bag_return_type;
                    $details['origin'] = $cargo_bag->origin_hub->name;
                    $details['destination'] = $cargo_bag->destination_hub->name;
                    $details['last_junction'] = $last_junction;
                    $details['actual_weight'] = $bag->actual_weight;
                    // $details['shipping_mode'] = $cargo_bag->shipping_mode->mode;
                    $details['without_manifest'] = 0;

                    return ['status' => 0, 'success' => 'Bag has been added', 'details' => $details];
                } else {
                    $bag_origin = $bag->origin_hub->name;
                    $bag_dest = $bag->destination_hub->name;
                    $cargo_bag = '-';
                    $last_junction = "-";
                    $mapping = V2JunctionMapping::where('id', $bag->junction_mapping_id);

                    if ($mapping->exists()) {
                        $mapping = $mapping->first();
                        $misroute = 1;

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
                    } else {
                        if (($bag->destination_hub_id == $admin_default_hub_id))
                            $misroute = 0;
                        else
                            $misroute = 1;
                    }

                    $details = array();

                    $bag_return_type = '';
                    if ($bag->type == 1){
                        $bag_return_type = "Normal";
                    } elseif ($bag->type == 2) {
                        $bag_return_type = "Return";
                    }

                    $details['misroute'] = $misroute;
                    $details['bag_id'] = $bag->id;
                    $details['bag_number'] = $request->bag_number;
                    $details['manifest_id'] = 'Without Manifest';
                    $details['bag_type'] = $bag_return_type;
                    $details['origin'] = $bag_origin . ' (Without Manifest)';
                    $details['destination'] = $bag_dest . ' (Without Manifest)';
                    $details['last_junction'] = $last_junction;
                    $details['actual_weight'] = $bag->actual_weight;
                    $details['shipping_mode'] = 'Without Manifest';
                    $details['without_manifest'] = 1;

                    return ['status' => 0, 'success' => 'Bag has been added', 'details' => $details];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Bag Number is already received or created'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Bag with given Bag Number is present'];
        }
    }


    public function receive_bag_details_old(Request $request)
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

    public function receive_bag_store(Request $request) // receive bag -> store
    {
        //        $bag_exists = array();
        //        $bag_misroute = array();
        //        $bag_not_exists = array();
        //        $bag_not_exists_in_manifest = array();
        //        $bag_not_exists_in_mapping = array();
        //        $bag_short_received = array();
        //        $request_bag_ids = explode(',', $request->bag_ids);
        //        foreach ($request_bag_ids as $bag_id) {
        //            $bag = CargoManifestBag::where('id', $bag_id)
        //                ->whereIn('status_id', $this->bag_can_be_received_statuses);
        //
        //            if ($bag->exists()) {
        //                $bag = $bag->latest()->first();
        //                $cargo_bag = CargoManifest::leftjoin('manifest_bags as mb', function ($join) use ($bag) {
        //                    $join->on('mb.cargo_manifest_id', 'cargo_manifests.id');
        //                })
        //                    ->select(['cargo_manifests.*'])
        //                    ->where('cargo_manifests.status_id', 1)
        //                    ->where('mb.cargo_manifest_bag_id', $bag->id);
        //
        //                if ($cargo_bag->exists()) {
        //                    $cargo_bag = $cargo_bag->latest()->first();
        //                    $mapping = V2JunctionMapping::where('id', $bag->junction_mapping_id);
        //
        //                    if ($mapping->exists()) {
        //                        $mapping = $mapping->first();
        //                        $misroute = 1;
        //                        if (in_array($mapping->destination_id, session('hubs'))) {
        //                            $misroute = 0;
        //                            $bag->status_id = 7;
        //                            $bag->short_received_shipments = 0;
        //                        }
        //                        if ($misroute == 1) {
        //                            foreach ($mapping->junctions as $junction) {
        //                                if (in_array($junction->junction_id, session('hubs'))) {
        //                                    $misroute = 0;
        //                                    $bag->status_id = 3;
        //                                }
        //                            }
        //                        }
        //
        //                        if ($misroute == 1) {
        //                            $short_received_count = 0;
        //                            $received_count = 0;
        //                            $bag->status_id = 5;
        //                            $bag->junction_mapping_id = null;
        //                            foreach ($bag->shipment as $shipment) {
        //                                $shipment_table = Shipment::find($shipment->shipment_id);
        //                                if (in_array($shipment_table->shipper_status_id, [3, 21, 26, 32, 49])) {
        //                                    ShipmentsJourneyController::add($shipment->shipment_id, 11, 11, null, null, null, Auth::id(), $bag->seal_number);
        //                                    $shipment_table->shipper_status_id = 11;
        //                                    $shipment_table->consignee_status_id = 11;
        //                                    $shipment_table->update();
        //                                    $short_received_count++;
        //                                } else {
        //                                    $received_count++;
        //                                }
        //                            }
        //
        //                            $bag->short_received_shipments = $short_received_count;
        //                            $bag->received_shipments = $received_count;
        //                        }
        //                        $bag->current_hub_id = Auth::user()->default_hub_id;
        //                        $bag->updated_by = Auth::id();
        //                        $bag->update();
        //
        //                        ManifestBag::where('cargo_manifest_bag_id', $bag->id)
        //                            ->where('cargo_manifest_id', $cargo_bag->id)->update(['status' => 1]);
        //
        //                        if ($misroute == 0) {
        //                            array_push($bag_exists, $bag->seal_number);
        //                        } else {
        //                            array_push($bag_misroute, $bag->seal_number);
        //                        }
        //                        CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), NULL, NULL, 4);
        //                    } else {
        //                        array_push($bag_not_exists_in_mapping, $bag_id);
        //                    }
        //                } else {
        //                    array_push($bag_not_exists_in_manifest, $bag_id);
        //                }
        //            } else {
        //                array_push($bag_not_exists, $bag_id);
        //            }
        //        }
        //        foreach ($bag_exists as $bag_id) {
        //            $bag = CargoManifestBag::where('seal_number', $bag_id)->latest()->first();
        //            $cargo_bag = CargoManifest::leftjoin('manifest_bags as mb', function ($join) use ($bag) {
        //                $join->on('mb.cargo_manifest_id', 'cargo_manifests.id');
        //            })
        //                ->select(['cargo_manifests.*', 'mb.cargo_manifest_bag_id'])
        //                ->where('cargo_manifests.status_id', 1)
        //                ->where('mb.cargo_manifest_bag_id', $bag->id);
        //
        //            if ($cargo_bag->exists()) {
        //                $cargo_bag = $cargo_bag->latest()->first();
        //                $manifest_bags = ManifestBag::where('cargo_manifest_id', $cargo_bag->id)->where('status', 0)->get();
        //                $bag_short_received_count = 0;
        //                $cargo_short_received = array();
        //                foreach ($manifest_bags as $manifest_bag) {
        //                    if ($manifest_bag->status == 0) {
        //                        if (!in_array($manifest_bag->cargo_manifest_bag_id, $bag_short_received)) {
        //                            $short_received_bag = CargoManifestBag::find($manifest_bag->cargo_manifest_bag_id);
        //                            $short_received_bag->status_id = 9;
        //                            $bag->current_hub_id = Auth::user()->default_hub_id;
        //                            $short_received_bag->update();
        //                            CargoManifestBagJourneyController::add($short_received_bag->id, $short_received_bag->seal_number, $short_received_bag->status_id, Auth::id(), NULL, NULL, 4);
        //                            $bag_short_received_count++;
        //                            array_push($bag_short_received, $manifest_bag->cargo_manifest_bag_id);
        //                            array_push($cargo_short_received, $short_received_bag->id);
        //                        } else {
        //                            $bag_short_received_count++;
        //                        }
        //                    }
        //                }
        //
        //                if ($bag_short_received_count == 0) {
        //                    CargoManifest::find($cargo_bag->id)->update(['status_id' => 2]);
        //                }
        //
        //                if (count($cargo_short_received) > 0) {
        //                    foreach ($cargo_short_received as $cargo_short) {
        //                        $bag_shipments = CargoManifestBagShipments::where('cargo_manifest_bag_id', $cargo_short)->get(['shipment_id']);
        //                        $shipments = array();
        //                        foreach ($bag_shipments as $shipment) {
        //                            array_push($shipments, $shipment->shipment_id);
        //                        }
        //
        //                        DisputeController::add_cargo_short_received($cargo_bag->id, $shipments, null, 1);
        //                    }
        //                }
        //            }
        //        }
        //        foreach ($bag_misroute as $bag_id) {
        //            $bag = CargoManifestBag::where('seal_number', $bag_id)->latest()->first();
        //            $cargo_bag = CargoManifest::leftjoin('manifest_bags as mb', function ($join) use ($bag) {
        //                $join->on('mb.cargo_manifest_id', 'cargo_manifests.id');
        //            })
        //                ->select(['cargo_manifests.*', 'mb.cargo_manifest_bag_id'])
        //                ->where('cargo_manifests.status_id', 1)
        //                ->where('mb.cargo_manifest_bag_id', $bag->id);
        //
        //            if ($cargo_bag->exists()) {
        //                $cargo_bag = $cargo_bag->latest()->first();
        //                $manifest_bags = ManifestBag::where('cargo_manifest_id', $cargo_bag->id)->where('status', 0)->get();
        //                $bag_short_received_count = 0;
        //                $cargo_short_received = array();
        //                foreach ($manifest_bags as $manifest_bag) {
        //                    if ($manifest_bag->status == 0) {
        //                        if (!in_array($manifest_bag->cargo_manifest_bag_id, $bag_short_received)) {
        //                            $short_received_bag = CargoManifestBag::find($manifest_bag->cargo_manifest_bag_id);
        //                            $short_received_bag->status_id = 9;
        //                            $bag->current_hub_id = Auth::user()->default_hub_id;
        //                            $bag->updated_by = Auth::id();
        //                            $short_received_bag->update();
        //                            CargoManifestBagJourneyController::add($short_received_bag->id, $short_received_bag->seal_number, $short_received_bag->status_id, Auth::id(), NULL, NULL, 4);
        //                            $bag_short_received_count++;
        //                            array_push($bag_short_received, $manifest_bag->cargo_manifest_bag_id);
        //                            array_push($cargo_short_received, $short_received_bag->id);
        //                        } else {
        //                            $bag_short_received_count++;
        //                        }
        //                    }
        //                }
        //
        //                if ($bag_short_received_count == 0) {
        //                    CargoManifest::find($cargo_bag->id)->update(['status_id' => 2]);
        //                }
        //
        //                if (count($cargo_short_received) > 0) {
        //                    foreach ($cargo_short_received as $cargo_short) {
        //                        $bag_shipments = CargoManifestBagShipments::where('cargo_manifest_bag_id', $cargo_short)->get(['shipment_id']);
        //                        $shipments = array();
        //                        foreach ($bag_shipments as $shipment) {
        //                            array_push($shipments, $shipment->shipment_id);
        //                        }
        //
        //                        DisputeController::add_cargo_short_received($cargo_bag->id, $shipments, null, 1);
        //                    }
        //                }
        //            }
        //        }
        //
        //        $success_html = false;
        //        $misroute_html = false;
        //        $bag_not_exists_error = false;
        //        $bag_not_exists_in_manifest_error = false;
        //        $bag_not_exists_in_mapping_error = false;
        //        $bag_short_received_error = false;
        //        if (count($bag_not_exists) > 0) {
        //            $bag_not_exists_error = "Following Bag(s) already received or doesn't exists.<br><ul>";
        //            foreach ($bag_not_exists as $v) {
        //                $bag_not_exists_error .= "<li>" . CargoManifestBag::find($v)->seal_number ?? $v . "</li>";
        //            }
        //            $bag_not_exists_error .= "</ul>";
        //        }
        //        if (count($bag_not_exists_in_manifest) > 0) {
        //            $bag_not_exists_in_manifest_error = "Following Bag(s) doesn't exists in any manifest.<br><ul>";
        //            foreach ($bag_not_exists_in_manifest as $v) {
        //                $bag_not_exists_in_manifest_error .= "<li>" . CargoManifestBag::find($v)->seal_number . "</li>";
        //            }
        //            $bag_not_exists_in_manifest_error .= "</ul>";
        //        }
        //        if (count($bag_not_exists_in_mapping) > 0) {
        //            $bag_not_exists_in_mapping_error = "Following Bag(s) doesn't associated with any mapping.<br><ul>";
        //            foreach ($bag_not_exists_in_mapping as $v) {
        //                $bag_not_exists_in_mapping_error .= "<li>" . CargoManifestBag::find($v)->seal_number . "</li>";
        //            }
        //            $bag_not_exists_in_mapping_error .= "</ul>";
        //        }
        //        if (count($bag_short_received) > 0) {
        //            $bag_short_received_error = "Following Bag(s) are short received.<br><ul>";
        //            foreach ($bag_short_received as $bag_id) {
        //                $bag_short_received_error .= "<li>" . CargoManifestBag::find($bag_id)->seal_number . "</li>";
        //            }
        //            $bag_short_received_error .= "</ul>";
        //        }
        //        if (count($bag_exists) > 0) {
        //            $success_html = "Following Bag(s) are received successfully.<br><ul>";
        //            foreach ($bag_exists as $v) {
        //                $success_html .= "<li>" . $v . "</li>";
        //            }
        //            $success_html .= "</ul>";
        //        }
        //        if (count($bag_misroute) > 0) {
        //            $misroute_html = "Following Bag(s) are received as misrouted successfully.<br><ul>";
        //            foreach ($bag_misroute as $v) {
        //                $misroute_html .= "<li>" . $v . "</li>";
        //            }
        //            $misroute_html .= "</ul>";
        //        }
        //
        //        return back()->with(['success_html' => $success_html, 'misroute_html' => $misroute_html, 'bag_short_received_error' => $bag_short_received_error, 'bag_not_exists_in_mapping_error' => $bag_not_exists_in_mapping_error, 'bag_not_exists_in_manifest_error' => $bag_not_exists_in_manifest_error, 'bag_not_exist_error' => $bag_not_exists_error]);

        try {
            DB::beginTransaction();

            $bag_exists = array();
            $bag_misroute = array();
            $bag_not_exists = array();
            $bag_not_exists_in_manifest = array();
            $bag_not_exists_in_mapping = array();
            $bag_short_received = array();
            $request_bag_ids = explode(',', $request->bag_ids);
            $admin_assigned_hubs = session('hubs');
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
                                    if (in_array($shipment_table->shipper_status_id, [3, 21, 26, 32, 49, 70, 73, 76,30,37,69,72,75])) {
                                        if($bag->type == 1) {
                                            $status = 11;
                                        } elseif($bag->type == 2) {
                                            if($shipment_table->booking_type_id == 2) {
                                                if(in_array($shipment_table->shipper_status_id, [26,30,72,73])) {
                                                    $status = 72;
                                                } else {
                                                    $status = 75;
                                                }
                                                
                                            } elseif($shipment_table->booking_type_id == 3) {
                                                if(in_array($shipment_table->shipper_status_id, [32,37,69,70])) {
                                                    $status = 69;
                                                } else {
                                                    $status = 75;
                                                }
                                            } else {
                                                $status = 75;
                                            }
                                        }
                                        
                                        ShipmentsJourneyController::add($shipment->shipment_id, $status, $status, null, null, null, Auth::id(), $bag->seal_number);
                                        $shipment_table->shipper_status_id = $status;
                                        $shipment_table->consignee_status_id = $status;

                                        $shipment_table->update();
                                        $short_received_count++;
                                    } else {
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
                            CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), NULL, NULL, 4);
                        } else {
                            array_push($bag_not_exists_in_mapping, $bag_id);
                        }
                    } else {
                        //array_push($bag_not_exists_in_manifest, $bag_id);

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
                                    if (in_array($shipment_table->shipper_status_id, [3, 21, 26, 32, 49, 70, 73, 76,30,37,69,72,75])) {
                                        if($bag->type == 1) {
                                            $status = 11;
                                        } elseif($bag->type == 2) {
                                            if($shipment_table->booking_type_id == 2) {
                                                if(in_array($shipment_table->shipper_status_id, [26,30,72,73])) {
                                                    $status = 72;
                                                } else {
                                                    $status = 75;
                                                }
                                                
                                            } elseif($shipment_table->booking_type_id == 3) {
                                                if(in_array($shipment_table->shipper_status_id, [32,37,69,70])) {
                                                    $status = 69;
                                                } else {
                                                    $status = 75;
                                                }
                                            } else {
                                                $status = 75;
                                            }
                                        }
                                        ShipmentsJourneyController::add($shipment->shipment_id, $status, $status, null, null, null, Auth::id(), $bag->seal_number);
                                        $shipment_table->shipper_status_id = $status;
                                        $shipment_table->consignee_status_id = $status;
                                        $shipment_table->update();
                                        $short_received_count++;

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
                                    } else {
                                        $received_count++;
                                    }
                                }

                                $bag->short_received_shipments = $short_received_count;
                                $bag->received_shipments = $received_count;
                            }
                            $bag->current_hub_id = Auth::user()->default_hub_id;
                            $bag->updated_by = Auth::id();
                            $bag->update();

                            if ($misroute == 0) {
                                array_push($bag_exists, $bag->seal_number);
                            } else {
                                array_push($bag_misroute, $bag->seal_number);
                            }
                            CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), NULL, NULL, 4);
                        } else {
                            //array_push($bag_not_exists_in_mapping, $bag_id);

                            $misroute = 1;
                            if (($bag->destination_hub_id == Auth::user()->default_hub_id) || (in_array($bag->destination_hub_id,$admin_assigned_hubs))) {
                                $misroute = 0;
                                $bag->status_id = 7;
                                $bag->short_received_shipments = 0;
                            }

                            // maybe ik our condition aegi yahan p
                            //bag type k behalf p bag ki origin fetch krke admin's default hub id check krke received ya misroute decide krna h

                            if ($misroute == 1) {
                                $short_received_count = 0;
                                $received_count = 0;
                                $bag->status_id = 5;
                                $bag->junction_mapping_id = null;
                                foreach ($bag->shipment as $shipment) {
                                    $shipment_table = Shipment::find($shipment->shipment_id);
                                    if (in_array($shipment_table->shipper_status_id, [3, 21, 26, 32, 49, 70, 73, 76,30,37,69,72,75])) {
                                        if($bag->type == 1) {
                                            $status = 11;
                                        } elseif($bag->type == 2) {
                                            //$status = 75;
                                            if($shipment_table->booking_type_id == 2) {
                                                if(in_array($shipment_table->shipper_status_id, [26,30,72,73])) {
                                                    $status = 72;
                                                } else {
                                                    $status = 75;
                                                }
                                                
                                            } elseif($shipment_table->booking_type_id == 3) {
                                                if(in_array($shipment_table->shipper_status_id, [32,37,69,70])) {
                                                    $status = 69;
                                                } else {
                                                    $status = 75;
                                                }
                                            } else {
                                                $status = 75;
                                            }
                                        }
                                        ShipmentsJourneyController::add($shipment->shipment_id,  $status,  $status, null, null, null, Auth::id(), $bag->seal_number);
                                        $shipment_table->shipper_status_id =  $status;
                                        $shipment_table->consignee_status_id =  $status;
                                        $shipment_table->update();
                                        $short_received_count++;

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
                                    } else {
                                        $received_count++;
                                    }
                                }

                                $bag->short_received_shipments = $short_received_count;
                                $bag->received_shipments = $received_count;
                            }
                            $bag->current_hub_id = Auth::user()->default_hub_id;
                            $bag->updated_by = Auth::id();
                            $bag->update();

                            if ($misroute == 0) {
                                array_push($bag_exists, $bag->seal_number);
                            } else {
                                array_push($bag_misroute, $bag->seal_number);
                            }
                            CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), NULL, NULL, 4);
                        }
                    }
                } else {
                    array_push($bag_not_exists, $bag_id);
                }
            }

            foreach ($bag_exists as $bag_id) { // short received patch

                $bag = CargoManifestBag::where('seal_number', $bag_id)->latest()->first();
                $cargo_bag = CargoManifest::leftjoin('manifest_bags as mb', function ($join) use ($bag) {
                    $join->on('mb.cargo_manifest_id', 'cargo_manifests.id');
                })
                    ->select(['cargo_manifests.*', 'mb.cargo_manifest_bag_id'])
                    ->where('cargo_manifests.status_id', 1)
                    ->where('mb.cargo_manifest_bag_id', $bag->id);

                if ($cargo_bag->exists()) {
                    $cargo_bag = $cargo_bag->latest()->first();
                    $manifest_bags = ManifestBag::where('cargo_manifest_id', $cargo_bag->id)->where('status', 0)->get();
                    $bag_short_received_count = 0;
                    $cargo_short_received = array();
                    foreach ($manifest_bags as $manifest_bag) {
                        if ($manifest_bag->status == 0) {
                            if (!in_array($manifest_bag->cargo_manifest_bag_id, $bag_short_received)) {
                                $short_received_bag = CargoManifestBag::find($manifest_bag->cargo_manifest_bag_id);
                                $short_received_bag->status_id = 9;
                                $bag->current_hub_id = Auth::user()->default_hub_id;
                                $short_received_bag->update();
                                CargoManifestBagJourneyController::add($short_received_bag->id, $short_received_bag->seal_number, $short_received_bag->status_id, Auth::id(), NULL, NULL, 4);
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
                    $manifest_bags = ManifestBag::where('cargo_manifest_id', $cargo_bag->id)->where('status', 0)->get();
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
                                CargoManifestBagJourneyController::add($short_received_bag->id, $short_received_bag->seal_number, $short_received_bag->status_id, Auth::id(), NULL, NULL, 4);
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

            DB::commit();
            return back()->with(['success_html' => $success_html, 'misroute_html' => $misroute_html, 'bag_short_received_error' => $bag_short_received_error, 'bag_not_exists_in_mapping_error' => $bag_not_exists_in_mapping_error, 'bag_not_exists_in_manifest_error' => $bag_not_exists_in_manifest_error, 'bag_not_exist_error' => $bag_not_exists_error]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with(['went_wrong_html' => 'Something Went Wrong !']);
        }
    }

    public function receive_bag_store_old(Request $request)
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
                                if (in_array($shipment_table->shipper_status_id, [3, 21, 26, 32, 49])) {
                                    ShipmentsJourneyController::add($shipment->shipment_id, 11, 11, null, null, null, Auth::id(), $bag->seal_number);
                                    $shipment_table->shipper_status_id = 11;
                                    $shipment_table->consignee_status_id = 11;
                                    $shipment_table->update();
                                    $short_received_count++;
                                } else {
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
                        CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, Auth::id(), NULL, NULL, 4);
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
                $manifest_bags = ManifestBag::where('cargo_manifest_id', $cargo_bag->id)->where('status', 0)->get();
                $bag_short_received_count = 0;
                $cargo_short_received = array();
                foreach ($manifest_bags as $manifest_bag) {
                    if ($manifest_bag->status == 0) {
                        if (!in_array($manifest_bag->cargo_manifest_bag_id, $bag_short_received)) {
                            $short_received_bag = CargoManifestBag::find($manifest_bag->cargo_manifest_bag_id);
                            $short_received_bag->status_id = 9;
                            $bag->current_hub_id = Auth::user()->default_hub_id;
                            $short_received_bag->update();
                            CargoManifestBagJourneyController::add($short_received_bag->id, $short_received_bag->seal_number, $short_received_bag->status_id, Auth::id(), NULL, NULL, 4);
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
                $manifest_bags = ManifestBag::where('cargo_manifest_id', $cargo_bag->id)->where('status', 0)->get();
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
                            CargoManifestBagJourneyController::add($short_received_bag->id, $short_received_bag->seal_number, $short_received_bag->status_id, Auth::id(), NULL, NULL, 4);
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
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 410);
        }
        $receive_cargo = CargoManifest::join('cities as oh', 'cargo_manifests.origin_hub_id', '=', 'oh.id')
            ->join('cities as dh', 'cargo_manifests.destination_hub_id', '=', 'dh.id')
            ->leftJoin('shipping_modes as sm', 'cargo_manifests.shipping_mode_id', '=', 'sm.id')
            ->join('admins as a', 'cargo_manifests.created_by', '=', 'a.id')
            ->leftjoin('fleets as f', 'cargo_manifests.vehicle_id', '=', 'f.id')
            ->leftjoin('transport_modes as tm', 'cargo_manifests.transport_mode_id', '=', 'tm.id')

            ->leftJoin('manifest_bags', 'manifest_bags.cargo_manifest_id', '=', 'cargo_manifests.id')
            ->leftJoin('cargo_manifest_bags', 'cargo_manifest_bags.id', '=', 'manifest_bags.cargo_manifest_bag_id')
            ->leftJoin('cargo_manifest_bag_shipments as cargo_shipments', 'cargo_shipments.cargo_manifest_bag_id', '=', 'cargo_manifest_bags.id')
            ->leftJoin('shipments', 'shipments.id', '=', 'cargo_shipments.shipment_id')
            ->leftJoin('shipper_segment_logs', 'shipper_segment_logs.shipment_id', '=', 'cargo_shipments.shipment_id')
            ->leftJoin('sub_category_segments as sub_segment', 'shipper_segment_logs.sub_segment_id', '=', 'sub_segment.id')

            ->select(
                'cargo_manifests.id as manifest_id',
                'cargo_manifests.status_id',
                'oh.id as origin_id',
                'oh.name as origin',
                'dh.id as destination_id',
                'dh.name as destination',
                'cargo_manifests.shipments',
                'cargo_manifests.bags',
                'cargo_manifests.driver_name',
                'f.reg_number as vehicle',
                'cargo_manifests.driver_phone',
                'sm.mode as shipping_mode',
                'tm.name as transport_mode',
                'cargo_manifests.bags_weight',
                'cargo_manifests.actual_weight',
                'cargo_manifests.created_at as transit_at',
                'a.name as transitted_by',
                'oh.hub_id as origin_hub_id',
                'dh.hub_id as destination_hub_id',
                'cargo_manifests.vendor_name as vendor',
                'cargo_manifests.driver_phone as phone_number',
                'cargo_manifests.status_id as status',
                'cargo_manifests.id as manifest',
                'cargo_manifests.short_received_bags as short_received_bags',
                DB::raw('GROUP_CONCAT(sub_segment.name) as segment_names'),
                DB::raw('COUNT(DISTINCT sub_segment.name) as segment_count'),
                DB::raw('GROUP_CONCAT(shipments.actual_weight) as segment_weights'),
            )
            ->groupBy('cargo_manifests.id');

        if (($request->tracking_number != null && $request->tracking_number != '') || $request->bag_number != null && $request->bag_number != '') {
            $receive_cargo->join('manifest_bags as mb', 'cargo_manifests.id', '=', 'mb.cargo_manifest_id')
                ->join('cargo_manifest_bags as b', 'b.id', '=', 'mb.cargo_manifest_bag_id');

            if ($tracking_number = $request->get('tracking_number')) {
                $receive_cargo->join('cargo_manifest_bag_shipments as bs', 'b.id', '=', 'bs.cargo_manifest_bag_id')
                    ->join('shipments as s', 'bs.shipment_id', '=', 's.id')
                    ->where('s.tracking_number', '=', $tracking_number);
            }

            if ($bag_number = $request->get('bag_number')) {
                $receive_cargo->where('b.seal_number', '=', $bag_number);
            }
        }

        if ($request->get('transit_from_date') && $request->get('transit_to_date')) {
            $from = $request->get('transit_from_date');
            $to = $request->get('transit_to_date');

            // $stop_date = date('Y-m-d H:i:s', strtotime($to . ' +1 day'));
            $receive_cargo->whereBetween('cargo_manifests.created_at', [$from, $to]);
        }
        if (($request->search_filter_origin != null) && ($request->search_filter_destination != null)) {
            $origin = $request->get('search_filter_origin');
            $destination = $request->get('search_filter_destination');
            $receive_cargo->where('oh.name', '=', $origin)
                ->where('dh.name', '=', $destination);
        }

        if ($request->search_filter_origin != null) {
            $origin = $request->get('search_filter_origin');
            $receive_cargo->where('oh.name', '=', $origin);
        }
        if ($request->search_filter_destination != null) {
            $destination = $request->get('search_filter_destination');
            $receive_cargo->where('dh.name', '=', $destination);
        }

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
                        if ($fleet) {
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
            })
            ->addColumn('rush_shipment_count', function ($master_cargo) {
                $shipping_mode = 1; // rush
                $manifest_id = $master_cargo->manifest_id;
                $manifest_bag = CargoManifest::find($manifest_id);
                $all_bags = $manifest_bag->manifest_bags->pluck('cargo_manifest_bag_id');

                $shipments = CargoManifestBagShipments::whereIn('cargo_manifest_bag_id',$all_bags)->pluck('shipment_id');
                $shipment_mode_count = Shipment::whereIn('id',$shipments)->where('shipping_mode_id',$shipping_mode)->count();

                if($shipment_mode_count > 0)
                    return $shipment_mode_count;
                else
                    return '--';
            })
            ->addColumn('rush_shipment_weight', function ($master_cargo) {
                $shipping_mode = 1; // rush
                $manifest_id = $master_cargo->manifest_id;
                $manifest_bag = CargoManifest::find($manifest_id);
                $all_bags = $manifest_bag->manifest_bags->pluck('cargo_manifest_bag_id');

                $shipments = CargoManifestBagShipments::whereIn('cargo_manifest_bag_id',$all_bags)->pluck('shipment_id');

                $shipment_mode_count = Shipment::whereIn('id',$shipments)->where('shipping_mode_id',$shipping_mode)->sum('actual_weight');

                if($shipment_mode_count > 0)
                    return $shipment_mode_count;
                else
                    return '--';
            })
            ->addColumn('swift_shipment_count', function ($master_cargo) {
                $shipping_mode = 3; // swift
                $manifest_id = $master_cargo->manifest_id;
                $manifest_bag = CargoManifest::find($manifest_id);
                $all_bags = $manifest_bag->manifest_bags->pluck('cargo_manifest_bag_id');

                $shipments = CargoManifestBagShipments::whereIn('cargo_manifest_bag_id',$all_bags)->pluck('shipment_id');
                $shipment_mode_count = Shipment::whereIn('id',$shipments)->where('shipping_mode_id',$shipping_mode)->count();

                if($shipment_mode_count > 0)
                    return $shipment_mode_count;
                else
                    return '--';
            })
            ->addColumn('swift_shipment_weight', function ($master_cargo) {
                $shipping_mode = 3; // Swift
                $manifest_id = $master_cargo->manifest_id;
                $manifest_bag = CargoManifest::find($manifest_id);
                $all_bags = $manifest_bag->manifest_bags->pluck('cargo_manifest_bag_id');

                $shipments = CargoManifestBagShipments::whereIn('cargo_manifest_bag_id',$all_bags)->pluck('shipment_id');

                $shipment_mode_count = Shipment::whereIn('id',$shipments)->where('shipping_mode_id',$shipping_mode)->sum('actual_weight');

                if($shipment_mode_count > 0)
                    return $shipment_mode_count;
                else
                    return '--';
            })
            ->addColumn('saver_shipment_count', function ($master_cargo) {
                $shipping_mode = 2; // saver
                $manifest_id = $master_cargo->manifest_id;
                $manifest_bag = CargoManifest::find($manifest_id);
                $all_bags = $manifest_bag->manifest_bags->pluck('cargo_manifest_bag_id');

                $shipments = CargoManifestBagShipments::whereIn('cargo_manifest_bag_id',$all_bags)->pluck('shipment_id');
                $shipment_mode_count = Shipment::whereIn('id',$shipments)->where('shipping_mode_id',$shipping_mode)->count();

                if($shipment_mode_count > 0)
                    return $shipment_mode_count;
                else
                    return '--';
            })
            ->addColumn('saver_shipment_weight', function ($master_cargo) {
                $shipping_mode = 2; // saver
                $manifest_id = $master_cargo->manifest_id;
                $manifest_bag = CargoManifest::find($manifest_id);
                $all_bags = $manifest_bag->manifest_bags->pluck('cargo_manifest_bag_id');

                $shipments = CargoManifestBagShipments::whereIn('cargo_manifest_bag_id',$all_bags)->pluck('shipment_id');

                $shipment_mode_count = Shipment::whereIn('id',$shipments)->where('shipping_mode_id',$shipping_mode)->sum('actual_weight');

                if($shipment_mode_count > 0)
                    return $shipment_mode_count;
                else
                    return '--';
            })
            

            ->editColumn('express_count', function ($row) {
                return substr_count($row->segment_names, 'Express');
            })
            ->editColumn('cod_count', function ($row) {
                return substr_count($row->segment_names, 'COD');
            })
            ->editColumn('logistics_count', function ($row) {
                return substr_count($row->segment_names, 'Logistics');
            })
            ->editColumn('warehouse_count', function ($row) {
                return substr_count($row->segment_names, 'Warehouse');
            })
            ->editColumn('international_count', function ($row) {
                return substr_count($row->segment_names, 'International');
            })
            ->editColumn('hyperlocal_count', function ($row) {
                return substr_count($row->segment_names, 'Hyperlocal');
            })
            ->editColumn('fod_count', function ($row) {
                return substr_count($row->segment_names, 'FOD');
            })
            ->editColumn('retail_count', function ($row) {
                return substr_count($row->segment_names, 'Retail');
            })
            

            ->editColumn('logistics_weight', function ($row) {
                $segmentNames = explode(',', $row->segment_names);
                $segmentWeights = explode(',', $row->segment_weights);
                $totalWeight = 0;
            
                foreach ($segmentNames as $index => $name) {
                    if (trim($name) === 'Logistics' && isset($segmentWeights[$index]) && !empty($segmentWeights[$index])) {
                        $totalWeight += (float)$segmentWeights[$index];
                    }
                }
                return $totalWeight;
            })
            ->editColumn('express_weight', function ($row) {
                $segmentNames = explode(',', $row->segment_names);
                $segmentWeights = explode(',', $row->segment_weights);
                $totalWeight = 0;
            
                foreach ($segmentNames as $index => $name) {
                    if (trim($name) === 'Express' && isset($segmentWeights[$index]) && !empty($segmentWeights[$index])) {
                        $totalWeight += (float)$segmentWeights[$index];
                    }
                }
                return $totalWeight;
            })
            ->editColumn('warehouse_weight', function ($row) {
                $segmentNames = explode(',', $row->segment_names);
                $segmentWeights = explode(',', $row->segment_weights);
                $totalWeight = 0;
            
                foreach ($segmentNames as $index => $name) {
                    if (trim($name) === 'Warehouse' && isset($segmentWeights[$index]) && !empty($segmentWeights[$index])) {
                        $totalWeight += (float)$segmentWeights[$index];
                    }
                }
                return $totalWeight;
            })
            ->editColumn('international_weight', function ($row) {
                $segmentNames = explode(',', $row->segment_names);
                $segmentWeights = explode(',', $row->segment_weights);
                $totalWeight = 0;
            
                foreach ($segmentNames as $index => $name) {
                    if (trim($name) === 'International' && isset($segmentWeights[$index]) && !empty($segmentWeights[$index])) {
                        $totalWeight += (float)$segmentWeights[$index];
                    }
                }
                return $totalWeight;
            })
            ->editColumn('cod_weight', function ($row) {
                $segmentNames = explode(',', $row->segment_names);
                $segmentWeights = explode(',', $row->segment_weights);
                $totalWeight = 0;
            
                foreach ($segmentNames as $index => $name) {
                    if (trim($name) === 'COD' && isset($segmentWeights[$index]) && !empty($segmentWeights[$index])) {
                        $totalWeight += (float)$segmentWeights[$index];
                    }
                }
                return $totalWeight;
            })
            ->editColumn('hyperlocal_weight', function ($row) {
                $segmentNames = explode(',', $row->segment_names);
                $segmentWeights = explode(',', $row->segment_weights);
                $totalWeight = 0;
            
                foreach ($segmentNames as $index => $name) {
                    if (trim($name) === 'Hyperlocal' && isset($segmentWeights[$index]) && !empty($segmentWeights[$index])) {
                        $totalWeight += (float)$segmentWeights[$index];
                    }
                }
                return $totalWeight;
            })
            ->editColumn('fod_weight', function ($row) {
                $segmentNames = explode(',', $row->segment_names);
                $segmentWeights = explode(',', $row->segment_weights);
                $totalWeight = 0;
            
                foreach ($segmentNames as $index => $name) {
                    if (trim($name) === 'FOD' && isset($segmentWeights[$index]) && !empty($segmentWeights[$index])) {
                        $totalWeight += (float)$segmentWeights[$index];
                    }
                }
                return $totalWeight;
            })
            ->editColumn('retail_weight', function ($row) {
                $segmentNames = explode(',', $row->segment_names);
                $segmentWeights = explode(',', $row->segment_weights);
                $totalWeight = 0;
            
                foreach ($segmentNames as $index => $name) {
                    if (trim($name) === 'Retail' && isset($segmentWeights[$index]) && !empty($segmentWeights[$index])) {
                        $totalWeight += (float)$segmentWeights[$index];
                    }
                }
                return $totalWeight;
            })
            ->rawColumns(['short_received_bags', 'bags','manifest_id','shipments']);
        return $datatables->make(true);
    }

    public function receive_bag_shipments_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 411);
        return view('admin.cargo.manifest.receive_shipments');
    }

    public function receive_bag_shipments_index_old()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 411);
        return view('admin.cargo.manifest.receive_shipments_old');
    }

    public function receive_bag_shipments_details(Request $request) // receive normal bag shipment
    {

        //        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        //
        //        if ($shipment->exists()) {
        //            $shipment = $shipment->first();
        //
        //            $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
        //            if (!$dispute_check) {
        //                return ['status' => 1, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
        //            }
        //
        //            if ($shipment->shipper_status_id != 3 && $shipment->shipper_status_id != 21 && $shipment->shipper_status_id != 26 && $shipment->shipper_status_id != 32 && $shipment->shipper_status_id != 49) {
        //                return ['status' => 1, 'error' => 'Given Tracking Number has already been modified!'];
        //            }
        //            $bag_shipment = CargoManifestBagShipments::where('shipment_id', $shipment->id);
        //
        //            if ($bag_shipment->exists()) {
        //                $bag_shipment = $bag_shipment->where('status', 0);
        //
        //                if ($bag_shipment->exists()) {
        //                    $bag_shipment = $bag_shipment->latest()->first();
        //                    $bag = $bag_shipment->bag;
        //                    if ($bag) {
        //
        //                        if ($bag->type != $request->bag_type) {
        //                            return ['status' => 1, 'error' => 'Shipment bag type is not same as selected bag type'];
        //                        }
        //
        //                        $cargo_manifest_bag = ManifestBag::where('cargo_manifest_bag_id', $bag->id)->latest()->first();
        //                        if (!$cargo_manifest_bag) {
        //                            return ['status' => 1, 'error' => 'No Bag exists for the following shipment'];
        //                        }
        //                    }
        //
        //                    if (!in_array($bag->destination_hub->hub_id, session('hubs'))) {
        //                        return ['status' => 1, 'error' => 'Shipment Bag doesn\'t belong to your assigned hub(s)!'];
        //                    }
        //
        //                    if (!$request->has('pieces_confirm')) {
        //                        if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
        //                            $details = array();
        //                            $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
        //
        //                            $details['id'] = $shipment->id;
        //                            $details['tracking_number'] = $shipment->tracking_number;
        //                            $details['pieces_count'] = $shipment->pieces;
        //                            $details['pieces_tracking_numbers'] = $shipment_pieces;
        //                            ShipmentScanningJourneyController::add($shipment->id, 20, 1, Auth::id(), null, null);
        //                            return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
        //                        }
        //                    }
        //                    $details = array();
        //
        //                    $details['id'] = $shipment->id;
        //                    $details['tracking_number'] = $shipment->tracking_number;
        //                    $details['bag_number'] = $bag->seal_number;
        //
        //                    if ($shipment->shipper_status_id == 21) {
        //                        $details['origin'] = $shipment->consignee_city->name;
        //                        if ($shipment->return_address_id != NULL) {
        //                            $details['destination'] = $shipment->return_address->city->name;
        //                            $details['hub'] = $shipment->return_address->city->hub_city->name;
        //                        } else {
        //                            $details['destination'] = $shipment->pickup_address->city->name;
        //                            $details['hub'] = $shipment->pickup_address->city->hub_city->name;
        //                        }
        //                    } else {
        //                        $details['origin'] = $shipment->pickup_address->city->name;
        //                        $details['destination'] = $shipment->consignee_city->name;
        //                        $details['hub'] = $shipment->consignee_city->hub_city->name;
        //                    }
        //
        //                    $details['consignee'] = $shipment->consignee_name;
        //                    $details['shipping_mode'] = $shipment->shipping_mode->mode;
        //                    $details['amount'] = number_format($shipment->amount);
        //                    $details['service_type'] = $shipment->booking_type->booking_type;
        //                    ShipmentScanningJourneyController::add($shipment->id, 20, 1, Auth::id(), null, null);
        //                    return ['status' => 0, 'success' => 'Shipment has been Added!', 'details' => $details];
        //                } else {
        //                    return ['status' => 1, 'error' => 'Given Bag Number\'s has already been Received'];
        //                }
        //            } else {
        //                return ['status' => 1, 'error' => 'Given Tracking Number is not in any Bag'];
        //            }
        //        } else {
        //            return ['status' => 1, 'error' => 'Invalid Tracking Number'];
        //        }

        // without restriction
        $return_reattempt_flag = 0;
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $shipment_id = $shipment->id;
            //to do : saving shipper weight as arrival weight and calculating based on shipper weight incase of arrival missing at origin.
            if(!$shipment->actual_weight) {
                $actual_weight = $shipment->estimated_weight;
                if ($shipment->booking_type_id == 4) {
                    $international_shipment = InternationalShipment::where('shipment_id', $shipment->id);
                    if ($international_shipment->exists()) {
                        $city = City::find($shipment->consignee_city_id);
                        $hub_id = $city->hub_id;
                        $standard_charges_hub = WalkInInternationalStandardWeightChargeHub::where('hub_id', $hub_id)->first();
                        $check = WalkInInternationalStandardWeightCharge::find($standard_charges_hub->international_charges_id);
                        if ($shipment->walk_in_delivery_type_id == 1) {
                            $check_actual_weight = $check->door_actual_weight;
                        } else {
                            $check_actual_weight = $check->hub_actual_weight;
                        }
                        if ($actual_weight < $check_actual_weight) {
                            $actual_weight = $check_actual_weight;
                        }
                    } else {
                        $check = WalkInStandardWeightCharge::where(['shipping_mode_id' => $shipment->shipping_mode_id, 'delivery_type_id' => $shipment->walk_in_delivery_type_id])->first();
                        if ($actual_weight < $check['actual_weight']) {
                            $actual_weight = $check['actual_weight'];
                        }
                    }
                }

                $shipment->actual_weight = $actual_weight;
                $shipment->save();

                if ($shipment->packaging_material_request == 0 && $shipment->shipment_type == 1) {
                    if ($shipment->booking_type_id == 4) {
                        ShipmentChargesController::walkin_weight($shipment_id);
                    } else {
                        ShipmentChargesController::weight($shipment_id);
                        if($shipment->booking_type_id == 5){
                            ShipmentChargesController::reverse_pickup($shipment_id);
                        }
                        if ($shipment->business_category_id == 1) {
                            ShipmentChargesController::cash_handling($shipment_id);
                            ShipmentChargesController::insurance($shipment_id);
                            ShipmentChargesController::fuel_surcharge($shipment_id);
                            ShipmentChargesController::faf_charges($shipment_id);
                        } else {
                            ShipmentChargesController::international_fuel_surcharge($shipment_id);
                            ShipmentChargesController::international_faf_charges($shipment_id);
                        }
                    }

                    if ($shipment->walk_in_status == 0) {
                        InitialChargesWebhookController::webhook_subscription($shipment_id);
                    }
                }
                
            }
            // end of calculation block


            // disabled this because ali requirment
            //  $misroute_history_count = $shipment->shipment_journey->where('shipper_status_id', 49)->count();

            if (in_array($shipment->shipper_status_id,[23,25]))
                return ['status' => 1, 'error' => 'Shipment is at dispatched or delivered to shipper !'];
            // disabled this because ali requirment
            //   if ($misroute_history_count == 0) //for support screen misroute
            //   {
            //       if (($shipment->pickup_address->city->id == $shipment->destination_city->id) && ($shipment->intercepted != 1))
            //           return ['status' => 1, 'error' => 'Shipment`s origin and destination are same or Bag type is not relevant !'];
            //   }

            if (in_array($shipment->shipper_status_id, [5, 14, 25, 31, 36, 38])) // all delivered statuses
                return ['status' => 1, 'error' => 'Shipment is on out for delivery !'];

            if ($shipment->shipper_status_id == 1) // shipment not arrived at center
                return ['status' => 1, 'error' => 'Shipment not arrived at center yet !'];

            //if(($shipment->shipper_status_id == 5) && ($request->bag_type == 2))
            //    return ['status' => 1, 'error' => 'Shipment not arrived at center yet !'];

            $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
            if (!$dispute_check) {
                return ['status' => 1, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
            }

            // if ($shipment->shipper_status_id != 3 && $shipment->shipper_status_id != 21 && $shipment->shipper_status_id != 26 && $shipment->shipper_status_id != 32 && $shipment->shipper_status_id != 49) {
            //     return ['status' => 1, 'error' => 'Given Tracking Number has already been modified!'];
            // }
            $bag_shipment = CargoManifestBagShipments::where('shipment_id', $shipment->id);

            if ($bag_shipment->exists()) {
                $bag_shipment = $bag_shipment->where('status', 0);

                $bag_type = $request->bag_type;
                $shipment_status = $shipment->shipper_status_id;
                // validate bag n shipment type
                
                if ($bag_type == 1) {
                    if (in_array($shipment_status, [20, 21, 22, 23, 24, 25, 44, 47, 48,69,70,72,73,75,76,27,33,30,37])) {
                        return ['status' => 1, 'error' => 'Tracking Number is of return type while bag type is normal !'];
                    }
                } else {
                    if (!in_array($shipment_status, [20, 21, 22, 23, 24, 25, 44, 47, 48,69,70,72,73,75,76,27,33,30,37])) {
                        return ['status' => 1, 'error' => 'Tracking Number is of normal type while bag type is return !'];
                    }
                }
                // validate bag n shipment type end

                if ($bag_shipment->exists()) {
                    $bag_shipment = $bag_shipment->latest()->first();
                    $bag = $bag_shipment->bag;
                    if ($bag) {

                        if ($bag->type != $request->bag_type) {
                            return ['status' => 1, 'error' => 'Shipment bag type is not same as selected bag type'];
                        }

                        $cargo_manifest_bag = ManifestBag::where('cargo_manifest_bag_id', $bag->id)->latest()->first();
                        if (!$cargo_manifest_bag) {
                            //                            return ['status' => 1, 'error' => 'No Bag exists for the following shipment'];
                        }
                    }

                    //if (!in_array($bag->destination_hub->hub_id, session('hubs'))) {
                    //    return ['status' => 1, 'error' => 'Shipment Bag doesn\'t belong to your assigned hub(s)!'];
                    //}

                    if (!$request->has('pieces_confirm')) {
                        if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                            $details = array();
                            $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                            $details['id'] = $shipment->id;
                            $details['tracking_number'] = $shipment->tracking_number;
                            $details['pieces_count'] = $shipment->pieces;
                            $details['pieces_tracking_numbers'] = $shipment_pieces;
                            ShipmentScanningJourneyController::add($shipment->id, 20, 1, Auth::id(), null, null, null, null, null, null, null, $request->action);
                            return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                        }
                    }
                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['bag_number'] = $bag->seal_number;

                    if (in_array($shipment->shipper_status_id, [21,75,76])) {
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
                    ShipmentScanningJourneyController::add($shipment->id, 20, 1, Auth::id(), NULL, NULL, NULL, NULL, session('latitude'), session('longitude'), NULL, $request->action);
                    return ['status' => 0, 'success' => 'Shipment has been Added!', 'details' => $details];
                } else {
                    //return ['status' => 1, 'error' => 'Given Bag Number\'s has already been Received'];
                    //todo

                    if (!$request->has('pieces_confirm')) {
                        if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                            $details = array();
                            $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                            $details['id'] = $shipment->id;
                            $details['tracking_number'] = $shipment->tracking_number;
                            $details['pieces_count'] = $shipment->pieces;
                            $details['pieces_tracking_numbers'] = $shipment_pieces;
                            ShipmentScanningJourneyController::add($shipment->id, 20, 1, Auth::id(), null, null, null, null, null, null, null, $request->action);
                            return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                        }
                    }
                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['bag_number'] = 'N/A';

                    if (in_array($shipment->shipper_status_id, [21,75,76])) {
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
                    ShipmentScanningJourneyController::add($shipment->id, 20, 1, Auth::id(), null, null, null, null, null, null, null,  $request->action);
                    return ['status' => 0, 'success' => 'Shipment has been Added!', 'details' => $details];
                }
            } else {
                //return ['status' => 1, 'error' => 'Given Tracking Number is not in any Bag'];

                $bag_type = $request->bag_type;
                $shipment_status = $shipment->shipper_status_id;

                // validate bag n shipment type
                if ($bag_type == 1) {
                    if (in_array($shipment_status, [20, 21, 22, 23, 24, 25, 44, 47, 48,69,70,72,73,75,76,27,33,30,37])) {
                        return ['status' => 1, 'error' => 'Tracking Number is of return type while bag type is normal !'];
                    }

                    // check that is shipment return reattempt or not
                    $return_confirm_journey = ShipmentsJourney::where('shipment_id', $shipment->id)
                        ->where('shipper_status_id', 20);
                    if ($return_confirm_journey->exists()) {
                        $return_reattempt_flag = 1;
                    }

                    if($return_reattempt_flag)
                    {
                        return ['status' => 1, 'error' => 'Tracking Number is of return type while bag type is normal !!!'];
                    }
                    // check that is shipment return reattempt or not end

                } else {
                    if (!in_array($shipment_status, [20, 21, 22, 23, 24, 25, 44, 47, 48,69,70,72,73,75,76,27,33,30,37])) {
                        return ['status' => 1, 'error' => 'Tracking Number is of normal type while bag type is return !'];
                    }
                }
                // validate bag n shipment type end

                if (!$request->has('pieces_confirm')) {
                    if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                        $details = array();
                        $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['pieces_count'] = $shipment->pieces;
                        $details['pieces_tracking_numbers'] = $shipment_pieces;
                        ShipmentScanningJourneyController::add($shipment->id, 20, 1, Auth::id(), null, null, null, null, null, null, null, $request->action);
                        return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                    }
                }
                $details = array();
                $misroute = 1;

                $details['id'] = $shipment->id;
                $details['tracking_number'] = $shipment->tracking_number;
                $details['bag_number'] = 'N/A';

                if (in_array($shipment->shipper_status_id, [20, 21])) {
                    $details['origin'] = $shipment->consignee_city->name;
                    if ($shipment->return_address_id != NULL) {
                        $details['destination'] = $shipment->return_address->city->name;
                        $details['destination_id'] = $shipment->return_address->city->id;
                        $details['hub'] = $shipment->return_address->city->hub_city->name;
                    } else {
                        $details['destination'] = $shipment->pickup_address->city->name;
                        $details['destination_id'] = $shipment->pickup_address->city->id;
                        $details['hub'] = $shipment->pickup_address->city->hub_city->name;
                    }

                    if (Auth::user()->default_hub_id == $details['destination_id'])
                        $misroute = 0;
                } else {
                    $details['origin'] = $shipment->pickup_address->city->name;
                    $details['destination'] = $shipment->consignee_city->name;
                    $details['destination_id'] = $shipment->consignee_city->id;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;

                    if (Auth::user()->default_hub_id == $details['destination_id'])
                        $misroute = 0;
                }

                $details['consignee'] = $shipment->consignee_name;
                $details['shipping_mode'] = $shipment->shipping_mode->mode;
                $details['amount'] = number_format($shipment->amount);
                $details['service_type'] = $shipment->booking_type->booking_type;
                $details['misroute'] = $misroute;
                ShipmentScanningJourneyController::add($shipment->id, 20, 1, Auth::id(), null, null, null, null, null, null, null, $request->action);
                return ['status' => 0, 'success' => 'Shipment has been Added!', 'details' => $details];
            }
        } else {
            return ['status' => 1, 'error' => 'Invalid Tracking Number'];
        }
    }

    public function receive_bag_shipment_details_old(Request $request)
    {

        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();

            $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
            if (!$dispute_check) {
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

                        if ($bag->type != $request->bag_type) {
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
                            ShipmentScanningJourneyController::add($shipment->id,20,1,Auth::id(),NULL,NULL,NULL,NULL, session('latitude'), session('longitude'), NULL);
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
                    ShipmentScanningJourneyController::add($shipment->id ,20,1,Auth::id(),NULL,NULL,NULL,NULL, session('latitude'), session('longitude'), NULL);
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

    public  function receive_bag_shipments_details_return(Request $request) // receive return bag shipment
    {

        $return_reattempt_flag = 0;
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();

            if (in_array($shipment->shipper_status_id,[23,25]))
                return ['status' => 1, 'error' => 'Shipment is at dispatched or delivered to shipper !'];


            if (in_array($shipment->shipper_status_id, [5, 14, 25, 31, 36, 38])) // all delivered statuses
                return ['status' => 1, 'error' => 'Shipment is on out for delivery !'];

            $bag_type = $request->bag_type;
            $shipment_status = $shipment->shipper_status_id;

            if ($shipment_status != 11) // agr status 11 h to masla h q k forwarding and return dono p same 11 lagta h ispe sochna h, lekin koshish ki h niche is resolve krne ki ($return_confirm_journey,$return_reattampt)
            {
                if ($bag_type == 1) {
                    if (in_array($shipment_status, [20, 21, 22, 23, 24, 25, 44, 47, 48,69,70,72,73,75,76,27,33,30,37])) {
                        return ['status' => 1, 'error' => 'Tracking Number is of return type while bag type is normal !'];
                    }
                } else {

                    //add this flag because shipment show in return screen but shipment status is arrived at origin
                    $return_reattempt_flag = 1;

                    // check that is shipment return reattempt or not                    
                    $return_confirm_journey = ShipmentsJourney::where('shipment_id', $shipment->id)
                        ->where('shipper_status_id', 20);
                    if ($return_confirm_journey->exists()) {
                        $return_confirm_journey = $return_confirm_journey->first();
                        $return_confirm_journey_next = $return_confirm_journey->id + 1;

                        $return_reattampt = ShipmentsJourney::where('id', $return_confirm_journey_next)
                            ->where('shipper_status_id', 13);

                        if ($return_reattampt->exists()) {
                            $return_reattempt_flag = 1;
                        }
                    }
                    // check that is shipment return reattempt or not end

                    if ((!in_array($shipment_status, [20, 21, 22, 24, 44, 47, 48,49,26,27,29,69,70,72,73,75,76,27,33,32,30,37])) && ($return_reattempt_flag == 1)) {
                        return ['status' => 1, 'error' => 'Tracking Number is of normal type while bag type is return !'];
                    }
                }
            }


            $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
            if (!$dispute_check) {
                return ['status' => 1, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
            }

            // if ($shipment->shipper_status_id != 3 && $shipment->shipper_status_id != 21 && $shipment->shipper_status_id != 26 && $shipment->shipper_status_id != 32 && $shipment->shipper_status_id != 49) {
            //     return ['status' => 1, 'error' => 'Given Tracking Number has already been modified!'];
            // }
            $bag_shipment = CargoManifestBagShipments::where('shipment_id', $shipment->id);
            if ($bag_shipment->exists()) {
                $bag_shipment = $bag_shipment->latest()->take(1)->first();

                if ($bag_shipment->status == 1) // previous bag found but no new bag created
                {
                    if (!$request->has('pieces_confirm')) {
                        if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                            $details = array();
                            $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                            $details['id'] = $shipment->id;
                            $details['tracking_number'] = $shipment->tracking_number;
                            $details['pieces_count'] = $shipment->pieces;
                            $details['pieces_tracking_numbers'] = $shipment_pieces;
                            ShipmentScanningJourneyController::add($shipment->id, 20, 1, Auth::id(), null, null, null, null, null, null, null, $request->action);
                            return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                        }
                    }
                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['bag_number'] = 'N/A';

                    if (in_array($shipment->shipper_status_id, [21,69,70,72,73,75,76,30,37])) {
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
                    ShipmentScanningJourneyController::add($shipment->id, 20, 1, Auth::id(), null, null, null, null, null, null, null, $request->action);
                    return ['status' => 0, 'success' => 'Shipment has been Added!', 'details' => $details];
                } else // new bag found or fresh bag found
                {
                    $bag = $bag_shipment->bag;

                    if ($bag) {

                        if ($bag->type != $request->bag_type) {
                            return ['status' => 1, 'error' => 'Shipment bag type is not same as selected bag type'];
                        }

                        $cargo_manifest_bag = ManifestBag::where('cargo_manifest_bag_id', $bag->id)->latest()->first();
                        if (!$cargo_manifest_bag) {
                            //                            return ['status' => 1, 'error' => 'No Bag exists for the following shipment'];
                        }
                    }

                    //if (!in_array($bag->destination_hub->hub_id, session('hubs'))) {
                    //    return ['status' => 1, 'error' => 'Shipment Bag doesn\'t belong to your assigned hub(s)!'];
                    //}

                    if (!$request->has('pieces_confirm')) {
                        if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                            $details = array();
                            $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                            $details['id'] = $shipment->id;
                            $details['tracking_number'] = $shipment->tracking_number;
                            $details['pieces_count'] = $shipment->pieces;
                            $details['pieces_tracking_numbers'] = $shipment_pieces;
                            ShipmentScanningJourneyController::add($shipment->id, 20, 1, Auth::id(), null, null, null, null, null, null, null, $request->action);
                            return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                        }
                    }
                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['bag_number'] = $bag->seal_number;

                    if (in_array($shipment->shipper_status_id, [21,69,70,72,73,75,76,30,37])) {
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
                    ShipmentScanningJourneyController::add($shipment->id, 20, 1, Auth::id(), null, null, null, null, null, null, null, $request->action);
                    return ['status' => 0, 'success' => 'Shipment has been Added!', 'details' => $details];
                }
            } else // no previous exists
            {
                //return ['status' => 1, 'error' => 'Given Tracking Number is not in any Bag'];
               // dd('test');
                if (!$request->has('pieces_confirm')) {
                    if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                        $details = array();
                        $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['pieces_count'] = $shipment->pieces;
                        $details['pieces_tracking_numbers'] = $shipment_pieces;
                        ShipmentScanningJourneyController::add($shipment->id, 20, 1, Auth::id(), null, null, null, null, null, null, null, $request->action);
                        return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                    }
                }
                $details = array();
                $misroute = 1;

                $details['id'] = $shipment->id;
                $details['tracking_number'] = $shipment->tracking_number;
                $details['bag_number'] = 'N/A';

                if (in_array($shipment->shipper_status_id, [20,21,69,70,72,73,75,76,30,37])) {
                    $details['origin'] = $shipment->consignee_city->name;
                    if ($shipment->return_address_id != NULL) {
                        $details['destination'] = $shipment->return_address->city->name;
                        $details['destination_id'] = $shipment->return_address->city->id;
                        $details['hub'] = $shipment->return_address->city->hub_city->name;
                    } else {
                        $details['destination'] = $shipment->pickup_address->city->name;
                        $details['destination_id'] = $shipment->pickup_address->city->id;
                        $details['hub'] = $shipment->pickup_address->city->hub_city->name;
                    }
                    if (Auth::user()->default_hub_id == $details['destination_id'])
                        $misroute = 0;
                } else {
                    $details['origin'] = $shipment->pickup_address->city->name;
                    $details['destination'] = $shipment->consignee_city->name;
                    $details['destination_id'] = $shipment->consignee_city->id;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;
                    if (Auth::user()->default_hub_id == $details['destination_id'])
                        $misroute = 0;
                }

                $details['consignee'] = $shipment->consignee_name;
                $details['shipping_mode'] = $shipment->shipping_mode->mode;
                $details['amount'] = number_format($shipment->amount);
                $details['service_type'] = $shipment->booking_type->booking_type;
                $details['misroute'] = $misroute;
                ShipmentScanningJourneyController::add($shipment->id, 20, 1, Auth::id(), null, null, null, null, null, null, null, $request->action);
                return ['status' => 0, 'success' => 'Shipment has been Added!', 'details' => $details];
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
                ShipmentScanningJourneyController::add($shipment_id, $request->screen_location_id, 1, Auth::id(), NULL, NULL, $shipment_piece->id, NULL, session('latitude'), session('longitude'), NULL, 0);

                return ['status' => 0, 'success' => 'Shipment Piece found!', 'scanned_shipment_piece' => $scanned_shipment_piece];
            } else {
                return ['status' => 1, 'error' => 'Given Item ID does not belong here'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment Item with given Item ID is present'];
        }
    }

    public function receive_bag_shipments_store(Request $request) // receive bag shipment store
    {
        //        $shipment_status_array = [3, 21, 26, 32, 49];
        //        $shipment_ids = array_unique(explode(',', $request->shipment_ids));
        //        $open_box_ids = explode(',', $request->open_box_ids);
        //        $bag_ids = array();
        //        $shipment_ids_array = array();
        //        $short_received_shipments_array = array();
        //        $shipments_already_marked_received_array = array();
        ////        todo : open box-work
        //        if (count($open_box_ids) > 0) {
        //            foreach ($shipment_ids as $index => $shipment) {
        //                if (in_array($shipment, $open_box_ids)) {
        //                    $shipment_detail = ShipmentDetail::where('shipment_id', $shipment)->where('is_open', '=', 0)->first();
        //                    if ($shipment_detail) {
        //                        $shipment_detail->is_open = 1;
        //                        $shipment_detail->save();
        //                    }
        //                    $shipment_data = Shipment::find($shipment);
        //                    $shipment_data->open_box = 1;
        //                    $shipment_data->save();
        //                    ShipmentOpenBoxJourneyController::add($shipment, 2, Auth::id());
        //                }
        //            }
        //        }
        ////        todo : open box-work end
        //        foreach ($shipment_ids as $shipment_id) {
        //            $bag_shipment = CargoManifestBagShipments::where('shipment_id', $shipment_id)/*->where('status', 0)*/
        //            ;
        //            if ($bag_shipment->exists()) {
        //                $bag_shipment = $bag_shipment->latest()->first();
        //                $shipment = Shipment::find($shipment_id);
        //                if (in_array($shipment->shipper_status_id, $shipment_status_array)) {
        //                    if ($bag_shipment->status == 0) {
        //                        $bag_shipment->status = 1;
        //                        $bag_shipment->save();
        //                    }
        //                    $bag = $bag_shipment->bag;
        //                    array_push($shipment_ids_array, $shipment->tracking_number);
        //                    $shipper_status_id = NULL;
        //                    $consignee_status_id = NULL;
        //                    if ($bag->type == 1) {
        //                        if ($shipment->booking_type_id == 4 && $shipment->walk_in_delivery_type_id == 2) {
        //                            //ShipmentsJourneyController::add($shipment_id, 4, 4, NULL, NULL, NULL, Auth::id());
        //                            $shipper_status_id = 15;
        //                            $consignee_status_id = 15;
        //                        } else {
        //                            $shipper_status_id = 4;
        //                            $consignee_status_id = 4;
        //                            $self_collection = SelfCollectionShipment::where('shipment_id', $shipment_id)->first();
        //                            if ($self_collection) {
        //                                $consignee_city = $shipment->consignee_city_id;
        //                                $user_city = $shipment->user->city_id;
        //                                if ($consignee_city == '202' || $consignee_city == '223') {
        //                                    NotificationsController::send(178, $shipment_id);
        //                                } else {
        //                                    $city_id = SelfCollectionCities::where('city_id', $consignee_city)->select('city_id', 'address')->first();
        //                                    if ($city_id) {
        //                                        NotificationsController::send(75, $shipment_id, $city_id->address);
        //                                    }
        //                                }
        //                            }
        //                        }
        //                    } else {
        //                        if ($shipment->booking_type_id == 1) {
        //                            $shipper_status_id = 22;
        //                            $consignee_status_id = 22;
        //                        } else if ($shipment->booking_type_id == 2) {
        //                            if ($shipment->shipper_status_id == 21) {
        //                                $shipper_status_id = 22;
        //                                $consignee_status_id = 22;
        //                            } else {
        //                                $shipper_status_id = 27;
        //                                $consignee_status_id = 27;
        //                            }
        //                        } else if ($shipment->booking_type_id == 3) {
        //                            $shipper_status_id = 33;
        //                            $consignee_status_id = 33;
        //                        } else if ($shipment->booking_type_id == 4) {
        //                            $shipper_status_id = 22;
        //                            $consignee_status_id = 22;
        //                        } else {
        //                            $shipper_status_id = 22;
        //                            $consignee_status_id = 22;
        //                        }
        //                    }
        //                    $shipment->shipper_status_id = $shipper_status_id;
        //                    $shipment->consignee_status_id = $consignee_status_id;
        //                    $shipment->save();
        //                    ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $consignee_status_id, NULL, NULL, NULL, Auth::id());
        //                    if (!in_array($bag->id, $bag_ids)) {
        //                        $bag_ids[] = $bag->id;
        //                    }
        //                } else {
        //                    array_push($shipments_already_marked_received_array, $shipment->tracking_number);
        //                }
        //            }
        //        }
        //        foreach ($bag_ids as $bag_id) {
        //            $bag = CargoManifestBag::find($bag_id);
        //            $bag->received_shipments = CargoManifestBagShipments::where('cargo_manifest_bag_id', $bag_id)->where('status', 1)->count();
        //            $short_received = CargoManifestBagShipments::where('cargo_manifest_bag_id', $bag_id)->where('status', 0)->count();
        //            if ($short_received > 0) {
        //                $bag->short_received_shipments = $short_received;
        //                $status_id = 8;
        //            } else {
        //                $bag->short_received_shipments = 0;
        //                $status_id = 7;
        //                $bag->completed = 1;
        //            }
        //            $bag->status_id = $status_id;
        //            $bag->received_at = Carbon::now();
        //            $bag->receiver_id = Auth::id();
        //            $bag->current_hub_id = Auth::user()->default_hub_id;
        //            $bag->save();
        //            ManifestBag::where('cargo_manifest_bag_id', $bag->id)->update(['status' => 1]);
        //            //dispute for short received
        //            if ($bag->status_id == 8) {
        //                $bag_short_received_shipments = CargoManifestBagShipments::where(['cargo_manifest_bag_id' => $bag_id, 'status' => 0])->pluck('shipment_id')->toArray();
        //                if (!empty($bag_short_received_shipments)) {
        //                    DisputeController::add_cargo_short_received($bag->seal_number, $bag_short_received_shipments, null, 2);
        //                }
        //            }
        ////        end dispute short received
        //            /* if($all_bag_ids == ''){
        //                 $all_bag_ids = $all_bag_ids . $bag->seal_number;
        //             }
        //             else{
        //                 $all_bag_ids = $all_bag_ids . ', ' .$bag->seal_number;
        //             }*/
        //        }
        //        foreach ($bag_ids as $bag_id) {
        //            $bag = CargoManifestBag::find($bag_id);
        //            $manifest_id = ManifestBag::where('cargo_manifest_bag_id', $bag->id)->latest()->first()->cargo_manifest_id;
        //            $manifest = CargoManifest::find($manifest_id);
        //            $manifest->received_bags = ManifestBag::where('cargo_manifest_id', $manifest_id)->where('status', 1)->count();
        //            $short_received_bags = ManifestBag::where('cargo_manifest_id', $manifest_id)->where('status', 0)->count();
        //            if ($short_received_bags > 0) {
        //                $manifest->short_received_bags = $short_received_bags;
        //            } else {
        //                $manifest->short_received_bags = 0;
        //                $manifest->status_id = 2;
        //            }
        //            $manifest->update();
        //        }
        //        $bag_shipments = CargoManifestBagShipments::whereIn('cargo_manifest_bag_id', $bag_ids)->where('status', 0);
        //        if ($bag_shipments->exists()) {
        //            $shipment_ids = $bag_shipments->pluck('shipment_id')->toArray();
        //            foreach ($shipment_ids as $shipment_id) {
        //                $shipment = Shipment::find($shipment_id);
        //                if (!in_array($shipment->tracking_number, $short_received_shipments_array)) {
        //                    array_push($short_received_shipments_array, $shipment->tracking_number);
        //                }
        //            }
        //        }
        //        $received_html = '';
        //        $sr_html = '';
        //        $already_received_shipments_html = '';
        //        if (count($short_received_shipments_array) > 0) {
        //            $sr_html = "Following Shipments(s) are marked as short received.<br><ul>";
        //            foreach ($short_received_shipments_array as $v) {
        //                $sr_html .= "<li>" . $v . "</li>";
        //            }
        //            $sr_html .= "</ul>";
        //        }
        //        if (count($shipments_already_marked_received_array) > 0) {
        //            $already_received_shipments_html = "Following Shipments(s) are already marked as received or processed .<br><ul>";
        //            foreach ($shipments_already_marked_received_array as $v) {
        //                $already_received_shipments_html .= "<li>" . $v . "</li>";
        //            }
        //            $already_received_shipments_html .= "</ul>";
        //        }
        //        if (count($shipment_ids_array) > 0) {
        //            $received_html = "Following Shipments(s) are marked as received. .<br><ul>";
        //            foreach ($shipment_ids_array as $v) {
        //                $received_html .= "<li>" . $v . "</li>";
        //            }
        //            $received_html .= "</ul>";
        //        }
        //        //return redirect()->back()->with('success', 'Selected Shipments of Bag Number(s)#' . $all_bag_ids . ' has been Received');
        //        return back()->with(['sr_html' => $sr_html, 'received_html' => $received_html, 'already_received_shipments_html' => $already_received_shipments_html]);

        // new code without restriction
//        Log::channel('cronJobLog')->info('cargo:check_start');
        try {
            DB::beginTransaction();
//            Log::channel('cronJobLog')->info('cargo:check_1');

        $shipment_status_array = [2,3,11,20,21,26,32,49,68,69,70,72,73,75,76,30,37];
        $shipment_ids = array_unique(explode(',', $request->shipment_ids));
        $open_box_ids = explode(',', $request->open_box_ids);
        $bag_ids = array();
        $shipment_ids_array = array();
        $shipment_ids_array_misrouted = array();
        $short_received_shipments_array = array();
        $shipments_already_marked_received_array = array();
        $shipmentNoReceive = array();
        $admin = Admin::where('id',\auth()->id())->select('default_hub_id')->first();
        $default_hub_id = $admin->default_hub_id;
        $default_hub_name = $admin->city->name;
        $admin_assigned_hubs = session('hubs');
        // dd($request->all(),session('hubs'),\auth()->id(),$admin_default_hubs);
        //todo : open box-work
            if (count($open_box_ids) > 0) {
                $shipment_details = ShipmentDetail::whereIn('shipment_id', $shipment_ids)->where('is_open', '=', 0)->get()->keyBy('shipment_id');
                $shipment_detail_success_ids = [];
                $shipment_data_success_ids = [];
                foreach ($shipment_ids as $shipment) {
                    if (in_array($shipment, $open_box_ids)) {
                        $shipment_detail = $shipment_details->get($shipment);
                        if ($shipment_detail) {
                            $shipment_detail_success_ids[] = $shipment_detail->id;
                        }
                        $shipment_data_success_ids[] = $shipment;
                        ShipmentOpenBoxJourneyController::add($shipment, 2, Auth::id());
                    }
                }
                if (!empty($shipment_detail_success_ids)) {
                    ShipmentDetail::whereIn('id', $shipment_detail_success_ids)->update(['is_open' => 1]);
                }

                if (!empty($shipment_data_success_ids)) {
                    Shipment::whereIn('id', $shipment_data_success_ids)->update(['open_box' => 1]);
                }
//                Log::channel('cronJobLog')->info('cargo:check_2');
            }
            //todo : open box-work end

            if ($request->bag_type == 1) {
                foreach ($shipment_ids as $shipment_id) {
                    $bag_shipment = CargoManifestBagShipments::where('shipment_id', $shipment_id)/*->where('status', 0)*/;
                    $shipment = Shipment::find($shipment_id);
                    if (in_array($shipment->shipper_status_id, [5, 14, 25, 31, 36, 38])) // all delivered statuses
                    {
                        $shipmentNoReceive[] = $shipment->tracking_number;
                        continue;
                    }
                    if ($bag_shipment->exists()) {
                        $bag_shipment = $bag_shipment->latest()->first();

                        if ($bag_shipment->status == 0) {
                            if (in_array($shipment->shipper_status_id, $shipment_status_array)) {
                                $check_city_id = City::where('id',$shipment->consignee_city_id)->select('hub_id');
                                if($check_city_id->exists())
                                {
                                    $check_city_id = $check_city_id->first();
                                    $selected_hub_id = $check_city_id->hub_id;
                                }
                                else
                                {
                                    $selected_hub_id = $shipment->consignee_city_id;
                                }
                                if (($selected_hub_id == $default_hub_id) || (in_array($selected_hub_id,$admin_assigned_hubs))) {
                                    $bag_shipment->status = 1;
                                    $bag_shipment->save();
                                }
                                //if ($bag_shipment->status == 0) {
                                //    $bag_shipment->status = 1;
                                //    $bag_shipment->save();
                                //}

                                $bag = $bag_shipment->bag;

                                //array_push($shipment_ids_array, $shipment->tracking_number);

                                $shipper_status_id = NULL;
                                $consignee_status_id = NULL;

                                if ($bag->type == 1) {
                                    if ($shipment->booking_type_id == 4 && $shipment->walk_in_delivery_type_id == 2) {
                                        //ShipmentsJourneyController::add($shipment_id, 4, 4, NULL, NULL, NULL, Auth::id());
                                        $shipper_status_id = 15;
                                        $consignee_status_id = 15;
                                    } else {
                                        $check_city_id = City::where('id',$shipment->consignee_city_id)->select('hub_id');
                                        if($check_city_id->exists())
                                        {
                                            $check_city_id = $check_city_id->first();
                                            $selected_hub_id = $check_city_id->hub_id;
                                        }
                                        else
                                        {
                                            $selected_hub_id = $shipment->consignee_city_id;
                                        }
                                        if (($selected_hub_id == $default_hub_id) || (in_array($selected_hub_id,$admin_assigned_hubs))) {
                                            $shipper_status_id = 4;
                                            $consignee_status_id = 4;
                                            array_push($shipment_ids_array, $shipment->tracking_number);
                                        }
                                        else
                                        {
                                           
                                            $shipper_status_id = 68;
                                            $consignee_status_id = 68;
                                            
                                            array_push($shipment_ids_array_misrouted, $shipment->tracking_number);

                                            MisroutedHistory::create([
                                                'shipment_id' => $shipment->id,
                                                'old_consignee_city_id' => Auth::user()->default_hub_id,
                                                'old_consignee_name' => $shipment->consignee_name,
                                                'old_consignee_address' => $shipment->consignee_address,
                                                'old_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                                                'old_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                                                'old_consignee_email' => $shipment->consignee_email,
                                                'new_consignee_city_id' => $shipment->consignee_city_id,
                                                'new_consignee_name' => $shipment->consignee_name,
                                                'new_consignee_address' => $shipment->consignee_address,
                                                'new_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                                                'new_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                                                'new_consignee_email' => $shipment->consignee_email,
                                                'admin_id' => Auth::id()
                                            ]);
                                        }

                                        $self_collection = SelfCollectionShipment::where('shipment_id', $shipment_id)->first();

                                        if ($self_collection) {
                                            $consignee_city = $shipment->consignee_city_id;
                                            $user_city = $shipment->user->city_id;

                                            if ($consignee_city == '202' || $consignee_city == '223') {
                                                NotificationsController::send(178, $shipment_id);
                                            } else {
                                                $city_id = SelfCollectionCities::where('city_id', $consignee_city)->select('city_id', 'address')->first();
                                                if ($city_id) {
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
                        } else {
                            $check_city_id = City::where('id',$shipment->consignee_city_id)->select('hub_id');
                            if($check_city_id->exists())
                            {
                                $check_city_id = $check_city_id->first();
                                $selected_hub_id = $check_city_id->hub_id;
                            }
                            else
                            {
                                $selected_hub_id = $shipment->consignee_city_id;
                            }
                            if (($selected_hub_id == $default_hub_id) || (in_array($selected_hub_id,$admin_assigned_hubs))) {
                                $shipment->shipper_status_id = 4;
                                $shipment->consignee_status_id = 4;
                                $shipment->save();

                                ShipmentsJourneyController::add($shipment_id, 67, 67, NULL, NULL, NULL, Auth::id()); // without manifest status
                                ShipmentsJourneyController::add($shipment_id, 4, 4, NULL, NULL, NULL, Auth::id()); // received at destination
                                array_push($shipment_ids_array, $shipment->tracking_number);
                            }
                            else
                            {
                                $shipment->shipper_status_id = 68;
                                $shipment->consignee_status_id = 68;

                                $shipment->save();
                                $last_scanned = ShipmentsJourney::where('shipment_id', $shipment_id)->select('city_id');
                                if ($last_scanned->exists()) {
                                    $last_scanned = $last_scanned->latest()->take(1)->first();
                                    $remarks = 'Misrouted from ' . $last_scanned->city->name . ' to ' . $default_hub_name . '.';
                                } else {
                                    $remarks = '--';
                                }

                                ShipmentsJourneyController::add($shipment_id, 67, 67, NULL, NULL, NULL, Auth::id()); // without manifest status
                                ShipmentsJourneyController::add($shipment_id, 68, 68, NULL, $remarks, NULL, Auth::id());// new misrouted
                                array_push($shipment_ids_array_misrouted, $shipment->tracking_number);

                                MisroutedHistory::create([
                                    'shipment_id' => $shipment->id,
                                    'old_consignee_city_id' => Auth::user()->default_hub_id,
                                    'old_consignee_name' => $shipment->consignee_name,
                                    'old_consignee_address' => $shipment->consignee_address,
                                    'old_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                                    'old_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                                    'old_consignee_email' => $shipment->consignee_email,
                                    'new_consignee_city_id' => $shipment->consignee_city_id,
                                    'new_consignee_name' => $shipment->consignee_name,
                                    'new_consignee_address' => $shipment->consignee_address,
                                    'new_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                                    'new_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                                    'new_consignee_email' => $shipment->consignee_email,
                                    'admin_id' => Auth::id()
                                ]);
                            }
                        }
                    } else {

                        $check_city_id = City::where('id',$shipment->consignee_city_id)->select('hub_id');
                        if($check_city_id->exists())
                        {
                            $check_city_id = $check_city_id->first();
                            $selected_hub_id = $check_city_id->hub_id;
                        }
                        else
                        {
                            $selected_hub_id = $shipment->consignee_city_id;
                        }

                        if (($selected_hub_id == $default_hub_id) || (in_array($selected_hub_id,$admin_assigned_hubs))) {

                            $shipment->shipper_status_id = 4;
                            $shipment->consignee_status_id = 4;
                            $shipment->save();

                            ShipmentsJourneyController::add($shipment_id, 67, 67, NULL, NULL, NULL, Auth::id()); // without manifest status
                            ShipmentsJourneyController::add($shipment_id, 4, 4, NULL, NULL, NULL, Auth::id()); // received at destination
                            array_push($shipment_ids_array, $shipment->tracking_number);
                        }
                        else
                        {
                            $shipment->shipper_status_id = 68;
                            $shipment->consignee_status_id = 68;
                            
                            $shipment->save();
                            $last_scanned = ShipmentsJourney::where('shipment_id', $shipment_id)->select('city_id');
                            if ($last_scanned->exists()) {
                                $last_scanned = $last_scanned->latest()->take(1)->first();
                                $remarks = 'Misrouted from ' . $last_scanned->city->name . ' to ' . $default_hub_name . '.';
                            } else {
                                $remarks = '--';
                            }

                            ShipmentsJourneyController::add($shipment_id, 67, 67, NULL, NULL, NULL, Auth::id()); // without manifest status
                            ShipmentsJourneyController::add($shipment_id, 68, 68, NULL, $remarks, NULL, Auth::id());// new misrouted
                            array_push($shipment_ids_array_misrouted, $shipment->tracking_number);

                            MisroutedHistory::create([
                                'shipment_id' => $shipment->id,
                                'old_consignee_city_id' => Auth::user()->default_hub_id,
                                'old_consignee_name' => $shipment->consignee_name,
                                'old_consignee_address' => $shipment->consignee_address,
                                'old_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                                'old_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                                'old_consignee_email' => $shipment->consignee_email,
                                'new_consignee_city_id' => $shipment->consignee_city_id,
                                'new_consignee_name' => $shipment->consignee_name,
                                'new_consignee_address' => $shipment->consignee_address,
                                'new_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                                'new_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                                'new_consignee_email' => $shipment->consignee_email,
                                'admin_id' => Auth::id()
                            ]);
                        }
                    }

                    //check previous bag received shipments
                    if ($shipment->shipper_status_id == 4) {
                        $check_previous_shipment = CargoManifestBagShipments::where('shipment_id', $shipment_id)->where('status', 0);
                        if ($check_previous_shipment->exists()) {
                            $check_previous_shipment = $check_previous_shipment->first();
                            $check_previous_shipment->status = 1;
                            $check_previous_shipment->save();

                            $check_previous_shipment_bag = $check_previous_shipment->bag;

                            if (!$check_previous_shipment_bag->completed) {
                                $check_previous_shipment_bag->short_received_shipments = $check_previous_shipment_bag->short_received_shipments - 1;
                                $check_previous_shipment_bag->received_shipments = $check_previous_shipment_bag->received_shipments + 1;
                                $check_previous_shipment_bag->save();
                                $final_qty = $check_previous_shipment_bag->shipments - $check_previous_shipment_bag->received_shipments;
                                if ($final_qty == 0) {
                                    $check_previous_shipment_bag->status_id = 7;
                                    $check_previous_shipment_bag->completed = 1;
                                    $check_previous_shipment_bag->save();
                                }
                            }
                        }
                    }
                    //check previous bag received shipments end
                }
//                Log::channel('cronJobLog')->info('cargo:check_3');
            }
            else {
                foreach ($shipment_ids as $shipment_id) {
                    $bag_shipment = CargoManifestBagShipments::where('shipment_id', $shipment_id)/*->where('status', 0)*/;
                    $shipment = Shipment::find($shipment_id);
                    if (in_array($shipment->shipper_status_id, [5, 14, 25, 31, 36, 38])) // all delivered statuses
                    {
                        $shipmentNoReceive[] = $shipment->tracking_number;
                        continue;
                    }
                    $omni_return_city_hub = $shipment->return_address_id != NULL ? $shipment->return_address->city->hub_id : NULL;
                    $omni_return_city = $shipment->return_address_id;
                    if ($bag_shipment->exists()) {
                        $bag_shipment = $bag_shipment->latest()->first();
                        //dd($bag_shipment);
                        if ($bag_shipment->status == 1) {
                            $shipment = Shipment::find($shipment_id);
                            if($omni_return_city == NULL) {
                                $origin = $shipment->pickup_address->city->id;

                                $check_city_id = City::where('id',$origin)->select('hub_id');
                                if($check_city_id->exists())
                                {
                                    $check_city_id = $check_city_id->first();
                                    $selected_hub_id = $check_city_id->hub_id;
                                }
                                else
                                {
                                    $selected_hub_id = $origin;
                                }
                            } else {
                                $selected_hub_id = $omni_return_city_hub;
                            }
                            
                            if (($selected_hub_id == $default_hub_id) || (in_array($selected_hub_id,$admin_assigned_hubs)) || ($omni_return_city_hub == $default_hub_id) || (in_array($omni_return_city_hub,$admin_assigned_hubs)) ) // wisevarsa -> pickupaddress id
                            {
                                // $shipment->shipper_status_id = 22;
                                // $shipment->consignee_status_id = 22;
                                // $shipment->save();

                                if($shipment->booking_type_id == 2) {
                                    if(in_array($shipment->shipper_status_id, [30,27,72])) {
                                        $shipper_status_id = 27;
                                        $consignee_status_id = 27;
                                        $without_manifest = 74;
                                    } else {
                                        $shipper_status_id = 22;
                                        $consignee_status_id = 22;
                                        $without_manifest = 77;
                                    }
                                } elseif($shipment->booking_type_id == 3) {
                                    if(in_array($shipment->shipper_status_id, [32,37,69,70])) {
                                        $shipper_status_id = 33;
                                        $consignee_status_id = 33;
                                        $without_manifest = 71;
                                    } else {
                                        $shipper_status_id = 22;
                                        $consignee_status_id = 22;
                                        $without_manifest = 77;
                                    }
                                } else {
                                    $shipper_status_id = 22;
                                    $consignee_status_id = 22;
                                    $without_manifest = 77;
                                }
                                $shipment->shipper_status_id = $shipper_status_id;
                                $shipment->consignee_status_id = $consignee_status_id;
                                $shipment->save();

                                ShipmentsJourneyController::add($shipment_id, $without_manifest, $without_manifest, NULL, NULL, NULL, Auth::id()); // without bag/manifest status
                                ShipmentsJourneyController::add($shipment_id, $consignee_status_id, $consignee_status_id, NULL, NULL, NULL, Auth::id()); // received at origin
                                array_push($shipment_ids_array, $shipment->tracking_number);
                            } else {
                                // $shipment->shipper_status_id = 75;
                                // $shipment->consignee_status_id = 75;
                                // $shipment->save();
                                if($shipment->booking_type_id == 2) {
                                    if(in_array($shipment->shipper_status_id, [30,27,72])) {
                                        $shipper_status_id = 72;
                                        $consignee_status_id = 72;
                                        $without_manifest = 74;
                                    } else {
                                        $shipper_status_id = 75;
                                        $consignee_status_id = 75;
                                        $without_manifest = 77;
                                    }
                                } elseif($shipment->booking_type_id == 3) {
                                    if(in_array($shipment->shipper_status_id, [32,37,69])) {
                                        $shipper_status_id = 69;
                                        $consignee_status_id = 69;
                                        $without_manifest = 71;
                                    } else {
                                        $shipper_status_id = 75;
                                        $consignee_status_id = 75;
                                        $without_manifest = 77;
                                    }
                                } else {
                                    $shipper_status_id = 75;
                                    $consignee_status_id = 75;
                                    $without_manifest = 77;
                                }
                                $shipment->shipper_status_id = $shipper_status_id;
                                $shipment->consignee_status_id = $consignee_status_id;
                                $shipment->save();

                                $last_scanned = ShipmentsJourney::where('shipment_id', $shipment_id)->select('city_id');
                                if ($last_scanned->exists()) {
                                    $last_scanned = $last_scanned->latest()->take(1)->first();
                                    $remarks = 'Misrouted from ' . $last_scanned->city->name . ' to ' . $default_hub_name . '.';
                                } else {
                                    $remarks = '--';
                                }
                                ShipmentsJourneyController::add($shipment_id, $without_manifest, $without_manifest, NULL, NULL, NULL, Auth::id()); // without manifest status
                                ShipmentsJourneyController::add($shipment_id, $consignee_status_id, $consignee_status_id, NULL, $remarks, NULL, Auth::id()); // new misrouted
                                array_push($shipment_ids_array_misrouted, $shipment->tracking_number);
                                MisroutedHistory::create([
                                    'shipment_id' => $shipment->id,
                                    'old_consignee_city_id' => Auth::user()->default_hub_id,
                                    'old_consignee_name' => $shipment->consignee_name,
                                    'old_consignee_address' => $shipment->consignee_address,
                                    'old_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                                    'old_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                                    'old_consignee_email' => $shipment->consignee_email,
                                    'new_consignee_city_id' => $shipment->consignee_city_id,
                                    'new_consignee_name' => $shipment->consignee_name,
                                    'new_consignee_address' => $shipment->consignee_address,
                                    'new_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                                    'new_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                                    'new_consignee_email' => $shipment->consignee_email,
                                    'admin_id' => Auth::id()
                                ]);
                            }
                        } else {
                            if (in_array($shipment->shipper_status_id, $shipment_status_array)) {

                                if ($bag_shipment->status == 0) {
                                    $bag_shipment->status = 1;
                                    $bag_shipment->save();
                                }

                                $bag = $bag_shipment->bag;

                                $shipper_status_id = NULL;
                                $consignee_status_id = NULL;

                                if ($bag->type == 1) {
                                    if ($shipment->booking_type_id == 4 && $shipment->walk_in_delivery_type_id == 2) {
                                        //ShipmentsJourneyController::add($shipment_id, 4, 4, NULL, NULL, NULL, Auth::id());
                                        $shipper_status_id = 15;
                                        $consignee_status_id = 15;
                                    } else {

                                        if ($shipment->consignee_city_id == $default_hub_id) {
                                            $shipper_status_id = 4;
                                            $consignee_status_id = 4;
                                        }
                                        else
                                        {
                                            $shipper_status_id = 68;
                                            $consignee_status_id = 68;
                                        }

                                        $self_collection = SelfCollectionShipment::where('shipment_id', $shipment_id)->first();

                                        if ($self_collection) {
                                            $consignee_city = $shipment->consignee_city_id;
                                            $user_city = $shipment->user->city_id;

                                            if ($consignee_city == '202' || $consignee_city == '223') {
                                                NotificationsController::send(178, $shipment_id);
                                            } else {
                                                $city_id = SelfCollectionCities::where('city_id', $consignee_city)->select('city_id', 'address')->first();
                                                if ($city_id) {
                                                    NotificationsController::send(75, $shipment_id, $city_id->address);
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    if($omni_return_city == NULL ) {
                                        $check_city_id = City::where('id',$shipment->pickup_address->city->id)->select('hub_id');
                                        if($check_city_id->exists())
                                        {
                                            $check_city_id = $check_city_id->first();
                                            $selected_hub_id = $check_city_id->hub_id;
                                        }
                                        else
                                        {
                                            $selected_hub_id = $shipment->pickup_address->city->id;
                                        }
                                    } else {
                                        $selected_hub_id = $omni_return_city_hub;
                                    }
                                    
                                    if ($shipment->booking_type_id == 1) {
                                        if (($selected_hub_id == Auth::user()->default_hub_id) || (in_array($selected_hub_id,$admin_assigned_hubs))  || ($omni_return_city_hub == $default_hub_id) || (in_array($omni_return_city_hub,$admin_assigned_hubs))) {
                                            $shipper_status_id = 22;
                                            $consignee_status_id = 22;
                                        } else {
                                            $shipper_status_id = 75;
                                            $consignee_status_id = 75;
                                        }
                                    } else if ($shipment->booking_type_id == 2) {
                                        if ($shipment->shipper_status_id == 21) {
                                            if (($selected_hub_id == Auth::user()->default_hub_id) || (in_array($selected_hub_id,$admin_assigned_hubs))  || ($omni_return_city_hub == $default_hub_id) || (in_array($omni_return_city_hub,$admin_assigned_hubs)) ) {
                                                $shipper_status_id = 22;
                                                $consignee_status_id = 22;
                                            } else {
                                                $shipper_status_id = 75;
                                                $consignee_status_id = 75;
                                            }
                                        } else {
                                            if (($selected_hub_id == Auth::user()->default_hub_id) || (in_array($selected_hub_id,$admin_assigned_hubs)) || ($omni_return_city_hub == $default_hub_id) || (in_array($omni_return_city_hub,$admin_assigned_hubs))) {
                                                if(in_array($shipment->shipper_status_id, [30,26,72,73])) {
                                                    $shipper_status_id = 27;
                                                    $consignee_status_id = 27;
                                                } else {
                                                    $shipper_status_id = 22;
                                                    $consignee_status_id = 22;
                                                }
                                                
                                            } else {
                                                if(in_array($shipment->shipper_status_id, [30,26,72,73])) {
                                                    $shipper_status_id = 72;
                                                    $consignee_status_id = 72;
                                                } else {
                                                    $shipper_status_id = 75;
                                                    $consignee_status_id = 75;
                                                }
                                            }
                                        }
                                    } else if ($shipment->booking_type_id == 3) {
                                        if (($selected_hub_id == Auth::user()->default_hub_id) || (in_array($selected_hub_id,$admin_assigned_hubs)) || ($omni_return_city_hub == $default_hub_id) || (in_array($omni_return_city_hub,$admin_assigned_hubs))) {
                                            if(in_array($shipment->shipper_status_id, [32,37,69,70])) {
                                                $shipper_status_id = 33;
                                                $consignee_status_id = 33;
                                            } else {
                                                $shipper_status_id = 22;
                                                $consignee_status_id = 22;
                                            }
                                        } else {
                                            if(in_array($shipment->shipper_status_id, [32,37,69,70])) {
                                                $shipper_status_id = 69;
                                                $consignee_status_id = 69;
                                            } else {
                                                $shipper_status_id = 75;
                                                $consignee_status_id = 75;
                                            }
                                        }
                                    } else if ($shipment->booking_type_id == 4) {
                                        if (($selected_hub_id == Auth::user()->default_hub_id) || (in_array($selected_hub_id,$admin_assigned_hubs)) || ($omni_return_city_hub == $default_hub_id) || (in_array($omni_return_city_hub,$admin_assigned_hubs))) {
                                            $shipper_status_id = 22;
                                            $consignee_status_id = 22;
                                        } else {
                                            $shipper_status_id = 75;
                                            $consignee_status_id = 75;
                                        }
                                    } else {
                                        if (($selected_hub_id == Auth::user()->default_hub_id) || (in_array($selected_hub_id,$admin_assigned_hubs)) || ($omni_return_city_hub == $default_hub_id) || (in_array($omni_return_city_hub,$admin_assigned_hubs))) {
                                            $shipper_status_id = 22;
                                            $consignee_status_id = 22;
                                        } else {
                                            $shipper_status_id = 75;
                                            $consignee_status_id = 75;
                                        }
                                    }
                                }
                                $shipment->shipper_status_id = $shipper_status_id;
                                $shipment->consignee_status_id = $consignee_status_id;

                                $shipment->save();

                                if ($shipper_status_id != 75 || $shipper_status_id != 72 || $shipper_status_id != 69)
                                    array_push($shipment_ids_array, $shipment->tracking_number);

                                if ($shipper_status_id == 75 || $shipper_status_id == 72 || $shipper_status_id == 69 )
                                    array_push($shipment_ids_array_misrouted, $shipment->tracking_number);
                                    MisroutedHistory::create([
                                        'shipment_id' => $shipment->id,
                                        'old_consignee_city_id' => Auth::user()->default_hub_id,
                                        'old_consignee_name' => $shipment->consignee_name,
                                        'old_consignee_address' => $shipment->consignee_address,
                                        'old_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                                        'old_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                                        'old_consignee_email' => $shipment->consignee_email,
                                        'new_consignee_city_id' => $shipment->consignee_city_id,
                                        'new_consignee_name' => $shipment->consignee_name,
                                        'new_consignee_address' => $shipment->consignee_address,
                                        'new_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                                        'new_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                                        'new_consignee_email' => $shipment->consignee_email,
                                        'admin_id' => Auth::id()
                                    ]);

                                ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $consignee_status_id, NULL, NULL, NULL, Auth::id());

                                if (!in_array($bag->id, $bag_ids)) {
                                    $bag_ids[] = $bag->id;
                                }
                            } else {
                                array_push($shipments_already_marked_received_array, $shipment->tracking_number);
                            }
                        }
                    } else {
                        
                        $shipment = Shipment::find($shipment_id);
                        if($omni_return_city == NULL) {
                            $origin = $shipment->pickup_address->city->id;

                            $check_city_id = City::where('id',$origin)->select('hub_id');
                            if($check_city_id->exists())
                            {
                                $check_city_id = $check_city_id->first();
                                $selected_hub_id = $check_city_id->hub_id;
                            }
                            else
                            {
                                $selected_hub_id = $origin;
                            }
                        } else {
                            $selected_hub_id = $omni_return_city_hub;
                        }
                        
                        if (($selected_hub_id == $default_hub_id) || (in_array($selected_hub_id,$admin_assigned_hubs)) || ($omni_return_city_hub == $default_hub_id) || (in_array($omni_return_city_hub,$admin_assigned_hubs))) // wisevarsa -> pickupaddress id
                        {
                            // $shipment->shipper_status_id = 22;
                            // $shipment->consignee_status_id = 22;
                            // $shipment->save();
                            
                            if($shipment->booking_type_id == 2) {
                                if(in_array($shipment->shipper_status_id, [30,27,72])) {
                                    $shipper_status_id = 27;
                                    $consignee_status_id = 27;
                                    $without_manifest = 74;
                                } else {
                                    $shipper_status_id = 22;
                                    $consignee_status_id = 22;
                                    $without_manifest = 77;
                                }
                            } elseif($shipment->booking_type_id == 3) {
                                if(in_array($shipment->shipper_status_id, [32,37,69,70])) {
                                    $shipper_status_id = 33;
                                    $consignee_status_id = 33;
                                    $without_manifest = 71;
                                } else {
                                    $shipper_status_id = 22;
                                    $consignee_status_id = 22;
                                    $without_manifest = 77;
                                }
                            } else {
                                $shipper_status_id = 22;
                                $consignee_status_id = 22;
                                $without_manifest = 77;
                            }
                            $shipment->shipper_status_id = $shipper_status_id;
                            $shipment->consignee_status_id = $consignee_status_id;
                            $shipment->save();
                            
                            ShipmentsJourneyController::add($shipment_id, $without_manifest, $without_manifest, NULL, NULL, NULL, Auth::id()); // without manifest status
                            ShipmentsJourneyController::add($shipment_id, $consignee_status_id, $consignee_status_id, NULL, NULL, NULL, Auth::id()); // received at destination
                            array_push($shipment_ids_array, $shipment->tracking_number);
                        }
                        else
                        {
                            // $shipment->shipper_status_id = 75;
                            // $shipment->consignee_status_id = 75;
                            // $shipment->save();
                            if($shipment->booking_type_id == 2) {
                                if(in_array($shipment->shipper_status_id, [30,27,72])) {
                                    $shipper_status_id = 72;
                                    $consignee_status_id = 72;
                                    $without_manifest = 74;
                                } else {
                                    $shipper_status_id = 75;
                                    $consignee_status_id = 75;
                                    $without_manifest = 77;
                                }
                            } elseif($shipment->booking_type_id == 3) {
                                if(in_array($shipment->shipper_status_id, [32,37,69])) {
                                    $shipper_status_id = 69;
                                    $consignee_status_id = 69;
                                    $without_manifest = 71;
                                } else {
                                    $shipper_status_id = 75;
                                    $consignee_status_id = 75;
                                    $without_manifest = 77;
                                }
                            } else {
                                $shipper_status_id = 75;
                                $consignee_status_id = 75;
                                $without_manifest = 77;
                            }
                            $shipment->shipper_status_id = $shipper_status_id;
                            $shipment->consignee_status_id = $consignee_status_id;
                            $shipment->save();

                            $last_scanned = ShipmentsJourney::where('shipment_id', $shipment_id)->select('city_id');
                            if ($last_scanned->exists()) {
                                $last_scanned = $last_scanned->latest()->take(1)->first();
                                $remarks = 'Misrouted from ' . $last_scanned->city->name . ' to ' . $default_hub_name . '.';
                            } else {
                                $remarks = '--';
                            }
                            ShipmentsJourneyController::add($shipment_id, $without_manifest, $without_manifest, NULL, NULL, NULL, Auth::id()); // without manifest status
                            ShipmentsJourneyController::add($shipment_id, $consignee_status_id, $consignee_status_id, NULL, $remarks, NULL, Auth::id());// new misrouted
                            array_push($shipment_ids_array_misrouted, $shipment->tracking_number);
                            MisroutedHistory::create([
                                'shipment_id' => $shipment->id,
                                'old_consignee_city_id' => Auth::user()->default_hub_id,
                                'old_consignee_name' => $shipment->consignee_name,
                                'old_consignee_address' => $shipment->consignee_address,
                                'old_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                                'old_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                                'old_consignee_email' => $shipment->consignee_email,
                                'new_consignee_city_id' => $shipment->consignee_city_id,
                                'new_consignee_name' => $shipment->consignee_name,
                                'new_consignee_address' => $shipment->consignee_address,
                                'new_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                                'new_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                                'new_consignee_email' => $shipment->consignee_email,
                                'admin_id' => Auth::id()
                            ]);
                        }
                    }
                }
//                Log::channel('cronJobLog')->info('cargo:check_4');
            }

            // received and short received
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

                // shipment in bag but bag not in manifest
                $manifest_bag = ManifestBag::where('cargo_manifest_bag_id', $bag->id);
                if ($manifest_bag->exists()) {
                    $manifest_bag = $manifest_bag->latest()->first();
                    $manifest_bag->status = 1;
                    $manifest_bag->save();
                }
                // shipment in bag but bag not in manifest end

                //dispute for short received
                if ($bag->status_id == 8) {
                    $bag_short_received_shipments = CargoManifestBagShipments::where(['cargo_manifest_bag_id' => $bag_id, 'status' => 0])->pluck('shipment_id')->toArray();
                    //dd($bag_short_received_shipments);
                    if (!empty($bag_short_received_shipments)) {
                        DisputeController::add_cargo_short_received($bag->seal_number, $bag_short_received_shipments, null, 2);
                    }
                }

                //end dispute short received
                /* if($all_bag_ids == ''){
                 $all_bag_ids = $all_bag_ids . $bag->seal_number;
             }
             else{
                 $all_bag_ids = $all_bag_ids . ', ' .$bag->seal_number;
             }*/
            }
//            Log::channel('cronJobLog')->info('cargo:check_5');
            foreach ($bag_ids as $bag_id) {
                $manifest_bag = ManifestBag::where('cargo_manifest_bag_id', $bag->id)->latest()->first();
                if (!empty($manifest_bag)) {
                    $bag = CargoManifestBag::find($bag_id);
                    $manifest_id = $manifest_bag->cargo_manifest_id; //
                    $manifest = CargoManifest::find($manifest_id); //
                    $manifest->received_bags = ManifestBag::where('cargo_manifest_id', $manifest_id)->where('status', 1)->count(); //
                    $short_received_bags = ManifestBag::where('cargo_manifest_id', $manifest_id)->where('status', 0)->count(); //

                    if ($short_received_bags > 0) {
                        $manifest->short_received_bags = $short_received_bags;
                    } else {
                        $manifest->short_received_bags = 0;
                        $manifest->status_id = 2;
                    }

                    $manifest->update();
                }
            }
            // received and short received end
//            Log::channel('cronJobLog')->info('cargo:check_6');
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
//            Log::channel('cronJobLog')->info('cargo:check_7');
            //remove misrouted shipment ids from $short_received_shipments_array
            $exclude_from_short_received = Shipment::whereIn('tracking_number',$short_received_shipments_array)->whereIn('shipper_status_id',[11,68,69,72,75])->pluck('tracking_number')->toArray();
            $short_received_shipments_array = array_diff($short_received_shipments_array, $exclude_from_short_received);
            //remove misrouted shipment ids from $short_received_shipments_array end

            $received_html = '';
            $rm_html = '';
            $sr_html = '';
            $already_received_shipments_html = '';
            $misrouted_html = '';

            if (count($short_received_shipments_array) > 0) {
                $sr_html = "Following Shipments(s) are marked as short received.<br><ul>";
                foreach ($short_received_shipments_array as $v) {
                    $sr_html .= "<li>" . $v . "</li>";
                }
                $sr_html .= "</ul>";
            }
//            Log::channel('cronJobLog')->info('cargo:check_8');
            if (count($shipments_already_marked_received_array) > 0) {
                $already_received_shipments_html = "Following Shipments(s) are already marked as received or processed .<br><ul>";
                foreach ($shipments_already_marked_received_array as $v) {
                    $already_received_shipments_html .= "<li>" . $v . "</li>";
                }
                $already_received_shipments_html .= "</ul>";
            }
//            Log::channel('cronJobLog')->info('cargo:check_9');
            if (count($shipment_ids_array) > 0) {
                $received_html = "Following Shipments(s) are marked as received. .<br><ul>";
                foreach ($shipment_ids_array as $v) {
                    $received_html .= "<li>" . $v . "</li>";
                }
                $received_html .= "</ul>";
            }
//            Log::channel('cronJobLog')->info('cargo:check_10');
            if (count($shipment_ids_array_misrouted) > 0) {
                $misrouted_html = "Following Shipments(s) are marked as misrouted.<br><ul>";
                foreach ($shipment_ids_array_misrouted as $m) {
                    $misrouted_html .= "<li>" . $m . "</li>";
                }
                $misrouted_html .= "</ul>";
            }
            if (count($shipmentNoReceive) > 0) {
                $rm_html = "The following shipments are not marked as received in the bag because they are currently out for delivery.<br><ul>";
                foreach ($shipmentNoReceive as $m) {
                    $rm_html .= "<li>" . $m . "</li>";
                }
                $rm_html .= "</ul>";
            }
//            Log::channel('cronJobLog')->info('cargo:check_11');
            DB::commit();
//            Log::channel('cronJobLog')->info('cargo:completed');
            //return redirect()->back()->with('success', 'Selected Shipments of Bag Number(s)#' . $all_bag_ids . ' has been Received');
            return back()->with(['sr_html' => $sr_html, 'received_html' => $received_html, 'already_received_shipments_html' => $already_received_shipments_html, 'misrouted_html' => $misrouted_html,'rm_html'=> $rm_html]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::channel('cronJobLog')->error('cargo:failed'.json_encode($th->getMessage()), ['trace' => json_encode($th->getTraceAsString())]);
            return back()->with(['went_wrong' => 'Something Went Wrong']);
    }}

    public function receive_bag_shipments_store_old(Request $request)
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

                            $self_collection = SelfCollectionShipment::where('shipment_id', $shipment_id)->first();

                            if ($self_collection) {
                                $consignee_city = $shipment->consignee_city_id;
                                $user_city = $shipment->user->city_id;

                                if ($consignee_city == '202' || $consignee_city == '223') {
                                    NotificationsController::send(178, $shipment_id);
                                } else {
                                    $city_id = SelfCollectionCities::where('city_id', $consignee_city)->select('city_id', 'address')->first();
                                    if ($city_id) {
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
            ->select('cargo_manifest_draft_bags.bag_id as bag_id', 'cargo_manifest_draft_bags.seal_number as bag_number', 'cargo_manifest_draft_bags.shipments_count', 'd.name as destination', 'c.name as origin', 'cargo_manifest_draft_bags.pieces_count', 'cargo_manifest_draft_bags.remarks')
            ->where('cargo_manifest_draft_bags.added_by', Auth::id())
            ->orderby('cargo_manifest_draft_bags.created_at', 'desc');

        return Datatables::of($draft)
            ->editColumn('remarks', function ($shipments) {
                $remarks = '<textarea maxlength="250" type="text" id="remarks" ref="' . $shipments->bag_id . '" class="form-control form-control-sm remarks">' . $shipments->remarks . '</textarea>';
                return $remarks;
            })
            ->addColumn('action', function ($shipments) {
                $dropdown = '<a href="javascript:void(0);" class="btn btn-icon btn-danger bag_remove"><i class="la la-close"></i></a>';
                return $dropdown;
            })
            ->rawColumns(['remarks','action'])
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
            if (count($draft_bags) > 0) {
                foreach ($draft_bags as $draft_bag) {
                    if (!in_array($draft_bag->destination_id, $bag_destinations)) {
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

    public function manifest_draft_setting()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 509);
        $users = Admin::join('admin_roles as ar', 'ar.id', '=', 'admins.role_id')
            ->select(['admins.id', 'admins.trax_id', 'admins.name'])
            ->where('ar.department_id', 6)
            ->where('admins.status', 1);

        if (session('role_id') != 1) {
            $users = $users->where('default_hub_id', Auth::user()->default_hub_id);
        }

        $users = $users->get();
        return view('admin.cargo.manifest.draft_setting', compact('users'));
    }

    public function manifest_draft_setting_list(Request $request)
    {
        $cargo = CargoManifestDraftBags::join('cities as o', 'cargo_manifest_draft_bags.origin_id', '=', 'o.id')
            ->join('cities as d', 'cargo_manifest_draft_bags.destination_id', '=', 'd.id')
            ->join('admins as u', 'cargo_manifest_draft_bags.added_by', '=', 'u.id')
            ->select('cargo_manifest_draft_bags.id as id', 'cargo_manifest_draft_bags.seal_number as seal_number', 'cargo_manifest_draft_bags.shipments_count as shipment_count', 'o.id as origin_id', 'o.name as origin', 'd.id as destination_id', 'd.name as destination', 'u.name as assigned_to', 'cargo_manifest_draft_bags.created_at as created_at');


        if (session('role_id') != 1) {
            $cargo->where('cargo_manifest_draft_bags.origin_id', Auth::user()->default_hub_id);
        }

        $datatables = Datatables::of($cargo);

        return $datatables->make(true);
    }

    public function manifest_draft_update(Request $request)
    {
        $request->validate([
            'ids' => 'required',
            'user_id' => 'required',
        ]);

        $ids = explode(',', $request->ids);
        CargoManifestDraftBags::whereIn('id', $ids)->update(['added_by' => $request->user_id]);
        return back()->with(['success' => 'Bag Transfered Successfully']);
    }

    public function remarks_info(Request $request)
    {
        if ($request->bag_id) {
            $remarks = CargoManifestBagRemarks::join('admins as a', 'a.id', 'cargo_manifest_bag_remarks.added_by')
                ->leftjoin('cargo_manifest_bags as cmb', 'cmb.id', 'cargo_manifest_bag_remarks.bag_id')
                ->where('cargo_manifest_bag_remarks.bag_id', $request->bag_id)
                ->select(['cargo_manifest_bag_remarks.*', 'a.name as added_by_name', 'cmb.seal_number'])
                ->orderby('cargo_manifest_bag_remarks.id', 'desc');
            if ($remarks->exists()) {
                $remarks = $remarks->get();
                return $remarks;
            } else {
                return [];
            }
        } else {
            return [];
        }
    }

    public function sack_bag_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 739);
        $origins = City::where('status', '=', 1)->get();
        return view('admin.cargo.manifest.bags.sack_bag', compact('origins'));
    }

    public function sack_bag_list(Request $request)
    {

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 740);
        }
        $issuebag = IssueSackBagOrigin::join('cities as c', 'c.id', '=', 'issue_sack_bag_origins.origin')
            ->join('admins as ad', 'ad.id', '=', 'issue_sack_bag_origins.user_id')
            ->select(
                'issue_sack_bag_origins.sack_bag_no as sack_bag_no', 
                'c.name as origin', 
                'ad.name as user_id',
                'issue_sack_bag_origins.remarks',
                'issue_sack_bag_origins.created_at as active_time',
                'issue_sack_bag_origins.inactive_at as inactive_time',
                'issue_sack_bag_origins.inactive_by as inactive_by',
                'issue_sack_bag_origins.status as status',
                'issue_sack_bag_origins.id as sack_bag_id',
                'issue_sack_bag_origins.active_by as sack_bag_active_by',
                'issue_sack_bag_origins.inactive_by as sack_bag_inactive_by',
                'issue_sack_bag_origins.updated_at as new_active_time',
            );

        $datatables = Datatables::of($issuebag)
        ->editColumn('status', function ($sack_bag){
            $status = '-';
            if ($sack_bag->status == 1) {
                $status = "Active";
            } else {
                $status = "Inactive";
            }
            return $status;
        })

        ->editColumn('user_id', function ($sack_bag) {
            $user = '-';
            if ($sack_bag->sack_bag_active_by == null && $sack_bag->sack_bag_inactive_by == null) {
                $user = $sack_bag->user_id;
            } else if ($sack_bag->sack_bag_active_by != null) {
                $user = optional(Admin::find($sack_bag->sack_bag_active_by))->name ?? '-';
            } else if ($sack_bag->sack_bag_inactive_by != null) {
                $user = optional(Admin::find($sack_bag->sack_bag_inactive_by))->name ?? '-';
            }        
            return $user;
        })

        ->editColumn('remarks', function ($sack_bag){
            $remarks = '-';
            if ($sack_bag->remarks != null) {
                $remarks = $sack_bag->remarks; 
            }
            return $remarks;
        })
        
        ->editColumn('inactive_time', function ($sack_bag){
            $inactive_time = '-';
            if ($sack_bag->inactive_time != null) {
                $inactive_time = $sack_bag->inactive_time; 
            }
            return $inactive_time;
        })

        ->editColumn('active_time', function ($sack_bag) {
            if ($sack_bag->status == 0) {
                return '-';
            }
            if ($sack_bag->new_active_time !== null) {
                return $sack_bag->new_active_time;
            }
            return $sack_bag->active_time ?? '-';
        })
        

        ->addColumn('action', function ($sack_bag) {
            $dropdown = '
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';

            if ($sack_bag->status == 1) {
                $dropdown .= '<button type="button" class="dropdown-item status_sack_bag" data-id="' . $sack_bag->sack_bag_id . '" data-status="0">
                                <div class="row no-gutters align-items-center">
                                    <div class="col-2"><i class="ft-x-circle"></i></div>
                                    <div class="col-9 offset-1">Inactive</div>
                                </div>
                            </button>';
            } elseif ($sack_bag->status == 0) {
                $dropdown .= '<button type="button" class="dropdown-item status_sack_bag" data-id="' . $sack_bag->sack_bag_id . '" data-status="1">
                                <div class="row no-gutters align-items-center">
                                    <div class="col-2"><i class="ft-check-circle"></i></div>
                                    <div class="col-9 offset-1">Active</div>
                                </div>
                            </button>';
            }
        
            $dropdown .= '</div>
                </div>';
        
            return $dropdown;
        })
        ->rawColumns(['action'])
        ;

        return $datatables->make(true);
    }

    public function sack_bag_no_check(Request $request)
    {
        if ($sack_bag_no = $request->get('sack_bag_no')) {

            $sack_bag = IssueSackBagOrigin::where('sack_bag_no', $sack_bag_no);
            if ($sack_bag->exists()) {
                return response()->json(['error' => 'Sack-Bag No: ' . $sack_bag_no . ' already exist ']);
            }
            return response()->json(['success' => 'Sack-Bag No# available']);
        } else {
            return response()->json(['error' => 'Sack-Bag No# not found ']);
        }
    }

    public function update_sack_bag_status(Request $request)
    {
        $id = $request->get('id');
        $status = $request->get('status');
        $auth_user = Auth::user();    

        if (!$id || is_null($status)) {
            return response()->json(['error' => 'Invalid ID or Status provided.']);
        }

        $sack_bag = IssueSackBagOrigin::find($id);
        if (!$sack_bag) {
            return response()->json(['error' => 'Sack-Bag not found.']);
        }

        $new_status = (int)$status;
        $current_status = $sack_bag->status;

        $sack_bag->status = $new_status;

        // Always update the timestamp manually
        $sack_bag->updated_at = Carbon::now();

        if ($new_status === 0) {
            // Deactivating
            $sack_bag->inactive_at = Carbon::now();
            $sack_bag->inactive_by = $auth_user->id;
        } elseif ($current_status === 0 && $new_status === 1) {
            // Reactivating (do not clear inactive_at/by)
            $sack_bag->active_by = $auth_user->id;
        }

        // Prevent Laravel from auto-managing timestamps
        $sack_bag->timestamps = false;
        $sack_bag->save();

        return response()->json(['success' => 'Sack-Bag status updated successfully.']);
    }

    //sack_bag_no check during bag creation
    public function sack_bag_no_check_for_cb(Request $request)
    {
        if ($sack_bag_no = $request->get('sack_bag_no')) {

            $sack_bag = IssueSackBagOrigin::where('sack_bag_no', $sack_bag_no);
            if ($sack_bag->exists()) {
                return response()->json(['success' => 'Sack-Bag No# available']);
            } else {
                return response()->json(['error' => 'Sack-Bag not found ']);
            }
        } else {
            return response()->json(['error' => 'Sack-Bag No# not found ']);
        }
    }

    public function add_sack_bag(Request $request)
    {
        $user_id = session('id');
        $sackbag_array = array();

        if (is_array($request->sack_bag_no)) {

            $timestamp = Carbon::now();
            foreach ($request->sack_bag_no  as  $key => $data) {
                if (!is_null($data)) {
                    $sack_bag = IssueSackBagOrigin::where('sack_bag_no', $data);
                    if (!$sack_bag->exists()) {
                        array_push($sackbag_array, ['sack_bag_no' => $data, 'origin' => $request->origin, 'sack_destination_id' => $request->origin, 'remarks' => $request->remarks[$key], 'user_id' => $user_id, 'type' => 1, 'created_at' =>  $timestamp, 'updated_at' =>  $timestamp, 'reporting_date' =>  $timestamp]);
                    }
                }
            }

            try {
                if (!empty($sackbag_array)) {
                    IssueSackBagOrigin::insert($sackbag_array);
                    return redirect()->route('admin.cargo_manifest.bags.sack_bag.index')->with(['success' => 'Sack Bag Added Successfully']);
                }
                return redirect()->route('admin.cargo_manifest.bags.sack_bag.index')->with(['error' => 'Sack Bag Not Added']);
            } catch (\Exception $e) {
                return redirect()->route('admin.cargo_manifest.bags.sack_bag.index')->with(['error' => 'Sack Bag Not Added']);
            }
        } else {
            return redirect()->route('admin.cargo_manifest.bags.sack_bag.index')->with(['error' => 'Sack Bag Not Added: Empty Data']);
        }
    }

    
}
