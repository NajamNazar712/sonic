<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\CRM\CRMCommentController;
use App\Http\Controllers\CRM\CRMController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\CRM\CrmComments;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\CRM\CrmRequestStatus;
use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\Shipper\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class ShipperCRMController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function index(){
        $case_nature = CrmRequestCaseNature::all(['id', 'name']);
        $case_nature_type = CrmRequestCaseNatureType::select('id', 'type')->get();
        $channels = CrmRequestChannel::select('id', 'channel')->get();
        $status = CrmRequestStatus::where('id', '!=', 3)->select('id', 'name')->get();
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        return view('client.crm.requests')->with(['case_nature' => $case_nature, 'case_nature_type' => $case_nature_type, 'channels' => $channels, 'status' => $status, 'shipment_status' => $shipment_status]);
    }
    public function requests_list(Request $request){
        $launched_request = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('crm_request_statuses as crs', 'crs.id', '=', 'crm_requests.status_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', 'a.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 's.shipper_status_id')
            ->select('crm_requests.id as id', 's.tracking_number as tracking_number', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'crs.name as request_status', 'ad.name as agent', 'a.name as name', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at','crm_requests.description','crm_requests.status_id', 'ss.name as shipment_status')
            ->where('crm_requests.shipper_id', session('user_id'));
        $datatables = Datatables::of($launched_request)
            ->addColumn('id_padded', function ($requests) {
                return str_pad($requests->id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('id_padded_link', function ($requests) {
                return '<u><a href=' . route('cod.crm.request.details', ['id' => $requests->id]) . ' target="_blank">' . str_pad($requests->id, 6, '0', STR_PAD_LEFT). '</a></u>';
            })
            ->addColumn('tracking_number_hyperlink', function ($requests) {
                return '<u><a href=' . route('cod.tracking.index') . '?tracking_number=' . $requests->tracking_number . ' class="tracking" target="_blank">' . $requests->tracking_number . '</a></u>';
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
            ->addColumn('status',function ($requests){
                if($requests->status_id == 3){
                    return 'In-Process';
                }else{
                    return $requests->request_status;
                }
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    if ($keyword == 2) {
                        $query->whereIn('crm_requests.status_id',[2,3]);
                    }
                    else {
                        $query->where('crm_requests.status_id', '=', $keyword);
                    }
                }
            })
            ->filterColumn('shipment_status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
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
                $route = route('cod.crm.request.details', ['id' => $requests->id]);
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                        <button onclick="window.open(\'' . $route . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>
                        
                    </div>
                  </div>
                ';

                    return $dropdown;

            });

        return $datatables->make(true);
    }

    public function request_details(Request $request, $id){
        $crm_request = CrmRequest::find($id);
        if($crm_request->shipper_id == session('user_id')){
            $shipment_status = null;
            if($crm_request->shipment_id != null) {
                $shipment_status = Shipment::find($crm_request->shipment_id);
                $shipment_status = $shipment_status->status_shipper->name;
            }
            $crm_comments = array();
            $last_comment = null;
            $crm_comments = CrmComments::where('crm_request_id', $id)->where('comment_type',0);
            if($crm_comments->exists()){
                $crm_comments = $crm_comments->orderBy('created_at','asc')->get();
                $last_comment = CrmComments::where('crm_request_id', $id)->latest()->first();
                $last_comment = $last_comment->id;
            }
            $launched_by  = '';
            if($crm_request->launched_by == 0){
                $launched_by = 'Agent';
            }else if($crm_request->launched_by == 1){
                $launched_by = User::find($crm_request->launched_by_id)->name;
            }else if($crm_request->launched_by == 2){
                $launched_by = SubstituteUser::find($crm_request->launched_by_id)->name;
            }
            if($crm_request){
                return view('client.crm.details')->with(['crm_details' => $crm_request, 'launched_by' => $launched_by, 'comments' => $crm_comments, 'last_comment_id' => $last_comment, 'shipment_status' => $shipment_status]);
            }else{
                return redirect()->back()->with('danger', 'CRM Request Not found!');
            }
        }else{
            return redirect()->route('cod.crm.request.index')->with('error', '404 Not Found!');
        }
    }

    public function add_request(Request $request){
        $nature_id = $request->case_nature_id;
        $complaint_id = $request->complaint_id;
        $shipment_ids = $request->shipment_ids;
        $description = $request->description;
        $launched_by = 1;
        $present_shipments = array();
        $flag = false;
        if(session('user_type') == 2){
            $launched_by = 2;
        }
        if ($request->has('payment_request')) {
            if($request->payment_request == 1){
                $payment_id = $request->payment_id;
                $payment_id_padded = str_pad($request->payment_id, 6, 0, STR_PAD_LEFT);
                if(!empty($payment_id)){
                    $payment = DonePayment::find($payment_id);
                    $payment_shipment = DonePaymentShipment::where('done_payment_id', $payment->id)->first();
                    $shipment = Shipment::where('id', $payment_shipment->shipment_id)->first();
                    $is_shipment = CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_id', $nature_id);
                    if($is_shipment->exists()){
                        return ['status' => 0, 'error' => 'Request/Complaint already lodged for the Payment ID: ' . $payment_id_padded];
                    }
                    else{
                        CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment->id, session('user_id'), NULL, $description);
                    }
                    return ['status' => 1, 'success' => 'Request(s) successfully added'];
                }else{
                    return ['status' => 0, 'error' => 'No Payment selected!'];
                }
            }
        }
        else{
            if(!empty($shipment_ids)){
                foreach ($shipment_ids as $shipment_id) {
                    $shipment = Shipment::find($shipment_id);
                    if($shipment){
                        $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->first();
                        if($is_shipment){
                            if($is_shipment->case_nature_id != $nature_id){
                                CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                            }else{

                                $present_shipments[] = $shipment->tracking_number;
                                $flag = true;
                            }
                        }else{
                            CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                        }
                    }

                }
                return ['status' => 1, 'success' => 'Request(s) successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
//            return ['status' => 1, 'success' => 'Request(s) successfully added'];
            }else{
                return ['status' => 0, 'error' => 'No shipments selected!'];
            }
        }
    }

    public function add_feedback(Request $request){
        $nature_id = 3;
        $channel_id = 1;
        $description = $request->description;
        $launched_by = 1;
        $shipment_ids = $request->shipment_ids;
        $flag = false;
        $present_shipments = null;
        if($shipment_ids != null) {
            foreach ($shipment_ids as $shipment_id) {
                if (session('user_type') == 2) {
                    $launched_by = 2;
                }
                if ($channel_id == null) {
                    return ['status' => 0, 'error' => 'Channel Not selected!'];
                }
                if ($description == null) {
                    return ['status' => 0, 'error' => 'Description Not Entered!'];
                }
                $shipment = Shipment::find($shipment_id);
                if($shipment) {
                    $is_shipment = CrmRequest::where('shipment_id', $shipment_id)->where('case_nature_id', $nature_id)->first();
                    if (!$is_shipment) {
                        CRMController::add($nature_id, NULL, $channel_id, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                    } else {

                        $present_shipments[] = $shipment->tracking_number;
                        $flag = true;
                    }
                }else{
                    CRMController::add($nature_id, null, $channel_id, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                }
            }
        }
        else{
            if (session('user_type') == 2) {
                $launched_by = 2;
            }
            if ($channel_id == null) {
                return ['status' => 0, 'error' => 'Channel Not selected!'];
            }
            if ($description == null) {
                return ['status' => 0, 'error' => 'Description Not Entered!'];
            }

            CRMController::add($nature_id, NULL, $channel_id, 1, Auth::id(), $launched_by, null, session('user_id'), NULL, $description);
        }
        return ['status' => 1, 'success' => 'Feedback successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
    }

    public function add_comment(Request $request){
        $comment = $request->comment;
        $request_id = $request->request_id;
        $comment_by = 1;
        if($comment == null){
            return ['status' => 0, 'error' => 'Comment Not selected!'];
        }
        if(!$request_id){
            return ['status' => 0, 'error' => 'Request ID Not selected!'];
        }
        if(session('user_type') == 2){
            $comment_by = 2;
        }
        CRMCommentController::add($request_id, Auth::id(),$comment_by,0, $comment);
        $last_comment = CrmComments::where('crm_request_id', $request_id)->latest()->first();
        return ['status' => 1, 'success' => 'Comment successfully added', 'last_comment_id' => $last_comment->id];

    }

    public function get_latest_comment(Request $request){
        $comment_id = $request->comment_id;
        $request_id = $request->request_id;
        if(($comment_id != null) && ($request_id != null)){
            $comment_details = CrmComments::where('crm_request_id', $request_id)->where('comment_type',0)->latest()->first();
            if($comment_details){
                $name = '';
                if($comment_details->id > $comment_id){
                    if($comment_details->comment_by == 2){
                        $name = $comment_details->substitute_user->name;
                    }else if($comment_details->comment_by == 0){
                        $name = 'Agent';
                    }
                    return ['status' => 1, 'comment' => $comment_details, 'name' => $name];
                }
            }
        }


    }

}
