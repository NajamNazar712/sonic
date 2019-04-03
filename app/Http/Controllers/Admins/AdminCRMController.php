<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\CRM\CRMCommentController;
use App\Http\Controllers\CRM\CRMController;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\CRM\CrmComments;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestAgentHistory;
use App\Http\Models\CRM\CrmRequestStatus;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\CRM\CrmRequestTagging;
use App\Http\Models\CRM\CrmRequestTaggingHistory;
use App\Http\Models\CRM\CrmRequestTaggingTypes;
use App\Http\Models\Shipment;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\Shipper\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\Admin\AdminRoleModulePermission;
use App\Http\Models\Admin\Module;
use App\Http\Models\Admin\ModulePermission;

use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class AdminCRMController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function add_request(Request $request){
        $nature_id = $request->case_nature_id;
        $complaint_id = $request->complaint_id;
        $channel_id = $request->channel_id;
        $shipment_ids = $request->shipment_ids;
        $description = $request->description;
        $flag = false;
        $present_shipments = array();
        if(!empty($shipment_ids)){
            foreach ($shipment_ids as $shipment_id) {
                $shipment = Shipment::find($shipment_id);
                if($shipment){
                    $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->first();
                    if($is_shipment){
                        if($is_shipment->case_nature_id != $nature_id){
                            CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
                        }else{
                            $present_shipments[] = $shipment->tracking_number;
                            $flag = true;
                        }
                    }else{
                        CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
                    }
                }
            }
            return ['status' => 1, 'success' => 'Request(s) successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
        }else{
            return ['status' => 0, 'error' => 'No shipments selected!'];
        }
    }

    public function update_request(Request $request){
        $request_id = $request->request_id;
        $nature_id = $request->case_nature_id;
        $complaint_id = $request->complaint_id;
        $channel_id = $request->channel_id;
        if($request_id != null){
            $crm_request = CrmRequest::find($request_id);
            $crm_request->case_nature_id = $nature_id;
            $crm_request->case_nature_type_id = $complaint_id;
            $crm_request->channel_id = $channel_id;
            $crm_request->save();
            return ['status' => 1, 'success' => 'Request successfully updated!'];
        }
        return ['status' => 0, 'error' => 'Request not found!'];

    }
    public function add_feedback(Request $request){
        $nature_id = 3;
        $channel_id = $request->channel_id;
        $description = $request->description;
        if($channel_id == null){
            return ['status' => 0, 'error' => 'Channel Not selected!'];
        }
        if($description == null){
            return ['status' => 0, 'error' => 'Description Not Entered!'];
        }

        CRMController::add($nature_id, NULL, $channel_id, 1, Auth::id(), 0, NULL, NULL, NULL ,$description);
        return ['status' => 1, 'success' => 'Feedback successfully added'];

    }

    public function request_details(Request $request,$id){
        $crm_request = CrmRequest::find($id);
        $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id' )
            ->select('a.id as id', 'a.name as name')
            ->whereNotIn('admin_roles.department_id', [1,3])->get();
        $types = CrmRequestTaggingTypes::get();
        $departments = AdminDepartment::whereNotIn('id', [1,3])->get();
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
            $launched_by = $launched_by = $crm_request->launched_by_admin->name;
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
        $crm_status_history = CrmRequestStatusHistory::where('crm_request_id', $id)->get();
        $crm_tagging_history = CrmRequestTaggingHistory::where('crm_request_id', $id)->get();
        if($crm_request){
            return view('admin.crm.request_details')->with(['crm_details' => $crm_request, 'launched_by' => $launched_by, 'comments' => $crm_comments, 'last_comment_id' => $last_comment, 'admins' => $admins, 'types' => $types, 'departments' => $departments, 'tagged_name' => $tagged_name,'crm_tagging' => $crm_tagging, 'crm_agent_history' => $crm_agent_history, 'crm_status_history' => $crm_status_history, 'crm_tagging_history' => $crm_tagging_history, 'agent' => $agent_name, 'tag_check' => $tagged, 'tag_permission' => $tag_permission]);
        }else{
            return redirect()->back()->with('danger', 'CRM Request Not found!');
        }
    }

    public function add_comment(Request $request){
        $comment = $request->comment;
        $request_id = $request->request_id;
        $comment_by = 0;
        $comment_type = 0;
        if($request->internal_switch == 1){
            $comment_type = 1;
        }
        if($comment == null){
            return ['status' => 0, 'error' => 'Comment Not selected!'];
        }
        if(!$request_id){
            return ['status' => 0, 'error' => 'Request ID Not selected!'];
        }

        CRMCommentController::add($request_id, Auth::id(),$comment_by,$comment_type, $comment);
        $last_comment = CrmComments::where('crm_request_id', $request_id)->where('comment_by',0)->latest()->first();
        return ['status' => 1, 'success' => 'Comment successfully added', 'last_comment_id' => $last_comment->id];
    }

    public function get_latest_comment(Request $request){
        $comment_id = $request->comment_id;
        $request_id = $request->request_id;
        if(($comment_id != null) && ($request_id != null)){
            $name = '';
            $comment_details = CrmComments::where('crm_request_id', $request_id)->latest()->first();
            if($comment_details){
                if($comment_details->id > $comment_id){
                    if($comment_details->comment_by == 0){
                        $name = $comment_details->admin->name;
                    }else if($comment_details->comment_by == 1){
                        $name = $comment_details->shipper->name;
                    }else{
                        $name = $comment_details->substitute_user->name;
                    }
                    return ['status' => 1, 'comment' => $comment_details, 'name'=> $name];
                }
            }

        }
    }

    public function get_request_info(Request $request){
        $request_id = $request->request_id;
        $request_details = CrmRequest::find($request_id);
        if($request_details){
            $tracking_number = '';
            if($request_details->shipment_id != null){
                $tracking_number = Shipment::find($request_details->shipment_id)->tracking_number;
            }
            return ['status' => 1, 'details' => $request_details, 'tracking_number' => $tracking_number];
        }else{
            return ['status' => 0, 'error' => 'Request ID not found!'];
        }
    }


    public function launched_re_open_index(){
        $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->select('id', 'name')->get();
        $case_nature_type = CrmRequestCaseNatureType::select('id', 'type')->get();
        $channels = CrmRequestChannel::select('id', 'channel')->get();
        $status = CrmRequestStatus::whereIn('id', [1,5])->select('id', 'name')->get();
        $agents = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id',3)->get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->get();
        return view('admin.crm.launched_re_open')->with(['case_nature' => $case_nature, 'case_nature_type' => $case_nature_type, 'channels' => $channels, 'status' => $status, 'agents' => $agents,'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests]);
    }

    public function launched_re_open_list(Request $request){
        $launched_request = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('crm_request_statuses as crs', 'crs.id', '=', 'crm_requests.status_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', 'a.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('users as u', 'u.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('substitute_users as su', 'su.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->select('crm_requests.id as id', 's.tracking_number as tracking_number','crcn.id as nature_id', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'crs.name as status', 'ad.name as agent', 'a.name as name', 'u.name as shipper', 'su.name as sub_shipper', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at', 'crm_requests.description as description')
            ->where(function ($query) {
                $query->where(function($sub_query) {
                    $sub_query->whereIn('crm_requests.status_id', [1,5])
                     ->whereIn(DB::raw('(SELECT role_id FROM admins WHERE id = '. Auth::id() .')'), [1,6]);
                })->orWhere(function($sub_query) {
                    $role = Auth::user()->role_id;
                    $sub_query->whereIn('crm_requests.status_id', [1,5])
                   ->where(DB::raw('(SELECT permission_id FROM admin_role_module_permissions WHERE role_id = '. $role .' AND permission_id = 183)'), in_array(183, session('permissions')));
                });
            });
        $datatables = Datatables::of($launched_request)
            ->setRowAttr([
                'nature' => function ($requests) {
                    $nature = '';
                    if($requests->nature_id == 1){
                        $nature = 1;
                    }else if($requests->nature_id == 2){
                        $nature = 2;
                    }else if($requests->nature_id == 3){
                        $nature = 3;
                    }
                    return $nature;
                }
            ])
            ->addColumn('tracking_number_hyperlink', function ($requests) {
                return '<u><a href=' . route('admin.tracking.index') . '?tracking_number=' . $requests->tracking_number . ' class="tracking" target="_blank">' . $requests->tracking_number . '</a></u>';
            })
            ->editColumn('agent', function ($requests){
                if($requests->agent == null){
                    return '-';
                }
                else{
                    return $requests->agent;
                }
            })
            ->addColumn('launched_by_name', function ($requests){
                $name = '';
                if($requests->launched_added_by == 0){
                    $name = $requests->name;
                }
                else if($requests->launched_added_by == 1){
                    $name = $requests->shipper;
                }else{
                    $name = $requests->sub_shipper;
                }
                return $name;
            })
            ->filterColumn('launched_by_name', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 0)
                            ->where('a.name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 1)
                            ->where('u.name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 2)
                            ->where('su.name', 'like', '%' . $keyword . '%');
                    });
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('launched_by_name', DB::raw('IF (crm_requests.launched_by = 0, a.name, IF (crm_requests.launched_by = 1, u.name, IF (crm_requests.launched_by = 2, su.name, "")))') . ' $1')
            ->filterColumn('case_nature_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('crcnt.id','=',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('added_by', function($requests){
                if($requests->launched_added_by == 0) {
                    return 'Admin';
                }
                else if($requests->launched_added_by == 1) {
                    return 'Shipper';
                }
                else{
                    return 'Shipper Substitute User';
                }
            })
            ->addColumn('action', function($requests) {
                $route = route('admin.crm.request.details', ['id' => $requests->id]);
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    $dropdown .= '<button onclick="window.open(\'' . $route . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>';
                    if($requests->nature_id == 1 || $requests->nature_id == 2){
                        $dropdown .= '<button type="button" class="dropdown-item update_request"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update</div></button>';
                    }


                    $dropdown .= '</div>
                  </div>
                ';

                    return $dropdown;
            });

        return $datatables->make(true);
    }
    public function in_process_index(){
        $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->select('id', 'name')->get();
        $case_nature_type = CrmRequestCaseNatureType::select('id', 'type')->get();
        $channels = CrmRequestChannel::where('id', '>', 2)->select('id', 'channel')->get();
        $agents = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id',3)->get();
        return view('admin.crm.in_process')->with(['case_nature' => $case_nature, 'case_nature_type' => $case_nature_type, 'channels' => $channels, 'agents' => $agents]);
    }

    public function in_process_list(Request $request){
        $in_process_request = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', 'a.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('users as u', 'u.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('substitute_users as su', 'su.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->leftjoin('crm_request_taggings as crt', 'crt.crm_request_id', '=', 'crm_requests.id')
            ->leftjoin('admin_departments as adp', 'adp.id', '=', 'crt.tagged_id')
            ->select('crm_requests.id as id', 's.tracking_number as tracking_number', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'ad.name as agent', 'a.name as name', 'u.name as shipper', 'su.name as sub_shipper', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at', 'crm_requests.description as description', 'crt.tagged_id as tagged_to', 'crt.crm_request_tagging_type_id as crm_request_tagging_type_id')
            ->where('crm_requests.status_id', 2);
        if (!in_array(session('role_id'), [1, 6])) {
            $in_process_request = $in_process_request->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->where('crm_requests.agent_id', Auth::id());
                })
                ->orWhere(function ($sub_query) {
                    $sub_query->where('crt.crm_request_tagging_type_id', 2)
                        ->where('crt.tagged_id', '=', Auth::id());
                })
                ->orWhere(function ($sub_query) {
                    $sub_query->where('crt.crm_request_tagging_type_id', 1)
                        ->where('adp.id', '=', session('department_id'));
                });
            });
        }

        $datatables = Datatables::of($in_process_request)
            ->addColumn('tracking_number_hyperlink', function ($requests) {
                return '<u><a href=' . route('admin.tracking.index') . '?tracking_number=' . $requests->tracking_number . ' class="tracking" target="_blank">' . $requests->tracking_number . '</a></u>';
            })
            ->editColumn('added_by', function($requests){
                if($requests->launched_added_by == 0) {
                    return 'Admin';
                }
                else if($requests->launched_added_by == 1) {
                    return 'Shipper';
                }
                else{
                    return 'Shipper Substitute User';
                }
            })
            ->editColumn('tagged_to', function($requests){
                if($requests->crm_request_tagging_type_id == 1) {
                    $name = AdminDepartment::find($requests->tagged_to)->name;
                    return $name;
                }
                else if($requests->crm_request_tagging_type_id == 2) {
                    $name = Admin::find($requests->tagged_to)->name;
                    return $name;
                }
                else{
                    return '-';
                }
            })
            ->editColumn('agent', function ($requests){
                if($requests->agent == null){
                    return '-';
                }
                else{
                    return $requests->agent;
                }
            })
            ->addColumn('launched_by_name', function ($requests){
                $name = '';
                if($requests->launched_added_by == 0){
                    $name = $requests->name;
                }
                else if($requests->launched_added_by == 1){
                    $name = $requests->shipper;
                }else{
                    $name = $requests->sub_shipper;
                }
                return $name;
            })
            ->filterColumn('launched_by_name', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 0)
                            ->where('a.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 1)
                                ->where('u.name', 'like', '%' . $keyword . '%');
                        })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 2)
                                ->where('su.name', 'like', '%' . $keyword . '%');
                        });
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('launched_by_name', DB::raw('IF (crm_requests.launched_by = 0, a.name, IF (crm_requests.launched_by = 1, u.name, IF (crm_requests.launched_by = 2, su.name, "")))') . ' $1')
            ->filterColumn('case_nature_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('crcnt.id','=',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action', function($requests) {
                $route = route('admin.crm.request.details', ['id' => $requests->id]);
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    $dropdown .= '<button onclick="window.open(\'' . $route . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>';
                    $dropdown .= '</div>
                  </div>
                ';

                    return $dropdown;
            });

        return $datatables->make(true);
    }

    public function resolved_index(){
        $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->select('id', 'name')->get();
        $case_nature_type = CrmRequestCaseNatureType::select('id', 'type')->get();
        $channels = CrmRequestChannel::where('id', '>', 2)->select('id', 'channel')->get();
        $agents = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id',3)->get();
        return view('admin.crm.resolved')->with(['case_nature' => $case_nature, 'case_nature_type' => $case_nature_type, 'channels' => $channels, 'agents' => $agents]);
    }

    public function resolved_list(Request $request){
        $resolved_request = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', 'a.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('users as u', 'u.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('substitute_users as su', 'su.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->leftJoin('crm_request_status_histories as inp', function ($join) {
                $join->on('inp.crm_request_id', '=', 'crm_requests.id')
                    ->where('inp.created_at','=',
                        DB::raw('(select max(created_at) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 2)'));
            })
            ->leftJoin('crm_request_status_histories as res', function ($join) {
                $join->on('res.crm_request_id', '=', 'crm_requests.id')
                    ->where('res.created_at','=',
                        DB::raw('(select max(created_at) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 3)'));
            })
            ->select('crm_requests.id as id', 's.tracking_number as tracking_number', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'ad.name as agent', 'a.name as name', 'u.name as shipper', 'su.name as sub_shipper', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at', 'crm_requests.description as description','inp.created_at as inprocess','res.created_at as resolved')
            ->where('crm_requests.status_id', 3);
        if(!in_array(session('role_id'), [1,6])){
            $resolved_request = $resolved_request->where(function ($query) {
                $query->where('crm_requests.agent_id', Auth::id());
            });
        }

        $datatables = Datatables::of($resolved_request)
            ->addColumn('tracking_number_hyperlink', function ($requests) {
                return '<u><a href=' . route('admin.tracking.index') . '?tracking_number=' . $requests->tracking_number . ' class="tracking" target="_blank">' . $requests->tracking_number . '</a></u>';
            })
            ->editColumn('added_by', function($requests){
                if($requests->launched_added_by == 0) {
                    return 'Admin';
                }
                else if($requests->launched_added_by == 1) {
                    return 'Shipper';
                }
                else{
                    return 'Shipper Substitute User';
                }
            })
            ->editColumn('in_process_resolved_tat', function ($requests){
                if($requests->inprocess && $requests->resolved){
                    $process = Carbon::parse($requests->inprocess);
                    $resolved = Carbon::parse($requests->resolved);
                    return $resolved->diffForHumans($process);
                }
                return "-";
            })
            ->addColumn('launched_by_name', function ($requests){
                $name = '';
                if($requests->launched_added_by == 0){
                    $name = $requests->name;
                }
                else if($requests->launched_added_by == 1){
                    $name = $requests->shipper;
                }else{
                    $name = $requests->sub_shipper;
                }
                return $name;
            })
            ->filterColumn('launched_by_name', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 0)
                            ->where('a.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 1)
                                ->where('u.name', 'like', '%' . $keyword . '%');
                        })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 2)
                                ->where('su.name', 'like', '%' . $keyword . '%');
                        });
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('launched_by_name', DB::raw('IF (crm_requests.launched_by = 0, a.name, IF (crm_requests.launched_by = 1, u.name, IF (crm_requests.launched_by = 2, su.name, "")))') . ' $1')
            ->editColumn('agent', function ($requests){
                if($requests->agent == null){
                    return '-';
                }
                else{
                    return $requests->agent;
                }
            })->filterColumn('case_nature_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('crcnt.id','=',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action', function($requests) {
                $route = route('admin.crm.request.details', ['id' => $requests->id]);
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    $dropdown .= '<button onclick="window.open(\'' . $route . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>';
                    $dropdown .= '</div>
                  </div>
                ';

                    return $dropdown;
            });

        return $datatables->make(true);
    }
    public function closed_index(){
        $case_nature = CrmRequestCaseNature::select('id', 'name')->get();
        $case_nature_type = CrmRequestCaseNatureType::select('id', 'type')->get();
        $channels = CrmRequestChannel::select('id', 'channel')->get();
        return view('admin.crm.closed')->with(['case_nature' => $case_nature, 'case_nature_type' => $case_nature_type, 'channels' => $channels]);
    }

    public function closed_list(Request $request){
        $closed_request = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', 'a.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('users as u', 'u.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('substitute_users as su', 'su.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->leftJoin('crm_request_status_histories as inp', function ($join) {
                $join->on('inp.crm_request_id', '=', 'crm_requests.id')
                    ->where('inp.created_at','=',
                        DB::raw('(select max(created_at) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 1)'));
            })
            ->leftJoin('crm_request_status_histories as res', function ($join) {
                $join->on('res.crm_request_id', '=', 'crm_requests.id')
                    ->where('res.created_at','=',
                        DB::raw('(select max(created_at) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id and crm_request_status_histories.status_id = 4)'));
            })
            ->select('crm_requests.id as id', 's.tracking_number as tracking_number', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'ad.name as agent', 'a.name as name', 'u.name as shipper', 'su.name as sub_shipper', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at', 'crm_requests.description as description','inp.created_at as inprocess','res.created_at as closed')
            ->where('crm_requests.status_id', 4);
            if(!in_array(session('role_id'), [1,6])){
                $closed_request = $closed_request->where(function($query) {
                    $query->where('crm_requests.agent_id', Auth::id());
                });
            }

        $datatables = Datatables::of($closed_request)
            ->addColumn('tracking_number_hyperlink', function ($requests) {
                return '<u><a href=' . route('admin.tracking.index') . '?tracking_number=' . $requests->tracking_number . ' class="tracking" target="_blank">' . $requests->tracking_number . '</a></u>';
            })
            ->editColumn('added_by', function($requests){
                if($requests->launched_added_by == 0) {
                    return 'Admin';
                }
                else if($requests->launched_added_by == 1) {
                    return 'Shipper';
                }
                else{
                    return 'Shipper Substitute User';
                }
            })
            ->editColumn('total_tat', function ($requests){
                if($requests->inprocess && $requests->closed){
                    $process = Carbon::parse($requests->inprocess);
                    $closed = Carbon::parse($requests->closed);
                    return $closed->diffForHumans($process);
                }
                return "-";
            })
            ->addColumn('launched_by_name', function ($requests){
                $name = '';
                if($requests->launched_added_by == 0){
                    $name = $requests->name;
                }
                else if($requests->launched_added_by == 1){
                    $name = $requests->shipper;
                }else{
                    $name = $requests->sub_shipper;
                }
                return $name;
            })
            ->filterColumn('launched_by_name', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 0)
                            ->where('a.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 1)
                                ->where('u.name', 'like', '%' . $keyword . '%');
                        })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 2)
                                ->where('su.name', 'like', '%' . $keyword . '%');
                        });
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('launched_by_name', DB::raw('IF (crm_requests.launched_by = 0, a.name, IF (crm_requests.launched_by = 1, u.name, IF (crm_requests.launched_by = 2, su.name, "")))') . ' $1')
            ->editColumn('agent', function ($requests){
                if($requests->agent == null){
                    return '-';
                }
                else{
                    return $requests->agent;
                }
            })
            ->filterColumn('case_nature_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('crcnt.id','=',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action', function($requests) {
                $route = route('admin.crm.request.details', ['id' => $requests->id]);
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    $dropdown .= '<button onclick="window.open(\'' . $route . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>';
                    $dropdown .= '</div>
                  </div>
                ';

                    return $dropdown;
            });

        return $datatables->make(true);
    }

    public function assign(Request $request){
//        if($request->multiple == 0) {
//            $crm_requests = CrmRequest::find($request->crm_request_id);
//
//            if ($request->crm_request_id == $crm_requests->id) {
//                if ($crm_requests->agent_id != $request->admin_id) {
//                    CrmRequestAgentHistory::create([
//                        'crm_request_id' => $crm_requests->id,
//                        'agent_id' => $request->admin_id
//                    ]);
//                    $crm_requests->agent_id = $request->admin_id;
//                    $crm_requests->save();
//                    return ['status' => 0, 'success' => 'Request has been Assigned'];
//                }
//                return ['status' => 1, 'error' => 'Request is already Assigned to Agent'];
//            }
//        }
//        elseif ($request->multiple == 1) {
            foreach ($request->crm_request_ids as $crm_request_id)
            {
                $crm_requests = CrmRequest::find($crm_request_id);
                if ($crm_request_id == $crm_requests->id) {
                    CrmRequestAgentHistory::create([
                        'crm_request_id' => $crm_requests->id,
                        'agent_id' => $request->admin_id
                    ]);
                    $crm_requests->agent_id = $request->admin_id;
                    $crm_requests->save();
                }
            }
            return ['status' => 0, 'success' => 'Request(s) has been Assigned'];
//        }
    }

    public function valid(Request $request)
    {
        $crm_request = CrmRequest::where('id', $request->id)->first();
        if ($crm_request['agent_id'] != null) {
            if ($request->prev_status == 1 || $request->prev_status == 5) {
                if ($crm_request['status_id'] != 2) {
                    CrmRequest::where('id', $request->id)->update([
                        'status_id' => 2
                    ]);
                    CrmRequestStatusHistory::create([
                        'crm_request_id' => $request->id,
                        'status_id' => 2,
                        'agent_id' => Auth::id()
                    ]);
                    return ['status' => 0, 'success' => 'Request marked as In-Process', 'marked_status' => 2];
                } else {
                    return ['status' => 1, 'error' => 'Request is already marked as In-Process'];
                }
            }
            if ($request->prev_status == 2) {
                if ($crm_request['status_id'] != 3) {
                    CrmRequest::where('id', $request->id)->update([
                        'status_id' => 3
                    ]);
                    CrmRequestStatusHistory::create([
                        'crm_request_id' => $request->id,
                        'status_id' => 3,
                        'agent_id' => Auth::id()
                    ]);
                    return ['status' => 0, 'success' => 'Request marked as Resolved', 'marked_status' => 3];
                } else {
                    return ['status' => 1, 'error' => 'Request is already marked as Re-Open'];
                }
            }
            if ($request->prev_status == 3) {
                if ($crm_request['status_id'] != 4) {
                    CrmRequest::where('id', $request->id)->update([
                        'status_id' => 4
                    ]);
                    CrmRequestStatusHistory::create([
                        'crm_request_id' => $request->id,
                        'status_id' => 4,
                        'agent_id' => Auth::id()
                    ]);

                    CrmRequestTagging::where('crm_request_id', $request->id)->delete();
                    return ['status' => 0, 'success' => 'Request marked as Closed', 'marked_status' => 4];
                } else {
                    return ['status' => 1, 'error' => 'Request is already marked as Resolved'];
                }
            }
            if ($request->prev_status == 4) {
                if ($crm_request['status_id'] != 5) {
                    CrmRequest::where('id', $request->id)->update([
                        'status_id' => 5
                    ]);
                    CrmRequestStatusHistory::create([
                        'crm_request_id' => $request->id,
                        'status_id' => $crm_request['status_id'],
                        'agent_id' => $crm_request['agent_id']
                    ]);
                    return ['status' => 0, 'success' => 'Request marked as Re-Open', 'marked_status' => 5];
                } else {
                    return ['status' => 1, 'error' => 'Request is already marked as Re-Open'];
                }
            }
        }
        else{
            return ['status' => 1, 'error' => 'Agent is not assigned yet'];
        }
    }

    public function invalid(Request $request)
    {
        $crm_request = CrmRequest::where('id', $request->id)->first();
        if ($crm_request['agent_id'] != null) {
            if ($crm_request['status_id'] != 4) {
                CrmRequest::where('id', $request->id)->update([
                    'status_id' => 4
                ]);
                CrmRequestStatusHistory::create([
                    'crm_request_id' => $request->id,
                    'status_id' => 4,
                    'agent_id' => Auth::id()
                ]);

                CrmRequestTagging::where('crm_request_id', $request->id)->delete();
                return ['status' => 0, 'success' => 'Request marked as Closed'];
            } else {
                return ['status' => 1, 'error' => 'Request is already marked as Closed'];
            }
        }
        else{
            return ['status' => 1, 'error' => 'Agent is not assigned yet'];
        }
    }

    public function admin_tag(Request $request){
        $crm_request = CrmRequest::where('id', $request->crm_request_id)->first();
        if($request->crm_request_tagging_type_id == 1){
            $name = AdminDepartment::where('id', $request->tagged_id)->first();
        }
        else if($request->crm_request_tagging_type_id == 2){
            $name = Admin::where('id', $request->tagged_id)->first();
        }
        $tagged_crm_request = CrmRequestTagging::where('crm_request_id', $request->crm_request_id)->first();
        if(!empty($tagged_crm_request)){
            if($tagged_crm_request['tagged_id'] != $request->tagged_id) {
                CrmRequestTagging::where('crm_request_id', $request->crm_request_id)->update([
                    'crm_request_tagging_type_id' => $request->crm_request_tagging_type_id,
                    'tagged_id' => $request->tagged_id
                ]);

                CrmRequestTaggingHistory::create([
                    'crm_request_id' => $request->crm_request_id,
                    'crm_request_tagging_type_id' => $request->crm_request_tagging_type_id,
                    'tagged_id' => $request->tagged_id,
                    'agent_id' => Auth::id()
                ]);

                if ($request->prev_status == 3) {
                    CrmRequest::where('id', $request->crm_request_id)->update([
                        'status_id' => 2
                    ]);
                    CrmRequestStatusHistory::create([
                        'crm_request_id' => $request->crm_request_id,
                        'status_id' => $crm_request['status_id'],
                        'agent_id' => Auth::id()
                    ]);
                }
                NotificationsController::send(31,$request->crm_request_id);
            }
            else{
                return ['status' => 1, 'error' => 'Request is already tagged to ' . $name['name']];
            }
        }
        else{
            CrmRequestTagging::create([
                'crm_request_id' => $request->crm_request_id,
                'crm_request_tagging_type_id' => $request->crm_request_tagging_type_id,
                'tagged_id' => $request->tagged_id
            ]);

            CrmRequestTaggingHistory::create([
                'crm_request_id' => $request->crm_request_id,
                'crm_request_tagging_type_id' => $request->crm_request_tagging_type_id,
                'tagged_id' => $request->tagged_id,
                'agent_id' => Auth::id()
            ]);
            NotificationsController::send(31,$request->crm_request_id);
        }
        return ['status' => 0, 'success' => 'Request successfully tagged to ' . $name['name']];
    }
    public function crm_index(){
        return view('admin.crm.index');
    }

    public function crm_list(){
        $roles = AdminRole::join('admin_departments as ad', 'admin_roles.department_id', '=', 'ad.id')
            ->join('admins as a', 'admin_roles.updated_by', '=', 'a.id')
            ->select('admin_roles.id', 'admin_roles.name', 'ad.name as department', 'admin_roles.created_at', 'admin_roles.updated_at', 'a.name as updated_by')
            ->where('admin_roles.department_id', '=', 3);

        $datatables = Datatables::of($roles)
            ->addColumn('action', function($role) {
                if (session('role_id') == 1 || session('role_id') == 6 || in_array(188, session('permissions'))) {
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

        return $datatables->make(true);

    }

    public function crm_update_index($id){
        $departments = AdminDepartment::where('id', '!=', 1)->get(['id', 'name']);
        $modules = Module::with('permissions')->where('id', '=', 18)->get();
        $role = AdminRole::find($id);
        $permissions = $role->module_permissions->pluck('permission_id')->toArray();

        if ($role->id != 1) {
            return view('admin.crm.update.index')->with(['departments' => $departments, 'modules' => $modules, 'role' => $role, 'permissions' => $permissions]);
        }
        else {
            return redirect()->route('admin.access_denied');
        }
    }

    public function crm_update_store(Request $request, $id) {
        $admin_role = AdminRole::find($id);
        $admin_role->updated_by = Auth::id();

        $admin_role->save();

        if ($request->has('permission_ids')) {
            $current_permission_ids = AdminRoleModulePermission::where('role_id', $id)->pluck('permission_id')->toArray();;

            $crm_module_permission = ModulePermission::where('module_id', '!=', 18)->pluck('id')->toArray();
            $crm_module_main_permission = ModulePermission::where('id', '=', 188)->first();
            $delete_permission_ids = array_diff($current_permission_ids, $request->input('permission_ids'));
            $new_permission_ids = array_diff($request->input('permission_ids'), $current_permission_ids);

            AdminRoleModulePermission::where('role_id', $id)->where('permission_id', '!=', $crm_module_main_permission['id'])->whereNotIn('permission_id', $crm_module_permission)->whereIn('permission_id', $delete_permission_ids)->delete();

            foreach($new_permission_ids as $permission_id) {
                $admin_role_module_permission = new AdminRoleModulePermission();

                $admin_role_module_permission->role_id = $id;
                $admin_role_module_permission->permission_id = $permission_id;

                $admin_role_module_permission->save();
            }
        }
        else {
            AdminRoleModulePermission::where('role_id', $id)->delete();
        }

        return redirect()->route('admin.crm.permissions')->with(['success' => 'CRM Permissions: ' . $request->input('name') . ' has been updated!']);
    }
}
