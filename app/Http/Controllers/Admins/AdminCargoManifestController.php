<?php

namespace App\Http\Controllers\Admins;


use App\Http\Models\Admin\CargoManifest\CargoManifest;
use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagStatus;
use App\Http\Models\Admin\CargoManifest\V2JunctionMapping;
use App\Http\Models\Admin\CargoManifest\V2JunctionRoutes;
use App\Http\Models\Admin\CargoManifest\V2Junctions;
use App\Http\Models\Admin\CargoManifest\V2JunctionVehicles;
use App\Http\Controllers\CargoManifestBagJourneyController;
use App\Http\Controllers\ShipmentOpenBoxJourneyController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\Fleet;
use App\http\Models\Admin\ShipmentOnHold;
use App\Http\Models\BookingType;
use App\Http\Models\City;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\Shipment;
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

class AdminCargoManifestController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function manifest_mapping_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),396);
        $cities = City::select(['id', 'name'])->where('business_category_id', 1)->where('hub', 1)->get();
        $vehicles = Fleet::where('status', 1)->select(['id', 'reg_number'])->get();
        return view('admin.cargo.manifest.mapping')->with(['cities' => $cities, 'vehicles' => $vehicles]);
    }

    public function manifest_mapping_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),397);
        }

        $mapping =  V2JunctionMapping::join('cities as oc','oc.id', '=', 'v2_junction_mappings.origin_id')
            ->join('cities as dc','dc.id', '=', 'v2_junction_mappings.destination_id')
            ->join('admins as a','a.id', '=', 'v2_junction_mappings.updated_by')
            ->select('v2_junction_mappings.id as id','v2_junction_mappings.status as status', 'v2_junction_mappings.updated_at as updated_at','oc.name as origin','oc.hub_location_latitude as ohllat','oc.hub_location_longitude as ohllng','dc.name as destination','dc.hub_location_latitude as dhllat','dc.hub_location_longitude as dhllng', 'a.name as updated_by');

        return Datatables::of($mapping)
            ->addColumn('origin_display',function($mapping){
                return "<a href='https://www.google.com/maps/?q=".$mapping->ohllat.",".$mapping->ohllng."' target='_blank' class='btn btn-sm btn-outline-info align-middle'><i class='ft-map-pin'></i></a> ".$mapping->origin;
            })
            ->addColumn('destination_display',function($mapping){
                return "<a href='https://www.google.com/maps/?q=".$mapping->dhllat.",".$mapping->dhllng."' target='_blank' class='btn btn-sm btn-outline-info align-middle'><i class='ft-map-pin'></i></a> ".$mapping->destination;
            })
            ->addColumn('junctions_display',function($mapping){
                $junctions = V2Junctions::where('junction_mapping_id',$mapping->id)
                    ->join('cities as j','j.id', '=', 'v2_junctions.junction_id')
                    ->select(['j.name as junction','j.hub_location_latitude as jhllat','j.hub_location_longitude as jhllng'])
                    ->get();

                $junction_data = '';
                foreach ($junctions as $j)
                {
                    $junction_data.= "<a href='https://www.google.com/maps/?q=".$j->jhllat.",".$j->jhllng."' target='_blank' class='btn btn-sm btn-outline-info align-middle'><i class='ft-map-pin'></i></a> ".$j->junction ."<br><br>";
                }

                return $junction_data;
            })
            ->addColumn('junctions',function($mapping){
                $junctions = V2Junctions::where('junction_mapping_id',$mapping->id)
                    ->join('cities as j','j.id', '=', 'v2_junctions.junction_id')
                    ->select(['j.name as junction','j.hub_location_latitude as jhllat','j.hub_location_longitude as jhllng'])
                    ->get();

                $junction_data = '';
                foreach ($junctions as $j)
                {
                    $junction_data.= $j->junction.",";
                }

                $junction_data = substr(trim($junction_data), 0, -1);
                if(!$junction_data)
                {
                    $junction_data = "";
                }
                return $junction_data;
            })
            ->editColumn('status',function ($mapping){
                if($mapping->status == 1)
                {
                    return "Enabled";
                }
                else{
                    return "Disabled";
                }
            })
            ->addColumn('action',function ($mapping) {
                $dropdown = "";

                if (session('role_id') == 1 || count(array_intersect([549, 550], session('permissions'))) !== 0) {
                    $dropdown .= '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">';

                    if (session('role_id') == 1 || in_array(549, session('permissions'))) {
                        $edit_route = route('admin.cargo.mapping.manifest.edit',$mapping->id);
                        $dropdown .= '<a href="'.$edit_route.'" class="dropdown-item edit_mapping"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></div></a>';
                    }
                    if (session('role_id') == 1 || in_array(550, session('permissions'))) {
                        if($mapping->status == 1)
                        {
                            $text = "Disable";
                        }
                        else{
                            $text = "Enable";
                        }
                        $dropdown .= '<button type="button" class="dropdown-item status_mapping"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">'.$text.'</div></div></button>';
                    }
                    $dropdown .= '</div>
                        </div>
                    ';
                }

                return $dropdown;
            })
            ->make(true);
    }

    public function manifest_mapping_store(Request $request){

        if(count($request->junctions) > 1 && $request->junctions[1] == null)
        {
            return redirect()->back()->with('error', 'Please Select Junction 1');
        }

        $check = V2JunctionMapping::where([['origin_id', $request->origin], ['destination_id' , $request->destination],['status',1]]);
        if($check->exists()) {
            return redirect()->back()->with('error', 'Mapping against these hubs already exists!');
        }

        $mapping = new V2JunctionMapping();

        $mapping->origin_id = $request->origin;
        $mapping->destination_id = $request->destination;
        $mapping->updated_by = Auth::id();
        $mapping->save();

        foreach ($request->junctions as $j) {
            if($j != null) {
                $junction = new V2Junctions();
                $junction->junction_mapping_id = $mapping->id;
                $junction->junction_id = $j;
                $junction->save();
            }
        }

        $previous = $request->origin;
        foreach ($request->route_junctions as $key => $rj)
        {
            $route_junction = new V2JunctionRoutes();
            $route_junction->junction_mapping_id = $mapping->id;
            $route_junction->starting_hub_id = $previous;
            $route_junction->ending_hub_id = $rj;
            $route_junction->save();

            $previous = $rj;

            foreach ($request->vehicles[$key] as $vehicle)
            {
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
        if($mapping->doesntExist())
        {
            return response()->json(['status'=>0,'error'=>'Mapping Doesn\'t Exists..']);
        }

        $mapping = $mapping->first();

        if($mapping->status == 1)
        {
            $mapping->status = 0;
            $mapping->updated_by = Auth::id();
            $mapping->update();
            return response()->json(['status'=>1,'success'=>'Mapping Disabled Successfully..']);
        }
        else if($mapping->status == 0)
        {
            $mapping->status = 1;
            $mapping->updated_by = Auth::id();
            $mapping->update();
            return response()->json(['status'=>1,'success'=>'Mapping Enabled Successfully..']);
        }
        else{
            return response()->json(['status'=>0,'error'=>'Invalid Status..']);
        }
    }

    public function manifest_mapping_edit($id){

        $mapping = V2JunctionMapping::where('id', $id);
        if($mapping->doesntExist())
        {
            return back()->with(['error'=>'Invalid Mapping ID']);
        }

        $mapping = $mapping->with(['junctions','routes','routes.vehicles','routes.starting','routes.ending'])
            ->first();
        $cities = City::select(['id', 'name'])->where('business_category_id', 1)->where('hub', 1)->get();
        $vehicles = Fleet::where('status', 1)->select(['id', 'reg_number'])->get();
        return view('admin.cargo.manifest.edit')->with(['mapping' => $mapping,'cities'=>$cities,'vehicles'=>$vehicles]);
    }

    // not done
    public function manifest_mapping_edit_update($id,Request $request){
        if(count($request->junctions) > 1 && $request->junctions[1] == null)
        {
            return redirect()->back()->with('error', 'Please Select Junction 1');
        }

        $check = V2JunctionMapping::where([['origin_id', $request->origin], ['destination_id' , $request->destination],['status',1],['id','!=',$id]]);
        if($check->exists()) {
            return redirect()->back()->with('error', 'Mapping against these hubs already exists!');
        }

        $mapping = V2JunctionMapping::find($id);

        if(!$mapping)
        {
            return redirect()->back()->with('error', 'Mapping ot found!');
        }

        $mapping->origin_id = $request->origin;
        $mapping->destination_id = $request->destination;
        $mapping->updated_by = Auth::id();
        $mapping->update();

        $mapping->junctions()->delete();
        foreach ($mapping->routes as $routes)
        {
            $routes->vehicles()->delete();
        }
        $mapping->routes()->delete();

        foreach ($request->junctions as $j) {
            if($j != null) {
                $junction = new V2Junctions();
                $junction->junction_mapping_id = $mapping->id;
                $junction->junction_id = $j;
                $junction->save();
            }
        }

        $previous = $request->origin;
        foreach ($request->route_junctions as $key => $rj)
        {
            $route_junction = new V2JunctionRoutes();
            $route_junction->junction_mapping_id = $mapping->id;
            $route_junction->starting_hub_id = $previous;
            $route_junction->ending_hub_id = $rj;
            $route_junction->save();

            $previous = $rj;

            foreach ($request->vehicles[$key] as $vehicle)
            {
                $route_vehicle = new V2JunctionVehicles();
                $route_vehicle->junction_route_id = $route_junction->id;
                $route_vehicle->vehicle_id = $vehicle;
                $route_vehicle->save();
            }
        }

        return redirect()->back()->with('success', 'Mapping added successfully.');
    }

    public function pending_bag_index() {

        ActivityTrailController::createActivityTrailLog(Auth::id(),398);
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $service_type = BookingType::all();
        $shipping_mode = ShippingMode::all();
        return view('admin.cargo.manifest.bags.pending')->with(['shipment_status'=>$shipment_status,'service_type'=>$service_type,'shipping_mode'=>$shipping_mode]);
    }

    public function pending_bag_list(Request $request) {

        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),399);
        }
        $today = Carbon::today();
        $on_hold_shipments = ShipmentOnHold::whereDate('dispatch_date', '>', $today)->where('status', 1)->pluck('shipment_id')->toArray();
        $shipments = Shipment::join('booking_types as bt', 'shipments.booking_type_id', '=', 'bt.id')
            ->join('shipment_status as ss', 'shipments.shipper_status_id', '=', 'ss.id')
            ->join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
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
        ActivityTrailController::createActivityTrailLog(Auth::id(),400);
        return view('admin.cargo.manifest.bags.create')->with('print', session('print'));
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

        $origin_details['id'] = Auth::user()->default_hub_id;
        $origin_details['name'] = $origin->name;

        $destination = $shipment->consignee_city->hub_city;

        $destination_details = array();

        $destination_details['id'] = $destination->id;
        $destination_details['name'] = $destination->name;

        $details = array();

        $details['origin'] = $origin_details;
        $details['destination'] = $destination_details;
        $details['actual_weight'] = 0;

        foreach ($request->shipment_ids as $shipment_id){
            $shipment_actual_weight = Shipment::find($shipment_id);
            $details['actual_weight'] = $details['actual_weight'] + $shipment_actual_weight->actual_weight;
        }
        
         return $details;
    }
    
    public function create_bag_seal_number(Request $request) {
        if ($request->filled('seal_number')) {
            $seal_number = CargoManifestBag::where('seal_number', $request->input('seal_number'));

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

    public function create_shipment_details(Request $request) {

        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();
            if($shipment->pickup_address->city->hub_id == Auth::user()->default_hub_id || $shipment->shipper_status_id == 20) {
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
                        if ($shipment->shipper_status_id == 20) {
                            if ($shipment->return_address_id != NULL) {
                                $hub_id = $shipment->return_address->city->hub_id;
                            } else {
                                $hub_id = $shipment->consignee_city->hub_id;
                            }
                        } else {
                            $hub_id = $shipment->consignee_city->hub_id;
                        }

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
                        if (($shipment->pickup_address->city->hub_id != $shipment->consignee_city->hub_id) || (in_array($shipment->shipper_status_id, [49, 55]) && ($shipment->consignee_city->hub_id != $hub_id)) || ($shipment->shipper_status_id == 20 && $shipment->return_address_id != NULL && $shipment->pickup_address->city->hub_id != $hub_id)) {
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
//                            if ($request->shipping_mode_id == 0 || $request->shipping_mode_id == $shipment->shipping_mode->id) {
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

                                /*   $shipping_mode_id = $request->shipping_mode_id;

                                   if ($shipping_mode_id == 0) {
                                       $shipping_mode_id = $shipment->shipping_mode->id;

                                       $details['shipping_mode']['id'] = $shipment->shipping_mode->id;
                                       $details['shipping_mode']['name'] = $shipment->shipping_mode->mode;
                                   }*/

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
                                            ->whereIn('shipments.shipper_status_id', [2, 49, 55]);
                                        /*  ->where('shipments.shipping_mode_id', $shipping_mode_id);*/
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
                                            ->select(DB::raw('count(shipments.id) as count'));
                                        /*  ->where('shipments.shipping_mode_id', $shipping_mode_id);*/

                                    }

                                    if (session('role_id') != 1) {
                                        $shipments = $shipments->whereIn('oc.hub_id', session('hubs'));
                                    }

                                    $shipments = $shipments->first();

                                    $details['total'] = $shipments->count;
                                }
                                ShipmentScanningJourneyController::add($shipment->id, 2, 1, Auth::id(), null, null);

                                return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
//                            }
//                            else {
//                                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment\'s Shipment Mode is different'];
//                            }
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
            }
            else{
                return ['status' => 1, 'error' => 'Shipment belongs to another hub'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
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
            $bag->save();
          
            CargoManifestBagJourneyController::add($bag->id,$bag->seal_number, $bag->status_id, Auth::id(), NULL, NULL);

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

            return redirect()->route('admin.cargo_manifest.bags.create.index')->with(['success' => 'Bag Created with Bag Number: ' . $bag->seal_number]);
        }
        else {
            return back()->withErrors('All Shipments have already been added to another Bag!');
        }
    }

    public function history_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),401);
        $transport_mode = TransportMode::all();
        $bag_statuses = CargoManifestBagStatus::all();
        return view('admin.cargo.manifest.bags.history')->with(['bag_statuses' => $bag_statuses,'transport_mode' => $transport_mode]);
    }

    public function history_list(Request $request) {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),402);
        }
        $bags = CargoManifestBag::join('cities as oh', 'cargo_manifest_bags.origin_hub_id', '=', 'oh.id')
            ->join('cities as dh', 'cargo_manifest_bags.destination_hub_id', '=', 'dh.id')
            ->join('admins as a', 'cargo_manifest_bags.created_by', '=', 'a.id')
            ->join('cargo_manifest_bag_statuses as bs', 'cargo_manifest_bags.status_id', '=', 'bs.id')
            ->join('transport_modes as tm', 'cargo_manifest_bags.transport_mode_id', '=', 'tm.id')
            ->select('cargo_manifest_bags.id', 'cargo_manifest_bags.status_id', 'oh.id as origin_id', 'oh.name as origin', 'dh.id as destination_id', 'dh.name as destination', 'cargo_manifest_bags.shipments', 'tm.name as transport_mode','cargo_manifest_bags.shipments_weight', DB::raw('(SELECT SUM(`s`.`chargeable_weight`) FROM `shipments` AS `s` INNER JOIN `cargo_manifest_bag_shipments` AS `bss` ON `s`.`id` = `bss`.`shipment_id` WHERE `bss`.`cargo_manifest_bag_id` = `cargo_manifest_bags`.`id`) AS `chargeable_weight`'), 'cargo_manifest_bags.actual_weight', 'cargo_manifest_bags.created_at as transit_at', 'a.name as transitted_by', 'oh.hub_id as origin_hub_id', 'dh.hub_id as destination_hub_id', 'cargo_manifest_bags.type as bag_type','cargo_manifest_bags.seal_number', 'bs.name as status','cargo_manifest_bags.junction_mapping_id as junction_mapping_id');

        if (session('role_id') != 1) {
            $bags = $bags->where(function ($query) {
                $query->whereIn('oh.hub_id', session('hubs'))->orWhereIn('dh.hub_id', session('hubs'));
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
            ->addColumn('junctions', function ($bag) {
                $junctions = V2Junctions::where('junction_mapping_id',$bag->junction_mapping_id)->get();
                $junction_data = '';


                foreach ($junctions as $junction) {
                    $city = City::find($junction->junction_id);
                    $junction_data.= "<a href='https://www.google.com/maps/?q=".$city->hub_location_latitude.",".$city->hub_location_longitude."' target='_blank' class='btn btn-sm btn-outline-info align-middle'><i class='ft-map-pin'></i></a> ".$city->name ."<br><br>";

                }
                return $junction_data;
                // return $route_management->id;
            })
            ->filterColumn('cargo_manifest_bags.seal_number', function ($query, $keyword) {
                return $query->where('cargo_manifest_bags.seal_number', '=', $keyword);
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
        return view('admin.cargo.manifest.create');
    }

    public function bag_details(Request $request) {
        $bag = CargoManifestBag::where('seal_number', $request->bag_number);

        if ($bag->exists()) {
            $bag = $bag->first();

            if (in_array($bag->status_id, [1, 3, 5])) {
                $hub_id = $bag->origin_hub_id;
                $destination_hub_id = $bag->destination_hub_id;

                $allowed = FALSE;

                if (session('role_id') == 1) {
                    $allowed = TRUE;
                }
                else if (in_array($hub_id, session('hubs'))) {
                    $allowed = TRUE;
                }

                if ($allowed) {
                        $details = array();

                        $origin = $bag->origin_hub;
                        $destination = $bag->destination_hub;

                        $details['id'] = $bag->id;
                        $details['bag_number'] = $bag->seal_number;
                        $details['shipments'] = $bag->shipments;
                        $details['origin'] = $origin->name;
                        $details['destination'] = $destination->name;
                        $details['actual_weight'] = $request->bag_weight;

                        return ['status' => 0, 'success' => 'Bag has been added', 'details' => $details];
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

    public static function print($cargo_id, $type = NULL) {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $cargo = CargoManifest::find($cargo_id);


        $jucntion_names = '';
       /* if($master_cargo->route_management_id){
            foreach ($master_cargo->route_management->junctions as $value) {
                $jucntion_names .= ' - '.$value->junction['name'].' - ';
            }
        }*/
        $sender = $cargo->sender;
        $receiver = ($cargo->received_by) ? $cargo->receiver : NULL;

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
                          
                            <tr>
                              ';
      /*  if($master_cargo->onward_forwarding == 1){
            $html .= '<td class="color secondary"><strong>Onward Forwarding Cargo No.</strong></td>';
        }
        else{
            $html .= '<td class="color secondary"><strong>Master Cargo No.</strong></td>';
        }*/
        $html .= '
                              <td>' . str_pad($cargo->id, 6, '0', STR_PAD_LEFT) . '</td>
                            </tr>
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
                              <td class="text-center">' . $cargo->origin_hub->name . '-'. $cargo->destination_hub->name . '</td>
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
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($master_cargo->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . str_pad($cargo->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                              </td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Destination Hub</strong></td>
                              <td>' . $cargo->destination_hub->name . '</td>
                              <td class="color secondary"><strong>Vehicle Number</strong></td>
                              <td>' . (($cargo->fleet_id) ? $cargo->fleet->reg_number :  '-') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transit Date</strong></td>
                              <td>' . $cargo->created_at . '</td>
                              <td class="color secondary"><strong>Contact Phone</strong></td>
                              <td>' . $cargo->phone_number . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>No. of Bags</strong></td>
                              <td>' . $cargo->bags . '</td>
                              <td colspan="2"></td>
                            </tr>
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

        if($type == 1){
            $html .= '
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </body>
                </html>
      ';
            $pdf = SnappyPDF::loadHTML($html)->save('reports/cargo_manifest'. str_pad($cargo->id, 6, '0', STR_PAD_LEFT) .'.pdf');
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
}
