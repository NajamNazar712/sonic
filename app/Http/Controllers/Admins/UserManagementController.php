<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\ModulePermission;
use App\Http\Models\EmployeeShift;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeDesignation;
use App\Http\Models\ReportingLocation;
use App\Http\Models\Rider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\City;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\AdminRoleModulePermission;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\Module;

use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class UserManagementController extends Controller
{
    public function __construct() {
      $this->middleware('auth:admin');

      $this->middleware('Permission');
    }

    public function rejoin(Request $request)
    {
        $employee_id = $request->employee_id;
        if(!$employee_id){
            return response()->json(['status' => 1, 'error' => 'Admin not found!']);
        }
        $employee = Admin::find($employee_id);
        if(!$employee)
        {
            return response()->json(['status' => 1, 'error' => 'Admin not found!']);
        }

        $staff = Employee::where('trax_id',$employee->trax_id)->where('trax_id','!=',null);

        if($staff->doesntExist()){
            return response()->json(['status' => 1, 'error' => 'Admin not associated with any Employee!']);
        }
        $staff = $staff->first();

        $global_setting = GlobalSettings::where('type', 'latest_employee_id');
        if ($global_setting->exists()) {
            $global_setting = $global_setting->first();
            $trax_id = $global_setting->setting_value + 1;
            $global_setting->setting_value = $trax_id;
            $global_setting->save();
            $trax_id = 'Trax' . str_pad($trax_id, 5, '0', STR_PAD_LEFT);
        } else {
            $trax_id = null;
        }

        $employee->status = 1;
        $employee->updated_by = Auth::id();
        $employee->trax_id = $trax_id;
        $employee->save();

        $staff->status_id = AdminHumanResourseController::GetStatusOfEmployee($staff->id);
        $staff->trax_id = $trax_id;
        $staff->save();
        return response()->json(['status' => 0, 'success' => 'Admin Rejoined Successfully!']);
    }

    public function user_index() {
      ActivityTrailController::createActivityTrailLog(Auth::id(),358);
      $hubs=City::select('id','name')->where('hub',1)->get();
      $roles = AdminRole::with('department')->get();
      return view('admin.user_management.user.index')->with(['hubs'=>$hubs,'roles'=>$roles]);
    }

    public function user_list(Request $request) {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),359);
        }
        $users = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')
            ->join('admin_departments as ad', 'ar.department_id', '=', 'ad.id')
            ->leftjoin('admins as a', 'admins.updated_by', '=', 'a.id')
            ->leftjoin('employee_designations as ed', 'admins.designation_id', '=', 'ed.id')
            ->leftjoin('employees as emp', 'emp.trax_id', '=', 'admins.trax_id')
            ->leftjoin('cities as h', 'h.id', '=', 'admins.default_hub_id')
        ->select('admins.id', 'admins.name', 'admins.phone_number', 'admins.email', 'admins.cnic', 'ar.name as role', 'ad.name as department', 'admins.created_at', 'admins.updated_at', 'a.name as updated_by', 'admins.status', 'h.name as default_hub','admins.trax_id as trax_id','admins.designation as designation','admins.official_phone_number','emp.first_inactive','ed.name as designation_name');

        if(!in_array(session('role_id'), [1, 58, 70, 63])) {
            $users = $users
                ->where(function ($sub_query) {
                    $sub_query->where('ad.id', session('department_id'));
                });
        }
        if($search_roles = $request->get('search_roles')){
            $admin_roles = $users->whereIn('ar.id', $search_roles);
        }
        $datatables = Datatables::of($users)
        ->editColumn('role', function($user) {
            return $user->role . ' - ' . $user->department;
        })
        ->editColumn('status', function ($user) {
            return (($user->status) ? 'Enabled' : 'Disabled');
        })
        ->editColumn('designation', function ($user) {
            return (($user->designation_name != null) ? $user->designation_name : $user->designation);
        })
        ->removeColumn('department')
        ->addColumn('action', function($user) {
            if (session('role_id') == 1 || count(array_intersect([83, 84, 542,620], session('permissions'))) !== 0) {
                $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                $enable_button = '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                $disable_button = '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';

                $phone_edit_button = '<button type="button" class="dropdown-item phone"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Phone No. Update</div></button>';

                $rejoin_button = '<button type="button" class="dropdown-item rejoin" data-target-id="'.$user->id.'"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Rejoin Admin</div></button>';

                $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">
                ';

                if (session('role_id') == 1 || in_array(83, session('permissions'))) {
                    $dropdown .= $edit_button;
                }

                if (session('role_id') == 1 || in_array(84, session('permissions'))) {
                    if ($user->status) {
                        $dropdown .= $disable_button;
                    }
                    else {
                        $dropdown .= $enable_button;
                    }
                }

                if (session('role_id') == 1 || in_array(542, session('permissions'))) {
                    $dropdown .= $phone_edit_button;
                }


                if (session('role_id') == 1 || in_array(620, session('permissions'))) {
                    if($user->status == 0 && $user->first_inactive == 1) {
                        $dropdown .= $rejoin_button;
                    }
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
        ->filterColumn('status', function($query, $keyword) {
            $keyword = strtolower($keyword);

            if (strpos('enabled', $keyword) !== FALSE) {
                $query->where('admins.status', '=', 1);
            }
            else if (strpos('disabled', $keyword) !== FALSE) {
                $query->where('admins.status', '=', 0);
            }
            else {
                $query->whereRaw('FALSE');
            }
        })
        ->filterColumn('role', function($query, $keyword) {
            $keyword = str_replace(' ', '', str_replace('-', '', strtolower($keyword)));

            if ($keyword != '') {
                $query->where('ar.name', 'like', '%' . $keyword . '%')->orWhere('ad.name', 'like', '%' . $keyword . '%');
            }
        });

        return $datatables->make(true);
    }

    public function user_email(Request $request) {
        if ($request->filled('email')) {
            $email = Admin::where('email', $request->input('email'));

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
            $trax_id = Admin::where('trax_id', $request->input('trax_id'));

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

    public function user_status(Request $request) {
        $admin = Admin::find($request->id);

        if ($admin) {
            $admin->status = $request->status;

            $admin->save();

            $employee = Employee::where('trax_id',$admin->trax_id)->where('trax_id','!=',null);
            if($employee->exists())
            {
                $employee = $employee->first();
                if($request->status) {
                    $employee->status_id = AdminHumanResourseController::GetStatusOfEmployee($employee->id);
                }
                else{
                    $employee->status_id = 2;
                }

                $employee->update();
            }

            if ($request->status) {
                return ['status' => 0, 'success' => 'Admin has been enabled'];
            }
            else {
                return ['status' => 0, 'success' => 'Admin has been disabled'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Admin with given ID is present'];
        }
    }

    public function user_add_index() {
        if (!in_array(session('role_id'), [1, 58])) {
            $roles = AdminRole::with('department')->where('id', '!=', 1)->where('department_id', session('department_id'))->get();
        }
        else{
            $roles = AdminRole::with('department')->get();
        }
        $hubs = City::where('hub', 1)->get();
        $shifts = EmployeeShift::where('status', 1)->get();
        $designations = EmployeeDesignation::where('status',1)->get();

        return view('admin.user_management.user.add.index')->with(['roles' => $roles, 'hubs' => $hubs, 'shifts' => $shifts,'designations'=>$designations]);
    }

    public function user_add_store(Request $request) {
        $employee_id = null;
        $admin = new Admin();

        $admin->name = $request->input('name');
        $admin->email = $request->input('email');
        $admin->phone_number = $request->input('phone_number');
        $admin->official_phone_number = $request->input('official_phone_number');
        $admin->cnic = $request->input('cnic');
        $admin->role_id = $request->input('role_id');
        $admin->default_hub_id = $request->input('default_hub');
        $admin->password = bcrypt($request->input('pin'));
        $admin->dummy_pin = $request->input('pin');
        $admin->shift_id = $request->input('shift_id');
        $admin->designation_id = $request->input('designation_id');

        if($request->trax_id != null){
            $trax_id = $request->trax_id;
            $employee = Employee::where('trax_id', $request->trax_id);
            if($employee->exists()){
                $employee = $employee->first();
                $employee_id = $employee->id;
            }
            else{
                $employee = new Employee();
                $employee->trax_id = $trax_id;
                $employee->name = $request->name;
                $employee->city_id = $request->default_hub;
                $employee->cnic = $request->cnic;
                $employee->phone_number = $request->phone_number;
                $employee->official_phone_number = $request->official_phone_number;
                $employee->employee_type_id = 1;
                $employee->request_status_id = 3;
                $employee->status_id = 3;
                $employee->official_email = $request->email;
                $employee->designation_id = $request->designation_id;
                $employee->department_id = EmployeeDesignation::find($request->designation_id)->department_id ?? null;
                $employee->pin = $request->pin;
                $employee->shift_id = $request->shift_id;
                $employee->save();

                $employee_id = $employee->id;

            }

        }
        else{
            $global_setting = GlobalSettings::where('type', 'latest_employee_id');

            if($global_setting->exists()){
                $global_setting = $global_setting->first();
                $trax_id = $global_setting->setting_value + 1;
                $global_setting->setting_value = $trax_id;
                $global_setting->save();
                $trax_id = 'Trax'. str_pad($trax_id, 5, '0', STR_PAD_LEFT);

                $employee = new Employee();
                $employee->trax_id = $trax_id;
                $employee->name = $request->name;
                $employee->city_id = $request->default_hub;
                $employee->cnic = $request->cnic;
                $employee->phone_number = $request->phone_number;
                $employee->official_phone_number = $request->official_phone_number;
                $employee->employee_type_id = 1;
                $employee->request_status_id = 3;
                $employee->status_id = 3;
                $employee->official_email = $request->email;
                $employee->designation_id = $request->designation_id;
                $employee->department_id = EmployeeDesignation::find($request->designation_id)->department_id ?? null;
                $employee->pin = $request->pin;
                $employee->shift_id = $request->shift_id;
                $employee->save();

                $employee_id = $employee->id;
            }
            else{
                $trax_id = null;
            }
        }

        $admin->trax_id = $trax_id;
        $admin->employee_id = $employee_id;

        $admin->save();



        if ($request->has('hub_ids')) {
            foreach($request->input('hub_ids') as $hub_id) {
                $admin_hub = new AdminHub();

                $admin_hub->admin_id = $admin->id;
                $admin_hub->hub_id = $hub_id;

                $admin_hub->save();
            }
        }

        return redirect()->route('admin.user_management.users.index')->with(['success' => 'User: ' . $request->input('name') . ' has been added!']);
    }

    public function user_assign_hub(Request $request){
        $user_ids =explode(',' , $request->id);
        foreach($user_ids as $user_id){

                foreach($request->input('hubs') as $hub_id) {
                    $admin_hub_exist = AdminHub::where('admin_id',$user_id)->where('hub_id',$hub_id)->first();
                   
                    if($admin_hub_exist)
                    {
                    break;
                        // dd($admin_hub_exist);
                       
                    }
                    else{
                        $admin_hub = new AdminHub();

                        $admin_hub->hub_id = $hub_id;
                        $admin_hub->admin_id = $user_id;
    
                        $admin_hub->save();
                    }
                }     
        }
        return redirect()->back()->with(['status'=>1,'success'=>"Hubs has been Assigned successfully!"]);  
    }

    public function user_update_index($id) {
        if (!in_array(session('role_id'), [1, 58])) {
            $roles = AdminRole::with('department')->where('id', '!=', 1)->where('department_id', session('department_id'))->get();
        }
        else{
            $roles = AdminRole::with('department')->get();
        }
        $hubs = City::where('hub', 1)->get();
        $user = Admin::find($id);
        $user_hubs = $user->hubs->pluck('hub_id')->toArray();
        $shifts = EmployeeShift::where('status', 1)->get();
        $designations = EmployeeDesignation::where('status',1)->get();
//        $reporting_locations = ReportingLocation::where('status',1)->get();

        ActivityTrailController::createActivityTrailLog(Auth::id(),231,1);
        return view('admin.user_management.user.update.index')->with(['roles' => $roles, 'hubs' => $hubs, 'user' => $user, 'user_hubs' => $user_hubs, 'shifts' => $shifts,'designations'=>$designations]);
        
    }

    public function validate_phone(Request $request)
    {
        $id = null;
        if($request->has('id'))
        {
            $id = $request->id;
        }

        $phone_number = null;
        if($request->has('phone_number'))
        {
            $phone_number = $request->phone_number;
        }

        if($request->has('official_phone_number'))
        {
            $phone_number = $request->official_phone_number;
        }

        $phone_validate = Admin::where('id','!=',$id)->where(function ($query) use ($phone_number){
            $query->where('phone_number',$phone_number)
                ->orwhere('official_phone_number',$phone_number);
            })->exists();

        if($phone_validate)
        {
            return "false";
        }

        $trax_id = null;
        if($id != null)
        {
            $admin = Admin::find($id);
            $trax_id = $admin->trax_id;
        }

        $phone_validate = Employee::where('trax_id','!=',$trax_id)->where(function ($query) use ($phone_number){
            $query->where('phone_number',$phone_number)
                ->orwhere('official_phone_number',$phone_number);
            })->exists();

        if($phone_validate)
        {
            return "false";
        }

        return "true";
    }

    public function user_update_store(Request $request, $id) {

            $admin = Admin::find($id);

            $admin->name = $request->input('name');
            $admin->email = $request->input('email');
            $admin->phone_number = $request->input('phone_number');
            $admin->official_phone_number = $request->input('official_phone_number');
            $admin->cnic = $request->input('cnic');
            if($admin->role_id != $request->input('role_id'))
            {
                ActivityTrailController::createActivityTrailLog(Auth::id(),232,1);
            }
            $admin->role_id = $request->input('role_id');
            $admin->default_hub_id = $request->input('default_hub');
            $admin->updated_by = Auth::id();
            $admin->trax_id = $request->trax_id;
            $admin->shift_id = $request->input('shift_id');
            $admin->designation_id = $request->input('designation_id');

            if ($request->filled('pin')) {
                $admin->password = bcrypt($request->input('pin'));
                $admin->dummy_pin = $request->input('pin');
            }



            $admin->save();

            if ($request->has('hub_ids')) {
                $current_hub_ids = AdminHub::where('admin_id', $id)->pluck('hub_id')->toArray();

                $delete_hub_ids = array_diff($current_hub_ids, $request->input('hub_ids'));
                $new_hub_ids = array_diff($request->input('hub_ids'), $current_hub_ids);

                AdminHub::where('admin_id', $id)->whereIn('hub_id', $delete_hub_ids)->delete();

                foreach($new_hub_ids as $hub_id) {
                    $admin_hub = new AdminHub();

                    $admin_hub->admin_id = $id;
                    $admin_hub->hub_id = $hub_id;

                    $admin_hub->save();
                }
            }
            else {
                AdminHub::where('admin_id', $id)->delete();
            }

            $employee = Employee::where('trax_id',$admin->trax_id)->where('trax_id','!=',null);
            if($employee->exists())
            {
                $employee = $employee->first();
                $employee->designation_id = $admin->designation_id;
                $employee->city_id = $admin->default_hub_id;
                $employee->phone_number = $admin->phone_number;
                $employee->official_phone_number = $admin->official_phone_number;
                $employee->official_email = $admin->email;
                $employee->cnic = $admin->cnic;
                $employee->name = $admin->name;
                $employee->pin = $admin->dummy_pin;
                $employee->shift_id = $admin->shift_id;

                $employee->update();
            }
            return redirect()->route('admin.user_management.users.index')->with(['success' => 'User: ' . $request->input('name') . ' has been updated!']);
    }

    public function role_index() {
        ActivityTrailController::createActivityTrailLog(Auth::id(),374);
        $departments = AdminDepartment::all();
        return view('admin.user_management.role.index')->with(['departments'=>$departments]);
    }

    public function role_list(Request $request) {
        $roles = AdminRole::join('admin_departments as ad', 'admin_roles.department_id', '=', 'ad.id')
        ->join('admins as a', 'admin_roles.updated_by', '=', 'a.id')
        ->select('admin_roles.id', 'admin_roles.name', 'ad.name as department', 'admin_roles.created_at', 'admin_roles.updated_at', 'a.name as updated_by');

        $datatables = Datatables::of($roles)
        ->addColumn('action', function($role) {
            if (session('role_id') == 1 || in_array(87, session('permissions'))) {
                return '<div class="btn-group">
                          <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                          <div class="dropdown-menu dropdown-menu-sm">
                            <button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                          </div>
                        </div>
                ';
            }
            else {
                return '';
            }
        });

        return $datatables->make(true);
    }

    public function role_add_index() {
        $departments = AdminDepartment::get(['id', 'name']);
        $modules = Module::with('permissions')->get();

        return view('admin.user_management.role.add.index')->with(['departments' => $departments, 'modules' => $modules]);
    }

    public function role_add_store(Request $request) {
        $admin_role = new AdminRole();

        $admin_role->name = $request->input('name');
        $admin_role->department_id = $request->input('department_id');
        $admin_role->updated_by = Auth::id();

        $admin_role->save();

        if ($request->has('permission_ids')) {
            foreach($request->input('permission_ids') as $permission_id) {
                $admin_role_module_permission = new AdminRoleModulePermission();

                $admin_role_module_permission->role_id = $admin_role->id;
                $admin_role_module_permission->permission_id = $permission_id;

                $admin_role_module_permission->save();
            }
        }

        return redirect()->route('admin.user_management.roles.index')->with(['success' => 'Role: ' . $request->input('name') . ' has been added!']);
    }

    public function role_update_index($id) {
        $departments = AdminDepartment::get(['id', 'name']);
        $modules = Module::with('permissions')->get();
        $role = AdminRole::find($id);
        $permissions = $role->module_permissions->pluck('permission_id')->toArray();
        ActivityTrailController::createActivityTrailLog(Auth::id(),229,1);
        return view('admin.user_management.role.update.index')->with(['departments' => $departments, 'modules' => $modules, 'role' => $role, 'permissions' => $permissions]);
    }

    public function role_update_store(Request $request, $id) {
        ActivityTrailController::createActivityTrailLog(Auth::id(),230,1);
        $admin_role = AdminRole::find($id);

        $admin_role->name = $request->input('name');
        $admin_role->department_id = $request->input('department_id');
        $admin_role->updated_by = Auth::id();

        $admin_role->save();

        if ($request->has('permission_ids')) {
            $current_permission_ids = AdminRoleModulePermission::where('role_id', $id)->pluck('permission_id')->toArray();

            $crm_module_permission = ModulePermission::where('module_id', '=', 18)->pluck('id');
            $delete_permission_ids = array_diff($current_permission_ids, $request->input('permission_ids'));
            $new_permission_ids = array_diff($request->input('permission_ids'), $current_permission_ids);

            AdminRoleModulePermission::where('role_id', $id)->whereNotIn('permission_id', $crm_module_permission)->whereIn('permission_id', $delete_permission_ids)->delete();

            foreach($new_permission_ids as $permission_id) {
                $admin_role_module_permission = new AdminRoleModulePermission();

                $admin_role_module_permission->role_id = $id;
                $admin_role_module_permission->permission_id = $permission_id;

                $admin_role_module_permission->save();
            }
        }
        else {
            AdminRoleModulePermission::where('role_id', $id)->delete();
        }

        return redirect()->route('admin.user_management.roles.index')->with(['success' => 'Role: ' . $request->input('name') . ' has been updated!']);
    }

    public function admin_otp_index(){
        return view('admin.otp.admin');
    }

    public function admin_otp_list(Request $request){
        $admins = Admin::select('cities.name as city','admins.id as id', 'admins.name as name', 'admins.otp as otp', 'admins.reset_pin_otp as reset_pin_otp', 'admins.last_login_attempt')
            ->where('admins.status', 1)
            ->join('cities', 'admins.default_hub_id', '=', 'cities.id')
            ->whereNotNull('admins.otp');
        if(!in_array(session('role_id'), [1, 58, 61, 56, 71, 70, 63])) {
            $admins = $admins->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->where('ar.department_id', session('department_id'));
        }
        $datatable = Datatables::of($admins);
        return $datatable->make(true);
    }


    public function user_info(Request $request){
        $admin_id = $request->admin_id;

        if($admin_id){
            $admin = Admin::find($admin_id);
            if($admin){
                return response()->json(['status' => 0, 'phone'=> $admin->phone_number]);

            }
            return response()->json(['status' => 1, 'error'=> 'User not found!']);

        }
        return response()->json(['status' => 1, 'error'=> 'User not found!']);
    }
    public function user_phone_update(Request $request){
        $admin_id = $request->id;
        $phone = $request->phone_number;
        $validate = $this->validate_phone($request);

        if($validate == "false")
        {
            return redirect()->back()->with('error', 'Phone Number Already Exists!!');
        }

        if($admin_id){
            $admin = Admin::find($admin_id);
            if($admin){
                $admin->phone_number = $phone;
                $admin->save();

                $employee = Employee::where('trax_id',$admin->trax_id)->where('trax_id','!=',null);
                if($employee->exists())
                {
                    $employee = $employee->first();
                    $employee->phone_number = $phone;
                    $employee->update();
                }
                return redirect()->back()->with('success', 'Phone Number updated!');

            }
            return redirect()->back()->with('error', 'User not found!!');
        }
        return redirect()->back()->with('error', 'User not found!!');

    }

    public function rider_delivery_note_otp_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),414);
        return view('admin.otp.rider_delivery_note');
    }

    public function rider_delivery_note_otp_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),415);
        }
        $riders = Rider::join('cities','riders.city_id','=','cities.id')
            ->join('cities as c','cities.hub_id','=','c.id')
            ->select('riders.id as id', 'riders.name as name','riders.otp_date as otp_date', 'riders.phone as phone_no', 'riders.delivery_note_otp as otp', 'c.name as hub')
            ->where('riders.status', 1)
            ->whereNotNull('delivery_note_otp');
        $datatable = Datatables::of($riders);
        return $datatable->make(true);
    }

}