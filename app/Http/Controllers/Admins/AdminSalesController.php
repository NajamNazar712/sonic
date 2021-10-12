<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\KeyAccountDailySummary;
use App\Http\Models\Admin\KeyAccountDailySummaryCrm;
use App\Http\Models\Admin\KeyAccountPendingCrm;
use App\Http\Models\Admin\KeyAccountPendingSummaryCrm;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminSalesController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function key_accounts_dashboard_index(){
        $case_nature = CrmRequestCaseNature::get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->get();
        $case_nature_channels = CrmRequestChannel::where('id', '!=', 1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->get();

        $case_nature_types = CrmRequestCaseNatureType::whereIn('nature_id', [1, 2])->get();
        $admin_id = Auth::id();
        $date = Carbon::today()->toDateString();
        foreach ($case_nature_channels as $case_nature_channel){
            $channels[$case_nature_channel->id] = 0;
        }
        foreach ($case_nature_types as $case_nature_type){
            $types[$case_nature_type->id] = 0;
            $pending_types[$case_nature_type->id]['count'] = 0;
            $pending_types[$case_nature_type->id]['tat'] = 0;
        }
        $summary_trace_count = KeyAccountDailySummary::whereDate('created_at', $date)->where('admin_id', $admin_id)->sum('count');
        $pending_summary_trace_count = KeyAccountPendingCrm::where('admin_id', $admin_id)->count();
        $summary_crm_channel = KeyAccountDailySummaryCrm::select(DB::raw('sum(key_account_daily_summary_crms.count) as count'), 'channel_id')->whereDate('created_at', $date)->where('admin_id', $admin_id)->groupBy('channel_id');
        if($summary_crm_channel->exists()){
            $summary_crm_channel = $summary_crm_channel->get();
            foreach ($summary_crm_channel as $crm_summary_channel){
                $channels[$crm_summary_channel->channel_id] = $crm_summary_channel->count;
            }
        }
        $summary_crm = KeyAccountDailySummaryCrm::select(DB::raw('sum(key_account_daily_summary_crms.count) as count'), 'key_account_daily_summary_crms.case_nature_type_id')->whereDate('created_at', $date)->where('admin_id', $admin_id)->groupBy('case_nature_type_id');
        if($summary_crm->exists()){
            $summary_crm = $summary_crm->get();
            foreach ($summary_crm as $crm_summary){
                $types[$crm_summary->case_nature_type_id] = $crm_summary->count;
            }
        }
        $pending_summary_crm = KeyAccountPendingSummaryCrm::select(DB::raw('sum(key_account_pending_summary_crms.count) as count'), 'key_account_pending_summary_crms.case_nature_type_id', 'key_account_pending_summary_crms.tat')->whereDate('created_at', $date)->where('admin_id', $admin_id)->groupBy('case_nature_type_id');
        if($pending_summary_crm->exists()){
            $pending_summary_crm = $pending_summary_crm->get();
            foreach ($pending_summary_crm as $pending_crm_summary){
                $pending_types[$pending_crm_summary->case_nature_type_id]['count'] = $pending_crm_summary->count;
                $pending_types[$pending_crm_summary->case_nature_type_id]['tat'] = $pending_crm_summary->tat;
            }
        }
        $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id' )
            ->select('a.id as id', 'a.name as name')
            ->whereNotIn('admin_roles.department_id', [1,3])->get();

        return view('admin.sales.key_accounts.dashboard')->with(['case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_channels' => $case_nature_channels, 'case_nature_type_claims' => $case_nature_type_claims, 'case_nature_types' => $case_nature_types, 'summary_trace_count' => $summary_trace_count, 'types' => $types, 'channels' => $channels, 'pending_types' => $pending_types, 'selected_admin' => $admin_id, 'admins' => $admins, 'date' => $date, 'pending_summary_trace_count' => $pending_summary_trace_count]);
    }

    public function key_accounts_dashboard_details(Request $request){
        $case_nature = CrmRequestCaseNature::get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->get();
        $case_nature_channels = CrmRequestChannel::where('id', '!=', 1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->get();

        $case_nature_types = CrmRequestCaseNatureType::whereIn('nature_id', [1, 2])->get();
        if($request->has('search_admin')){
            $admin_id = $request->search_admin;
        }
        else{
            $admin_id = Auth::id();
        }
        $date = Carbon::parse($request->search_date)->toDateString();
        foreach ($case_nature_channels as $case_nature_channel){
            $channels[$case_nature_channel->id] = 0;
        }
        foreach ($case_nature_types as $case_nature_type){
            $types[$case_nature_type->id] = 0;
            $pending_types[$case_nature_type->id]['count'] = 0;
            $pending_types[$case_nature_type->id]['tat'] = 0;
        }
        $summary_trace_count = KeyAccountDailySummary::whereDate('created_at', $date)->where('admin_id', $admin_id)->sum('count');
        $pending_summary_trace_count = KeyAccountPendingCrm::where('admin_id', $admin_id)->count();
        $summary_crm_channel = KeyAccountDailySummaryCrm::select(DB::raw('sum(key_account_daily_summary_crms.count) as count'), 'channel_id')->whereDate('created_at', $date)->where('admin_id', $admin_id)->groupBy('channel_id');
        if($summary_crm_channel->exists()){
            $summary_crm_channel = $summary_crm_channel->get();
            foreach ($summary_crm_channel as $crm_summary_channel){
                $channels[$crm_summary_channel->channel_id] = $crm_summary_channel->count;
            }
        }
        $summary_crm = KeyAccountDailySummaryCrm::select(DB::raw('sum(key_account_daily_summary_crms.count) as count'), 'key_account_daily_summary_crms.case_nature_type_id')->whereDate('created_at', $date)->where('admin_id', $admin_id)->groupBy('case_nature_type_id');
        if($summary_crm->exists()){
            $summary_crm = $summary_crm->get();
            foreach ($summary_crm as $crm_summary){
                $types[$crm_summary->case_nature_type_id] = $crm_summary->count;
            }
        }
        $pending_summary_crm = KeyAccountPendingSummaryCrm::select(DB::raw('sum(key_account_pending_summary_crms.count) as count'), 'key_account_pending_summary_crms.case_nature_type_id', 'key_account_pending_summary_crms.tat')->whereDate('created_at', $date)->where('admin_id', $admin_id)->groupBy('case_nature_type_id');
        if($pending_summary_crm->exists()){
            $pending_summary_crm = $pending_summary_crm->get();
            foreach ($pending_summary_crm as $pending_crm_summary){
                $pending_types[$pending_crm_summary->case_nature_type_id]['count'] = $pending_crm_summary->count;
                $pending_types[$pending_crm_summary->case_nature_type_id]['tat'] = $pending_crm_summary->tat;
            }
        }
        $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id' )
            ->select('a.id as id', 'a.name as name')
            ->whereNotIn('admin_roles.department_id', [1,3])->get();

        return view('admin.sales.key_accounts.dashboard')->with(['case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_channels' => $case_nature_channels, 'case_nature_type_claims' => $case_nature_type_claims, 'case_nature_types' => $case_nature_types, 'summary_trace_count' => $summary_trace_count, 'types' => $types, 'channels' => $channels, 'pending_types' => $pending_types, 'selected_admin' => $admin_id, 'admins' => $admins, 'date' => $date, 'pending_summary_trace_count' => $pending_summary_trace_count]);
    }
}
