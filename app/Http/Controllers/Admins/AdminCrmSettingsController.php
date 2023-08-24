<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\AdminRole;
use App\Http\Models\City;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmSettings;
use App\Http\Models\CRM\CrmTatHolidays;
use App\Http\Models\CRM\Escalation\CrmEscalation;
use App\Http\Models\CRM\Escalation\CrmEscalationLevel;
use App\Http\Models\CRM\Escalation\CrmEscalationShipmentStatus;
use App\Http\Models\CRM\Escalation\CrmEscalationTagging;
use App\Http\Models\CRM\Escalation\CrmEscalationTaggingHub;
use App\Http\Models\CRM\Escalation\CrmEscalationTaggingLevel;
use App\Http\Models\CRM\Escalation\CrmEscalationTaggingLevelEmail;
use App\Http\Models\CRM\Escalation\CrmEscalationTaggingLevelRole;
use App\Http\Models\CRM\Escalation\CrmEscalationTaggingShipmentStatus;
use App\Http\Models\ShipmentStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\GlobalSettings;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use DB;

class AdminCrmSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function crm_cut_off_time_and_holidays_index(){
        $from = CrmSettings::where('name','TAT Cut-Off Time From')->first();
        $to = CrmSettings::where('name','TAT Cut-Off Time To')->first();

        $cut_off_time_from = $from->setting_value;
        $cut_off_time_to = $to->setting_value;
        return view('admin.settings.crm_cut_off_time_and_holidays')->with(['cut_off_time_from' => $cut_off_time_from, 'cut_off_time_to' => $cut_off_time_to]);
    }
    public function crm_cut_off_time_and_holidays_update(Request $request){
        $from = $request->cut_off_time_from;
        $to = $request->cut_off_time_to;
        if($from == $to){
            return redirect()->back()->with('error', 'Cutt-Off time can\'t be same');
        }
        else{
            $cut_off_time_from = CrmSettings::where('name','TAT Cut-Off Time From')->first();
            $cut_off_time_from->setting_value = $from;
            $cut_off_time_from->save();

            $cut_off_time_to = CrmSettings::where('name','TAT Cut-Off Time To')->first();
            $cut_off_time_to->setting_value = $to;
            $cut_off_time_to->save();

            return redirect()->back()->with('success', 'Cut-Off time is Updated Successfully!');
        }
    }
    public function crm_cut_off_time_and_holidays_list(){
        $holidays = CrmTatHolidays::leftjoin('admins as a', 'a.id', '=', 'crm_tat_holidays.created_by')
            ->select('crm_tat_holidays.reason as reason', 'crm_tat_holidays.holiday as holiday', 'crm_tat_holidays.created_at as created_at', 'a.name as created_by');

        return Datatables::of($holidays)
            ->make(true);
    }
    public function crm_cut_off_time_and_holidays_add(Request $request){
        $holiday_date = $request->holiday_date;
        $holiday_reason = $request->holiday_reason;
        $existing_holiday = CrmTatHolidays::where('holiday', $holiday_date);
        if ($existing_holiday->exists()){
            return ['status' => 0, 'error' => 'Holiday is already marked on the selected date!'];
        }
        else{
            $new_holiday = new CrmTatHolidays();
            $new_holiday->holiday = $holiday_date;
            $new_holiday->reason = $holiday_reason;
            $new_holiday->created_by = Auth::id();
            $new_holiday->save();
            return ['status' => 1, 'success' => 'Holiday added successfully!'];
        }
    }

    public function escalation_launched_index(){
        return view('admin.settings.CRM.escalation.launched.index');
    }

    public function escalation_launched_list(){
        $launched = CrmEscalation::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_escalations.case_nature')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_escalations.case_nature_type')
            ->leftjoin('admins as a', 'a.id', '=', 'crm_escalations.updated_by')
            ->select('crm_escalations.id as id', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crm_escalations.tat as tat', 'crm_escalations.mark_as as mark_as', 'crm_escalations.comment as comment', 'crm_escalations.updated_at as updated_at', 'a.name as updated_by', 'crm_escalations.status as status')
            ->where('crm_escalations.crm_request_status', 1);


        $datatables = Datatables::of($launched)
            ->editColumn('mark_as', function ($escalation) {
                if($escalation->mark_as == 1){
                    return 'Valid';
                }
                else{
                    return 'Invalid';
                }
            })
            ->editColumn('status', function ($escalation) {
                if($escalation->status == 1){
                    return 'Enabled';
                }
                else{
                    return 'Disabled';
                }
            })
            ->addColumn('view_statuses', function ($escalation) {
                return '<button class="btn btn-sm btn-outline-info align-middle">View</button>';
            })
            ->addColumn('action', function($escalation) {
                if (session('role_id') == 1 || count(array_intersect([83, 84, 507], session('permissions'))) !== 0) {
                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    $enable_button = '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    $disable_button = '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';

                    $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">
                ';

                    $dropdown .= $edit_button;

                    if ($escalation->status == 1) {
                        $dropdown .= $disable_button;
                    }
                    else {
                        $dropdown .= $enable_button;
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
        return $datatables->make(true);
    }

    public function escalation_launched_add_index(){
        $shipment_statuses = ShipmentStatus::select('id','name')->where('id', '!=', 1)->get();
        $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
        return view('admin.settings.CRM.escalation.launched.add')->with(['shipment_statuses' => $shipment_statuses, 'case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_type_claims' => $case_nature_type_claims]);
    }

    public function escalation_launched_add_store(Request $request){
        $case_nature_id = $request->input('case_nature_select');
        if($case_nature_id == 1){
            $case_nature_type_id = $request->input('case_nature_complaint');
        }
        else if($case_nature_id == 2){
            $case_nature_type_id = $request->input('case_nature_request');
        }
        else if($case_nature_id == 4){
            $case_nature_type_id = $request->input('case_nature_claim');
        }
        $shipment_statuses = $request->shipment_statuses;
        $tat = $request->input('tat');
        $mark_as = $request->input('mark_as_select');
            
        $comment = $request->input('auto_comment');
        
        $launched_escalation = new CrmEscalation();
        $launched_escalation->crm_request_status = 1;
        $launched_escalation->case_nature = $case_nature_id;
        $launched_escalation->case_nature_type = $case_nature_type_id;
        $launched_escalation->tat = $tat;
        $launched_escalation->mark_as = $mark_as;
        $launched_escalation->comment = $comment;
        $launched_escalation->status = 1;
        $launched_escalation->updated_by = Auth::id();
        $launched_escalation->save();

        foreach ($shipment_statuses as $shipment_status){
            $launched_escalation_shipment_status = new CrmEscalationShipmentStatus();
            $launched_escalation_shipment_status->escalation_id = $launched_escalation->id;
            $launched_escalation_shipment_status->shipment_status_id = $shipment_status;
            $launched_escalation_shipment_status->save();
        }
        return redirect()->route('admin.settings.escalation.launched.index')->with('success', 'Launched Escalation Added Successfully');
    }

    public function escalation_launched_edit_index($id){
        $escalation = CrmEscalation::find($id);
        $statuses = array();
        foreach ($escalation->shipment_statuses as $shipment_status){
            $statuses[] = $shipment_status->shipment_status_id;
        }
        $shipment_statuses = ShipmentStatus::select('id','name')->where('id', '!=', 1)->get();
        $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
        return view('admin.settings.CRM.escalation.launched.edit')->with(['shipment_statuses' => $shipment_statuses, 'case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_type_claims' => $case_nature_type_claims, 'escalation' => $escalation, 'statuses' => $statuses]);
    }

    public function escalation_launched_edit_store(Request $request){
        $launched_escalation = CrmEscalation::find($request->id);
        $case_nature_id = $launched_escalation->case_nature;
        if($case_nature_id == 1){
            $case_nature_type_id = $request->input('case_nature_complaint');
        }
        else if($case_nature_id == 2){
            $case_nature_type_id = $request->input('case_nature_request');
        }
        else if($case_nature_id == 3){
            $case_nature_type_id = $request->input('case_nature_claim');
        }
        $shipment_statuses = $request->shipment_statuses;
        $tat = $request->input('tat');
        $mark_as = $request->input('mark_as_select');
        $comment = $request->input('auto_comment');
        $launched_escalation->case_nature_type = $case_nature_type_id;
        $launched_escalation->tat = $tat;
        $launched_escalation->mark_as = $mark_as;
        $launched_escalation->comment = $comment;
        $launched_escalation->status = 1;
        $launched_escalation->updated_by = Auth::id();
        $launched_escalation->save();
        CrmEscalationShipmentStatus::where('escalation_id', $launched_escalation->id)->delete();
        foreach ($shipment_statuses as $shipment_status){
            $launched_escalation_shipment_status = new CrmEscalationShipmentStatus();
            $launched_escalation_shipment_status->escalation_id = $launched_escalation->id;
            $launched_escalation_shipment_status->shipment_status_id = $shipment_status;
            $launched_escalation_shipment_status->save();
        }
        return redirect()->route('admin.settings.escalation.launched.index')->with('success', 'Launched Escalation Added Successfully');
    }

    public function escalation_in_process_index(){
        return view('admin.settings.CRM.escalation.in_process.index');
    }

    public function escalation_in_process_list(){
        $in_process = CrmEscalation::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_escalations.case_nature')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_escalations.case_nature_type')
            ->leftjoin('admins as a', 'a.id', '=', 'crm_escalations.updated_by')
            ->select('crm_escalations.id as id', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crm_escalations.tat as tat', 'crm_escalations.mark_as as mark_as', 'crm_escalations.comment as comment', 'crm_escalations.updated_at as updated_at', 'a.name as updated_by', 'crm_escalations.status as status')
        ->where('crm_escalations.crm_request_status', 2);


        $datatables = Datatables::of($in_process)
            ->editColumn('mark_as', function ($escalation) {
                if($escalation->mark_as == 1){
                    return 'Resolved';
                }
                else{
                    return 'In-Process';
                }
            })
            ->editColumn('status', function ($escalation) {
                if($escalation->status == 1){
                    return 'Enabled';
                }
                else{
                    return 'Disabled';
                }
            })
            ->addColumn('view_statuses', function ($escalation) {
                return '<button class="btn btn-sm btn-outline-info align-middle">View</button>';
            })
            ->addColumn('action', function($escalation) {
                if (session('role_id') == 1 || count(array_intersect([83, 84, 517], session('permissions'))) !== 0) {
                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    $enable_button = '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    $disable_button = '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';

                    $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">
                ';

                    $dropdown .= $edit_button;

                    if ($escalation->status == 1) {
                        $dropdown .= $disable_button;
                    }
                    else {
                        $dropdown .= $enable_button;
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
        return $datatables->make(true);
    }

    public function escalation_in_process_add_index(){
        $shipment_statuses = ShipmentStatus::select('id','name')->where('id', '!=', 1)->get();
        $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
        return view('admin.settings.CRM.escalation.in_process.add')->with(['shipment_statuses' => $shipment_statuses, 'case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_type_claims' => $case_nature_type_claims]);
    }

    public function escalation_in_process_add_store(Request $request){
        $case_nature_id = $request->input('case_nature_select');
        if($case_nature_id == 1){
            $case_nature_type_id = $request->input('case_nature_complaint');
        }
        else if($case_nature_id == 2){
            $case_nature_type_id = $request->input('case_nature_request');
        }
        else if($case_nature_id == 3){
            $case_nature_type_id = $request->input('case_nature_claim');
        }
        $shipment_statuses = $request->shipment_statuses;
        $tat = $request->input('tat');
        $mark_as = $request->input('mark_as_select');
        $comment = $request->input('auto_comment');

        $in_process_escalation = new CrmEscalation();
        $in_process_escalation->crm_request_status = 2;
        $in_process_escalation->case_nature = $case_nature_id;
        $in_process_escalation->case_nature_type = $case_nature_type_id;
        $in_process_escalation->tat = $tat;
        $in_process_escalation->mark_as = $mark_as;
        $in_process_escalation->comment = $comment;
        $in_process_escalation->status = 1;
        $in_process_escalation->updated_by = Auth::id();
        $in_process_escalation->save();

        foreach ($shipment_statuses as $shipment_status){
            $in_process_escalation_shipment_status = new CrmEscalationShipmentStatus();
            $in_process_escalation_shipment_status->escalation_id = $in_process_escalation->id;
            $in_process_escalation_shipment_status->shipment_status_id = $shipment_status;
            $in_process_escalation_shipment_status->save();
        }
        return redirect()->route('admin.settings.escalation.in_process.index')->with('success', 'In-Process Escalation Added Successfully');
    }

    public function escalation_in_process_edit_index($id){
        $escalation = CrmEscalation::find($id);
        $statuses = array();
        foreach ($escalation->shipment_statuses as $shipment_status){
            $statuses[] = $shipment_status->shipment_status_id;
        }
        $shipment_statuses = ShipmentStatus::select('id','name')->where('id', '!=', 1)->get();
        $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
        return view('admin.settings.CRM.escalation.in_process.edit')->with(['shipment_statuses' => $shipment_statuses, 'case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_type_claims' => $case_nature_type_claims, 'escalation' => $escalation, 'statuses' => $statuses]);
    }

    public function escalation_in_process_edit_store(Request $request){
        $in_process_escalation = CrmEscalation::find($request->id);
        $case_nature_id = $in_process_escalation->case_nature;
        if($case_nature_id == 1){
            $case_nature_type_id = $request->input('case_nature_complaint');
        }
        else if($case_nature_id == 2){
            $case_nature_type_id = $request->input('case_nature_request');
        }
        else if($case_nature_id == 3){
            $case_nature_type_id = $request->input('case_nature_claim');
        }
        $shipment_statuses = $request->shipment_statuses;
        $tat = $request->input('tat');
        $mark_as = $request->input('mark_as_select');
        $comment = $request->input('auto_comment');
        $in_process_escalation->case_nature_type = $case_nature_type_id;
        $in_process_escalation->tat = $tat;
        $in_process_escalation->mark_as = $mark_as;
        $in_process_escalation->comment = $comment;
        $in_process_escalation->status = 1;
        $in_process_escalation->updated_by = Auth::id();
        $in_process_escalation->save();
        CrmEscalationShipmentStatus::where('escalation_id', $in_process_escalation->id)->delete();
        foreach ($shipment_statuses as $shipment_status){
            $in_process_escalation_shipment_status = new CrmEscalationShipmentStatus();
            $in_process_escalation_shipment_status->escalation_id = $in_process_escalation->id;
            $in_process_escalation_shipment_status->shipment_status_id = $shipment_status;
            $in_process_escalation_shipment_status->save();
        }
        return redirect()->route('admin.settings.escalation.in_process.index')->with('success', 'In-Process Escalation Added Successfully');
    }

    public function escalation_status_update(Request $request){
        $escalation = CrmEscalation::find($request->id);
        $escalation->status = $request->status;
        $escalation->save();
        if($request->status == 1){
            $status = 'enabled';
        }
        else{
            $status = 'disabled';
        }
        return response()->json(['status' => 1, 'success' => 'Escalation ' . $status . ' successfully!']);
    }

    public function escalation_view_statuses(Request $request){
        $escalation = CrmEscalation::find($request->id);
        $statuses = array();
        foreach ($escalation->shipment_statuses as $shipment_status){
            $statuses[] = $shipment_status->status->name;
        }
        return response()->json(['status' => 1, 'statuses' => $statuses]);
    }

    public function escalation_level_index(){
        $levels = CrmEscalationLevel::get();
        return view('admin.settings.CRM.escalation.level')->with(['levels' => $levels]);
    }

    public function escalation_level_store(Request $request){
        foreach ($request->level as $index => $level){
            $index = $index +1;
            $existing_escaltion_level = CrmEscalationLevel::where('id', $index);
            if($existing_escaltion_level->exists()){
                $existing_escaltion_level = $existing_escaltion_level->first();
                $existing_escaltion_level->name = $level;
                $existing_escaltion_level->save();
            }
            else{
                $escalation_level = new CrmEscalationLevel();
                $escalation_level->name = $level;
                $escalation_level->save();
            }
        }

        return redirect()->back()->with('success', 'Escalation Level(s) updated Successfully');
    }


    public function escalation_tagging_index(){
        return view('admin.settings.CRM.escalation.tagging.index');
    }

    public function escalation_tagging_list(){
        $tagging = CrmEscalationTagging::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_escalation_taggings.case_nature')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_escalation_taggings.case_nature_type')
            ->leftjoin('admins as a', 'a.id', '=', 'crm_escalation_taggings.updated_by')
            ->select('crm_escalation_taggings.id as id', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crm_escalation_taggings.updated_at as updated_at', 'a.name as updated_by', 'crm_escalation_taggings.status as status');


        $datatables = Datatables::of($tagging)
            ->editColumn('view_hubs', function ($escalation) {
                return '<button class="btn btn-sm btn-outline-info align-middle">View</button>';
            })
            ->addColumn('view_statuses', function ($escalation) {
                return '<button class="btn btn-sm btn-outline-info align-middle">View</button>';
            })
            ->editColumn('view_levels', function ($escalation) {
                return '<button class="btn btn-sm btn-outline-info align-middle">View</button>';
            })
            ->editColumn('status', function ($escalation) {
                if($escalation->status == 1){
                    return 'Enabled';
                }
                else{
                    return 'Disabled';
                }
            })
            ->addColumn('action', function($escalation) {
                if (session('role_id') == 1 || count(array_intersect([83, 84], session('permissions'))) !== 0) {
                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    $enable_button = '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    $disable_button = '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';

                    $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">
                ';

                    $dropdown .= $edit_button;

                    if ($escalation->status == 1) {
                        $dropdown .= $disable_button;
                    }
                    else {
                        $dropdown .= $enable_button;
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
        return $datatables->make(true);
    }

    public function escalation_tagging_add_index(){
        $shipment_statuses = ShipmentStatus::select('id','name')->where('id', '!=', 1)->get();
        $hubs = City::where('hub',1)->select('id','name')->get();
        $admin_roles = AdminRole::leftjoin('admin_departments as ad', 'ad.id', '=', 'admin_roles.department_id')
            ->select('admin_roles.id as id', 'admin_roles.name as name', 'ad.name as department')
            ->where('admin_roles.id', '!=', 1)
            ->get();

        $levels = CrmEscalationLevel::get();
        $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
        return view('admin.settings.CRM.escalation.tagging.add')->with(['shipment_statuses' => $shipment_statuses, 'case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_type_claims' => $case_nature_type_claims, 'hubs' => $hubs, 'levels' => $levels, 'admin_roles' => $admin_roles]);
    }


    public function escalation_tagging_add_store(Request $request){
        $case_nature_id = $request->input('case_nature_select');
        if($case_nature_id == 1){
            $case_nature_type_id = $request->input('case_nature_complaint');
        }
        else if($case_nature_id == 2){
            $case_nature_type_id = $request->input('case_nature_request');
        }
        else if($case_nature_id == 3){
            $case_nature_type_id = $request->input('case_nature_claim');
        }
        $shipment_statuses = $request->shipment_statuses;
        $levels = $request->level;

        $tagging_escalation = new CrmEscalationTagging();
        $tagging_escalation->case_nature = $case_nature_id;
        $tagging_escalation->case_nature_type = $case_nature_type_id;
        $tagging_escalation->hub_status = $request->hub_status;
        $tagging_escalation->status = 1;
        $tagging_escalation->updated_by = Auth::id();
        $tagging_escalation->save();

        if($request->has('hubs')){
            $hubs = $request->hubs;
            foreach ($hubs as $hub){
                $tagging_escalation_hub = new CrmEscalationTaggingHub();
                $tagging_escalation_hub->escalation_tagging_id = $tagging_escalation->id;
                $tagging_escalation_hub->hub_id = $hub;
                $tagging_escalation_hub->save();
            }
        }
        foreach ($shipment_statuses as $shipment_status){
            $tagging_escalation_shipment_status = new CrmEscalationTaggingShipmentStatus();
            $tagging_escalation_shipment_status->escalation_tagging_id = $tagging_escalation->id;
            $tagging_escalation_shipment_status->shipment_status_id = $shipment_status;
            $tagging_escalation_shipment_status->save();
        }

        foreach($levels as $index => $level){
            $addition_emails = explode(',', $request->additional_emails[$index]);
            $addition_emails_cc = explode(',', $request->additional_emails_cc[$index]);
            $addition_emails_bcc = explode(',', $request->additional_emails_bcc[$index]);
            if(isset($request->admin_role_select[$index])){
                $admin_roles = $request->admin_role_select[$index];
                $tat = $request->tat[$index];
                $level_tagging_escalation = new CrmEscalationTaggingLevel();
                $level_tagging_escalation->escalation_tagging_id = $tagging_escalation->id;
                $level_tagging_escalation->level_id = $level;
                $level_tagging_escalation->tat = $tat;
                $level_tagging_escalation->save();
                foreach ($admin_roles as $admin_role){
                    $level_tagging_escalation_role = new CrmEscalationTaggingLevelRole();
                    $level_tagging_escalation_role->escalation_tagging_id = $tagging_escalation->id;
                    $level_tagging_escalation_role->tagging_level_id = $level_tagging_escalation->id;
                    $level_tagging_escalation_role->role_id = $admin_role;
                    $level_tagging_escalation_role->save();
                }
                foreach ($addition_emails as $addition_email){
                    if($addition_email != null) {
                        $level_tagging_escalation_email = new CrmEscalationTaggingLevelEmail();
                        $level_tagging_escalation_email->escalation_tagging_id = $tagging_escalation->id;
                        $level_tagging_escalation_email->tagging_level_id = $level_tagging_escalation->id;
                        $level_tagging_escalation_email->status = 1;
                        $level_tagging_escalation_email->email = $addition_email;
                        $level_tagging_escalation_email->save();
                    }
                }
                foreach ($addition_emails_cc as $addition_email){
                    if($addition_email != null) {
                        $level_tagging_escalation_email = new CrmEscalationTaggingLevelEmail();
                        $level_tagging_escalation_email->escalation_tagging_id = $tagging_escalation->id;
                        $level_tagging_escalation_email->tagging_level_id = $level_tagging_escalation->id;
                        $level_tagging_escalation_email->status = 2;
                        $level_tagging_escalation_email->email = $addition_email;
                        $level_tagging_escalation_email->save();
                    }
                }
                foreach ($addition_emails_bcc as $addition_email){
                    if($addition_email != null) {
                        $level_tagging_escalation_email = new CrmEscalationTaggingLevelEmail();
                        $level_tagging_escalation_email->escalation_tagging_id = $tagging_escalation->id;
                        $level_tagging_escalation_email->tagging_level_id = $level_tagging_escalation->id;
                        $level_tagging_escalation_email->status = 3;
                        $level_tagging_escalation_email->email = $addition_email;
                        $level_tagging_escalation_email->save();
                    }
                }
            }
        }
        return redirect()->route('admin.settings.escalation.tagging.index')->with('success', 'Tagging Escalation Added Successfully');
    }

    public function escalation_tagging_edit_index($id){
        $escalation_tagging = CrmEscalationTagging::find($id);
        $statuses = array();
        foreach ($escalation_tagging->shipment_statuses as $shipment_status){
            $statuses[] = $shipment_status->shipment_status_id;
        }
        $shipment_statuses = ShipmentStatus::select('id','name')->where('id', '!=', 1)->get();
        $selected_hubs = array();
        foreach ($escalation_tagging->hubs as $hub){
            $selected_hubs[] = $hub->hub_id;
        }
        $hubs = City::where('hub',1)->select('id','name')->get();
        $admin_roles = AdminRole::leftjoin('admin_departments as ad', 'ad.id', '=', 'admin_roles.department_id')
            ->select('admin_roles.id as id', 'admin_roles.name as name', 'ad.name as department')
            ->where('admin_roles.id', '!=', 1)
            ->get();

        $levels = CrmEscalationLevel::get();
        $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
        $selected_admin_roles = array();
        $selected_tat = array();
        $selected_roles = array();
        $selected_emails = array();
        foreach($escalation_tagging->levels as $level){
            $selected_admin_roles[$level->level_id] = (int)$level->tagged_id;
            $selected_tat[$level->level_id] = $level->tat;
            $selected_emails[$level->level_id] = '';
            $selected_emails_cc[$level->level_id] = '';
            $selected_emails_bcc[$level->level_id] = '';
            foreach ($level->roles as $role){
                $selected_roles[$level->level_id][] = $role->role_id;
            }
            foreach ($level->emails as $email){
                if($email->status == 1){
                    $selected_emails[$level->level_id] .= $email->email . ',';
                }
                elseif ($email->status == 2){
                    $selected_emails_cc[$level->level_id] .= $email->email . ',';
                }
                elseif ($email->status == 3){
                    $selected_emails_bcc[$level->level_id] .= $email->email . ',';
                }
            }
        }
        return view('admin.settings.CRM.escalation.tagging.edit')->with(['shipment_statuses' => $shipment_statuses, 'case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_type_claims' => $case_nature_type_claims, 'hubs' => $hubs, 'levels' => $levels, 'admin_roles' => $admin_roles, 'escalation_tagging' => $escalation_tagging, 'statuses' => $statuses, 'selected_hubs' => $selected_hubs, 'selected_admin_roles' => $selected_admin_roles, 'selected_tat' => $selected_tat, 'selected_emails' => $selected_emails, 'selected_emails_cc' => $selected_emails_cc, 'selected_emails_bcc' => $selected_emails_bcc, 'selected_roles' => $selected_roles]);
    }


    public function escalation_tagging_edit_store(Request $request){
        $tagging_escalation = CrmEscalationTagging::find($request->id);
        $case_nature_id = $tagging_escalation->case_nature;
        if($case_nature_id == 1){
            $case_nature_type_id = $request->input('case_nature_complaint');
        }
        else if($case_nature_id == 2){
            $case_nature_type_id = $request->input('case_nature_request');
        }
        else if($case_nature_id == 3){
            $case_nature_type_id = $request->input('case_nature_claim');
        }
        $shipment_statuses = $request->shipment_statuses;
        $levels = $request->level;

        $tagging_escalation->case_nature_type = $case_nature_type_id;
        $tagging_escalation->hub_status = $request->hub_status;
        $tagging_escalation->status = 1;
        $tagging_escalation->updated_by = Auth::id();
        $tagging_escalation->save();

        CrmEscalationTaggingHub::where('escalation_tagging_id', $tagging_escalation->id)->delete();
        if($request->has('hubs')){
            $hubs = $request->hubs;
            foreach ($hubs as $hub){
                $tagging_escalation_hub = new CrmEscalationTaggingHub();
                $tagging_escalation_hub->escalation_tagging_id = $tagging_escalation->id;
                $tagging_escalation_hub->hub_id = $hub;
                $tagging_escalation_hub->save();
            }
        }
        CrmEscalationTaggingShipmentStatus::where('escalation_tagging_id', $tagging_escalation->id)->delete();
        foreach ($shipment_statuses as $shipment_status){
            $tagging_escalation_shipment_status = new CrmEscalationTaggingShipmentStatus();
            $tagging_escalation_shipment_status->escalation_tagging_id = $tagging_escalation->id;
            $tagging_escalation_shipment_status->shipment_status_id = $shipment_status;
            $tagging_escalation_shipment_status->save();
        }

        CrmEscalationTaggingLevel::where('escalation_tagging_id', $tagging_escalation->id)->delete();
        CrmEscalationTaggingLevelEmail::where('escalation_tagging_id', $tagging_escalation->id)->delete();
        CrmEscalationTaggingLevelRole::where('escalation_tagging_id', $tagging_escalation->id)->delete();
        foreach($levels as $index => $level){
            $addition_emails = explode(',', $request->additional_emails[$index]);
            $addition_emails_cc = explode(',', $request->additional_emails_cc[$index]);
            $addition_emails_bcc = explode(',', $request->additional_emails_bcc[$index]);
            if(isset($request->admin_role_select[$index])){
                $admin_roles = $request->admin_role_select[$index];
                $tat = $request->tat[$index];
                $level_tagging_escalation = new CrmEscalationTaggingLevel();
                $level_tagging_escalation->escalation_tagging_id = $tagging_escalation->id;
                $level_tagging_escalation->level_id = $level;
                $level_tagging_escalation->tat = $tat;
                $level_tagging_escalation->save();
                foreach ($admin_roles as $admin_role){
                    $level_tagging_escalation_role = new CrmEscalationTaggingLevelRole();
                    $level_tagging_escalation_role->escalation_tagging_id = $tagging_escalation->id;
                    $level_tagging_escalation_role->tagging_level_id = $level_tagging_escalation->id;
                    $level_tagging_escalation_role->role_id = $admin_role;
                    $level_tagging_escalation_role->save();
                }
                foreach ($addition_emails as $addition_email){
                    if($addition_email != null) {
                        $level_tagging_escalation_email = new CrmEscalationTaggingLevelEmail();
                        $level_tagging_escalation_email->escalation_tagging_id = $tagging_escalation->id;
                        $level_tagging_escalation_email->tagging_level_id = $level_tagging_escalation->id;
                        $level_tagging_escalation_email->status = 1;
                        $level_tagging_escalation_email->email = $addition_email;
                        $level_tagging_escalation_email->save();
                    }
                }
                foreach ($addition_emails_cc as $addition_email){
                    if($addition_email != null) {
                        $level_tagging_escalation_email = new CrmEscalationTaggingLevelEmail();
                        $level_tagging_escalation_email->escalation_tagging_id = $tagging_escalation->id;
                        $level_tagging_escalation_email->tagging_level_id = $level_tagging_escalation->id;
                        $level_tagging_escalation_email->status = 2;
                        $level_tagging_escalation_email->email = $addition_email;
                        $level_tagging_escalation_email->save();
                    }
                }
                foreach ($addition_emails_bcc as $addition_email){
                    if($addition_email != null) {
                        $level_tagging_escalation_email = new CrmEscalationTaggingLevelEmail();
                        $level_tagging_escalation_email->escalation_tagging_id = $tagging_escalation->id;
                        $level_tagging_escalation_email->tagging_level_id = $level_tagging_escalation->id;
                        $level_tagging_escalation_email->status = 3;
                        $level_tagging_escalation_email->email = $addition_email;
                        $level_tagging_escalation_email->save();
                    }
                }
            }
        }
        return redirect()->route('admin.settings.escalation.tagging.index')->with('success', 'Tagging Escalation updated Successfully');
    }

    public function escalation_tagging_status_update(Request $request){
        $escalation = CrmEscalationTagging::find($request->id);
        $escalation->status = $request->status;
        $escalation->save();
        if($request->status == 1){
            $status = 'enabled';
        }
        else{
            $status = 'disabled';
        }
        return response()->json(['status' => 1, 'success' => 'Escalation Tagging ' . $status . ' successfully!']);
    }

    public function escalation_tagging_view_statuses(Request $request){
        $escalation_tagging = CrmEscalationTagging::find($request->id);
        $statuses = array();
        foreach ($escalation_tagging->shipment_statuses as $shipment_status){
            $statuses[] = $shipment_status->status->name;
        }
        return response()->json(['status' => 1, 'statuses' => $statuses]);
    }

    public function escalation_tagging_view_hubs(Request $request){
        $escalation_tagging = CrmEscalationTagging::find($request->id);
        $hubs = array();
        foreach ($escalation_tagging->hubs as $hub){
            $hubs[] = $hub->hub->name;
        }
        return response()->json(['status' => 1, 'hubs' => $hubs]);
    }

    public function escalation_tagging_view_levels(Request $request){
        $escalation_tagging = CrmEscalationTagging::find($request->id);
        $selected_levels = array();
        foreach($escalation_tagging->levels as $level){
            $selected_levels['name'][] = $level->level->name;

            $role_text = '';
            $role_count = count($level->roles);
            foreach ($level->roles as $index => $role){
                if($role_count > ($index + 1)){
                    $role_text .= $role->role->name . ' | ' . $role->role->department->name. ', ';
                }
                else{
                    $role_text .= $role->role->name . ' | ' . $role->role->department->name;
                }
            }
            $selected_levels['admin_roles'][] =  $role_text;
            $selected_levels['tat'][] = $level->tat;
            $email_text = '';
            $email_text_cc = '';
            $email_text_bcc = '';
            $email_count = 0;
            $email_count_cc = 0;
            $email_count_bcc = 0;
            foreach ($level->emails as $index => $email){
                if($email->status == 1){
                    if($email_count == 0){
                        $email_text .= $email->email;
                    }
                    else{
                        $email_text .= ', ' . $email->email;
                    }
                    $email_count++;
                }
                elseif ($email->status == 2){
                    if($email_count_cc == 0){
                        $email_text_cc .= $email->email;
                    }
                    else{
                        $email_text_cc .= ', ' . $email->email;
                    }
                    $email_count_cc++;
                }
                elseif($email->status == 3){
                    if($email_count_bcc == 0){
                        $email_text_bcc .= $email->email;
                    }
                    else{
                        $email_text_bcc .= ', ' . $email->email;
                    }
                    $email_count_bcc++;
                }
            }

            $selected_levels['emails'][] = $email_text;
            $selected_levels['emails_cc'][] = $email_text_cc;
            $selected_levels['emails_bcc'][] = $email_text_bcc;
        }
        return response()->json(['status' => 1, 'selected_levels' => $selected_levels]);
    }

    public function csat_cases_setting_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 698);

        $csat_types = GlobalSettings::where('type', 'csat_type')->latest()->first();
        $case_nature_types = CrmRequestCaseNatureType::all();

        if(isset($csat_types)){
            $csat_types = explode(',', $csat_types->text);
            return view('admin.settings.CRM.csat_cases_type_setting')->with(['csat_types'=>$csat_types,'case_nature_types'=>$case_nature_types]);
        }else{
            $csat_types = [];
            return view('admin.settings.CRM.csat_cases_type_setting')->with(['csat_types'=>$csat_types,'case_nature_types'=>$case_nature_types]);
        }

    }

    public function csat_cases_setting_store(Request $request)
    {
        $csat_types = GlobalSettings::where('type', 'csat_type')->latest()->first();
        $case_types = $request->get('case_types');
        $case_types = implode(',', $case_types);

        if(!isset($csat_types))
        {
            GlobalSettings::create(['setting_value'=> 1 , 'type' => 'csat_type', 'text'=> $case_types]);
            return redirect()->route('admin.settings.csat_cases_setting.index')->with('success', 'Updated Successfully');
        }
        else
        {
            GlobalSettings::where('type','=','csat_type')->update(['text' => $case_types]);
            return redirect()->route('admin.settings.csat_cases_setting.index')->with('success', 'Updated Successfully');

        }
    }


    public function csat_score_formula_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 698);   

        $formulas = GlobalSettings::where('type', 'csat_formula')->latest()->first();
        $formulas = explode(',', $formulas->text ?? '');

        return view('admin.settings.CRM.csat_formula_setting')->with(['formulas'=>$formulas]);

    }

    public function csat_score_formula_store(Request $request)
    {
        $csat_formula = GlobalSettings::where('type', 'csat_formula')->latest()->first();
        $values = $request->get('submitted_values');   
        
        if(!isset($csat_formula))
        {
            GlobalSettings::create(['setting_value'=> 1 , 'type' => 'csat_formula', 'text'=> $values]);
            return redirect()->route('admin.settings.csat_cases_setting.formula.index')->with('success', 'Updated Successfully');
        }
        else
        {
            GlobalSettings::where('type','=','csat_formula')->update(['text' => $values]);
            return redirect()->route('admin.settings.csat_cases_setting.formula.index')->with('success', 'Updated Successfully');

        }

    }
}
