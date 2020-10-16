<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        return view('admin.sales.key_accounts.dashboard')->with(['case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_channels' => $case_nature_channels, 'case_nature_type_claims' => $case_nature_type_claims, 'case_nature_types' => $case_nature_types]);
    }

    public function key_accounts_dashboard_details(Request $request){

    }
}
