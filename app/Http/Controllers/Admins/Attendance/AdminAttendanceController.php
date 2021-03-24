<?php

namespace App\Http\Controllers\Admins\Attendance;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\RiderType;
use App\Http\Models\City;
use App\Http\Models\Rider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;

class AdminAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function admin_attendance_index(Request $request){
        $cities = City::select('id','name')->get();
        $departments = AdminDepartment::select('id','name')->get();
        $users = Admin::select('id','name')->get();
        $trax_id = Admin::select('id','trax_id')->wherenotnull('trax_id')->get();
        return view('admin.attendance.admin.index')->with(["department" => $departments, "city" => $cities, "admins" => $users, "trax_id" => $trax_id]);
    }

    public function admin_attendance_list(Request $request)
    {

        $admin_attendances = EmployeeAttendance::join('admins as a', 'a.id', 'employee_attendances.employee_id')
            ->leftjoin('cities as c', 'c.id', 'a.default_hub_id')
            ->leftjoin('admin_roles as ar', 'ar.id', 'a.role_id')
            ->leftjoin('admin_departments as ad', 'ad.id', 'ar.department_id')
            ->select('a.name as admin_name', 'a.trax_id as trax_id', 'c.name as city_name', 'c.id', 'a.designation as designation', 'employee_attendances.attendance_date as attendance_date', 'employee_attendances.clock_in as clock_in', 'employee_attendances.clock_out as clock_out', 'employee_attendances.clock_in_latitude as clock_in_latitude', 'employee_attendances.clock_in_longitude as clock_in_longitude', 'employee_attendances.clock_out_latitude', 'employee_attendances.clock_out_longitude', 'ad.name as department', 'ad.id')
            ->where('employee_attendances.employee_type', 1);


        $rider_attendances = EmployeeAttendance::join('riders as r', 'r.id', 'employee_attendances.employee_id')
            ->leftjoin('cities as c', 'c.id', 'r.city_id')
            ->leftjoin('rider_types as rt', 'rt.id', 'r.rider_type_id')
            ->select('r.name as rider_name', 'r.trax_id as trax_id', 'c.name as city_name', 'c.id', 'rt.name as rider_type', 'rt.id', 'employee_attendances.attendance_date as attendance_date', 'employee_attendances.clock_in as clock_in', 'employee_attendances.clock_out as clock_out', 'employee_attendances.clock_in_latitude as clock_in_latitude', 'employee_attendances.clock_in_longitude as clock_in_longitude', 'employee_attendances.clock_out_latitude', 'employee_attendances.clock_out_longitude')
            ->where('employee_attendances.employee_type', 2);

        if (session('role_id') != 1) {
            $admin_attendances = $admin_attendances->whereIn('c.hub_id', session('hubs'));
            $rider_attendances = $rider_attendances->whereIn('c.hub_id', session('hubs'));
        }
        $users = array();
        if($admin_attendances->exists() || $rider_attendances->exists()){
            if ($admin_attendances->exists()) {
                $admin_attendances = $admin_attendances->get();
                foreach ($admin_attendances as $admin_attendance) {
                    $user = array();
                    $user['trax_id'] = $admin_attendance->trax_id;
                    $user['name'] = $admin_attendance->admin_name;
                    $user['city_name'] = $admin_attendance->city_name;
                    $user['designation'] = $admin_attendance->designation;
                    $user['department'] = $admin_attendance->department;
                    $user['attendance_date'] = $admin_attendance->attendance_date;
                    $user['clock_in'] = $admin_attendance->clock_in;
                    $user['clock_out'] = $admin_attendance->clock_out;
                    $href = "https://www.google.com/maps/search/?api=1&query=" . $admin_attendance->clock_in_latitude . ',' . $admin_attendance->clock_in_longitude;
                    if ($admin_attendance->clock_in_latitude && $admin_attendance->clock_in_longitude) {
                        $clock_in = '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href="https://www.google.com/maps/search/?api=1&query=' . $admin_attendance->clock_in_latitude . ',' . $admin_attendance->clock_in_longitude.'" target="_blank"><i class="la la-map-marker"></i> View</a></div>';
                    } else {
                        $clock_in = '-';
                    }
                    if ($admin_attendance->clock_out_latitude && $admin_attendance->clock_out_longitude) {
                        $clock_out = '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href="https://www.google.com/maps/search/?api=1&query=' . $admin_attendance->clock_out_latitude . ',' . $admin_attendance->clock_out_longitude.'" target="_blank"><i class="la la-map-marker"></i> View</a></div>';
                    } else {
                        $clock_out = '-';
                    }
                    $user['clock_in_location'] = $clock_in;
                    $user['clock_out_location'] = $clock_out;
                    $user['employee_type'] = "Staff";
                    $users[] = $user;
                    $users = collect($users);
                }
            }
            if ($rider_attendances->exists()) {
                $rider_attendances = $rider_attendances->get();
                foreach ($rider_attendances as $rider_attendance) {
                    $user = array();
                    $user['trax_id'] = $rider_attendance->trax_id;
                    $user['name'] = $rider_attendance->rider_name;
                    $user['city_name'] = $rider_attendance->city_name;
                    $user['designation'] = $rider_attendance->rider_type;
                    $user['department'] = "Operations";
                    $user['attendance_date'] = $rider_attendance->attendance_date;
                    $user['clock_in'] = $rider_attendance->clock_in;
                    $user['clock_out'] = $rider_attendance->clock_out;
                    if ($rider_attendance->clock_in_latitude && $rider_attendance->clock_in_longitude) {
                        $clock_in = '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href="https://www.google.com/maps/search/?api=1&query=' . $rider_attendance->clock_in_latitude . ',' . $rider_attendance->clock_in_longitude.'" target="_blank"><i class="la la-map-marker"></i> View</a></div>';
                    } else {
                        $clock_in = '-';
                    }
                    if ($rider_attendance->clock_out_latitude && $rider_attendance->clock_out_longitude) {
                        $clock_out = '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href="https://www.google.com/maps/search/?api=1&query=' . $rider_attendance->clock_out_latitude . ',' . $rider_attendance->clock_out_longitude.'" target="_blank"><i class="la la-map-marker"></i> View</a></div>';
                    } else {
                        $clock_out = '-';
                    }
                    $user['clock_in_location'] = $clock_in;
                    $user['clock_out_location'] = $clock_out;
                    $user['employee_type'] = "Rider";
                    $users[] = $user;
                    $users = collect($users);
                }
            }
        }
        else{
            $user = array();
            $user['trax_id'] = '';
            $user['name'] = '';
            $user['city_name'] = '';
            $user['designation'] = '';
            $user['department'] = '';
            $user['attendance_date'] = '';
            $user['clock_in'] = '';
            $user['clock_out'] = '';
            $user['clock_in_location'] = '';
            $user['clock_out_location'] = '';
            $user['employee_type'] = '';
            $users[] = $user;
            $users = collect($users);
        }
        $datatable = Datatables::of($users);
        /*if ($search_admin = $request->get('search_admin')) {
            $datatable->filterColumn('users.name', $search_admin);
        }
        if ($search_city = $request->get('search_city')) {
            $datatable->where('city_name', $search_city);
        }
        if ($search_department = $request->get('search_department')) {
            $datatable->where('department', $search_department);
        }
        if ($search_trax_id = $request->get('search_trax_id')) {
            $datatable->where('trax_id', $search_trax_id);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('attendance_date', [$from, $to]);
        }*/
         return $datatable->make(true);
    }

    /*public function rider_attendance_index(Request $request)
    {
        $cities = City::select('id','name')->get();
        $users = Rider::select('id','name')->get();
        $trax_id = Rider::select('id','trax_id')->wherenotnull('trax_id')->get();
        return view('admin.attendance.rider.index')->with(["city" => $cities, "admins" => $users, "trax_id" => $trax_id]);
    }
    public function rider_attendance_list(Request $request)
    {

        $rider_attendance = EmployeeAttendance::join('riders as r', 'r.id', 'employee_attendances.employee_id')
            ->leftjoin('cities as c', 'c.id', 'r.city_id')
            ->leftjoin('rider_types as rt', 'rt.id', 'r.rider_type_id')
            ->select('r.name as rider_name', 'r.trax_id as trax_id', 'c.name as city_name', 'c.id', 'rt.name as rider_type', 'rt.id', 'employee_attendances.attendance_date as attendance_date', 'employee_attendances.clock_in as clock_in', 'employee_attendances.clock_out as clock_out', 'employee_attendances.clock_in_latitude as clock_in_latitude', 'employee_attendances.clock_in_longitude as clock_in_longitude', 'employee_attendances.clock_out_latitude', 'employee_attendances.clock_out_longitude')
            ->where('employee_type', 2);

        if (session('role_id') != 1) {
            $rider_attendance = $rider_attendance->whereIn('c.hub_id', session('hubs'));
        }
        $datatable = Datatables::of($rider_attendance)
            ->addColumn('department', function ($rider_attendance) {
                return 'Operations';
            })
            ->addColumn("clock_in_location", function ($rider_attendance) {
                if ($rider_attendance->clock_in_latitude && $rider_attendance->clock_in_longitude) {
                    $clock_in = '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href="https://www.google.com/maps/dir/' . $rider_attendance->clock_in_latitude . ',' . $rider_attendance->clock_in_longitude . '" target="_blank"><i class="la la-map-marker"></i> View</a></div>';
                } else {
                    $clock_in = '-';
                }
                return $clock_in;
            })
            ->addColumn("clock_out_location", function ($rider_attendance) {
                if ($rider_attendance->clock_out_latitude && $rider_attendance->clock_out_longitude) {
                    $clock_out = '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href="https://www.google.com/maps/dir/' . $rider_attendance->clock_out_latitude . ',' . $rider_attendance->clock_out_longitude . '" target="_blank"><i class="la la-map-marker"></i> View</a></div>';
                } else {
                    $clock_out = '-';
                }

                return $clock_out;
            });

        if ($search_rider = $request->get('search_rider')) {
            $datatable->where('employee_attendances.employee_id', $search_rider);
        }
        if ($search_city = $request->get('search_city')) {
            $datatable->where('c.id', $search_city);
        }
        if ($search_trax_id = $request->get('search_trax_id')) {
            $datatable->where('employee_attendances.employee_id', $search_trax_id);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('employee_attendances.attendance_date', [$from, $to]);
        }
        return $datatable->make(true);


    }*/

}
