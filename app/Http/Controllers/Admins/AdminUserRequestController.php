<?php

namespace App\Http\Controllers\Admins;

use App\AdminUserRequestHub;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\AdminUserRequest;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\City;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class AdminUserRequestController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function user_requests_index(){
        //dd(session('role_id'));
        $hubs=City::select('id','name')->where('hub',1)->get();
        $departments = Admin::join('admin_roles as ar','ar.id','=','admins.role_id')
            ->join('admin_departments as ad','ad.id','=','ar.department_id')
            ->where('admins.id',Auth::id())
            ->select('ad.id','ad.name')->get();
        return view('admin.user_management.user_request.index')->with(['hubs'=>$hubs,'departments'=>$departments]);
    }
    public function user_requests_list(Request $request) {
        $users = AdminUserRequest::leftjoin('cities as c','c.id','=','admin_user_requests.default_hub_id')
            ->leftjoin('admin_departments as ad','ad.id','=','admin_user_requests.department')
            ->leftjoin('admins as a','a.id','=','admin_user_requests.request_added_by')
            ->leftjoin('admins as as','as.id','=','admin_user_requests.verified_by_hr')
            ->leftjoin('admins as ac','ac.id','=','admin_user_requests.forwarded_by')
        ->select('admin_user_requests.id','admin_user_requests.trax_id as trax_id','admin_user_requests.designation as designation','admin_user_requests.name as name', 'admin_user_requests.email as email', 'admin_user_requests.phone_number as phone_number', 'admin_user_requests.cnic as cnic','c.name as default_hub','ad.name as department','admin_user_requests.request_created_at as request_created_at','a.name as request_craeted_by','admin_user_requests.verified_by_hr_at as verified_by_hr_at','as.name as verified_by_hr','admin_user_requests.status as status','admin_user_requests.forwarded_at','ac.name as forwarded_by', 'admin_user_requests.outlook_email')
            ->orderBy('admin_user_requests.created_at','desc');

            if(session('role_id') != 1 && session('role_id') != 63){
                $users->whereIn('admin_user_requests.status',[0,1]);
            };

        if(session('role_id') != 1 && session('role_id') != 63){
            $users->where('ad.id',session('department_id'));
        }

        $datatables = Datatables::of($users)
            ->addColumn('requested_from_date', function($user){
                if($user->request_created_at){
                    Carbon::setWeekendDays([
                        Carbon::SUNDAY,
                    ]);
                    $request_date = Carbon::parse($user->request_created_at);
                    $verified_date = Carbon::parse($user->verified_by_hr_at);
                    $days = $request_date->diffInDays($verified_date);
                    if($days <= 0){
                        return '-';
                    }
                    else{
                        return $days . 'days';
                    }
                }
                else{
                    return '-';
                }
            })
            ->editColumn('status', function ($user) {
                if($user->status == 0){
                    return 'Requested';
                }
                else if($user->status == 1){
                    return 'HR Verified';
                }
                else if($user->status == 2){
                    return 'Admin Verified';
                }
                else if($user->status == 3){
                    return 'Request Completed';
                }
            })
            ->addColumn('verified_from_date', function($user){
                if($user->verified_by_hr_at){
                    Carbon::setWeekendDays([
                        Carbon::SUNDAY,
                    ]);
                    $verified_date = Carbon::parse($user->verified_by_hr_at);
                    $forwarded_date = Carbon::parse($user->forwarded_at);
                    $days = $verified_date->diffInDays($forwarded_date);
                    if($days <= 0){
                        return '-';
                    }
                    else{
                        return $days . 'days';
                    }
                }
                else{
                    return '-';
                }
            })
            ->editColumn('outlook_email', function ($user) {
                if($user->outlook_email == 0){
                    return 'No';
                }
                else if($user->outlook_email == 1){
                    return 'Yes';
                }else{
                    return '-';
                }
            })
            ->addColumn('action', function($user) {
                $verify = '<button type="button" class="dropdown-item verify"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Verify Info</div></button>';
                $add_role = '<button type="button" class="dropdown-item addrole"><div class="row no-gutters align-items-center"><div class="col-2"><i class="la la-user-plus"></i></div><div class="col-9 offset-1">Add Role</div></button>';
                $forwarded_by = '<button type="button" class="dropdown-item forward"><div class="row no-gutters align-items-center"><div class="col-2"><i class="la la-arrow-circle-right"></i></div><div class="col-9 offset-1">Forward</div></button>';
                $view_details = '<button type="button" class="dropdown-item details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="la la-file-o"></i></div><div class="col-9 offset-1">View Details</div></button>';

                if(session('role_id') == 1 || (( session('role_id') == 63) && ($user->status == 0 || $user->status == 3 ))) {
                    $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">';

                    if ((session('role_id') == 1 || session('role_id') == 63) && $user->status == 0) {
                        $dropdown .= $verify;
                    }
                    if (session('role_id') == 1 && $user->status == 1) {
                        $dropdown .= $add_role;
                    }
                    if ((session('role_id') == 1|| session('role_id') == 63) && $user->status == 3) {
                        $dropdown .= $view_details;
                    }
                    return $dropdown;
                }
            });
        return $datatables->make(true);
    }

    public function user_request_add_index() {
        $departments = Admin::join('admin_roles as ar','ar.id','=','admins.role_id')
            ->join('admin_departments as ad','ad.id','=','ar.department_id')
            ->where('admins.id',Auth::id())
            ->select('ad.id','ad.name')->get();

        if(session('role_id') == 1){
            $departments = AdminDepartment::where('id', '!=', 1)->get();
        }
        $hubs = City::where('hub', 1)->get();
        return view('admin.user_management.user_request.add.index')->with(['departments' => $departments, 'hubs' => $hubs]);
    }
    public function user_add_store(Request $request) {
        if($request->has('outlook_email')){
            $outlook_email = 1;
        }
        else{
            $outlook_email = 0;
        }
        $admin = new AdminUserRequest();

        $admin->name = $request->input('name');
        $admin->department = $request->input('department');
        $admin->designation = $request->input('designation');
        $admin->request_added_by = Auth::id();
        $admin->request_created_at = Carbon::now();
        $admin->outlook_email = $outlook_email;
        $admin->save();

        if ($request->has('hub_ids')) {
            foreach($request->input('hub_ids') as $hub_id) {
                $admin_hub = new AdminUserRequestHub();

                $admin_hub->admin_user_requests_id = $admin->id;
                $admin_hub->hubs_id = $hub_id;
                $admin_hub->save();
            }
        }

        NotificationsController::send(200, $admin->id);

        return redirect()->route('admin.user_management.user_requests.index')->with(['success' => 'User: ' . $request->input('name') . ' has been added!']);
    }

    public function verify_index($id) {
        if(session('role_id') == 1 ||  session('role_id') == 63) {
            $departments = AdminDepartment::select('id','name')->where('id','!=',1)->get();
            $hubs = City::where('hub', 1)->get();
            $user = AdminUserRequest::find($id);
            $user_hubs = AdminUserRequestHub::where('admin_user_requests_id', $id)->pluck('hubs_id')->toArray();

              return view('admin.user_management.user_request.verify.index')->with(['departments' => $departments, 'hubs' => $hubs, 'user' => $user, 'user_hubs' => $user_hubs]);
        }
        else{
            return view('admin.access_denied');
        }
    }

    public function verify_store(Request $request, $id) {

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

        $admin = AdminUserRequest::find($id);

        $admin->name = $request->input('name');
        $admin->trax_id = $trax_id;
        $admin->email = $request->input('email');
        $admin->phone_number = $request->input('phone_number');
        $admin->cnic = $request->input('cnic');
        $admin->department = $request->input('department');
        $admin->designation = $request->input('designation');
        $admin->default_hub_id = $request->input('default_hub');
        $admin->verified_by_hr = Auth::id();
        $admin->verified_by_hr_at = Carbon::now();
        $admin->password = bcrypt($request->input('password'));
        $admin->visible_password = $request->input('password');
        $admin->status = 1;
        $admin->save();

        if ($request->has('hub_ids')) {
            $current_hub_ids = AdminUserRequestHub::where('admin_user_requests_id', $id)->pluck('hubs_id')->toArray();

            $delete_hub_ids = array_diff($current_hub_ids, $request->input('hub_ids'));
            $new_hub_ids = array_diff($request->input('hub_ids'), $current_hub_ids);

            AdminUserRequestHub::where('admin_user_requests_id', $id)->whereIn('hub_id', $delete_hub_ids)->delete();

            foreach($new_hub_ids as $hub_id) {
                $admin_hub = new AdminUserRequestHub();

                $admin_hub->admin_user_requests_id = $id;
                $admin_hub->hubs_id = $hub_id;

                $admin_hub->save();
            }
        }
        else {
            AdminUserRequestHub::where('admin_user_requests_id', $id)->delete();
        }
        NotificationsController::send(201, $admin->id);
            return redirect()->route('admin.user_management.user_requests.index')->with(['success' => 'User: ' . $request->input('name') . ' has been verified!']);
    }

    public function user_email(Request $request) {
        if ($request->filled('email')) {
            $email = AdminUserRequest::where('email', $request->input('email'));

            if ($request->has('id')) {
                $email = $email->where('id', '!=', $request->input('id'));
            }

            if (!$email->exists()) {
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
    public function user_trax_id(Request $request) {
        if ($request->filled('trax_id')) {
            $trax_id = AdminUserRequest::where('trax_id', $request->input('trax_id'));

            if ($request->has('id')) {
                $trax_id = $trax_id->where('id', '!=', $request->input('id'));
            }

            if (!$trax_id->exists()) {
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

    public function user_save_index($id) {
        if(session('role_id') == 1) {
            if(session('role_id') != 1) {
                $roles = AdminRole::with('department')->where('id', '!=', 1)->where('department_id', session('department_id'))->get();
            }
            else{
                $roles = AdminRole::with('department')->where('id', '!=', 1)->get();
            }
            $departments = AdminDepartment::select('id','name')->get();
            $hubs = City::where('hub', 1)->get();
            $user = AdminUserRequest::find($id);
            $user_hubs = AdminUserRequestHub::where('admin_user_requests_id', $id)->pluck('hubs_id')->toArray();

            return view('admin.user_management.user_request.save.index')->with(['departments' => $departments, 'hubs' => $hubs, 'user' => $user,'roles'=> $roles ,'user_hubs' => $user_hubs]);
        }
        else{
            return view('admin.access_denied');
        }
    }
    public function user_save(Request $request, $id) {

        $admin_user_request = AdminUserRequest::find($id);
        $admin_user_request->name = $request->input('name');
        $admin_user_request->email = $request->input('email');
        $admin_user_request->phone_number = $request->input('phone_number');
        $admin_user_request->cnic = $request->input('cnic');
        $admin_user_request->department = $request->input('department');
        $admin_user_request->designation = $request->input('designation');
        $admin_user_request->default_hub_id = $request->input('default_hub');
//        $admin_user_request->verified_by_hr = Auth::id();
//        $admin_user_request->verified_by_hr_at = Carbon::now();
        $admin_user_request->password = bcrypt($request->input('password'));
        $admin_user_request->visible_password = $request->input('password');
        if($admin_user_request->outlook_email == 1){
            $admin_user_request->visible_outlook_password = $request->input('visible_outlook_password');
        }

        $admin_user_request->forwarded_by = Auth::id();
        $admin_user_request->forwarded_at = Carbon::now();
        $admin_user_request->status = 3;
//        $admin_user_request->status = 2;
        $admin_user_request->save();


        $admin = new Admin();

        $admin->name = $request->input('name');
        $admin->email = $request->input('email');
        $admin->trax_id = $admin_user_request->trax_id;
        $admin->phone_number = $request->input('phone_number');
        $admin->cnic = $request->input('cnic');
        /* $admin->department = $request->input('department');*/
        $admin->designation = $request->input('designation');
        $admin->default_hub_id = $request->input('default_hub');
        $admin->password = $admin_user_request->password;
        $admin->created_at = Carbon::now();
        $admin->updated_at = $admin->created_at;
        if ($request->has('api_token')) {
            $admin->api_token = $request->input('api_token');
        }
        if ($request->has('role_id')) {
            $admin->role_id = $request->input('role_id');
        }
        $admin->status = 1;
        $admin->save();
        $admin_ids = $admin->id;


        if ($request->has('hub_ids')) {
            $current_hub_ids = AdminUserRequestHub::where('admin_user_requests_id', $id)->pluck('hubs_id')->toArray();

            $delete_hub_ids = array_diff($current_hub_ids, $request->input('hub_ids'));
            $new_hub_ids = array_diff($request->input('hub_ids'), $current_hub_ids);

            AdminUserRequestHub::where('admin_user_requests_id', $id)->whereIn('hubs_id', $delete_hub_ids)->delete();

            foreach ($new_hub_ids as $hub_id) {
                $admin_hub = new AdminUserRequestHub();
                $admin_hub->admin_user_requests_id = $id;
                $admin_hub->hubs_id = $hub_id;

                $admin_hub->save();

            }
            $new_hub_ids = $request->input('hub_ids');
            foreach ($new_hub_ids as $hub_id) {
                $save_hub_id = new AdminHub();
                $save_hub_id->admin_id = $admin_ids;
                $save_hub_id->hub_id = $hub_id;
                $save_hub_id->save();
            }
        } else {
            AdminUserRequestHub::where('admin_user_requests_id', $id)->delete();
        }
        NotificationsController::send(202, $admin_user_request->id);

        return redirect()->route('admin.user_management.user_requests.index')->with(['success' => 'User: ' . $request->input('name') . ' has been Saved!']);
    }

  public function forward(Request $request){
        $admin_user = $request->id;
        $user = AdminUserRequest::find($admin_user);
        $user->forwarded_by = Auth::id();
        $user->forwarded_at = Carbon::now();
        $user->status = 3;
        $user->save();

        return response()->json(['success'=>'Request Forwarded']);

  }
}
