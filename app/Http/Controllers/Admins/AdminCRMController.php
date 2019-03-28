<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\CRM\CRMCommentController;
use App\Http\Controllers\CRM\CRMController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\CRM\CrmComments;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestAgentHistory;
use App\Http\Models\CRM\CrmRequestStatus;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\Shipment;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\Shipper\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;

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
        if(!empty($shipment_ids)){
            foreach ($shipment_ids as $shipment_id) {
                $shipment = Shipment::find($shipment_id);

//                $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->first();
//                if($is_shipment){
//                    if($is_shipment->case_nature_id != $nature_id){
                        CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 0, $shipment_id, $shipment->user_id, NULL ,$description);
//                    }else{
//                        $shipment = Shipment::find($shipment_id);
//                        $present_shipments[] = $shipment->tracking_number;
//                        $flag = true;
//                    }
//                }

            }
            return ['status' => 1, 'success' => 'Request(s) successfully added'];
        }else{
            return ['status' => 0, 'error' => 'No shipments selected!'];
        }
    }

    public function update_request(Request $request){
        return $request;
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
        if($crm_request){
            return view('admin.crm.request_details')->with(['crm_details' => $crm_request, 'launched_by' => $launched_by, 'comments' => $crm_comments, 'last_comment_id' => $last_comment]);
        }else{
            return redirect()->back()->with('danger', 'CRM Request Not found!');
        }
    }

    public function add_comment(Request $request){
        $comment = $request->comment;
        $request_id = $request->request_id;
        $comment_by = 0;
        if($comment == null){
            return ['status' => 0, 'error' => 'Comment Not selected!'];
        }
        if(!$request_id){
            return ['status' => 0, 'error' => 'Request ID Not selected!'];
        }

        CRMCommentController::add($request_id, Auth::id(),$comment_by,0, $comment);
        $last_comment = CrmComments::where('crm_request_id', $request_id)->where('comment_by',0)->latest()->first();
        return ['status' => 1, 'success' => 'Comment successfully added', 'last_comment_id' => $last_comment->id];
    }

    public function get_latest_comment(Request $request){
        $comment_id = $request->comment_id;
        $request_id = $request->request_id;
        if(($comment_id != null) && ($request_id != null)){
            $comment_details = CrmComments::where('crm_request_id', $request_id)->where('comment_by','!=',0)->latest()->first();
            if($comment_details->id > $comment_id){
                return ['status' => 1, 'comment' => $comment_details];
            }
        }


    }

    public function launched_re_open_index(){
        $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->select('id', 'name')->get();
        $case_nature_type = CrmRequestCaseNatureType::select('id', 'type')->get();
        $channels = CrmRequestChannel::where('id', '>', 2)->select('id', 'channel')->get();
        $status = CrmRequestStatus::whereIn('id', [1,5])->select('id', 'name')->get();
        $agents = Admin::whereIn('role_id',[6,13])->get();
        return view('admin.crm.launched_re_open')->with(['case_nature' => $case_nature, 'case_nature_type' => $case_nature_type, 'channels' => $channels, 'status' => $status, 'agents' => $agents]);
    }

    public function launched_re_open_list(Request $request){
        $launched_request = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('crm_request_statuses as crs', 'crs.id', '=', 'crm_requests.status_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', 'a.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->select('crm_requests.id as id', 's.tracking_number as tracking_number', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'crs.name as status', 'ad.name as agent', 'a.name as name', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at', 'crm_requests.description as description')
        ->whereIn('crm_requests.status_id', [1,5]);
        $datatables = Datatables::of($launched_request)
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
                $route = route('admin.crm.request.details', ['id' => $requests->id, 'agent' => $requests->agent]);
                if (session('role_id') == 1 || in_array(179, session('permissions'))) {
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    if (session('role_id') == 1 || in_array(183, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item assign"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Assign Agent</div></button>';
                    }
                    $dropdown .= '<button onclick="window.open(\'' . $route . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>';
                    $dropdown .= '</div>
                  </div>
                ';

                    return $dropdown;
                }
                else {
                    '';
                }
            });

        return $datatables->make(true);
    }
    public function in_process_index(){
        $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->select('id', 'name')->get();
        $case_nature_type = CrmRequestCaseNatureType::select('id', 'type')->get();
        $channels = CrmRequestChannel::where('id', '>', 2)->select('id', 'channel')->get();
        $agents = Admin::whereIn('role_id',[6,13])->get();
        return view('admin.crm.in_process')->with(['case_nature' => $case_nature, 'case_nature_type' => $case_nature_type, 'channels' => $channels, 'agents' => $agents]);
    }

    public function in_process_list(Request $request){
        $in_process_request = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', 'a.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->select('crm_requests.id as id', 's.tracking_number as tracking_number', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'ad.name as agent', 'a.name as name', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at', 'crm_requests.description as description')
            ->where(['crm_requests.status_id' => 2]);
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
            ->editColumn('agent', function ($requests){
                if($requests->agent == null){
                    return '-';
                }
                else{
                    return $requests->agent;
                }
            })
            ->addColumn('action', function($requests) {
                $route = route('admin.crm.request.details', ['id' => $requests->id]);
                if (session('role_id') == 1 || in_array(180, session('permissions'))) {
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    if (session('role_id') == 1 || in_array(183, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item assign"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Assign Agent</div></button>';
                    }
                    $dropdown .= '<button onclick="window.open(\'' . $route . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>';
                    $dropdown .= '</div>
                  </div>
                ';

                    return $dropdown;
                }
                else {
                    '';
                }
            });

        return $datatables->make(true);
    }

    public function resolved_index(){
        $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->select('id', 'name')->get();
        $case_nature_type = CrmRequestCaseNatureType::select('id', 'type')->get();
        $channels = CrmRequestChannel::where('id', '>', 2)->select('id', 'channel')->get();
        $agents = Admin::whereIn('role_id',[6,13])->get();
        return view('admin.crm.resolved')->with(['case_nature' => $case_nature, 'case_nature_type' => $case_nature_type, 'channels' => $channels, 'agents' => $agents]);
    }

    public function resolved_list(Request $request){
        $in_process_request = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', 'a.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->select('crm_requests.id as id', 's.tracking_number as tracking_number', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'ad.name as agent', 'a.name as name', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at', 'crm_requests.description as description')
            ->where(['crm_requests.status_id' => 3]);
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
            ->editColumn('agent', function ($requests){
                if($requests->agent == null){
                    return '-';
                }
                else{
                    return $requests->agent;
                }
            })
            ->addColumn('action', function($requests) {
                $route = route('admin.crm.request.details', ['id' => $requests->id]);
                if (session('role_id') == 1 || in_array(181, session('permissions'))) {
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    if (session('role_id') == 1 || in_array(183, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item assign"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Assign Agent</div></button>';
                    }
                    $dropdown .= '<button onclick="window.open(\'' . $route . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>';
                    $dropdown .= '</div>
                  </div>
                ';

                    return $dropdown;
                }
                else {
                    '';
                }
            });

        return $datatables->make(true);
    }
    public function closed_index(){
        $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->select('id', 'name')->get();
        $case_nature_type = CrmRequestCaseNatureType::select('id', 'type')->get();
        $channels = CrmRequestChannel::where('id', '>', 2)->select('id', 'channel')->get();
        return view('admin.crm.closed')->with(['case_nature' => $case_nature, 'case_nature_type' => $case_nature_type, 'channels' => $channels]);
    }

    public function closed_list(Request $request){
        $in_process_request = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', 'a.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->select('s.tracking_number as tracking_number', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'ad.name as agent', 'a.name as name', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at', 'crm_requests.description as description')
            ->where(['crm_requests.status_id' => 4]);
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
            ->editColumn('agent', function ($requests){
                if($requests->agent == null){
                    return '-';
                }
                else{
                    return $requests->agent;
                }
            })
            ->addColumn('action', function($requests) {
                $route = route('admin.crm.request.details', ['id' => $requests->id]);
                if (session('role_id') == 1 || in_array(182, session('permissions'))) {
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    if (session('role_id') == 1 || in_array(183, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item assign"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Assign Agent</div></button>';
                    }
                    $dropdown .= '<button onclick="window.open(\'' . $route . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>';
                    $dropdown .= '</div>
                  </div>
                ';

                    return $dropdown;
                }
                else {
                    '';
                }
            });

        return $datatables->make(true);
    }
    
    public function assign(Request $request){
        if($request->multiple == 0) {
            $crm_requests = CrmRequest::find($request->crm_request_id);

            if ($request->crm_request_id == $crm_requests->id) {
                if ($crm_requests->agent_id != $request->admin_id) {
                    if ($crm_requests->agent_id != null) {
                        CrmRequestAgentHistory::create([
                            'crm_request_id' => $crm_requests->id,
                            'agent_id' => $crm_requests->agent_id
                        ]);
                    }
                    $crm_requests->agent_id = $request->admin_id;
                    $crm_requests->save();
                    return ['status' => 0, 'success' => 'Request has been Assigned'];
                }
                return ['status' => 1, 'error' => 'Request is already Assigned to Agent'];
            }
        }
        elseif ($request->multiple == 1) {
            foreach ($request->crm_request_ids as $crm_request_id)
            {
                $crm_requests = CrmRequest::find($crm_request_id);
                if ($crm_request_id == $crm_requests->id) {
                        if ($crm_requests->agent_id != null) {
                            if ($crm_requests->agent_id != $request->admin_id) {
                                CrmRequestAgentHistory::create([
                                    'crm_request_id' => $crm_requests->id,
                                    'agent_id' => $crm_requests->agent_id
                                ]);
                            }
                        }
                        $crm_requests->agent_id = $request->admin_id;
                        $crm_requests->save();
                    }
            }
            return ['status' => 0, 'success' => 'Request(s) has been Assigned'];
        }
    }

    public function valid(Request $request){
        $crm_request = CrmRequest::where('id',$request->id)->first();
        if($crm_request['status_id'] != $request->status){
            CrmRequest::where('id',$request->id)->update([
                'status_id' => $request->status
            ]);
            CrmRequestStatusHistory::create([
                'crm_request_id' => $request->id,
                'status_id' => $crm_request['status_id'],
                'agent_id' => Auth::id()
            ]);
            return ['status' => 0, 'success' => 'Request marked as In-Process'];
        }
        else{
            return ['status' => 1, 'error' => 'Request is already marked as In-Process'];
        }
    }

    public function invalid(Request $request){
        $crm_request = CrmRequest::where('id',$request->id)->first();
        if($crm_request['status_id'] != $request->status){
            CrmRequest::where('id',$request->id)->update([
                'status_id' => $request->status
            ]);
            CrmRequestStatusHistory::create([
                'crm_request_id' => $request->id,
                'status_id' => $crm_request['status_id'],
                'agent_id' => Auth::id()
            ]);
            return ['status' => 0, 'success' => 'Request marked as Closed'];
        }
        else{
            return ['status' => 1, 'error' => 'Request is already marked as Closed'];
        }
    }
}
