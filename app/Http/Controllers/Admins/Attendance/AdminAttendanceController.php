<?php

namespace App\Http\Controllers\Admins\Attendance;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\Attendance\EmployeeAttendanceActionLog;
use App\Http\Models\City;
use App\Http\Models\EmployeeShift;
use App\Http\Models\HR\Employee;
use App\Http\Models\ReportingLocation;
use App\Http\Models\Rider;
use Auth;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
        $search_from = Carbon::createFromFormat('d F, Y',$request->search_from);
        $search_to = Carbon::createFromFormat('d F, Y',$request->search_to);
        $search_to = $search_to->modify('+1 day');
        $period = array();

        $interval = new \DateInterval('P1D');;
        $daterange = new \DatePeriod($search_from, $interval ,$search_to);

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


        if ($request->get('search_from') && $request->get('search_to')) {
            $search_from = Carbon::createFromFormat('d F, Y',$request->search_from)->toDateString();
            $search_to = Carbon::createFromFormat('d F, Y',$request->search_to)->toDateString();
            $attendances->whereBetween('employee_attendances.attendance_date', [$search_from, $search_to]);
        }

        $attendances->groupBy('employee_attendances.employee_id','employee_attendances.employee_type');

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
            if(count($periods) > 0) {
                foreach ($periods['display'] as $key => $period) {
                    $datatable->addColumn($period, function ($employee) use ($key, $periods, $today) {
                        $data = EmployeeAttendance::where('employee_id', $employee->employee_id)
                            ->where('attendance_date', $periods['search'][$key])
                            ->where('employee_type', $employee->employee_type)
                            ->where(function ($query) {
                                $query->where('clock_in_datetime', '!=', null)
                                    ->orWhere('clock_in', '!=', null);
                            })
                            ->first();

                        if ($data) {
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
                        } else if ($periods['search'][$key] > $today) {
                            $time = "-";
                        } else {
                            $time = "<span class='text-danger'>A</span>";
                        }

                        return $time;

                    });
                }
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
        $rules = [
            'attendance' => ['required', 'mimes:xlx,xlsx'],
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return back()->with(['error' => "Invalid File Format"]);
        } else {
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
                'trax_id' => 'User ID',
                'attendance_datetime' => 'Attendance DateTime',
            ];

            $messages = [
                'required' => ':attribute is Required.',
                'integer' => ':attribute must be an Integer.',
                'exists' => 'Given :attribute is Invalid.',
                'check_trax_id' => 'Employee id not found!',

            ];
            $rules = [
                'trax_id' => ['required', 'between:1,100', 'check_trax_id'],
                'attendance_datetime' => ['required', 'date_format:j-n-Y  G:i:s'],
            ];


            $fields = [0 => 'trax_id', 1 => '', 2 => '', 3 => 'attendance_datetime'];
            if ($file = $request->file('attendance')) {
                $spreadsheet = IOFactory::createReaderForFile($file);
                $spreadsheet->setReadDataOnly(true);
                $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

                $header = ['User ID', 'Verify Mode', 'IO Mode', 'IO Time'];

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

                    $attendance_data = array();
                    foreach ($rows as $key => $row) {
                        $date_string = explode(":", trim($row['attendance_datetime']));
                        if (count($date_string) == 3) {
                            $date_string[2] = str_pad($date_string[2], 2, 0, STR_PAD_LEFT);
                            $date_string[1] = str_pad($date_string[1], 2, 0, STR_PAD_LEFT);
                            $rows[$key]['attendance_datetime'] = implode(':', $date_string);
                            $row['attendance_datetime'] = implode(':', $date_string);
                        }
                        $rows[$key]['trax_id'] = "Trax" . trim($row['trax_id']);
                        $row['trax_id'] = "Trax" . trim($row['trax_id']);

                        $row_id = $key + 2;

                        $validate = Validator::make($row, $rules, $messages);

                        $validate->setAttributeNames($names);

                        if ($validate->fails()) {
                            $errors['Row #' . $row_id] = $validate->errors()->all();
                        }
                        $attendance_data[trim($row['trax_id'])][Carbon::parse($row['attendance_datetime'])->format("Y-m-d")][] = Carbon::parse($row['attendance_datetime'])->format("G:i:s");
                    }
                    if (empty($errors)) {
                        $updated = 0;
                        $not_updated = 0;
                        $latitude = '24.857788594719032';
                        $longitude = '67.12465366441765';

                        foreach ($attendance_data as $user_id => $attendance) {
                            foreach ($attendance as $date => $time) {
                                $employee = Employee::where('trax_id', $user_id);
                                if ($employee->exists()) {
                                    $employee = $employee->first();
                                    $shift = EmployeeShift::find($employee->shift_id);
                                    $night = false;
                                    if ($shift) {
                                        $shift_minutes = Carbon::parse($shift->start_time)->diffInMinutes(Carbon::parse($shift->end_time), false);
                                        if ($shift_minutes < 0) {
                                            $night = true;
                                        }
                                    }
                                    $type = $employee->employee_type_id;
                                    if ($type == 1) {
                                        $employee_id = $employee->admin->id;
                                    } else {
                                        $employee_id = $employee->rider->id;
                                    };
                                    $clock_out_flag = true;
                                    if ($night) {
                                        $max = max($time);
                                        $clock_in = Carbon::parse($date . ' ' . $max)->format("Y-m-d H:i:s");
                                        $clock_out = null;
                                        $clock_out_flag = false;
                                    } else {
                                        $min = min($time);
                                        $max = max($time);
                                        $clock_in = Carbon::parse($date . ' ' . $min)->format("Y-m-d H:i:s");
                                        if ($min == $max) {
                                            $clock_out = null;
                                            $clock_out_flag = false;
                                        } else {
                                            $clock_out = Carbon::parse($date . ' ' . $max)->format("Y-m-d H:i:s");
                                        }
                                    }
                                    $employee_attendance = EmployeeAttendance::where('employee_id', $employee_id)->where('employee_type', $type)->where('attendance_date', $date);
                                    $clock_in_flag = false;
                                    if ($employee_attendance->exists()) {
                                        $employee_attendance = $employee_attendance->first();
                                        if (!$employee_attendance->clock_in_datetime) {
                                            $clock_in_flag = true;
                                        } else {
                                            $clock_out = Carbon::parse($date . ' ' . $max)->format("Y-m-d H:i:s");
                                        }
                                    } else {
                                        $clock_in_flag = true;
                                        $employee_attendance = new EmployeeAttendance();
                                        $employee_attendance->employee_id = $employee_id;
                                        $employee_attendance->employee_type = $type;
                                        $employee_attendance->attendance_date = Carbon::parse($date)->format("Y-m-d");
                                        $employee_attendance->save();
                                    }
                                    if ($clock_in_flag) {
                                        $employee_attendance->clock_in_datetime = $clock_in;
                                        $employee_attendance->clock_in_latitude = $latitude;
                                        $employee_attendance->clock_in_longitude = $longitude;
                                        $employee_attendance->clock_in_location = 2;
                                        $employee_attendance->save();

                                        $clock_in_action = new EmployeeAttendanceActionLog();
                                        $clock_in_action->employee_id = $employee_attendance->employee_id;
                                        $clock_in_action->employee_type = $employee_attendance->employee_type;
                                        $clock_in_action->action_id = 1;
                                        $clock_in_action->action_date = $employee_attendance->clock_in_datetime;
                                        $clock_in_action->attendance_date = $employee_attendance->attendance_date;
                                        $clock_in_action->latitude = $latitude;
                                        $clock_in_action->longitude = $longitude;
                                        $clock_in_action->location_status = 2;
                                        $clock_in_action->save();
                                    }
                                    if ($clock_out_flag) {
                                        $employee_attendance->clock_out_datetime = $clock_out;
                                        $employee_attendance->clock_out_location = 2;
                                        $employee_attendance->clock_out_latitude = $latitude;
                                        $employee_attendance->clock_out_longitude = $longitude;
                                        $employee_attendance->save();

                                        $clock_out_action = new EmployeeAttendanceActionLog();
                                        $clock_out_action->employee_id = $employee_attendance->employee_id;
                                        $clock_out_action->employee_type = $employee_attendance->employee_type;
                                        $clock_out_action->action_id = 2;
                                        $clock_out_action->action_date = $employee_attendance->clock_out_datetime;
                                        $clock_out_action->attendance_date = $employee_attendance->attendance_date;
                                        $clock_out_action->latitude = $latitude;
                                        $clock_out_action->longitude = $longitude;
                                        $clock_out_action->location_status = 2;
                                        $clock_out_action->save();
                                    }
                                    $updated++;
                                }
                            }
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

    public function attendance_print(Request $request)
    {
        $trax_id = $request->pdf_trax_id;
        $date_from = $request->pdf_date_from;
        $date_to = $request->pdf_date_to;
        $from = Carbon::parse($request->pdf_date_from_formatted)->format('Y-m-d 00:00:00');
        $to = Carbon::parse($request->pdf_date_to_formatted)->format('Y-m-d 23:59:59');

        $leave = 0;
        $absent = 0;
        $late = 0;
        $earlyout = 0;
        $total_presents = 0;
        $ontime = 0;
        $total = 0;
        $sunday = 0;

        $employee = Employee::where('trax_id', $trax_id);

        if (!$employee->exists()) {
            return redirect()->back()->with(['status' => 0, 'error' => 'Employee not found!']);
        }
        $employee = $employee->first();
        $type = $employee->employee_type_id;
        if ($type == 1) {
            $employee_id = $employee->admin->id;
        } else {
            $employee_id = $employee->rider->id;
        }
        $employee_attendances = EmployeeAttendance::where('employee_id', $employee_id)->where('employee_type', $type)->whereBetween('attendance_date', [$from, $to])->orderBy('attendance_date', 'ASC');
        if(!$employee_attendances->exists()){
            return redirect()->back()->with(['status' => 0, 'error' => 'Attendance not found!']);
        }
        $employee_attendances = $employee_attendances->get();
        $html = '<!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Attendance</title>

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
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class=""></td>
                             </tr>
                             <tr>
                                <td class="text-center align-middle"><h2>Trax Online (Pvt.) Ltd</h2></td>
                             </tr>
                             <tr>
                                <td class="text-center align-middle">Employee Time Sheet PDF</td>
                             </tr>
                             <tr>
                                <td class="text-center align-middle">'.$date_from.' TO '.$date_to.'</td>
                             </tr>
                             </tbody>
                         </table>';

        $html .= '<table class="table border table-sm">
                    
                    <tbody>
                        <tr class="text-center">
                            <td class="color primary border twice" colspan="10"><b>Employee Information</b></td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Employee ID</td>
                            <td colspan="2"  class="border twice-right">' . $employee->trax_id . '</td>
                            <td colspan="3" class="border twice-right">Location</td>
                            <td colspan="3"  class="border twice-right">' . $employee->city->name . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Employee Name</td>
                            <td colspan="2"  class="border twice-right">' . $employee->name . '</td>
                            <td colspan="3" class="border twice-right">Department</td>
                            <td colspan="3"  class="border twice-right">' . $employee->department->name . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Designation</td>
                            <td colspan="2"  class="border twice-right">' . $employee->designation->name . '</td>
                            <td colspan="3"  class="border twice-right">Employee Type</td>
                            <td colspan="3"  class="border twice-right">' . $employee->employee_type->name . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Shift</td>
                            <td colspan="2"  class="border twice-right">' . $employee->shift->name . '</td>
                        </tr>
                        <tr class="text-center">
                            <td class="color primary border twice" colspan="10"><b>Attandence Details</b></td>
                        </tr>
                        <tr>
                            <td class="color primary border twice"><b>Day</b></td>
                            <td class="color primary border twice"><b>Date In</b></td>
                            <td class="color primary border twice"><b>Time In</b></td>
                            <td class="color primary border twice"><b>Date Out</b></td>
                            <td class="color primary border twice"><b>Time Out</b></td>
                            <td class="color primary border twice"><b>Work Hours</b></td>
                            <td class="color primary border twice"><b>Late Arrival</b></td>
                            <td class="color primary border twice"><b>Early Departure</b></td>
                            <td class="color primary border twice"><b>Overtime</b></td>
                            <td class="color primary border twice"><b>Remarks</b></td>
                        </tr>
                   ';
        foreach ($employee_attendances as $employee_attendance){

            $date_in = Carbon::parse($employee_attendance->attendance_date)->format("Y-m-d");
            $day = Carbon::parse($employee_attendance->attendance_date)->format("l");
            $time_in = '';
            $time_out = '';
            $date_out = '';
            $working_hours = '';
            $early_departure = '';
            $late_arrival = '';
            $over_time = '';

            $total++;
            $remarks = '';
            if($employee_attendance->leave_status){
                $remarks = "Leave";
                $leave++;
            }else{
                if(!$employee_attendance->clock_in_datetime){
                    if (Carbon::parse($employee_attendance->attendance_date)->format("l") == "Sunday"){
                        $remarks = "Sunday";
                        $sunday++;
                    }else{
                        $remarks = "Absent";
                        $absent++;
                    }
                }
                else{
                    $total_presents++;
                    $time_in = Carbon::parse($employee_attendance->clock_in_datetime)->format("H:i:s");
                    if($employee_attendance->clock_out_datetime){
                        $time_out = Carbon::parse($employee_attendance->clock_out_datetime)->format("H:i:s");
                        $date_out = Carbon::parse($employee_attendance->clock_out_datetime)->format("Y-m-d");
                    }
                    $shift = EmployeeShift::find($employee->shift_id);
                    if ($shift){
                        $expected_clockin = Carbon::createFromFormat('Y-m-d H:i:s', $employee_attendance->attendance_date.$shift->start_time)->addMinutes((int)$shift->grace_time);
                        $clock_in = Carbon::parse($employee_attendance->clock_in_datetime);
                        $time_diff = $expected_clockin->diffInMinutes(Carbon::parse($clock_in), false);
                        if ($time_diff > 0) {
                            $remarks = 'Late';
                            $late++;
                            $late_arrival = Carbon::parse($clock_in)->diff(Carbon::parse($shift->start_time))->format('%H:%I:%S');
                        }else{
                            $remarks = 'On Time';
                            $ontime++;
                        }
                        if($employee_attendance->clock_out_datetime){
                            $shift_minutes = Carbon::parse($shift->start_time)->diffInMinutes(Carbon::parse($shift->end_time), false);
                            if($shift_minutes < 0){
                                $expected_clockout = Carbon::createFromFormat('Y-m-d H:i:s', $employee_attendance->attendance_date.$shift->end_time)->addDay();
                            }else{
                                $expected_clockout = Carbon::createFromFormat('Y-m-d H:i:s', $employee_attendance->attendance_date.$shift->end_time);
                            }
                            $time_diff_out = $expected_clockout->diffInMinutes(Carbon::parse($employee_attendance->clock_in_datetime), false);
                            $working_hours = Carbon::parse($employee_attendance->clock_out_datetime)->diff(Carbon::parse($employee_attendance->clock_in_datetime))->format('%H:%I:%S');
                            if($time_diff_out < 0){
                                $remarks .= ' - Early Out';
                                $earlyout++;
                                $early_departure = Carbon::parse($time_out)->diff(Carbon::parse($shift->end_time))->format('%H:%I:%S');
                            } elseif ($time_diff_out > 0){
                                $remarks .= ' - Overtime';
                                $over_time = Carbon::parse($time_out)->diff(Carbon::parse($shift->end_time))->format('%H:%I:%S');
                            } else {
                                $remarks .= ' - On Time';
                            }
                        }
                    }
                }
            }
            $html .= '<tr>';
            $html .= '<td class="border twice-right">' . $day . '</td>';
            $html .= '<td class="border twice-right">' . $date_in . '</td>';
            $html .= '<td class="border twice-right">' . $time_in . '</td>';
            $html .= '<td class="border twice-right">' . $date_out . '</td>';
            $html .= '<td class="border twice-right">' . $time_out . '</td>';
            $html .= '<td class="border twice-right">' . $working_hours . '</td>';
            $html .= '<td class="border twice-right">' . $late_arrival . '</td>';
            $html .= '<td class="border twice-right">' . $early_departure . '</td>';
            $html .= '<td class="border twice-right">' . $over_time . '</td>';
            $html .= '<td class="border twice-right">' . $remarks . '</td>';
        }
        $html .= '    </tbody>
                      </table>';

        $html.='<table class="table border table-sm">
                    <tbody>
                        <tr class="text-center">
                            <td class="color primary border twice" colspan="10"><b>Attendance Summary</b></td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Total : '.$total.'</td>
                            <td colspan="2" class="border twice-right">Total Present : '.$total_presents.'</td>
                            <td colspan="3" class="border twice-right">Absent : '.$absent.'</td>
                            <td colspan="3" class="border twice-right">On Time : '.$ontime.'</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Late : '.$late.'</td>
                            <td colspan="2" class="border twice-right">Early Departure : '.$earlyout.'</td>
                            <td colspan="3" class="border twice-right">Offdays : '.$sunday.'</td>
                            <td colspan="3" class="border twice-right">Leave : '.$leave.'</td>
                        </tr>
                        </tbody>
                      </table>
                        ';
        $html .= ' 
                      </div>
                      </body>
                      </html>';

        $pdf = SnappyPDF::loadHTML($html);
        $filename = 'Attendance' . $trax_id . '.pdf';
        return $pdf->download($filename);
    }


}
