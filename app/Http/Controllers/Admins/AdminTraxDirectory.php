<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Admins\ActivityTrailController;

class AdminTraxDirectory extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),52);
        $hubs=City::select('id','name')->where('hub',1)->get();
        return view('admin.trax_directory.index')->with(['hubs'=>$hubs]);
    }
    public function list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),112);
        }
        $admin = Admin::join('admin_roles as ar','admins.role_id','=','ar.id')
            ->join('admin_departments as ad', 'ar.department_id', '=', 'ad.id')
            ->leftjoin('employees as emp', 'emp.trax_id', '=', 'admins.trax_id')
            ->leftjoin('employee_designations as ed', 'ed.id', '=', 'emp.designation_id')
            ->leftjoin('cities as h', 'h.id', '=', 'admins.default_hub_id')
            ->select('admins.name as name', 'admins.phone_number as phone','ad.name as department', 'admins.email as email', 'admins.created_at as date','h.name as city','admins.official_phone_number as official_phone', 'emp.emergency_contact as emergency_contact', 'emp.emergency_contact_person as emergency_contact_person','ed.name as designation')->
            where('admins.status',1)->where('ar.id','!=',1);

        $datatable = Datatables::of($admin)
            ->editColumn('role', function($user) {
                return $user->role . ' - ' . $user->department;
            })
        ->editColumn('city',function($user){
            if($user->city != null){
                return $user->city;
            }
            else{
                return '-';
            }
        })->editColumn('designation',function($user){
            if($user->designation != null){
                return $user->designation;
            }
            else{
                return '-';
            }
        })
        ->filterColumn('role', function($query, $keyword) {
            $keyword = str_replace('-', '', strtolower($keyword));

            if ($keyword != '') {
                $query->where('ar.name', 'like', '%' . $keyword . '%')->orWhere('ad.name', 'like', '%' . $keyword . '%');
            }
        });

            return $datatable->make(true);
    }
}
