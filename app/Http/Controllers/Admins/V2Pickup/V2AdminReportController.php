<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\PickupRequest;
use App\Http\Models\Rider;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestAttempt;
use App\Http\Models\V2Pickup\V2PickupRequestLegend;
use App\Http\Models\V2Pickup\V2PickupRequestNotPickReason;
use App\Http\Models\V2Pickup\V2PickupRequestRiderStatus;
use App\Http\Models\V2Pickup\V2PickupRequestStatus;
use App\Http\Models\V2Pickup\V2PickupReport;
use App\Http\Models\V2Pickup\V2PickupReportCategory;
use App\Http\Models\V2Pickup\V2PickupReportSummary;
use App\Http\Models\V2Pickup\V2PickupReportLegend;
use App\Http\Models\Admin\SalePersonTag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Auth;

class V2AdminReportController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public static function insertReportData(){
        $yesterday = Carbon::yesterday();
        $today = Carbon::now()->startOfDay();

        $total_pickups = 0;
        $total_operations = 0;
        $total_sales = 0;
        $total_cut_off_time_before = 0;
        $total_cut_off_time_after = 0;
        $attempted_and_picked = 0;
        $attempted_and_not_picked = 0;
        $attempted_failed = 0;

        $settings = GlobalSettings::where('type', 'pickup_arrival_cut_off_time');
        $arrival_cut_off_time = '8';
        if ($settings->exists()) {
            $settings = $settings->first();
            $arrival_cut_off_time = $settings->setting_value;
        }
//        $arrival_time = Carbon::parse($arrival_cut_off_time)->toTimeString();
        $report = V2PickupReport::whereDate('date', $today);
        if($report->exists()){
            $report->delete();
            V2PickupReportSummary::whereDate('date', $today)->delete();
        }
        $yesterday->setTime($arrival_cut_off_time,0,1);
        $today->setTime($arrival_cut_off_time,0,0);

        $pickup_request_attempts = V2PickupRequestAttempt::whereBetween('attempt_date', [$yesterday,$today]);

//        $pickup_requests = V2PickupRequest::leftjoin('v2_pickup_request_attempts as ra', 'ra.pickup_request_id','=','v2_pickup_requests.id')->whereDate('v2_pickup_requests.created_at', '<=', Carbon::today())->whereDate('ra.attempt_date', '>=', $yesterday)->groupBy('ra.pickup_request_id');
        
        if($pickup_request_attempts->exists()){
            $pickup_request_attempts = $pickup_request_attempts->groupBy('pickup_request_id')->pluck('pickup_request_id')->toArray();
           if(!empty($pickup_request_attempts)){
               foreach ($pickup_request_attempts as $pickup_request_id) {
                   $pickup_request = V2PickupRequest::find($pickup_request_id);
                   $department_id = NULL;
                   $category_id = NULL;
                   $legend_id = NULL;
                   $booked = 0;
                   $received = 0;
                   $attempts = 0;

                   $total_pickups++;
                   $shipper_id = $pickup_request->shipper_id;
                   $sales_person = SalePersonTag::where('user_id', $shipper_id)->where('status', 0);
                   if($sales_person->exists()){
                       $sales_person = $sales_person->first();
                       $admin_id = $sales_person->admin_id;
                   }
                   $reason_id = null;

                   $pickup_request_id = $pickup_request->id;
                   $status_id = $pickup_request->status_id;
                   $booked = $pickup_request->booked;
                   if($pickup_request->received !== null){
                       $received = $pickup_request->received;
                   }
                   if($booked > 0){
                       $difference = ($booked - $received) / $booked;

                       if ($difference < 0) {
                        $difference = 0;
                       }
                       
                       $difference_shipments = $difference * 100;

                   }else{
                       $difference = 0;
                       $difference_shipments = 0;
                   }
                   $attempts = $pickup_request->attempts;
                   if(($status_id == 2) && ($attempts >= 1) && ($difference_shipments <= 10)){
                       $legend_id = 1;
                       $category_id = 1;
                       $attempted_and_picked++;
                   }
                   if(($status_id == 2) && ($attempts >= 1) && ($difference_shipments > 10)){
                       $legend_id = 2;
                       $department_id = 7;
                       $category_id = 1;
                       $total_sales++;
                       $attempted_and_picked++;
                   }
                   if(($status_id == 3) && ($attempts >= 1)){
                       $pickup_attempts = $pickup_request->pickup_attempts;
                       $pickup_attempt_flag = FALSE;
                       $pickup_attempt_operation_status = array(7,8,9);

                       if(count($pickup_attempts) > 0){
                           foreach ($pickup_attempts as $pickup_attempt){
                               if(in_array($pickup_attempt->reason_id, $pickup_attempt_operation_status)){
                                   $pickup_attempt_flag = TRUE;
                               }
                           }
                       }

                       if ($pickup_attempt_flag) {
                           $department_id = 6;
                           $legend_id = 5;
                           $total_operations++;
                       }
                       else {
                           $department_id = 7;
                           $legend_id = 3;
                           $total_sales++;
                       }

                       $category_id = 2;
                       $attempted_and_not_picked++;
                   }
                   if($status_id == 4){
                       $legend_id = 4;
                       $department_id = 7;
                       $category_id = 3;
                       $total_sales++;
                       $attempted_failed++;
                   }

                   if($pickup_request->after_cut_off_time == null){
                       $total_cut_off_time_before++;
                   }
                   else{
                       $total_cut_off_time_after++;
                   }


                   if($legend_id == null){
                       $legend_id = 6;
                   }
                   $pickup_report = new V2PickupReport();
                   $pickup_report->date = $today;
                   $pickup_report->pickup_request_id = $pickup_request_id;
                   $pickup_report->category_id = $category_id;
                   $pickup_report->status_id = $status_id;
                   $pickup_report->sale_person_id = $admin_id;
                   $pickup_report->expected_shipments = $booked;
                   $pickup_report->received_shipments = $received;
                   $pickup_report->difference_shipments = $difference_shipments;
                   $pickup_report->department_id = $department_id;
                   $pickup_report->legend_id = $legend_id;
                   $pickup_report->save();

               }
               $pickup_report_summary = new V2PickupReportSummary();
               $pickup_report_summary->date = $today;
               $pickup_report_summary->total = $total_pickups;
               $pickup_report_summary->pending_operations = $total_operations;
               $pickup_report_summary->pending_sales = $total_sales;
               $pickup_report_summary->before_cut_off_time = $total_cut_off_time_before;
               $pickup_report_summary->after_cut_off_time = $total_cut_off_time_after;
               $pickup_report_summary->attempted_and_picked = $attempted_and_picked;
               $pickup_report_summary->attempted_and_not_picked = $attempted_and_not_picked;
               $pickup_report_summary->attempted_failed = $attempted_failed;
               $pickup_report_summary->save();
           }


        }
        
    }    

    public function pickup_report_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),207);
        $stats = array();
        $today = Carbon::today();  
        

        $report_summary_data=V2PickupReportSummary::whereDate('created_at',$today);
        if($report_summary_data->exists()){
            $report_summary_data = $report_summary_data->first();
            $stats['total']= $report_summary_data->total;
            $stats['pending_operations']= $report_summary_data->pending_operations;
            $stats['pending_sales']= $report_summary_data->pending_sales;
            $stats['before_cut_off_time'] = $report_summary_data->before_cut_off_time;
            $stats['after_cut_off_time'] = $report_summary_data->after_cut_off_time;
            $stats['attempted_and_picked'] = $report_summary_data->attempted_and_picked;
            $stats['attempted_and_not_picked'] = $report_summary_data->attempted_and_not_picked;
            $stats['attempted_failed'] = $report_summary_data->attempted_failed;
        }else{
            $stats['total']= 0;
            $stats['pending_operations']= 0;
            $stats['pending_sales']= 0;
            $stats['before_cut_off_time'] = 0;
            $stats['after_cut_off_time'] = 0;
            $stats['attempted_and_picked'] = 0;
            $stats['attempted_and_not_picked'] = 0;
            $stats['attempted_failed'] = 0;
        }


        $legends = V2PickupReportLegend::all();
        $department = DB::connection('reports')->table('admin_departments')->whereIn('id',[6,7])->get();
        $salesperson = DB::connection('reports')->table('admins')->leftjoin('admin_roles as ar', 'ar.id', '=', 'admins.role_id')->leftjoin('admin_departments as ad', 'ad.id', '=', 'ar.department_id')->select('admins.id','admins.name')->where('ar.department_id',7)->get();
        $origin = DB::connection('reports')->table('cities')->where('pickup',1)->where('status',1)->get();
        $category = DB::connection('reports')->table('v2_pickup_report_categories')->select('id', 'name')->get();
        $pickup_request_status = DB::connection('reports')->table('v2_pickup_request_statuses')->select('id', 'name')->get();

        return view('admin.reports.pickup_report')->with(['stats'=>$stats,'legends'=>$legends,'departments'=>$department,'salespersons'=>$salesperson,'origins'=>$origin,'categories'=>$category, 'pickup_request_statuses' => $pickup_request_status]);
    }

    public function pickup_report_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),208);
        }
        $today = Carbon::today();
        $pickup_report = V2PickupReport::join('v2_pickup_requests as v', 'v2_pickup_reports.pickup_request_id', '=', 'v.id')
             ->leftjoin('v2_pickup_request_statuses as vprs','vprs.id','=','v2_pickup_reports.status_id')
             ->leftjoin('users as u','u.id','=','v.shipper_id')
             ->leftjoin('admins as a','a.id','=','v2_pickup_reports.sale_person_id')
            ->leftjoin('admin_departments as ad','ad.id','=','v2_pickup_reports.department_id')
            ->join('user_shipping_infos as usi', 'v.pickup_address_id', '=', 'usi.id')
            ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
            ->select('v.id as pickup_request_id', 'v.created_at as requested_date','vprs.name as status','u.name as shipper',
            'a.name as salesperson','v2_pickup_reports.expected_shipments as expected_shipments',
            'v2_pickup_reports.received_shipments as received_shipments','v2_pickup_reports.difference_shipments as difference_shipments',
            'ad.name as department','v.attempts as attempted_count','usi.poc AS contact_person', 'usi.vendor as vendor',
            'usi.phone AS contact_number','usi.pickup_address AS address', 'ci.name AS city','v2_pickup_reports.category_id as category_id','v2_pickup_reports.legend_id as legend_id');
        if ($request->get('search_date_from') == null) {
            $pickup_report = $pickup_report->whereDate('v2_pickup_reports.date',$today);
            }
        $datatables = Datatables::of($pickup_report)
        ->setRowAttr([
            'class' => function ($pickup_report) {
                if ($pickup_report->legend_id == 1 ) {
                    return 'attempted_and_picked_less_than_ten';
                }
                else if($pickup_report->legend_id == 2 ){
                    return 'attempted_and_picked_greater_than_ten';
                }
                else if($pickup_report->legend_id == 3 ){
                    return 'attempt_and_notpicked';
                }
                else if($pickup_report->legend_id == 5 ){
                    return 'attempt_failed';
                }
                else if($pickup_report->legend_id == 4 ){
                    return 'cancelled';
                }
            }
        ])

        ->addColumn('trax_reason', function ($pickup_report){
            $reasons = '';
            $attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_report->pickup_request_id)->whereNotNull('reason_id');
            if($attempts->exists()){
                $reason_ids = $attempts->pluck('reason_id')->toArray();
                if(count($reason_ids) > 0){
                    foreach ($reason_ids as $reason_id) {
                        $reasons .= V2PickupRequestNotPickReason::find($reason_id)->name . ',' . PHP_EOL;
                    }
                }
            }
            return $reasons;
        })
        ->addColumn('trax_remarks', function ($pickup_report){
            $trax_remarks = '';
            $attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_report->pickup_request_id)->whereNotNull('trax_remarks');
            if($attempts->exists()){
                $trax_remarks_rows = $attempts->pluck('trax_remarks')->toArray();
                if(count($trax_remarks_rows) > 0){
                    foreach ($trax_remarks_rows as $remark) {
                        $trax_remarks .= $remark . ',' . PHP_EOL;
                    }
                }
            }
            return $trax_remarks;
        })
        ->addColumn('attempted_date', function ($pickup_report){
            $attempted_date = '';
            $attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_report->pickup_request_id);
            if($attempts->exists()){
                $attempted_date_rows = $attempts->pluck('attempt_date')->toArray();
                if(count($attempted_date_rows) > 0){
                    foreach ($attempted_date_rows as $attempt_date) {
                        $attempted_date .= $attempt_date . ',' . PHP_EOL;
                    }
                }
            }
            return $attempted_date;
        });

        if($department = $request->get('search_department')){
            $datatables->where('v2_pickup_reports.department_id', '=', $department);
        }

        if($salesperson = $request->get('search_salesperson')){
            $datatables->where('a.name', '=', $salesperson);
        }

        if($origin = $request->get('search_origin')){
            $datatables->where('ci.id', '=', $origin);
        }

        if($category = $request->get('search_category')){
            $datatables->where('v2_pickup_reports.category_id', '=', $category);
        }

        if($pickup_status = $request->get('search_pickup_status')){
            $datatables->where('v2_pickup_reports.status_id', '=', $pickup_status);
        }

        if($cut_off_time = $request->get('search_cut_off_time')){
            if($cut_off_time == 0){
                $datatables->whereNull('v.after_cut_off_time');
            }else if($cut_off_time == 1){
                $datatables->where('v.after_cut_off_time', '=', $cut_off_time);
            }
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatables->whereBetween('v2_pickup_reports.date', [$from,$to]);
        }

        return $datatables->make(true);
    }
    
    public function pickup_report_data(Request $request){
        $stats = array();
        $date = Carbon::now();
        if($request->get('search_date_from')){
            $first_day = $request->search_date_from;
        }
        else{
            $first_day= Carbon::today();
                
        }
        if($request->get('search_date_to')){
            $last_day = $request->search_date_to;
        }
        else{
            $last_day= Carbon::today();
        }

        $report_summary_data=V2PickupReportSummary::select([ DB::raw('SUM(total) as total'),
        DB::raw('SUM(pending_operations) as pending_operations'),DB::raw('SUM(pending_sales) as pending_sales'),
        DB::raw('SUM(before_cut_off_time) as before_cut_off_time'),DB::raw('SUM(after_cut_off_time) as after_cut_off_time'),
        DB::raw('SUM(attempted_and_picked) as attempted_and_picked'),DB::raw('SUM(attempted_and_not_picked) as attempted_and_not_picked'),
        DB::raw('SUM(attempted_failed) as attempted_failed')
        ])
        ->whereBetween('created_at',[$first_day, $last_day])->first();
        $stats['total']= $report_summary_data->total;
        $stats['pending_operations']= $report_summary_data->pending_operations;
        $stats['pending_sales']= $report_summary_data->pending_sales;
        $stats['before_cut_off_time'] = $report_summary_data->before_cut_off_time;
        $stats['after_cut_off_time'] = $report_summary_data->after_cut_off_time;
        $stats['attempted_and_picked'] = $report_summary_data->attempted_and_picked;
        $stats['attempted_and_not_picked'] = $report_summary_data->attempted_and_not_picked;
        $stats['attempted_failed'] = $report_summary_data->attempted_failed;

        return response()->json(['status' => 1, 'stats' => $stats]);


    }
    
    
   
}
