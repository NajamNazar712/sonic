<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\BusinessProjectionAccount;
use App\Http\Models\Admin\BusinessProjectionHub;
use App\Http\Models\Admin\BusinessProjectionReason;
use App\Http\Models\Admin\BusinessProjectionReasonsLog;
use App\Http\Models\Admin\BusinessProjectionShipment;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Shipper\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Auth;

class BusinessProjectionRetentionController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    static public function create_business_projections(){
        $date = Carbon::yesterday();
        $shippers = array();
        $date_from = Carbon::yesterday()->format('Y-m-d 08:00A');
        $date_to = Carbon::today()->format('Y-m-d 07:59A');

        $business_account = BusinessProjectionAccount::whereDate('date', $date);
        if(!$business_account->exists()){

            $shippers = User::where('status', '>', '2')->select('id', 'name', 'city_id','average_shipments')->get();
            if(count($shippers) > 0){
                foreach ($shippers as $shipper) {

                    $average_shipment = 0;
                    $projected_shipment = 0;
                    $last_day_numbers = 0;
                    $achieved = 0;

                    $sale_person = SalePersonTag::where('user_id', $shipper->id)->where('status', 0);
                    if($sale_person->exists()){
                        $sale_person = $sale_person->first();
                        $sale_person_id = $sale_person->admin_id;
                    }else{
                        continue;
                    }
                    $projected_shipments = BusinessProjectionShipment::where('user_id', $shipper->id);
                    if($projected_shipments->exists()){
                        $projected_shipments = $projected_shipments->first();
                        $projected_shipment = $projected_shipments->shipment;
                    }
                    $last_day_numbers = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                        ->whereExists(function ($query) use ($date_from, $date_to) {
                            $query->from('shipments_journey')
                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$date_from, $date_to])
                                ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', '=', 0)->count();
                    if($shipper->average_shipments){
                        $average_shipment = $shipper->average_shipments;
                    }
                    if($projected_shipment != 0){
                        $achieved = ($last_day_numbers / $projected_shipment) * 100;
                    }

                    $business_projection_account = new BusinessProjectionAccount();
                    $business_projection_account->user_id = $shipper->id;
                    $business_projection_account->city_id = $shipper->city_id;
                    $business_projection_account->sale_person_id = $sale_person_id;
                    $business_projection_account->average_shipment = $average_shipment;
                    $business_projection_account->projected_shipment = $projected_shipment;
                    $business_projection_account->last_day_number = $last_day_numbers;
                    $business_projection_account->achieved = $achieved;
                    $business_projection_account->date = $date;
                    $business_projection_account->save();

                }
            }
        }

        $business_hubs = BusinessProjectionHub::whereDate('date', $date);
        if(!$business_hubs->exists()){
            $projected_accounts_data = BusinessProjectionAccount::where('date', $date)->select('city_id',DB::raw('SUM(average_shipment) as average_shipments'), DB::raw('SUM(projected_shipment) as projected_shipments'), DB::raw('SUM(last_day_number) as last_day_numbers'));
            if($projected_accounts_data->exists()){
                $projected_accounts_data = $projected_accounts_data->groupBy('city_id')->get();
                if(count($projected_accounts_data) > 0){
                    foreach ($projected_accounts_data as $hub_data){
                        $projected_hubs = new BusinessProjectionHub();
                        $projected_hubs->hub_id = $hub_data->city_id;
                        $projected_hubs->average_shipment = $hub_data->average_shipments;
                        $projected_hubs->projected_shipment = $hub_data->projected_shipments;
                        $projected_hubs->last_day_number = $hub_data->last_day_numbers;
                        if($hub_data->projected_shipments > 0){
                            $total_achieved = ($hub_data->last_day_numbers / $hub_data->projected_shipments) * 100;
                            $projected_hubs->achieved = round($total_achieved);
                        }else{
                            $projected_hubs->achieved = 0;
                        }
                        $projected_hubs->date = $date;
                        $projected_hubs->save();
                    }
                }
            }

        }

    }

    public function dashboard(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),285);
        $data = array();
        $date = Carbon::yesterday()->toDateString();
        $business_accounts = BusinessProjectionAccount::where('date', $date)->select(DB::raw('SUM(average_shipment) as average_shipments'), DB::raw('SUM(projected_shipment) as projected_shipments'), DB::raw('SUM(last_day_number) as last_day_numbers'), DB::raw('AVG(achieved) as achieved'));
        if(session('department_id') == 7){
            if(!in_array(session('id'), session('sale_users_bypass'))){
                $business_accounts = $business_accounts->whereIn('user_id', session('tagged_shippers'));
            }
        }
        $business_accounts = $business_accounts->first();

        $hubs_data = BusinessProjectionHub::where('date', $date)->get();

        $reasons_data = BusinessProjectionAccount::where('date', $date)->select(DB::raw("business_projection_reason_id as reason_id, count(business_projection_reason_id) as count"))->groupBy('business_projection_reason_id');
        if(session('department_id') == 7){
            if(!in_array(session('id'), session('sale_users_bypass'))){
                $reasons_data = $reasons_data->whereIn('user_id', session('tagged_shippers'));
            }
        }
        $reasons_data_count = 0;
        $reasons_data = $reasons_data->get();
        if($reasons_data){
            $reasons_data_count = 1;
        }
        $reasons = BusinessProjectionReason::all();
        foreach ($reasons as $reason){

            $data[$reason->id]['name'] = $reason->name;
            $data[$reason->id]['count'] = 0;
            if($reasons_data_count > 0){
                foreach ($reasons_data as $reason_data) {
                    if($reason_data['reason_id'] == $reason->id){
                        $data[$reason->id]['count'] = $reason_data['count'];
                    }
                }
            }

        }
        return view('admin.sales.dashboard.index')->with(['business_accounts_total' => $business_accounts, 'hubs_data' => $hubs_data, 'reasons_data' => $data, 'reasons' => $reasons]);
    }
    public function dashboard_list(Request $request){

        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),286);
        }
        $business = BusinessProjectionAccount::join('users as u', 'u.id', '=', 'business_projection_accounts.user_id')
            ->join('cities', 'cities.id', '=', 'business_projection_accounts.city_id')
            ->join('admins as sp', 'sp.id', '=', 'business_projection_accounts.sale_person_id')
            ->leftjoin('business_projection_reasons as bpr', 'bpr.id', '=', 'business_projection_accounts.business_projection_reason_id')
            ->select('business_projection_accounts.id', 'u.id as account_id','u.name as shipper', 'cities.name as city', 'u.poc', 'u.phone', 'u.address', 'u.email', 'u.status', 'sp.name as sales_person', 'bpr.name as reason', 'business_projection_accounts.remarks', 'business_projection_accounts.average_shipment','business_projection_accounts.projected_shipment','business_projection_accounts.last_day_number','business_projection_accounts.achieved')
            ->whereDate('date', Carbon::yesterday());
        if(session('department_id') == 7){
            if(!in_array(session('id'), session('sale_users_bypass'))){
                $business = $business->whereIn('u.id', session('tagged_shippers'));
            }
        }
        return Datatables::of($business)
            ->editColumn('shipper_status', function ($data){
                if($data->status == 3){
                    return 'Enable';
                }else if($data->status == 4){
                    return 'Disable';
                }
            })
            ->filterColumn('shipper_status', function($query, $keyword) {
                if ($keyword == 3 || $keyword == 4) {
                    $query->where('u.status', '=', $keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('achieved', function ($data){
                return $data->achieved. '%';
            })
            ->addColumn("action", function ($data) {
                $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                ';
                $dropdown .= '<button type="button" class="dropdown-item reason"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update Reason</div></button>';

                $dropdown .= '
                    </div>
                  </div>
                ';

                return $dropdown;

            })
            ->make(true);
    }

    public function update_reason(Request $request){
        $row_id = $request->row_id;
        $reason_id = $request->reason_id;
        $remarks = $request->remarks;
        if($row_id && $reason_id){
            $account = BusinessProjectionAccount::find($row_id);
            if($account->business_projection_reason_id != NULL){
                $logs = new BusinessProjectionReasonsLog();
                $logs->user_id = $account->user_id;
                $logs->admin_id = Auth::id();
                $logs->business_projection_reason_id = $account->business_projection_reason_id;
                $logs->save();
            }
            $account->business_projection_reason_id = $reason_id;
            $account->remarks = $remarks;
            $account->save();
            return response()->json(['status' => 1, 'success' => 'Reason successfully updated!']);
        }

    }
}
