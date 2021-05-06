<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\OperationRidersCategory;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\GlobalSettings;
use App\http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\RiderType;
use App\Http\Models\City;
use App\Http\Models\HR\Employee;
use App\Http\Models\Rider;
use App\Http\Models\Rider\RiderRequest;
use App\Http\Models\Rider\RidersIncentive;
use App\Http\Models\Rider\RidersIncentiveSetting;
use App\Http\Models\RiderCategory;
use App\Http\Models\Route;
use App\Http\Models\RouteType;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\SmsHistory;
use App\Http\Models\SmsHistoryRider;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2PickupReceivedShipment;
use App\Http\Models\V2Pickup\V2PickupRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use DB;
use App\Http\Controllers\Admins\ActivityTrailController;

class RiderManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function permanent_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),58);
        $category = RiderCategory::all();
        return view('admin.management.riders.permanent_index')->with(['categories'=>$category]);
    }

    public function permanent_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),118);
        }
        $rider = Rider::join('cities','riders.city_id','=','cities.id')
            ->join('cities as c','cities.hub_id','=','c.id')
            ->leftjoin('zones as z','cities.zone_id','=','z.id')
            ->leftjoin('routes','routes.id','=','riders.route_id')
            ->join('rider_categories','rider_categories.id','=','riders.rider_category_id')
            ->leftjoin('admins as cb', 'cb.id', '=', 'riders.created_by')
            ->leftjoin('admins as ub', 'ub.id', '=', 'riders.updated_by')
            ->select('cities.name as city','c.name as hub','z.name as zone','riders.id as rider_id','riders.id','riders.name as rider', 'riders.trax_id' ,'riders.phone','riders.cnic', 'riders.address','routes.code as route','routes.start','routes.end','rider_categories.name as category','riders.status as status','riders.created_at as created_at','cb.name as created_by', 'ub.name as updated_by', 'riders.rider_type_id','riders.blacklist','riders.updated_at')
        ->where('riders.rider_type_id', 1)
        ->where('riders.blacklist', 0);
        if (session('role_id') != 1) {
            $rider = $rider->whereIn('cities.hub_id', session('hubs'));
        }


        return Datatables::of($rider)
            ->editColumn('status', function ($rider) {
                return ($rider->status == 0)? 'Inactive': 'Active';
            })
            ->editColumn('trax_id', function ($rider) {
                if($rider->trax_id != null){
                    return $rider->trax_id;
                }
                else{
                    return '-';
                }

            })
            ->editColumn('route', function ($rider) {
                return $rider->route.' ('.$rider->start. ' to '.$rider->end.')';
            })
            ->filterColumn('route',function($query, $keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%'.$keyword.'%')->orWhere('routes.start', 'like', '%'.$keyword.'%')->orWhere('routes.end', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($rider) {
                if (session('role_id') == 1 || count(array_intersect([98, 99, 381, 382], session('permissions'))) !== 0) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if (session('role_id') == 1 || in_array(98, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item" data-target-id=' . $rider->id . ' rel="editRider" data-toggle="modal" data-target="#editRider"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update Rider</div></button>';
                    }

                    if (session('role_id') == 1 || in_array(99, session('permissions'))) {
                        if ($rider->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $rider->id . '  rel="riderInactive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate Rider</div></button>';
                        }
                        else {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $rider->id . '  rel="riderActive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Rider</div></button>';
                        }
                    }
                    if (session('role_id') == 1 || in_array(381, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item incentive" data-target-id=' . $rider->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Make Rider Incentive</div></button>';
                    }
                    if (session('role_id') == 1 || in_array(382, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item blacklist" data-target-id=' . $rider->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Blacklist</div></button>';
                    }


                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                }
                else {
                    return '';
                }
            })
            // ->addColumn('zone', function ($rider) {
            //     return $rider->city;
            // })
            ->make(true);
    }

    public function addRiderView(){
        $city = City::where('business_category_id', 1)->select(['id','name'])->get();
        $category = RiderCategory::all();
        $route_types = RouteType::all();
        $operation_riders = OperationRidersCategory::all();
        
        return view('admin.management.add_rider_form')->with(['cities'=>$city,'categories'=>$category,'route_types' => $route_types, 'cities'=>$city,'operation_riders' =>$operation_riders]);
    }
    public function addRiderDetails(Request $request){
        $type = $request->rider_type;
        $validations = [
            'city_id'=>'required|numeric',
            'rider_name'=>'required|max:255',
            'phone'=>'required|max:255',
            'cnic'=>'required|max:255',
            'address'=>'required|max:255',
            'route_id'=>'required',
            'operation_rider_id'=>'required',
            'rider_category'=>'required|numeric',
            'pin' => 'required|numeric',
        ];
        $validate = Validator::make($request->all(), $validations);
        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        if(Rider::where('cnic',$request->cnic)->exists()){
            return redirect()->back()->with('error', 'Rider with this CNIC already exist!');
        }
        $route_id = null;
        if($request->route_id == 'other'){
            $route = new Route();
            $route->city_id = $request->city_id;
            $route->code = $request->route_code;
            $route->start = $request->start;
            $route->end = $request->end;
            $route->junction = $request->junction;
            $route->route_type_id = $request->route_type_id;
            $route->status = 1;
            $route->save();
            $route_id = $route->id;
        }else{
            $route_id = $request->route_id;
        }
        $global_setting = GlobalSettings::where('type', 'latest_employee_id');

        if($global_setting->exists()){
            $global_setting = $global_setting->first();
            $trax_id = $global_setting->setting_value + 1;
            $global_setting->setting_value = $trax_id;
            $global_setting->save();
            $trax_id = 'Trax'. str_pad($trax_id, 5, '0', STR_PAD_LEFT);
        }
        else{
            $trax_id = null;
        }
        Rider::where('route_id', $request->route_id)->update(['route_id' => NULL]);
        $rider = Rider::create([
            'city_id'=>$request->city_id,
            'name'=>$request->rider_name,
            'phone'=>$request->phone,
            'cnic'=>$request->cnic,
            'address'=>$request->address,
            'route_id'=>$route_id,
            'rider_category_id'=>$request->rider_category,
            'operation_rider_id'=>$request->operation_rider_id,
            'status'=>1,
            'special_rider' => ($request->has('special_rider_checkbox')? 1:0),
            'pin'=> bcrypt($request->pin),
            'created_by' => Auth::id(),
            'trax_id' => $trax_id,
            'rider_type_id' => $type
        ]);
        if($rider){
            NotificationsController::send(61, $rider->id, $request->pin);
            return redirect()->back()->with('success','Rider added successfully');
        }
    }
    public function categoryListAjax(Request $request){
        $city_id = $request->id;
        $route = Route::select(['id','code','start','end'])->where('city_id',$city_id)->where('status',1);

        if (session('role_id') != 1) {
            $route = $route->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $route = $route->get();

        return response()->json(['route' => $route]);
    }
    public function editRiderView($id){
        $city = City::where('business_category_id', 1)->select(['id','name'])->get();
        $category = RiderCategory::all();
        $route_types = RouteType::all();
        $rider = Rider::find($id);
        $route = Route::where('city_id',$rider->city_id)->get();
        $operation_rider_ids =  OperationRidersCategory::all();
        return view('admin.management.edit_rider_form')->with(['rider_id'=>$id,'cities'=>$city,'categories'=>$category,'rider'=>$rider,'routes'=>$route,'route_types' => $route_types,'operation_rider_ids' => $operation_rider_ids]);
    }
    public function editRiderDetails(Request $request,$id){
        $validations = [
            'city_id'=>'required|numeric',
            'rider_name'=>'required|max:255',
            'phone'=>'required|max:255',
            'cnic'=>'required|max:255',
            'address'=>'required|max:255',
            'route_id'=>'required',
            'rider_category'=>'required|numeric'
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }

        $rider = Rider::find($id);
        if(Rider::where('id',  '<>', $id)->where('cnic',$request->cnic)->exists()){
            return redirect()->back()->with('error', 'Another Rider with this CNIC already exist!');
        }
        $rider->city_id = $request->city_id;
        $rider->name = $request->rider_name;
        $rider->phone = $request->phone;
        $rider->cnic = $request->cnic;
        $rider->address = $request->address;


        $rider->rider_category_id = $request->rider_category;

        if($request->has('special_rider_checkbox')){
            $rider->special_rider = 1;
        }else{
            $rider->special_rider = 0;
        }

        $rider->updated_by = Auth::id();

        if($request->route_id == 'other'){
            $route = new Route();
            $route->city_id = $request->city_id;
            $route->code = $request->route_code;
            $route->start = $request->start;
            $route->end = $request->end;
            $route->junction = $request->junction;
            $route->route_type_id = $request->route_type_id;
            $route->status = 1;
            $route->save();

            $rider->route_id = $route->id;
        }else{
            Rider::where('route_id', $request->route_id)->where('id', '<>', $id)->update(['route_id' => NULL]);
            $rider->route_id = $request->route_id;
        }
        if($request->pin != '') {
            if($rider->dummy_pin != $request->pin) {
                $rider->pin = bcrypt($request->pin);
                $rider->dummy_pin = $request->pin;

                NotificationsController::send(61, $rider->id, $request->pin);
            }
        }


        $rider->save();

        if($rider){
            return redirect()->back()->with('success','Rider updated successfully');
        }
    }
    public function riderStatus(Request $request){
        $id = $request->cid;
        $status = $request->status;
        if($status == 'riderActive'){
            $rider = Rider::where('id',$id)->update(['status'=>1]);
            if($rider){
                return redirect()->back()->with('success','Rider is activated successfully');
            }
        }else if($status == 'riderInactive'){
            $rider =Rider::where('id',$id)->update(['status'=>0]);
            if($rider){
                return redirect()->back()->with('success','Route is now inactive');
            }

        }

    }
    public function rider_phone_unique(Request $request) {
        if ($request->filled('phone')) {
            if ($request->input('phone') == '0213-8772222') {
                return 'true';
            }
            else {
                $rider = Rider::where('phone', $request->input('phone'));

                if ($request->has('id')) {
                    $rider = $rider->where('id', '!=', $request->input('id'));
                }

                if (!$rider->exists()) {
                    return 'true';
                }
                else {
                    return 'false';
                }
            }
        }
        else {
            return 'true';
        }
    }

    public function rider_incentive(Request $request){
        $rider_id = $request->rider_id;
        if($rider_id){
            $rider = Rider::find($rider_id);
            if($rider){
                $rider_status = $rider->rider_type_id;
                if($rider_status == 1){
                    $rider->rider_type_id = 2;
                    $rider->updated_by = Auth::id();
                    $rider->save();
                    return response()->json(['status' => 0, 'success' => 'Rider Marked as Incentive Rider!']);
                }
                return response()->json(['status' => 1, 'error' => 'Rider already Marked as Incentive Rider!']);
            }
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }
    }

    public function rider_permanent(Request $request){
        $rider_id = $request->rider_id;
        if($rider_id){
            $rider = Rider::find($rider_id);
            if($rider){
                $rider_status = $rider->rider_type_id;
                if($rider_status == 2){
                    $rider->rider_type_id = 1;
                    $rider->updated_by = Auth::id();
                    $rider->save();
                    return response()->json(['status' => 0, 'success' => 'Rider Marked as Permanent Rider!']);
                }
                return response()->json(['status' => 1, 'error' => 'Rider already Marked as Permanent Rider!']);
            }
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }
    }

    public function rider_blacklist(Request $request){
        $rider_id = $request->rider_id;
        $action = $request->action;
        if(!$rider_id){
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }

        $rider = Rider::find($rider_id);
        if(!$rider){
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }

        if($action == 'block'){
            $rider->blacklist = 1;
            $rider->status = 0;
            $rider->updated_by = Auth::id();
            $rider->save();
            return response()->json(['status' => 0, 'success' => 'Rider is blacklisted!']);
        }
        if($action == 'unblock'){
            $rider->blacklist = 0;
            $rider->status = 1;
            $rider->updated_by = Auth::id();
            $rider->save();
            return response()->json(['status' => 0, 'success' => 'Rider is Unblocked!']);
        }

    }

    public function send_sms(Request $request){
        $rider_ids = explode(',', $request->selected_riders);
        if(count($rider_ids) > 0) {
           $body = trim($request->get('body'));
            $sms_history = new SmsHistory();
            $sms_history->body = $body;
            $sms_history->sender_id = Auth::id();
            $sms_history->save();
            $sms_history_id = $sms_history->id;
           foreach ($rider_ids as $rider_id){
               $rider = Rider::find($rider_id);
               if($rider){
                   $to = $rider->phone;
                   $sms_history_rider = new SmsHistoryRider();
                   $sms_history_rider->sms_history_id = $sms_history_id;
                   $sms_history_rider->rider_id = $rider_id;
                   $sms_history_rider->save();
                   NotificationsController::custom_sms($body, $to);
               }
           }
            return redirect()->back()->with('success' , 'SMS successfully sent!');
        }
    }

    public function incentive_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),59);
        $category = RiderCategory::all();
        return view('admin.management.riders.incentive_index')->with(['categories'=>$category]);
    }

    public function incentive_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),119);
        }
        $rider = Rider::join('cities','riders.city_id','=','cities.id')
            ->join('cities as c','cities.hub_id','=','c.id')
            ->leftjoin('zones as z','cities.zone_id','=','z.id')
            ->leftjoin('routes','routes.id','=','riders.route_id')
            ->join('rider_categories','rider_categories.id','=','riders.rider_category_id')
            ->leftjoin('admins as cb', 'cb.id', '=', 'riders.created_by')
            ->leftjoin('admins as ub', 'ub.id', '=', 'riders.updated_by')
            ->select('cities.name as city','c.name as hub','z.name as zone','riders.id as rider_id','riders.id','riders.name as rider', 'riders.trax_id' ,'riders.phone','riders.cnic', 'riders.address','routes.code as route','routes.start','routes.end','rider_categories.name as category','riders.status as status','riders.created_at','cb.name as created_by', 'ub.name as updated_by', 'riders.rider_type_id','riders.blacklist','riders.updated_at')
            ->where('riders.rider_type_id', 2)
            ->where('riders.blacklist', 0);

        if (session('role_id') != 1) {
            $rider = $rider->whereIn('cities.hub_id', session('hubs'));
        }

        return Datatables::of($rider)
            ->editColumn('status', function ($rider) {
                return ($rider->status == 0)? 'Inactive': 'Active';
            })
            ->editColumn('trax_id', function ($rider) {
                if($rider->trax_id != null){
                    return $rider->trax_id;
                }
                else{
                    return '-';
                }

            })
            ->editColumn('route', function ($rider) {
                return $rider->route.' ('.$rider->start. ' to '.$rider->end.')';
            })
            ->filterColumn('route',function($query, $keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%'.$keyword.'%')->orWhere('routes.start', 'like', '%'.$keyword.'%')->orWhere('routes.end', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($rider) {
                if (session('role_id') == 1 || count(array_intersect([98, 99, 381, 382], session('permissions'))) !== 0) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if (session('role_id') == 1 || in_array(98, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item" data-target-id=' . $rider->id . ' rel="editRider" data-toggle="modal" data-target="#editRider"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update Rider</div></button>';
                    }

                    if (session('role_id') == 1 || in_array(99, session('permissions'))) {
                        if ($rider->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $rider->id . '  rel="riderInactive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate Rider</div></button>';
                        }
                        else {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $rider->id . '  rel="riderActive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Rider</div></button>';
                        }
                    }
                    if (session('role_id') == 1 || in_array(381, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item permanent" data-target-id=' . $rider->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Make Rider Permanent</div></button>';
                    }
                    if (session('role_id') == 1 || in_array(382, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item blacklist" data-target-id=' . $rider->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Blacklist</div></button>';
                    }


                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                }
                else {
                    return '';
                }
            })
            ->make(true);
    }

    public function blacklist_index(){
        $category = RiderCategory::all();
        return view('admin.management.riders.blacklisted')->with(['categories'=>$category]);
    }
    public function blacklist_list(Request $request){
        $rider = Rider::join('cities','riders.city_id','=','cities.id')
            ->join('cities as c','cities.hub_id','=','c.id')
            ->leftjoin('routes','routes.id','=','riders.route_id')
            ->join('rider_categories','rider_categories.id','=','riders.rider_category_id')
            ->leftjoin('admins as cb', 'cb.id', '=', 'riders.created_by')
            ->leftjoin('admins as ub', 'ub.id', '=', 'riders.updated_by')
            ->leftjoin('rider_types as rt', 'rt.id', '=', 'riders.rider_type_id')
            ->select('cities.name as city','c.name as hub','riders.id as rider_id','riders.id','riders.name as rider', 'riders.trax_id' ,'riders.phone','riders.cnic', 'riders.address','routes.code as route','routes.start','routes.end','rider_categories.name as category','riders.status as status','riders.created_at','cb.name as created_by', 'ub.name as updated_by', 'riders.rider_type_id','riders.blacklist', 'rt.name as rider_type')
            ->where('riders.blacklist', 1);

        if (session('role_id') != 1) {
            $rider = $rider->whereIn('cities.hub_id', session('hubs'));
        }

        return Datatables::of($rider)
            ->editColumn('status', function ($rider) {
                return ($rider->status == 0)? 'Inactive': 'Active';
            })
            ->editColumn('trax_id', function ($rider) {
                if($rider->trax_id != null){
                    return $rider->trax_id;
                }
                else{
                    return '-';
                }

            })
            ->editColumn('route', function ($rider) {
                return $rider->route.' ('.$rider->start. ' to '.$rider->end.')';
            })
            ->filterColumn('route',function($query, $keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%'.$keyword.'%')->orWhere('routes.start', 'like', '%'.$keyword.'%')->orWhere('routes.end', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($rider) {
                if (session('role_id') == 1 || count(array_intersect([99, 382], session('permissions'))) !== 0) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if (session('role_id') == 1 || in_array(382, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item blacklist" data-target-id=' . $rider->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Unblock</div></button>';
                    }


                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                }
                else {
                    return '';
                }
            })
            ->make(true);
    }
    public function sms_history_index(){
        return view('admin.management.riders.sms_history');
    }
    public function sms_history_list(Request $request){
        $sms = SmsHistory::join('admins', 'admins.id', '=', 'sms_histories.sender_id')
            ->select('sms_histories.id', 'sms_histories.body', 'sms_histories.created_at', 'admins.name as send_by', DB::raw('(SELECT COUNT(sr.id) FROM sms_history_riders AS sr  where sr.sms_history_id = sms_histories.id) AS riders') );
        return Datatables::of($sms)
            ->editColumn('riders_count', function ($sms) {
                return '<center><button class="btn btn-sm btn-outline-info align-middle">' . $sms->riders . '</button></center>';
            })

            ->make(true);
    }
    public function all_riders(Request $request){
        $sms_history_id = $request->input('sms_history_id');

        $sms_history_rider = SmsHistoryRider::where('sms_history_id', $sms_history_id);
        if(!$sms_history_rider->exists()){
            return ['status' => 1, 'error' => 'No Rider found!'];
        }
        $sms_history_rider = $sms_history_rider->pluck('rider_id')->toArray();

        if (count($sms_history_rider) > 0) {
            $names = array();
            foreach ($sms_history_rider as $rider_id) {
                $names[] = Rider::find($rider_id)->name;
            }

            return ['status' => 0, 'success' => 'Rider Name', 'name' => $names];
        }
        else {
            return ['status' => 1, 'error' => 'No Rider found!'];
        }
    }

    public function rider_request_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),60);
        $rider_type = RiderType::all();
        $city = City::where('business_category_id', 1)->get();
        $category = RiderCategory::all();
        $route = Route::all();
        $operation_rider_category = OperationRidersCategory::all();
        return view('admin.management.riders.rider_request')->with(['rider_types'=>$rider_type, 'categories'=>$category, 'routes' => $route, 'cities' => $city,'operation_rider_category' => $operation_rider_category]);
    }

    public function rider_request_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),120);
        }
        $rider_request = RiderRequest::join('cities as c','rider_requests.city_id', '=', 'c.id')
            ->join('employees as e', 'e.rider_request_id', '=', 'rider_requests.id')
            ->select('rider_requests.id', 'rider_requests.name as rider_name', 'rider_requests.cnic', 'rider_requests.phone_no', 'rider_requests.pin', 'rider_requests.created_at', 'rider_requests.updated_at', 'rider_requests.status', 'rider_requests.city_id', 'rider_requests.rider_type_id', 'c.name as city_name', 'e.trax_id  as trax_id', 'e.address')
            ->where('rider_requests.status', 0);

        if (session('role_id') != 1) {
            $rider_request = $rider_request->whereIn('c.hub_id', session('hubs'));
        }
        return Datatables::of($rider_request)
            ->editColumn('status', function ($rider_request) {
                return ($rider_request->status == 0) ? 'Pending' : 'Processed';
            })
            ->addColumn("action", function ($rider_request) {
                if($rider_request->trax_id && $rider_request->trax_id != null){
                    if (session('role_id') == 1 || count(array_intersect([426], session('permissions'))) !== 0) {
                        $dropdown = '
                          <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                        ';
                            if (session('role_id') == 1 || in_array(426, session('permissions'))) {
                                $dropdown .= '<button type="button" class="dropdown-item approve" data-target-id=' . $rider_request->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Approve Rider</div></button>';
                            }
                        $dropdown .= '
                            </div>
                          </div>
                        ';

                        return $dropdown;
                    } else {
                        return '';
                    }
                } else {
                    return '';
                }
            })
            ->make(true);
    }

    public function approveRider(Request $request){
        $validations = [
            'city_id'=>'required|numeric',
            'rider_name'=>'required|max:255',
            'phone'=>'required|max:255',
            'cnic'=>'required|max:255',
            'address'=>'required|max:255',
            'route_id'=>'required|numeric',
            'rider_category'=>'required|numeric',
            'pin' => 'required|integer|digits:4',
            'rider_request_id' => 'required',
            'rider_type' => "required|numeric",
            'category' => "required|numeric"
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        $employee = Employee::where('rider_request_id', $request->rider_request_id);
        if($employee->exists()){
            $employee = $employee->first();
            $employee->status_id = 1;
            $employee->save();
            $trax_id = $employee->trax_id;
            $employee_id = $employee->id;
        }
        else{
            $global_setting = GlobalSettings::where('type', 'latest_employee_id');

            if($global_setting->exists()){
                $global_setting = $global_setting->first();
                $trax_id = $global_setting->setting_value + 1;
                $global_setting->setting_value = $trax_id;
                $global_setting->save();
                $trax_id = 'Trax'. str_pad($trax_id, 5, '0', STR_PAD_LEFT);
            }
            else{
                $trax_id = null;
            }
            $employee_id = null;
        }

        $rider = Rider::create([
            'city_id'=>$request->city_id,
            'name'=>$request->rider_name,
            'phone'=>$request->phone,
            'cnic'=>$request->cnic,
            'address'=>$request->address,
            'route_id'=>$request->route_id,
            'rider_category_id'=>$request->rider_category,
            'status'=>1,
            'pin'=> bcrypt($request->pin),
            'created_by' => Auth::id(),
            'trax_id' => $trax_id,
            'employee_id' => $employee_id,
            'rider_type_id'  => $request->rider_type,
            'operation_rider_id'  => $request->category
        ]);
        if($rider){
            NotificationsController::send(61, $rider->id, $request->pin);
            $rider_request = RiderRequest::find($request->rider_request_id);
            if($rider_request){
                $rider_request->status = 1;
                $rider_request->save();
            }
            return redirect()->back()->with('success','Rider added successfully');
        }
    }

    static public function riders_incentives_calculation($date){

        $date_from = Carbon::createFromFormat("Y-m-d H:i:s",$date)->format('Y-m-d 06:00A');
        $next_day = Carbon::parse($date)->addDay(4);
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s",$next_day)->format('Y-m-d 05:59A');
        $riders = Rider::where('status', 1)->select('id', 'rider_category_id')->get();
        if(count($riders) > 0){
            foreach ($riders as $rider){
                $rider_category_id = $rider->rider_category_id;
                $pickup_shipment_ids = array();
                $delivery_shipment_ids = array();
                $pickup_incentive = 0;
                $delivery_incentive = 0;
                $pickup_shipments_count = 0;
                $delivery_shipments_count = 0;
                $pickup_shipment_ids = V2PickupReceivedShipment::where('rider_id', $rider->id)->whereBetween('created_at', [$date_from, $date_to]);

                if($pickup_shipment_ids->exists()){
                    $pickup_shipment_ids = $pickup_shipment_ids->pluck('shipment_id')->toArray();

                    $pickup_shipments_count = count($pickup_shipment_ids);
                    $cod_shipments = array();
                    $non_cod_shipments = array();
                    $verified_shipments = array();
                    $test = array();
                    $cod_shipments = Shipment::whereIn('id', $pickup_shipment_ids)->where('amount', '>', 0)->whereNotIn('user_id', [3324,10104])->pluck('id')->toArray();
                    $non_cod_shipments = Shipment::whereIn('id', $pickup_shipment_ids)->where('amount', '=', 0)->whereNotIn('user_id', [3324,10104])->pluck('id')->toArray();
                    $verified_shipments = Shipment::whereIn('id', $pickup_shipment_ids)->whereIn('user_id', [3324,10104])->pluck('id')->toArray();

                    if(count($cod_shipments) > 0){

                        $shipment_weight_type_1_count = Shipment::whereIn('id', $cod_shipments)->where('actual_weight' , '>', 0.10)->where('actual_weight' , '=<', 1.50)->count();

                        $shipment_weight_type_2_count = Shipment::whereIn('id', $cod_shipments)->where('actual_weight' , '=>', 1.51)->count();
                        $shipment_weight_type_3_count = Shipment::whereIn('id', $cod_shipments)->where('actual_weight' , '=<', 0.10)->count();
                        $test = [$shipment_weight_type_1_count, $shipment_weight_type_2_count, $shipment_weight_type_2_count];
                        if($shipment_weight_type_1_count > 0){
                            $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 1)->first();
                            $incentive_shipment = $setting_cod->setting_value;
                            $pickup_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                        }
                        if($shipment_weight_type_2_count > 0){
                            $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 2)->first();
                            $incentive_shipment = $setting_cod->setting_value;
                            $pickup_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                        }
                        if($shipment_weight_type_3_count > 0){
                            $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 3)->first();
                            $incentive_shipment = $setting_cod->setting_value;
                            $pickup_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                        }

                        if($rider->id == 297){
                            dd($test);
                        }
                    }

                    if(count($non_cod_shipments) > 0){

                        $shipment_weight_type_1_count = Shipment::whereIn('id', $non_cod_shipments)->where('actual_weight' , '=<', 1.50)->where('actual_weight', '>', 0.10)->count();
                        $shipment_weight_type_2_count = Shipment::whereIn('id', $non_cod_shipments)->where('actual_weight' , '=>', 1.51)->count();
                        $shipment_weight_type_3_count = Shipment::whereIn('id', $non_cod_shipments)->where('actual_weight' , '=<', 0.10)->count();
                        if($shipment_weight_type_1_count > 0){
                            $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 1)->first();
                            $incentive_shipment = $setting_cod->setting_value;
                            $pickup_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                        }
                        if($shipment_weight_type_2_count > 0){
                            $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 2)->first();
                            $incentive_shipment = $setting_cod->setting_value;
                            $pickup_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                        }
                        if($shipment_weight_type_3_count > 0){
                            $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 3)->first();
                            $incentive_shipment = $setting_cod->setting_value;
                            $pickup_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                        }

                    }

                    if(count($verified_shipments) > 0){
                        $shipment_weight_type_1_count = Shipment::whereIn('id', $verified_shipments)->where('actual_weight' , '=<', 1.50)->where('actual_weight' , '>', 0.10)->count();
                        $shipment_weight_type_2_count = Shipment::whereIn('id', $verified_shipments)->where('actual_weight' , '=>', 1.51)->count();
                        $shipment_weight_type_3_count = Shipment::whereIn('id', $verified_shipments)->where('actual_weight' , '=<', 0.10)->count();
                        if($shipment_weight_type_1_count > 0){
                            $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 1)->first();
                            $incentive_shipment = $setting_cod->setting_value;
                            $pickup_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                        }
                        if($shipment_weight_type_2_count > 0){
                            $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 2)->first();
                            $incentive_shipment = $setting_cod->setting_value;
                            $pickup_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                        }
                        if($shipment_weight_type_3_count > 0){
                            $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 3)->first();
                            $incentive_shipment = $setting_cod->setting_value;
                            $pickup_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                        }
                    }

                }

                $delivery_date = $date_to;
                $delivery_date = Carbon::parse($delivery_date)->toDateString();
                $delivery_notes = DeliveryNote::where('rider_id', $rider->id)->whereDate('pending_for_verification_at', $delivery_date)->where('status', '!=', 4);
                if($delivery_notes->exists()){
                    $delivery_note_ids = array();
                    $delivery_note_ids = $delivery_notes->pluck('id')->toArray();
                    $delivery_note_ids_count = count($delivery_note_ids);
                    if($delivery_note_ids_count > 0){
                        $delivery_shipment_ids = ShipmentsJourney::whereIn('reference_1_id', $delivery_note_ids)->whereIn('shipper_status_id', [14, 30, 36,37])->where('verification', 1)->pluck('shipment_id')->toArray();
                        array_unique($delivery_shipment_ids);

                        $delivered_shipment_count = count($delivery_shipment_ids);

                        if($delivered_shipment_count > 0){

                            $cod_shipments = array();
                            $non_cod_shipments = array();
                            $verified_shipments = array();

                            $cod_shipments = Shipment::whereIn('id', $delivery_shipment_ids)->where('amount', '>', 0)->whereNotIn('user_id', [3324,10104])->pluck('id')->toArray();
                            $non_cod_shipments = Shipment::whereIn('id', $delivery_shipment_ids)->where('amount', '=', 0)->whereNotIn('user_id', [3324,10104])->pluck('id')->toArray();
                            $verified_shipments = Shipment::whereIn('id', $delivery_shipment_ids)->whereIn('user_id', [3324,10104])->pluck('id')->toArray();

                            if(count($cod_shipments) > 0){

                                $shipment_weight_type_1_count = Shipment::whereIn('id', $cod_shipments)->where('actual_weight' , '=<', 1.50)->where('actual_weight' , '>', 0.10)->count();
                                $shipment_weight_type_2_count = Shipment::whereIn('id', $cod_shipments)->where('actual_weight' , '=>', 1.51)->count();
                                $shipment_weight_type_3_count = Shipment::whereIn('id', $cod_shipments)->where('actual_weight' , '=<', 0.10)->count();
                                if($shipment_weight_type_1_count > 0){
                                    $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 1)->first();
                                    $incentive_shipment = $setting_cod->setting_value;
                                    $delivery_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                                }
                                if($shipment_weight_type_2_count > 0){
                                    $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 2)->first();
                                    $incentive_shipment = $setting_cod->setting_value;
                                    $delivery_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                                }
                                if($shipment_weight_type_3_count > 0){
                                    $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 3)->first();
                                    $incentive_shipment = $setting_cod->setting_value;
                                    $delivery_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                                }

                            }

                            if(count($non_cod_shipments) > 0){

                                $shipment_weight_type_1_count = Shipment::whereIn('id', $non_cod_shipments)->where('actual_weight' , '=<', 1.50)->where('actual_weight', '>', 0.10)->count();
                                $shipment_weight_type_2_count = Shipment::whereIn('id', $non_cod_shipments)->where('actual_weight' , '=>', 1.51)->count();
                                $shipment_weight_type_3_count = Shipment::whereIn('id', $non_cod_shipments)->where('actual_weight' , '=<', 0.10)->count();
                                if($shipment_weight_type_1_count > 0){
                                    $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 1)->first();
                                    $incentive_shipment = $setting_cod->setting_value;
                                    $delivery_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                                }
                                if($shipment_weight_type_2_count > 0){
                                    $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 2)->first();
                                    $incentive_shipment = $setting_cod->setting_value;
                                    $delivery_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                                }
                                if($shipment_weight_type_3_count > 0){
                                    $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 3)->first();
                                    $incentive_shipment = $setting_cod->setting_value;
                                    $delivery_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                                }

                            }

                            if(count($verified_shipments) > 0){
                                $shipment_weight_type_1_count = Shipment::whereIn('id', $verified_shipments)->where('actual_weight' , '=<', 1.50)->where('actual_weight' , '>', 0.10)->count();
                                $shipment_weight_type_2_count = Shipment::whereIn('id', $verified_shipments)->where('actual_weight' , '=>', 1.51)->count();
                                $shipment_weight_type_3_count = Shipment::whereIn('id', $verified_shipments)->where('actual_weight' , '=<', 0.10)->count();
                                if($shipment_weight_type_1_count > 0){
                                    $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 1)->first();
                                    $incentive_shipment = $setting_cod->setting_value;
                                    $delivery_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                                }
                                if($shipment_weight_type_2_count > 0){
                                    $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 2)->first();
                                    $incentive_shipment = $setting_cod->setting_value;
                                    $delivery_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                                }
                                if($shipment_weight_type_3_count > 0){
                                    $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 3)->first();
                                    $incentive_shipment = $setting_cod->setting_value;
                                    $delivery_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                                }
                            }

                        }
                    }

                }

                if($pickup_incentive > 0 || $delivery_incentive > 0){
                    $riders_incentive = new RidersIncentive();
                    $riders_incentive->rider_id = $rider->id;
                    $riders_incentive->date = Carbon::now();
                    $riders_incentive->pickup_shipments = $pickup_shipments_count;
                    $riders_incentive->pickup_incentive = $pickup_incentive;
                    $riders_incentive->delivery_shipments = $delivered_shipment_count;
                    $riders_incentive->delivery_incentive = $delivery_incentive;
                    $riders_incentive->save();
                }
            }
        }
    }
}
