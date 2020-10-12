<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class AdminTraxDirectory extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function index()
    {
        $hubs=City::select('id','name')->where('hub',1)->get();
        return view('admin.trax_directory.index')->with(['hubs'=>$hubs]);
    }
    public function list(){
        $admin = Admin::join('admin_roles as ar','admins.role_id','=','ar.id')
            ->join('admin_departments as ad', 'ar.department_id', '=', 'ad.id')
            ->leftjoin('cities as h', 'h.id', '=', 'admins.default_hub_id')
            ->select('admins.name as name', 'admins.phone_number as phone','ad.name as department', 'admins.email as email', 'ar.name as role', 'admins.created_at as date','h.name as city')->
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
