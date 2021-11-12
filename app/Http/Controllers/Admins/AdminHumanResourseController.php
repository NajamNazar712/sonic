<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\OperationRidersCategory;
use App\Http\Models\Admin\RiderType;
use App\Http\Models\BanksList;
use App\Http\Models\City;
use App\Http\Models\EmployeeShift;
use App\Http\Models\FnfSectionEmployee;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeAttachment;
use App\Http\Models\HR\EmployeeBankInformation;
use App\Http\Models\HR\EmployeeBloodGroup;
use App\Http\Models\HR\EmployeeDesignation;
use App\Http\Models\HR\EmployeeDesignationHub;
use App\Http\Models\HR\EmployeeDomicile;
use App\Http\Models\HR\EmployeeEducationalBackground;
use App\Http\Models\HR\EmployeeEmployementHistory;
use App\Http\Models\HR\EmployeeGender;
use App\Http\Models\HR\EmployeeMaritalStatus;
use App\Http\Models\HR\EmployeeMedicalInformation;
use App\Http\Models\HR\EmployeeNationality;
use App\Http\Models\HR\EmployeePayslip;
use App\Http\Models\HR\EmployeeReference;
use App\Http\Models\HR\EmployeeRelationship;
use App\Http\Models\HR\EmployeeReligion;
use App\Http\Models\HR\EmployeeStatus;
use App\Http\Models\HR\EmployeeType;
use App\Http\Models\HR\StaffCategory;
use App\Http\Models\ReportingLocation;
use App\Http\Models\Rider\RiderRequest;
use App\Http\Models\Rider\RidersIncentive;
use App\Http\Models\RiderCategory;
use App\Http\Models\Route;
use App\Http\Models\RouteType;
use App\Http\Models\Zone;
use App\RiderMainCategory;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Rider;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\Datatables\Datatables;
use Auth;

class AdminHumanResourseController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function download_docs()
    {    ActivityTrailController::createActivityTrailLog(Auth::id(),387);
        return view('admin.human_resource.download_docs');
    }
    public function allusers()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),54);
        // $riders = Rider::where('status', 1)->get();
        //         $admins = Admin::where('status', 1)->get();
        $roles = ['Admin', 'Rider'];
        $roles = collect($roles);
        return view('admin.human_resource.allusers')->with(['roles' => $roles]);
    }
    public function all_user_ajax(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),114);
        }
        $assigned_hubs = session('hubs');
        if (session('role_id') != 1) {
            if (count($assigned_hubs) > 0) {
                // $riders = Rider::where('status',1)->where('rider_type_id',1)->whereIn('city_id', $assigned_hubs)->get();
                $riders = Rider::join('cities', 'riders.city_id', '=', 'cities.id')
                    ->join('cities as c', 'cities.hub_id', '=', 'c.id')
                    ->select('c.name as hub', 'riders.id', 'riders.name', 'riders.trax_id', 'riders.phone', 'riders.cnic', 'riders.created_at as created_at')
                    ->where('rider_type_id', 1)
                    ->where('riders.status', 1)
                    ->whereIn('cities.hub_id', $assigned_hubs)->get();


                $admins = Admin::whereIn('default_hub_id', $assigned_hubs)->where('status', 1)->get();

                $users = array();
                if (count($riders) > 0) {
                    foreach ($riders as $rider) {
                        $user = array();
                        $user['id'] = $rider->id;
                        $user['name'] = $rider->name;
                        $user['cnic'] = $rider->cnic;
                        $user['phone'] = $rider->phone;
                        $user['trax_id'] = $rider->trax_id;
                        $user['role'] = 'Rider';
                        if ($rider->created_at) {
                            $user['created_at'] = date_format($rider->created_at, "Y/m/d H:i:s");
                        } else {
                            $user['created_at'] = $rider->created_at;
                        }
                        $users[] = $user;
                        $users = collect($users);
                    }
                }
                if (count($admins) > 0) {
                    foreach ($admins as $admin) {
                        $user = array();
                        $user['id'] = $admin->id;
                        $user['name'] = $admin->name;
                        $user['cnic'] = $admin->cnic;
                        $user['phone'] = $admin->phone_number;
                        $user['trax_id'] = $admin->trax_id;
                        $user['role'] = 'Admin';
                        if ($admin->created_at) {
                            $user['created_at'] = date_format($admin->created_at, "Y/m/d H:i:s");
                        } else {
                            $user['created_at'] = $admin->created_at;
                        }

                        $users[] = $user;
                        $users = collect($users);
                    }
                }

                return Datatables::of($users)
                    ->make(true);
            } else {
                $user = array();
                $user['id'] = NULL;
                $user['name'] = '';
                $user['cnic'] = '';
                $user['phone'] = '';
                $user['trax_id'] = '';
                $user['role'] = '';
                $user['created_at'] = '';
                $users[] = $user;
                $users = collect($users);
                return Datatables::of($users)
                    ->make(true);
            }
        } else {
           
            $riders = Rider::where('status', 1)->where('rider_type_id', 1)->get();

            $admins = Admin::where('status', 1)->get();

            $users = array();
            if (count($riders) > 0) {
                foreach ($riders as $rider) {
                    $user = array();
                    $user['id'] = $rider->id;
                    $user['name'] = $rider->name;
                    $user['cnic'] = $rider->cnic;
                    $user['phone'] = $rider->phone;
                    $user['trax_id'] = $rider->trax_id;
                    $user['role'] = 'Rider';
                    if ($rider->created_at) {
                        $user['created_at'] = date_format($rider->created_at, "Y/m/d H:i:s");
                    } else {
                        $user['created_at'] = $rider->created_at;
                    }
                    $users[] = $user;
                    $users = collect($users);
                }
            }
            if (count($admins) > 0) {
                foreach ($admins as $admin) {
                    $user = array();
                    $user['id'] = $admin->id;
                    $user['name'] = $admin->name;
                    $user['cnic'] = $admin->cnic;
                    $user['phone'] = $admin->phone_number;
                    $user['trax_id'] = $admin->trax_id;
                    $user['role'] = 'Admin';
                    if ($admin->created_at) {
                        $user['created_at'] = date_format($admin->created_at, "Y/m/d H:i:s");
                    } else {
                        $user['created_at'] = $admin->created_at;
                    }

                    $users[] = $user;
                    $users = collect($users);
                }
            }

            return Datatables::of($users)
                ->make(true);
        }
    }

    public function employee_directory_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),57);
        $rider_type = RiderType::all();
        $route = Route::all();
        $operation_rider_category = OperationRidersCategory::all();
        $rider_categories = RiderCategory::all();
        $rider_main_categories = RiderMainCategory::all();
        $route_types = RouteType::all();
        $employee_types = EmployeeType::all();
        $employee_statuses = EmployeeStatus::all();
        $employee_department = AdminDepartment::all();
        $employee_shifts = EmployeeShift::where('status',1)->get(['id','name']);
        $city = City::where('business_category_id', 1)->get();
        $staff_categories = StaffCategory::all();
        return view('admin.human_resource.employee_directory.index')->with(['cities' => $city,'employee_types'=>$employee_types,'rider_categories' => $rider_categories, 'rider_types'=>$rider_type, 'routes' => $route,'operation_rider_category' => $operation_rider_category,'route_types'=>$route_types,'employee_statuses'=>$employee_statuses,'employee_department'=>$employee_department,'rider_main_categories'=>$rider_main_categories,'employee_shifts'=>$employee_shifts, 'staff_categories' =>$staff_categories]);
    }

    public function employee_directory_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),117);
        }
        $employees = Employee::join('cities', 'employees.city_id', '=', 'cities.id')
            ->join('employee_genders as eg','eg.id','=','employees.employee_gender_id')
            ->leftjoin('admin_departments as ads','ads.id','=','employees.department_id')
            ->leftjoin('admins as staff','staff.trax_id','=','employees.trax_id')
            ->leftjoin('riders as r','r.trax_id','=','employees.trax_id')
            ->leftjoin('rider_requests as rr','rr.id','=','employees.rider_request_id')
            ->leftjoin('rider_types as rr_rt','rr_rt.id','=','rr.rider_type_id')
            ->leftjoin('rider_types as r_rt','r_rt.id','=','r.rider_type_id')
            ->join('employee_types as et','et.id','=','employees.employee_type_id')
            ->join('employee_request_statuses as ers','ers.id','=','employees.request_status_id')
            ->join('employee_statuses as es','es.id','=','employees.status_id')
            ->select(['r.name as check_if_rider_present_bit','r.rider_category_id as category_id','r.route_id as route_id','r.operation_rider_id as operation_id','r.blacklist as blacklist_rider','rr_rt.id as inactive_rider_type_id','rr_rt.name as inactive_rider_type','r_rt.id as active_rider_type_id','r_rt.name as active_rider_type','employees.id as employee_id', 'employees.name as employee_name','employees.city_id as city_id', 'cities.name as city' ,'employees.trax_id' ,'employees.request_status_id','employees.status_id as status_id' ,'employees.employee_type_id', 'eg.name as gender', 'employees.cnic', 'employees.phone_number', 'et.name as employee_type','employees.status_id','ers.name as request_status', 'es.name as status', 'employees.created_at as requested_at','employees.pin as pin','employees.address as address','employees.shift_id as shift_id','employees.guardian_name as father_name','ads.name as department_name','employees.first_inactive', 'employees.rider_sub_category as rider_sub_category', 'employees.rider_main_category as rider_main_category'])
            ->where(function ($q){
                $q ->where('r.blacklist','=',0)
                    ->orWhere('r.blacklist','=',null);
            });

        if (session('role_id') != 1) {
            $employees = $employees->whereIn('cities.hub_id', session('hubs'));
        }

        return Datatables::of($employees)
            ->addColumn('id_padded', function ($user) {
                return str_pad($user->employee_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('users.id', function ($query, $keyword) {
                return $query->where('users.id', '=', $keyword);
            })
            ->filterColumn('et.name', function ($query, $keyword) {
                if($keyword == 1)
                {
                    return $query->where('employees.employee_type_id','=',1);
                }
                elseif ($keyword == 2)
                {
                    return $query->where('et.name','=','Rider')
                        ->where(function ($q){
                           $q->where([['employees.status_id','!=',2],['r_rt.id',1]])
                            ->orwhere([['employees.status_id','=',2],['rr_rt.id',1]]);
                        });

                }
                elseif ($keyword == 3)
                {
                    return $query->where('et.name','=','Rider')
                        ->where(function ($q){
                            $q->where([['employees.status_id','!=',2],['r_rt.id',2]])
                                ->orwhere([['employees.status_id','=',2],['rr_rt.id',2]]);
                        });
                }

                return null;
            })
            ->addColumn('employee_hub',function($user){
                return City::where('id',$user->city_id)->first()->hub_city->name ?? "";
            })
            ->editColumn('employee_type',function ($user){
                if($user->employee_type_id == 1)
                {
                    return $user->employee_type;
                }
                else{

                    $type = $user->employee_type;
                    if ($user->status_id != 2) {
                        if(isset($user->active_rider_type))
                        {
                            $type .=' - ' . $user->active_rider_type;
                        }
                        return $type;
                    } else {
                        if(isset($user->inactive_rider_type))
                        {
                            $type .=' - ' . $user->inactive_rider_type;
                        }
                        return $type;
                    }

                }
            })
            ->editColumn('employee_name',function ($user){
                return $user->employee_name;
            })
            ->filterColumn('ads.name',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ads.name',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($result) {
                if(session('role_id') == 1 || in_array(468, session('permissions')) || in_array(469, session('permissions'))  || in_array(98, session('permissions')) || in_array(381, session('permissions')) || in_array(620, session('permissions'))){
                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                    if($result->request_status_id == 1 || $result->request_status_id == 2){
                        if (session('role_id') == 1 || in_array(469, session('permissions'))) {

                            $dropdown .= '<button type="button" class="dropdown-item approve" data-target-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Approve</div></button>';

                            $dropdown .= '<button type="button" class="dropdown-item reject" data-target-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Reject</div></button>';

                        }
                    }
                    if($result->request_status_id == 3 && $result->employee_type_id == 1)
                    {
                        if ($result->status_id != 2 && (session('role_id') == 1 || in_array(591, session('permissions')))) {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate_staff" data-target-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate Staff</div></button>';
                        }

                        if ($result->status_id == 2) {
                            if(session('role_id') == 1 || in_array(591, session('permissions'))) {
                                $dropdown .= '<button type="button" class="dropdown-item activate_staff" data-target-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Staff</div></button>';
                            }

                            if(session('role_id') == 1 || in_array(620, session('permissions'))) {
                                if($result->first_inactive == 1) {
                                    $dropdown .= '<button type="button" class="dropdown-item rejoin" data-target-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Rejoin Staff</div></button>';
                                }
                            }
                        }
                    }

                    if($result->request_status_id == 3 && $result->employee_type_id == 2)
                    {
                        if (session('role_id') == 1 || in_array(98, session('permissions'))) {
                            $dropdown .= '<button type="button" class="dropdown-item update_rider" data-target-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Update Rider</div></button>';
                        }

                        if($result->status_id != 2)
                        {
                            if (session('role_id') == 1 || in_array(381, session('permissions'))) {
                                if($result->active_rider_type_id == 1)
                                {
                                    $dropdown .= '<button type="button" class="dropdown-item incentive" data-target-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Mark Rider Incentive</div></button>';
                                }
                                else{
                                    $dropdown .= '<button type="button" class="dropdown-item permanent" data-target-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Mark Rider Permanent</div></button>';
                                }

                            }

//                            if (session('role_id') == 1 || in_array(382, session('permissions'))) {
//                                $dropdown .= '<button type="button" class="dropdown-item blacklist" data-target-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Blacklist</div></button>';
//                            }

                            if (session('role_id') == 1 || in_array(99, session('permissions'))) {
                                $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate Rider</div></button>';
                            }


                        }

                        if($result->status_id == 2 && $result->check_if_rider_present_bit != null)
                        {
                            if (session('role_id') == 1 || in_array(99, session('permissions'))) {
                                $dropdown .= '<button type="button" class="dropdown-item activate" data-target-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Rider</div></button>';
                            }

                            if(session('role_id') == 1 || in_array(620, session('permissions'))) {
                                if($result->first_inactive == 1) {
                                    $dropdown .= '<button type="button" class="dropdown-item rejoin" data-target-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Rejoin Rider</div></button>';
                                }
                            }
                        }
                    }

                    if (session('role_id') == 1 || in_array(468, session('permissions'))) {
                        $route = route("admin.human_resource.employee_directory.edit", $result->employee_id);
                        $dropdown .= '<button class="dropdown-item update_pin_btn"  data-toggle="modal" data-target="#UpdatePinModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Update Bolt & Sonic Pin</div></div></button><a href="' . $route . '"><button class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Update Details</div></div></button></a>';
                    }

                    $dropdown .= '
                </div>
              </div>
            ';
                    return $dropdown;
                }
                else{
                    return '';
                }
            })
            ->make(true);
    }

    public function employee_directory_pin(Request $request)
    {
        $validations = [
            'pin' => 'required|integer|digits:4'
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        $employee = Employee::find($request->employee_id);
        if($employee)
        {
            $employee->pin = $request->pin;
            $employee->update();

            if($employee->employee_type_id == 1)
            {
                $admin = Admin::where('trax_id',$employee->trax_id)->where('trax_id','!=',null);
                if($admin->exists()) {
                    $admin = $admin->first();
                    $admin->password = bcrypt($employee->pin);
                    $admin->dummy_pin = $employee->pin;
                    $admin->update();
                }
            }
            else {
                $rider = Rider::where('trax_id',$employee->trax_id)->where('trax_id','!=',null);
                if($rider->exists()) {
                    $rider = $rider->first();
                    $rider->pin = bcrypt($employee->pin);
                    $rider->dummy_pin = $employee->pin;
                    $rider->update();
                }
            }

            return back()->with(['success'=>'Employee Pin Updated Successfully']);
        }
        return back()->with(['error'=>'Employee Not Found']);
    }

    public function rejoin_employee(Request $request)
    {
        $employee_id = $request->employee_id;
        if(!$employee_id){
            return response()->json(['status' => 1, 'error' => 'Employee not found!']);
        }
        $employee = Employee::find($employee_id);
        if(!$employee)
        {
            return response()->json(['status' => 1, 'error' => 'Employee not found!']);
        }

        if($employee->employee_type_id == 1)
        {
            $staff = Admin::where('trax_id',$employee->trax_id)->where('trax_id','!=',null);
        }
        else{
            $staff = Rider::where('trax_id',$employee->trax_id)->where('trax_id','!=',null);
        }
        if($staff->doesntExist()){
            return response()->json(['status' => 1, 'error' => 'Employee not found!']);
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

        $staff->status = 1;
        $staff->updated_by = Auth::id();
        $staff->trax_id = $trax_id;
        $staff->save();

        $employee->status_id = self::GetStatusOfEmployee($employee->id);
        $employee->trax_id = $trax_id;
        $employee->save();
        return response()->json(['status' => 0, 'success' => 'Employee Rejoined Successfully!']);
    }

    public function employee_directory_make_rider_incentive(Request $request)
    {
        $employee_id = $request->employee_id;
        if($employee_id){
            $employee = Employee::find($employee_id);
            if($employee){
                $rider = Rider::where('trax_id',$employee->trax_id)->where('trax_id','!=',null);
                if($rider->exists())
                {
                    $rider = $rider->first();
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
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }
    }
    public function employee_directory_make_rider_permanent(Request $request)
    {
        $employee_id = $request->employee_id;
        if($employee_id){
            $employee = Employee::find($employee_id);
            if($employee){
                $rider = Rider::where('trax_id',$employee->trax_id)->where('trax_id','!=',null);
                if($rider->exists())
                {
                    $rider = $rider->first();
                    $rider_status = $rider->rider_type_id;
                    if($rider_status == 2){
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

                        $rider->trax_id = $trax_id;
                        $rider->rider_type_id = 1;
                        $rider->updated_by = Auth::id();
                        $rider->save();

                        $employee->trax_id = $trax_id;
                        $employee->update();
                        return response()->json(['status' => 0, 'success' => 'Rider Marked as Permanent Rider!']);
                    }

                    return response()->json(['status' => 1, 'error' => 'Rider already Marked as Permanent Rider!']);
                }
                return response()->json(['status' => 1, 'error' => 'Rider not found!']);
            }
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }
    }
    public function employee_directory_make_rider_blacklist(Request $request)
    {
        $employee_id = $request->employee_id;
        if(!$employee_id){
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }
        $employee = Employee::find($employee_id);
        if(!$employee)
        {
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }
        $rider = Rider::where('trax_id',$employee->trax_id)->where('trax_id','!=',null);
        if($rider->doesntExist()){
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }

        $rider = $rider->first();

        $rider->blacklist = 1;
        $rider->status = 0;
        $rider->updated_by = Auth::id();
        $rider->save();
        return response()->json(['status' => 0, 'success' => 'Rider is blacklisted!']);

    }
    public function employee_directory_make_rider_activate(Request $request)
    {
        $employee_id = $request->employee_id;
        if(!$employee_id){
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }
        $employee = Employee::find($employee_id);
        if(!$employee)
        {
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }
        $rider = Rider::where('trax_id',$employee->trax_id)->where('trax_id','!=',null);
        if($rider->doesntExist()){
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }
        $rider = $rider->first();

        $rider->status = 1;
        $rider->updated_by = Auth::id();
        $rider->save();

        $employee->status_id = self::GetStatusOfEmployee($employee->id);
        $employee->first_inactive = 1;
        $employee->save();
        return response()->json(['status' => 0, 'success' => 'Rider is Activated!']);

    }
    public function employee_directory_make_rider_deactivate(Request $request)
    {
        $employee_id = $request->employee_id;
        if(!$employee_id){
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }
        $employee = Employee::find($employee_id);
        if(!$employee)
        {
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }
        $rider = Rider::where('trax_id',$employee->trax_id)->where('trax_id','!=',null);
        if($rider->doesntExist()){
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }
        $rider = $rider->first();

        $rider->status = 0;
        $rider->route_id = null;
        $rider->updated_by = Auth::id();
        $rider->save();

        $employee->status_id = 2;
        $employee->save();
        return response()->json(['status' => 0, 'success' => 'Rider is Inactive!']);
    }

    public function employee_directory_make_staff_activate(Request $request)
    {
        $employee_id = $request->employee_id;
        if(!$employee_id){
            return response()->json(['status' => 1, 'error' => 'Staff not found!']);
        }
        $employee = Employee::find($employee_id);
        if(!$employee)
        {
            return response()->json(['status' => 1, 'error' => 'Staff not found!']);
        }
        $staff = Admin::where('trax_id',$employee->trax_id)->where('trax_id','!=',null);
        if($staff->doesntExist()){
            return response()->json(['status' => 1, 'error' => 'Staff not found!']);
        }
        $staff = $staff->first();

        $staff->status = 1;
        $staff->updated_by = Auth::id();
        $staff->save();

        $employee->status_id = self::GetStatusOfEmployee($employee->id);
        $employee->first_inactive = 1;
        $employee->save();
        return response()->json(['status' => 0, 'success' => 'Staff is Activated!']);

    }
    public function employee_directory_make_staff_deactivate(Request $request)
    {
        $employee_id = $request->employee_id;
        if(!$employee_id){
            return response()->json(['status' => 1, 'error' => 'Staff not found!']);
        }
        $employee = Employee::find($employee_id);
        if(!$employee)
        {
            return response()->json(['status' => 1, 'error' => 'Staff not found!']);
        }
        $staff = Admin::where('trax_id',$employee->trax_id)->where('trax_id','!=',null);
        if($staff->doesntExist()){
            return response()->json(['status' => 1, 'error' => 'Staff not found!']);
        }
        $staff = $staff->first();

        $staff->status = 0;
        $staff->updated_by = Auth::id();
        $staff->save();

        $employee->status_id = 2;
        $employee->save();
        return response()->json(['status' => 0, 'success' => 'Staff is Inactive!']);
    }

    public function employee_directory_make_rider_update(Request $request)
    {
        $validations = [
            'city_id'=>'required|numeric',
            'rider_name'=>'required|max:255',
            'phone'=>'required|max:255',
            'cnic'=>'required|max:255',
            'address'=>'required|max:255',
            'route_id'=>'required',
            'rider_category'=>'required|numeric',
            'rider_main_category'=>'required|numeric',
            'pin' => 'required|integer|digits:4',
            'rider_type' => "required|numeric",
            'category' => "required|numeric"
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        $employee = Employee::where('id',$request->employee_id);
        if($employee->doesntExist())
        {
            return redirect()->back()->with('error','Rider Not Found');
        }

        $employee = $employee->first();
        $trax_id = $employee->trax_id;
        $rider = Rider::where('trax_id',$trax_id)->where('trax_id','!=',null);
        if($rider->doesntExist()) {
            $rider = new Rider();
            $rider->city_id = $request->city_id;
            $rider->name = $request->rider_name;
            $rider->phone = $request->phone;
            $rider->cnic = $request->cnic;
            $rider->address = $request->address;
            $rider->status =1;
            $rider->pin = bcrypt($request->pin);
            $rider->dummy_pin = $request->pin;
            $rider->created_by = Auth::id();
            $rider->trax_id = $trax_id;
            $rider->shift_id = $request->shift_id;
            $rider->rider_type_id  = $request->rider_type;
            $rider->save();

            $employee->status_id = self::GetStatusOfEmployee($employee->id);
            $employee->save();

        }
        else{
            $rider = $rider->first();
        }
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
            Rider::where('route_id', $request->route_id)->where('id', '<>', $rider->id)->update(['route_id' => NULL]);
            $route_id = $request->route_id;
        }

        $rider->route_id = $route_id;
        $rider->operation_rider_id = $request->category;
        $rider->rider_category_id = $request->rider_category;
        $rider->rider_main_category_id = $request->rider_main_category;
        $rider->update();

        if($rider){
            NotificationsController::send(61, $rider->id, $request->pin);
            $rider_request = RiderRequest::find($employee->rider_request_id);
            if($rider_request){
                $rider_request->status = 1;
                $rider_request->save();
            }
            $employee->rider_sub_category = $request->rider_category;
            $employee->rider_main_category = $request->rider_main_category;
            $employee->save();
            return redirect()->back()->with('success','Rider Updated successfully');
        }
    }

    public static function GetStatusOfEmployee($employee_id)
    {
        $employee_fields = ['guardian_name','phone_number','address','city_id','department_id','zone_id','pin','official_phone_number'];
        $employee_attachments_fields = ['cheque','photo'];
        $employee = Employee::find($employee_id);
        foreach ($employee_fields as $field)
        {
            if($employee[$field] == null)
            {
                return 3;
            }
        }

        $bankInfo = EmployeeBankInformation::where('employee_id',$employee_id);
        if($bankInfo->doesntExist())
        {
            return 3;
        }

        $attachments = EmployeeAttachment::where('employee_id',$employee_id);
        if($attachments->doesntExist())
        {
            return 3;
        }

        $attachments = $attachments->first();
        foreach ($employee_attachments_fields as $employee_attachments_field) {
            if($attachments[$employee_attachments_field] == null)
            {
                return 3;
            }
        }

        return 1;
    }

    public function employee_directory_approve(Request $request){
        if(is_array($request->employee_ids)){
            foreach ($request->employee_ids as $employee_id) {
                $employee = Employee::find($employee_id);
                if(in_array($employee->request_status_id, [1, 2])) {
                    if($employee->trax_id == null) {
                        if($employee->employee_type_id == 1){
                            if($employee->staff_category_id == 1){
                                $global_setting = GlobalSettings::where('type', 'latest_employee_id');
                                $trax_id_prefix = 'Trax';
                            }elseif ($employee->staff_category_id == 2){
                                $global_setting = GlobalSettings::where('type', 'latest_intern_id');
                                $trax_id_prefix = 'TraxI';
                            }else{
                                return response()->json(['status' => 1, 'error' => 'Invalid Staff Category']);
                            }
                        }else{
                            $global_setting = GlobalSettings::where('type', 'latest_employee_id');
                            $trax_id_prefix = 'Trax';
                        }
                        if ($global_setting->exists()) {
                            $global_setting = $global_setting->first();
                            $trax_id = $global_setting->setting_value + 1;
                            $global_setting->setting_value = $trax_id;
                            $global_setting->save();
                            $trax_id = $trax_id_prefix . str_pad($trax_id, 5, '0', STR_PAD_LEFT);
                        } else {
                            $trax_id = null;
                        }

                        $employee->trax_id = $trax_id;

                    }
                    $employee->request_status_id = 3;
                    $employee->save();

                    if($employee->employee_type_id == 1)
                    {

                        $admin = Admin::where('trax_id',$employee->trax_id)->where('trax_id','!=',null);

                        if($admin->doesntExist())
                        {
                            $admin = new Admin();

                            $admin->name = $employee->name;
                            $admin->email = $employee->official_email;
                            $admin->phone_number = $employee->phone_number;
                            $admin->official_phone_number = $employee->official_phone_number;
                            $admin->cnic = $employee->cnic;
                            $admin->role_id = $employee->designation->role_id ?? 79;
                            $admin->default_hub_id = $employee->city->hub_city->id;
                            $admin->password = bcrypt($employee->pin);
                            $admin->dummy_pin = $employee->pin;
                            $admin->designation_id = $employee->designation_id;
                            $admin->shift_id = $employee->shift_id;


                            if($employee->status_id == 2)
                            {
                                $admin->status = 0;
                            }
                            else{
                                $admin->status = 1;
                            }

                            $admin->updated_by = Auth::id();
                            $admin->trax_id = $employee->trax_id;
                            $admin->employee_id = $employee->id;

                            $admin->save();

                            foreach ($employee->designation->hubs as $hub)
                            {
                                $admin_hub = new AdminHub();
                                $admin_hub->admin_id = $admin->id;
                                $admin_hub->hub_id = $hub->hub_id;
                                $admin_hub->save();
                            }
                        }

                    }
                }
            }

            return response()->json(['status' => 0, 'success' => 'Employee(s) Approved Successfully!']);
        }
        else{
            return response()->json(['status' => 1, 'success' => 'Invalid Selection!']);
        }
    }
    public function employee_directory_reject(Request $request){
        if(is_array($request->employee_ids)) {
            foreach ($request->employee_ids as $employee_id) {
                $employee = Employee::find($employee_id);
                if(in_array($employee->request_status_id, [1, 2])){
                    $employee->request_status_id = 4;
                    $employee->save();

//                    if ($employee->employee_type_id == 2) {
//                        RiderRequest::where('id', $employee->rider_request_id)->update(['status'=>2]);
//                    }
                }
            }
            return response()->json(['status' => 0, 'success' => 'Employee(s) Rejected Successfully!']);
        }
        else{
            return response()->json(['status' => 1, 'success' => 'Invalid Selection!']);
        }

    }

    public function employee_directory_edit(Employee $employee)
    {
        $religions = EmployeeReligion::all();
        $nationalities = EmployeeNationality::all();
        $domiciles = EmployeeDomicile::all();
        $maritial_statuses = EmployeeMaritalStatus::all();
        $blood_groups = EmployeeBloodGroup::all();
        $designations = EmployeeDesignation::where('status', 1)->get();
        $cities = City::where('status',1)->where('business_category_id',1)->get();
        $zones = Zone::where('status', 1)->get();
        $departments = AdminDepartment::all();
        $relationships = EmployeeRelationship::all();
        $banks = BanksList::where('status',1)->get();
        $medical_infos = $employee->medical_infos;
        $bank_info = $employee->bank_info;
        $reference = $employee->reference;
        $educations = $employee->education_infos;
        $employments = $employee->employment_history;
        $attachments = $employee->attachments;
        $place_of_birth_cities = City::where('business_category_id',1)->get();
        $shifts = EmployeeShift::where('status', 1)->get();
        $staff_categories = StaffCategory::all();
        $genders = EmployeeGender::all();
        $rider_types = RiderType::all();
        $main_categories = RiderMainCategory::all();
        $sub_categories = RiderCategory::all();
        $rider_request = $employee->rider_request;
        return view('admin.human_resource.employee_directory.update',compact('employments','blood_groups','attachments','educations','reference','bank_info','banks','medical_infos','employee','religions','nationalities','domiciles','maritial_statuses','designations','departments','zones','relationships', 'place_of_birth_cities','cities', 'shifts', 'staff_categories', 'genders', 'rider_types', 'main_categories', 'sub_categories', 'rider_request'));
    }

    public function employee_directory_profile_update (Employee $employee, Request $request)
    {

//        return $request;
        $request->validate([
            'personal_number'=> [Rule::unique('employees', 'phone_number')->ignore($employee->id),Rule::unique('employees', 'official_phone_number')->ignore($employee->id)],
            'official_number'=> 'bail|nullable|'.Rule::unique('employees', 'phone_number')->ignore($employee->id).'|'.Rule::unique('employees', 'official_phone_number')->ignore($employee->id).'',
            'cnic'=> [Rule::unique('employees', 'cnic')->ignore($employee->id)],
        ]);

        $employee->request_status_id = 2;
        $employee->name = $request->employee_name;
        $employee->phone_number = $request->personal_number;
        $employee->guardian_name = $request->name;
        $employee->mother_name = $request->mother_name;
        $employee->religion_id = $request->religion;
        $employee->nationality_id = $request->nationality;
        $employee->domicile_id = $request->domicile;
        $employee->marital_status_id = $request->marital_status;
        $employee->blood_group = $request->blood_group;
        $employee->personal_email = $request->personal_email;
        $employee->address = $request->address;
        $employee->emergency_contact = $request->emergency_contact;
        $employee->cnic = $request->cnic;
        $employee->cnic_issue_date = $request->cnic_issue_date_formatted;
        $employee->cnic_expiry_date = $request->cnic_expiry_date_formatted;
        $employee->designation_id = $request->designation;
        $employee->city_id = $request->city;
        $employee->department_id = $request->department;
        $employee->zone_id = $request->zone;
        $employee->official_email = $request->official_email;
        $employee->official_phone_number = $request->official_number;
//        $employee->sonic_id = $request->sonic_id;
        $employee->pin = $request->bolt_pin;
        $employee->place_of_birth = $request->place_of_birth;
        $employee->date_of_birth = $request->date_of_birth_formatted;
        $employee->status_id = ($employee->status_id == 2) ? 2 : self::GetStatusOfEmployee($employee->id);
        $employee->shift_id = $request->shift_id;
        $employee->staff_category_id = $request->staff_category;
        $employee->rider_sub_category = $request->rider_sub_category;
        $employee->rider_main_category = $request->rider_main_category;
        $employee->update();

        $rider_request = RiderRequest::find($employee->rider_request_id);
        if($rider_request){
            $rider_request->rider_type_id = $request->rider_type;
            $rider_request->save();
        }

        if($employee->employee_type_id == 1)
        {
            $admin = Admin::where('trax_id',$employee->trax_id)->where('trax_id','!=',null);
            if($admin->exists())
            {
                $admin = $admin->first();

                if($admin->designation_id != $employee->designation_id)
                {
                    AdminHub::where('admin_id',$admin->id)->delete();

                    $hubs = EmployeeDesignationHub::where('designation_id',$employee->designation_id)->get(['hub_id']);
                    foreach ($hubs as $hub)
                    {
                        $admin_hub = new AdminHub();
                        $admin_hub->admin_id = $admin->id;
                        $admin_hub->hub_id = $hub->hub_id;
                        $admin_hub->save();
                    }
                }

                $admin->designation_id = $employee->designation_id;
                $admin->role_id = $employee->designation->role_id ?? 79;
                $admin->phone_number = $employee->phone_number;
                $admin->official_phone_number = $employee->official_phone_number;
                $admin->email = $employee->official_email;
                $admin->cnic = $employee->cnic;
                $admin->name = $employee->name;
                $admin->default_hub_id = $employee->city->hub_city->id;
                $admin->password = bcrypt($employee->pin);
                $admin->dummy_pin = $employee->pin;
                $admin->shift_id = $employee->shift_id;
                $admin->update();

            }
        }
        else{
            $rider = Rider::where('trax_id',$employee->trax_id)->where('trax_id','!=',null);
            if($rider->exists())
            {
                $rider = $rider->first();
                $rider->city_id = $employee->city_id;
                $rider->name = $employee->name;
                $rider->phone = $employee->official_phone_number;
                $rider->cnic = $employee->cnic;
                $rider->address = $employee->address;
                $rider->dummy_pin = $employee->pin;
                $rider->shift_id = $employee->shift_id;
                $rider->pin = bcrypt($employee->pin);
                $rider->rider_type_id = $request->rider_type;
                $rider->rider_main_category_id = $request->rider_main_category;
                $rider->rider_category_id = $request->rider_sub_category;
                $rider->save();
            }
        }

        return back()->with(['success'=>'Employee Profile Updated Successfully']);
    }

    public function employee_directory_medical_update(Employee $employee, Request $request)
    {
        if($employee->medical_infos->count() > 0)
        {
            $employee->medical_infos()->delete();
        }
        foreach ($request->name as $key => $value)
        {
            $medical_info = new EmployeeMedicalInformation();
            $medical_info->employee_id = $employee->id;
            $medical_info->name = $request->name[$key];
            $medical_info->relationship_id = $request->relationship[$key];
            $medical_info->date_of_birth = $request->formatted_dob[$key];
            $medical_info->marital_status = $request->marital_status[$key];
            $medical_info->save();

            echo $request->formatted_dob[$key];
        }
        if(count($request->name) > 0)
        {
            $employee->request_status_id = 2;
            $employee->status_id =  ($employee->status_id == 2) ? 2 : self::GetStatusOfEmployee($employee->id);
            $employee->update();
        }

        return back()->with(['success'=>'Employee Medical Information Updated Successfully']);
    }

    public function employee_directory_education_update(Employee $employee, Request $request)
    {
        if($employee->education_infos->count() > 0)
        {
            $employee->education_infos()->delete();
        }
        foreach ($request->name as $key => $value)
        {
            $education = new EmployeeEducationalBackground();
            $education->employee_id = $employee->id;
            $education->name = $request->name[$key];
            $education->degree = $request->degree[$key];
            $education->grade = $request->grade[$key];
            $education->passing_year = $request->formatted_passing_year[$key];
            $education->save();
        }

        if(count($request->name) > 0)
        {
            $employee->request_status_id = 2;
            $employee->status_id =  ($employee->status_id == 2) ? 2 : self::GetStatusOfEmployee($employee->id);
            $employee->update();
        }
        return back()->with(['success'=>'Employee Educational Information Updated Successfully']);
    }

    public function employee_directory_employment_update(Employee $employee, Request $request)
    {
        if($employee->employment_history->count() > 0)
        {
            $employee->employment_history()->delete();
        }
        foreach ($request->name as $key => $value)
        {
            $employment = new EmployeeEmployementHistory();
            $employment->employee_id = $employee->id;
            $employment->name = $request->name[$key];
            $employment->designation = $request->position[$key];
            $employment->from = $request->formatted_from[$key];
            $employment->to = $request->formatted_to[$key];
            $employment->reason = $request->reason[$key];
            $employment->save();
        }

        if(count($request->name) > 0)
        {
            $employee->request_status_id = 2;
            $employee->status_id =  ($employee->status_id == 2) ? 2 : self::GetStatusOfEmployee($employee->id);
            $employee->update();
        }
        return back()->with(['success'=>'Employee Employment History Updated Successfully']);
    }

    public function employee_directory_bank_update(Employee $employee, Request $request)
    {
        if($employee->bank_info()->exists())
        {
            $bank_info = $employee->bank_info->first();
        }
        else{
            $bank_info = new EmployeeBankInformation();
        }

        $bank_info->employee_id = $employee->id;
        $bank_info->account_title = $request->account_title;
        $bank_info->branch_code = $request->branch_code;
        $bank_info->account_no = $request->account_number;
        $bank_info->bank_id = $request->bank_name;
        $bank_info->branch_name = $request->branch_name;
        $bank_info->iban = $request->iban_number;
        $bank_info->save();

        $employee->request_status_id = 2;
        $employee->status_id =  ($employee->status_id == 2) ? 2 : self::GetStatusOfEmployee($employee->id);
        $employee->update();


        return back()->with(['success'=>'Employee Bank Information Updated Successfully']);
    }

    public function employee_directory_reference_update(Employee $employee, Request $request)
    {
        if($employee->reference()->exists())
        {
            $reference = $employee->reference->first();
        }
        else{
            $reference = new EmployeeReference();
        }

        $reference->employee_id = $employee->id;
        $reference->name = $request->name;
        $reference->occupation = $request->occupation;
        $reference->relationship = $request->relationship;
        $reference->years = $request->years;
        $reference->phone_number = $request->phone;
        $reference->email = $request->email;
        $reference->save();

        $employee->request_status_id = 2;
        $employee->status_id =  ($employee->status_id == 2) ? 2 : self::GetStatusOfEmployee($employee->id);
        $employee->update();

        return back()->with(['success'=>'Employee Reference Updated Successfully']);
    }

    public function employee_directory_attachments_update(Employee $employee, Request $request)
    {
        $request->validate([
            'cv_1'=>'mimes:pdf,png,jpeg,jpg',
            'cv_2'=>'mimes:pdf,png,jpeg,jpg',
            'cv_3'=>'mimes:pdf,png,jpeg,jpg',
            'cv_4'=>'mimes:pdf,png,jpeg,jpg',
            'academic_1'=>'mimes:pdf,png,jpeg,jpg',
            'academic_2'=>'mimes:pdf,png,jpeg,jpg',
            'academic_3'=>'mimes:pdf,png,jpeg,jpg',
            'academic_4'=>'mimes:pdf,png,jpeg,jpg',
            'cnic_1'=>'mimes:pdf,png,jpeg,jpg',
            'cnic_1'=>'mimes:pdf,png,jpeg,jpg',
            'cnic_2'=>'mimes:pdf,png,jpeg,jpg',
            'cnic_3'=>'mimes:pdf,png,jpeg,jpg',
            'cnic_4'=>'mimes:pdf,png,jpeg,jpg',
            'photo_1'=>'mimes:pdf,png,jpeg,jpg',
            'photo_2'=>'mimes:pdf,png,jpeg,jpg',
            'photo_3'=>'mimes:pdf,png,jpeg,jpg',
            'photo_4'=>'mimes:pdf,png,jpeg,jpg',
            'experience_certificate_1'=>'mimes:pdf,png,jpeg,jpg',
            'experience_certificate_2'=>'mimes:pdf,png,jpeg,jpg',
            'experience_certificate_3'=>'mimes:pdf,png,jpeg,jpg',
            'experience_certificate_4'=>'mimes:pdf,png,jpeg,jpg',
            'last_pay_slip_1'=>'mimes:pdf,png,jpeg,jpg',
            'last_pay_slip_2'=>'mimes:pdf,png,jpeg,jpg',
            'last_pay_slip_3'=>'mimes:pdf,png,jpeg,jpg',
            'last_pay_slip_4'=>'mimes:pdf,png,jpeg,jpg',
            'nikkah_nama_1'=>'mimes:pdf,png,jpeg,jpg',
            'nikkah_nama_2'=>'mimes:pdf,png,jpeg,jpg',
            'nikkah_nama_3'=>'mimes:pdf,png,jpeg,jpg',
            'nikkah_nama_4'=>'mimes:pdf,png,jpeg,jpg',
            'cnic_spouse_1'=>'mimes:pdf,png,jpeg,jpg',
            'cnic_spouse_2'=>'mimes:pdf,png,jpeg,jpg',
            'cnic_spouse_3'=>'mimes:pdf,png,jpeg,jpg',
            'cnic_spouse_4'=>'mimes:pdf,png,jpeg,jpg',
            'child_b_form_1'=>'mimes:pdf,png,jpeg,jpg',
            'child_b_form_2'=>'mimes:pdf,png,jpeg,jpg',
            'child_b_form_3'=>'mimes:pdf,png,jpeg,jpg',
            'child_b_form_4'=>'mimes:pdf,png,jpeg,jpg',
            'cnic_nominee_1'=>'mimes:pdf,png,jpeg,jpg',
            'cnic_nominee_2'=>'mimes:pdf,png,jpeg,jpg',
            'cnic_nominee_3'=>'mimes:pdf,png,jpeg,jpg',
            'cnic_nominee_4'=>'mimes:pdf,png,jpeg,jpg',
            'utility_bill_1'=>'mimes:pdf,png,jpeg,jpg',
            'utility_bill_2'=>'mimes:pdf,png,jpeg,jpg',
            'utility_bill_3'=>'mimes:pdf,png,jpeg,jpg',
            'utility_bill_4'=>'mimes:pdf,png,jpeg,jpg',
            'affidavit_1'=>'mimes:pdf,png,jpeg,jpg',
            'affidavit_2'=>'mimes:pdf,png,jpeg,jpg',
            'affidavit_3'=>'mimes:pdf,png,jpeg,jpg',
            'affidavit_4'=>'mimes:pdf,png,jpeg,jpg',
            'cheque_1'=>'mimes:pdf,png,jpeg,jpg',
            'cheque_2'=>'mimes:pdf,png,jpeg,jpg',
            'cheque_3'=>'mimes:pdf,png,jpeg,jpg',
            'cheque_4'=>'mimes:pdf,png,jpeg,jpg',
        ],
            [
                'cv_1.mimes' => 'CV must be a file of type: pdf,png,jpeg,jpg',
                'cv_2.mimes' => 'CV must be a file of type: pdf,png,jpeg,jpg',
                'cv_3.mimes' => 'CV must be a file of type: pdf,png,jpeg,jpg',
                'cv_4.mimes' => 'CV must be a file of type: pdf,png,jpeg,jpg',
                'academic_1.mimes' => 'Academic Certificates must be a file of type: pdf,png,jpeg,jpg',
                'academic_2.mimes' => 'Academic Certificates must be a file of type: pdf,png,jpeg,jpg',
                'academic_3.mimes' => 'Academic Certificates must be a file of type: pdf,png,jpeg,jpg',
                'academic_4.mimes' => 'Academic Certificates must be a file of type: pdf,png,jpeg,jpg',
                'cnic_1.mimes' => 'CNIC must be a file of type: pdf,png,jpeg,jpg',
                'cnic_2.mimes' => 'CNIC must be a file of type: pdf,png,jpeg,jpg',
                'cnic_3.mimes' => 'CNIC must be a file of type: pdf,png,jpeg,jpg',
                'cnic_4.mimes' => 'CNIC must be a file of type: pdf,png,jpeg,jpg',
                'photo_1.mimes' => 'Passport Size Photo must be a file of type: pdf,png,jpeg,jpg',
                'photo_2.mimes' => 'Passport Size Photo must be a file of type: pdf,png,jpeg,jpg',
                'photo_3.mimes' => 'Passport Size Photo must be a file of type: pdf,png,jpeg,jpg',
                'photo_4.mimes' => 'Passport Size Photo must be a file of type: pdf,png,jpeg,jpg',
                'experience_certificate_1.mimes' => 'Experience Certificate must be a file of type: pdf,png,jpeg,jpg',
                'experience_certificate_2.mimes' => 'Experience Certificate must be a file of type: pdf,png,jpeg,jpg',
                'experience_certificate_3.mimes' => 'Experience Certificate must be a file of type: pdf,png,jpeg,jpg',
                'experience_certificate_4.mimes' => 'Experience Certificate must be a file of type: pdf,png,jpeg,jpg',
                'last_pay_slip_1.mimes' => 'Last Pay Slip must be a file of type: pdf,png,jpeg,jpg',
                'last_pay_slip_2.mimes' => 'Last Pay Slip must be a file of type: pdf,png,jpeg,jpg',
                'last_pay_slip_3.mimes' => 'Last Pay Slip must be a file of type: pdf,png,jpeg,jpg',
                'last_pay_slip_4.mimes' => 'Last Pay Slip must be a file of type: pdf,png,jpeg,jpg',
                'nikkah_nama_1.mimes' => 'Nikkah Nama must be a file of type: pdf,png,jpeg,jpg',
                'nikkah_nama_2.mimes' => 'Nikkah Nama must be a file of type: pdf,png,jpeg,jpg',
                'nikkah_nama_3.mimes' => 'Nikkah Nama must be a file of type: pdf,png,jpeg,jpg',
                'nikkah_nama_4.mimes' => 'Nikkah Nama must be a file of type: pdf,png,jpeg,jpg',
                'cnic_spouse_1.mimes' => 'CNIC Spouse must be a file of type: pdf,png,jpeg,jpg',
                'cnic_spouse_2.mimes' => 'CNIC Spouse must be a file of type: pdf,png,jpeg,jpg',
                'cnic_spouse_3.mimes' => 'CNIC Spouse must be a file of type: pdf,png,jpeg,jpg',
                'cnic_spouse_4.mimes' => 'CNIC Spouse must be a file of type: pdf,png,jpeg,jpg',
                'child_b_form_1.mimes' => 'Child B Form must be a file of type: pdf,png,jpeg,jpg',
                'child_b_form_2.mimes' => 'Child B Form must be a file of type: pdf,png,jpeg,jpg',
                'child_b_form_3.mimes' => 'Child B Form must be a file of type: pdf,png,jpeg,jpg',
                'child_b_form_4.mimes' => 'Child B Form must be a file of type: pdf,png,jpeg,jpg',
                'cnic_nominee_1.mimes' => 'CNIC Nominee must be a file of type: pdf,png,jpeg,jpg',
                'cnic_nominee_2.mimes' => 'CNIC Nominee must be a file of type: pdf,png,jpeg,jpg',
                'cnic_nominee_3.mimes' => 'CNIC Nominee must be a file of type: pdf,png,jpeg,jpg',
                'cnic_nominee_4.mimes' => 'CNIC Nominee must be a file of type: pdf,png,jpeg,jpg',
                'utility_bill_1.mimes' => 'Utility Bill must be a file of type: pdf,png,jpeg,jpg',
                'utility_bill_2.mimes' => 'Utility Bill must be a file of type: pdf,png,jpeg,jpg',
                'utility_bill_3.mimes' => 'Utility Bill must be a file of type: pdf,png,jpeg,jpg',
                'utility_bill_4.mimes' => 'Utility Bill must be a file of type: pdf,png,jpeg,jpg',
                'affidavit_1.mimes' => 'Affidavit must be a file of type: pdf,png,jpeg,jpg',
                'affidavit_2.mimes' => 'Affidavit must be a file of type: pdf,png,jpeg,jpg',
                'affidavit_3.mimes' => 'Affidavit must be a file of type: pdf,png,jpeg,jpg',
                'affidavit_4.mimes' => 'Affidavit must be a file of type: pdf,png,jpeg,jpg',
                'cheque_1.mimes' => 'Cheque must be a file of type: pdf,png,jpeg,jpg',
                'cheque_2.mimes' => 'Cheque must be a file of type: pdf,png,jpeg,jpg',
                'cheque_3.mimes' => 'Cheque must be a file of type: pdf,png,jpeg,jpg',
                'cheque_4.mimes' => 'Cheque must be a file of type: pdf,png,jpeg,jpg',
            ]);

        if($employee->attachments()->exists())
        {
            $attachments = $employee->attachments;
        }
        else{
            $attachments = new EmployeeAttachment();
            $attachments->employee_id = $employee->id;
        }

        $date = Carbon::now()->format('Y_m_d');

        if ($request->hasFile('cv_1') || $request->hasFile('cv_2') || $request->hasFile('cv_3') || $request->hasFile('cv_4'))       {
            $cv_array = [];
            if($attachments->cv != NULL) {
                $cvs = explode(',', $attachments->cv);
                foreach ($cvs as $cv)
                {
                    $pos = strpos($cv, "cv_1_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cv_1')) {
                            Storage::disk('public')->delete($cv);
                        }
                        $cv_array[0] = $cv;
                    }
                    $pos = strpos($cv, "cv_2_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cv_2')) {
                            Storage::disk('public')->delete($cv);
                        }
                        $cv_array[1] = $cv;
                    }
                    $pos = strpos($cv, "cv_3_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cv_3')) {
                            Storage::disk('public')->delete($cv);
                        }
                        $cv_array[2] = $cv;
                    }
                    $pos = strpos($cv, "cv_4_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cv_4')) {
                            Storage::disk('public')->delete($cv);
                        }
                        $cv_array[3] = $cv;
                    }
                }
            }
            if($request->hasFile('cv_1'))
            {
                $file = $request->file('cv_1');
                $filename = 'cv_1_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cv_array[0] = $directory.'/'.$filename;
            }
            if($request->hasFile('cv_2'))
            {
                $file = $request->file('cv_2');
                $filename = 'cv_2_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cv_array[1] = $directory.'/'.$filename;
            }
            if($request->hasFile('cv_3'))
            {
                $file = $request->file('cv_3');
                $filename = 'cv_3_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cv_array[2] = $directory.'/'.$filename;
            }
            if($request->hasFile('cv_4'))
            {
                $file = $request->file('cv_4');
                $filename = 'cv_4_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cv_array[3] = $directory.'/'.$filename;
            }

            $attachments->cv = implode(',',$cv_array);
        }

        if ($request->hasFile('cnic_1') || $request->hasFile('cnic_2')|| $request->hasFile('cnic_3') || $request->hasFile('cnic_4'))
        {
            $cnic_array = [];
            if($attachments->cnic != NULL) {
                $cnics = explode(',',$attachments->cnic);
                foreach ($cnics as $cnic)
                {
                    $pos = strpos($cnic, "cnic_1_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cnic_1')) {
                            Storage::disk('public')->delete($cnic);
                        }
                        $cnic_array[0] = $cnic;
                    }
                    $pos = strpos($cnic, "cnic_2_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cnic_2')) {
                            Storage::disk('public')->delete($cnic);
                        }
                        $cnic_array[1] = $cnic;
                    }
                    $pos = strpos($cnic, "cnic_3_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cnic_3')) {
                            Storage::disk('public')->delete($cnic);
                        }
                        $cnic_array[2] = $cnic;
                    }
                    $pos = strpos($cnic, "cnic_4_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cnic_4')) {
                            Storage::disk('public')->delete($cnic);
                        }
                        $cnic_array[3] = $cnic;
                    }
                }
            }

            if($request->hasFile('cnic_1'))
            {
                $file = $request->file('cnic_1');
                $filename = 'cnic_1_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cnic_array[0] = $directory.'/'.$filename;
            }
            if($request->hasFile('cnic_2'))
            {
                $file = $request->file('cnic_2');
                $filename = 'cnic_2_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cnic_array[1] = $directory.'/'.$filename;
            }
            if($request->hasFile('cnic_3'))
            {
                $file = $request->file('cnic_3');
                $filename = 'cnic_3_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cnic_array[2] = $directory.'/'.$filename;
            }
            if($request->hasFile('cnic_4'))
            {
                $file = $request->file('cnic_4');
                $filename = 'cnic_4_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cnic_array[3] = $directory.'/'.$filename;
            }

            $attachments->cnic = implode(',',$cnic_array);

        }

        if ($request->hasFile('photo_1') || $request->hasFile('photo_2') || $request->hasFile('photo_3') || $request->hasFile('photo_4'))       {
            $photo_array = [];
            if($attachments->photo != NULL) {
                $photos = explode(',', $attachments->photo);
                foreach ($photos as $photo)
                {
                    $pos = strpos($photo, "photo_1_");
                    if($pos !== false)
                    {
                        if($request->hasFile('photo_1')) {
                            Storage::disk('public')->delete($photo);
                        }
                        $photo_array[0] = $photo;
                    }
                    $pos = strpos($photo, "photo_2_");
                    if($pos !== false)
                    {
                        if($request->hasFile('photo_2')) {
                            Storage::disk('public')->delete($photo);
                        }
                        $photo_array[1] = $photo;
                    }
                    $pos = strpos($photo, "photo_3_");
                    if($pos !== false)
                    {
                        if($request->hasFile('photo_3')) {
                            Storage::disk('public')->delete($photo);
                        }
                        $photo_array[2] = $photo;
                    }
                    $pos = strpos($photo, "photo_4_");
                    if($pos !== false)
                    {
                        if($request->hasFile('photo_4')) {
                            Storage::disk('public')->delete($photo);
                        }
                        $photo_array[3] = $photo;
                    }
                }
            }
            if($request->hasFile('photo_1'))
            {
                $file = $request->file('photo_1');
                $filename = 'photo_1_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $photo_array[0] = $directory.'/'.$filename;
            }
            if($request->hasFile('photo_2'))
            {
                $file = $request->file('photo_2');
                $filename = 'photo_2_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $photo_array[1] = $directory.'/'.$filename;
            }
            if($request->hasFile('photo_3'))
            {
                $file = $request->file('photo_3');
                $filename = 'photo_3_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $photo_array[2] = $directory.'/'.$filename;
            }
            if($request->hasFile('photo_4'))
            {
                $file = $request->file('photo_4');
                $filename = 'photo_4_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $photo_array[3] = $directory.'/'.$filename;
            }

            $attachments->photo = implode(',',$photo_array);
        }


        if ($request->hasFile('academic_1') || $request->hasFile('academic_2') || $request->hasFile('academic_3') || $request->hasFile('academic_4'))       {
            $academic_array = [];
            if($attachments->academic != NULL) {
                $academics = explode(',', $attachments->academic);
                foreach ($academics as $academic)
                {
                    $pos = strpos($academic, "academic_1_");
                    if($pos !== false)
                    {
                        if($request->hasFile('academic_1')) {
                            Storage::disk('public')->delete($academic);
                        }
                        $academic_array[0] = $academic;
                    }
                    $pos = strpos($academic, "academic_2_");
                    if($pos !== false)
                    {
                        if($request->hasFile('academic_2')) {
                            Storage::disk('public')->delete($academic);
                        }
                        $academic_array[1] = $academic;
                    }
                    $pos = strpos($academic, "academic_3_");
                    if($pos !== false)
                    {
                        if($request->hasFile('academic_3')) {
                            Storage::disk('public')->delete($academic);
                        }
                        $academic_array[2] = $academic;
                    }
                    $pos = strpos($academic, "academic_4_");
                    if($pos !== false)
                    {
                        if($request->hasFile('academic_4')) {
                            Storage::disk('public')->delete($academic);
                        }
                        $academic_array[3] = $academic;
                    }
                }
            }
            if($request->hasFile('academic_1'))
            {
                $file = $request->file('academic_1');
                $filename = 'academic_1_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $academic_array[0] = $directory.'/'.$filename;
            }
            if($request->hasFile('academic_2'))
            {
                $file = $request->file('academic_2');
                $filename = 'academic_2_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $academic_array[1] = $directory.'/'.$filename;
            }
            if($request->hasFile('academic_3'))
            {
                $file = $request->file('academic_3');
                $filename = 'academic_3_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $academic_array[2] = $directory.'/'.$filename;
            }
            if($request->hasFile('academic_4'))
            {
                $file = $request->file('academic_4');
                $filename = 'academic_4_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $academic_array[3] = $directory.'/'.$filename;
            }

            $attachments->academic = implode(',',$academic_array);
        }

        if ($request->hasFile('experience_certificate_1') || $request->hasFile('experience_certificate_2') || $request->hasFile('experience_certificate_3') || $request->hasFile('experience_certificate_4'))       {
            $experience_certificate_array = [];
            if($attachments->experience != NULL) {
                $experience_certificates = explode(',', $attachments->experience);
                foreach ($experience_certificates as $experience_certificate)
                {
                    $pos = strpos($experience_certificate, "experience_certificate_1_");
                    if($pos !== false)
                    {
                        if($request->hasFile('experience_certificate_1')) {
                            Storage::disk('public')->delete($experience_certificate);
                        }
                        $experience_certificate_array[0] = $experience_certificate;
                    }
                    $pos = strpos($experience_certificate, "experience_certificate_2_");
                    if($pos !== false)
                    {
                        if($request->hasFile('experience_certificate_2')) {
                            Storage::disk('public')->delete($experience_certificate);
                        }
                        $experience_certificate_array[1] = $experience_certificate;
                    }
                    $pos = strpos($experience_certificate, "experience_certificate_3_");
                    if($pos !== false)
                    {
                        if($request->hasFile('experience_certificate_3')) {
                            Storage::disk('public')->delete($experience_certificate);
                        }
                        $experience_certificate_array[2] = $experience_certificate;
                    }
                    $pos = strpos($experience_certificate, "experience_certificate_4_");
                    if($pos !== false)
                    {
                        if($request->hasFile('experience_certificate_4')) {
                            Storage::disk('public')->delete($experience_certificate);
                        }
                        $experience_certificate_array[3] = $experience_certificate;
                    }
                }
            }
            if($request->hasFile('experience_certificate_1'))
            {
                $file = $request->file('experience_certificate_1');
                $filename = 'experience_certificate_1_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $experience_certificate_array[0] = $directory.'/'.$filename;
            }
            if($request->hasFile('experience_certificate_2'))
            {
                $file = $request->file('experience_certificate_2');
                $filename = 'experience_certificate_2_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $experience_certificate_array[1] = $directory.'/'.$filename;
            }
            if($request->hasFile('experience_certificate_3'))
            {
                $file = $request->file('experience_certificate_3');
                $filename = 'experience_certificate_3_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $experience_certificate_array[2] = $directory.'/'.$filename;
            }
            if($request->hasFile('experience_certificate_4'))
            {
                $file = $request->file('experience_certificate_4');
                $filename = 'experience_certificate_4_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $experience_certificate_array[3] = $directory.'/'.$filename;
            }

            $attachments->experience = implode(',',$experience_certificate_array);
        }

        if ($request->hasFile('last_pay_slip_1') || $request->hasFile('last_pay_slip_2') || $request->hasFile('last_pay_slip_3') || $request->hasFile('last_pay_slip_4'))       {
            $last_pay_slip_array = [];
            if($attachments->last_pay_slip != NULL) {
                $last_pay_slips = explode(',', $attachments->last_pay_slip);
                foreach ($last_pay_slips as $last_pay_slip)
                {
                    $pos = strpos($last_pay_slip, "last_pay_slip_1_");
                    if($pos !== false)
                    {
                        if($request->hasFile('last_pay_slip_1')) {
                            Storage::disk('public')->delete($last_pay_slip);
                        }
                        $last_pay_slip_array[0] = $last_pay_slip;
                    }
                    $pos = strpos($last_pay_slip, "last_pay_slip_2_");
                    if($pos !== false)
                    {
                        if($request->hasFile('last_pay_slip_2')) {
                            Storage::disk('public')->delete($last_pay_slip);
                        }
                        $last_pay_slip_array[1] = $last_pay_slip;
                    }
                    $pos = strpos($last_pay_slip, "last_pay_slip_3_");
                    if($pos !== false)
                    {
                        if($request->hasFile('last_pay_slip_3')) {
                            Storage::disk('public')->delete($last_pay_slip);
                        }
                        $last_pay_slip_array[2] = $last_pay_slip;
                    }
                    $pos = strpos($last_pay_slip, "last_pay_slip_4_");
                    if($pos !== false)
                    {
                        if($request->hasFile('last_pay_slip_4')) {
                            Storage::disk('public')->delete($last_pay_slip);
                        }
                        $last_pay_slip_array[3] = $last_pay_slip;
                    }
                }
            }
            if($request->hasFile('last_pay_slip_1'))
            {
                $file = $request->file('last_pay_slip_1');
                $filename = 'last_pay_slip_1_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $last_pay_slip_array[0] = $directory.'/'.$filename;
            }
            if($request->hasFile('last_pay_slip_2'))
            {
                $file = $request->file('last_pay_slip_2');
                $filename = 'last_pay_slip_2_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $last_pay_slip_array[1] = $directory.'/'.$filename;
            }
            if($request->hasFile('last_pay_slip_3'))
            {
                $file = $request->file('last_pay_slip_3');
                $filename = 'last_pay_slip_3_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $last_pay_slip_array[2] = $directory.'/'.$filename;
            }
            if($request->hasFile('last_pay_slip_4'))
            {
                $file = $request->file('last_pay_slip_4');
                $filename = 'last_pay_slip_4_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $last_pay_slip_array[3] = $directory.'/'.$filename;
            }

            $attachments->last_pay_slip = implode(',',$last_pay_slip_array);
        }

        if ($request->hasFile('nikkah_nama_1') || $request->hasFile('nikkah_nama_2') || $request->hasFile('nikkah_nama_3') || $request->hasFile('nikkah_nama_4'))       {
            $nikkah_nama_array = [];
            if($attachments->nikkah_nama != NULL) {
                $nikkah_namas = explode(',', $attachments->nikkah_nama);
                foreach ($nikkah_namas as $nikkah_nama)
                {
                    $pos = strpos($nikkah_nama, "nikkah_nama_1_");
                    if($pos !== false)
                    {
                        if($request->hasFile('nikkah_nama_1')) {
                            Storage::disk('public')->delete($nikkah_nama);
                        }
                        $nikkah_nama_array[0] = $nikkah_nama;
                    }
                    $pos = strpos($nikkah_nama, "nikkah_nama_2_");
                    if($pos !== false)
                    {
                        if($request->hasFile('nikkah_nama_2')) {
                            Storage::disk('public')->delete($nikkah_nama);
                        }
                        $nikkah_nama_array[1] = $nikkah_nama;
                    }
                    $pos = strpos($nikkah_nama, "nikkah_nama_3_");
                    if($pos !== false)
                    {
                        if($request->hasFile('nikkah_nama_3')) {
                            Storage::disk('public')->delete($nikkah_nama);
                        }
                        $nikkah_nama_array[2] = $nikkah_nama;
                    }
                    $pos = strpos($nikkah_nama, "nikkah_nama_4_");
                    if($pos !== false)
                    {
                        if($request->hasFile('nikkah_nama_4')) {
                            Storage::disk('public')->delete($nikkah_nama);
                        }
                        $nikkah_nama_array[3] = $nikkah_nama;
                    }
                }
            }
            if($request->hasFile('nikkah_nama_1'))
            {
                $file = $request->file('nikkah_nama_1');
                $filename = 'nikkah_nama_1_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $nikkah_nama_array[0] = $directory.'/'.$filename;
            }
            if($request->hasFile('nikkah_nama_2'))
            {
                $file = $request->file('nikkah_nama_2');
                $filename = 'nikkah_nama_2_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $nikkah_nama_array[1] = $directory.'/'.$filename;
            }
            if($request->hasFile('nikkah_nama_3'))
            {
                $file = $request->file('nikkah_nama_3');
                $filename = 'nikkah_nama_3_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $nikkah_nama_array[2] = $directory.'/'.$filename;
            }
            if($request->hasFile('nikkah_nama_4'))
            {
                $file = $request->file('nikkah_nama_4');
                $filename = 'nikkah_nama_4_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $nikkah_nama_array[3] = $directory.'/'.$filename;
            }

            $attachments->nikkah_nama = implode(',',$nikkah_nama_array);
        }

        if ($request->hasFile('cnic_spouse_1') || $request->hasFile('cnic_spouse_2') || $request->hasFile('cnic_spouse_3') || $request->hasFile('cnic_spouse_4'))       {
            $cnic_spouse_array = [];
            if($attachments->cnic_spouse != NULL) {
                $cnic_spouses = explode(',', $attachments->cnic_spouse);
                foreach ($cnic_spouses as $cnic_spouse)
                {
                    $pos = strpos($cnic_spouse, "cnic_spouse_1_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cnic_spouse_1')) {
                            Storage::disk('public')->delete($cnic_spouse);
                        }
                        $cnic_spouse_array[0] = $cnic_spouse;
                    }
                    $pos = strpos($cnic_spouse, "cnic_spouse_2_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cnic_spouse_2')) {
                            Storage::disk('public')->delete($cnic_spouse);
                        }
                        $cnic_spouse_array[1] = $cnic_spouse;
                    }
                    $pos = strpos($cnic_spouse, "cnic_spouse_3_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cnic_spouse_3')) {
                            Storage::disk('public')->delete($cnic_spouse);
                        }
                        $cnic_spouse_array[2] = $cnic_spouse;
                    }
                    $pos = strpos($cnic_spouse, "cnic_spouse_4_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cnic_spouse_4')) {
                            Storage::disk('public')->delete($cnic_spouse);
                        }
                        $cnic_spouse_array[3] = $cnic_spouse;
                    }
                }
            }
            if($request->hasFile('cnic_spouse_1'))
            {
                $file = $request->file('cnic_spouse_1');
                $filename = 'cnic_spouse_1_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cnic_spouse_array[0] = $directory.'/'.$filename;
            }
            if($request->hasFile('cnic_spouse_2'))
            {
                $file = $request->file('cnic_spouse_2');
                $filename = 'cnic_spouse_2_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cnic_spouse_array[1] = $directory.'/'.$filename;
            }
            if($request->hasFile('cnic_spouse_3'))
            {
                $file = $request->file('cnic_spouse_3');
                $filename = 'cnic_spouse_3_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cnic_spouse_array[2] = $directory.'/'.$filename;
            }
            if($request->hasFile('cnic_spouse_4'))
            {
                $file = $request->file('cnic_spouse_4');
                $filename = 'cnic_spouse_4_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cnic_spouse_array[3] = $directory.'/'.$filename;
            }

            $attachments->cnic_spouse = implode(',',$cnic_spouse_array);
        }

        if ($request->hasFile('child_b_form_1') || $request->hasFile('child_b_form_2') || $request->hasFile('child_b_form_3') || $request->hasFile('child_b_form_4'))       {
            $child_b_form_array = [];
            if($attachments->child_b_form != NULL) {
                $child_b_forms = explode(',', $attachments->child_b_form);
                foreach ($child_b_forms as $child_b_form)
                {
                    $pos = strpos($child_b_form, "child_b_form_1_");
                    if($pos !== false)
                    {
                        if($request->hasFile('child_b_form_1')) {
                            Storage::disk('public')->delete($child_b_form);
                        }
                        $child_b_form_array[0] = $child_b_form;
                    }
                    $pos = strpos($child_b_form, "child_b_form_2_");
                    if($pos !== false)
                    {
                        if($request->hasFile('child_b_form_2')) {
                            Storage::disk('public')->delete($child_b_form);
                        }
                        $child_b_form_array[1] = $child_b_form;
                    }
                    $pos = strpos($child_b_form, "child_b_form_3_");
                    if($pos !== false)
                    {
                        if($request->hasFile('child_b_form_3')) {
                            Storage::disk('public')->delete($child_b_form);
                        }
                        $child_b_form_array[2] = $child_b_form;
                    }
                    $pos = strpos($child_b_form, "child_b_form_4_");
                    if($pos !== false)
                    {
                        if($request->hasFile('child_b_form_4')) {
                            Storage::disk('public')->delete($child_b_form);
                        }
                        $child_b_form_array[3] = $child_b_form;
                    }
                }
            }
            if($request->hasFile('child_b_form_1'))
            {
                $file = $request->file('child_b_form_1');
                $filename = 'child_b_form_1_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $child_b_form_array[0] = $directory.'/'.$filename;
            }
            if($request->hasFile('child_b_form_2'))
            {
                $file = $request->file('child_b_form_2');
                $filename = 'child_b_form_2_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $child_b_form_array[1] = $directory.'/'.$filename;
            }
            if($request->hasFile('child_b_form_3'))
            {
                $file = $request->file('child_b_form_3');
                $filename = 'child_b_form_3_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $child_b_form_array[2] = $directory.'/'.$filename;
            }
            if($request->hasFile('child_b_form_4'))
            {
                $file = $request->file('child_b_form_4');
                $filename = 'child_b_form_4_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $child_b_form_array[3] = $directory.'/'.$filename;
            }

            $attachments->child_b_form = implode(',',$child_b_form_array);
        }

        if ($request->hasFile('cnic_nominee_1') || $request->hasFile('cnic_nominee_2') || $request->hasFile('cnic_nominee_3') || $request->hasFile('cnic_nominee_4'))       {
            $cnic_nominee_array = [];
            if($attachments->cnic_nominee != NULL) {
                $cnic_nominees = explode(',', $attachments->cnic_nominee);
                foreach ($cnic_nominees as $cnic_nominee)
                {
                    $pos = strpos($cnic_nominee, "cnic_nominee_1_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cnic_nominee_1')) {
                            Storage::disk('public')->delete($cnic_nominee);
                        }
                        $cnic_nominee_array[0] = $cnic_nominee;
                    }
                    $pos = strpos($cnic_nominee, "cnic_nominee_2_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cnic_nominee_2')) {
                            Storage::disk('public')->delete($cnic_nominee);
                        }
                        $cnic_nominee_array[1] = $cnic_nominee;
                    }
                    $pos = strpos($cnic_nominee, "cnic_nominee_3_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cnic_nominee_3')) {
                            Storage::disk('public')->delete($cnic_nominee);
                        }
                        $cnic_nominee_array[2] = $cnic_nominee;
                    }
                    $pos = strpos($cnic_nominee, "cnic_nominee_4_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cnic_nominee_4')) {
                            Storage::disk('public')->delete($cnic_nominee);
                        }
                        $cnic_nominee_array[3] = $cnic_nominee;
                    }
                }
            }
            if($request->hasFile('cnic_nominee_1'))
            {
                $file = $request->file('cnic_nominee_1');
                $filename = 'cnic_nominee_1_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cnic_nominee_array[0] = $directory.'/'.$filename;
            }
            if($request->hasFile('cnic_nominee_2'))
            {
                $file = $request->file('cnic_nominee_2');
                $filename = 'cnic_nominee_2_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cnic_nominee_array[1] = $directory.'/'.$filename;
            }
            if($request->hasFile('cnic_nominee_3'))
            {
                $file = $request->file('cnic_nominee_3');
                $filename = 'cnic_nominee_3_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cnic_nominee_array[2] = $directory.'/'.$filename;
            }
            if($request->hasFile('cnic_nominee_4'))
            {
                $file = $request->file('cnic_nominee_4');
                $filename = 'cnic_nominee_4_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cnic_nominee_array[3] = $directory.'/'.$filename;
            }

            $attachments->cnic_nominee = implode(',',$cnic_nominee_array);
        }

        if ($request->hasFile('utility_bill_1') || $request->hasFile('utility_bill_2') || $request->hasFile('utility_bill_3') || $request->hasFile('utility_bill_4'))       {
            $utility_bill_array = [];
            if($attachments->utility_bill != NULL) {
                $utility_bills = explode(',', $attachments->utility_bill);
                foreach ($utility_bills as $utility_bill)
                {
                    $pos = strpos($utility_bill, "utility_bill_1_");
                    if($pos !== false)
                    {
                        if($request->hasFile('utility_bill_1')) {
                            Storage::disk('public')->delete($utility_bill);
                        }
                        $utility_bill_array[0] = $utility_bill;
                    }
                    $pos = strpos($utility_bill, "utility_bill_2_");
                    if($pos !== false)
                    {
                        if($request->hasFile('utility_bill_2')) {
                            Storage::disk('public')->delete($utility_bill);
                        }
                        $utility_bill_array[1] = $utility_bill;
                    }
                    $pos = strpos($utility_bill, "utility_bill_3_");
                    if($pos !== false)
                    {
                        if($request->hasFile('utility_bill_3')) {
                            Storage::disk('public')->delete($utility_bill);
                        }
                        $utility_bill_array[2] = $utility_bill;
                    }
                    $pos = strpos($utility_bill, "utility_bill_4_");
                    if($pos !== false)
                    {
                        if($request->hasFile('utility_bill_4')) {
                            Storage::disk('public')->delete($utility_bill);
                        }
                        $utility_bill_array[3] = $utility_bill;
                    }
                }
            }
            if($request->hasFile('utility_bill_1'))
            {
                $file = $request->file('utility_bill_1');
                $filename = 'utility_bill_1_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $utility_bill_array[0] = $directory.'/'.$filename;
            }
            if($request->hasFile('utility_bill_2'))
            {
                $file = $request->file('utility_bill_2');
                $filename = 'utility_bill_2_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $utility_bill_array[1] = $directory.'/'.$filename;
            }
            if($request->hasFile('utility_bill_3'))
            {
                $file = $request->file('utility_bill_3');
                $filename = 'utility_bill_3_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $utility_bill_array[2] = $directory.'/'.$filename;
            }
            if($request->hasFile('utility_bill_4'))
            {
                $file = $request->file('utility_bill_4');
                $filename = 'utility_bill_4_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $utility_bill_array[3] = $directory.'/'.$filename;
            }

            $attachments->utility_bill = implode(',',$utility_bill_array);
        }

        if ($request->hasFile('affidavit_1') || $request->hasFile('affidavit_2') || $request->hasFile('affidavit_3') || $request->hasFile('affidavit_4'))       {
            $affidavit_array = [];
            if($attachments->affidavit != NULL) {
                $affidavits = explode(',', $attachments->affidavit);
                foreach ($affidavits as $affidavit)
                {
                    $pos = strpos($affidavit, "affidavit_1_");
                    if($pos !== false)
                    {
                        if($request->hasFile('affidavit_1')) {
                            Storage::disk('public')->delete($affidavit);
                        }
                        $affidavit_array[0] = $affidavit;
                    }
                    $pos = strpos($affidavit, "affidavit_2_");
                    if($pos !== false)
                    {
                        if($request->hasFile('affidavit_2')) {
                            Storage::disk('public')->delete($affidavit);
                        }
                        $affidavit_array[1] = $affidavit;
                    }
                    $pos = strpos($affidavit, "affidavit_3_");
                    if($pos !== false)
                    {
                        if($request->hasFile('affidavit_3')) {
                            Storage::disk('public')->delete($affidavit);
                        }
                        $affidavit_array[2] = $affidavit;
                    }
                    $pos = strpos($affidavit, "affidavit_4_");
                    if($pos !== false)
                    {
                        if($request->hasFile('affidavit_4')) {
                            Storage::disk('public')->delete($affidavit);
                        }
                        $affidavit_array[3] = $affidavit;
                    }
                }
            }
            if($request->hasFile('affidavit_1'))
            {
                $file = $request->file('affidavit_1');
                $filename = 'affidavit_1_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $affidavit_array[0] = $directory.'/'.$filename;
            }
            if($request->hasFile('affidavit_2'))
            {
                $file = $request->file('affidavit_2');
                $filename = 'affidavit_2_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $affidavit_array[1] = $directory.'/'.$filename;
            }
            if($request->hasFile('affidavit_3'))
            {
                $file = $request->file('affidavit_3');
                $filename = 'affidavit_3_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $affidavit_array[2] = $directory.'/'.$filename;
            }
            if($request->hasFile('affidavit_4'))
            {
                $file = $request->file('affidavit_4');
                $filename = 'affidavit_4_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $affidavit_array[3] = $directory.'/'.$filename;
            }

            $attachments->affidavit = implode(',',$affidavit_array);
        }

        if ($request->hasFile('cheque_1') || $request->hasFile('cheque_2') || $request->hasFile('cheque_3') || $request->hasFile('cheque_4'))       {
            $cheque_array = [];
            if($attachments->cheque != NULL) {
                $cheques = explode(',', $attachments->cheque);
                foreach ($cheques as $cheque)
                {
                    $pos = strpos($cheque, "cheque_1_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cheque_1')) {
                            Storage::disk('public')->delete($cheque);
                        }
                        $cheque_array[0] = $cheque;
                    }
                    $pos = strpos($cheque, "cheque_2_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cheque_2')) {
                            Storage::disk('public')->delete($cheque);
                        }
                        $cheque_array[1] = $cheque;
                    }
                    $pos = strpos($cheque, "cheque_3_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cheque_3')) {
                            Storage::disk('public')->delete($cheque);
                        }
                        $cheque_array[2] = $cheque;
                    }
                    $pos = strpos($cheque, "cheque_4_");
                    if($pos !== false)
                    {
                        if($request->hasFile('cheque_4')) {
                            Storage::disk('public')->delete($cheque);
                        }
                        $cheque_array[3] = $cheque;
                    }
                }
            }
            if($request->hasFile('cheque_1'))
            {
                $file = $request->file('cheque_1');
                $filename = 'cheque_1_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cheque_array[0] = $directory.'/'.$filename;
            }
            if($request->hasFile('cheque_2'))
            {
                $file = $request->file('cheque_2');
                $filename = 'cheque_2_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cheque_array[1] = $directory.'/'.$filename;
            }
            if($request->hasFile('cheque_3'))
            {
                $file = $request->file('cheque_3');
                $filename = 'cheque_3_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cheque_array[2] = $directory.'/'.$filename;
            }
            if($request->hasFile('cheque_4'))
            {
                $file = $request->file('cheque_4');
                $filename = 'cheque_4_'. $date . '.' . $file->extension();
                $directory = 'employee_directory/employee_' . $employee->id . '';
                Storage::disk('public')->putFileAs($directory, $file, $filename);
                $cheque_array[3] = $directory.'/'.$filename;
            }

            $attachments->cheque = implode(',',$cheque_array);
        }


        $attachments->save();

        $employee->request_status_id = 2;
        $employee->status_id =  ($employee->status_id == 2) ? 2 : self::GetStatusOfEmployee($employee->id);
        $employee->update();


        return back()->with(['success'=>'Employee Attachments Updated Successfully']);
    }
    public function reporting_location_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),388);
        $cities = City::where('status', 1)->where('business_category_id', 1)->get();
        return view('admin.human_resource.reporting_location')->with(['cities' => $cities]);
    }

    public function reporting_location_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),389);
        }
        $location = ReportingLocation::join('cities as c', 'reporting_locations.city_id', '=', 'c.id')
            ->select(['reporting_locations.id as id', 'reporting_locations.name as location_name', 'c.name as city', 'c.id as city_id', 'reporting_locations.lat', 'reporting_locations.long', 'reporting_locations.status', 'reporting_locations.address', 'reporting_locations.radius']);

        if (session('role_id') != 1) {
            $location = $location->whereIn('c.hub_id', session('hubs'));
        }

        return Datatables::of($location)
            ->addColumn('map', function ($data) {
               return '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="http://www.google.com/maps/place/' . $data->lat . ',' . $data->long . '" target="_blank"><i class="la la-map-marker align-middle"></i></a></button>';
            })
            ->editColumn('status', function ($data) {
                if($data->status == 0){
                    return 'In-Active';
                }
                else{
                    return 'Active';
                }
            })
            ->addColumn("action", function ($data) {
                if(session('role_id') == 1 || in_array(479, session('permissions')) || in_array(480, session('permissions'))){
                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                    if (session('role_id') == 1 || in_array(479, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item edit" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    }
                    if (session('role_id') == 1 || in_array(480, session('permissions'))) {
                        if($data->status == 0) {
                            $dropdown .= '<button type="button" class="dropdown-item enable" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                        }
                        else{
                            $dropdown .= '<button type="button" class="dropdown-item disable" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                        }
                    }
                    $dropdown .= '
                </div>
              </div>
            ';
                    return $dropdown;
                }
                else{
                    return '';
                }
            })
            ->make(true);
    }

    public function reporting_location_status(Request $request){
        $id = $request->id;
        $location = ReportingLocation::find($id);
        if($request->status == 0){
            $status = 'Disabled';
        }
        else{
            $status = 'Enabled';
        }
        $location->status = $request->status;
        $location->save();
        return response()->json(['status' => 1, 'success' => 'Location '. $status .' successfully!']);
    }

    public function reporting_location_add(Request $request){
        $location = new ReportingLocation();
        $location->city_id = $request->city;
        $location->name = $request->name;
        $location->address = $request->address;
        $location->lat = $request->lat;
        $location->long = $request->long;
        $location->radius = $request->radius;
        $location->save();
        return redirect()->back()->with('success', 'Location Added Successfully!');
    }

    public function reporting_location_edit(Request $request){
        $location = ReportingLocation::find($request->location_id);
        $location->city_id = $request->city;
        $location->name = $request->name;
        $location->address = $request->address;
        $location->lat = $request->lat;
        $location->long = $request->long;
        $location->radius = $request->radius;
        $location->save();
        return redirect()->back()->with('success', 'Location Updated Successfully!');
    }
    public function designation_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),390);
        $departments = AdminDepartment::all();
        $hubs = City::select('id','name')->where('hub',1)->get();
        return view('admin.human_resource.designation',compact('departments','hubs'));
    }

    public function designation_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),391);
        }
        $designations = EmployeeDesignation::leftjoin('admin_departments as ad','ad.id','employee_designations.department_id')
            ->leftjoin('admin_roles as r','r.id','employee_designations.role_id')
            ->select(['employee_designations.id as id', 'employee_designations.name as name', 'employee_designations.code', 'employee_designations.status', 'employee_designations.description','employee_designations.department_id','ad.name as department','r.name as role','r.id as role_id']);

        return Datatables::of($designations)
            ->editColumn('status', function ($data) {
                if($data->status == 0){
                    return 'In-Active';
                }
                else{
                    return 'Active';
                }
            })
            ->addColumn('hubs',function($data){
                return EmployeeDesignationHub::where('designation_id',$data->id)->get(['hub_id'])->toArray();
            })
            ->addColumn("action", function ($data) {
                if(session('role_id') == 1 || in_array(482, session('permissions')) || in_array(483, session('permissions'))){
                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                    if (session('role_id') == 1 || in_array(482, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item edit" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    }
                    if (session('role_id') == 1 || in_array(483, session('permissions'))) {
                        if($data->status == 0) {
                            $dropdown .= '<button type="button" class="dropdown-item enable" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                        }
                        else{
                            $dropdown .= '<button type="button" class="dropdown-item disable" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                        }
                    }
                    $dropdown .= '
                </div>
              </div>
            ';
                    return $dropdown;
                }
                else{
                    return '';
                }
            })
            ->make(true);
    }

    public function designation_status(Request $request){
        $id = $request->id;
        $designation = EmployeeDesignation::find($id);
        if($request->status == 0){
            $status = 'Disabled';
        }
        else{
            $status = 'Enabled';
        }
        $designation->status = $request->status;
        $designation->save();
        return response()->json(['status' => 1, 'success' => 'Designation '. $status .' successfully!']);
    }

    public function designation_roles(Request $request)
    {
        $roles = AdminRole::where('department_id',$request->department_id)->get();

        return response()->json(['status'=>1,'roles'=>$roles]);
    }

    public function designation_add(Request $request){
        $designation = new EmployeeDesignation();
        $designation->name = $request->name;
        $designation->department_id = $request->department_id;
        $designation->role_id = $request->role_id;
        $designation->description = $request->description;
        $designation->save();

        foreach ($request->hub_id as $hub)
        {
            $designation_hub = new EmployeeDesignationHub();
            $designation_hub->designation_id = $designation->id;
            $designation_hub->hub_id = $hub;
            $designation_hub->save();
        }

        $designation->code = 'Des'. str_pad($designation->id, 3, '0', STR_PAD_LEFT);
        $designation->save();
        return redirect()->back()->with('success', 'Designation Added Successfully!');
    }

    public function designation_edit(Request $request){
        $designation = EmployeeDesignation::find($request->designation_id);
        $designation->name = $request->name;
        $designation->department_id = $request->department_id;
        $designation->role_id = $request->role_id;
        $designation->description = $request->description;
        $designation->save();

        EmployeeDesignationHub::where('designation_id',$designation->id)->delete();

        foreach ($request->hub_id as $hub)
        {
            $designation_hub = new EmployeeDesignationHub();
            $designation_hub->designation_id = $designation->id;
            $designation_hub->hub_id = $hub;
            $designation_hub->save();
        }

        $admins = Admin::where('designation_id',$designation->id)->get(['id']);
        foreach ($admins as $admin)
        {
            AdminHub::where('admin_id',$admin->id)->delete();
            foreach ($request->hub_id as $hub)
            {
                $admin_hub = new AdminHub();
                $admin_hub->admin_id = $admin->id;
                $admin_hub->hub_id = $hub;
                $admin_hub->save();
            }
        }

        return redirect()->back()->with('success', 'Designation Updated Successfully!');
    }

    public function employee_get_designation(Request $request)
    {
        if($request->has('department_id'))
        {
            $designations = EmployeeDesignation::where('department_id',$request->department_id)->where('status',1);
            if($designations->exists())
            {
                return response()->json(['status'=>1,'designations'=>$designations->get()]);
            }
            else{
                return response()->json(['status'=>0,'error'=>'Designation Not Found']);
            }
        }
        else{
            return response()->json(['status'=>0,'error'=>'Department is Required']);
        }
    }
    public function department_index(){

        ActivityTrailController::createActivityTrailLog(Auth::id(),392);
        return view('admin.human_resource.department');
    }

    public function department_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),393);
        }
        $departments = AdminDepartment::select(['admin_departments.id as id', 'admin_departments.name as name', 'admin_departments.code as code', 'admin_departments.description as description']);

        return Datatables::of($departments)
            ->addColumn("action", function ($data) {
                if(session('role_id') == 1 || in_array(485, session('permissions'))){
                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                    $dropdown .= '<button type="button" class="dropdown-item edit" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                    $dropdown .= '
                </div>
              </div>
            ';
                    return $dropdown;
                }
                else{
                    return '';
                }
            })
            ->make(true);
    }

    public function department_add(Request $request){
        $department = new AdminDepartment();
        $department->name = $request->name;
        $department->description = $request->description;
        $department->save();

        $department->code = 'Dep'. str_pad($department->id, 3, '0', STR_PAD_LEFT);
        $department->save();
        return redirect()->back()->with('success', 'Department Added Successfully!');
    }

    public function department_edit(Request $request){
        $department = AdminDepartment::find($request->department_id);
        $department->name = $request->name;
        $department->description = $request->description;
        $department->save();
        return redirect()->back()->with('success', 'Department Updated Successfully!');
    }

    public function rider_incentive_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),242);
        $cities = DB::table('cities')->select('id','name')->get();
        $hubs = DB::table('cities')->where('hub',1)->select('id','name')->get();
        $zones = DB::table('zones')->where('status',1)->select('id','name')->get();

        return view('admin.human_resource.rider_incentive')->with(['cities' => $cities, 'hubs' => $hubs, 'zones' => $zones]);
    }

    public function rider_incentive_list(Request $request){
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 243);
        }
        $incentives = RidersIncentive::join('riders', 'riders.id', '=', 'riders_incentives.rider_id')
            ->join('cities', 'cities.id', '=', 'riders.city_id')
            ->join('rider_categories as rc', 'rc.id', '=', 'riders.rider_category_id')
            ->join('rider_types as rt', 'rt.id', '=', 'riders.rider_type_id')
            ->select('riders.id as rider_id', 'riders.name as rider_name', 'riders.phone as rider_phone', 'riders.cnic', 'riders.employee_id', 'rt.name as rider_type','cities.name as rider_city', 'riders_incentives.date', 'riders_incentives.pickup_shipments', 'riders_incentives.pickup_incentive', 'riders_incentives.delivery_shipments', 'riders_incentives.delivery_incentive', 'riders.trax_id as employee_id');

        $datatable = Datatables::of($incentives);

        if($city = $request->get('search_city')){
            $datatable->where('cities.id', '=', $city);
        }
        if($hub = $request->get('search_hub')){
            $datatable->where('cities.hub_id', '=', $hub);
        }

        if($zone = $request->get('search_zone')){
            $datatable->where('cities.zone_id', '=', $zone);
        }

        if($employee_id = $request->get('employee_id')){
            $datatable->where('riders.trax_id', '=', $employee_id);
        }

        if($employee_name = $request->get('employee_name')){
            $datatable->where('riders.name', 'like', "%".$employee_name."%");
        }

        if ($request->get('search_date_from') != null && $request->get('search_date_from') != null) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('riders_incentives.date', [$from,$to]);
        }


        return $datatable->make(true);



    }

    public function employee_shift_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),433);
        return view('admin.human_resource.employee_shift');
    }

    public function employee_shift_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(),434);
        }

        $shifts = EmployeeShift::all();
        return Datatables::of($shifts)
            ->editColumn('status', function ($data) {
                if ($data->status == 0) {
                    return 'In-Active';
                } else {
                    return 'Active';
                }
            })
            ->editColumn('start_time_formatted', function ($data) {
                return Carbon::parse($data->start_time)->format("g:i A");
            })
            ->editColumn('end_time_formatted', function ($data) {
                return Carbon::parse($data->end_time)->format("g:i A");
            })
            ->addColumn("action", function ($data) {
                if (session('role_id') == 1 || in_array(593, session('permissions')) || in_array(594, session('permissions'))) {
                    $dropdown = '
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';
                    if (session('role_id') == 1 || in_array(593, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item edit" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    }
                    if (session('role_id') == 1 || in_array(594, session('permissions'))) {
                        if ($data->status == 0) {
                            $dropdown .= '<button type="button" class="dropdown-item enable" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                        } else {
                            $dropdown .= '<button type="button" class="dropdown-item disable" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
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
            ->make(true);
    }

    public function employee_shift_status(Request $request)
    {
        $id = $request->id;
        $shift = EmployeeShift::find($id);
        if ($request->status == 0) {
            $status = 'Disabled';
        } else {
            $status = 'Enabled';
        }
        $shift->status = $request->status;
        $shift->save();
        return response()->json(['status' => 1, 'success' => 'Shift ' . $status . ' successfully!']);
    }

    public function employee_shift_add(Request $request)
    {
        $shift = new EmployeeShift();
        $shift->name = $request->name;
        $shift->start_time = Carbon::parse($request->start_time)->format("H:i:s");
        $shift->end_time = Carbon::parse($request->end_time)->format("H:i:s");
        $shift->extension_minutes = $request->extension_minutes;
        $shift->save();
        return redirect()->back()->with('success', 'Shift Added Successfully!');
    }

    public function employee_shift_edit(Request $request)
    {
        $shift = EmployeeShift::find($request->shift_id);
        $shift->name = $request->name;
        $shift->start_time = Carbon::parse($request->start_time)->format("H:i:s");
        $shift->end_time = Carbon::parse($request->end_time)->format("H:i:s");
        $shift->extension_minutes = $request->extension_minutes;
        $shift->save();
        return redirect()->back()->with('success', 'Shift Updated Successfully!');
    }

    public function payslip_index(Request $request){
        ActivityTrailController::createActivityTrailLog(Auth::id(),436);
        return view('admin.human_resource.payslip.index');
    }

    public function payslip_list(Request $request){
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 437);
        }
        $payslips = EmployeePayslip::select('id', 'payroll_month','trax_id', 'name', 'designation', 'department', 'hub', 'zone', 'joining_date', 'cnic', 'total_deduction', 'net_salary', 'iban','total_salary');
        if(!in_array(596, session('permissions'))){
            $payslips->whereRaw('false');
        }

        $datatable = Datatables::of($payslips)
            ->addColumn('action', function () {
                $dropdown = '
                <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                $dropdown .= '<button type="button" class="dropdown-item print" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Print</div></button>';

                return $dropdown;
            });

        if ($request->get('search_payslip_month')) {
            $month = $request->get('search_payslip_month');
            $from = Carbon::parse($month)->startOfMonth()->toDateString();
            $to = Carbon::parse($month)->endOfMonth()->toDateString();
            $datatable->whereBetween('employee_payslips.payroll_month', [$from,$to]);
        }
        return $datatable->make(true);

    }
    public function payslip_excel_upload(Request $request){

        $payroll_month = $request->payslip_month_formatted;
        if(!$payroll_month){
            return redirect()->back()->with('error', 'Payslip month not selected!');
        }

        Validator::extend('check_trax_id', function ($attribute, $value, $parameters, $validator) {

            if ($value) {
                $result = false;
                if(Admin::where('trax_id', $value)->exists()){
                    $result = true;
                }
                else{
                    if(Rider::where('trax_id', $value)->exists()){
                        $result = true;
                    }
                }
                if ($result) {
                    return true;
                } else {
                    return false;
                }
            }
        });
        $names = [
            'trax_id' => 'Employee ID',
            'name' => 'Employee Name',
            'designation' => 'Designation',
            'department' => 'Department',
            'hub' => 'Hub',
            'zone' => 'Zone',
            'joining_date' => 'Date of Joining',
            'cnic' => 'Cnic',
            'employee_status' => 'Employee Status',
            'payroll_days' => 'Payroll Days',
            'present_days' => 'Present Days',
            'pay_cut_days' => 'Pay Cut Days',
            'absent_days' => 'Absent Days',
            'extra_paid_days' => 'Extra Paid Days',
            'fuel_days' => 'Fuel Days',
            'basic_salary' => 'Basic Salary',
            'house_rent' => 'House Rent',
            'medical' => 'Medical',
            'gross_salary' => 'Gross Salary',
            'mobile_allowance' => 'Mobile Allowance',
            'vehicle_allowance' => 'Vehicle Allowance',
            'fuel_allowance' => 'Fuel Allowance',
            'conveyance_allowance' => 'Conveyance Allowance',
            'vehicle_maintenance' => 'Vehicle Maintenance',
            'fixed_incentive' => 'Fixed Incentive',
            'holiday_allowance' => 'Sunday / Holiday Allowance',
            'overtime' => 'Overtime',
            'bonus' => 'Bonus',
            'arrears' => 'Arrears',
            'pickup_incentive' => 'Pickup Incentive',
            'delivery_incentive' => 'Delivery Incentive',
            'operation_incentive' => 'Operations Incentive',
            'extra_duty_allowance' => 'Extra Duty Allowance',
            'others_addition' => 'Others Addition',
            'total_salary' => 'Total Salary',
            'paycut' => 'Pay Cut',
            'absent' => 'Absent',
            'late_deduction' => 'Late Deduction',
            'income_tax' => 'Income Tax',
            'eobi' => 'EOBI',
            'advance_salary' => 'Advance Salary',
            'month_closing' => 'Month Closing',
            'loan' => 'Loan',
            'fuel_card' => 'Fuel Card',
            'open_parcel' => 'Open Parcel',
            'phone_call' => 'Phone Call',
            'recovery' => 'Recovery',
            'auction_sale' => 'Auction Sale',
            'penalty' => 'Penalty',
            'others_deduction' => 'Others Deduction',
            'van_deduction' => 'Van Deduction',
            'medical_insurance' => 'Medical Insurance',
            'total_deduction' => 'Total Deduction',
            'net_salary' => 'Net Salary',
            'iban' => 'IBAN',
            'employee_type' => 'Employee Type',
            'confirmation_date' => 'Confirmation Date'
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'exists' => 'Given :attribute is Invalid.',
            'check_trax_id' => 'Employee id not found!',

        ];
        $rules = [
            'trax_id' => ['required', 'between:1,100','check_trax_id'],
            'name' => ['required', 'between:1,100'],
            'designation' => ['required', 'between:1,100'],
            'department' => ['required', 'between:1,100'],
            'hub' => ['required', 'between:1,100'],
            'zone' => ['nullable', 'between:1,100'],
            'joining_date' => ['required', 'date_format:Y-m-d'],
            'confirmation_date' => ['nullable', 'date_format:Y-m-d'],
            'cnic' => ['required', 'between:1,100'],
            'employee_status' => ['nullable', 'string','between:1,100'],
            'employee_type' => ['nullable','string','between:1,100'],
            'payroll_days' => ['nullable', 'integer'],
            'present_days' => ['nullable', 'integer'],
            'pay_cut_days' => ['nullable', 'integer'],
            'absent_days' => ['nullable', 'integer'],
            'extra_paid_days' => ['nullable', 'integer'],
            'fuel_days' => ['nullable', 'integer'],
            'basic_salary' => ['required', 'integer'],
            'house_rent' => ['nullable', 'integer'],
            'medical' => ['nullable', 'integer'],
            'gross_salary' => ['nullable', 'integer'],
            'mobile_allowance' => ['nullable', 'integer'],
            'vehicle_allowance' => ['nullable', 'integer'],
            'fuel_allowance' => ['nullable', 'integer'],
            'conveyance_allowance' => ['nullable', 'integer'],
            'vehicle_maintenance' => ['nullable', 'integer'],
            'fixed_incentive' => ['nullable', 'integer'],
            'holiday_allowance' => ['nullable', 'integer'],
            'overtime' => ['nullable', 'integer'],
            'bonus' => ['nullable', 'integer'],
            'arrears' => ['nullable', 'integer'],
            'pickup_incentive' => ['nullable', 'integer'],
            'delivery_incentive' => ['nullable', 'integer'],
            'operation_incentive' => ['nullable', 'integer'],
            'extra_duty_allowance' => ['nullable', 'integer'],
            'others_addition' => ['nullable', 'integer'],
            'total_salary' => ['required', 'integer'],
            'paycut' => ['nullable', 'integer'],
            'absent' => ['nullable', 'integer'],
            'late_deduction' => ['nullable', 'integer'],
            'income_tax' => ['nullable', 'integer'],
            'eobi' => ['nullable', 'integer'],
            'advance_salary' => ['nullable', 'integer'],
            'month_closing' => ['nullable', 'integer'],
            'loan' => ['nullable', 'integer'],
            'fuel_card' => ['nullable', 'integer'],
            'open_parcel' => ['nullable', 'integer'],
            'phone_call' => ['nullable', 'integer'],
            'recovery' => ['nullable', 'integer'],
            'auction_sale' => ['nullable', 'integer'],
            'penalty' => ['nullable', 'integer'],
            'others_deduction' => ['nullable', 'integer'],
            'van_deduction' => ['nullable', 'integer'],
            'medical_insurance' => ['nullable', 'integer'],
            'total_deduction' => ['nullable', 'integer'],
            'net_salary' => ['nullable', 'integer'],
            'iban' => ['nullable','string'],

        ];


        $fields = [0 => 'trax_id', 1 => 'name', 2 => 'designation', 3 => 'department', 4 => 'hub', 5 => 'zone', 6 => 'joining_date', 7 => 'cnic', 8 => 'employee_status', 9 => 'payroll_days', 10 => 'present_days', 11 => 'pay_cut_days', 12 => 'absent_days', 13 => 'extra_paid_days', 14 => 'fuel_days', 15 => 'basic_salary', 16 => 'house_rent', 17 => 'medical', 18 => 'gross_salary', 19 => 'mobile_allowance', 20 => 'vehicle_allowance', 21 => 'fuel_allowance', 22 => 'conveyance_allowance', 23 => 'vehicle_maintenance', 24 => 'fixed_incentive', 25 => 'holiday_allowance', 26 => 'overtime', 27 => 'bonus', 28 => 'arrears', 29 => 'pickup_incentive', 30 => 'delivery_incentive', 31 => 'operation_incentive', 32 => 'extra_duty_allowance', 33 => 'others_addition', 34 => 'total_salary', 35 => 'paycut', 36 => 'absent', 37 => 'late_deduction', 38 => 'income_tax', 39 => 'eobi', 40 => 'advance_salary', 41 => 'month_closing', 42 => 'loan', 43 => 'fuel_card', 44 => 'open_parcel', 45 => 'phone_call', 46 => 'recovery', 47 => 'auction_sale', 48 => 'penalty', 49 => 'others_deduction', 50 => 'van_deduction', 51 => 'medical_insurance', 52 => 'total_deduction', 53 => 'net_salary', 54 => 'iban', 55 => 'confirmation_date', 56 => 'employee_type'];
        if ($file = $request->file('payslip')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Employee ID','Employee Name','Designation','Department', 'Hub', 'Zone', 'Date of Joining', 'CNIC', 'Employee Status', 'Payroll Days', 'Present Days', 'Pay Cut Days', 'Absent Days', 'Extra Paid Days', 'Fuel Days', 'Basic Salary', 'House Rent', 'Medical', 'Gross Salary', 'Mobile Allowance', 'Vehicle Allowance', 'Fuel Allowance', 'Conveyance Allowance', 'Vehicle Maintenance', 'Fixed Incentive', 'Sunday / Holiday Allowance', 'Overtime', 'Bonus', 'Arrears', 'Pickup Incentive', 'Delivery Incentive', 'Operations Incentive', 'Extra Duty Allowance', 'Others Addition', 'Total Salary', 'Pay Cut', 'Absent', 'Late Deduction', 'Income Tax', 'EOBI', 'Advance Salary', 'Month Closing', 'Loan', 'Fuel Card', 'Open Parcel', 'Phone Call', 'Recovery', 'Auction Sale', 'Penalty', 'Others Deduction', 'Van Deduction', 'Medical Insurance', 'Total Deduction', 'Net Salary', 'IBAN', 'Confirmation Date', 'Employee Type'];

            if (isset($spreadsheet)) {
                $header_correct = true;

                foreach ($spreadsheet[0] as $index => $header_value) {
                    if ($index == 54) {
                    } elseif (!isset($header[$index]) || $header_value != $header[$index]) {
                        $header_correct = false;
                        break;
                    }
                }

                if (!$header_correct) {
                    return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
                } else {
                    unset($spreadsheet[0]);
                }
            }

            if (!empty($spreadsheet) || !isset($spreadsheet)) {
                $rows = array();
                foreach ($spreadsheet as $spreadsheet_row) {
                    $row = array();

                    foreach ($spreadsheet_row as $key => $value) {
                        $row[$fields[$key]] = $value;
                    }

                    $rows[] = $row;
                }

                unset($spreadsheet);
                $errors = array();

                foreach ($rows as $key => $row) {
                    $row_id = $key + 2;

                    $validate = Validator::make($row, $rules, $messages);

                    $validate->setAttributeNames($names);

                    if ($validate->fails()) {
                        $errors['Row #' . $row_id] = $validate->errors()->all();
                    }
                }
                if (empty($errors)) {
                    $updated = 0;
                    $not_updated = 0;

                    $payroll_cut_off_date = Carbon::parse($payroll_month)->startOfMonth()->addDays(24)->toDateString();

                    foreach ($rows as $key => $row) {
                        $payslip = new EmployeePayslip();
                        $payslip->payroll_month = $payroll_month;
                        $payslip->payroll_cut_off_date = $payroll_cut_off_date;
                        $payslip->trax_id = trim($row['trax_id']);
                        $payslip->name = trim($row['name']);
                        $payslip->designation = trim($row['designation']);
                        $payslip->department = trim($row['department']);
                        $payslip->hub = trim($row['hub']);
                        $payslip->zone = trim($row['zone']);
                        $payslip->joining_date = trim($row['joining_date']);
                        $payslip->confirmation_date = trim($row['confirmation_date']);
                        $payslip->cnic = trim($row['cnic']);
                        $payslip->employee_status = trim($row['employee_status']);
                        $payslip->employee_type = trim($row['employee_type']);
                        $payslip->payroll_days = trim($row['payroll_days']);
                        $payslip->present_days = trim($row['present_days']);
                        $payslip->pay_cut_days = trim($row['pay_cut_days']);
                        $payslip->absent_days = trim($row['absent_days']);
                        $payslip->extra_paid_days = trim($row['extra_paid_days']);
                        $payslip->fuel_days = trim($row['fuel_days']);
                        $payslip->basic_salary = trim($row['basic_salary']);
                        $payslip->house_rent = trim($row['house_rent']);
                        $payslip->medical = trim($row['medical']);
                        $payslip->gross_salary = trim($row['gross_salary']);
                        $payslip->mobile_allowance = trim($row['mobile_allowance']);
                        $payslip->vehicle_allowance = trim($row['vehicle_allowance']);
                        $payslip->fuel_allowance = trim($row['fuel_allowance']);
                        $payslip->conveyance_allowance = trim($row['conveyance_allowance']);
                        $payslip->vehicle_maintenance = trim($row['vehicle_maintenance']);
                        $payslip->fixed_incentive = trim($row['fixed_incentive']);
                        $payslip->holiday_allowance = trim($row['holiday_allowance']);
                        $payslip->overtime = trim($row['overtime']);
                        $payslip->bonus = trim($row['bonus']);
                        $payslip->arrears = trim($row['arrears']);
                        $payslip->pickup_incentive = trim($row['pickup_incentive']);
                        $payslip->delivery_incentive = trim($row['delivery_incentive']);
                        $payslip->operation_incentive = trim($row['operation_incentive']);
                        $payslip->extra_duty_allowance = trim($row['extra_duty_allowance']);
                        $payslip->others_addition = trim($row['others_addition']);
                        $payslip->total_salary = trim($row['total_salary']);
                        $payslip->paycut = trim($row['paycut']);
                        $payslip->absent = trim($row['absent']);
                        $payslip->late_deduction = trim($row['late_deduction']);
                        $payslip->income_tax = trim($row['income_tax']);
                        $payslip->eobi = trim($row['eobi']);
                        $payslip->advance_salary = trim($row['advance_salary']);
                        $payslip->month_closing = trim($row['month_closing']);
                        $payslip->loan = trim($row['loan']);
                        $payslip->fuel_card = trim($row['fuel_card']);
                        $payslip->open_parcel = trim($row['open_parcel']);
                        $payslip->phone_call = trim($row['phone_call']);
                        $payslip->recovery = trim($row['recovery']);
                        $payslip->auction_sale = trim($row['auction_sale']);
                        $payslip->penalty = trim($row['penalty']);
                        $payslip->others_deduction = trim($row['others_deduction']);
                        $payslip->van_deduction = trim($row['van_deduction']);
                        $payslip->medical_insurance = trim($row['medical_insurance']);
                        $payslip->total_deduction = trim($row['total_deduction']);
                        $payslip->net_salary = trim($row['net_salary']);
                        $payslip->iban = trim($row['iban']);
                        $payslip->added_by = Auth::id();
                        $payslip->save();
                        $updated++;
                    }
                    $error_msg = '';
                    if ($not_updated > 1) {
                        $error_msg = 'Total ' . $not_updated . ' rows could not updated!';
                    }

                    return redirect()->back()->with(['success' => 'Total ' . $updated . ' rows updated', 'error' => $error_msg]);
                } else {
                    $errors = array_map(function ($row, $errors) {
                        return $row . ':' . PHP_EOL . implode(' | ', $errors);
                    }, array_keys($errors), $errors);

                    return redirect()->back()->withErrors($errors);
                }

            } else {
                return redirect()->back()->with('error', 'No Records in File');
            }


        }

    }

    public function payslip_print(Request $request){

        $payslip = EmployeePayslip::find($request->payslip_id);

        if(!$payslip){
            return response()->json(['status' => 0, 'error' => 'Payslip not found!']);
        }
        $payroll_month = Carbon::parse($payslip->payroll_month)->format('F Y');
        $payroll_cut_off_date = Carbon::parse($payslip->payroll_cut_off_date)->toDateString();
        $personal_contact = '';

        if(Admin::where('trax_id', $payslip->trax_id)->exists()){
            $user = Admin::where('trax_id', $payslip->trax_id)->first();
            $personal_contact = $user->phone_number;
        }else{
            $rider = Rider::where('trax_id', $payslip->trax_id);
            if($rider->exists()){
                $rider = $rider->first();
                $personal_contact = $rider->phone;
            }
        }

        $basic_salary = ($payslip->basic_salary != NULL) ? number_format($payslip->basic_salary) :'-';
        $house_rent = ($payslip->house_rent != NULL) ? number_format($payslip->house_rent) :'-';
        $medical = ($payslip->medical != NULL) ? number_format($payslip->medical) :'-';
        $gross_salary = ($payslip->gross_salary != NULL) ? number_format($payslip->gross_salary) :'-';
        $payroll_days = ($payslip->payroll_days != NULL) ? $payslip->payroll_days:'-';
        $present_days = ($payslip->present_days != NULL) ? $payslip->present_days:'-';
        $absent_days = ($payslip->absent_days != NULL) ? $payslip->absent_days:'-';
        $pay_cut_days = ($payslip->pay_cut_days != NULL) ? $payslip->pay_cut_days:'-';
        $extra_paid_days = ($payslip->extra_paid_days != NULL) ? $payslip->extra_paid_days:'-';
        $fuel_days = ($payslip->fuel_days != NULL) ? $payslip->fuel_days:'-';


        $mobile_allowance = ($payslip->mobile_allowance != NULL) ? number_format($payslip->mobile_allowance) : '-';
        $vehicle_allowance = ($payslip->vehicle_allowance != NULL) ? number_format($payslip->vehicle_allowance) : '-';
        $fuel_allowance = ($payslip->fuel_allowance != NULL) ? number_format($payslip->fuel_allowance) : '-';
        $conveyance_allowance = ($payslip->conveyance_allowance != NULL) ? number_format($payslip->conveyance_allowance) : '-';
        $vehicle_maintenance = ($payslip->vehicle_maintenance != NULL) ? number_format($payslip->vehicle_maintenance) : '-';
        $fixed_incentive = ($payslip->fixed_incentive != NULL) ? number_format($payslip->fixed_incentive) : '-';
        $holiday_allowance = ($payslip->holiday_allowance != NULL) ? number_format($payslip->holiday_allowance) : '-';
        $overtime = ($payslip->overtime != NULL) ? number_format($payslip->overtime) : '-';
        $bonus = ($payslip->bonus != NULL) ? number_format($payslip->bonus) : '-';
        $arrears = ($payslip->arrears != NULL) ? number_format($payslip->arrears) : '-';
        $pickup_incentive = ($payslip->pickup_incentive != NULL) ? number_format($payslip->pickup_incentive) : '-';
        $delivery_incentive = ($payslip->delivery_incentive != NULL) ? number_format($payslip->delivery_incentive) : '-';
        $operations_incentive = ($payslip->operation_incentive != NULL) ? number_format($payslip->operation_incentive) : '-';
        $extra_duty_allowance = ($payslip->extra_duty_allowance != NULL) ? number_format($payslip->extra_duty_allowance) : '-';
        $others_addition = ($payslip->others_addition != NULL) ? number_format($payslip->others_addition) : '-';

        $total_addition = ($payslip->total_salary != NULL) ? number_format($payslip->total_salary) : '-';

        $paycut = ($payslip->paycut != NULL) ? number_format($payslip->paycut) : '-';
        $absent = ($payslip->absent != NULL) ? number_format($payslip->absent) : '-';
        $late_deduction = ($payslip->late_deduction != NULL) ? number_format($payslip->late_deduction) : '-';
        $income_tax = ($payslip->income_tax != NULL) ? number_format($payslip->income_tax) : '-';
        $eobi = ($payslip->eobi != NULL) ? number_format($payslip->eobi) : '-';
        $advance_salary = ($payslip->advance_salary != NULL) ? number_format($payslip->advance_salary) : '-';
        $month_closing = ($payslip->month_closing != NULL) ? number_format($payslip->month_closing) : '-';
        $loan = ($payslip->loan != NULL) ? number_format($payslip->loan) : '-';
        $fuel_card = ($payslip->fuel_card != NULL) ? number_format($payslip->fuel_card) : '-';
        $open_parcel = ($payslip->open_parcel != NULL) ? number_format($payslip->open_parcel) : '-';
        $phone_call = ($payslip->phone_call != NULL) ? number_format($payslip->phone_call) : '-';
        $recovery = ($payslip->recovery != NULL) ? number_format($payslip->recovery) : '-';
        $auction_sale = ($payslip->auction_sale != NULL) ? number_format($payslip->auction_sale) : '-';
        $penalty = ($payslip->penalty != NULL) ? number_format($payslip->penalty) : '-';
        $van_deduction = ($payslip->van_deduction != NULL) ? number_format($payslip->van_deduction) : '-';
        $others_deduction = ($payslip->others_deduction != NULL) ? number_format($payslip->others_deduction) : '-';

        $total_deduction = ($payslip->total_deduction != NULL) ? number_format($payslip->total_deduction) : '-';
        $net_salary = ($payslip->net_salary != NULL) ? number_format($payslip->net_salary) : '-';

        $html = '<!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Payslip</title>

                     <style>
                     @page {
                        size: A4 portrait;
                      }
                      body {
                        font-size: 0.95rem !important;
                        
                      }
                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
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
                      
                      .table-borderless td, .table th {
                        border: none;
                     }
                     td{
                        color: #000;
                     }
                    </style>';

        $html .= '</head>
                  <body>
                   
                      <div class="table-responsive">
                          <table class="table table-borderless mb-0">
                          
                          <tbody>
                            <tr>
                              <td class="text-left align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class=""></td>
                       
                                 <td class="text-right align-middle"><h1 class="d-block">SALARY SLIP</h1></td>
                             </tr>
                             <tr>
                                <td class="text-left align-middle">Head Office (Karachi): </td>
                                <td class="text-right align-middle"><b>Payroll Month: </b><u>'. $payroll_month .'</u></td>
                             </tr>
                             <tr>
                                <td class="text-left align-middle">Plot 105, Sector 7-A, Mehran Town, Korangi, Karachi.</td>
                                <td class="text-right align-middle"><b>Payroll Cut Off Date: </b> <u>'. $payroll_cut_off_date .'</u></td>
                                
                             </tr>
                             </tbody>
                         </table>';

        $html .= '<table class="table border table-sm">
                    
                    <tbody>
                        <tr class="text-center">
                            <td class="color primary border twice" colspan="8"><b>Employee Information</b></td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Employee ID</td>
                            <td colspan="2"  class="border twice-right">'. $payslip->trax_id .'</td>
                            <td colspan="2"  class="border twice-right">Date of Joining</td>
                            <td colspan="2"  class="border twice-right">'. $payslip->joining_date .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Employee Name</td>
                            <td colspan="2"  class="border twice-right">'. $payslip->name .'</td>
                            <td colspan="2"  class="border twice-right">Date of Confirmation</td>
                            <td colspan="2"  class="border twice-right">'. $payslip->confirmation_date .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Designation</td>
                            <td colspan="2"  class="border twice-right">'. $payslip->designation .'</td>
                            <td colspan="2"  class="border twice-right">Employee Type</td>
                            <td colspan="2"  class="border twice-right">'. $payslip->employee_type .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Department</td>
                            <td colspan="2"  class="border twice-right">'. $payslip->department .'</td>
                            <td colspan="2"  class="border twice-right">Employee Status</td>
                            <td colspan="2"  class="border twice-right">'. $payslip->employee_status .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Location</td>
                            <td colspan="2"  class="border twice-right">'. $payslip->hub .'</td>
                            <td colspan="2"  class="border twice-right">Personal Contact #</td>
                            <td colspan="2"  class="border twice-right">'. $personal_contact .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">CNIC No.</td>
                            <td colspan="2"  class="border twice-right">'. $payslip->cnic .'</td>
                            <td colspan="2"  class="border twice-right">Bank Account No.</td>
                            <td colspan="2"  class="border twice-right">'. $payslip->iban .'</td>
                        </tr>
                        <tr class="text-center">
                            <td class="color primary border twice" colspan="8"><b>Salary Breakup</b></td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Basic Salary</td>
                            <td colspan="2"  class="border twice-right">'. $basic_salary .'</td>
                            <td colspan="1"  class="border twice-right">Payroll Days</td>
                            <td colspan="1"  class="border twice-right">'. $payroll_days .'</td>
                            <td colspan="1"  class="border twice-right">Absent Days</td>
                            <td colspan="1"  class="border twice-right">'. $absent_days .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">House Rent</td>
                            <td colspan="2"  class="border twice-right">'. $house_rent .'</td>
                            <td colspan="1"  class="border twice-right">Present Days</td>
                            <td colspan="1"  class="border twice-right">'. $present_days .'</td>
                            <td colspan="1"  class="border twice-right">Extra Paid Days</td>
                            <td colspan="1"  class="border twice-right">'. $extra_paid_days .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Medical</td>
                            <td colspan="2"  class="border twice-right">'. $medical .'</td>
                            <td colspan="1"  class="border twice-right">Pay Cut Days</td>
                            <td colspan="1"  class="border twice-right">'. $pay_cut_days .'</td>
                            <td colspan="1"  class="border twice-right">Fuel Days</td>
                            <td colspan="1"  class="border twice-right">'. $fuel_days .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right"><b>Gross Salary</b></td>
                            <td colspan="2"  class="border twice-right">'. $gross_salary .'</td>
                            <td colspan="4"  class="border twice-right"></td>
                        </tr>
                        <tr class="text-center">
                            <td class="color primary border twice" colspan="4"><b>Addition</b></td>
                            <td class="color primary border twice" colspan="4"><b>Deduction</b></td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Mobile Allowance</td>
                            <td colspan="2"  class="border twice-right">'. $mobile_allowance .'</td>
                            <td colspan="2"  class="border twice-right">Pay Cut</td>
                            <td colspan="2"  class="border twice-right">'. $paycut .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Vehicle Allowance</td>
                            <td colspan="2"  class="border twice-right">'. $vehicle_allowance .'</td>
                            <td colspan="2"  class="border twice-right">Absent</td>
                            <td colspan="2"  class="border twice-right">'. $absent .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Fuel Allowance</td>
                            <td colspan="2"  class="border twice-right">'. $fuel_allowance .'</td>
                            <td colspan="2"  class="border twice-right">Late Deduction</td>
                            <td colspan="2"  class="border twice-right">'. $late_deduction .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Conveyance Allowance</td>
                            <td colspan="2"  class="border twice-right">'. $conveyance_allowance .'</td>
                            <td colspan="2"  class="border twice-right">Income Tax</td>
                            <td colspan="2"  class="border twice-right">'. $income_tax .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Vehicle Maintenance</td>
                            <td colspan="2"  class="border twice-right">'. $vehicle_maintenance .'</td>
                            <td colspan="2"  class="border twice-right">EOBI</td>
                            <td colspan="2"  class="border twice-right">'. $eobi .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Fixed Incentive</td>
                            <td colspan="2"  class="border twice-right">'. $fixed_incentive .'</td>
                            <td colspan="2"  class="border twice-right">Advance Salary</td>
                            <td colspan="2"  class="border twice-right">'. $advance_salary .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Sunday / Holiday Allowance</td>
                            <td colspan="2"  class="border twice-right">'. $holiday_allowance .'</td>
                            <td colspan="2"  class="border twice-right">Month Closing</td>
                            <td colspan="2"  class="border twice-right">'. $month_closing .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Overtime</td>
                            <td colspan="2"  class="border twice-right">'. $overtime .'</td>
                            <td colspan="2"  class="border twice-right">Loan</td>
                            <td colspan="2"  class="border twice-right">'. $loan .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Bonus</td>
                            <td colspan="2"  class="border twice-right">'. $bonus .'</td>
                            <td colspan="2"  class="border twice-right">Fuel Card</td>
                            <td colspan="2"  class="border twice-right">'. $fuel_card .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Arrears</td>
                            <td colspan="2"  class="border twice-right">'. $arrears .'</td>
                            <td colspan="2"  class="border twice-right">Open Parcel</td>
                            <td colspan="2"  class="border twice-right">'. $open_parcel .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Pickup Incentive</td>
                            <td colspan="2"  class="border twice-right">'. $pickup_incentive .'</td>
                            <td colspan="2"  class="border twice-right">Phone Call</td>
                            <td colspan="2"  class="border twice-right">'. $phone_call .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Delivery Incentive</td>
                            <td colspan="2"  class="border twice-right">'. $delivery_incentive .'</td>
                            <td colspan="2"  class="border twice-right">Recovery</td>
                            <td colspan="2"  class="border twice-right">'. $recovery .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Operations Incentive</td>
                            <td colspan="2"  class="border twice-right">'. $operations_incentive .'</td>
                            <td colspan="2"  class="border twice-right">Auction Sale</td>
                            <td colspan="2"  class="border twice-right">'. $auction_sale .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Extra Duty Allowance</td>
                            <td colspan="2"  class="border twice-right">'. $extra_duty_allowance .'</td>
                            <td colspan="2"  class="border twice-right">Penalty</td>
                            <td colspan="2"  class="border twice-right">'. $penalty .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Others Addition</td>
                            <td colspan="2"  class="border twice-right">'. $others_addition .'</td>
                            <td colspan="2"  class="border twice-right">Van Deduction</td>
                            <td colspan="2"  class="border twice-right">'. $van_deduction .'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right"></td>
                            <td colspan="2"  class="border twice-right"></td>
                            <td colspan="2"  class="border twice-right">Others Deduction</td>
                            <td colspan="2"  class="border twice-right">'. $others_deduction .'</td>
                        </tr>
                        
                        <tr class="text-center">
                            <td class="color primary border twice" colspan="2"><b>Total Addition</b></td>
                            <td class="color primary border twice" colspan="2">'. $total_addition .'</td>
                            <td class="color primary border twice" colspan="2"><b>Total Deduction</b></td>
                            <td class="color primary border twice" colspan="2">'. $total_deduction .'</td>
                        </tr>
                        <tr class="text-left">
                            <td class="color primary border twice" colspan="6"><b>Net Salary</b></td>
                            <td class="color primary border twice text-center" colspan="2">'. $net_salary .'</td>
                        </tr>
                        <tr class="text-left">
                            <td class="border twice" colspan="8" rowspan="5"><i>Note: This is a system generated document and does not require any signature.</i></td>
                        </tr>
                   </tbody>
                         </table>';


        $html .=  ' 
                      </div>
                      </body>
                      </html>';

        $pdf = SnappyPDF::loadHTML($html);

        $filename = 'payslip_' . $payslip->id . '.pdf';

        $result = $pdf->download($filename);
        $pdf_file = 'data:application/pdf;base64,' . base64_encode($result);
        return array('status' => 1, 'image' => $pdf_file);

    }
}
