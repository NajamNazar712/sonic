<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\EmployeeRegistration;
use App\Http\Models\EmployeeRegistrationAllowance;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Allowances;
use App\Http\Models\City;
use App\Http\Models\EmployeeRegistrationStatus;
use App\Http\Models\HR\EmployeeDesignation;
use App\Models\Admin\AdminPositionTypes;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class AdminERFController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function index(){
        $erf_status = EmployeeRegistrationStatus::all();
        return view('admin.human_resource.erf.index')->with(['erf_status' => $erf_status]);
    }

    public function list(Request $request){
        $erf = EmployeeRegistration::join('admins as a', 'a.id', '=', 'employee_registrations.department_head_id')
            ->join('cities as c', 'c.id', '=', 'employee_registrations.city_id')
            ->join('cities as h', 'h.id', '=', 'employee_registrations.hub_id')
            ->join('employee_designations as d', 'd.id', '=', 'employee_registrations.designation_id')
            ->join('admin_departments as dp', 'dp.id', '=', 'employee_registrations.department_id')
            ->join('employee_registration_statuses as s', 's.id', '=', 'employee_registrations.status')
            ->select('employee_registrations.id as id', 'a.name as admin','c.name as city','h.name as hub','d.name as designation','dp.name as department','s.name as status','s.id');



        $datatables = Datatables::of($erf)
            ->addColumn('actions', function($erf) {
                if (session('role_id') == 1 || in_array(188, session('permissions'))) {
                    return '<div class="btn-group">
                          <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                          <div class="dropdown-menu dropdown-menu-sm">
                            <button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                          </div>
                        </div>
                ';
                }
                else {
                    return '';
                }
            });

        if($status = $request->get('status')){
            $erf->where('employee_registrations.status', '=',$status);
        }


        return $datatables->make(true);

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
