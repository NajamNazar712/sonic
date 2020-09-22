<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\City;
use App\Http\Models\Rider;
use App\Http\Models\RiderCategory;
use App\Http\Models\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class RiderManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function permanent_index(){
        $category = RiderCategory::all();
        return view('admin.management.riders.permanent_index')->with(['categories'=>$category]);
    }

    public function permanent_list(Request $request){
        $rider = Rider::join('cities','riders.city_id','=','cities.id')
            ->join('cities as c','cities.hub_id','=','c.id')
            ->leftjoin('routes','routes.id','=','riders.route_id')
            ->join('rider_categories','rider_categories.id','=','riders.rider_category_id')
            ->leftjoin('admins as cb', 'cb.id', '=', 'riders.created_by')
            ->leftjoin('admins as ub', 'ub.id', '=', 'riders.updated_by')
            ->select('cities.name as city','c.name as hub','riders.id as rider_id','riders.id','riders.name as rider', 'riders.trax_id' ,'riders.phone','riders.cnic', 'riders.address','routes.code as route','routes.start','routes.end','rider_categories.name as category','riders.status as status','riders.created_at','cb.name as created_by', 'ub.name as updated_by', 'riders.rider_type_id','riders.blacklist')
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
                if (session('role_id') == 1 || count(array_intersect([98, 99], session('permissions'))) !== 0) {
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
            ->make(true);
    }

    public function addRiderView(){
        $city = City::select(['id','name'])->get();
        $category = RiderCategory::all();
        return view('admin.management.add_rider_form')->with(['cities'=>$city,'categories'=>$category]);
    }
    public function addRiderDetails(Request $request){
        $type = $request->rider_type;
        $validations = [
            'city_id'=>'required|numeric',
            'rider_name'=>'required|max:255',
            'phone'=>'required|max:255',
            'cnic'=>'required|max:255',
            'address'=>'required|max:255',
            'route_id'=>'required|numeric',
            'rider_category'=>'required|numeric',
            'pin' => 'required|numeric',
            'trax_id'=>'required|max:255|string',
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
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
            'special_rider' => ($request->has('special_rider_checkbox')? 1:0),
            'pin'=> bcrypt($request->pin),
            'created_by' => Auth::id(),
            'trax_id' => $request->trax_id,
            'rider_type' => $type
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

        return response()->json($route);
    }
    public function editRiderView($id){
        $city = City::select(['id','name'])->get();
        $category = RiderCategory::all();
        $rider = Rider::find($id);
        $route = Route::where('city_id',$rider->city_id)->get();
        return view('admin.management.edit_rider_form')->with(['rider_id'=>$id,'cities'=>$city,'categories'=>$category,'rider'=>$rider,'routes'=>$route]);
    }
    public function editRiderDetails(Request $request,$id){
        $validations = [
            'city_id'=>'required|numeric',
            'rider_name'=>'required|max:255',
            'phone'=>'required|max:255',
            'cnic'=>'required|max:255',
            'address'=>'required|max:255',
            'route_id'=>'required|numeric',
            'trax_id'=>'required|string',
            'rider_category'=>'required|numeric'
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        // $rider = Rider::where('id',$id)->update([
        //     'city_id'=>$request->city_id,
        //     'name'=>$request->rider_name,
        //     'phone'=>$request->phone,
        //     'cnic'=>$request->cnic,
        //     'address'=>$request->address,
        //     'route_id'=>$request->route_id,
        //     'rider_category_id'=>$request->rider_category,
        //     'special_rider' => ($request->has('special_rider_checkbox')? 1:0)
        // ]);

        $rider = Rider::find($id);

        $rider->city_id = $request->city_id;
        $rider->name = $request->rider_name;
        $rider->phone = $request->phone;
        $rider->cnic = $request->cnic;
        $rider->address = $request->address;
        $rider->route_id = $request->route_id;

        $rider->rider_category_id = $request->rider_category;
        $rider->trax_id = $request->trax_id;

        if($request->has('special_rider_checkbox')){
            $rider->special_rider = 1;
        }else{
            $rider->special_rider = 0;
        }
        if($request->pin != '') {
            $rider->pin = bcrypt($request->pin);

            NotificationsController::send(61, $rider->id, $request->pin);
        }
        $rider->updated_by = Auth::id();
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
            $rider->save();
            return response()->json(['status' => 0, 'success' => 'Rider is blacklisted!']);
        }
        if($action == 'unblock'){
            $rider->blacklist = 1;
            $rider->save();
            return response()->json(['status' => 0, 'success' => 'Rider is Unblocked!']);
        }



    }

    public function incentive_index(){

    }

    public function incentive_list(){

    }
}
