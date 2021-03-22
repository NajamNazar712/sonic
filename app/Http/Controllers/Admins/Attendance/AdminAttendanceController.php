<?php

namespace App\Http\Controllers\Admins\Attendance;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\Attendance\AdminAttendance;
use App\Http\Models\City;
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

        $admin_attendance = AdminAttendance::join('admins as a', 'a.id', 'admin_attendances.admin_id')
            ->leftjoin('cities as c', 'c.id', 'a.default_hub_id')
            ->leftjoin('admin_roles as ar', 'ar.id', 'a.role_id')
            ->leftjoin('admin_departments as ad', 'ad.id', 'ar.department_id')
            ->select('a.name as admin_name', 'a.trax_id as trax_id', 'c.name as city_name', 'c.id', 'a.designation as designation', 'admin_attendances.attendance_date as attendance_date', 'admin_attendances.clock_in as clock_in', 'admin_attendances.clock_out as clock_out', 'admin_attendances.clock_in_latitude as clock_in_latitude', 'admin_attendances.clock_in_longitude as clock_in_longitude', 'admin_attendances.clock_out_latitude', 'admin_attendances.clock_out_longitude', 'ad.name as department', 'ad.id');

        if (session('role_id') != 1) {
            $admin_attendance = $admin_attendance->whereIn('c.hub_id', session('hubs'));
        }
        $datatable = Datatables::of($admin_attendance)
            ->addColumn("clock_in_location", function ($admin_attendance) {
                if ($admin_attendance->clock_in_latitude && $admin_attendance->clock_in_longitude) {
                    $clock_in = '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href="https://www.google.com/maps/dir/' . $admin_attendance->clock_in_latitude . ',' . $admin_attendance->clock_in_longitude . '"" target="_blank"><i class="la la-map-marker"></i> View</a></div>';
                } else {
                    $clock_in = '-';
                }
                return $clock_in;
            })
            ->addColumn("clock_out_location", function ($admin_attendance) {
                if ($admin_attendance->clock_out_latitude && $admin_attendance->clock_out_longitude) {
                    $clock_out = '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href="https://www.google.com/maps/dir/' . $admin_attendance->clock_out_latitude . ',' . $admin_attendance->clock_out_longitude . '" target="_blank"><i class="la la-map-marker"></i> View</a></div>';
                } else {
                    $clock_out = '-';
                }

                return $clock_out;
            });
        if ($search_admin = $request->get('search_admin')) {
            $datatable->where('admin_attendances.admin_id', $search_admin);
        }
        if ($search_city = $request->get('search_city')) {
            $datatable->where('c.id', $search_city);
        }
        if ($search_department = $request->get('search_department')) {
            $datatable->where('ad.id', $search_department);
        }
        if ($search_trax_id = $request->get('search_trax_id')) {
            $datatable->where('admin_attendances.admin_id', $search_trax_id);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('admin_attendances.attendance_date', [$from, $to]);
        }
        return $datatable->make(true);


    }
}
