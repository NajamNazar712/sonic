<?php

namespace App\Http\Controllers\Admins\Attendance;

use App\Http\Controllers\Admins\ActivityTrailController;
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
        ActivityTrailController::createActivityTrailLog(Auth::id(),56);
        $cities = City::select('id','name')->get();
        $departments = AdminDepartment::select('id','name')->get();
        $users = Admin::select('id','name')->get();
        $trax_id = Admin::wherenotnull('trax_id')->pluck('trax_id')->toArray();
        $rider_trax_id = Rider::wherenotnull('trax_id')->pluck('trax_id')->toArray();
        $trax_ids = array_merge($trax_id, $rider_trax_id);
        $riders = Rider::where('status', 1)->select('id', 'name')->get();
        return view('admin.attendance.admin.index')->with(["departments" => $departments, "cities" => $cities, "admins" => $users, "trax_ids" => $trax_ids, "riders" => $riders]);
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
            ->select('a.name as admin_name', 'a.trax_id as trax_id', 'c.name as city_name', 'c.id as city_id', 'a.designation as designation', 'r.name as rider_name', 'r.trax_id as rider_trax_id', 'rc.name as rider_city_name', 'rc.id as rider_city_id', 'rt.name as rider_type', 'rt.id as rider_type_id', 'employee_attendances.attendance_date as attendance_date', 'employee_attendances.clock_in as clock_in', 'employee_attendances.clock_out as clock_out', 'employee_attendances.clock_in_latitude as clock_in_latitude', 'employee_attendances.clock_in_longitude as clock_in_longitude', 'employee_attendances.clock_out_latitude', 'employee_attendances.clock_out_longitude', 'ad.name as department', 'ad.id', 'employee_attendances.employee_type', 'employee_attendances.clock_in_location as clock_in_status', 'employee_attendances.clock_out_location as clock_out_status');

        if (session('role_id') != 1) {
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
                    return "ON-Site";
                } else if ($employee->clock_in_status == 1) {
                    return "OFF-Site";
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
                    return "ON-Site";
                } else if ($employee->clock_out_status == 1) {
                    return "OFF-Site";
                } else {
                    return "";
                }
            });

        if ($search_admin = $request->get('search_admin')) {
            $datatable->where('a.id', $search_admin);
        }
        if ($search_rider = $request->get('search_rider')) {
            $datatable->where('r.id', $search_rider);
        }
        if ($search_city = $request->get('search_city')) {
            $datatable->where('c.id', $search_city)->orWhere('rc.id', $search_city);
        }
        if ($search_department = $request->get('search_department')) {
            $datatable->where('ad.id', $search_department);
        }
        if ($search_trax_id = $request->get('search_trax_id')) {
            $datatable->where('a.trax_id', $search_trax_id)
                ->orWhere('r.trax_id', $search_trax_id);
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('employee_attendances.attendance_date', [$from, $to]);
        }

        return $datatable->make(true);
    }

}
