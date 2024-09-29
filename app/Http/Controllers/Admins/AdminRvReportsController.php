<?php

namespace App\Http\Controllers\Admins;
use App\Http\Models\RvShipmentAssignAgentDetails;
use App\Http\Controllers\Controller;
use App\Http\Traits\RvTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
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
        return view('admin.reports.bot_rvr.index');
    }

    public function botRvCallRecordList(Request $request){
       
        $rv_report = RvShipmentAssignAgentDetails::where('agent_id',4620)
        ->where('rv_assign_agent_status_id','!=','')
        ->where('call_count','!=','')
            ->select(
                DB::raw('Date(created_at) as date'),
                DB::raw('(case  WHEN call_count = 1 THEN "1st Calls" 
                WHEN call_count = 2 THEN "2nd Calls" 
                WHEN call_count = 3 THEN "3rd Calls"
                WHEN call_count IS NULL THEN "Total"
                 end
                )
                as description'),
                DB::raw('sum(case when rv_assign_agent_sub_status_id = 35 then 1 else 0 end) AS option1'),
                DB::raw('sum(case when rv_assign_agent_sub_status_id = 36 then 1 else 0 end) AS option2'),
                DB::raw('sum(case when rv_assign_agent_sub_status_id = 37 then 1 else 0 end) AS option3'),
                DB::raw('sum(case when rv_assign_agent_sub_status_id = 34 then 1 else 0 end) AS option4'),
                DB::raw('sum(CASE WHEN rv_assign_agent_sub_status_id = 35 THEN 1 ELSE 0 END 
                        + CASE WHEN rv_assign_agent_sub_status_id = 36 THEN 1 ELSE 0 END
                        + CASE WHEN rv_assign_agent_sub_status_id = 37 THEN 1 ELSE 0 END
                        + CASE WHEN rv_assign_agent_sub_status_id = 34 THEN 1 ELSE 0 END) AS total1'
                    ),
                    DB::raw('sum(case when rv_assign_agent_sub_status_id = 32 then 1 else 0 end) AS busy'),
                    DB::raw('sum(case when rv_assign_agent_sub_status_id = 33 then 1 else 0 end) AS disconnected'),
                    DB::raw('sum(CASE WHEN rv_assign_agent_sub_status_id = 32 THEN 1 ELSE 0 END 
                    + CASE WHEN rv_assign_agent_sub_status_id = 33 THEN 1 ELSE 0 END) AS total2'
                ),    
                DB::raw('sum(CASE WHEN rv_assign_agent_sub_status_id = 35 THEN 1 ELSE 0 END 
                        + CASE WHEN rv_assign_agent_sub_status_id = 36 THEN 1 ELSE 0 END
                        + CASE WHEN rv_assign_agent_sub_status_id = 37 THEN 1 ELSE 0 END
                        + CASE WHEN rv_assign_agent_sub_status_id = 34 THEN 1 ELSE 0 END
                        + CASE WHEN rv_assign_agent_sub_status_id = 32 THEN 1 ELSE 0 END  
                        + CASE WHEN rv_assign_agent_sub_status_id = 33 THEN 1 ELSE 0 END) AS grandtotal' ),
                DB::raw('count(shipment_id) as no_of_shipment'),

                        )
                    
                ->groupBy(DB::raw('DATE(created_at) ,call_count WITH ROLLUP')
            );
            $datatable = Datatables::of($rv_report);
            if ($request->get('search_date_from') && $request->get('search_date_to')) {
                $from = $request->get('search_date_from');
                $to = $request->get('search_date_to');
                $rv_report->where([['rv_shipment_assign_agent_details.created_at','>=', $from], ['rv_shipment_assign_agent_details.created_at', '<=', $to]]);
            }

            return $datatable->make(true);
    }
}
