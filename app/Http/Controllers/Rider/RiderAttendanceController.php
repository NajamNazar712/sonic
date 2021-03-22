<?php

namespace App\Http\Controllers\Rider;

use App\Http\Models\Admin\RiderType;
use App\Http\Models\City;
use App\Http\Models\Rider\RiderAttendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class RiderAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function rider_attendance_index(Request $request){
        $rider_type = RiderType::select('id','name')->get();
        $cities = City::select('id','name')->get();
        return view('admin.attendance.rider.index')->with(["rider_type" => $rider_type, "city" => $cities]);
    }

    public function rider_attendance_list(Request $request)
    {

        $rider_attendance = RiderAttendance::join('riders as r', 'r.id', 'rider_attendances.rider_id')
            ->leftjoin('cities as c', 'c.id', 'r.city_id')
            ->leftjoin('rider_types as rt', 'rt.id', 'r.rider_type_id')
            ->select('r.name as rider_name', 'r.trax_id as trax_id', 'c.name as city_name', 'c.id', 'rt.name as rider_type','rt.id', 'rider_attendances.attendance_date as attendance_date', 'rider_attendances.clock_in as clock_in', 'rider_attendances.clock_out as clock_out', 'rider_attendances.clock_in_latitude as clock_in_latitude', 'rider_attendances.clock_in_longitude as clock_in_longitude', 'rider_attendances.clock_out_latitude', 'rider_attendances.clock_out_longitude');

        if (session('role_id') != 1) {
            $rider_attendance = $rider_attendance->whereIn('c.hub_id', session('hubs'));
        }
        return Datatables::of($rider_attendance)
            ->addColumn('department', function ($rider_attendance) {
                return 'Operations';
            })
            ->addColumn("clock_in_location", function ($rider_attendance) {
                if($rider_attendance->clock_in_latitude && $rider_attendance->clock_in_longitude){
                    $clock_in = '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href="https://www.google.com/maps/dir/' . $rider_attendance->clock_in_latitude . ',' . $rider_attendance->clock_in_longitude . '" target="_blank"><i class="la la-map-marker"></i> View</a></div>';
                }else{
                    $clock_in = '-';
                }
                return $clock_in;
            })
            ->addColumn("clock_out_location", function ($rider_attendance) {
                if($rider_attendance->clock_out_latitude && $rider_attendance->clock_out_longitude){
                    $clock_out = '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href="https://www.google.com/maps/dir/' . $rider_attendance->clock_out_latitude . ',' . $rider_attendance->clock_out_longitude . '" target="_blank"><i class="la la-map-marker"></i> View</a></div>';
                }else{
                    $clock_out = '-';
                }

                return $clock_out;
            })
            ->make(true);


    }
}
