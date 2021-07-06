<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Model\IncidenceMonitoring;
use App\Http\Model\IncidenceMonitoringArea;
use App\Http\Model\IncidenceMonitoringCaseNature;
use App\Http\Model\IncidenceMonitoringNCLevel;
use App\Http\Model\IncidenceMonitoringStatusHistory;
use App\Http\Model\IncidenceMonitoringTaggedPerson;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\City;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use IncidenceMonitoringNCLevelSeeder;
use Yajra\Datatables\Datatables as DatatablesDatatables;
use Yajra\Datatables\Facades\Datatables;

class IncidenceMonitoringController extends Controller
{
    // public $sale_role_ids = array();
    // public $finance_role_ids = array();
    // public $operation_role_ids = array();

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
        // $this->sale_role_ids = AdminRole::where('department_id',7)->pluck('id')->toArray();
        // $this->finance_role_ids = AdminRole::where('department_id',4)->pluck('id')->toArray();
        // $this->operation_role_ids = AdminRole::where('department_id',6)->pluck('id')->toArray();
    }


    public function index(){
        $monitoring_areas = IncidenceMonitoringArea::all();
        $case_natures = IncidenceMonitoringCaseNature::all();
        $nc_levels = IncidenceMonitoringNCLevel::all();
        $stations = City::all();
        return view('admin.incidence_monitoring.index', compact('monitoring_areas', 'case_natures', 'nc_levels', 'stations'));
    }

    public function get_managers(Request $request){
        $admin_ids = AdminHub::where('hub_id',$request->hub_id)->pluck('admin_id')->toArray();
        if(count($admin_ids) > 0){

            $agents = Admin::whereIn('id', $admin_ids)->where('status',1)->get();
           
            return response()->json(['status' => 1, 'agents' => $agents]);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Agents Does\'nt exist!']);
        }
    }


    public function add(Request $request){
        
        $incidence_monitoring = new IncidenceMonitoring();
        $incidence_monitoring->station_id = $request->station_id;
        $incidence_monitoring->incidence_monitoring_area_id = $request->monitoring_area_id;
        $incidence_monitoring->time_from = $request->time_from;
        $incidence_monitoring->time_to = $request->time_to;
        $incidence_monitoring->incidence_monitoring_case_nature_id = $request->case_nature_id;
        $incidence_monitoring->obeservation = $request->observations;
        $incidence_monitoring->incidence_monitoring_n_c_level_id = $request->nc_level_id;
        $incidence_monitoring->tagging_date = Carbon::now();
        $incidence_monitoring->clip_link = $request->clip_link;
        $incidence_monitoring->admin_id = Auth::user()->id;
        $incidence_monitoring->save();

        foreach ($request->tagged_to as $value) {
            $incidence_monitoring_tagged_person = new IncidenceMonitoringTaggedPerson();
            $incidence_monitoring_tagged_person->incidence_monitoring_id = $incidence_monitoring->id;
            $incidence_monitoring_tagged_person->admin_id = $value;
            $incidence_monitoring_tagged_person->save();
        }

        $history = new IncidenceMonitoringStatusHistory();
        $history->incidence_monitoring_id = $incidence_monitoring->id;
        $history->status_id = $incidence_monitoring->status_id;
        $history->admin_id = Auth::user()->id;
        $history->save();


        return redirect()->back()->with('success', 'Report Added successfully.');
    }

    public function list(Request $request){
        $data = IncidenceMonitoring::join('admins as admin','admin.id','=','incidence_monitorings.admin_id')
        ->join('cities as station','station.id','=','incidence_monitorings.station_id')
        ->join('incidence_monitoring_areas as area','area.id','=','incidence_monitorings.incidence_monitoring_area_id')
        ->join('incidence_monitoring_case_natures as case_nature','case_nature.id','=','incidence_monitorings.incidence_monitoring_case_nature_id')
        ->join('incidence_monitoring_n_c_levels as nc_level','nc_level.id','=','incidence_monitorings.incidence_monitoring_n_c_level_id')
        ->join('incidence_monitoring_statuses as status','status.id','=','incidence_monitorings.status_id')
        ->select(['incidence_monitorings.id','incidence_monitorings.time_from','incidence_monitorings.time_to','incidence_monitorings.obeservation','incidence_monitorings.tagging_date','incidence_monitorings.clip_link','station.name as station_name','area.area as area_name','case_nature.case_nature as case_nature_type','nc_level.nc_level as nc_level_name','admin.name as created_by','status.status as status_name']);
        

        $datatables = Datatables::of($data)
        ->addColumn('tagged_to', function($report) {
            $tagged_users = IncidenceMonitoringTaggedPerson::where('incidence_monitoring_id', $report->id)->get();
            $msg = '';
            foreach ($tagged_users as $tagged_user) {
                $msg.= $tagged_user->admin->name.'  ';
            }
           return $msg;
        })
        ->addColumn('incidence_monitorings_id_padded', function ($report) {
            return str_pad($report->id, 6, '0', STR_PAD_LEFT);
        })
        ->addColumn('time_slot', function($report) {
            $time_slot = $report->time_from.' - '.$report->time_to;
            return $time_slot;
        })
        ->addColumn('action', function($data) {
            $dropdown = '';
            if (session('role_id') == 1 || in_array(536, session('permissions'))){
                $dropdown .= '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
                    <button onclick="window.open(\'' . route('admin.incidence_monitoring.view_report', ['id' => $data->id]) . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>
                    </div>
              </div>
            ';
            }
            return $dropdown;
        });
       
        return $datatables->make(true);
    }

    public function view_report($id){
        $report = IncidenceMonitoring::find($id);
        if($crm_request){
            $shipment_status = null;
            $shipment_status_date = null;
            $shipper = null;
            $arrival_date = '';
            if($crm_request->shipment_id != null) {
                $shipment_status = Shipment::find($crm_request->shipment_id);
                $shipment_status = $shipment_status->status_shipper->name;
                $journey = ShipmentsJourney::where('shipment_id', $crm_request->shipment_id)->where('shipper_status_id', '=', 2);
                if($journey->exists()){
                    $journey = $journey->latest()->first();
                    $arrival_date = $journey->created_at;
                }
                $shipment_status_journey = ShipmentsJourney::where('shipment_id', $crm_request->shipment_id)->latest('id')->first();
                $shipment_status_date = $shipment_status_journey->created_at;
            }

            if($crm_request->shipper_id != null){
                $shipper = User::find($crm_request->shipper_id);
                $shipper = $shipper->name;
            }
            $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id' )
                ->select('a.id as id', 'a.name as name')
                ->whereNotIn('admin_roles.department_id', [1,3])->get();
            $types = CrmRequestTaggingTypes::get();
            $departments = AdminDepartment::whereNotIn('id', [1,3])->get();
            $hubs = City::where('hub', 1)->get();
            $tagged = CrmRequestTagging::where('crm_request_id', $crm_request['id'])->first();
            $tagged_name = '';
            $tag_check = '';
            $tag_permission = '';
            if($tagged){
                if($tagged['crm_request_tagging_type_id'] == 1){
                    $tag_check = Auth::user()->role_id;
                    $tag = AdminRole::where('id', $tag_check)->first();
                    $tag_permission = $tag->department_id;
                    $tagged_name = AdminDepartment::find($tagged['tagged_id'])->name;
                }
                else if($tagged['crm_request_tagging_type_id'] == 2){
                    $tagged_name = Admin::find($tagged['tagged_id'])->name;
                }
            }
            $escalation_tagged = CrmRequestEscalationTagging::where('crm_request_id', $crm_request['id'])
                ->where('role_id', session('role_id'))
                ->where(function ($sub_sub_query) {
                    $sub_sub_query->whereNull('hub_id')
                        ->orWhereNotNull('hub_id')
                        ->whereIn('hub_id', session('hubs'));
                });
            if($escalation_tagged->exists()){
                $escalation_tagged_check = true;
            }
            else{
                $escalation_tagged_check = false;
            }
            $agent = Admin::where('id', $crm_request['agent_id'])->first();
            $agent_name = '';
            if($agent){
                $agent_name = $agent['name'];
            }

            $crm_comments = array();
            $last_comment = null;
            $crm_comments = CrmComments::where('crm_request_id', $id);
            if($crm_comments->exists()){
                $crm_comments = $crm_comments->orderBy('created_at','asc')->get();
                $last_comment = CrmComments::where('crm_request_id', $id)->latest()->first();
                $last_comment = $last_comment->id;
            }

            $launched_by  = '';
            if($crm_request->launched_by == 0){
                $launched_by = $launched_by = $crm_request->launched_by_admin->name."(Admin)";
            }else if($crm_request->launched_by == 1){
                $launched_by = User::find($crm_request->launched_by_id)->name;
            }else if($crm_request->launched_by == 2){
                $launched_by = SubstituteUser::find($crm_request->launched_by_id)->name;
            }
            $crm_tagging = array();
            $crm_tagging_details = CrmRequestTagging::where('crm_request_id', $id)->first();
            if($crm_tagging_details){
                $crm_tagging = $crm_tagging_details;
            }
            $crm_agent_history = CrmRequestAgentHistory::where('crm_request_id', $id)->get();
            $crm_request_ids = array();
            if($crm_request->shipment_id){
                $all_crm_request_ids_for_shipment = CrmRequest::where('shipment_id',$crm_request->shipment_id)->pluck('id')->toArray();
                $crm_request_ids = $all_crm_request_ids_for_shipment;
            }else{
                $crm_request_ids = [$id];
            }
            $crm_status_history = CrmRequestStatusHistory::whereIn('crm_request_id', $crm_request_ids)->get();
            $crm_tagging_history = CrmRequestTaggingHistory::where('crm_request_id', $id)->get();
            $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->get();
            $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
            $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
            $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();

            $sale_person = SalePersonTag::where('user_id', $crm_request->shipper_id)->where('status', 0)->first();

            $crm_escalation_tagging_history = CrmRequestEscalationTagging::where('crm_request_id', $id)->get();

            $escalation_status_flag = true;
            $escalation_log_flag = false;
            $escalation_tagging_id = NULL;
            $crm_escalation_levels = NULL;
            $crm_request_escalation_status = CrmRequestEscalationStatus::where('crm_request_id', $crm_request->id);
            if($crm_request_escalation_status->exists()){
                $crm_request_escalation_status = $crm_request_escalation_status->first();
                if($crm_request_escalation_status->status == 0){
                    $escalation_status_flag = false;
                }
            }
            $crm_request_escalation_log = CrmRequestEscalationLog::where('crm_request_id', $crm_request->id);
            if($crm_request_escalation_log->exists()){
                $crm_request_escalation_log = $crm_request_escalation_log->latest()->first();
                $previous_levels = CrmRequestEscalationLog::where('crm_request_id', $crm_request->id)->where('escalation_tagging_id', $crm_request_escalation_log->escalation_tagging_id)->pluck('level_id')->toArray();
                $crm_request_escalation_tagging = CrmEscalationTaggingLevel::where('escalation_tagging_id', $crm_request_escalation_log->escalation_tagging_id)->whereNotIn('level_id', $previous_levels)->pluck('level_id')->toArray();
                $crm_escalation_levels = CrmEscalationLevel::whereIn('id', $crm_request_escalation_tagging)->get();
                if(count($crm_escalation_levels) > 0){
                    $escalation_log_flag = true;
                    $escalation_tagging_id = $crm_request_escalation_log->escalation_tagging_id;
                }
            }

            $crm_images_count = $crm_request->images->count();

            return view('admin.incidence_monitoring.view_report')->with(['crm_details' => $crm_request, 'launched_by' => $launched_by, 'comments' => $crm_comments, 'last_comment_id' => $last_comment, 'admins' => $admins, 'types' => $types, 'departments' => $departments, 'tagged_name' => $tagged_name,'crm_tagging' => $crm_tagging, 'crm_agent_history' => $crm_agent_history, 'crm_status_history' => $crm_status_history, 'crm_tagging_history' => $crm_tagging_history, 'agent' => $agent_name, 'tag_check' => $tagged, 'tag_permission' => $tag_permission, 'shipment_status' => $shipment_status, 'shipper' => $shipper,'case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'arrival_date' => $arrival_date, 'shipment_status_date' => $shipment_status_date, 'sale_person' => $sale_person, 'case_nature_type_claims' => $case_nature_type_claims, 'hubs' => $hubs, 'escalation_tagged_check' => $escalation_tagged_check, 'crm_escalation_tagging_history' => $crm_escalation_tagging_history, 'escalation_status_flag' => $escalation_status_flag, 'escalation_log_flag' => $escalation_log_flag, 'escalation_tagging_id' => $escalation_tagging_id, 'crm_escalation_levels' => $crm_escalation_levels, 'crm_images_count' => $crm_images_count]);
        }else{
            return redirect()->back()->with('danger', 'CRM Request Not found!');
        }

    }

    
}
