<?php

namespace App\Http\Controllers\Admins;

use App\AdminUserRequestHub;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\AdminUserRequest;
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
        ->select('admin_user_requests.id','admin_user_requests.trax_id as trax_id','admin_user_requests.designation as designation','admin_user_requests.name as name', 'admin_user_requests.email as email', 'admin_user_requests.phone_number as phone_number', 'admin_user_requests.cnic as cnic','c.name as default_hub','ad.name as department','admin_user_requests.request_created_at as request_created_at','a.name as request_craeted_by','admin_user_requests.verified_by_hr_at as verified_by_hr_at','as.name as verified_by_hr','admin_user_requests.status as status')
            ->whereIn('admin_user_requests.status',[0,1])

           ;
        if(session('role_id') != 1 ){
            $users->where('ad.id',session('department_id'));
        }

        $datatables = Datatables::of($users)
            ->editColumn('verified_by_hr_at', function($user) {
                if($user->verified_by_hr_at == null){
                    return '-';
                }
                else{
                    return $user->verified_by_hr_at;
                }
            })
            ->editColumn('trax_id', function ($user) {
                if($user->trax_id == null){
                    return '-';
                }
                else{
                    return $user->trax_id;
                }
            })
            ->editColumn('verified_by_hr', function ($user) {
                if($user->verified_by_hr == null){
                    return '-';
                }
                else{
                    return $user->verified_by_hr;
                }
            })
            ->editColumn('request_created_at', function ($user) {
                if($user->request_created_at == null){
                    return '-';
                }
                else{
                    return $user->request_created_at;
                }
            })
            ->editColumn('admin', function ($user) {
                if($user->admin == null){
                    return '-';
                }
                else{
                    return $user->admin;
                }
            })
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
                    return 'Verified';
                }
            })
            ->addColumn('action', function($user) {
                $verify = '<button type="button" class="dropdown-item verify"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Verify Info</div></button>';
                $add_role = '<button type="button" class="dropdown-item addrole"><div class="row no-gutters align-items-center"><div class="col-2"><i class="la la-user-plus"></i></div><div class="col-9 offset-1">Add Role</div></button>';
                if(session('role_id') == 1 || (session('department_id') == 2 && $user->status == 0)) {
                    $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">';
                    if($user->status == 0){
                        $dropdown .= $verify;
                    }
                    if(session('role_id') == 1 && $user->status == 1) {
                        $dropdown .= $add_role;
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
            $departments = AdminDepartment::all();
        }

        $hubs = City::where('hub', 1)->get();
        return view('admin.user_management.user_request.add.index')->with(['departments' => $departments, 'hubs' => $hubs]);
    }
    public function user_add_store(Request $request) {
        $admin = new AdminUserRequest();

        $admin->name = $request->input('name');
        $admin->email = $request->input('email');
        if($request->has('trax_id')){
            $admin->trax_id = $request->input('trax_id');
        }
        $admin->phone_number = $request->input('phone_number');
        $admin->cnic = $request->input('cnic');
        $admin->department = $request->input('department');
        $admin->designation = $request->input('designation');
        $admin->default_hub_id = $request->input('default_hub');
        $admin->request_added_by = Auth::id();
        $admin->request_created_at = Carbon::now();

        $admin->save();

        if ($request->has('hub_ids')) {
            foreach($request->input('hub_ids') as $hub_id) {
                $admin_hub = new AdminUserRequestHub();

                $admin_hub->admin_user_requests_id = $admin->id;
                $admin_hub->hubs_id = $hub_id;
                $admin_hub->save();
            }
        }

        return redirect()->route('admin.user_management.user_requests.index')->with(['success' => 'User: ' . $request->input('name') . ' has been added!']);
    }

    public function verify_index($id) {

        $departments = AdminDepartment::select('id','name')->where('id','!=',1)->get();
        $hubs = City::where('hub', 1)->get();
        $user = AdminUserRequest::find($id);
        $user_hubs = AdminUserRequestHub::where('admin_user_requests_id', $id)->pluck('hubs_id')->toArray();

          return view('admin.user_management.user_request.verify.index')->with(['departments' => $departments, 'hubs' => $hubs, 'user' => $user, 'user_hubs' => $user_hubs]);

    }

    public function verify_store(Request $request, $id) {

            $admin = AdminUserRequest::find($id);

            $admin->name = $request->input('name');
            $admin->trax_id = $request->input('trax_id');
            $admin->email = $request->input('email');
            $admin->phone_number = $request->input('phone_number');
            $admin->cnic = $request->input('cnic');
            $admin->department = $request->input('department');
            $admin->designation = $request->input('designation');
            $admin->default_hub_id = $request->input('default_hub');
            $admin->verified_by_hr = Auth::id();
            $admin->verified_by_hr_at = Carbon::now();
            $admin->password = bcrypt($request->input('password'));
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
            AdminUserRequestHub::where('admin_user_request_id', $id)->delete();
        }

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
        if (session('role_id') != 1) {
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
    public function user_save(Request $request, $id) {

        $admin_user_request = AdminUserRequest::find($id);
        $admin_user_request->status = 2;
        $admin_user_request->save();

        if( $admin_user_request->status == 2) {

            $admin = new Admin();

            $admin->name = $request->input('name');
            $admin->email = $request->input('email');
            $admin->trax_id = $request->input('trax_id');
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

            return redirect()->route('admin.user_management.users.index')->with(['success' => 'User: ' . $request->input('name') . ' has been Saved!']);
        }
        else {
            return redirect()->route('admin.access_denied');
         }
    }

   /* public function user_assign_hub(Request $request){
        $user_ids = explode(',' , $request->id);
        foreach($user_ids as $user_id){

            foreach($request->input('hubs') as $hub_id) {
                $admin_hub_exist = AdminUserRequestHub::where('admin_user_requests_id',$user_id)->where('hub_id',$hub_id)->first();

                if($admin_hub_exist)
                {
                    break;
                    // dd($admin_hub_exist);

                }
                else{
                    $admin_user_request_hub = new AdminUserRequestHub();
                    $admin_user_request_hub->hubs_id = $hub_id;
                    $admin_user_request_hub->admin_user_request_id = $user_id;
                    $admin_user_request_hub->save();
                }
            }
        }
        return redirect()->back()->with(['status'=>1,'success'=>"Hubs has been Assigned successfully!"]);
    }*/
}
