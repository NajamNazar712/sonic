<?php

namespace App\Http\Controllers\Admins;


use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Allowances;
use App\Http\Models\City;
use App\Http\Models\EmployeeRequisition;
use App\Http\Models\EmployeeRequisitionAllowances;
use App\Http\Models\EmployeeRequisitionReplacement;
use App\Http\Models\EmployeeRequisitionStatus;
use App\Http\Models\EmployeeRequisitionStatusLog;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeDesignation;
use App\Models\Admin\AdminPositionTypes;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class AdminERFController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function index(){
       
        $erf_status = EmployeeRequisitionStatus::select('id','name')->get();
        return view('admin.human_resource.erf.index')->with(['erf_status' => $erf_status]);
    }

    public function list(Request $request){
        $erf = EmployeeRequisition::join('admins as a', 'a.id', '=', 'employee_requisitions.department_head_id')
            ->join('cities as c', 'c.id', '=', 'employee_requisitions.city_id')
            ->join('cities as h', 'h.id', '=', 'employee_requisitions.hub_id')
            ->join('employee_designations as d', 'd.id', '=', 'employee_requisitions.designation_id')
            ->join('admin_departments as dp', 'dp.id', '=', 'employee_requisitions.department_id')
            ->join('employee_requisition_statuses as s', 's.id', '=', 'employee_requisitions.status_id')
            ->select(['employee_requisitions.id as erf_id', 'a.name as admin','c.name as city','h.name as hub','d.name as designation','dp.name as department','s.name as status','s.id']);



        $datatables = Datatables::of($erf)
            ->addColumn('actions',function ($erf) {
              /*  $view_charges_button = '<button type="button" class="dropdown-item view_charges"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Charges</div></button>';*/
                $view_button = '<button type="button" class="dropdown-item view_print"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-crosshair"></i></div><div class="col-9 offset-1">View</div></button>';
               /* $dispute_button = '<button type="button" class="dropdown-item dispute_modal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-circle"></i></div><div class="col-9 offset-1">Dispute</div></button>';*/



                    $dropdown = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                    ';

                        $dropdown .= $view_button;

                    $dropdown .= '
                            </div>
                        </div>
                    ';
                return $dropdown;
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
        $employee_trax_id = Employee::select('trax_id')->get();

        return view('admin.human_resource.erf.add')->with(['cities' => $cities,'hubs' => $hubs,'departments' => $departments,'designations' => $designations,'department_heads' => $department_heads,'admin_positions' => $admin_positions,'allowances' => $allowances,'employee_trax_id' => $employee_trax_id]);
    }

    public function submit(Request $request){

        $erf = new EmployeeRequisition();
        $erf->department_id = $request->department;
        $erf->designation_id = $request->designation;
        $erf->hub_id = $request->hub;
        $erf->city_id =  $request->city;
        $erf->department_head_id =  $request->department_head;
        $erf->status_id = 1;
        $erf->type = $request->erf_type;
        $erf->submitted_by = Auth::id();
        $erf->vacancies = $request->vacancies;
        $erf->position_type_id = $request->position;
        $erf->salary_from = $request->salary_from;
        $erf->salary_to = $request->salary_to;
        $erf->qualifications = $request->qualification;
        $erf->skills = $request->skills;
        $erf->job_description = $request->job_description;
        $erf->save();

       if($request->erf_type == 1){
           foreach($request->allowances as $allowance){
               $assigned_benefits = new EmployeeRequisitionAllowances();
               $assigned_benefits->er_id = $erf->id;
               $assigned_benefits->allowance_id = $allowance;
               $assigned_benefits->save();
           }
       }
       else{

          foreach($request->addmore as $data){

             $replacement = new EmployeeRequisitionReplacement();
             $replacement->er_id = $erf->id;
             $replacement->trax_id = $data['trax_id'];
             $replacement->last_gross_salary = $data['salary'];
             $replacement->date = $data['date'];
             $replacement->save();
          }
       }

       $log = new EmployeeRequisitionStatusLog();
       $log->er_id = $erf->id ;
       $log->admin_id = Auth::id();
       $log->status_id = 1;
        $log->save();

        $email = $request->admin_email;
        $path = $this::erf_print($erf->id);
        $data['id'] = $erf->id;
        $data['email'] = $email;
        NotificationsController::send(133, $data, url('/') . '/' . 'reports/employee_requisition_'. str_pad($erf->id, 6, '0', STR_PAD_LEFT) .'.pdf');
        return view('admin.human_resource.erf.index')->with(['success' => 'Request Submitted']);

    }

    public static function erf_print($erf_id) {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $erf = EmployeeRequisition::find($erf_id);

        $city = City::find($erf->city_id);
        $hub = City::where('hub',1)->where('id',$erf->hub_id)->first();
        $department = AdminDepartment::find($erf->department_id);
        $line_manager = Admin::find($erf->department_head_id);
        if($erf->type == 1){
            $position_type = AdminPositionTypes::find($erf->position_type_id);
            $designation = EmployeeDesignation::find($erf->designation_id);
            $type = 'Additional';
            $benefits = EmployeeRequisitionAllowances::join('allowances as a','a.id','=','employee_requisition_allowances.allowance_id')->where('er_id',$erf->id)->select('a.name as name')->get();
        }



        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Cargo Slip & Checklist</title>

                     <style>
                      body {
                        font-size: 0.75rem !important;
                        font-weight: bold !important;
                      }

                      td.replacement span {
                        width: auto !important;
                      }

                      .border.twice {
                        border-width: 1px !important;
                      }

                      .border.twice-top {
                        border-top-width: 1px !important;
                      }

                      .border.twice-bottom {
                        border-bottom-width: 1px !important;
                      }

                      .border.twice-left {
                        border-left-width: 1px !important;
                      }

                      .border.twice-right {
                        border-right-width: 1px !important;
                      }

                      .font-small {
                        font-size: 0.65rem !important;
                      }
                    </style>';

        $html .= '</head>
                  <body>
                   
                      <div class="erf_slip">
                          <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr colspan="3">
                              <td class="text-center align-middle" rowspan="6"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                       
                                 <td class="text-center align-middle color secondary" >Employee Requisition</td>
                              </tr>
                              <tr colspan="3"></tr>
                              <tr colspan="3">
                                  <td class="text-center align-middle color secondary" >ERF ID : '.$erf->id.'</td>
                              </tr>
                              <tr>
                                  <td class="text-center align-middle color secondary" >Requested Date : '.$erf->created_at.'</td>
                                  </tr>
                              <tr>
                                  <td class="text-center align-middle color secondary" >Status : '.$erf->status->name.'</td>
                                  </tr>
                              <tr>
                                  <td class="text-center align-middle color secondary" >ERF Type : '.$type.'</td>
                                  
                              </tr>
                            <tr>
                         
                              <td class="color secondary"><strong>Department</strong></td>
                              <td>' . $department->name . '</td>
                            
                                 <td class="color secondary"> <strong>Position Type</strong></td>
                               <td>' . $position_type->name . '</td>
                          
                            
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Designation</strong></td>
                              <td>' . $designation->name . '</td>
                               <td class="color secondary"><strong>Employee Category</strong></td>
                              <td>' . $department->name . '</td>
                            </tr>
                           
                            <tr>
                              <td class="color secondary"><strong>Hub</strong></td>
                              <td>' . $hub->name. '</td>
                               <td class="color secondary"><strong>City</strong></td>
                              <td>' . $city->name . '</td>
                            </tr>
                             
                             <tr>
                              <td class="color secondary"><strong>Salary From</strong></td>
                              <td>' . $erf->salary_from. '</td>
                               <td class="color secondary"><strong>Salary To</strong></td>
                              <td>' . $erf->salary_to. '</td>
                            </tr>
                            
                            <tr>
                              <td class="color secondary"><strong>Line Manager</strong></td>
                              <td>' . $line_manager->name. '</td>
                               <td class="color secondary"><strong>Vacancies</strong></td>
                              <td>' . $erf->vacancies. '</td>
                            </tr>
                         </tbody>
                         </table> 
                      </div>
                       
                              ';
        if($erf->type == 1){
            $html .= '<div class="additional">
                           <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="color secondary">Allowances/Benefits</td>
                       
                              <td class="text-center align-middle color secondary"> ';
            foreach($benefits as $benefit){
        $html .=        '<span class="badge badge-secondary mr-2">'.$benefit->name. '</span>';

            }

            $html .= '
                </td>
                </tr>
                            <tr>
                         
                              <td class="color secondary"><strong>Job Description</strong></td>
                              <td>' . $erf->job_description . '</td>
                              </tr>
                              <tr>
                            
                                 <td class="color secondary"> <strong>Required Skills</strong></td>
                               <td >' . $erf->skills . '</td>
                      
                            
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Qualification Required</strong></td>
                              <td>' . $erf->qualifications . '</td>
         
                            </tr>                
                         </tbody>
                         </table> 
                        </div>';
        }

        $status_logs = EmployeeRequisitionStatusLog::join('admins as a','a.id','=','employee_requisition_status_logs.admin_id')
            ->join('employee_requisition_statuses as ers','ers.id','=','employee_requisition_status_logs.status_id')
            ->where('employee_requisition_status_logs.er_id',$erf_id)->select('a.name as admin','ers.name as status','employee_requisition_status_logs.created_at as date')->get();

        $html .= '<div class="statuses">
            <table class="table table-sm table-bordered border">
               <thead>
                    <tr>
                      <th scope="col">Date & Time</th>
                      <th scope="col">User</th>
                      <th scope="col">Status</th>
        
                    </tr>
                </thead>
               <tbody>';
        foreach($status_logs as $log){

                $html .= '<tr>
                            <td>'.$log->date.'</td>
                            <td>'.$log->admin.'</td>
                            <td>'.$log->status.'</td>
                        </tr>';

             }

        $html .='
               
               </tbody>
            </table>
            </div>
            </body>
            </html>';

            $pdf = SnappyPDF::loadHTML($html)->save('reports/employee_requisition_'. str_pad($erf->id, 6, '0', STR_PAD_LEFT) .'.pdf');
            return $pdf;
        }
    }
