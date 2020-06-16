<?php

namespace App\Http\Controllers\Admins\V2Pickup;

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
        $pickup_reports = new V2PickupReport();
        $pickup_summaries = new V2PickupReportSummary();
        $total = $attempted_and_picked = $attempted_and_not_picked = $attempted_failed = $operations_total ='';
        $sales_total = $before_cut_off_total = $after_cut_off_total = $department_id='';
        $legend_id = '';
        $status_id = $category_id = '';
        $pickup_requests = V2PickupRequest::whereDate('created_at', $yesterday);
        
        if($pickup_requests->exists()){
           
           $pickup_requests = $pickup_requests->get();
     

            foreach($pickup_requests as $pickup_request){
                $shipper_id = $pickup_request->shipper_id;
                $sales_person = SalePersonTag::where('user_id', $shipper_id)->where('status', 0);
                if($sales_person->exists()){
                    $sales_person = $sales_person->first();
                    $admin_id = $sales_person->admin_id;
                }
                $reason = $pickup_request->pickup_attempt_latest->whereNotNull('reason_id')->first();
                $reason_id = $reason->reason_id;
              
                    // if($reason_id == 7 || $reason_id == 8 || $reason_id == 9 ){
                    //     $department_id=6;
                    //     $operations_total++;
                    // }
                    // if($reason_id == 1 || $reason_id == 2 || $reason_id == 3 || $reason_id == 4 || $reason_id == 5 || $reason_id == 6 ){
                    //     $department_id=7;
                    //     $sales_total++;
                    // }
              
                $total++;
                $id = $pickup_request->id;
           
                 $status_id = $pickup_request->status_id;
                $booked=$pickup_request->booked;
                $received=$pickup_request->received;
                $difference = ($booked - $received)/$booked;
                $difference_shipments= 100 - $difference;
                
                $attempted=$pickup_request->attempts;
                if($status_id == 2 && $attempted > 0){
                    // if(){
                    $category_id = 1;
                    $attempted_and_picked++;
                // }
                }
                if($status_id == 3 && $attempted > 0 ){
                    $category_id = 2;
                    $attempted_and_not_picked++;
                }
                if($status_id == 4 && $attempted > 0 ){
                    $category_id = 3;
                    $attempted_failed++;
                }

                if(($status_id == 2) && ($attempted > 0) && ($difference_shipments <= 10)){
                    $legend_id=1;
                }
                if(($status_id == 2) && ($attempted > 0) && ($difference_shipments > 10)){
                    $legend_id=2;
                    $department_id=7;
                    $sales_total++;
                }
                if(($status_id == 3) && ($attempted > 0)){
                    $legend_id=3;
                    $department_id=7;
                    $sales_total++;
                }
                if($status_id == 4){
                    $legend_id=4;
                    $department_id=7;
                    $sales_total++;
                }
                if($reason_id == 7 || $reason_id == 8 || $reason_id == 9 ){
                    $legend_id=5;
                    $department_id=6;
                    $sales_total++;
                }
                
                // if($difference_shipments <= 10 && $category_id == 1){
                //     $legend_id=1;
                //     $attempted_and_picked++;
                // }
                // else if($difference_shipments > 10 && $category_id == 1){
                //     $legend_id =2;
                //     $attempted_and_picked++;
                // }
                // else if($category_id ==2 ){
                //     $legend_id = 3;
                //     $attempted_and_not_picked++;
                // }
                // else if($category_id == 4){
                //     $legend_id =4;
                // }
                // else if($category_id == 3){
                //     $legend_id =5;
                //     $attempted_failed++;
                // }
    
                if($pickup_request->after_cut_off_time == 1){
                    $before_cut_off_total++;
                } 
                else{
                    $after_cut_off_total++;
                }  
    
                $pickup_reports->date =$today;
                $pickup_reports->pickup_request_id =$id;
                $pickup_reports->category_id =$category_id;
                $pickup_reports->status_id =$status_id;
                $pickup_reports->sale_person_id =$admin_id;
                $pickup_reports->expected_shipments =$booked;
                $pickup_reports->received_shipments =$received;
                $pickup_reports->difference_shipments =$difference_shipments;

                $pickup_reports->department_id = $department_id;
                $pickup_reports->legend_id =$legend_id;
                $pickup_reports->save();
            } 
            $pickup_summaries->date =$today;
            $pickup_summaries->total =$total;
            $pickup_summaries->pending_operations =$operations_total;
            $pickup_summaries->pending_sales =$sales_total;
            $pickup_summaries->before_cut_off_time =$before_cut_off_total;
            $pickup_summaries->after_cut_off_time =$after_cut_off_total;
            $pickup_summaries->attempted_and_picked =$attempted_and_picked;
            $pickup_summaries->attempted_and_not_picked =$attempted_and_not_picked;
            $pickup_summaries->attempted_failed =$attempted_failed;
            $pickup_summaries->save();

        }
        
    }    

    public function pickup_report_index(){
        
        $stats=V2PickupReportSummary::select([ DB::raw('SUM(total) as total'),
        DB::raw('SUM(pending_operations) as pending_operations'),DB::raw('SUM(pending_sales) as pending_sales'),
        DB::raw('SUM(before_cut_off_time) as before_cut_off_time'),
        DB::raw('SUM(after_cut_off_time) as after_cut_off_time'),DB::raw('SUM(attempted_and_picked) as attempted_and_picked'),
        DB::raw('SUM(attempted_and_not_picked) as attempted_and_not_picked'),
        DB::raw('SUM(attempted_failed) as attempted_failed')
        ])->get();
       // $stats=V2PickupReportSummary::all();
        $legends = V2PickupReportLegend::all();
        $department = DB::connection('reports')->table('admin_departments')->whereIn('id',[6,7])->get();
        $salesperson = DB::connection('reports')->table('admins')->leftjoin('admin_roles as ar', 'ar.id', '=', 'admins.role_id')->leftjoin('admin_departments as ad', 'ad.id', '=', 'ar.department_id')->select('admins.name')->where('ar.department_id',7)->get();
        $origin = DB::connection('reports')->table('cities')->where('pickup',1)->where('status',1)->get();
        $category = DB::connection('reports')->table('v2_pickup_report_categories')->get();

        return view('admin.reports.pickup_report')->with(['stats'=>$stats,'legends'=>$legends,'departments'=>$department,'salespersons'=>$salesperson,'origins'=>$origin,'categories'=>$category]);
    }

    public function pickup_report_list(Request $request){
        $today = Carbon::now()->startOfDay();

        $pickup_report = V2PickupReport::join('v2_pickup_requests as v', 'v2_pickup_reports.pickup_request_id', '=', 'v.id')
             ->leftjoin('v2_pickup_request_statuses as vprs','vprs.id','=','v2_pickup_reports.status_id')
             ->leftjoin('users as u','u.id','=','v.shipper_id')

            ->leftjoin('sale_person_tags as spt','spt.id','=','v2_pickup_reports.sale_person_id')
            ->leftjoin('admins as a', function ($join) {
                $join->on('a.id', '=', 'spt.admin_id')
                    ->where('spt.status','=',0);
            })
            ->leftjoin('admin_departments as ad','ad.id','=','v2_pickup_reports.department_id')
            ->join('user_shipping_infos as usi', 'v.pickup_address_id', '=', 'usi.id')
            ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
            ->select('v.id as pickup_request_id', 'v.created_at as requested_date','vprs.name as status','u.name as shipper',
            'a.name as salesperson','v2_pickup_reports.expected_shipments as expected_shipments',
            'v2_pickup_reports.received_shipments as received_shipments','v2_pickup_reports.difference_shipments as difference_shipments',
            'ad.name as department','v.attempts as attempted_count','usi.poc AS contact_person', 'usi.vendor as vendor',
            'usi.phone AS contact_number','usi.pickup_address AS address', 'ci.name AS city','v2_pickup_reports.category_id as category_id')
            // ->groupBy('v2_pickup_reports.pickup_request_id')
            ;
        $datatables = Datatables::of($pickup_report)
        ->setRowAttr([
            'class' => function ($pickup_report) {
                if (($pickup_report->difference_shipments <= 10)  && ($pickup_report->category_id == 1) ) {
                    return 'attempted_and_picked_less_than_ten';
                }
                else if(($pickup_report->difference_shipments > 10)  && ($pickup_report->category_id == 1) ){
                    return 'attempted_and_picked_greater_than_ten';
                }
                else if($pickup_report->category_id == 2){
                    return 'attempt_and_notpicked';
                }
                else if($pickup_report->category_id == 3){
                    return 'attempt_failed';
                }
                else if($pickup_report->status_id == 4){
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
                        $reasons .= V2PickupRequestNotPickReason::find($reason_id)->name . PHP_EOL;
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
                        $trax_remarks .= $remark . PHP_EOL;
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
                        $attempted_date .= $attempt_date . PHP_EOL;
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

        if($cut_off_time = $request->get('search_cut_off_time')){
            $datatables->where('v.after_cut_off_time', '=', $cut_off_time);
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatables->whereBetween('v2_pickup_reports.created_at', [$from,$to]);
        }

        return $datatables->make(true);
    } 
    
    
   
}
