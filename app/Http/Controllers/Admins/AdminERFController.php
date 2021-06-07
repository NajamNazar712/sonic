<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\City;
use App\Http\Models\HR\EmployeeDesignation;
use App\Models\Admin\AdminPositionTypes;
use Illuminate\Http\Request;

class AdminERFController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function index(){
        return view('admin.human_resource.erf.index');
    }

    public function list(){
        //
    }

    public function add(){
        $cities = City::where('status',1)->where('business_category_id',1)->select('id','name')->get();
        $hubs =  City::where('hub',1)->select('id','name')->get();
        $departments = AdminDepartment::where('id', '!=', 1)->select('id', 'name')->get();
        $designations = EmployeeDesignation::select('id','name')->get();
        $department_heads = Admin::whereIn('role_id', [2, 3, 4, 6, 15, 18, 19, 22, 25, 34, 36])->where('status', 1)->select('id','name')->get();
        $admin_positions = AdminPositionTypes::select('id','name')->get();
        return view('admin.human_resource.erf.add')->with(['cities' => $cities,'hubs' => $hubs,'departments' => $departments,'designations' => $designations,'department_heads' => $department_heads,'admin_positions' => $admin_positions]);
    }
}
