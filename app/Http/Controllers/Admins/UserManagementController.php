<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Retail\RetailShipperInfo;
use LDAP\Result;
use Carbon\Carbon;
use App\Http\Models\City;
use App\Http\Models\Rider;
use App\AdminHubAccessType;
use App\UserLostShipmentHub;
use Illuminate\Http\Request;
use App\Http\Models\Admin\Admin;
use App\Http\Models\HR\Employee;
use Yajra\Datatables\Datatables;
use App\Http\Models\Admin\Module;
use App\Http\Models\EmployeeShift;
use App\Http\Models\ZoneClassCity;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin\AdminHub;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\AdminRole;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\BusinessCategory;
use App\Http\Models\ReportingLocation;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\HR\EmployeeBloodGroup;
use App\Http\Models\Admin\ModulePermission;
use App\Http\Models\HR\EmployeeDesignation;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\AdminRoleModulePermission;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\Admins\AdminHumanResourseController;
use App\Http\Models\Admin\UserRoleManagementLog;

use Illuminate\Support\Facades\Log;
use PhpParser\Parser\Multiple;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function rejoin(Request $request)
    {
        $employee_id = $request->employee_id;
        if (!$employee_id) {
            return response()->json(['status' => 1, 'error' => 'Admin not found!']);
        }
        $employee = Admin::find($employee_id);
        if (!$employee) {
            return response()->json(['status' => 1, 'error' => 'Admin not found!']);
        }

        $staff = Employee::where('trax_id', $employee->trax_id)->where('trax_id', '!=', null);

        if ($staff->doesntExist()) {
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
        $staff->joining_date = Carbon::now();
        $staff->save();
        return response()->json(['status' => 0, 'success' => 'Admin Rejoined Successfully!']);
    }

    public function user_index() {
      ActivityTrailController::createActivityTrailLog(Auth::id(),358);
      $hubs=City::select('id','name')->where('hub',1)->get();
      $roles = AdminRole::with('department')->get();
      $employee_designations = EmployeeDesignation::get();
      $blood_group = EmployeeBloodGroup::select('id','name')->get();
      return view('admin.user_management.user.index')->with(['hubs'=>$hubs,'roles'=>$roles, 'blood_groups' => $blood_group, 'employee_designations' => $employee_designations]);
    }

    public function user_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 359);
        }
        $users = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')
            ->join('admin_departments as ad', 'ar.department_id', '=', 'ad.id')
            ->leftjoin('admins as a', 'admins.updated_by', '=', 'a.id')
            ->leftjoin('employee_designations as ed', 'admins.designation_id', '=', 'ed.id')
            ->leftjoin('employees as emp', 'emp.trax_id', '=', 'admins.trax_id')
            ->leftjoin('employee_blood_groups as bg', 'bg.id', '=', 'emp.blood_group')
            ->leftjoin('cities as h', 'h.id', '=', 'admins.default_hub_id')
            ->leftjoin('admin_hub_access_types as ahat', function ($join) {
                $join->on('ahat.admin_id', '=', 'admins.id')
                    ->where('ahat.id', '=', DB::raw('(SELECT MAX(id) FROM admin_hub_access_types WHERE admin_hub_access_types.admin_id = admins.id)'));
            })
        ->select('admins.id', 'admins.name', 'admins.phone_number', 'admins.email', 'admins.cnic', 'ar.name as role', 'ad.name as department', 'admins.created_at as created', 'admins.updated_at as updated', 'a.name as updated_by', 'admins.status', 'h.name as default_hub','admins.trax_id as trax_id','ed.name as designation','admins.official_phone_number','emp.first_inactive','ed.name as designation_name', 'bg.name as blood_group', 'emp.emergency_contact as emergency_contact_no', 'emp.emergency_contact_person as emergency_contact_person','admins.management_user as management_user', 'ad.id as admin_dept_id', 'ahat.hub_access_type as hat');

        if (!in_array(session('role_id'), [1, 58, 70, 63])) {
            $users = $users
                ->where(function ($sub_query) {
                    $sub_query->where('ad.id', session('department_id'));
                });
        }
        if ($search_roles = $request->get('search_roles')) {
            $admin_roles = $users->whereIn('ar.id', $search_roles);
        }
        if ($request->get('filter_management_users') == '1') {
            $users->where('admins.management_user', 1);
        }
        $datatables = Datatables::of($users)
            ->setRowAttr([
                'class' => function ($users) {
                    if ($users->management_user == 1) {
                        return "is_management_user";
                    }
                },
            ])
            ->editColumn('role', function ($user) {
                return $user->role . ' - ' . $user->department;
            })
            ->editColumn('status', function ($user) {
                return (($user->status) ? 'Enabled' : 'Disabled');
            })
            ->editColumn('designation', function ($user) {
                return (($user->designation_name != null) ? $user->designation_name : $user->designation);
            })
            ->filterColumn('bg.name', function ($query, $keyword) {
                $query->where('bg.id', $keyword);
            })
            ->removeColumn('department')
            ->addColumn('ahat', function ($user) {
                $type = '-';

                if($user->hat == 1 || empty($user->hat)){
                    $type = 'Multiple Hubs';
                }else if ($user->hat == 2){
                    $type = 'Default Hubs';
                }else if ($user->hat == 3){
                    $type = 'Zonal Hubs';
                }

                $admin_hubs = AdminHub::where('admin_id', $user->id)->pluck('hub_id');

                if($admin_hubs->isNotEmpty()){
                    return '<button class="btn btn-sm btn-outline-info align-middle get_hub_access_type" data-target-id="' . $user->id . '">' . $type . '</button>';
                }else{
                    return 'No Hub Assigned';
                }
            })

            ->addColumn('ahat_excel', function ($user) {
               return $this->getHubsNameForUser($user->id);
            })
            ->addColumn('action', function ($user) {
                if (session('role_id') == 1 || count(array_intersect([83, 84, 542, 620], session('permissions'))) !== 0) {
                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    $enable_button = '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    $disable_button = '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';

                    $phone_edit_button = '<button type="button" class="dropdown-item phone"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Phone No. Update</div></button>';

                    $rejoin_button = '<button type="button" class="dropdown-item rejoin" data-target-id="' . $user->id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Rejoin Admin</div></button>';

                    $lost_hub_user_shipment_button = '<button type="button" class="dropdown-item lost_hub_user_shipment" data-target-id="' . $user->id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">User Lost Shipment Hub</div></button>';


                    $set_hub_access = '<button type="button" class="dropdown-item set_hub_access" data-target-id="' . $user->id . '" data-target-hub_access_type="' . $user->hat . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Set Hub Access</div></button>';

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
                        } else {
                            $dropdown .= $enable_button;
                        }
                    }

                    if (session('role_id') == 1 || in_array(542, session('permissions'))) {
                        $dropdown .= $phone_edit_button;
                    }

                    if (session('role_id') == 1 || in_array(979, session('permissions'))) {
                        $dropdown .= $lost_hub_user_shipment_button;
                    }

                    if (session('role_id') == 1 || in_array(620, session('permissions'))) {
                        if ($user->status == 0 && $user->first_inactive == 1) {
                            $dropdown .= $rejoin_button;
                        }
                    }

                    if (session('role_id') == 1 || in_array(620, session('permissions'))) {
                        if ($user->status == 0 && $user->first_inactive == 1) {
                            $dropdown .= $rejoin_button;
                        }
                    }

                    if (session('role_id') == 1 || in_array(1004, session('permissions'))) {
                        if ($user->admin_dept_id == 6) {
                            $dropdown .= $set_hub_access;
                        }
                    }

                    $dropdown .= '
                      </div>
                    </div>
                ';

                    return $dropdown;
                } else {
                    return '';
                }
            })
            ->filterColumn('status', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                if (strpos('enabled', $keyword) !== FALSE) {
                    $query->where('admins.status', '=', 1);
                } else if (strpos('disabled', $keyword) !== FALSE) {
                    $query->where('admins.status', '=', 0);
                } else {
                    $query->whereRaw('FALSE');
                }
            })
            ->filterColumn('role', function ($query, $keyword) {
                $keyword = str_replace(' ', '', str_replace('-', '', strtolower($keyword)));

                if ($keyword != '') {
                    $query->where('ar.name', 'like', '%' . $keyword . '%')->orWhere('ad.name', 'like', '%' . $keyword . '%');
                }
            })->rawColumns(['action','ahat']);

        return $datatables->make(true);
    }

    public function user_email(Request $request)
    {
        if ($request->filled('email')) {
            $email = Admin::where('email', $request->input('email'));

            if ($request->has('id')) {
                $email = $email->where('id', '!=', $request->input('id'));
            }

            if (!$email->exists()) {
                return 'true';
            } else {
                return 'false';
            }
        } else {
            return 'false';
        }
    }

    public function user_trax_id(Request $request)
    {
        if ($request->filled('trax_id')) {
            $trax_id = Admin::where('trax_id', $request->input('trax_id'));

            if ($request->has('id')) {
                $trax_id = $trax_id->where('id', '!=', $request->input('id'));
            }

            if (!$trax_id->exists()) {
                return 'true';
            } else {
                return 'false';
            }
        } else {
            return 'false';
        }
    }

    public function user_status(Request $request)
    {
        $admin = Admin::find($request->id);

        if ($admin) {
            $admin->status = $request->status;
            $admin->updated_by = Auth::id();
            $admin->save();

            $employee = Employee::where('trax_id', $admin->trax_id)->where('trax_id', '!=', null);
            if ($employee->exists()) {
                $employee = $employee->first();
                if ($request->status) {
                    $employee->status_id = AdminHumanResourseController::GetStatusOfEmployee($employee->id);

                } else {
                    $employee->status_id = 2;
                }
                $employee->first_inactive = 1;
                $employee->update();
            }

            if ($request->status) {
                return ['status' => 0, 'success' => 'Admin has been enabled'];
            } else {
                return ['status' => 0, 'success' => 'Admin has been disabled'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Admin with given ID is present'];
        }
    }

    // public function user_add_index()
    // {
    //     if (!in_array(session('role_id'), [1, 58])) {
    //         $roles = AdminRole::with('department')->where('id', '!=', 1)->where('is_active', 1)->where('department_id', session('department_id'))->get();
    //     } else {
    //         $roles = AdminRole::with('department')->where('is_active', 1)->get();
    //     }
    //     $hubs = City::where('hub', 1)->get();
    //     $shifts = EmployeeShift::where('status', 1)->get();
    //     $designations = EmployeeDesignation::where('status', 1)->with('department')->get();
    //     return view('admin.user_management.user.add.index')->with(['roles' => $roles, 'hubs' => $hubs, 'shifts' => $shifts, 'designations' => $designations]);
    // }

    public function user_add_index()
    {
        if (!in_array(session('role_id'), [1, 58])) {
            $roles = AdminRole::with('department')->where('id', '!=', 1)->where('is_active', 1)->where('department_id', session('department_id'))->get();
        } else {
            $roles = AdminRole::with('department')->where('is_active', 1)->get();
        }
        $categories = BusinessCategory::get();
        $hubs = City::where('hub', 1)->get();
        $shifts = EmployeeShift::where('status', 1)->get();
        $designations = EmployeeDesignation::where('status', 1)->with('department')->get();
        return view('admin.user_management.user.add.index')->with(['roles' => $roles, 'hubs' => $hubs, 'shifts' => $shifts, 'designations' => $designations, 'categories' => $categories]);
    }

    public function user_add_store(Request $request)
    {
        $employee_id = null;
        $duplicate_account = $request->has('duplicate_account') ? true : false;
        $admin = new Admin();

        $admin->name = $request->input('name');
        $admin->email = $request->input('email');
        $admin->phone_number = $request->input('phone_number');
        $admin->official_phone_number = $request->input('official_phone_number');
        $admin->role_id = $request->input('role_id');
        $admin->default_hub_id = $request->input('default_hub');
        $admin->password = bcrypt($request->input('pin'));
        $admin->dummy_pin = $request->input('pin');
        $admin->shift_id = $request->input('shift_id');
        $admin->designation_id = $request->input('designation_id');

        if (!$duplicate_account) {
            $admin->cnic = $request->input('cnic');
            if ($request->trax_id != null) {
                $trax_id = $request->trax_id;
                $employee = Employee::where('trax_id', $request->trax_id);
                if ($employee->exists()) {
                    $employee = $employee->first();
                    $employee_id = $employee->id;
                } else {
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

            } else {
                $global_setting = GlobalSettings::where('type', 'latest_employee_id');

                if ($global_setting->exists()) {
                    $global_setting = $global_setting->first();
                    $trax_id = $global_setting->setting_value + 1;
                    $global_setting->setting_value = $trax_id;
                    $global_setting->save();
                    $trax_id = 'Trax' . str_pad($trax_id, 5, '0', STR_PAD_LEFT);

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
                } else {
                    $trax_id = null;
                }
            }

            $admin->trax_id = $trax_id;
            $admin->employee_id = $employee_id;
        } else {
            $admin->duplicate_user = 1;
        }

        $admin->save();



        if ($request->has('hub_ids')) {
            foreach ($request->input('hub_ids') as $hub_id) {
                $admin_hub = new AdminHub();

                $admin_hub->admin_id = $admin->id;
                $admin_hub->hub_id = $hub_id;

                $admin_hub->save();
            }
        }

        return redirect()->route('admin.user_management.users.index')->with(['success' => 'User: ' . $request->input('name') . ' has been added!']);
    }

    public function user_assign_hub(Request $request)
    {
        $user_ids = explode(',', $request->id);
        foreach ($user_ids as $user_id) {
            $update_user = false; 
            foreach ($request->input('hubs') as $hub_id) {
                $admin_hub_exist = AdminHub::where('admin_id', $user_id)->where('hub_id', $hub_id)->first();

                if (!$admin_hub_exist) {
                    $admin_hub = new AdminHub();

                    $admin_hub->hub_id = $hub_id;
                    $admin_hub->admin_id = $user_id;

                    $admin_hub->save();
                    $update_user = true;
                }
            }
            if($update_user){
                $admin = Admin::find($user_id);
                $admin->updated_by = Auth::id();
                $admin->updated_at = Carbon::now();
                $admin->save();
            }
        }
        return redirect()->back()->with(['status' => 1, 'success' => "Hubs has been Assigned successfully!"]);
    }

    // public function user_update_index($id)
    // {
    //     if (!in_array(session('role_id'), [1, 58])) {
    //         $roles = AdminRole::with('department')->where('is_active', 1)->where('id', '!=', 1)->where('department_id', session('department_id'))->get();
    //     } else {
    //         $roles = AdminRole::with('department')->where('is_active', 1)->get();
    //     }
    //     $hubs = City::where('hub', 1)->get();
    //     $user = Admin::find($id);
    //     $user_hubs = $user->hubs->pluck('hub_id')->toArray();
    //     $shifts = EmployeeShift::where('status', 1)->get();
    //     $designations = EmployeeDesignation::where('status', 1)->with('department')->get();

    //     ActivityTrailController::createActivityTrailLog(Auth::id(), 231, 1);
    //     return view('admin.user_management.user.update.index')->with(['roles' => $roles, 'hubs' => $hubs, 'user' => $user, 'user_hubs' => $user_hubs, 'shifts' => $shifts, 'designations' => $designations]);

    // }

    public function user_update_index($id)
    {
        if (!in_array(session('role_id'), [1, 58])) {
            $roles = AdminRole::with('department')->where('is_active', 1)->where('id', '!=', 1)->where('department_id', session('department_id'))->get();
        } else {
            $roles = AdminRole::with('department')->where('is_active', 1)->get();
        }
        $categories = BusinessCategory::get();
        $hubs = City::where('hub', 1)->get();
        $user = Admin::find($id);
        $user_hubs = $user->hubs->pluck('hub_id')->toArray();
        $shifts = EmployeeShift::where('status', 1)->get();
        $designations = EmployeeDesignation::where('status', 1)->with('department')->get();
        ActivityTrailController::createActivityTrailLog(Auth::id(), 231, 1);
        return view('admin.user_management.user.update.index')->with(['roles' => $roles, 'hubs' => $hubs, 'user' => $user, 'user_hubs' => $user_hubs, 'shifts' => $shifts, 'designations' => $designations, 'categories' => $categories]);

    }

    public function validate_phone(Request $request)
    {
        $id = null;
        if ($request->has('id')) {
            $id = $request->id;
        }

        $phone_number = null;
        if ($request->has('phone_number')) {
            $phone_number = $request->phone_number;
        }

        if ($request->has('official_phone_number')) {
            $phone_number = $request->official_phone_number;
        }

        $phone_validate = Admin::where('id', '!=', $id)->where(function ($query) use ($phone_number) {
            $query->where('phone_number', $phone_number)
                ->orwhere('official_phone_number', $phone_number);
        })->exists();

        if ($phone_validate) {
            return "false";
        }

        $trax_id = null;
        if ($id != null) {
            $admin = Admin::find($id);
            $trax_id = $admin->trax_id;
        }

        $phone_validate = Employee::where('trax_id', '!=', $trax_id)->where(function ($query) use ($phone_number) {
            $query->where('phone_number', $phone_number)
                ->orwhere('official_phone_number', $phone_number);
        })->exists();

        if ($phone_validate) {
            return "false";
        }

        return "true";
    }

    public function user_update_store(Request $request, $id)
    {

        $admin = Admin::find($id);

        $admin->name = $request->input('name');
        $admin->email = $request->input('email');
        $admin->phone_number = $request->input('phone_number');
        $admin->official_phone_number = $request->input('official_phone_number');
        if ($admin->role_id != $request->input('role_id')) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 232, 1);
        }
        $super_admins = [3, 5, 7, 665];
        $hr_roles = AdminRole::where('department_id', 10)->where('is_active', 1)->pluck('id')->toArray();
        if (in_array(Auth::id(), $super_admins) && in_array($request->input('role_id'), $hr_roles)) {
            $admin->role_id = $request->input('role_id');
            $admin->designation_id = $request->input('designation_id');
        } else {
            if (!in_array($request->input('role_id'), $hr_roles)) {
                $admin->role_id = $request->input('role_id');
                $admin->designation_id = $request->input('designation_id');
            } else {
                $role = AdminRole::find($request->input('role_id'));
                NotificationsController::send(186, Auth::id(), $role->name);
            }
        }
        $admin->default_hub_id = $request->input('default_hub');
        $admin->updated_by = Auth::id();
        $admin->updated_at = Carbon::now();
        $admin->shift_id = $request->input('shift_id');

        if ($request->filled('pin')) {
            $admin->password = bcrypt($request->input('pin'));
            $admin->dummy_pin = $request->input('pin');
        }


        if ($admin->duplicate_user == 0) {
            $admin->cnic = $request->input('cnic');
            $admin->trax_id = $request->trax_id;
        }
        $admin->save();

        if ($request->has('hub_ids')) {
            $current_hub_ids = AdminHub::where('admin_id', $id)->pluck('hub_id')->toArray();

            $delete_hub_ids = array_diff($current_hub_ids, $request->input('hub_ids'));
            $new_hub_ids = array_diff($request->input('hub_ids'), $current_hub_ids);

            AdminHub::where('admin_id', $id)->whereIn('hub_id', $delete_hub_ids)->delete();

            $admin_hub_access = AdminHubAccessType::where('admin_id', $id);

            if($admin_hub_access->exists()) {
                $admin_hub_access->latest()->first()->update([
                    'hub_access_type' => 1
                ]);
            }
            foreach ($new_hub_ids as $hub_id) {
                $admin_hub = new AdminHub();

                $admin_hub->admin_id = $id;
                $admin_hub->hub_id = $hub_id;

                $admin_hub->save();
            }
            $deleted_name_hubs = City::whereIn('id', $delete_hub_ids)->pluck('name')
            ->implode(', ');


        } else {
            $delete_hub_ids = [];
            $current_hub_ids = AdminHub::where('admin_id', $id)->pluck('hub_id')->toArray();
            $deleted_name_hubs = City::whereIn('id', $delete_hub_ids)->pluck('name')
            ->implode(', ');
            AdminHub::where('admin_id', $id)->delete();
        }
        if(!empty($deleted_name_hubs)) {
            $array['Deleted Hubs'] = $deleted_name_hubs;
            $record = new UserRoleManagementLog;
            $record->changed_by_id = Auth::id();
            $record->data = json_encode($array);
            $record->changed_in_record_id = $id;
            $record->screen_name = 'User Management';
            $record->save();
        }

        if ($admin->duplicate_user == 0) {
            $employee = Employee::where('trax_id', $admin->trax_id)->where('trax_id', '!=', null);
            if ($employee->exists()) {
                $employee = $employee->first();
                $employee->designation_id = $admin->designation_id;
                $employee->department_id = EmployeeDesignation::find($admin->designation_id)->department_id ?? null;
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
        }
        return redirect()->route('admin.user_management.users.index')->with(['success' => 'User: ' . $request->input('name') . ' has been updated!']);
    }

    public function role_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 374);
        $departments = AdminDepartment::all();
        $modules = Module::all();
        $permissions = ModulePermission::all();

        return view('admin.user_management.role.index')->with(['departments' => $departments, 'modules' => $modules, 'permissions' => $permissions]);
    }

    public function role_list(Request $request)
    {
        $roles = AdminRole::join('admin_departments as ad', 'admin_roles.department_id', '=', 'ad.id')
            ->join('admins as a', 'admin_roles.updated_by', '=', 'a.id')
            ->select('admin_roles.id', 'admin_roles.name', 'admin_roles.is_active', 'ad.name as department', 'admin_roles.created_at as created', 'admin_roles.updated_at as updated', 'a.name as updated_by');

        $datatables = Datatables::of($roles)
            ->addColumn('is_active', function ($role) {
                if ($role->is_active == 1) {
                    return 'Enabled';
                } else {
                    return 'Disabled';
                }
            })
            ->addColumn('action', function ($role) {
                if (session('role_id') == 1 || in_array(87, session('permissions'))) {
                    if ($role->is_active == 1) {
                        $enable_disable = '<button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    } else {
                        $enable_disable = '<button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    }

                    return '<div class="btn-group">
                          <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                          <div class="dropdown-menu dropdown-menu-sm">
                            <button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                            <button type="button" class="dropdown-item duplicate"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-copy"></i></div><div class="col-9 offset-1">Duplicate</div></button>
                            ' . $enable_disable . '
                          </div>
                        </div>
                ';
                } else {
                    return '';
                }
            })->rawColumns(['action']);

        return $datatables->make(true);
    }

    public function role_status(Request $request)
    {
        $admin_role = AdminRole::find($request->id);

        if ($admin_role) {

            if ($admin_role->is_active == 1) {
                $admin_role->is_active = 0;
                $admin_role->updated_by = Auth::id();
                $admin_role->save();
            } else {
                $admin_role->is_active = 1;
                $admin_role->updated_by = Auth::id();
                $admin_role->save();
            }



            if ($request->is_active) {
                return ['status' => 0, 'success' => 'Role has been enabled'];
            } else {
                return ['status' => 0, 'success' => 'Role has been disabled'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Role with given ID is present'];
        }
    }

    public function role_add_index()
    {
        $departments = AdminDepartment::get(['id', 'name']);
        $modules = Module::with('permissions')->get();

        return view('admin.user_management.role.add.index')->with(['departments' => $departments, 'modules' => $modules]);
    }

    public function role_permission_index()
    {
        $modules = Module::all();
        return view('admin.user_management.role.permissions.index')->with(['modules' => $modules]);
    }


    public function crm_role_permission_index()
    {
        $modules = ModulePermission::whereIn('id', [179, 184, 185, 186, 213,233, 234, 235, 236,310, 352, 353, 363,869,795,543,523,787,201,202,309])->get();
        return view('admin.user_management.role.permissions.crm_index')->with(['modules' => $modules]);
    }

    public function crm_update_index($ids)
    {

        $modules = ModulePermission::whereIn('id', [179, 184, 185, 186, 213,233, 234, 235, 236,310, 352, 353, 363,869,795,543,523,787,201,202,309])->get();
        return view('admin.user_management.role.add.crm_bulk_index')->with(['modules' => $modules, 'ids' => $ids]);

    }

    public function delete_crm_update_index($ids)
    {

        $modules = ModulePermission::whereIn('id', [179, 184, 185, 186, 213,233, 234, 235, 236,310, 352, 353, 363,869,795,543,523,787,201,202,309])->get();
        return view('admin.user_management.role.remove.crm_bulk_index')->with(['modules' => $modules, 'ids' => $ids]);

    }


    public function delete_bulk_remove_store(Request $request)
    {

        $roles = explode(',', $request->ids);
        $permission_ids = $request->module_ids;
        if ($request->has('module_ids')) {
            foreach ($roles as $role) {
                foreach ($permission_ids as $permission_id) {
                    
                    AdminRoleModulePermission::where('role_id', $role)->where('permission_id', $permission_id)->delete();

                }
            }
        }

        return redirect()->route('admin.crm.permissions')->with(['success' => 'Deleted Succesfully']);

    }

    public function module_permission(Request $request)
    {

        $modules = Module::with('permissions')->find($request->module_id)->permissions;
        return $modules;
    }

    public function crm_role_permission_list(Request $request)
    {

        $permissions = array();
        if (isset($request->permissions)) {
            $permissions = $request->permissions;
            $permissions_data = ModulePermission::whereIn('id', $permissions)->get();
        }

        $admin_perm = array();
        $admin_roles = AdminRole::join('admin_role_module_permissions', 'admin_role_module_permissions.role_id', 'admin_roles.id')
            ->join('admin_departments', 'admin_departments.id', 'admin_roles.department_id')
            ->whereIn('admin_role_module_permissions.permission_id', $permissions)
            ->select(['admin_roles.id', 'admin_roles.name', 'admin_departments.name as department'])
            ->get();

        foreach ($admin_roles as $admin_role_key => $admin_role_value) {
            $admin_perm[$admin_role_key] = $admin_role_value;

            $admin_roles_perm = AdminRoleModulePermission::leftjoin('module_permissions', 'module_permissions.id', 'admin_role_module_permissions.permission_id')
                ->whereIn('module_permissions.id', $permissions)
                ->where('admin_role_module_permissions.role_id', $admin_role_value->id)
                ->pluck('module_permissions.id')
                ->toArray();
            $admin_perm[$admin_role_key]['permissions'] = $admin_roles_perm;

        }

        $data['permissions'] = $permissions;
        $data['admin_perm'] = $admin_perm;
        $data['permissions_data'] = $permissions_data;

        return $data;
    }

    public function role_permission_list(Request $request)
    {
        $permissions = array();
        $module_id = $request->module_id;
        if (isset($request->permissions)) {
            $permissions = $request->permissions;
            $permissions_data = ModulePermission::where('module_id', $request->module_id)->whereIn('id', $permissions)->get();

        } else {
            $permissions = Module::with('permissions')->find($request->module_id)->permissions->pluck('id')->toArray();
            $permissions_data = Module::with('permissions')->find($request->module_id)->permissions;
        }

        $admin_perm = array();

        $admin_roles = AdminRole::join('admin_role_module_permissions', 'admin_role_module_permissions.role_id', 'admin_roles.id')
            ->join('admin_departments', 'admin_departments.id', 'admin_roles.department_id')
            ->whereIn('admin_role_module_permissions.permission_id', $permissions)
            ->select(['admin_roles.id', 'admin_roles.name', 'admin_departments.name as department'])
            ->groupBy('admin_roles.name')
            ->orderBy('admin_roles.id')
            ->get();

        foreach ($admin_roles as $admin_role_key => $admin_role_value) {
            $admin_perm[$admin_role_key] = $admin_role_value;

            $admin_roles_perm = AdminRoleModulePermission::leftjoin('module_permissions', 'module_permissions.id', 'admin_role_module_permissions.permission_id')
                ->whereIn('module_permissions.id', $permissions)
                ->where('admin_role_module_permissions.role_id', $admin_role_value->id)
                ->pluck('module_permissions.id')
                ->toArray();
            $admin_perm[$admin_role_key]['permissions'] = $admin_roles_perm;

        }

        $data['module_id'] = $module_id;
        $data['permissions'] = $permissions;
        $data['admin_perm'] = $admin_perm;
        $data['permissions_data'] = $permissions_data;


        return $data;
    }

    public function module_permission_update_store(Request $request)
    {

        $module_id = $request->update_module_id;
        $curr_module_perm_id = explode(",", $request->update_module_permission);
        $permissions = $request->permission_ids;

        // selected permissions
        $module_all_permissions = ModulePermission::where('module_id', $request->update_module_id)->pluck('id')->toArray();

        // admins id who has selected module and permission access
        $all_admin_role_ids = AdminRoleModulePermission::whereIn('permission_id', $curr_module_perm_id)->distinct('role_id')->pluck('role_id')->toArray();


        $role_ids = array();


        foreach ($permissions as $key => $value) {

            // collecting role ids and permission id from form data
            $role_ids[] = $key;
            $role_id = $key;
            $permission_id = $value;

            $result = AdminRoleModulePermission::where('role_id', $role_id)->whereIn('permission_id', $module_all_permissions)->delete();

            foreach ($permission_id as $key => $value) {

                $AdminRoleModulePermission = new AdminRoleModulePermission();
                $AdminRoleModulePermission->role_id = $role_id;
                $AdminRoleModulePermission->permission_id = $value;
                $AdminRoleModulePermission->save();
            }

            $admin_role = AdminRole::find($role_id);
            $admin_role->updated_by = Auth::id();
            $admin_role->save();

        }

        $delete_permission_ids = array_diff($all_admin_role_ids, $role_ids);
        $result = AdminRoleModulePermission::whereIn('role_id', $delete_permission_ids)->whereIn('permission_id', $curr_module_perm_id)->delete();

        return redirect()->route('admin.user_management.roles.permissions.index')->with(['success' => 'Role as per permission has been updated!']);
    }

    public function role_add_store(Request $request)
    {
        $admin_role = new AdminRole();

        $admin_role->name = $request->input('name');
        $admin_role->department_id = $request->input('department_id');
        $admin_role->updated_by = Auth::id();

        $admin_role->save();

        if ($request->has('permission_ids')) {
            foreach ($request->input('permission_ids') as $permission_id) {
                $admin_role_module_permission = new AdminRoleModulePermission();

                $admin_role_module_permission->role_id = $admin_role->id;
                $admin_role_module_permission->permission_id = $permission_id;

                $admin_role_module_permission->save();
            }
        }

        return redirect()->route('admin.user_management.roles.index')->with(['success' => 'Role: ' . $request->input('name') . ' has been added!']);
    }

    public function role_duplicate(Request $request)
    {

        $id = $request->input('role_id');
        $admin_role_data = AdminRole::find($id);

        if ($admin_role_data) {
            $admin_role = new AdminRole();
            $admin_role->name = $request->input('designation');
            $admin_role->department_id = $admin_role_data->department_id;
            $admin_role->updated_by = Auth::id();
            $admin_role->save();

            $current_permission_ids = AdminRoleModulePermission::where('role_id', $id)->pluck('permission_id')->toArray();
            foreach ($current_permission_ids as $permission_id) {
                $admin_role_module_permission = new AdminRoleModulePermission();

                $admin_role_module_permission->role_id = $admin_role->id;
                $admin_role_module_permission->permission_id = $permission_id;
                $admin_role_module_permission->save();
            }
            return redirect()->back()->with(['success' => 'Role: ' . $request->input('name') . ' has been added!']);
        }
    }

    public function role_update_index($id)
    {
        $departments = AdminDepartment::get(['id', 'name']);
        $modules = Module::with('permissions')->get();
        $role = AdminRole::find($id);
        $permissions = $role->module_permissions->pluck('permission_id')->toArray();
        ActivityTrailController::createActivityTrailLog(Auth::id(), 229, 1);
        return view('admin.user_management.role.update.index')->with(['departments' => $departments, 'modules' => $modules, 'role' => $role, 'permissions' => $permissions]);
    }

    public function role_update_store(Request $request, $id)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 230, 1);
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

            foreach ($new_permission_ids as $permission_id) {
                $admin_role_module_permission = new AdminRoleModulePermission();

                $admin_role_module_permission->role_id = $id;
                $admin_role_module_permission->permission_id = $permission_id;

                $admin_role_module_permission->save();
            }

            $deleted_name_permissions = ModulePermission::whereIn('id', $delete_permission_ids)->pluck('name')
            ->implode(', ');
            
        } else {
            $current_permission_ids = AdminRoleModulePermission::where('role_id', $id)->pluck('permission_id')->toArray();
            $deleted_name_permissions = ModulePermission::whereIn('id', $current_permission_ids)->pluck('name')
            ->implode(', ');
            AdminRoleModulePermission::where('role_id', $id)->delete();
        }
            if(!empty($deleted_name_permissions)) {
                $array['Deleted Permissions'] = $deleted_name_permissions;
                $record = new UserRoleManagementLog;
                $record->changed_by_id = Auth::id();
                $record->data = json_encode($array);
                $record->changed_in_record_id = $id;
                $record->screen_name = 'Role Management';
                $record->save();
            }
            

        return redirect()->route('admin.user_management.roles.index')->with(['success' => 'Role: ' . $request->input('name') . ' has been updated!']);
    }

    public function admin_otp_index()
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 544);
        $settings = GlobalSettings::where('type', 'admin_otp');
        if ($settings->doesntExist()) {
            $settings = new GlobalSettings();
            $settings->setting_value = 1;
            $settings->type = "admin_otp";
            $settings->save();
        } else {
            $settings = $settings->first();
        }

        return view('admin.otp.admin')->with(['setting' => $settings]);
    }

    public function admin_otp_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 545);
        }
        $admins = Admin::select('cities.name as city', 'admins.id as id', 'admins.name as name', 'admins.otp as otp', 'admins.reset_pin_otp as reset_pin_otp', 'admins.last_login_attempt')
            ->where('admins.status', 1)
            ->join('cities', 'admins.default_hub_id', '=', 'cities.id')
            ->whereNotNull('admins.otp');
        if (!in_array(session('role_id'), [1, 58, 61, 56, 71, 70, 63, 104])) {
            $admins = $admins->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->where('ar.department_id', session('department_id'));
        }
        $datatable = Datatables::of($admins);
        return $datatable->make(true);
    }

    public function admin_otp_update(Request $request)
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 546);
        $settings = GlobalSettings::where('type', 'admin_otp');
        if ($settings->doesntExist()) {
            $settings = new GlobalSettings();
            $settings->type = "admin_otp";
        } else {
            $settings = $settings->first();
        }

        $settings->setting_value = $request->has('admin_otp_toggle') ? 1 : 0;
        $settings->save();

        return back()->with(['success' => "Admin OTP Updated Successfully"]);
    }


    public function user_info(Request $request)
    {
        $admin_id = $request->admin_id;

        if ($admin_id) {
            $admin = Admin::find($admin_id);
            if ($admin) {
                return response()->json(['status' => 0, 'phone' => $admin->phone_number]);

            }
            return response()->json(['status' => 1, 'error' => 'User not found!']);

        }
        return response()->json(['status' => 1, 'error' => 'User not found!']);
    }
    public function user_phone_update(Request $request)
    {
        $admin_id = $request->id;
        $phone = $request->phone_number;
        $validate = $this->validate_phone($request);

        if ($validate == "false") {
            return redirect()->back()->with('error', 'Phone Number Already Exists!!');
        }

        if ($admin_id) {
            $admin = Admin::find($admin_id);
            if ($admin) {
                $admin->phone_number = $phone;
                $admin->updated_by = Auth::id();
                $admin->save();

                $employee = Employee::where('trax_id', $admin->trax_id)->where('trax_id', '!=', null);
                if ($employee->exists()) {
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

    public function rider_delivery_note_otp_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 414);
        return view('admin.otp.rider_delivery_note');
    }

    public function rider_delivery_note_otp_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 415);
        }
        $riders = Rider::join('cities', 'riders.city_id', '=', 'cities.id')
            ->join('cities as c', 'cities.hub_id', '=', 'c.id')
            ->select('riders.id as id', 'riders.name as name', 'riders.otp_date as otp_date', 'riders.phone as phone_no', 'riders.delivery_note_otp as otp', 'c.name as hub')
            ->where('riders.status', 1)
            ->whereNotNull('delivery_note_otp');
        $datatable = Datatables::of($riders);
        return $datatable->make(true);
    }

    public function role_bulk_add_index($ids)
    {

        $modules = Module::with('permissions')->get();

        return view('admin.user_management.role.add.bulk_index')->with(['modules' => $modules, 'ids' => $ids]);
    }


    public function role_crm_bulk_add_store(Request $request)
    {
        $role_ids = explode(',', $request->ids);
        $permission_ids = $request->module_ids;
    
        foreach ($role_ids as $role) {
            if (is_string($role)) {
                $admin_role = AdminRole::find($role);
                if ($admin_role) {
                    if ($request->has('module_ids')) {
                        $admin_role->updated_by = Auth::id();
                        $admin_role->save();
    
                        foreach ($permission_ids as $permission_id) {
                            $existing_permission = AdminRoleModulePermission::where('role_id', $role)
                                ->where('permission_id', $permission_id);
    
                            if (!$existing_permission->exists()) {
                                $admin_role_module_permission = new AdminRoleModulePermission;
                                $admin_role_module_permission->role_id = $role;
                                $admin_role_module_permission->permission_id = $permission_id;
                                $admin_role_module_permission->save();
                            }
                        }
                    }
                } else {
                    return redirect()->route('admin.crm.permissions')->with(['error' => 'Error']);
                }
            }
        }
    
        return redirect()->route('admin.crm.permissions')->with(['success' => 'Roles have been updated!']);
    }
    





    public function role_bulk_add_store(Request $request)
    {

        $roles = explode(',', $request->ids);
        $permission_ids = $request->permission_ids;
        foreach ($roles as $role) {
            if ($request->has('permission_ids')) {

                $admin_role = AdminRole::find($role);
                $admin_role->updated_by = Auth::id();
                $admin_role->save();

                foreach ($permission_ids as $permission_id) {

                    $check_exists = AdminRoleModulePermission::where('role_id', $role)->where('permission_id', $permission_id);

                    if (!$check_exists->exists()) {

                        $admin_role_module_permission = new AdminRoleModulePermission();
                        $admin_role_module_permission->role_id = $role;
                        $admin_role_module_permission->permission_id = $permission_id;
                        $admin_role_module_permission->save();

                    }
                }
            }

        }

        return redirect()->route('admin.user_management.roles.index')->with(['success' => 'Roles has been updated!']);

    }


    public function role_bulk_remove_index($ids)
    {

        $modules = Module::with('permissions')->get();

        return view('admin.user_management.role.remove.bulk_index')->with(['modules' => $modules, 'ids' => $ids]);
    }

    public function role_bulk_remove_store(Request $request)
    {

        $roles = explode(',', $request->ids);
        $permission_ids = $request->permission_ids;
        if ($request->has('permission_ids')) {
            foreach ($roles as $role) {

                foreach ($permission_ids as $permission_id) {

                    AdminRoleModulePermission::where('role_id', $role)->where('permission_id', $permission_id)->delete();

                }
            }
        }

        return redirect()->route('admin.user_management.roles.index')->with(['success' => 'Roles has been updated!']);

    }

    public function lost_hub_user_shipment(Request $request){

        UserLostShipmentHub::where('admin_id', $request->admin_id)->delete();
        foreach ($request->select_lost_hub_user_shipment as $user_hub) {
            UserLostShipmentHub::create([
                'admin_id' => $request->admin_id,
                'hub_id' => $user_hub,
            ]);
        }

        return redirect()->back()->with(['success' => 'Hub has been updated!']);
    }

    public function get_lost_hub_user_shipment(Request $request) {
        $userLostShipments = UserLostShipmentHub::where('admin_id', $request->admin_id)->get()->pluck('hub_id')->toArray();
        return response()->json(['success' => true, 'data' => $userLostShipments]);
    }
    
    public function add_management_users(Request $request)
    {
        $admins = $request->userIDS;

        foreach($admins as $admin){
            $admin = Admin::where('id', $admin);
            if($admin->exists()){
                $admin = $admin->first();
                $admin->management_user = 1;
                $admin->save();    
            }
        }

        return response()->json(['status' => 1, 'success' => 'Added Successfully!']);
    }



    public function set_hub_access(Request $request)
    {
        $hubAccessType = $request->input('hub_access');
        $adminId = $request->input('set_hub_admin_id');
        $admin = Admin::find($adminId);


        if (empty($admin->default_hub_id)) {
            return redirect()->back()->with('error', 'Please assign a default hub first!');
        }
        
        $defaultHub = $admin->default_hub_id;
    
        $oldHubs = AdminHub::where('admin_id', $adminId)->pluck('hub_id')->toArray();

        DB::transaction(function () use ($adminId, $hubAccessType, $defaultHub, $oldHubs) {
            try {
                switch ($hubAccessType) {
                    case 1:
                        $this->assignMultipleHubs($adminId, $oldHubs);
                        break;
        
                    case 2:
                        AdminHub::where('admin_id', $adminId)->delete();
                        $this->assignDefaultHub($adminId, $defaultHub, $oldHubs);
                        break;
        
                    case 3:
                        AdminHub::where('admin_id', $adminId)->delete();
                        $this->assignZonalHubs($adminId, $defaultHub, $oldHubs);
                        break;
        
                    default:
                        throw new \InvalidArgumentException('Invalid hub access type');
                }
            } catch (\Exception $e) {
                Log::error('Transaction failed: ' . $e->getMessage());
            }
        });
        

        return redirect()->back()->with(['success' => 'Hub has been updated!']);
    }

    private function assignMultipleHubs($adminId, $oldHubs)
    {
        AdminHubAccessType::create([
            'admin_id' => $adminId,
            'hub_access_type' => 1,
            'previous_assigned_hubs' => implode(',' , $oldHubs),
            'new_assigned_hubs' => implode(',' , $oldHubs),
            'updated_by' => Auth::id()
        ]);
    }

    private function assignDefaultHub($adminId, $defaultHub, $oldHubs)
    {
        AdminHub::insert([
            'admin_id' => $adminId,
            'hub_id' => $defaultHub
        ]);

        AdminHubAccessType::create([
            'admin_id' => $adminId,
            'hub_access_type' => 2,
            'previous_assigned_hubs' => implode(',' , $oldHubs),
            'new_assigned_hubs' => $defaultHub,
            'updated_by' => Auth::id()
        ]);
    }

    private function assignZonalHubs($adminId, $defaultHub, $oldHubs)
    {
        $city = City::where(['hub_id' => $defaultHub, 'hub' => 1])->first();

        if (!$city) {
            return redirect()->back()->with(['error' => 'City Doesn\'t Exist!']);
        }
        
        $zone_id = $city->zone_id;
        
        if (!$zone_id) {
            return redirect()->back()->with(['error' => 'Zone Doesn\'t Exist!']);
        }
        
        $zonalHubs = City::where(['zone_id' => $zone_id, 'status' => 1, 'hub' => 1])->pluck('id')->toArray();
        $existingHubIds = AdminHub::where('admin_id', $adminId)->pluck('hub_id')->toArray();
        
        $insertData = [];
        foreach ($zonalHubs as $hubId) {
            if (!in_array($hubId, $existingHubIds)) {
                $insertData[] = [
                    'admin_id' => $adminId,
                    'hub_id' => $hubId,
                ];
            }   
        }

        if(!empty($insertData)){
            AdminHub::insert($insertData);
        }

        AdminHubAccessType::create([
            'admin_id' => $adminId,
            'hub_access_type' => 3,
            'previous_assigned_hubs' => implode(',' , $oldHubs),
            'new_assigned_hubs' => implode(',' , $zonalHubs),
            'updated_by' => Auth::id()
        ]);   
    }

    public function get_hub_access(Request $request) {
        return response()->json([
            'status' => 1,
            'hubs_name' => $this->getHubsNameForUser($request->id)
        ]);
    }

    private function getHubsNameForUser($user)
    {
        $adminHubs = AdminHub::where('admin_id', $user)->pluck('hub_id');

        if ($adminHubs->isEmpty()) {
            return 'No Hub Assigned';
        }

        $hubsNames = City::whereIn('id', $adminHubs)->pluck('name');
        return $hubsNames->implode(', ');
    }


    public function retail_otp_index()
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 833);
        $settings = GlobalSettings::where('type', 'admin_otp');
        if ($settings->doesntExist()) {
            $settings = new GlobalSettings();
            $settings->setting_value = 1;
            $settings->type = "admin_otp";
            $settings->save();
        } else {
            $settings = $settings->first();
        }

        return view('admin.otp.retail_otp')->with(['setting' => $settings]);
    }

    public function retail_otp_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 834);
        }
        $retail = RetailShipperInfo::select('cities.name as city', 'retail_shipper_infos.id as id', 'retail_shipper_infos.shipper_name as name','retail_shipper_infos.shipper_phone_no', 'retail_shipper_infos.retail_otp as otp', 'retail_shipper_infos.otp_expire_at')
            ->where('retail_shipper_infos.status', 1)
            ->join('cities', 'retail_shipper_infos.city_id', '=', 'cities.id')
            ->whereNotNull('retail_shipper_infos.retail_otp');
        if (!in_array(session('role_id'), [1, 58, 61, 56, 71, 70, 63, 104])) {
//            $retail = $retail->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->where('ar.department_id', session('department_id'));
        }
        $datatable = Datatables::of($retail);
        return $datatable->make(true);
    }
    
}