<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
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
        return view('admin.settings.user_request.index')->with(['hubs'=>$hubs]);
    }
    public function user_requests_list(Request $request) {
        $users = AdminUserRequest::leftjoin('cities as c','c.id','=','admin_user_requests.default_hub_id')
            ->leftjoin('admin_departments as ad','ad.id','=','admin_user_requests.department')
            ->join('admins as a','a.id','=','admin_user_requests.request_added_by')
        ->select('admin_user_requests.name as name', 'admin_user_requests.email as email', 'admin_user_requests.phone_number as phone_number', 'admin_user_requests.cnic as cnic','c.name as default_hub','ad.name as department','admin_user_requests.request_created_at as request_created_at','a.name as admin','admin_user_requests.verified_by_hr_at as verified_by_hr_at'.'\'admin_user_requests.verified_by_hr as verified_by_hr','admin_user_requests.status as status')
           ;

        $datatables = Datatables::of($users)
            ->editColumn('verified_by_hr_at', function($user) {
                if($user->verified_by_hr_at == null){
                    return '-';
                }
            })
            ->editColumn('verified_by_hr', function ($user) {
                if($user->verified_by_hr == null){
                    return '-';
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
            ->editColumn('status', function ($user) {
                if($user->status == 0){
                    return 'Requested';
                }
                else if($user->status == 1){
                    return 'Verified';
                }
            })
            ->addColumn('action', function($user) {
                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    $enable_button = '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    $disable_button = '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';

                $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">
                ';
            });

        return $datatables->make(true);
    }

    public function user_request_add_index() {
        $departments = Admin::join('admin_roles as ar','ar.id','=','admins.role_id')
             ->join('admin_departments as ad','ad.id','=','ar.department_id')
            ->where('admins.id',Auth::id())
            ->select('ad.id','ad.name')->get();
        $hubs = City::where('hub', 1)->get();
        return view('admin.settings.user_request.add.index')->with(['departments' => $departments, 'hubs' => $hubs]);
    }
    public function user_add_store(Request $request) {
        $admin = new AdminUserRequest();

        $admin->name = $request->input('name');
        $admin->email = $request->input('email');
        $admin->phone_number = $request->input('phone_number');
        $admin->cnic = $request->input('cnic');
        $admin->department = $request->input('department');
        $admin->default_hub_id = $request->input('default_hub');
        $admin->request_added_by = Auth::id();
        $admin->request_created_at = Carbon::now();

        $admin->save();

        if ($request->has('hub_ids')) {
            foreach($request->input('hub_ids') as $hub_id) {
                $admin_hub = new AdminHub();

                $admin_hub->admin_id = $admin->id;
                $admin_hub->hub_id = $hub_id;

                $admin_hub->save();
            }
        }

        return redirect()->route('admin.settings.user_requests.index')->with(['success' => 'User: ' . $request->input('name') . ' has been added!']);
    }
}
