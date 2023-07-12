<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\RcpAssignedAgent;
use App\Http\Models\Admin\RcpAssignedShipment;
use App\Http\Models\Admin\RcpAssignedShipmentLog;
use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Models\City;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\InterceptReBookRequest;
use App\Http\Models\InterceptReBookRequestHistory;
use App\Http\Models\RestrictedCityIntercept;
use App\Http\Models\ReturnAssignedShipmentLogs;
use App\Http\Models\ReturnAssignedShipments;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentReplacementParcelImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\Retail\RetailFranchise;
use App\Http\Models\Admin\Retail\RetailTraxCenter;
use App\Http\Models\SelfCollectionShipment;
use App\Http\Models\ShipmentDetail;

class AdminInterceptRebookRequestHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function intercept_request_history_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),49);
        return view('admin.delivery.intercept.history');
    }

    public function  intercept_request_history_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),109);
        }
        $intercept = InterceptReBookRequestHistory::join('shipments as s','s.id','=','intercept_re_book_request_histories.shipment_id')
            ->join('users as u', 'u.id', '=', 's.user_id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities as odc','odc.id','=','intercept_re_book_request_histories.old_consignee_city_id')
            ->join('cities as nc','nc.id','=','intercept_re_book_request_histories.new_consignee_city_id')
            ->leftjoin('return_assigned_shipments as ras', function ($join) {
                $join->on('ras.shipment_id', '=', 's.id')
                    ->where('ras.status', '=' , 1);
            })
            ->leftjoin('admins as agent','agent.id','=','ras.admin_id')
            ->leftjoin('city_areas as cas', 'cas.id', '=', 'intercept_re_book_request_histories.old_con_city_area_id')
            ->leftjoin('city_areas as cas2', 'cas2.id', '=', 'intercept_re_book_request_histories.new_con_city_area_id')
            ->select('cas2.name as new_consignee_area' ,'cas.name as old_consignee_area','agent.name as agent','s.tracking_number','s.tracking_number as tracking_number_link','oc.name as origin','odc.name as old_consignee_city','nc.name as new_consignee_city','intercept_re_book_request_histories.old_consignee_name','intercept_re_book_request_histories.old_consignee_address','intercept_re_book_request_histories.old_consignee_phone_number_1','intercept_re_book_request_histories.old_consignee_phone_number_2','intercept_re_book_request_histories.old_consignee_email','intercept_re_book_request_histories.new_consignee_name','intercept_re_book_request_histories.new_consignee_address','intercept_re_book_request_histories.new_consignee_phone_number_1','intercept_re_book_request_histories.new_consignee_phone_number_2','intercept_re_book_request_histories.new_consignee_email','intercept_re_book_request_histories.created_at','intercept_re_book_request_histories.old_amount','intercept_re_book_request_histories.new_amount', 'u.name as shipper','intercept_re_book_request_histories.intercept_type as type');
        if (session('role_id') != 1) {
            $intercept = $intercept->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->whereIn('odc.hub_id', session('hubs'));
                })
                    ->orWhere(function ($sub_query) {
                        $sub_query->whereIn('nc.hub_id', session('hubs'));
                    });
            });
        }

        
        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $intercept->whereBetween('intercept_re_book_request_histories.created_at', [$from, $to]);
        }

        return Datatables::of($intercept)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('old_amount', function($shipment){
                return number_format($shipment->old_amount);
            })
            ->editColumn('new_amount', function($shipment){
                return number_format($shipment->new_amount);
            })
            ->filterColumn('type', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('intercept_re_book_request_histories.intercept_type', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('type', function ($shipments) {
                if ($shipments->type == null){
                    return '-';
                }
                else if ($shipments->type == 2 ) {
                    return 'Same Consignee';
                } else {
                    return 'Different Consignee';
                }
            })
            ->make(true);
    }

    public function intercept_re_book_index($shipment_id){
        if($shipment_id){
            $shipment = Shipment::where('id',$shipment_id)->first();
            if($shipment){
                if($shipment->shipping_mode_id == 2){
                    $restricted_cities = RestrictedCityIntercept::pluck('city_id')->toArray();
                $consignee_cities = Shipment::leftjoin('city_deliveries as cd', 'cd.booking_type_id', '=', 'shipments.booking_type_id')
                    ->leftjoin('cities as c', 'c.id', '=', 'cd.city_id')
                    ->select('c.id as id', 'c.name as name')
                    ->where('shipments.id', $shipment_id)
                    ->where('cd.shipping_mode_id',2)
                    ->where('c.status', 1)
                    ->whereNotNull('c.zone_id')
                    ->whereNotIn('c.id', $restricted_cities);

                }
                else{
                    $consignee_cities = Shipment::leftjoin('city_deliveries as cd', 'cd.booking_type_id', '=', 'shipments.booking_type_id')
                        ->leftjoin('cities as c', 'c.id', '=', 'cd.city_id')
                        ->select('c.id as id', 'c.name as name')
                        ->where('shipments.id', $shipment_id)
                        ->where('c.status', 1)
                        ->whereNotNull('c.zone_id');
                }
                $consignee_cities = $consignee_cities->orderBy('c.name')
                    ->groupBy('c.name')
                    ->get();
//        dd($consignee_cities);
//        $consignee_cities = City::leftjoin('city_deliveries as cd', 'cd.city_id', '=', 'cities.id')->leftjoin('')->where('status', 1)->where('pickup',1)->whereNotNull('zone_id')->orderBy('name')->get();
                return view('admin.intercept.index')->with(['shipment' => $shipment, 'consignee_cities' => $consignee_cities]);
            }
            return redirect()->back()->with('error', 'Shipment not found!');
        }
        return redirect()->back()->with('error', 'Shipment not found!');
    }

    //Different & Same Consignee
    public function intercept_re_book_update(Request $request)
    {
        $rules = [
            'replacement_parcel_image' => ['nullable', 'mimes:png,jpeg,jpg'],
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return back()->with(['error' => "Invalid File Format Of Replacement Parcel Image"]);
        } else {
        $s_amount = str_replace(",", "", $request->amount);
        $amount = intval($s_amount);
        $shipment = Shipment::find($request->shipment_id);
        $user_id = $shipment->user_id;
        $intercept_type = $request->consignee;
        
        $shipment_status = $shipment->status_shipper->name;
        $crm = false;
        $crm_request = CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_type_id', 11);
        if($crm_request->exists()){
            $crm = true;
        }
        if ($shipment['shipper_status_id'] == 12 || $shipment['shipper_status_id'] == 52 || $crm == true) {
            if ($shipment['consignee_city_id'] != $request->consignee_city || $shipment['consignee_name'] != $request->consignee_name || $shipment['consignee_address'] != $request->consignee_address || $shipment['consignee_phone_number_1'] != $request->consignee_phone_number_1 || $shipment['consignee_phone_number_2'] != $request->consignee_phone_number_2 || $shipment['consignee_email'] != $request->consignee_email || $shipment['amount'] != $amount) {
                if ($shipment['intercepted'] == 1) {

                    return redirect()->back()->with('error', 'Intercept/Re-Book is already requested against Tracking Number: ' . $shipment['tracking_number']);
                } else {
                    $shipment = Shipment::find($request->shipment_id);
                    $s_amount = str_replace(",", "", "$request->amount");
                    $amount = (int)$s_amount;

                    //Different Consignee
                   if ($intercept_type == 1){
                    $city_area_id = ShipperShipmentBookController::consignee_address_area_intercept($request->consignee_city,$request->consignee_address);
                    InterceptReBookRequest::create([
                        'shipment_id' => $request->shipment_id,
                        'consignee_city_id' => $request->consignee_city,
                        'consignee_name' => $request->consignee_name,
                        'consignee_address' => $request->consignee_address,
                        'consignee_phone_number_1' => $request->consignee_phone_number_1,
                        'consignee_phone_number_2' => $request->consignee_phone_number_2,
                        'consignee_email' => $request->consignee_email,
                        'amount' => $amount,
                        'shipper_id' => $user_id,
                        'status' => 0,
                        'intercept_type' => $intercept_type,
                        'admin_id' => Auth::id(),
                        'city_area_id'=>$city_area_id
                    ]);
                       $shipment->consignee_status_id = 54;
                       $shipment->shipper_status_id = 54;
                       $shipment->intercepted = 1;
                       $shipment->save();

                       ShipmentsJourneyController::add($request->shipment_id, 54, 54, NULL, NULL, $user_id, Auth::id());
                    //Updating New RcpAssigned Tables for different consignee
                     $rcp_assigned_shipment = RcpAssignedShipment::where('shipment_id', $request->shipment_id)->where('assigned_status', 1)->where('shipment_status', 0);
                     if ($rcp_assigned_shipment->exists()) {

                         //Assuring if agent is requesting for intercept request status rcp_assigned_agent
                         $rcp_assigned_shipment = $rcp_assigned_shipment ->latest()->first();
                         if($rcp_assigned_shipment->admin_id == Auth::id()){

                             $rcp_assigned_shipment->shipment_status = 7; //intercept request
                             $rcp_assigned_shipment->admin_id = Auth::id();
                             $rcp_assigned_shipment->save();
                             

                             //updating return row of agent 
                             $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                             $rcp_assigned_agent->increment('intercept');
                             $rcp_assigned_agent->decrement('pending_shipments');
                             $rcp_assigned_agent->increment('actual_productivity');
                             
                             $rcp_assigned_agent->admin_id = Auth::id();
                             $rcp_assigned_agent->save();

                             //creating log 
                             $return_assign_log = new RcpAssignedShipmentLog();
                             $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                             $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                             $return_assign_log->status = 7; //intercept request
                             $return_assign_log->admin_id = Auth::id();
                             $return_assign_log->save();
                             }
                             
                         //If admin is updating the status update rcp_assigned_shipment & log
                             else{
                             $rcp_assigned_shipment->shipment_status = 7; //intercept request
                             $rcp_assigned_shipment->admin_id = Auth::id();
                             $rcp_assigned_shipment->save();

                             //updating already_updated & pending of agent if shipment is updated by admin 
                             $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                             $already_updated = $rcp_assigned_agent->increment('already_updated');
                             $rcp_assigned_agent->decrement('pending_shipments');
                             $rcp_assigned_agent->save();


                             $return_assign_log = new RcpAssignedShipmentLog();
                             $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                             $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                             $return_assign_log->status = 7; //intercept request
                             $return_assign_log->admin_id = Auth::id();
                             $return_assign_log->save();
                             }
                     }

                   }
                   //Same Consignee
                   else{
                       $new_con_city_area_id = ShipperShipmentBookController::consignee_address_area_intercept($request->consignee_city,$request->consignee_address);
                       $old_con_city_area_id = ShipperShipmentBookController::consignee_address_area_intercept($shipment->consignee_city_id,$shipment->consignee_address);
                       ShipperShipmentBookController::update_consignee_address_area($shipment->id,$new_con_city_area_id);
                       InterceptReBookRequestHistory::create([
                           'shipment_id' =>$request->shipment_id,
                           'old_consignee_city_id' => $shipment->consignee_city_id,
                           'new_consignee_city_id' => $request->consignee_city,
                           'old_consignee_name' => $shipment->consignee_name,
                           'new_consignee_name' => $request->consignee_name,
                           'old_consignee_address' => $shipment->consignee_address,
                           'new_consignee_address' => $request->consignee_address,
                           'old_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                           'new_consignee_phone_number_1' => $request->consignee_phone_number_1,
                           'old_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                           'new_consignee_phone_number_2' => $request->consignee_phone_number_2,
                           'old_consignee_email' => $shipment->consignee_email,
                           'new_consignee_email' => $request->consignee_email,
                           'old_amount' => $shipment->amount,
                           'new_amount' => $amount,
                           'shipper_id' => $user_id,
                           'intercept_type' => $intercept_type,
                           'new_con_city_area_id' => $new_con_city_area_id,
                           'old_con_city_area_id' => $old_con_city_area_id,
                       ]);
                       $shipment->consignee_status_id = 55;
                       $shipment->shipper_status_id = 55;
                       $shipment->consignee_address = $request->consignee_address;
                       $shipment->consignee_phone_number_1 = $request->consignee_phone_number_1;
                       $shipment->consignee_phone_number_2 = $request->consignee_phone_number_2;

                       $shipment->intercepted = 1;
                       $shipment->save();

                       ShipmentsJourneyController::add($request->shipment_id, 55, 55, NULL, NULL, $user_id, Auth::id());

                       //Updating New RcpAssigned Tables
                     $rcp_assigned_shipment = RcpAssignedShipment::where('shipment_id', $request->shipment_id)->where('assigned_status', 1)->where('shipment_status', 0);
                     if ($rcp_assigned_shipment->exists()) {

                         //Assuring if agent is requesting for intercept request status rcp_assigned_agent
                         $rcp_assigned_shipment = $rcp_assigned_shipment ->latest()->first();
                         if($rcp_assigned_shipment->admin_id == Auth::id()){

                             $rcp_assigned_shipment->shipment_status = 8; //intercept approved request
                             $rcp_assigned_shipment->admin_id = Auth::id();
                             $rcp_assigned_shipment->save();
                             

                             //updating return row of agent 
                             $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                             $rcp_assigned_agent->increment('intercept');
                             $rcp_assigned_agent->decrement('pending_shipments');
                             $rcp_assigned_agent->increment('actual_productivity');
                             
                             $rcp_assigned_agent->admin_id = Auth::id();
                             $rcp_assigned_agent->save();

                             //creating log for intercept request
                             $return_assign_log = new RcpAssignedShipmentLog();
                             $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                             $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                             $return_assign_log->status = 7; //intercept request
                             $return_assign_log->assigned_status = 2; //intercept request
                             $return_assign_log->admin_id = Auth::id();
                             $return_assign_log->save();

                             //creating log for intercept approved
                             $return_assign_log = new RcpAssignedShipmentLog();
                             $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                             $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                             $return_assign_log->status = 8; //intercept approved
                             $return_assign_log->admin_id = Auth::id();
                             $return_assign_log->save();

                             //creating log for intercept approved
                             $return_assign_log = new RcpAssignedShipmentLog();
                             $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                             $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                             $return_assign_log->status = 2; //unassigning shipment from agent
                             $return_assign_log->admin_id = Auth::id();
                             $return_assign_log->save();

                             }
                             
                            //If admin is updating the status update rcp_assigned_shipment & log
                             else{
                             $rcp_assigned_shipment->shipment_status = 8; //intercept approved
                             $rcp_assigned_shipment->assigned_status = 2;
                             $rcp_assigned_shipment->admin_id = Auth::id();
                             $rcp_assigned_shipment->save();

                             //updating already_updated & pending of agent if shipment is updated by admin 
                             $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                             $already_updated = $rcp_assigned_agent->increment('already_updated');
                             $rcp_assigned_agent->decrement('pending_shipments');
                             $rcp_assigned_agent->save();

                             //creating log for intercept request
                             $return_assign_log = new RcpAssignedShipmentLog();
                             $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                             $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                             $return_assign_log->status = 7; //intercept request
                             $return_assign_log->admin_id = Auth::id();
                             $return_assign_log->save();

                             //creating log for intercept approved
                             $return_assign_log = new RcpAssignedShipmentLog();
                             $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                             $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                             $return_assign_log->status = 8; //intercept approved
                             $return_assign_log->admin_id = Auth::id();
                             $return_assign_log->save();

                             //creating log for intercept approved
                             $return_assign_log = new RcpAssignedShipmentLog();
                             $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                             $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                             $return_assign_log->status = 2; //unassigning shipment from agent
                             $return_assign_log->admin_id = Auth::id();
                             $return_assign_log->save();
                             }
                     }


                      

                       if($request->hasFile('replacement_parcel_image')){
                           $shipment_parcel_image = ShipmentReplacementParcelImage::where('shipment_id', $request->shipment_id);
                           if($shipment_parcel_image->exists()){
                               $shipment_parcel_image = $shipment_parcel_image->first();
                               Storage::disk('public')->delete($shipment_parcel_image->picture_path);
                           }else{
                               $shipment_parcel_image = new ShipmentReplacementParcelImage();
                               $shipment_parcel_image->shipment_id = $request->shipment_id;
                           }
                           $time = Carbon::now()->toDateString();
                           $picture_path = 'replacement_parcel/' . $request->shipment_id . '_' . $time . '.png';
                           Storage::disk('public')->put($picture_path, file_get_contents($request->replacement_parcel_image));
                           $shipment_parcel_image->picture_path = $picture_path;
                           $shipment_parcel_image->save();
                       }

                   }
                    return redirect()->back()->with('success', 'Intercept/Re-Book request submitted against Tracking Number: ' . $shipment['tracking_number']);
                }
            } else {
                return redirect()->back()->with('error', 'Shipment is already book with same details against Tracking Number: ' . $shipment['tracking_number']);
            }
        } else {
            return redirect()->back()->with('error', 'Shipment is already updated with Status : ' . $shipment_status . ' against Tracking Number: ' . $shipment['tracking_number']);
        }
    }
    }

    
}
