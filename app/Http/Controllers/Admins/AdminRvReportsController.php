<?php

namespace App\Http\Controllers\Admins;

use ApiCallZongLog;
use App\Http\Models\RvShipmentAssignAgentDetails;
use App\Http\Controllers\Controller;
use App\Http\Models\Webhook\ApiCallLog;
use App\Http\Models\Webhook\ApiZongLog;
use App\Http\Traits\RvTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class AdminRvReportsController extends Controller
{
    use RvTrait;
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function botRvCallRecord(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 809);

        return view('admin.reports.bot_rvr.index');
    }

    public function botRvCallRecordList(Request $request){

        $rv_report = RvShipmentAssignAgentDetails::where('agent_id',4620)
        ->where('rv_assign_agent_status_id','!=','')
        ->where('call_count','!=','')
        ->select(
            DB::raw('Date(created_at) as date'),
            DB::raw("(case  WHEN call_count = 1 THEN '1st Calls' WHEN call_count = 2 THEN '2nd Calls' WHEN call_count = 3 THEN '3rd Calls' WHEN call_count IS NULL THEN 'Total' end ) as description"),
            DB::raw("sum(case when rv_assign_agent_sub_status_id = 35 then 1 else 0 end) AS option1"),
            DB::raw("concat(round(sum(case when rv_assign_agent_sub_status_id = 35 then 1 else 0 end) / count(DISTINCT Shipment_id) * 100,2), '%') AS option1_per"),
            DB::raw("sum(case when rv_assign_agent_sub_status_id = 36 then 1 else 0 end) AS option2"),
            DB::raw("concat(round(sum(case when rv_assign_agent_sub_status_id = 36 then 1 else 0 end) / count(DISTINCT Shipment_id) * 100,2), '%') AS option2_per"),
            DB::raw("sum(case when rv_assign_agent_sub_status_id = 37 then 1 else 0 end) AS option3"),
            DB::raw("concat(round(sum(case when rv_assign_agent_sub_status_id = 37 then 1 else 0 end) / count(DISTINCT Shipment_id) * 100,2), '%') AS option3_per"),
            DB::raw("sum(case when rv_assign_agent_sub_status_id = 34 then 1 else 0 end) AS option4"),
            DB::raw("concat(round(sum(case when rv_assign_agent_sub_status_id = 34 then 1 else 0 end) / count(DISTINCT Shipment_id) * 100,2), '%') AS option4_per"),
            DB::raw("sum(CASE WHEN rv_assign_agent_sub_status_id IN (35,36,37,34) then 1 else 0 end) AS total1"),
            DB::raw("concat(round(sum(case when rv_assign_agent_sub_status_id IN (35,36,37,34) then 1 else 0 end) / count(DISTINCT Shipment_id) * 100,2), '%') AS total1_per"),
            DB::raw("sum(case when rv_assign_agent_sub_status_id = 32 then 1 else 0 end) AS busy"),
            DB::raw("concat(round(sum(case when rv_assign_agent_sub_status_id = 32 then 1 else 0 end) / count(DISTINCT Shipment_id) * 100,2), '%') AS busy_per"),
            DB::raw("sum(case when rv_assign_agent_sub_status_id = 33 then 1 else 0 end) AS disconnected"),
            DB::raw("concat(round(sum(case when rv_assign_agent_sub_status_id = 33 then 1 else 0 end) / count(DISTINCT Shipment_id) * 100,2), '%') AS disconnected_per"),
            DB::raw("sum(CASE WHEN rv_assign_agent_sub_status_id IN (32,33) THEN 1 ELSE 0 END) AS total2"),
            DB::raw("concat(round(sum(case when rv_assign_agent_sub_status_id IN (32,33) then 1 else 0 end) / count(DISTINCT Shipment_id) * 100,2), '%') AS total2_per"),
            DB::raw("sum(CASE WHEN rv_assign_agent_sub_status_id IN (32,33,35,36,37,34) THEN 1 ELSE 0 END) AS grandtotal"),
            DB::raw('COUNT(DISTINCT CASE WHEN call_count IN (2, 3) THEN Shipment_id ELSE NULL END) + COUNT(CASE WHEN call_count = 1 THEN Shipment_id ELSE NULL END) as no_of_shipment')
        )->groupBy(DB::raw("DATE(created_at), call_count WITH ROLLUP"));
            $datatable = Datatables::of($rv_report);
            // ->editColumn('option1_per', function ($rv_report) {
            //     if($rv_report['description'] == '1st Calls'){
            //         if($rv_report['option1_per']){
            //             return round($rv_report['option1'] / $rv_report['no_of_shipments'] * 100 ,2).'%'; 
            //         }
            //         // if($rv_report['total1_per']){
            //         //     return round($rv_report['total1_per'] / $rv_report['no_of_shipments'] * 100 ,2).'%'; 
            //         // }

            //     }
            // });
            if ($request->get('search_date_from') && $request->get('search_date_to')) {
                $from = $request->get('search_date_from');
                $to = $request->get('search_date_to');
                $rv_report->where([['rv_shipment_assign_agent_details.created_at','>=', $from], ['rv_shipment_assign_agent_details.created_at', '<=', $to]]);
            }

            return $datatable->make(true);
    }

    public function botRvCallLogs()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 810);

        return view('admin.reports.bot_rvr_log.index');
    }
    public function botRvCallLogsList(Request $request)
    {

        $rv_call_logs = ApiCallLog::join('shipments as s','s.id', 'api_call_logs.shipment_id')
            // ->leftJoin('api_zong_logs as azl', function ($join) use ($request) {
            //     $join->on('azl.call_date_time', '=', 'api_call_logs.created_at');
            //     $join->on('azl.shipment_id', '=', 'api_call_logs.shipment_id');
            //     // $join->on('azl.created_at', '>=', DB::raw("'" . $request->get('search_date_from') . "'"));
            //     // $join->on('azl.created_at', '<=', DB::raw("'" . $request->get('search_date_to') . "'"));
            // })    
            ->leftJoin('api_zong_logs as azl', function ($join) {
                $join->on('azl.shipment_id', '=', 's.id')
                    ->whereRaw('DATE_FORMAT(azl.call_date_time, "%Y-%m-%d %H:%i") = DATE_FORMAT(api_call_logs.created_at, "%Y-%m-%d %H:%i")');
            })
            ->select('s.tracking_number as tracking_number',
            'api_call_logs.shipment_id as shipmentNo',
            'api_call_logs.call_count_initiate  as call_count',
            'api_call_logs.payload  as response',
            'api_call_logs.created_at as created_at',
            'azl.shipment_id as shipmentNo1',
            'azl.call_date_time as call_start_date',
            'azl.api_request as api_request',
            'azl.error as message',
            'azl.created_at as date_time')
            ->groupby('api_call_logs.call_count_initiate','api_call_logs.id');

              $datatable = Datatables::of($rv_call_logs)
            ->editColumn('tracking_number', function ($rv_call_logs) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$rv_call_logs->tracking_number' class='tracking' target='_blank'>$rv_call_logs->tracking_number</a></u>";
            })
            ->editColumn('response', function ($rv_call_logs) {
                if ($rv_call_logs['response'] && !request()->get('excel')) {
                    // Decode the JSON response
                    $responseData = json_decode($rv_call_logs['response'], true);

                    // Encode and escape the JSON for safe HTML output
                    $tooltipData = htmlspecialchars(json_encode($responseData, JSON_PRETTY_PRINT));

                    // Use a brief text for display (optional)
                    $displayText = 'Hover to view details';

                    return "<u><span data-toggle='tooltip' class='tracking json-tooltip' title='{$tooltipData}'>  {$displayText}  </span></u>";
                }else{
                    return $rv_call_logs['response'];
                }
                return '';
            })
            ->addColumn('call_message', function ($rv_call_logs) {
                if ($rv_call_logs['response']) {
                    return json_decode($rv_call_logs['response'])->message;
                }
            })
            ->editColumn('api_request', function ($rv_call_logs) {
                if ($rv_call_logs['api_request']&& !request()->get('excel')) {
                    // Decode the JSON response
                    $responseData = json_decode($rv_call_logs['api_request'], true);

                    // Encode and escape the JSON for safe HTML output
                    $tooltipData = htmlspecialchars(json_encode($responseData, JSON_PRETTY_PRINT));

                    // Use a brief text for display (optional)
                    $displayText = 'Hover to view details';

                    return "<u><span data-toggle='tooltip' class='tracking json-tooltip' title='{$tooltipData}'>  {$displayText}  </span></u>";
                }elseif(request()->get('excel')){
                    return $rv_call_logs['api_request'];
                }
                return '';
            })
            ->addColumn('call_end_date', function ($rv_call_logs) {
                if ($rv_call_logs['api_request']) {
                    return json_decode($rv_call_logs['api_request'])->end_date;
                }
            })
            ->addColumn('input', function ($rv_call_logs) {
                if ($rv_call_logs['api_request']) {
                    return json_decode($rv_call_logs['api_request'])->input;
                }
            })
            ->editColumn('message', function ($rv_call_logs) {
                if ($rv_call_logs['message'] && !request()->get('excel')) {
                    $tooltipData = htmlspecialchars($rv_call_logs['message'],JSON_PRETTY_PRINT);
                    $displayText = 'Hover to view details';

                    return "<u><span data-toggle='tooltip' class='tracking json-tooltip' title='{$tooltipData}'>  {$displayText}  </span></u>";
                }elseif($rv_call_logs['message'] && request()->get('excel')){
                    return $rv_call_logs['message'];
                }else{
                    return 'Data Saved SuccessFully!';
                }
            });
            if ($request->get('search_date_from') && $request->get('search_date_to') && !$request->get('search_tracking_no')) {
                $from = $request->get('search_date_from');
                $to = $request->get('search_date_to');
                $rv_call_logs->where([['api_call_logs.created_at', '>=', $from], ['api_call_logs.created_at', '<=', $to]]);
            }
            
        if($request->get('search_tracking_no')){
            $rv_call_logs->where('s.tracking_number', $request->get('search_tracking_no'));
        }   

        return $datatable->make(true);
    }
}
