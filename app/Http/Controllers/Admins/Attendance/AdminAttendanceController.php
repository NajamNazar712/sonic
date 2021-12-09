<?php

namespace App\Http\Controllers\Admins\Attendance;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\Attendance\EmployeeAttendanceActionLog;
use App\Http\Models\Admin\RiderType;
use App\Http\Models\City;
use App\Http\Models\EmployeeShift;
use App\Http\Models\HR\Employee;
use App\Http\Models\ReportingLocation;
use App\Http\Models\Rider;
use Carbon\Carbon;
use Cassandra\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\VarDumper\Cloner\Data;
use Yajra\Datatables\Datatables;

class AdminAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    private function distance($origin, $destination)
    {
        return $this->vincenty_distance($origin, $destination);
    }

    private function vincenty_distance($origin, $destination)
    {
        $earth_radius = 6371;

        list($origin_latitude, $origin_longitude) = explode(',', $origin);
        list($destination_latitude, $destination_longitude) = explode(',', $destination);

        $origin_latitude = deg2rad($origin_latitude);
        $origin_longitude = deg2rad($origin_longitude);
        $destination_latitude = deg2rad($destination_latitude);
        $destination_longitude = deg2rad($destination_longitude);

        $longitude_delta = $destination_longitude - $origin_longitude;

        $distance = round($earth_radius * (atan2(sqrt(pow(cos($destination_latitude) * sin($longitude_delta), 2) + pow(cos($origin_latitude) * sin($destination_latitude) - sin($origin_latitude) * cos($destination_latitude) * cos($longitude_delta), 2)), (sin($origin_latitude) * sin($destination_latitude) + cos($origin_latitude) * cos($destination_latitude) * cos($longitude_delta)))), 2);

        return $distance;
    }

    private function calculate_location_status($latitude, $longitude)
    {
        $location_status = 1;
        $reporting_locations = ReportingLocation::where('status', 1);
        if ($reporting_locations->exists()) {
            $reporting_locations = $reporting_locations->get();
            foreach ($reporting_locations as $reporting_location) {
                $reporting_location->radius;
                $destination = $reporting_location->lat . ',' . $reporting_location->long;
                $origin = $latitude . ',' . $longitude;
                $distance = $this->distance($origin, $destination);
                if ($distance <= $reporting_location->radius / 1000) {
                    $location_status = 2;
                    return $location_status;
                }
            }
        } else {
            $location_status = 0;
        }
        return $location_status;
    }

    public function admin_attendance_index(Request $request){
        ActivityTrailController::createActivityTrailLog(Auth::id(),56);

        if(in_array(session('role_id'), [1, 63, 70])){
            $cities = City::select('id','name')->get();
            $departments = AdminDepartment::select('id','name')->get();
            $users = Admin::where('status', 1)->select('id','name')->get();
            $trax_id = Admin::where('status', 1)->wherenotnull('trax_id')->pluck('trax_id')->toArray();
            $rider_trax_id = Rider::where('status', 1)->wherenotnull('trax_id')->pluck('trax_id')->toArray();
            $trax_ids = array_merge($trax_id, $rider_trax_id);
            $admin_cnic = Admin::wherenotnull('cnic')->where('status', 1)->pluck('cnic')->toArray();
            $rider_cnic = Rider::where('status', 1)->wherenotnull('cnic')->pluck('cnic')->toArray();
            $cnic = array_merge($admin_cnic, $rider_cnic);
            $riders = Rider::where('status', 1)->select('id', 'name')->get();
        }
        else{
            $cities = City::select('id','name')->whereIn('id', session('hubs'))->get();
            $departments = AdminDepartment::select('id','name')->where('id', session('department_id'))->get();
            $users = Admin::join('admin_roles as ar','ar.id','=','admins.role_id')
                ->join('admin_departments as ad','ad.id','=','ar.department_id')
                ->where('ad.id', session('department_id'))
                ->select('admins.id','admins.name')->get();
            $trax_id = Admin::wherenotnull('trax_id')->pluck('trax_id')->toArray();
            $rider_trax_id = Rider::wherenotnull('trax_id')->pluck('trax_id')->toArray();
            $trax_ids = array_merge($trax_id, $rider_trax_id);
            $admin_cnic = Admin::wherenotnull('cnic')->where('status', 1)->pluck('cnic')->toArray();
            $rider_cnic = Rider::where('status', 1)->wherenotnull('cnic')->pluck('cnic')->toArray();
            $cnic = array_merge($admin_cnic, $rider_cnic);
            $riders = Rider::where('status', 1)->select('id', 'name')->get();
        }

        return view('admin.attendance.admin.index')->with(["departments" => $departments, "cities" => $cities, "admins" => $users, "trax_ids" => $trax_ids, "riders" => $riders, "cnics"=>$cnic]);
    }

    public function admin_attendance_horizontal_index(Request $request){
        ActivityTrailController::createActivityTrailLog(Auth::id(),453);

        if(in_array(session('role_id'), [1, 63, 70])){
            $departments = AdminDepartment::select('id','name')->get();
            $users = Admin::where('status', 1)->select('id','name')->get();
            $riders = Rider::where('status', 1)->select('id', 'name')->get();
            $trax_id = Admin::where('status', 1)->wherenotnull('trax_id')->pluck('trax_id')->toArray();
            $rider_trax_id = Rider::where('status', 1)->wherenotnull('trax_id')->pluck('trax_id')->toArray();
            $trax_ids = array_merge($trax_id, $rider_trax_id);
        }
        else{
            $departments = AdminDepartment::select('id','name')->where('id', session('department_id'))->get();
            $users = Admin::join('admin_roles as ar','ar.id','=','admins.role_id')
                ->join('admin_departments as ad','ad.id','=','ar.department_id')
                ->where('ad.id', session('department_id'))
                ->select('admins.id','admins.name')->get();
            $riders = Rider::where('status', 1)->select('id', 'name')->get();
            $trax_id = Admin::wherenotnull('trax_id')->pluck('trax_id')->toArray();
            $rider_trax_id = Rider::wherenotnull('trax_id')->pluck('trax_id')->toArray();
            $trax_ids = array_merge($trax_id, $rider_trax_id);
        }

        return view('admin.attendance.admin.horizontal')->with(["departments" => $departments, "admins" => $users, "trax_ids" => $trax_ids, "riders" => $riders]);
    }

    public function admin_attendance_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),116);
        }
        $attendances = EmployeeAttendance::leftjoin('admins as a', 'a.id', 'employee_attendances.employee_id')
            ->leftjoin('cities as c', 'c.id', 'a.default_hub_id')
            ->leftjoin('admin_roles as ar', 'ar.id', 'a.role_id')
            ->leftjoin('admin_departments as ad', 'ad.id', 'ar.department_id')
            ->leftjoin('riders as r', 'r.id', 'employee_attendances.employee_id')
            ->leftjoin('cities as rc', 'rc.id', 'r.city_id')
            ->leftjoin('rider_types as rt', 'rt.id', 'r.rider_type_id')
            ->leftjoin('employee_shifts as aes', 'a.shift_id', 'aes.id')
            ->leftjoin('employee_shifts as res', 'r.shift_id', 'res.id')
            ->select('a.name as admin_name', 'a.trax_id as trax_id', 'c.name as city_name', 'c.id as city_id', 'a.designation as designation', 'r.name as rider_name', 'r.trax_id as rider_trax_id', 'rc.name as rider_city_name', 'rc.id as rider_city_id', 'rt.name as rider_type', 'rt.id as rider_type_id', 'employee_attendances.attendance_date as attendance_date', 'employee_attendances.clock_in as clock_in', 'employee_attendances.clock_out as clock_out', 'employee_attendances.clock_in_latitude as clock_in_latitude', 'employee_attendances.clock_in_longitude as clock_in_longitude', 'employee_attendances.clock_out_latitude', 'employee_attendances.clock_out_longitude', 'ad.name as department', 'ad.id as department_id', 'employee_attendances.employee_type', 'employee_attendances.clock_in_location as clock_in_status', 'employee_attendances.clock_out_location as clock_out_status', 'r.cnic as rider_cnic', 'a.cnic as admin_cnic', 'employee_attendances.clock_in_datetime as clock_in_datetime', 'employee_attendances.clock_out_datetime as clock_out_datetime', 'aes.name as admin_shift', 'res.name as rider_shift');

        if(session('role_id') != 1 && session('role_id') != 63 && session('role_id') != 70){
            if(session('department_id') != 6){
                $attendances->where('employee_attendances.employee_type', 1)
                    ->where('ad.id', session('department_id'));
            }
            else{
                $attendances->where(function($query){
                    $query->where('employee_attendances.employee_type', 2)
                        ->orWhere('ad.id', session('department_id'));
                });
            }
            $attendances = $attendances->whereIn('c.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($attendances)
            ->editColumn('trax_id', function ($employee) {
                if ($employee->employee_type == 2) {
                    return $employee->rider_trax_id;
                } else {
                    return $employee->trax_id;
                }
            })
            ->editColumn('shift', function ($employee) {
                if ($employee->employee_type == 2) {
                    return $employee->rider_shift;
                } else {
                    return $employee->admin_shift;
                }
            })
            ->editColumn('clock_in', function ($employee) {
                if ($employee->clock_in_datetime) {
                    return Carbon::parse($employee->clock_in_datetime)->format("Y-m-d H:i:s");
                } else {
                    return $employee->clock_in;
                }
            })
            ->editColumn('clock_out', function ($employee) {
                if ($employee->clock_out_datetime) {
                    return Carbon::parse($employee->clock_out_datetime)->format("Y-m-d H:i:s");
                } else {
                    return $employee->clock_out;
                }
            })
            ->editColumn('name', function ($employee) {
                if ($employee->employee_type == 2) {
                    return $employee->rider_name;
                } else {
                    return $employee->admin_name;
                }
            })
            ->editColumn('city_name', function ($employee) {
                if ($employee->employee_type == 2) {
                    return $employee->rider_city_name;
                } else {
                    return $employee->city_name;
                }
            })
            ->editColumn('employee_type', function ($employee) {
                if ($employee->employee_type == 2) {
                    return "Rider";
                } else {
                    return "Staff";
                }
            })
            ->editColumn('designation', function ($employee) {
                if ($employee->employee_type == 2) {
                    return $employee->rider_type;
                } else {
                    return $employee->designation;
                }
            })
            ->editColumn('department', function ($employee) {
                if ($employee->employee_type == 2) {
                    return "Operations";
                } else {
                    return $employee->department;
                }
            })
            ->editColumn('cnic', function ($employee) {
                if ($employee->employee_type == 2) {
                    return $employee->rider_cnic;
                } else {
                    return $employee->admin_cnic;
                }
            })
            ->addColumn('attendance_day', function ($employee) {
                    return date('l', strtotime($employee->attendance_date));
            })
            ->addColumn("clock_in_location", function ($employee) {
                if ($employee->clock_in_latitude && $employee->clock_in_longitude) {
                    $clock_in = '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href="https://www.google.com/maps/search/?api=1&query=' . $employee->clock_in_latitude . ',' . $employee->clock_in_longitude . '" target="_blank"><i class="la la-map-marker"></i> View</a></div>';
                } else {
                    $clock_in = '-';
                }
                return $clock_in;
            })
            ->addColumn('clock_in_status', function ($employee) {
                if ($employee->clock_in_status == 2) {
                    return "On-site";
                } else if ($employee->clock_in_status == 1) {
                    return "Off-site";
                } else {
                    return "";
                }
            })
            ->addColumn("clock_out_location", function ($employee) {
                if ($employee->clock_out_latitude && $employee->clock_out_longitude) {
                    $clock_out = '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href="https://www.google.com/maps/search/?api=1&query=' . $employee->clock_out_latitude . ',' . $employee->clock_out_longitude . '" target="_blank"><i class="la la-map-marker"></i> View</a></div>';
                } else {
                    $clock_out = '-';
                }

                return $clock_out;
            })
            ->addColumn('clock_out_status', function ($employee) {
                if ($employee->clock_out_status == 2) {
                    return "On-site";
                } else if ($employee->clock_out_status == 1) {
                    return "Off-site";
                } else {
                    return "";
                }
            });

        if ($search_admin = $request->get('search_admin')) {
            $datatable->where('a.id', $search_admin)->where('employee_type',1);
        }
        if ($search_rider = $request->get('search_rider')) {
            $datatable->where('r.id', $search_rider)->where('employee_type',2);
        }
        if ($search_city = $request->get('search_city')) {
            $datatable->where(function($q) use ($search_city){
                $q->where([['c.id', $search_city],['employee_type',1]])
                    ->orWhere([['rc.id', $search_city],['employee_type',2]]);
            });
        }
        if ($search_department = $request->get('search_department')) {
            if($search_department != 6) {
                $datatable->where('department_id', $search_department)->where('employee_type',1);
            }
            else{
                $datatable->where(function($query) use($search_department){
                    $query->where('employee_type',2)
                        ->orWhere('ad.id', $search_department);
                });
            }
        }

        if ($search_trax_id = $request->get('search_trax_id')) {
            $datatable->where(function($q) use ($search_trax_id){
                $q->where([['a.trax_id', $search_trax_id],['employee_type',1]])
                    ->orWhere([['r.trax_id', $search_trax_id],['employee_type',2]]);
            });
        }

        if ($search_cnic = $request->get('search_cnic')) {
            $datatable->where(function($q) use ($search_cnic){
                $q->where([['a.cnic', $search_cnic],['employee_type',1]])
                    ->orWhere([['r.cnic', $search_cnic],['employee_type',2]]);
            });
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('employee_attendances.attendance_date', [$from, $to]);
        }

        return $datatable->make(true);
    }

    public function admin_attendance_horizontal_table(Request $request,$array = false)
    {
        $date = Carbon::createFromFormat('M Y',$request->get('search_month'));
        $year = $date->year;
        $month = $date->month;
        $prev_month = $date->subMonth(1)->month;
        $from = new \DateTime(Carbon::createFromDate($year,$prev_month,26)->toDateString());
        $to = new \DateTime(Carbon::createFromDate($year,$month,25)->toDateString());
        $to = $to->modify( '+1 day' );
        $period = array();

        $interval = new \DateInterval('P1D');;
        $daterange = new \DatePeriod($from, $interval ,$to);


        foreach ($daterange as $date) {
            if($array)
            {
                $period['search'][] = $date->format('Y-m-d');
                $period['display'][] = $date->format('d/m/Y');
            }
            else{
                $period[] = $date->format('d/m/Y');
            }
        }

        if($array)
        {
            return $period;
        }
        return response()->json(['status'=>1,'period'=>$period]);

    }

    public function admin_attendance_horizontal_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),454);
        }

        $attendances = EmployeeAttendance::leftjoin('admins as a', 'a.id', 'employee_attendances.employee_id')
            ->leftjoin('cities as c', 'c.id', 'a.default_hub_id')
            ->leftjoin('riders as r', 'r.id', 'employee_attendances.employee_id')
            ->leftjoin('admin_roles as ar', 'ar.id', 'a.role_id')
            ->leftjoin('admin_departments as ad', 'ad.id', 'ar.department_id')
            ->leftjoin('employee_designations as ed', 'ed.id', 'a.designation_id')
            ->leftjoin('rider_types as rt', 'rt.id', 'r.rider_type_id')
            ->select('employee_attendances.employee_id','a.name as admin_name', 'a.trax_id as trax_id', 'a.designation as designation','ed.name as designation_name', 'r.name as rider_name', 'r.trax_id as rider_trax_id', 'rt.name as rider_type', 'rt.id as rider_type_id', 'ad.name as department', 'ad.id as department_id', 'employee_attendances.employee_type');


        if(session('role_id') != 1 && session('role_id') != 63 && session('role_id') != 70){
            if(session('department_id') != 6){
                $attendances->where('employee_attendances.employee_type', 1)
                    ->where('ad.id', session('department_id'));
            }
            else{
                $attendances->where(function($query){
                    $query->where('employee_attendances.employee_type', 2)
                        ->orWhere('ad.id', session('department_id'));
                });
            }
            $attendances = $attendances->whereIn('c.hub_id', session('hubs'));
        }


        if ($request->get('search_month')) {
            $date = Carbon::createFromFormat('M Y',$request->get('search_month'));
            $year = $date->year;
            $month = $date->month;
            $prev_month = $date->subMonth(1)->month;
            $from = Carbon::createFromDate($year,$prev_month,26)->toDateString();
            $to = Carbon::createFromDate($year,$month,25)->toDateString();
            $attendances->whereBetween('employee_attendances.attendance_date', [$from, $to]);
        }

        $attendances->groupBy('employee_attendances.employee_id');

        $periods =  $this->admin_attendance_horizontal_table($request,true);
        $today = Carbon::now();

        $datatable = Datatables::of($attendances)
            ->editColumn('trax_id', function ($employee) {
                if ($employee->employee_type == 2) {
                    return $employee->rider_trax_id;
                } else {
                    return $employee->trax_id;
                }
            })
            ->editColumn('name', function ($employee) {
                if ($employee->employee_type == 2) {
                    return $employee->rider_name;
                } else {
                    return $employee->admin_name;
                }
            })
            ->editColumn('designation', function ($employee) {
                if ($employee->employee_type == 2) {
                    return $employee->rider_type;
                } else {
                    if($employee->designation_name != null)
                    {
                        return $employee->designation_name;
                    }
                    return $employee->designation;
                }
            })
            ->editColumn('department', function ($employee) {
                if ($employee->employee_type == 2) {
                    return "Operations";
                } else {
                    return $employee->department;
                }
            });
            foreach($periods['display'] as $key => $period)
            {
                $datatable->addColumn($period, function ($employee) use ($key, $periods,$today) {
                    $data = EmployeeAttendance::where('employee_id',$employee->employee_id)
                        ->where('attendance_date',$periods['search'][$key])
                        ->where(function ($query){
                            $query->where('clock_in_datetime','!=',null)
                                ->orWhere('clock_in','!=',null);
                        })
                        ->first();

                    if($data) {
                        if ($data->clock_in_datetime) {
                            $time = Carbon::parse($data->clock_in_datetime)->format("H:i");
                        } else {
                            $time = $data->clock_in;
                        }

                        $time .= " <br> ";

                        if ($data->clock_out_datetime) {
                            $time .= Carbon::parse($data->clock_out_datetime)->format("H:i");
                        } else {
                            $time .= $data->clock_out;
                        }
                    }
                    else if($periods['search'][$key] > $today){
                        $time = "-";
                    }
                    else{
                        $time = "<span class='text-danger'>A</span>";
                    }

                    return $time;

                });
            }

        if ($search_admin = $request->get('search_admin')) {
            $datatable->where('a.id', $search_admin)->where('employee_type',1);
        }
        if ($search_rider = $request->get('search_rider')) {
            $datatable->where('r.id', $search_rider)->where('employee_type',2);
        }
        if ($search_department = $request->get('search_department')) {
            if($search_department != 6) {
                $datatable->where('ad.id', $search_department)->where('employee_type',1);
            }
            else{
                $datatable->where(function($query) use($search_department){
                    $query->where('employee_type',2)
                        ->orWhere('ad.id', $search_department);
                });
            }
        }

        if ($search_trax_id = $request->get('search_trax_id')) {
            $datatable->where(function($q) use ($search_trax_id){
                $q->where([['a.trax_id', $search_trax_id],['employee_type',1]])
                    ->orWhere([['r.trax_id', $search_trax_id],['employee_type',2]]);
            });
        }

        return $datatable->make(true);
    }

    public function mark_attendance_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 432);
        $admin_id = Auth::id();
        $date = Carbon::now()->format("Y-m-d");
        $admin = Admin::find($admin_id);
        if ($admin) {
            $admin_shift = EmployeeShift::where('id', $admin->shift_id);
            if ($admin_shift->exists()) {
                $admin_shift = $admin_shift->first();
                $shift_time = Carbon::createFromFormat('H:i:s', $admin_shift->start_time);
                if (Carbon::now()->lt($shift_time)) {
                    $date = Carbon::now()->subDays(1)->format("Y-m-d");
                }
            }
            $clock_in = 0;
            $clock_out = 0;
            $attendance = EmployeeAttendance::where('employee_id', $admin_id)
                ->whereDate('attendance_date', $date)
                ->where('employee_type', 1);
            if ($attendance->exists()) {
                $attendance = $attendance->first();
                if ($attendance->clock_in_datetime != NULL) {
                    $clock_in = 1;
                }
                if ($attendance->clock_out_datetime != NULL) {
                    $clock_out = 1;
                }
            }
            return view('admin.attendance.mark_index', compact('clock_in', 'clock_out', 'date'));
        } else {
            return redirect()->back()->with(['status' => 0, 'error' => 'User not found']);
        }
    }

    public function mark_attendance_submit(Request $request)
    {
        $attendance_date = $request->attendance_date;
        $attendance_mark = Carbon::now()->format('Y-m-d H:i:s');
        $admin_id = Auth::id();
        $admin = Admin::find($admin_id);
        if ($admin) {
            if (session('latitude') && session('longitude')) {
                $employee_attendance = EmployeeAttendance::where('employee_id', $admin_id)
                    ->where('employee_type', 1)
                    ->where('attendance_date', $attendance_date);
                if ($employee_attendance->exists()) {
                    $attendance = $employee_attendance->first();
                } else {
                    $attendance = new EmployeeAttendance();
                    $attendance->employee_id = $admin_id;
                    $attendance->employee_type = 1;
                    $attendance->attendance_date = $attendance_date;
                }
                $location_status = $this->calculate_location_status(session('latitude'), session('longitude'));
                if ($request->clock_in == 1 && $request->clock_out == 0) {
                    if ($attendance->clock_out_datetime == NULL){
                        $attendance->clock_out_datetime = $attendance_mark;
                        $attendance->clock_out_latitude = session('latitude');
                        $attendance->clock_out_longitude = session('longitude');
                        $attendance->clock_out_location = $location_status;
                        $attendance->save();

                        $attendance_action_log = new EmployeeAttendanceActionLog();
                        $attendance_action_log->employee_id = $admin_id;
                        $attendance_action_log->employee_type = 1;
                        $attendance_action_log->action_id = 2;
                        $attendance_action_log->action_date = $attendance_mark;
                        $attendance_action_log->attendance_date = $attendance_date;
                        $attendance_action_log->latitude = $attendance->clock_out_latitude;
                        $attendance_action_log->longitude = $attendance->clock_out_longitude;
                        $attendance_action_log->location_status = $location_status;
                        $attendance_action_log->save();
                        return response()->json(['status' => 2, 'success' => 'Clock Out Successful', 'date' => $attendance_mark, 'error' => 0]);
                    }else{
                        return response()->json(['status' => 2, 'message' => 'You have already marked Clock Out', 'error' => 1]);
                    }
                } elseif ($request->clock_in == 0 && $request->clock_out == 0) {
                    if($attendance->clock_in_datetime == NULL){
                        $attendance->clock_in_datetime = $attendance_mark;
                        $attendance->clock_in_latitude = session('latitude');
                        $attendance->clock_in_longitude = session('longitude');
                        $attendance->clock_in_location = $location_status;
                        $attendance->save();

                        $attendance_action_log = new EmployeeAttendanceActionLog();
                        $attendance_action_log->employee_id = $admin_id;
                        $attendance_action_log->employee_type = 1;
                        $attendance_action_log->action_id = 1;
                        $attendance_action_log->action_date = $attendance_mark;
                        $attendance_action_log->attendance_date = $attendance_date;
                        $attendance_action_log->latitude = $attendance->clock_in_latitude;
                        $attendance_action_log->longitude = $attendance->clock_in_longitude;
                        $attendance_action_log->location_status = $location_status;
                        $attendance_action_log->save();
                        return response()->json(['status' => 1, 'success' => 'Clock In Successful', 'date' => $attendance_mark, 'error' => 0]);
                    }else{
                        return response()->json(['status' => 1, 'message' => 'You have already marked Clock In', 'error' => 1]);
                    }
                } else {
                    return response()->json(['status' => 0, 'error' => 'Unable to mark attendance']);
                }
            } else {
                return response()->json(['status' => 0, 'error' => 'Enable Your Location First']);
            }
        } else {
            return redirect()->back()->with(['status' => 0, 'error' => 'User not found']);
        }

    }

    public function mark_attendance_list(Request $request)
    {
        $date = Carbon::today()->toDateString();
        if ($request->has('attendance_date')) {
            $date = $request->attendance_date;
        }
        $admin_attendance_action = EmployeeAttendanceActionLog::where('employee_id', Auth::id())
            ->whereDate('attendance_date', $date)
            ->where('employee_type', 1)
            ->select('latitude', 'longitude', 'action_id', 'attendance_date', 'action_date');

        $datatable = Datatables::of($admin_attendance_action)
            ->editColumn('latitude', function ($action) {
                if ($action->latitude && $action->longitude) {
                    $action = '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href="https://www.google.com/maps/search/?api=1&query=' . $action->latitude . ',' . $action->longitude . '" target="_blank"><i class="la la-map-marker"></i> View</a></div>';
                } else {
                    $action = '-';
                }
                return $action;
            })
            ->editColumn('attendance_date', function ($data) {
                return Carbon::parse($data->attendance_date)->format("Y-m-d");
            })
            ->editColumn('action_id', function ($data) {
                if ($data->action_id == 1) {
                    return 'Clock-In';
                } else {
                    return 'Clock-Out';
                }
            });
        return $datatable->make(true);
    }


}
