<?php

namespace App\Http\Controllers\Admins\Attendance;

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
        return view('admin.attendance.admin.index')->with(["department" => $departments, "city" => $cities]);
    }

    public function admin_attendance_list(Request $request)
    {

        $admin_attendance = AdminAttendance::join('admins as a', 'a.id', 'admin_attendances.admin_id')
            ->leftjoin('cities as c', 'c.id', 'a.default_hub_id')
            ->leftjoin('admin_roles as ar','ar.id', 'a.role_id' )
            ->leftjoin('admin_departments as ad', 'ad.id', 'ar.department_id')
            ->select('a.name as admin_name', 'a.trax_id as trax_id', 'c.name as city_name', 'c.id', 'a.designation as designation', 'admin_attendances.attendance_date as attendance_date', 'admin_attendances.clock_in as clock_in', 'admin_attendances.clock_out as clock_out', 'admin_attendances.clock_in_latitude as clock_in_latitude', 'admin_attendances.clock_in_longitude as clock_in_longitude', 'admin_attendances.clock_out_latitude', 'admin_attendances.clock_out_longitude', 'ad.name as department', 'ad.id');

        if (session('role_id') != 1) {
            $admin_attendance = $admin_attendance->whereIn('c.hub_id', session('hubs'));
        }
        return Datatables::of($admin_attendance)
            ->addColumn("clock_in_location", function ($admin_attendance) {
                if($admin_attendance->clock_in_latitude && $admin_attendance->clock_in_longitude){
                    $clock_in = '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href="http://maps.google.com/maps?saddr=' . $admin_attendance->clock_in_latitude . ',' . $admin_attendance->clock_in_longitude . '" target="_blank"><i class="la la-map-marker"></i> View</a></div>';
                }else{
                    $clock_in = '-';
                }
                return $clock_in;
            })
            ->addColumn("clock_out_location", function ($admin_attendance) {
                if($admin_attendance->clock_out_latitude && $admin_attendance->clock_out_longitude){
                    $clock_out = '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href="http://maps.google.com/maps?saddr=' . $admin_attendance->clock_out_latitude . ',' . $admin_attendance->clock_out_longitude . '" target="_blank"><i class="la la-map-marker"></i> View</a></div>';
                }else{
                    $clock_out = '-';
                }

                return $clock_out;
            })
            ->make(true);


    }
}
