<?php

namespace App\Http\Controllers\Admins\Attendance;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\Attendance\EmployeeAttendanceActionLog;
use App\Http\Models\Admin\RiderType;
use App\Http\Models\City;
use App\Http\Models\HR\Employee;
use App\http\Models\ReportingLocation;
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

    public function admin_attendance_index(Request $request){
        ActivityTrailController::createActivityTrailLog(Auth::id(),56);
        $cities = City::select('id','name')->get();
        $departments = AdminDepartment::select('id','name')->get();
        $users = Admin::where('status', 1)->select('id','name')->get();
        $trax_id = Admin::wherenotnull('trax_id')->pluck('trax_id')->toArray();
        $rider_trax_id = Rider::wherenotnull('trax_id')->pluck('trax_id')->toArray();
        $trax_ids = array_merge($trax_id, $rider_trax_id);
        $admin_cnic = Admin::wherenotnull('cnic')->where('status', 1)->pluck('cnic')->toArray();
        $rider_cnic = Rider::wherenotnull('cnic')->where('status', 1)->pluck('cnic')->toArray();
        $cnic = array_merge($admin_cnic, $rider_cnic);
        $riders = Rider::where('status', 1)->select('id', 'name')->get();
        return view('admin.attendance.admin.index')->with(["departments" => $departments, "cities" => $cities, "admins" => $users, "trax_ids" => $trax_ids, "riders" => $riders, "cnics"=>$cnic]);
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
            ->select('a.name as admin_name', 'a.trax_id as trax_id', 'c.name as city_name', 'c.id as city_id', 'a.designation as designation', 'r.name as rider_name', 'r.trax_id as rider_trax_id', 'rc.name as rider_city_name', 'rc.id as rider_city_id', 'rt.name as rider_type', 'rt.id as rider_type_id', 'employee_attendances.attendance_date as attendance_date', 'employee_attendances.clock_in as clock_in', 'employee_attendances.clock_out as clock_out', 'employee_attendances.clock_in_latitude as clock_in_latitude', 'employee_attendances.clock_in_longitude as clock_in_longitude', 'employee_attendances.clock_out_latitude', 'employee_attendances.clock_out_longitude', 'ad.name as department', 'ad.id as department_id', 'employee_attendances.employee_type', 'employee_attendances.clock_in_location as clock_in_status', 'employee_attendances.clock_out_location as clock_out_status', 'r.cnic as rider_cnic', 'a.cnic as admin_cnic');

        /*if (session('role_id') != 1) {
            $attendances = $attendances->whereIn('c.hub_id', session('hubs'));
        }*/

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
                $datatable->where('employee_type',2);
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

    public function mark_attendance_index(){
        $date = Carbon::now();
        dd($date);
        ActivityTrailController::createActivityTrailLog(Auth::id(),432);
        $today_date = Carbon::today()->toDateString();
        $attendance_clock_in = EmployeeAttendance::where('employee_id',Auth::id())->where('attendance_date',$today_date)->whereNotNull('clock_in')->whereNull('clock_out')->latest()->first();
         $clock_in = 0;
        $clock_out = 0;
        if($attendance_clock_in){
            $clock_in = 1;
        }
        if(isset($attendance_clock_in) && $attendance_clock_in->clockout != NULL){
            $clock_out = 1;
        }
        return view('admin.attendance.mark_index',compact('clock_in','clock_out'));
    }

    public function mark_attendance_submit(Request $request){
        
        $date_time = Carbon::now();
        $time = $date_time->toTimeString();
        $date = Carbon::today()->toDateString();
        $employee_type = 0;
        $auth_id = Auth::id();
        $admin = Admin::where('id',$auth_id);
        if($admin->exists()){
            $employee_type = 1;
        }
        else{
            $employee_type = 2;
        }
        if(session('latitude') && session('longitude')){

            $location_status = 0;
            $reporting_location = ReportingLocation::join('employees as e', 'reporting_locations.id', 'e.reporting_location_id')
                ->join('admins as a', 'e.id', 'a.employee_id')
                ->where('a.id', $auth_id);
            
            if($reporting_location->exists()){
                $reporting_location = $reporting_location->first();
                $reporting_location->radius;
                $destination = $reporting_location->lat . ',' . $reporting_location->long;
                $origin = session('latitude') . ',' . session('longitude');
                $distance = $this->distance($origin, $destination);
                if ($distance > $reporting_location->radius / 1000) {
                    $location_status = 1;
                } else {
                    $location_status = 2;
                }
            }

             if($request->clock_in == 1 && $request->clock_out == 0){
                $employee_attendance = EmployeeAttendance::where('employee_id',$auth_id)->where('attendance_date',$date)->latest();
                if($employee_attendance->exists()){
                    $attendance = $employee_attendance->first();
                    $attendance->clock_out = $time;
                    $attendance->clock_out_latitude = session('latitude');
                    $attendance->clock_out_longitude = session('longitude');
                    $attendance->updated_at = Carbon::now();
                    $attendance->employee_type = $employee_type;
                    $attendance->clock_out_location = $location_status;
                    $attendance->save();

                    $attendance_action_log = new EmployeeAttendanceActionLog();
                    $attendance_action_log->employee_id = $attendance->employee_id;
                    $attendance_action_log->employee_type = $employee_type;
                    $attendance_action_log->action_id = 2;
                    $attendance_action_log->action_date = $date;
                    $attendance_action_log->latitude =  $attendance->clock_out_latitude;
                    $attendance_action_log->longitude =  $attendance->clock_out_longitude;
                    $attendance_action_log->created_at =  Carbon::now();
                    $attendance_action_log->location_status = $location_status;
                    $attendance_action_log->save();

                    return response()->json(['status' => 2, 'success' => 'Clock Out Successful','time' => $time,'date' => $date]);
                }
            }
             else if(EmployeeAttendance::where('employee_id', $auth_id)->where('attendance_date', $date)->exists()){
                     $attendance_action_log = EmployeeAttendanceActionLog::where('employee_id',$auth_id)->where('action_date',$date)->latest()->first();
                     $employee_attendance = EmployeeAttendance::where('employee_id', $auth_id)->where('attendance_date', $date)->latest();
                     if ($employee_attendance->exists()) {
                         $attendance = $employee_attendance->first();
                         if($attendance_action_log->action_id == 1) {
                         $attendance->clock_out = $time;
                         $attendance->clock_out_latitude = session('latitude');
                         $attendance->clock_out_longitude = session('longitude');
                         $attendance->updated_at = Carbon::now();
                         $attendance->employee_type = $employee_type;
                         $attendance->clock_out_location = $location_status;
                         $attendance->save();
                         $msg = 'Clock Out Successful';
                         $status = 2;
                         $action_id = 2;
                     }
                     else{
                         $attendance->attendance_date = $date ;
                         $attendance->clock_in = $time;
                         $attendance->clock_in_latitude = session('latitude');
                         $attendance->clock_in_longitude = session('longitude');
                         $attendance->updated_at = Carbon::now();
                         $attendance->employee_type = $employee_type;
                         $attendance->clock_in_location = $location_status;
                         $attendance->save();
                         $msg = 'Clock In Successful';
                         $status = 1;
                         $action_id = 1;
                     }

                         $attendance_action_log = new EmployeeAttendanceActionLog();
                         $attendance_action_log->employee_id = $attendance->employee_id;
                         $attendance_action_log->employee_type = $employee_type;
                         $attendance_action_log->action_id = $action_id;
                         $attendance_action_log->action_date = $date;
                         $attendance_action_log->latitude =  $attendance->clock_out_latitude;
                         $attendance_action_log->longitude =  $attendance->clock_out_longitude;
                         $attendance_action_log->created_at =  Carbon::now();
                         $attendance_action_log->location_status = $location_status;
                         $attendance_action_log->save();

                         return response()->json(['status' => $status, 'success' => $msg,'time' => $time,'date' => $date]);
                 }
             }
             else{
                 $attendance = new EmployeeAttendance();
                 $attendance->employee_id = $auth_id;
                 $attendance->attendance_date = $date ;
                 $attendance->clock_in = $time;
                 $attendance->clock_in_latitude = session('latitude');
                 $attendance->clock_in_longitude = session('longitude');
                 $attendance->created_at = Carbon::now();
                 $attendance->employee_type = $employee_type;
                 $attendance->clock_in_location = $location_status;
                 $attendance->save();

                 $attendance_action_log = new EmployeeAttendanceActionLog();
                 $attendance_action_log->employee_id = $attendance->employee_id;
                 $attendance_action_log->employee_type = $employee_type;
                 $attendance_action_log->action_id = 1;
                 $attendance_action_log->action_date = $date;
                 $attendance_action_log->latitude =  $attendance->clock_in_latitude;
                 $attendance_action_log->longitude =  $attendance->clock_in_longitude;
                 $attendance_action_log->created_at =  Carbon::now();
                 $attendance_action_log->location_status = $location_status;
                 $attendance_action_log->save();

                 return response()->json(['status' => 1, 'success' => 'Clock In Successful','time' => $time,'date' => $date]);

             }
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Enable Your Location First']);
        }

    }

    public function mark_attendance_list(){

        $date = Carbon::today()->toDateString();
        $admin_attendance_action = EmployeeAttendanceActionLog::where('employee_id', Auth::id())
            ->whereDate('action_date',$date)
            ->where('employee_type', 1)
            ->select('latitude', 'longitude','action_id','created_at')
            ->orderBy('action_date', 'ASC');

        $datatable = Datatables::of($admin_attendance_action)
            ->editColumn('latitude',function($action){
                $api = 'https://maps.googleapis.com/maps/api/geocode/json?sensor=true&latlng='.$action->latitude.','.$action->longitude.'&key=AIzaSyAIg5c-H5DaYBwF_D0HuWliQZQ6XzKj8Nk';
                $data = json_decode(file_get_contents($api));
                $data_array = get_object_vars($data);
                $result = $data_array['results'][0]->formatted_address;
                return $result;
            })
            ->editColumn('action_id',function ($data){
               if($data->action_id == 1){
                   return 'Clock-In';
               }
               else{
                   return 'Clock-Out';
               }
            })
        ;
           return $datatable->make(true);
    }


}
