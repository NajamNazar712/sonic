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
use App\Http\Models\EmployeeRequisitionAttachments;
use App\Http\Models\EmployeeRequisitionReplacement;
use App\Http\Models\EmployeeRequisitionStatus;
use App\Http\Models\EmployeeRequisitionStatusLog;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeDesignation;
use App\Http\Models\Admin\AdminPositionTypes;
use App\Http\Models\HR\EmployeePayslip;
use SnappyImage;
use SnappyPDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Admins\ActivityTrailController;

class AdminERFController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function index(){

        ActivityTrailController::createActivityTrailLog(Auth::id(),257);
        $erf_status = EmployeeRequisitionStatus::select('id','name')->get();
        return view('admin.human_resource.erf.index')->with(['erf_status' => $erf_status]);
    }

    public function list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),258);
        }
        $erf = EmployeeRequisition::join('admins as a', 'a.id', '=', 'employee_requisitions.department_head_id')
            ->join('cities as c', 'c.id', '=', 'employee_requisitions.city_id')
            ->join('cities as h', 'h.id', '=', 'employee_requisitions.hub_id')
            ->join('employee_designations as d', 'd.id', '=', 'employee_requisitions.designation_id')
            ->join('admin_departments as dp', 'dp.id', '=', 'employee_requisitions.department_id')
            ->join('employee_requisition_statuses as s', 's.id', '=', 'employee_requisitions.status_id')
            ->leftjoin('employee_requisition_attachments',function($join){
                $join->on('employee_requisition_attachments.er_id','=','employee_requisitions.id')
                    ->where('employee_requisition_attachments.created_at','=',DB::raw('(select max(created_at) from employee_requisition_attachments where employee_requisition_attachments.er_id= employee_requisitions.id)'));
            })
            ->leftjoin('admins as ar', 'ar.id', '=', 'employee_requisitions.submitted_by')
            ->select(['employee_requisitions.id as erf_id','employee_requisitions.id as id', 'a.name as admin','c.name as city','h.name as hub','d.name as designation','dp.name as department','s.name as status','employee_requisitions.status_id as status_id','employee_requisition_attachments.id as document','employee_requisitions.type as type','employee_requisitions.employee_status as es','a.trax_id as trax_id','employee_requisitions.submitted_by as requested_by','ar.name as requested_by_name'
            ]);
        if (session('role_id') != 1 && session('department_id') != 10) {
            $erf = $erf->where('dp.id', session('department_id'));
        }

        if(session('role_id') != 1)
        {
            $erf = $erf->whereIn('h.id',session('hubs'));
        }

        $datatables = Datatables::of($erf)
            ->editColumn('erf_id', function ($erf) {
                return "ERF" . $erf->erf_id;
            })
            ->editColumn('type',function($erf){
                if($erf->type == 1){
                    return 'Additional';
                }
                else{
                    return 'Replacement';
                }
            })
            ->editColumn('es',function($erf){
                if($erf->es == 1){
                    return 'Inactive';
                }
                else if($erf->es == 2){
                    return 'Notice Period';
                }
            })
            ->addColumn('aging',function ($erf){
                $log = EmployeeRequisitionStatusLog::where('er_id',$erf->id)->where('status_id',3);
                if($log->exists())
                {
                    if(EmployeeRequisitionStatusLog::where('er_id',$erf->id)->where('status_id',4)->doesntExist()) {
                        $log = $log->first();
                        $from = Carbon::parse($log->created_at);
                        $to = Carbon::now();
                        return $from->diffInDays($to);
                    }
                    return "-";
                }
                return "-";
            })
            ->filterColumn('erf_id', function($query, $keyword) {
                $keyword = str_replace('erf', '', strtolower($keyword));
                if($keyword != ''){
                    $query->where('employee_requisitions.id', $keyword);
                }
            })
            ->filterColumn('employee_requisitions.type', function($query, $keyword) {
                $keyword =  strtolower($keyword);
                if($keyword == 'replacement'){
                    $query->where('employee_requisitions.type', 2);
                }
                else{
                    $query->where('employee_requisitions.type', 1);
                }
            })
            ->filterColumn('employee_requisitions.employee_status', function($query, $keyword) {
                $keyword =  strtolower($keyword);
                if($keyword == 'inactive'){
                    $query->where('employee_requisitions.employee_status', 1);
                }
                else if($keyword == 'notice period' || $keyword == 'notice'){
                    $query->where('employee_requisitions.employee_status', 2);
                }
            })
->editColumn('trax_id', function($erf) {
                if($erf->type == 1){
                    return '-';
                }
                else{
                    $items = array();
                    $trax_id = "";
                    $ids = EmployeeRequisitionReplacement::where('er_id',$erf->erf_id)->select('trax_id')->get();
                    foreach ($ids as $value){
//                        $trax_id.= $value->trax_id.",<br>";
                        $items[] = '<br>'.$value->trax_id;
                    }
                    return $items;
                }
            })
            ->editColumn('trax_id_for_excel', function($erf) {
                if($erf->type == 1){
                    return '-';
                }
                else{
                    $trax_id = "";
                    $ids = EmployeeRequisitionReplacement::where('er_id',$erf->erf_id)->select('trax_id')->get();
                    foreach ($ids as $value){
                        $trax_id.= $value->trax_id.",";
                    }
                    return $trax_id;
                }
            })
            ->addColumn('leaver_name_for_excel', function($erf) {
                if($erf->type == 1){
                    return '-';
                }
                else{
                    $trax_id = "";
                    $ids = EmployeeRequisitionReplacement::where('er_id',$erf->erf_id)->select('trax_id')->get();
                    foreach ($ids as $id){
                        $name = Employee::where('trax_id',$id->trax_id)->select('name')->first();
                        if($name){
                            $trax_id.= $name->name.",";
                        }
                    }
                    return $trax_id;
                }
            })
            ->addColumn('leaver_name', function($erf) {
                if($erf->type == 1){
                    return '-';
                }
                else{
                    $trax_id = "";
                    $leaver_name = array();
                    $ids = EmployeeRequisitionReplacement::where('er_id',$erf->erf_id)->select('trax_id')->get();
                    foreach ($ids as $id){
                        $name = Employee::where('trax_id',$id->trax_id)->select('name')->first();
//                        $trax_id.= $name->name.",<br>".' ';
                        if($name){
                            $leaver_name[] = '<br>'.$name->name;
                        }
                    }
                    return $leaver_name;
                }
            })
            ->editColumn('requested_by_name', function($erf) {
                if($erf->type == 1){
                    return '-';
                }
                else{
//                    $requested_by = Admin::where('id',$erf->requested_by)->select('name')->first();
                    return $erf->requested_by_name;
                }
            })
            ->filterColumn('ar.name', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('ar.name', "like","%".$keyword."%")->where('employee_requisitions.type','!=',1);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('requested_date', function($erf) {
                if($erf->type == 1){
                    return '-';
                }
                else{
                    $requested_date = EmployeeRequisition::where('id',$erf->id)->select('created_at')->first();
                    return $requested_date->created_at;
                }
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || count(array_intersect([518,519,520,521], session('permissions'))) !== 0) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if (($result->status_id == 1) && (session('role_id') == 1 || in_array(518, session('permissions')))) {
                            $dropdown .= '<button type="button" class="dropdown-item admin_approve" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Approve By HOD</div></button>';
                        $dropdown .= '<button type="button" class="dropdown-item admin_reject" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Reject By HOD</div></button>';

                    }
                    if (($result->status_id == 2 && $result->type == 1) && (session('role_id') == 1 || in_array(519, session('permissions')))) {
                        $dropdown .= '<button type="button" class="dropdown-item admin_approve" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Approve By CEO</div></button>';
                        $dropdown .= '<button type="button" class="dropdown-item admin_reject" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Reject By CEO</div></button>';

                    }
                    if (($result->status_id == 2 && $result->type == 2 || ($result->status_id == 3))  && (session('role_id') == 1 || in_array(520, session('permissions')))) {
                        $dropdown .= '<button type="button" class="dropdown-item approve_request" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Approve </div></button>';

                    }
                    if (session('role_id') == 1 || in_array(521, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item view_document" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View </div></button>';

                    }
                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                }
                else {
                    return '';
                }
            });


        if($status = $request->get('search_status')){
          
            $datatables->where('s.id', '=', $status);
        }


        return $datatables->make(true);

    }

    public function add(){

        ActivityTrailController::createActivityTrailLog(Auth::id(),263);
        $cities = City::where('status',1)->where('business_category_id',1)->select('id','name')->get();
        $hubs =  City::where('hub',1)->select('id','name')->get();
        if(session('role_id') == 1){
            $departments = AdminDepartment::select('id', 'name')->get();
        }
        else if (session('department_id') == 10) {
            $departments = AdminDepartment::where('id', '!=', 1)->select('id', 'name')->get();
        }
        else{
            $departments = AdminDepartment::where('id', '=', session('department_id'))->select('id', 'name')->get();
        }
        $designations = EmployeeDesignation::where('status',1)->select('id','name')->get();
      
        $department_admins = AdminDepartment::all()->pluck('department_head_id')->toArray();
        $department_heads = Admin::whereIn('id', $department_admins)->where('status', 1)->select('id','name')->get();

        $admin_positions = AdminPositionTypes::select('id','name')->get();
        $allowances = Allowances::all();
       
        $today = Carbon::now()->endOfDay();

        return view('admin.human_resource.erf.add')->with(['cities' => $cities,'hubs' => $hubs,'departments' => $departments,'designations' => $designations,'department_heads' => $department_heads,'admin_positions' => $admin_positions,'allowances' => $allowances,'today' => $today]);
    }

    public function submit_form(Request $request){
       
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

        if($request->has('employee_status')){
            if($request->employee_status == 1){
                $erf->employee_status = 1;
            }
            else {
                $erf->employee_status = 2;
            }
        }

        $erf->save();

       if($request->erf_type == 1){
           if($request->has('allowances')){
               foreach($request->allowances as $allowance){
                   $assigned_benefits = new EmployeeRequisitionAllowances();
                   $assigned_benefits->er_id = $erf->id;
                   $assigned_benefits->allowance_id = $allowance;
                   $assigned_benefits->save();
               }
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

        $path = $this::erf_print($erf->id,'pdf');
        $data['id'] = $erf->id;
        $data['email'] = $email;
        NotificationsController::send(133, $data, url('/') . '/' . 'reports/employee_requisition_'. str_pad($erf->id, 6, '0', STR_PAD_LEFT) .'.pdf');
        return redirect()->route('admin.human_resource.erf.index')->with(['success' => 'Request Submitted']);

    }

    public static function erf_print($erf_id,$document_type) {

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $erf = EmployeeRequisition::find($erf_id);

        if($erf->type == 1){
            $position = AdminPositionTypes::find($erf->position_type_id);
            $type = 'Additional';
            $benefits = EmployeeRequisitionAllowances::join('allowances as a','a.id','=','employee_requisition_allowances.allowance_id')->where('er_id',$erf->id)->select('a.name as name')->get();
        }
        else{
            $type = 'Replacement';
        }


        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Employee Requisition Form</title>

                     <style>
                      body {
                        font-size: 0.95rem !important;
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
                            <tr >
                              <td class="text-center align-middle" rowspan="5" colspan="2"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                       
                                 <td class="text-center align-middle color secondary" colspan="2" >Employee Requisition</td>
                             </tr>
                              <tr>
                                  <td class="text-center align-middle color secondary"  colspan="2">ERF ID : '.$erf->id.'</td>
                              </tr>
                              <tr>
                                  <td class="text-center align-middle color secondary"  colspan="2">Requested Date : '.$erf->created_at.'</td>
                                  </tr>
                              <tr>
                                  <td class="text-center align-middle color secondary" colspan="2">Status : '.$erf->status->name.'</td>
                                  </tr>
                              <tr>
                                  <td class="text-center align-middle color secondary" colspan="2" >ERF Type : '.$type.'</td>
                                  
                              </tr> ';
                             if($erf->type == 1){

                             $html .= '  <tr>
                         
                              <td class="color secondary"><strong>Department</strong></td>
                              <td>' . $erf->department->name . '</td>
                            
                                 <td class="color secondary"> <strong>Position Type</strong></td>
                               <td>' . $position->name . '</td>
                          
                            
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Designation</strong></td>
                              <td>' . $erf->designation->name . '</td>
                               <td class="color secondary"><strong>Employee Category</strong></td>
                              <td>' . $erf->department->name . '</td>
                            </tr>
                           
                            <tr>
                              <td class="color secondary"><strong>Hub</strong></td>
                              <td>' . $erf->hub->name. '</td>
                               <td class="color secondary"><strong>City</strong></td>
                              <td>' . $erf->city->name . '</td>
                            </tr>
                             
                             <tr>
                              <td class="color secondary"><strong>Salary From</strong></td>
                              <td>' . $erf->salary_from. '</td>
                               <td class="color secondary"><strong>Salary To</strong></td>
                              <td>' . $erf->salary_to. '</td>
                            </tr>
                            
                            <tr>
                              <td class="color secondary"><strong>Line Manager</strong></td>
                              <td>' . $erf->manager->name. '</td>
                               <td class="color secondary"><strong>Vacancies</strong></td>
                              <td>' . $erf->vacancies. '</td>
                            </tr>';
                             }
                             else{
                                 $html .= '  <tr>
                         
                              <td class="color secondary" colspan="2"><strong>Department</strong></td>
                              <td colspan="2">' . $erf->department->name . '</td>
                            
                            </tr>
                            <tr>
                              <td class="color secondary" colspan="2"><strong>Designation</strong></td>
                              <td colspan="2">' . $erf->designation->name . '</td>
                            
                            </tr>
                           
                            <tr>
                              <td class="color secondary" colspan="2"><strong>Hub</strong></td>
                              <td colspan="2">' . $erf->hub->name. '</td>
                            
                            </tr>
                             
                             <tr>
                               <td colspan="2" class="color secondary"><strong>City</strong></td>
                              <td colspan="2">' . $erf->city->name . '</td>
                            </tr>
                            
                            <tr>
                              <td colspan="2" class="color secondary"><strong>Line Manager</strong></td>
                              <td colspan="2">' . $erf->manager->name. '</td>
                            
                            </tr>';
                             }


                      $html .=  '</tbody>
                         </table> 
                      </div>';

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
                              <td class="text-justify">' . $erf->job_description . '</tdc>
                              </tr>
                              <tr>
                            
                                 <td class="color secondary"> <strong>Required Skills</strong></td>
                               <td class="text-justify">' . $erf->skills . '</td>
                      
                            
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Qualification Required</strong></td>
                              <td class="text-justify">' . $erf->qualifications . '</td>
         
                            </tr>                
                         </tbody>
                         </table> 
                        </div>';
        }
        else{

            $html .= '<div class="replacement">
            <table class="table table-sm table-bordered border">
               <thead>
                    <tr>
                      <th scope="col">Leaver Trax Id</th>
                      <th scope="col">Leaver Name</th>
                      <th scope="col">Date of Seperation</th>
                      <th scope="col">Last Gross Salary</th>
        
                    </tr>
                </thead>
               <tbody>';
            foreach($erf->replacement as $replacement) {
                     $employee = Employee::where('trax_id',$replacement->trax_id)->first();
                    $html .= '<tr>
                            <td>' . $replacement->trax_id . '</td>
                            <td>' . $employee->name . '</td>
                            <td>' . $replacement->date . '</td>
                            <td>' . $replacement->last_gross_salary . '</td>
                        </tr>';

            }

            $html .='
                
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
                      <th scope="col">Date And Time</th>
                    
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

            if($document_type == 'pdf'){
                $html .='
               
               </tbody>
            </table>
            </div>
            </body>
            </html>';
                $pdf = SnappyPDF::loadHTML($html)->save('reports/employee_requisition_'. str_pad($erf->id, 6, '0', STR_PAD_LEFT) .'.pdf');
                return $pdf;
            }
            else{
                $html .='
               
               </tbody>
            </table>
            </div>
             <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
            </body>
            </html>';

                return $html;

            }
        }

    public function file_upload(Request $request){

        $erf_id = str_replace("ERF","",$request->erf_id);
        $erf =EmployeeRequisition::find($erf_id);
        $date = Carbon::now()->format('Y_m_d');

        $request->validate([
            'file' => 'required|mimes:png,jpg,pdf,doc,docx|max:5120',
        ]);

        $file_type = request()->file->getClientOriginalExtension();

            $file = $request->file('file');
            $filename = 'form_'. $date . '_' . $erf_id .'.' .$file_type;
            $path = 'employee_requisition/'.$erf_id;

        if(!Storage::disk('public')->exists($path)){
            Storage::disk('public')->putFileAs('employee_requisition/'. $erf_id .'', $file, $filename);
        }
        else{
            $file->storeAs($path,$filename, 'public');

        }
        
        $attachments = new EmployeeRequisitionAttachments();
        $attachments->er_id = $erf_id;
        $attachments->admin_id = Auth::id();
        $attachments->file = $filename;
        $attachments->save();


        if($erf->status_id == 1){
            $erf->status_id = 2;
        }
        else if ($erf->status_id == 2){
           $erf->status_id = 3;
        }

        $erf->save();

        NotificationsController::send(174,$erf_id);

            $log = new EmployeeRequisitionStatusLog();
            $log->er_id = $erf_id;
            $log->admin_id = Auth::id();
            $log->status_id = $erf->status_id;
            $log->save();

            return redirect()->back()->with('success', 'Document Uploaded!');
        }

    public function approve(Request $request){
        $erf_id = str_replace("ERF","",$request->id);
        $erf = EmployeeRequisition::find($erf_id);
     
        if($erf->status_id == 3 || ($erf->status_id == 2 && $erf->type == 2 ) ){
            $erf->status_id = 4;
            $erf->save();

            $log = new EmployeeRequisitionStatusLog();
            $log->er_id = $erf_id;
            $log->admin_id = Auth::id();
            $log->status_id = 4;
            $log->save();
            return response()->json(['status' => 1,'success' => 'Status Updated']);
        }
        else{
            return response()->json(['status' => 0,'error'=> 'Status already approved']);
        }
    }

    public function documents(Request $request){
        
        $erf_id = str_replace("ERF","",$request->id);
        $user_documents = EmployeeRequisitionAttachments::where('er_id', $erf_id);
        if($user_documents->exists()){
            $user_documents = $user_documents->join('admins as a','a.id','=','employee_requisition_attachments.admin_id')->where('er_id',$erf_id)->select(['a.name as admin','employee_requisition_attachments.file as file','employee_requisition_attachments.er_id as id'])->get();
            return response()->json(['status' => 1,'documents' => $user_documents,'erf_id' => $erf_id]);
        }
        else{
            return response()->json(['status' => 2,'erf_id' => $erf_id]);
        }
    }

    public function print(Request $request){

        $erf_id = str_replace("ERF","",$request->id);
        $html = $this::erf_print($erf_id,'html');
        return $html;

    }

    public function employee_data(Request $request){
        $department = AdminDepartment::find($request->id);
        $data['department_head'] = Admin::find($department->department_head_id);
        $data['designations'] = EmployeeDesignation::where('department_id',$request->id)->select('name','id')->get();

        $invalid_employees = EmployeeRequisitionReplacement::pluck('trax_id')->toArray();
        $employee_trax_id  = Employee::whereNotIn('trax_id', $invalid_employees)->where('department_id',$request->id)->get();
        $trax_ids = array();
      
        foreach($employee_trax_id as $employee){
            $inactive = '';
            if($employee->status_id == 2){
               $inactive = '-inactive';
            }
            $trax_id = $employee->trax_id . $inactive;
            $trax_id_array = array('id' => $employee->trax_id,'name' => $trax_id);
            array_push($trax_ids,$trax_id_array);

        }
        return response()->json(['status' => 1,'emplyee_detail' => $data,'trax_ids' => $trax_ids]);

    }

    public function reject_reason(Request $request){

        $erf_id = str_replace("ERF","",$request->erf_id);
        $erf = EmployeeRequisition::find($erf_id);
        
        if(in_array($erf->status_id,[1,2,3]) && Auth::id() == 8){
            $erf->status_id = 6;
            NotificationsController::send(173,$erf_id);
        }
        else if(in_array($erf->status_id,[1,2,3])){
            $erf->status_id = 5;

        }
        else{
            return redirect()->back()->with('error','Status already marked approved or rejected');
        }

        $erf->save();

        $log = new EmployeeRequisitionStatusLog();
        $log->er_id = $erf_id;
        $log->admin_id = Auth::id();
        $log->status_id = $erf->status_id;
        $log->reject_reason = $request->reason;
        $log->save();

        return redirect()->back()->with('success', 'Reason Added!');

    }

    public function employee_details(Request $request){
       $trax_id = $request->trax_id;
       if($trax_id){
           $last_salary = 0;
           $employee = Employee::where('trax_id',$trax_id)->first();
           if($employee){
              
                 $details['designation'] = $employee->designation->name;
                 $details['name'] = $employee->name;
                 if($employee->last_working_date == null){
                     $details['last_working_date'] = '-';
                 }
                 else{
                     $details['last_working_date'] = $employee->last_working_date;
                 }
                 $salary = EmployeePayslip::where('trax_id',$trax_id)->latest('payroll_month')->first();
                 if($salary){
                     $details['last_salary'] = $salary->gross_salary;
                 }
                 else{
                     $details['last_salary'] = 0;
                 }
                 return response()->json(['status' => 1, 'details' => $details]);
           }
           else{
               return response()->json(['status' => 0,'error' => 'No employee found with this trax id']);
           }
       }
    }

}
