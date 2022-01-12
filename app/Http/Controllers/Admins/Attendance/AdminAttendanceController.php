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
use App\Http\Models\HR\EmployeePayslip;
use App\Http\Models\ReportingLocation;
use App\Http\Models\Rider;
use Carbon\Carbon;
use Cassandra\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
        $month = "01 ".$request->get('search_month');
        $date = Carbon::createFromFormat('d M Y',$month);
        $year = $date->year;
        $month = $date->month;
        $prev = $date->subMonth(1);
        $prev_month = $prev->month;
        $prev_year = $prev->year;
        $from = new \DateTime(Carbon::createFromDate($prev_year,$prev_month,26)->toDateString());
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
            $month = "01 ".$request->get('search_month');
            $date = Carbon::createFromFormat('d M Y',$month);
            $year = $date->year;
            $month = $date->month;
            $prev = $date->subMonth(1);
            $prev_month = $prev->month;
            $prev_year = $prev->year;
            $from = Carbon::createFromDate($prev_year,$prev_month,26)->toDateString();
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

    public function attendance_excel_upload(Request $request)
    {
        Validator::extend('check_trax_id', function ($attribute, $value, $parameters, $validator) {

            if ($value) {
                $result = false;
                if (Admin::where('trax_id', $value)->exists()) {
                    $result = true;
                } else {
                    if (Rider::where('trax_id', $value)->exists()) {
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
            'trax_id' => ['required', 'between:1,100', 'check_trax_id'],
            'name' => ['required', 'between:1,100'],
            'designation' => ['required', 'between:1,100'],
            'department' => ['required', 'between:1,100'],
            'hub' => ['required', 'between:1,100'],
            'zone' => ['nullable', 'between:1,100'],
            'joining_date' => ['required', 'date_format:Y-m-d'],
            'confirmation_date' => ['nullable', 'date_format:Y-m-d'],
            'cnic' => ['required', 'between:1,100'],
            'employee_status' => ['nullable', 'string', 'between:1,100'],
            'employee_type' => ['nullable', 'string', 'between:1,100'],
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
            'iban' => ['nullable', 'string'],

        ];


        $fields = [0 => 'trax_id', 1 => 'attendance_date', 2 => 'clock_in_datetime', 3 => 'clock_out_datetime'];
        if ($file = $request->file('attendance')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Employee ID', 'Attendance Date(yyyy-mm-dd)', 'Clock-in DateTime(yyyy-mm-dd hh:mm:ss)', 'Clock-out DateTime(yyyy-mm-dd hh:mm:ss)'];

            if (isset($spreadsheet)) {
                $header_correct = true;

                foreach ($spreadsheet[0] as $index => $header_value) {
                    if ($index == 3) {
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

                    foreach ($rows as $key => $row) {
                        $employee = Employee::where('trax_id', $row['trax_id']);
                        if($employee->exists()){
                            $employee = $employee->first();
                        }
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


}
