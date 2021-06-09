<?php

namespace App\Http\Controllers\Admins;

use App\EmployeeRegistration;
use App\EmployeeRegistrationAllowance;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Allowances;
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
        $users = EmployeeRegistration::join('admins as a', 'a.id', '=', 'employee_registrations.department_head_id')
            ->join('cities as c', 'c.id', '=', 'employee_registrations.city_id')
            ->join('cities as h', 'h.id', '=', 'employee_registrations.hub_id')
            ->join('designation as d', 'd.id', '=', 'employee_registrations.designation_id')
            ->join('departments as dp', 'dp.id', '=', 'employee_registrations.department_id')
            ->select('employee_registrations.id', 'a.name')
            ->where('ar.id', '!=', 1);

    }

    public function add(){
        $cities = City::where('status',1)->where('business_category_id',1)->select('id','name')->get();
        $hubs =  City::where('hub',1)->select('id','name')->get();
        $departments = AdminDepartment::where('id', '!=', 1)->select('id', 'name')->get();
        $designations = EmployeeDesignation::select('id','name')->get();
        $department_heads = Admin::whereIn('role_id', [2, 3, 4, 6, 15, 18, 19, 22, 25, 34, 36])->where('status', 1)->select('id','name')->get();
        $admin_positions = AdminPositionTypes::select('id','name')->get();
        $allowances = Allowances::all();
        return view('admin.human_resource.erf.add')->with(['cities' => $cities,'hubs' => $hubs,'departments' => $departments,'designations' => $designations,'department_heads' => $department_heads,'admin_positions' => $admin_positions,'allowances' => $allowances]);
    }

    public function submit(Request $request){

        $erf = new EmployeeRegistration();
        $erf->department_id = $request->department;
        $erf->designation_id = $request->designation;
        $erf->hub_id = $request->hub;
        $erf->city_id =  $request->city;
        $erf->department_head_id =  $request->department_head;
        $erf->vacancies = $request->vacancies;
        $erf->position_type_id = $request->position;
        $erf->salary_from = $request->salary_from;
        $erf->salary_to = $request->salary_to;
        $erf->qualifications = $request->qualification;
        $erf->skills = $request->skills;
        $erf->job_description = $request->job_description;
        $erf->status = 1;
        $erf->type = 1;
        $erf->save();

        foreach($request->allowances as $allowance){
          $assigned_benefits = new EmployeeRegistrationAllowance();
          $assigned_benefits->erf_id = $erf->id;
          $assigned_benefits->allowance_id = $allowance;
          $assigned_benefits->save();
        }

        return view('admin.human_resource.erf.index')->with(['success' => 'Request Submitted']);

    }

}
