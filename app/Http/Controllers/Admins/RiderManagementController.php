<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\OperationRidersCategory;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\RiderType;
use App\Http\Models\City;
use App\Http\Models\EmployeeConvertHistory;
use App\Http\Models\EmployeeShift;
use App\Http\Models\HR\Employee;
use App\Http\Models\ReportingLocation;
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
use App\RiderMainCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Rider\RiderRemark;
use App\Http\Models\Rider\RiderRemarksResponse;
use App\Http\Models\Rider\RiderRemarkStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use DB;

class RiderManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function permanent_index()
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 58);
        $category = RiderCategory::all();
        $main_category = RiderMAinCategory::all();
        return view('admin.management.riders.permanent_index')->with(['categories' => $category, 'main_categories' => $main_category]);
    }

    public function permanent_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 118);
        }
        $rider = Rider::join('cities', 'riders.city_id', '=', 'cities.id')
            ->join('cities as c', 'cities.hub_id', '=', 'c.id')
            ->leftjoin('zones as z', 'cities.zone_id', '=', 'z.id')
            ->leftjoin('routes', 'routes.id', '=', 'riders.route_id')
            ->leftjoin('rider_main_categories', 'riders.rider_main_category_id', '=', 'rider_main_categories.id')
            ->join('rider_categories', 'rider_categories.id', '=', 'riders.rider_category_id')
            ->leftjoin('admins as cb', 'cb.id', '=', 'riders.created_by')
            ->leftjoin('admins as ub', 'ub.id', '=', 'riders.updated_by')
            ->leftjoin('employees as emp', 'emp.trax_id', '=', 'riders.trax_id')
            ->select('cities.name as city', 'c.name as hub', 'z.name as zone', 'riders.id as rider_id', 'riders.id', 'riders.name as rider', 'riders.trax_id', 'riders.phone', 'riders.cnic', 'riders.address', 'routes.code as route', 'routes.start', 'routes.end', 'rider_categories.name as category', 'rider_main_categories.name as main_category', 'riders.status as status', 'riders.created_at as created_at', 'cb.name as created_by', 'ub.name as updated_by', 'riders.rider_type_id', 'riders.blacklist', 'riders.updated_at', 'emp.first_inactive', 'riders.incentive_amount')
            ->where('riders.rider_type_id', 1)
            ->where('riders.blacklist', 0);
        if (session('role_id') != 1) {
            $rider = $rider->whereIn('cities.hub_id', session('hubs'));
        }


        return Datatables::of($rider)
            ->editColumn('status', function ($rider) {
                return ($rider->status == 0) ? 'Inactive' : 'Active';
            })
            ->editColumn('trax_id', function ($rider) {
                if ($rider->trax_id != null) {
                    return $rider->trax_id;
                } else {
                    return '-';
                }
            })->editColumn('main_category', function ($rider) {
                if ($rider->main_category != null) {
                    return $rider->main_category;
                } else {
                    return '-';
                }
            })
            ->editColumn('route', function ($rider) {
                return $rider->route . ' (' . $rider->start . ' to ' . $rider->end . ')';
            })
            ->filterColumn('route', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%' . $keyword . '%')->orWhere('routes.start', 'like', '%' . $keyword . '%')->orWhere('routes.end', 'like', '%' . $keyword . '%');
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($rider) {
                if ((session('role_id') == 1 || count(array_intersect([98, 99, 381, 382, 620], session('permissions'))) !== 0) && (EmployeeConvertHistory::where('rider_id', $rider->rider_id)->doesntExist())) {
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
                        } else {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $rider->id . '  rel="riderActive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Rider</div></button>';
                        }
                    }
                    if (session('role_id') == 1 || in_array(381, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item incentive" data-target-id=' . $rider->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Make Rider Incentive</div></button>';
                    }
                    if (session('role_id') == 1 || in_array(382, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item blacklist" data-target-id=' . $rider->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Blacklist</div></button>';
                    }


                    if (session('role_id') == 1 || in_array(620, session('permissions'))) {
                        if ($rider->status == 0 && $rider->first_inactive == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item rejoin" data-target-id=' . $rider->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Rejoin Rider</div></button>';
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
            // ->addColumn('zone', function ($rider) {
            //     return $rider->city;
            // })
            ->make(true);
    }

    public function addRiderView($type)
    {
        $city = City::where('business_category_id', 1)->where('status', 1)->select(['id', 'name'])->get();
        $category = RiderCategory::all();
        $main_category = RiderMainCategory::all();
        $route_types = RouteType::all();
        $operation_riders = OperationRidersCategory::all();
        $shifts = EmployeeShift::where('status', 1)->get();
        $reporting_locations = ReportingLocation::where('status', 1)->get();

        return view('admin.management.add_rider_form')->with(['cities' => $city, 'categories' => $category, 'route_types' => $route_types, 'cities' => $city, 'operation_riders' => $operation_riders, 'type' => $type, 'shifts' => $shifts, 'main_category' => $main_category, 'reporting_locations' => $reporting_locations]);
    }
    public function addRiderDetails(Request $request)
    {

        $type = $request->rider_type;
        $validations = [
            'city_id' => 'required|numeric',
            'rider_name' => 'required|max:255',
            'phone' => 'required|max:255',
            'cnic' => 'required|max:255',
            'address' => 'required|max:255',
            'route_id' => 'required',
            'operation_rider_id' => 'required',
            'rider_category' => 'required|numeric',
            'rider_main_category' => 'required|numeric',
            'pin' => 'required|numeric',
        ];
        $validate = Validator::make($request->all(), $validations);
        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        if (Rider::where('cnic', $request->cnic)->exists()) {
            return redirect()->back()->with('error', 'Rider with this CNIC already exist!');
        }
        $route_id = null;
        if ($request->route_id == 'other') {
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
        } else {
            $route_id = $request->route_id;
        }
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
        Rider::where('route_id', $request->route_id)->update(['route_id' => NULL]);
        $rider = Rider::create([
            'city_id' => $request->city_id,
            'name' => $request->rider_name,
            'phone' => $request->phone,
            'cnic' => $request->cnic,
            'address' => $request->address,
            'route_id' => $route_id,
            'rider_main_category_id' => $request->rider_main_category,
            'rider_category_id' => $request->rider_category,
            'operation_rider_id' => $request->operation_rider_id,
            'status' => 1,
            'special_rider' => ($request->has('special_rider_checkbox') ? 1 : 0),
            'ccd' => ($request->has('ccd_rider_checkbox') ? 1 : 0),
            'pin' => bcrypt($request->pin),
            'dummy_pin' => $request->pin,
            'created_by' => Auth::id(),
            'trax_id' => $trax_id,
            'rider_type_id' => $type,
            'shift_id' => 1,
            'incentive_amount' => $request->incentive_amount,
        ]);
        if ($rider) {
            $employee = new Employee();
            $employee->city_id = $request->city_id;
            $employee->name = $request->rider_name;
            $employee->phone_number = $request->phone;
            $employee->cnic = $request->cnic;
            $employee->employee_type_id = 2;
            $employee->request_status_id = 3;
            $employee->status_id = 3;
            $employee->address = $request->address;
            $employee->pin =  $request->pin;
            $employee->shift_id = 1;
            $employee->department_id = 6;
            $employee->trax_id = $trax_id;
            $employee->rider_main_category = $request->rider_main_category;
            $employee->rider_sub_category = $request->rider_category;
            $employee->rider_type_id = $type;
            $employee->save();

            $rider->employee_id = $employee->id;
            $rider->update();

            NotificationsController::send(61, $rider->id, $request->pin);
            return redirect()->back()->with('success', 'Rider added successfully');
        }
    }
    public function categoryListAjax(Request $request)
    {
        $city_id = $request->id;
        $route = Route::select(['id', 'code', 'start', 'end'])->where('city_id', $city_id)->where('status', 1);

        if (session('role_id') != 1) {
            $route = $route->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $route = $route->get();

        return response()->json(['route' => $route]);
    }
    public function editRiderView($id, $type)
    {
        $city = City::where('business_category_id', 1)->where('status', 1)->select(['id', 'name'])->get();
        $category = RiderCategory::all();
        $main_category = RiderMainCategory::all();
        $route_types = RouteType::all();
        $rider = Rider::find($id);
        $route = Route::where('city_id', $rider->city_id)->get();
        $operation_rider_ids =  OperationRidersCategory::all();
        $shifts =  EmployeeShift::where('status', 1)->get();
        $reporting_locations = ReportingLocation::where('status', 1)->get();
        return view('admin.management.edit_rider_form')->with(['rider_id' => $id, 'cities' => $city, 'categories' => $category, 'rider' => $rider, 'routes' => $route, 'route_types' => $route_types, 'operation_rider_ids' => $operation_rider_ids, 'type' => $type, 'shifts' => $shifts, 'main_category' => $main_category, 'reporting_locations' => $reporting_locations]);
    }
    public function editRiderDetails(Request $request, $id)
    {
        $validations = [
            'city_id' => 'required|numeric',
            'rider_name' => 'required|max:255',
            'phone' => 'required|max:255',
            'cnic' => 'required|max:255',
            'address' => 'required|max:255',
            'route_id' => 'required',
            'rider_category' => 'required|numeric',
            'rider_main_category' => 'required|numeric',
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }

        $rider = Rider::find($id);
        if (Rider::where('id',  '<>', $id)->where('cnic', $request->cnic)->exists()) {
            return redirect()->back()->with('error', 'Another Rider with this CNIC already exist!');
        }
        $rider->city_id = $request->city_id;
        $rider->name = $request->rider_name;
        $rider->phone = $request->phone;
        $rider->cnic = $request->cnic;
        $rider->address = $request->address;
        $rider->trax_id = $request->trax_id;
        $rider->incentive_amount = $request->incentive_amount;
        $rider->shift_id = 1;

        $rider->rider_category_id = $request->rider_category;
        $rider->rider_main_category_id = $request->rider_main_category;

        $rider->operation_rider_id = $request->operation_rider_id;

        if ($request->has('special_rider_checkbox')) {
            $rider->special_rider = 1;
        } else {
            $rider->special_rider = 0;
        }

        if ($request->has('edit_ccd_rider_checkbox')) {
            $rider->ccd = 1;
        } else {
            $rider->ccd = 0;
        }

        $rider->updated_by = Auth::id();


        if ($request->route_id == 'other') {
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
        } else {
            Rider::where('route_id', $request->route_id)->where('id', '<>', $id)->update(['route_id' => NULL]);
            $rider->route_id = $request->route_id;
        }
        if ($request->pin != '') {
            if ($rider->dummy_pin != $request->pin) {
                $rider->pin = bcrypt($request->pin);
                $rider->dummy_pin = $request->pin;

                NotificationsController::send(61, $rider->id, $request->pin);
            }
        }


        $rider->save();


        $employee = Employee::where('trax_id', $rider->trax_id)->where('trax_id', '!=', null);
        if ($employee->exists()) {
            $employee = $employee->first();
            $employee->city_id = $rider->city_id;
            $employee->name = $rider->name;
            $employee->phone_number = $rider->phone;
            $employee->cnic = $rider->cnic;
            $employee->address = $rider->address;
            $employee->pin = $rider->dummy_pin;
            $employee->shift_id = $rider->shift_id;

            $employee->rider_main_category = $request->rider_category;
            $employee->rider_sub_category = $request->rider_main_category;
            $employee->save();
        }

        if ($rider) {
            return redirect()->back()->with('success', 'Rider updated successfully');
        }
    }
    public function riderStatus(Request $request)
    {
        $id = $request->cid;
        $status = $request->status;
        if ($status == 'riderActive') {
            $rider = Rider::where('id', $id)->first();
            $rider->status = 1;
            $rider->update();
            $employee = Employee::where('trax_id', $rider->trax_id)->where('trax_id', '!=', null);
            if ($employee->exists()) {
                $employee = $employee->first();
                $employee->status_id = AdminHumanResourseController::GetStatusOfEmployee($employee->id);
                $employee->first_inactive = 1;
                $employee->update();
            }
            if ($rider) {
                return redirect()->back()->with('success', 'Rider is activated successfully');
            }
        } else if ($status == 'riderInactive') {
            $rider = Rider::where('id', $id)->first();
            $rider->status = 0;
            $rider->update();
            $employee = Employee::where('trax_id', $rider->trax_id)->where('trax_id', '!=', null);
            if ($employee->exists()) {
                $employee = $employee->first();
                $employee->status_id = 2;
                $employee->first_inactive = 1;
                $employee->update();
            }
            if ($rider) {
                return redirect()->back()->with('success', 'Rider is now inactive');
            }
        }
    }

    public function rejoin(Request $request)
    {
        $employee_id = $request->employee_id;
        if (!$employee_id) {
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }
        $employee = Rider::find($employee_id);
        if (!$employee) {
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }

        $staff = Employee::where('trax_id', $employee->trax_id)->where('trax_id', '!=', null);

        if ($staff->doesntExist()) {
            return response()->json(['status' => 1, 'error' => 'Rider not associated with any Employee!']);
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
        return response()->json(['status' => 0, 'success' => 'Rider Rejoined Successfully!']);
    }

    public function rider_phone_unique(Request $request)
    {
        if ($request->filled('phone')) {
            if ($request->input('phone') == '0213-8772222') {
                return 'true';
            } else {
                $rider = Rider::where('phone', $request->input('phone'));

                if ($request->has('id')) {
                    $rider = $rider->where('id', '!=', $request->input('id'));
                }

                if (!$rider->exists()) {
                    return 'true';
                } else {
                    return 'false';
                }
            }
        } else {
            return 'true';
        }
    }

    public function rider_incentive(Request $request)
    {
        $rider_id = $request->rider_id;
        if ($rider_id) {
            $rider = Rider::find($rider_id);
            if ($rider) {
                $rider_status = $rider->rider_type_id;
                if ($rider_status == 1) {
                    $rider->rider_type_id = 2;
                    $rider->updated_by = Auth::id();
                    $rider->save();

                    $employee = Employee::where('trax_id', $rider->trax_id)->where('trax_id', '!=', null);
                    if ($employee->exists()) {
                        $employee = $employee->first();
                        $employee->rider_type_id = 2;
                        $employee->update();
                    }
                    return response()->json(['status' => 0, 'success' => 'Rider Marked as Incentive Rider!']);
                }
                return response()->json(['status' => 1, 'error' => 'Rider already Marked as Incentive Rider!']);
            }
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }
    }

    public function rider_permanent(Request $request)
    {
        $rider_id = $request->rider_id;
        if ($rider_id) {
            $rider = Rider::find($rider_id);

            if ($rider) {
                $rider_status = $rider->rider_type_id;
                if ($rider_status == 2) {
                    // $global_setting = GlobalSettings::where('type', 'latest_employee_id');

                    // if ($global_setting->exists()) {
                    //     $global_setting = $global_setting->first();
                    //     $trax_id = $global_setting->setting_value + 1;
                    //     $global_setting->setting_value = $trax_id;
                    //     $global_setting->save();
                    //     $trax_id = 'Trax' . str_pad($trax_id, 5, '0', STR_PAD_LEFT);
                    // } else {
                    //     $trax_id = null;
                    // }

                    $employee = Employee::where('trax_id', $rider->trax_id)->where('trax_id', '!=', null);
                    if ($employee->exists()) {
                        $employee = $employee->first();

                        $employee_id = $employee->id;
                        // $employee->trax_id = $trax_id;
                        // $employee->rider_type_id = 1;
                        // $employee->update();
                        $route = route("admin.human_resource.employee_directory.edit", ['employee' => $employee_id]);
                        return response()->json(['status' => 0, 'route' => $route]);
                    }
                    // $rider->trax_id = $trax_id;
                    // $rider->rider_type_id = 1;
                    // $rider->updated_by = Auth::id();
                    // $rider->save();

                    // return response()->json(['status' => 0, 'success' => 'Rider Marked as Permanent Rider!']);
                }
                return response()->json(['status' => 1, 'error' => 'Rider already Marked as Permanent Rider!']);
            }
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }
    }

    public function rider_blacklist(Request $request)
    {
        $rider_id = $request->rider_id;
        $action = $request->action;
        if (!$rider_id) {
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }

        $rider = Rider::find($rider_id);
        if (!$rider) {
            return response()->json(['status' => 1, 'error' => 'Rider not found!']);
        }

        $employee = Employee::where('trax_id', $rider->trax_id)->where('trax_id', '!=', null);
        if ($employee->doesntExist()) {
        }

        $employee = $employee->first();
        if ($action == 'block') {
            $rider->blacklist = 1;
            $rider->status = 0;
            $rider->updated_by = Auth::id();
            $rider->save();

            $employee->status_id = 2;
            $employee->update();

            return response()->json(['status' => 0, 'success' => 'Rider is blacklisted!']);
        }
        if ($action == 'unblock') {
            $rider->blacklist = 0;
            $rider->status = 1;
            $rider->updated_by = Auth::id();
            $rider->save();

            $employee->status_id = AdminHumanResourseController::GetStatusOfEmployee($employee->id);
            $employee->update();

            return response()->json(['status' => 0, 'success' => 'Rider is Unblocked!']);
        }
    }

    public function send_sms(Request $request)
    {
        $rider_ids = explode(',', $request->selected_riders);
        if (count($rider_ids) > 0) {
            $body = trim($request->get('body'));
            $sms_history = new SmsHistory();
            $sms_history->body = $body;
            $sms_history->sender_id = Auth::id();
            $sms_history->save();
            $sms_history_id = $sms_history->id;
            foreach ($rider_ids as $rider_id) {
                $rider = Rider::find($rider_id);
                if ($rider) {
                    $to = $rider->phone;
                    $sms_history_rider = new SmsHistoryRider();
                    $sms_history_rider->sms_history_id = $sms_history_id;
                    $sms_history_rider->rider_id = $rider_id;
                    $sms_history_rider->save();
                    NotificationsController::custom_sms($body, $to);
                }
            }
            return redirect()->back()->with('success', 'SMS successfully sent!');
        }
    }

    public function incentive_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 59);
        $category = RiderCategory::all();
        $main_category = RiderMainCategory::all();
        return view('admin.management.riders.incentive_index')->with(['categories' => $category, 'main_categories' => $main_category]);
    }

    public function incentive_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 119);
        }
        $rider = Rider::join('cities', 'riders.city_id', '=', 'cities.id')
            ->join('cities as c', 'cities.hub_id', '=', 'c.id')
            ->leftjoin('zones as z', 'cities.zone_id', '=', 'z.id')
            ->leftjoin('routes', 'routes.id', '=', 'riders.route_id')
            ->join('rider_categories', 'rider_categories.id', '=', 'riders.rider_category_id')
            ->leftjoin('rider_main_categories', 'riders.rider_main_category_id', '=', 'rider_main_categories.id')
            ->leftjoin('admins as cb', 'cb.id', '=', 'riders.created_by')
            ->leftjoin('admins as ub', 'ub.id', '=', 'riders.updated_by')
            ->leftjoin('employees as emp', 'emp.trax_id', '=', 'riders.trax_id')
            ->select('cities.name as city', 'c.name as hub', 'z.name as zone', 'riders.id as rider_id', 'riders.id', 'riders.name as rider', 'riders.trax_id', 'riders.phone', 'riders.cnic', 'riders.address', 'routes.code as route', 'routes.start', 'routes.end', 'rider_main_categories.name as main_category', 'rider_categories.name as category', 'riders.status as status', 'riders.created_at', 'cb.name as created_by', 'ub.name as updated_by', 'riders.rider_type_id', 'riders.blacklist', 'riders.updated_at', 'emp.first_inactive')
            ->where('riders.rider_type_id', 2)
            ->where('riders.blacklist', 0);

        if (session('role_id') != 1) {
            $rider = $rider->whereIn('cities.hub_id', session('hubs'));
        }

        return Datatables::of($rider)
            ->editColumn('status', function ($rider) {
                return ($rider->status == 0) ? 'Inactive' : 'Active';
            })
            ->editColumn('trax_id', function ($rider) {
                if ($rider->trax_id != null) {
                    return $rider->trax_id;
                } else {
                    return '-';
                }
            })
            ->editColumn('route', function ($rider) {
                return $rider->route . ' (' . $rider->start . ' to ' . $rider->end . ')';
            })
            ->editColumn('main_category', function ($rider) {
                if ($rider->main_category != null) {
                    return $rider->main_category;
                } else {
                    return '-';
                }
            })
            ->filterColumn('route', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%' . $keyword . '%')->orWhere('routes.start', 'like', '%' . $keyword . '%')->orWhere('routes.end', 'like', '%' . $keyword . '%');
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($rider) {
                if ((session('role_id') == 1 || count(array_intersect([98, 99, 381, 382, 620, 690, 691], session('permissions'))) !== 0) && (EmployeeConvertHistory::where('rider_id', $rider->rider_id)->doesntExist())) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if (session('role_id') == 1 || in_array(98, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item" data-target-id=' . $rider->id . ' rel="editRider" data-toggle="modal" data-target="#editRider"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update Rider</div></button>';
                    }

                    //                    if (session('role_id') == 1 || in_array(99, session('permissions'))) {
                    //                        if ($rider->status == 1) {
                    //                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $rider->id . '  rel="riderInactive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate Rider</div></button>';
                    //                        }
                    //                        else {
                    //                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $rider->id . '  rel="riderActive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Rider</div></button>';
                    //                        }
                    //                    }

                    if (session('role_id') == 1 || in_array(691, session('permissions'))) {
                        if ($rider->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $rider->id . '  rel="riderInactive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate Rider</div></button>';
                        }
                    }
                    if (session('role_id') == 1 || in_array(690, session('permissions'))) {
                        if ($rider->status != 1) {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $rider->id . '  rel="riderActive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Rider</div></button>';
                        }
                    }

                    if (session('role_id') == 1 || in_array(381, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item permanent" data-target-id=' . $rider->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Make Rider Permanent</div></button>';
                    }
                    if (session('role_id') == 1 || in_array(382, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item blacklist" data-target-id=' . $rider->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Blacklist</div></button>';
                    }

                    if (session('role_id') == 1 || in_array(620, session('permissions'))) {
                        if ($rider->status == 0 && $rider->first_inactive == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item rejoin" data-target-id=' . $rider->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Rejoin Rider</div></button>';
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

    public function blacklist_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 350);
        $category = RiderCategory::all();
        $main_category = RiderMainCategory::all();
        return view('admin.management.riders.blacklisted')->with(['categories' => $category, 'main_categories' => $main_category]);
    }
    public function blacklist_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 351);
        }
        $rider = Rider::join('cities', 'riders.city_id', '=', 'cities.id')
            ->join('cities as c', 'cities.hub_id', '=', 'c.id')
            ->leftjoin('zones as z', 'cities.zone_id', '=', 'z.id')
            ->leftjoin('routes', 'routes.id', '=', 'riders.route_id')
            ->join('rider_categories', 'rider_categories.id', '=', 'riders.rider_category_id')
            ->leftjoin('rider_main_categories', 'riders.rider_main_category_id', '=', 'rider_main_categories.id')
            ->leftjoin('admins as cb', 'cb.id', '=', 'riders.created_by')
            ->leftjoin('admins as ub', 'ub.id', '=', 'riders.updated_by')
            ->leftjoin('rider_types as rt', 'rt.id', '=', 'riders.rider_type_id')
            ->select('cities.name as city', 'c.name as hub', 'z.name as zone', 'riders.id as rider_id', 'riders.id', 'riders.name as rider', 'riders.trax_id', 'riders.phone', 'riders.cnic', 'riders.address', 'routes.code as route', 'routes.start', 'routes.end', 'rider_categories.name as category', 'rider_main_categories.name as main_category', 'riders.status as status', 'riders.created_at', 'cb.name as created_by', 'ub.name as updated_by', 'riders.rider_type_id', 'riders.blacklist', 'rt.name as rider_type')
            ->where('riders.blacklist', 1);
        if (session('role_id') != 1) {
            $rider = $rider->whereIn('cities.hub_id', session('hubs'));
        }

        return Datatables::of($rider)
            ->editColumn('status', function ($rider) {
                return ($rider->status == 0) ? 'Inactive' : 'Active';
            })
            ->editColumn('trax_id', function ($rider) {
                if ($rider->trax_id != null) {
                    return $rider->trax_id;
                } else {
                    return '-';
                }
            })
            ->editColumn('main_category', function ($rider) {
                if ($rider->main_category != null) {
                    return $rider->main_category;
                } else {
                    return '-';
                }
            })
            ->editColumn('route', function ($rider) {
                return $rider->route . ' (' . $rider->start . ' to ' . $rider->end . ')';
            })
            ->filterColumn('route', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%' . $keyword . '%')->orWhere('routes.start', 'like', '%' . $keyword . '%')->orWhere('routes.end', 'like', '%' . $keyword . '%');
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($rider) {
                if ((session('role_id') == 1 || count(array_intersect([99, 382], session('permissions'))) !== 0) && (EmployeeConvertHistory::where('rider_id', $rider->rider_id)->doesntExist())) {
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
                } else {
                    return '';
                }
            })
            ->make(true);
    }
    public function sms_history_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 375);
        return view('admin.management.riders.sms_history');
    }
    public function sms_history_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 376);
        }

        $sms = SmsHistory::join('admins', 'admins.id', '=', 'sms_histories.sender_id')
            ->select('sms_histories.id', 'sms_histories.body', 'sms_histories.created_at', 'admins.name as send_by', DB::raw('(SELECT COUNT(sr.id) FROM sms_history_riders AS sr  where sr.sms_history_id = sms_histories.id) AS riders'));
        return Datatables::of($sms)
            ->editColumn('riders_count', function ($sms) {
                return '<center><button class="btn btn-sm btn-outline-info align-middle">' . $sms->riders . '</button></center>';
            })

            ->make(true);
    }
    public function all_riders(Request $request)
    {
        $sms_history_id = $request->input('sms_history_id');

        $sms_history_rider = SmsHistoryRider::where('sms_history_id', $sms_history_id);
        if (!$sms_history_rider->exists()) {
            return ['status' => 1, 'error' => 'No Rider found!'];
        }
        $sms_history_rider = $sms_history_rider->pluck('rider_id')->toArray();

        if (count($sms_history_rider) > 0) {
            $names = array();
            foreach ($sms_history_rider as $rider_id) {
                $names[] = Rider::find($rider_id)->name;
            }

            return ['status' => 0, 'success' => 'Rider Name', 'name' => $names];
        } else {
            return ['status' => 1, 'error' => 'No Rider found!'];
        }
    }

    public function rider_request_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 60);
        $rider_type = RiderType::all();
        $city = City::where('business_category_id', 1)->where('status', 1)->get();
        $category = RiderCategory::all();
        $main_category = RiderMainCategory::all();
        $route = Route::all();
        $operation_rider_category = OperationRidersCategory::all();
        $shifts = EmployeeShift::where('status', 1)->get();
        return view('admin.management.riders.rider_request')->with(['rider_types' => $rider_type, 'categories' => $category, 'routes' => $route, 'cities' => $city, 'operation_rider_category' => $operation_rider_category, 'shifts' => $shifts, 'main_category' => $main_category]);
    }

    public function rider_request_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 120);
        }
        $rider_request = RiderRequest::join('cities as c', 'rider_requests.city_id', '=', 'c.id')
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
                if ($rider_request->trax_id && $rider_request->trax_id != null) {
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

    public function approveRider(Request $request)
    {
        $validations = [
            'city_id' => 'required|numeric',
            'rider_name' => 'required|max:255',
            'phone' => 'required|max:255',
            'cnic' => 'required|max:255',
            'address' => 'required|max:255',
            'route_id' => 'required|numeric',
            'rider_category' => 'required|numeric',
            'rider_main_category' => 'required|numeric',
            'pin' => 'required|integer|digits:4',
            'rider_request_id' => 'required',
            'rider_type' => "required|numeric",
            'category' => "required|numeric",
            'rider_shift' => "required|numeric"
        ];
        $validate = Validator::make($request->all(), $validations);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate);
        }
        $employee = Employee::where('rider_request_id', $request->rider_request_id);
        if ($employee->exists()) {
            $employee = $employee->first();
            $employee->status_id = 1;
            $trax_id = $employee->trax_id;
            $employee_id = $employee->id;
            $employee->save();
        } else {
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
            $employee_id = null;
        }

        $rider = Rider::create([
            'city_id' => $request->city_id,
            'name' => $request->rider_name,
            'phone' => $request->phone,
            'cnic' => $request->cnic,
            'address' => $request->address,
            'route_id' => $request->route_id,
            'rider_category_id' => $request->rider_category,
            'rider_main_category_id' => $request->rider_main_category,
            'status' => 1,
            'pin' => bcrypt($request->pin),
            'created_by' => Auth::id(),
            'trax_id' => $trax_id,
            'employee_id' => $employee_id,
            'rider_type_id'  => $request->rider_type,
            'operation_rider_id'  => $request->category,
            'shift_id'  => $request->rider_shift
        ]);
        if ($rider) {
            NotificationsController::send(61, $rider->id, $request->pin);
            $rider_request = RiderRequest::find($request->rider_request_id);
            if ($rider_request) {
                $rider_request->status = 1;
                $rider_request->save();
            }
            return redirect()->back()->with('success', 'Rider added successfully');
        }
    }

    static public function riders_incentives_calculation($date)
    {

        $date_from = Carbon::createFromFormat("Y-m-d H:i:s", $date)->format('Y-m-d 06:00A');
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s", $next_day)->format('Y-m-d 05:59A');
        $riders = Rider::where('status', 1)->select('id', 'rider_category_id', 'incentive_amount')->get();
        if (count($riders) > 0) {
            foreach ($riders as $rider) {
                $rider_category_id = $rider->rider_category_id;
                if (in_array($rider_category_id, [3, 4, 5])) {
                    continue;
                }
                $pickup_shipment_ids = array();
                $delivery_shipment_ids = array();
                $pickup_incentive = 0;
                $delivery_incentive = 0;
                $pickup_shipments_count = 0;
                $delivered_shipment_count = 0;
                $pickup_shipment_ids = V2PickupReceivedShipment::where('rider_id', $rider->id)->whereBetween('created_at', [$date_from, $date_to]);

                if ($pickup_shipment_ids->exists()) {
                    $pickup_shipment_ids = $pickup_shipment_ids->pluck('shipment_id')->toArray();

                    $pickup_shipments_count = count($pickup_shipment_ids);
                    $cod_shipments = array();
                    $non_cod_shipments = array();
                    $verified_shipments = array();

                    $cod_shipments = Shipment::whereIn('id', $pickup_shipment_ids)->where('amount', '>', 0)->whereNotIn('user_id', [3324, 10104])->pluck('id')->toArray();
                    $non_cod_shipments = Shipment::whereIn('id', $pickup_shipment_ids)->where('amount', '=', 0)->whereNotIn('user_id', [3324, 10104])->pluck('id')->toArray();
                    $verified_shipments = Shipment::whereIn('id', $pickup_shipment_ids)->whereIn('user_id', [3324, 10104])->pluck('id')->toArray();

                    if ($rider_category_id == 1) {
                        if (count($cod_shipments) > 0) {

                            $shipment_weight_type_1_count = Shipment::whereIn('id', $cod_shipments)->whereBetween('actual_weight', [0.11, 1.50])->count();

                            $shipment_weight_type_2_count = Shipment::whereIn('id', $cod_shipments)->where('actual_weight', '>', 1.50)->count();
                            $shipment_weight_type_3_count = Shipment::whereIn('id', $cod_shipments)->where('actual_weight', '<', 0.11)->count();

                            if ($shipment_weight_type_1_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 1)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                            }
                            if ($shipment_weight_type_2_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 2)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                            }
                            if ($shipment_weight_type_3_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 3)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                            }
                        }

                        if (count($non_cod_shipments) > 0) {

                            $shipment_weight_type_1_count = Shipment::whereIn('id', $non_cod_shipments)->whereBetween('actual_weight', [0.11, 1.50])->count();
                            $shipment_weight_type_2_count = Shipment::whereIn('id', $non_cod_shipments)->where('actual_weight', '>', 1.50)->count();
                            $shipment_weight_type_3_count = Shipment::whereIn('id', $non_cod_shipments)->where('actual_weight', '<', 0.11)->count();
                            if ($shipment_weight_type_1_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 1)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                            }
                            if ($shipment_weight_type_2_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 2)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                            }
                            if ($shipment_weight_type_3_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 3)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                            }
                        }

                        if (count($verified_shipments) > 0) {
                            $shipment_weight_type_1_count = Shipment::whereIn('id', $verified_shipments)->whereBetween('actual_weight', [0.11, 1.50])->count();
                            $shipment_weight_type_2_count = Shipment::whereIn('id', $verified_shipments)->where('actual_weight', '>', 1.50)->count();
                            $shipment_weight_type_3_count = Shipment::whereIn('id', $verified_shipments)->where('actual_weight', '<', 0.11)->count();
                            if ($shipment_weight_type_1_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 1)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                            }
                            if ($shipment_weight_type_2_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 2)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                            }
                            if ($shipment_weight_type_3_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 3)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                            }
                        }
                    } else {

                        if (count($cod_shipments) > 0) {

                            $shipment_weight_type_1_count = Shipment::whereIn('id', $cod_shipments)->whereBetween('actual_weight', [0.11, 5.0])->count();

                            $shipment_weight_type_2_count = Shipment::whereIn('id', $cod_shipments)->where('actual_weight', '>', 5.0)->count();
                            $shipment_weight_type_3_count = Shipment::whereIn('id', $cod_shipments)->where('actual_weight', '<', 0.11)->count();

                            if ($shipment_weight_type_1_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 4)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                            }
                            if ($shipment_weight_type_2_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 5)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                            }
                            if ($shipment_weight_type_3_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 3)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                            }
                        }

                        if (count($non_cod_shipments) > 0) {

                            $shipment_weight_type_1_count = Shipment::whereIn('id', $non_cod_shipments)->whereBetween('actual_weight', [0.11, 5.0])->count();
                            $shipment_weight_type_2_count = Shipment::whereIn('id', $non_cod_shipments)->where('actual_weight', '>', 5.0)->count();
                            $shipment_weight_type_3_count = Shipment::whereIn('id', $non_cod_shipments)->where('actual_weight', '<', 0.11)->count();
                            if ($shipment_weight_type_1_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 4)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                            }
                            if ($shipment_weight_type_2_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 5)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                            }
                            if ($shipment_weight_type_3_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 3)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                            }
                        }

                        if (count($verified_shipments) > 0) {
                            $shipment_weight_type_1_count = Shipment::whereIn('id', $verified_shipments)->whereBetween('actual_weight', [0.11, 5.0])->count();
                            $shipment_weight_type_2_count = Shipment::whereIn('id', $verified_shipments)->where('actual_weight', '>', 5.0)->count();
                            $shipment_weight_type_3_count = Shipment::whereIn('id', $verified_shipments)->where('actual_weight', '<', 0.11)->count();
                            if ($shipment_weight_type_1_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 4)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                            }
                            if ($shipment_weight_type_2_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 5)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                            }
                            if ($shipment_weight_type_3_count > 0) {
                                $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 3)->first();
                                $incentive_shipment = $setting_cod->value;
                                $pickup_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                            }
                        }
                    }
                }

                $delivery_date = $date_to;
                $delivery_date = Carbon::parse($delivery_date)->toDateString();
                $delivery_notes = DeliveryNote::where('rider_id', $rider->id)->whereBetween('pending_for_verification_at', [$date_from, $date_to])->whereIn('status', [0, 1]);
                if ($delivery_notes->exists()) {
                    $delivery_note_ids = array();
                    $delivery_note_ids = $delivery_notes->pluck('id')->toArray();

                    $delivery_note_ids_count = count($delivery_note_ids);
                    if ($delivery_note_ids_count > 0) {
                        $delivery_shipment_ids = ShipmentsJourney::whereIn('reference_1_id', $delivery_note_ids)->whereIn('shipper_status_id', [14, 30, 36, 37])->where('verification', 1)->pluck('shipment_id')->toArray();
                        array_unique($delivery_shipment_ids);

                        $delivered_shipment_count = count($delivery_shipment_ids);

                        if ($delivered_shipment_count > 0) {

                            $cod_shipments = array();
                            $non_cod_shipments = array();
                            $verified_shipments = array();

                            $cod_shipments = Shipment::whereIn('id', $delivery_shipment_ids)->where('amount', '>', 0)->whereNotIn('user_id', [3324, 10104])->pluck('id')->toArray();
                            $non_cod_shipments = Shipment::whereIn('id', $delivery_shipment_ids)->where('amount', '=', 0)->whereNotIn('user_id', [3324, 10104])->pluck('id')->toArray();
                            $verified_shipments = Shipment::whereIn('id', $delivery_shipment_ids)->whereIn('user_id', [3324, 10104])->pluck('id')->toArray();

                            if ($rider_category_id == 1) {
                                if (count($cod_shipments) > 0) {

                                    $shipment_weight_type_1_count = Shipment::whereIn('id', $cod_shipments)->whereBetween('actual_weight', [0.11, 1.50])->count();
                                    $shipment_weight_type_2_count = Shipment::whereIn('id', $cod_shipments)->where('actual_weight', '>', 1.50)->count();
                                    $shipment_weight_type_3_count = Shipment::whereIn('id', $cod_shipments)->where('actual_weight', '<', 0.11)->count();


                                    if ($shipment_weight_type_1_count > 0) {
                                        $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 1)->first();
                                        $incentive_shipment = $setting_cod->value;
                                        $delivery_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                                    }
                                    if ($shipment_weight_type_2_count > 0) {
                                        $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 2)->first();
                                        $incentive_shipment = $setting_cod->value;
                                        $delivery_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                                    }
                                    if ($shipment_weight_type_3_count > 0) {
                                        $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 3)->first();
                                        $incentive_shipment = $setting_cod->value;
                                        $delivery_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                                    }
                                }

                                if (count($non_cod_shipments) > 0) {

                                    $shipment_weight_type_1_count = Shipment::whereIn('id', $non_cod_shipments)->whereBetween('actual_weight', [0.11, 1.50])->count();
                                    $shipment_weight_type_2_count = Shipment::whereIn('id', $non_cod_shipments)->where('actual_weight', '>', 1.50)->count();
                                    $shipment_weight_type_3_count = Shipment::whereIn('id', $non_cod_shipments)->where('actual_weight', '<', 0.11)->count();
                                    if ($shipment_weight_type_1_count > 0) {
                                        $setting_non_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 1)->first();
                                        $incentive_shipment = $setting_non_cod->value;
                                        $delivery_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                                    }
                                    if ($shipment_weight_type_2_count > 0) {
                                        $setting_non_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 2)->first();
                                        $incentive_shipment = $setting_non_cod->value;
                                        $delivery_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                                    }
                                    if ($shipment_weight_type_3_count > 0) {
                                        $setting_non_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 3)->first();
                                        $incentive_shipment = $setting_non_cod->value;
                                        $delivery_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                                    }
                                }

                                if (count($verified_shipments) > 0) {
                                    $shipment_weight_type_1_count = Shipment::whereIn('id', $verified_shipments)->whereBetween('actual_weight', [0.11, 1.50])->count();
                                    $shipment_weight_type_2_count = Shipment::whereIn('id', $verified_shipments)->where('actual_weight', '>', 1.50)->count();
                                    $shipment_weight_type_3_count = Shipment::whereIn('id', $verified_shipments)->where('actual_weight', '<', 0.11)->count();
                                    if ($shipment_weight_type_1_count > 0) {
                                        $setting_verification = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 1)->first();
                                        $incentive_shipment = $setting_verification->value;
                                        $delivery_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                                    }
                                    if ($shipment_weight_type_2_count > 0) {
                                        $setting_verification = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 2)->first();
                                        $incentive_shipment = $setting_verification->value;
                                        $delivery_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                                    }
                                    if ($shipment_weight_type_3_count > 0) {
                                        $setting_verification = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 3)->first();
                                        $incentive_shipment = $setting_verification->value;
                                        $delivery_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                                    }
                                }
                            } else {
                                if (count($cod_shipments) > 0) {

                                    $shipment_weight_type_1_count = Shipment::whereIn('id', $cod_shipments)->whereBetween('actual_weight', [0.11, 5.0])->count();
                                    $shipment_weight_type_2_count = Shipment::whereIn('id', $cod_shipments)->where('actual_weight', '>', 5.0)->count();
                                    $shipment_weight_type_3_count = Shipment::whereIn('id', $cod_shipments)->where('actual_weight', '<', 0.11)->count();


                                    if ($shipment_weight_type_1_count > 0) {
                                        $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 4)->first();
                                        $incentive_shipment = $setting_cod->value;
                                        $delivery_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                                    }
                                    if ($shipment_weight_type_2_count > 0) {
                                        $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 5)->first();
                                        $incentive_shipment = $setting_cod->value;
                                        $delivery_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                                    }
                                    if ($shipment_weight_type_3_count > 0) {
                                        $setting_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 1)->where('rider_shipment_weight_range_id', 3)->first();
                                        $incentive_shipment = $setting_cod->value;
                                        $delivery_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                                    }
                                }

                                if (count($non_cod_shipments) > 0) {

                                    $shipment_weight_type_1_count = Shipment::whereIn('id', $non_cod_shipments)->whereBetween('actual_weight', [0.11, 5.0])->count();
                                    $shipment_weight_type_2_count = Shipment::whereIn('id', $non_cod_shipments)->where('actual_weight', '>', 5.0)->count();
                                    $shipment_weight_type_3_count = Shipment::whereIn('id', $non_cod_shipments)->where('actual_weight', '<', 0.11)->count();
                                    if ($shipment_weight_type_1_count > 0) {
                                        $setting_non_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 4)->first();
                                        $incentive_shipment = $setting_non_cod->value;
                                        $delivery_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                                    }
                                    if ($shipment_weight_type_2_count > 0) {
                                        $setting_non_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 5)->first();
                                        $incentive_shipment = $setting_non_cod->value;
                                        $delivery_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                                    }
                                    if ($shipment_weight_type_3_count > 0) {
                                        $setting_non_cod = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 2)->where('rider_shipment_weight_range_id', 3)->first();
                                        $incentive_shipment = $setting_non_cod->value;
                                        $delivery_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                                    }
                                }

                                if (count($verified_shipments) > 0) {
                                    $shipment_weight_type_1_count = Shipment::whereIn('id', $verified_shipments)->whereBetween('actual_weight', [0.11, 5.0])->count();
                                    $shipment_weight_type_2_count = Shipment::whereIn('id', $verified_shipments)->where('actual_weight', '>', 5.1)->count();
                                    $shipment_weight_type_3_count = Shipment::whereIn('id', $verified_shipments)->where('actual_weight', '<', 0.11)->count();
                                    if ($shipment_weight_type_1_count > 0) {
                                        $setting_verification = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 4)->first();
                                        $incentive_shipment = $setting_verification->value;
                                        $delivery_incentive += $shipment_weight_type_1_count * $incentive_shipment;
                                    }
                                    if ($shipment_weight_type_2_count > 0) {
                                        $setting_verification = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 5)->first();
                                        $incentive_shipment = $setting_verification->value;
                                        $delivery_incentive += $shipment_weight_type_2_count * $incentive_shipment;
                                    }
                                    if ($shipment_weight_type_3_count > 0) {
                                        $setting_verification = RidersIncentiveSetting::where('rider_category_id', $rider_category_id)->where('rider_shipment_payment_type_id', 3)->where('rider_shipment_weight_range_id', 3)->first();
                                        $incentive_shipment = $setting_verification->value;
                                        $delivery_incentive += $shipment_weight_type_3_count * $incentive_shipment;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($pickup_incentive > 0 || $delivery_incentive > 0) {
                    $incentive_amount = 0;
                    if ($pickup_shipments_count) {

                        if ($rider->incentive_amount != null) {
                            $incentive_amount = ($rider->incentive_amount) * $pickup_shipments_count;
                        }
                    }

                    $riders_incentive = new RidersIncentive();
                    $riders_incentive->rider_id = $rider->id;
                    $riders_incentive->date = $date;
                    $riders_incentive->pickup_shipments = $pickup_shipments_count;

                    $riders_incentive->pickup_incentive = $incentive_amount; //$incentive_amount
                    $riders_incentive->delivery_shipments = $delivered_shipment_count;
                    $riders_incentive->delivery_incentive = $delivery_incentive;
                    $riders_incentive->save();
                }
            }
        }
    }

    public function rider_otp_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 414);
        $settings = GlobalSettings::where('type', 'rider_otp');
        if ($settings->doesntExist()) {
            $settings = new GlobalSettings();
            $settings->setting_value = 1;
            $settings->type = "rider_otp";
            $settings->save();
        } else {
            $settings = $settings->first();
        }

        return view('admin.otp.rider')->with(['setting' => $settings]);
    }

    public function rider_otp_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 415);
        }
        $riders = Rider::join('cities', 'riders.city_id', '=', 'cities.id')
            ->select('cities.name as city', 'riders.id as id', 'riders.name as name', 'riders.otp as otp', 'riders.reset_pin_otp as reset_pin_otp', 'riders.delivery_note_otp as delivery_note_otp', 'riders.otp_date as delivery_note_otp_date', 'riders.last_login_attempt')
            ->where('riders.status', 1);

        $datatable = Datatables::of($riders);
        return $datatable->make(true);
    }


    public function rider_otp_update(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 547);
        $settings = GlobalSettings::where('type', 'rider_otp');
        if ($settings->doesntExist()) {
            $settings = new GlobalSettings();
            $settings->type = "rider_otp";
        } else {
            $settings = $settings->first();
        }

        $settings->setting_value = $request->has('rider_otp_toggle') ? 1 : 0;
        $settings->save();

        return back()->with(['success' => "Rider OTP Updated Successfully"]);
    }



    public function rider_remarks_index()
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 656);
        $cities = City::where('status', '1')->where('business_category_id', '1')->select('id', 'name')->get();
        $riders = Rider::where('status', '1')->select('id', 'name')->get();
        $remarks = RiderRemark::select('id')->get();
        $statuses = RiderRemarkStatus::with('rider_remarks')->get();
        return view('admin.rider.rider_remarks')->with(['cities' => $cities, 'riders' => $riders, 'remarks' => $remarks, 'statuses' => $statuses]);
    }

    public function rider_remarks_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 657);
        }
        $rider_remarks = RiderRemark::leftJoin('riders as r', 'r.id', '=', 'rider_remarks.rider_id')
            ->leftJoin('admins as ad', 'ad.id', '=', 'rider_remarks.updated_by')
            ->leftJoin('cities as city', 'r.city_id', '=', 'city.id')
            ->leftJoin('cities as c', 'city.hub_id', '=', 'c.id')
            ->leftJoin('zones as z', 'city.zone_id', '=', 'z.id')
            ->leftJoin('rider_remarks_response as rrr', function ($join) {
                $join->on('rrr.rider_remarks_id', '=', 'rider_remarks.id')
                    ->where('rrr.type', '=', 1)
                    ->whereRaw('rrr.id = (SELECT MAX(id) FROM rider_remarks_response WHERE rider_remarks_id = rider_remarks.id AND type = 1)');
            })
            ->leftJoin('rider_remarks_response as rrr_2', function ($join) {
                $join->on('rrr_2.rider_remarks_id', '=', 'rider_remarks.id')
                    ->where('rrr_2.type', '=', 2)
                    ->whereRaw('rrr_2.id = (SELECT MAX(id) FROM rider_remarks_response WHERE rider_remarks_id = rider_remarks.id AND type = 2)');
            })    
            ->select([
                'rider_remarks.id as id',
                'r.name as rider_name',
                'r.id as rider_id',
                'r.trax_id as traxID',
                'city.id as city_id',
                'city.name as city_name',
                'c.name as hub',
                'z.name as zone_name',
                'rider_remarks.created_at as created_at',
                'rider_remarks.rider_remarks_status_id as status',
                'rider_remarks.updated_by as updated_by',
                'rider_remarks.updated_at as updated_at',
                'rider_remarks.rider_remarks as rider_remarks',
                'rrr.response as response',
                'rrr_2.response as response_2',
                'rider_remarks.id as rider_remarks_id',
                'ad.name as admin_name'
            ])
            ->orderBy('rider_remarks.created_at', 'desc');



        $datatables = Datatables::of($rider_remarks)
            ->editColumn('status', function ($rider_remarks) {
                if ($rider_remarks->status == 1) {
                    return 'Created';
                } else if ($rider_remarks->status == 2) {
                    return 'In Process';
                } else {
                    return 'Resolved';
                };
            })

            ->editColumn('id', function ($rider_remarks) {
                return 'R-00' . $rider_remarks->id;
            })

            ->filterColumn('rider_remarks.id', function ($query, $keyword) {
                $keyword = str_replace('R-00', '', ($keyword));
                if ($keyword != '') {
                    $query->where('rider_remarks.id', $keyword);
                }
            })

            ->addColumn("action", function ($rider_remarks) {
                $array = DB::table('admin_role_module_permissions')->where('role_id',Auth::user()->role_id)->pluck('permission_id')->toArray();
                
                if ($rider_remarks->status != 3 && count(array_intersect([863, 864, 865, 866], $array)) != null) {
                    $dropdown = '
                        <div class="btn-group" id="dasdas">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle action" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                    ';
            
                    if (($rider_remarks->status == 1) || session('role_id') == 1) {
                        if($rider_remarks->status == 1 && in_array(863, $array) && in_array(863, session('permissions'))) {
                            $dropdown .= '<button type="button" class="dropdown-item initial_response" data-id=' . $rider_remarks->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Initial Response</div></div></button>';
                        }
                        if($rider_remarks->status == 1 && in_array(864, $array) && in_array(864, session('permissions'))) {
                            $dropdown .= '<button type="button" class="dropdown-item rider_remarks_btn" data-value="2" data-id=' . $rider_remarks->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">In Process</div></div></button>';
                        }
                    }
            
                    if (($rider_remarks->status == 2) || session('role_id') == 1) {
                        if ($rider_remarks->status == 2 &&  in_array(865, $array) && in_array(865, session('permissions'))) {
                            $dropdown .= '<button type="button" class="dropdown-item final_response" data-id=' . $rider_remarks->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Final Response</div></div></button>';
                        }
                        if ($rider_remarks->status == 2 &&  in_array(866, $array) && in_array(866, session('permissions'))) {
                            $dropdown .= '<button type="button" class="dropdown-item rider_remarks_btn_1" data-value="3" data-id=' . $rider_remarks->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Resolved</div></div></button>';
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
            });
            

        if ($request->get('search_date_from') != null && $request->get('search_date_to') != null) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $stop_date = Carbon::createFromFormat('Y-m-d', $to)->endOfDay()->toDateTimeString();
            $datatables->whereBetween('rider_remarks.created_at', [$from, $stop_date]);
        }

        if ($search_city = $request->get('search_city')) {
            $datatables = $datatables->where('city_id', '=', $search_city);
        }


        if ($search_rider = $request->get('search_rider')) {
            $datatables = $datatables->where('rider_id', '=', $search_rider);
        }

        if ($search_remark = $request->get('search_remark')) {
            $datatables = $datatables->where('rider_remarks.id', '=', $search_remark);
        }

        return $datatables->make(true);
    }


    public function rider_remarks_post(Request $request)
    {
        if (isset($request->id) && isset($request->status_value)) {
            $validations = [
                'id' => 'required',
                'status_value' => 'required',
            ];

            $validate = Validator::make($request->all(), $validations);

            if ($validate->fails()) {
                return response()->json(['status' => 0, 'errors' => $validate->errors()]);
            } else {
                $id = $request->id;
                $rider_remark = RiderRemark::find($id);

                if (isset($request->status_value)) {
                    $rider_remark->rider_remarks_status_id = $request->status_value;
                    $rider_remark->updated_by = Auth::id();
                    $rider_remark->save();

                    return response()->json(['status' => 1]);
                }
            }
        } elseif (isset($request->id) && isset($request->initial_response)) {
            $validations = [
                'id' => 'required',
                'initial_response' => 'required',
            ];

            $validate = Validator::make($request->all(), $validations);

            if ($validate->fails()) {
                return response()->json(['status' => 0, 'errors' => $validate->errors()]);
            } else {
                $id = $request->id;
                $rider_remark = RiderRemark::find($id);

                $response = new RiderRemarksResponse();


                if (isset($request->initial_response)) {
                    $rider_remark->updated_by = Auth::id();
                    $response->rider_remarks_id = $rider_remark->id;
                    $response->response = $request->initial_response;
                    $response->updated_by = Auth::id();;
                    $response->type = '1';

                    $rider_remark->save();
                    $response->save();
                    return response()->json(['status' => 1]);
                }
            }
        } elseif (isset($request->id) && isset($request->final_response)) {
            $validations = [
                'id' => 'required',
                'final_response' => 'required',
            ];

            $validate = Validator::make($request->all(), $validations);

            if ($validate->fails()) {
                return response()->json(['status' => 0, 'errors' => $validate->errors()]);
            } else {
                $id = $request->id;
                $rider_remark = RiderRemark::find($id);
                // $response = RiderRemarksResponse::where('rider_remarks_id', $rider_remark->id)->first();
                $response = new RiderRemarksResponse();
                if (isset($request->final_response)) {
                    $response->rider_remarks_id = $rider_remark->id;
                    $response->response = $request->final_response;
                    $response->type = '2';
                    $response->updated_by = Auth::id();;

                    $rider_remark->updated_by = Auth::id();
                    $rider_remark->save();
                    $response->save();

                    return response()->json(['status' => 1]);
                }
            }
        } elseif (isset($request->id) && isset($request->status_value_2)) {
            $validations = [
                'id' => 'required',
                'status_value_2' => 'required',
            ];

            $validate = Validator::make($request->all(), $validations);

            if ($validate->fails()) {
                return response()->json(['status' => 0, 'errors' => $validate->errors()]);
            } else {
                $id = $request->id;
                $rider_remark = RiderRemark::find($id);

                if (isset($request->status_value_2)) {
                    $rider_remark->rider_remarks_status_id = $request->status_value_2;
                    $rider_remark->updated_by = Auth::id();

                    $rider_remark->save();

                    return response()->json(['status' => 1]);
                }
            }
        }
    }
}
