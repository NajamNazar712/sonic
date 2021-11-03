<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\AgentCallMonitoring;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\ConsigneeLocation;
use App\Http\Models\ConsigneeShipmentLocation;
use App\Http\Models\RiderDelivery;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\City;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PackagingMaterialRequestHistory;
use App\Http\Models\WarehouseStock;
use App\Http\Models\WarehouseStockRequest;
use App\Http\Models\WarehouseStockRequestHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class LastMileDebriefingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function supervisor_view()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 233);
        $hubs = City::where('hub', 1)->get();

        $settings = GlobalSettings::where('type', 'debriefing_time_setting');

        if ($settings->exists()) {
            $settings = $settings->first();
            $time = $settings->text;
        }
        else {
            $time = 0;
        }

        $next_time = Carbon::today()->endOfDay()->addHours($time);
          
        $prev_time = Carbon::today()->addHours($time);


        $bot_sms = GlobalSettings::where('type', 'bot_sms_id')->first();


        $bot_admin_id = $bot_sms->setting_value;


        $agents_count = AgentCallMonitoring::where('agent_id', '!=', $bot_admin_id)->where('created_at','>=',$prev_time)
            ->where('created_at','<=',$next_time)->count();

        $bot_sms_count = AgentCallMonitoring::where('agent_id', '=', $bot_admin_id)->where('created_at','>=',$prev_time)
            ->where('created_at','<=',$next_time)->count();
        return view('admin.debriefing.supervisor')->with(['hubs' => $hubs, 'agent_calls_assigned_count' => $agents_count, 'bot_sms_count' => $bot_sms_count]);
    }

    public function supervisor_agents(Request $request){
        $admin_ids = AdminHub::where('hub_id',$request->hub_id)->pluck('admin_id')->toArray();
        if(count($admin_ids) > 0){

            //$agents = Admin::whereIn('id', $admin_ids)->where('role_id', 18)->where('status',1)->get();

            $agents = Admin::join('employee_attendances as ea','ea.employee_id','=','admins.id')
            ->whereIn('admins.id', $admin_ids)
            ->where('admins.role_id', 18)
            ->where('admins.status',1)
            ->where('ea.clock_out_datetime','=',null)
            ->where('ea.attendance_date','=',Carbon::now()->format('Y-m-d'))
            ->select('admins.id', 'admins.name')->get();
           
            return response()->json(['status' => 1, 'agents' => $agents]);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Agents Does\'nt exist!']);
        }
    }

    public function supervisor_assign_agents(Request $request){
        $delivery_note_details = DeliveryNote::find($request->delivery_note_id);
        if($delivery_note_details->status == 0){
            $delivery_note_shipments = $delivery_note_details->delivery_note_undelivered_shipments;
            if(count($delivery_note_shipments) > 0){
                foreach ($delivery_note_shipments as $delivery_note_shipment){
                    $agent_call_monitor = AgentCallMonitoring::where('shipment_id', $delivery_note_shipment->shipment_id)->where('delivery_note_id', $delivery_note_shipment->delivery_note_id);
                    if($agent_call_monitor->exists()){
                        $agent_call_monitor = $agent_call_monitor->first();
                        if($agent_call_monitor->completed == 0){
                            $agent_call_monitor->agent_id = $request->agent_id;
                        }
                    }
                    else{
                        $agent_call_monitor = new AgentCallMonitoring;
                        $agent_call_monitor->agent_id = $request->agent_id;
                        $agent_call_monitor->shipment_id = $delivery_note_shipment->shipment_id;
                        $agent_call_monitor->delivery_note_id = $delivery_note_details->id;
                    }
                    $agent_call_monitor->save();
                }
                return response()->json(['status' => 0, 'success' => 'Agent Assign successfully.']);
            }
            else{
                return response()->json(['status' => 1, 'error' => 'Undelivered shipments not found!']);
            }
        }
        else{
            return response()->json(['status' => 1, 'error' => 'Delivery note already verified!']);
        }
    }

    public function supervisor_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 234);
        }
        $settings = GlobalSettings::where('type', 'debriefing_time_setting');

        if ($settings->exists()) {
            $settings = $settings->first();
            $time = $settings->text;
        }
        else {
            $time = 0;
        }

        // $next_time = Carbon::today()->addHours($time);
        $next_time = Carbon::today()->endOfDay()->addHours($time);
          
        $prev_time = Carbon::today()->addHours($time);

        $deliveries = DeliveryNote::join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
//            ->leftjoin('agent_call_monitorings as acm', 'acm.delivery_note_id', '=', 'delivery_notes.id')
            ->leftjoin('agent_call_monitorings as acm', function ($join) {
                $join->on('acm.delivery_note_id', '=', 'delivery_notes.id')
                    ->where('acm.id', '=',
                        DB::raw('(select max(id) from agent_call_monitorings where agent_call_monitorings.delivery_note_id = delivery_notes.id)'));
            })
            ->leftjoin('admins as agent', 'agent.id', '=', 'acm.agent_id')
            ->select(['delivery_notes.id as delivery_note', 'delivery_notes.id as delivery_note_id',  'oc.name as hub', 'riders.name as rider', 'delivery_notes.total_cod_amount as amount', 'delivery_notes.received_cod_amount as pending_cash_collection', 'delivery_notes.shipments_count', 'delivery_notes.shipments_count', 'delivery_notes.special_rider','delivery_notes.special_rider_name','delivery_notes.special_rider_phone','delivery_notes.delivered_shipments as delivered_shipments',DB::raw('(SELECT COUNT(d.id) FROM delivery_notes AS d INNER JOIN delivery_note_shipments AS dns ON d.id = dns.delivery_note_id WHERE dns.delivery_note_id = delivery_notes.id AND dns.status = 1) AS shipments_undelivered_count'), DB::raw('(SELECT COUNT(p.id) FROM delivery_notes AS p INNER JOIN delivery_note_shipments AS pdns ON p.id = pdns.delivery_note_id WHERE pdns.delivery_note_id = delivery_notes.id AND pdns.status = 0) AS shipments_pending_count'), 'agent.name as assigned_agent', DB::raw('(SELECT COUNT(f.id) FROM delivery_notes AS f INNER JOIN delivery_note_shipments AS fdns ON f.id = fdns.delivery_note_id WHERE fdns.delivery_note_id = delivery_notes.id AND fdns.fake_status = 1) AS shipments_fake_status_count'), 'agent.id as agent_id'])
            ->where('delivery_notes.created_at','>=',$prev_time)
            ->where('delivery_notes.created_at','<=',$next_time)
            ->where('delivery_notes.status', 0);


        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('delivery_notes.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($deliveries)
            ->editColumn('delivery_note', function ($deliveries) {
                return "<a href='javascript:void(0);' class='printdeliverynote'><u>" . str_pad($deliveries->delivery_note, 6, '0', STR_PAD_LEFT) . "</u></a>";
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('pending_cash_collection', function($shipment){
                return number_format($shipment->pending_cash_collection);
            })
            ->addColumn('delivery_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('delivery_notes.id', function ($query, $keyword) {
                return $query->where('delivery_notes.id', '=', $keyword);
            })
            ->addColumn('shipments_count_link', function($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $deliveries->shipments_count . '</button></div><h4 class="warning">100%</h4>';
                    return $count_cell;
                }
                else {
                    return 0;
                }
            })
            ->addColumn('delivered_shipments_link', function($deliveries) {
                if ($deliveries->delivered_shipments != 0) {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $deliveries->delivered_shipments . '</button></div><h4 class="success">'. round(($deliveries->delivered_shipments / $deliveries->shipments_count) * 100, 2) .'%</h4>';
                    return $count_cell;
                }
                else {
                    return 0;
                }
            })
            ->addColumn('pending_shipments_link', function($deliveries) {
                if ($deliveries->shipments_pending_count != 0) {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $deliveries->shipments_pending_count . '</button></div><h4 class="success">'. round(($deliveries->shipments_pending_count / $deliveries->shipments_count) * 100, 2) .'%</h4>';
                    return $count_cell;
                }
                else {
                    return 0;
                }
            })
            ->addColumn('undelivered_shipments_link', function($deliveries) {
                if ($deliveries->shipments_undelivered_count != 0) {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $deliveries->shipments_undelivered_count . '</button></div><h4 class="yellow">'. round(($deliveries->shipments_undelivered_count / $deliveries->shipments_count) * 100, 2) .'%</h4>';
                    return $count_cell;
                }
                else {
                    return 0;
                }
            })
            ->addColumn('fake_shipments_link', function($deliveries) {
                if ($deliveries->shipments_fake_status_count != 0) {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $deliveries->shipments_fake_status_count . '</button></div><h4 class="success">'. round(($deliveries->shipments_fake_status_count / $deliveries->shipments_count) * 100, 2) .'%</h4>';
                    return $count_cell;
                }
                else {
                    return 0;
                }
            })
            ->addColumn('call_agent_ratio', function ($deliveries){
                $call_overall_count = AgentCallMonitoring::where('delivery_note_id', $deliveries->delivery_note)->where('agent_id', $deliveries->agent_id)->count();
                $call_completed_count = AgentCallMonitoring::where('delivery_note_id', $deliveries->delivery_note)->where('agent_id', $deliveries->agent_id)->where('completed', 1)->count();

                $call_ratio = 0;
                if($call_overall_count > 0){
                    $call_ratio = ($call_completed_count / $call_overall_count) * 100;
                    $call_ratio = round($call_ratio, 2) . '%';
                }

                return $call_ratio;
            })
            ->addColumn('received_verify_delivery_ratio', function ($deliveries){
                $verify_shipments_count = ShipmentsJourney::where('reference_1_id', $deliveries->delivery_note)->where('verification', 1)->where('shipper_status_id', '!=', 5)->count();
                $verify_shipments_ratio = 0;
                $total_shipments = $deliveries->shipments_count;
                if($total_shipments > 0){
                    $verify_shipments_ratio = ($verify_shipments_count / $total_shipments) * 100;
                    $verify_shipments_ratio = round($verify_shipments_ratio, 2) . '%';
                }

                return $verify_shipments_ratio;
            })
            ->editColumn('rider', function ($rider) {
                if($rider->special_rider){
                    return $rider->rider . ' (' . $rider->special_rider_name . ')';
                }else{
                    return $rider->rider;
                }
            })->addColumn('action', function ($heads) {

                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">';
                    $assign_agent = '<button type="button" class="dropdown-item assign_agent" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus"></i></div><div class="col-9 offset-1">Assign Agent</div></button>';
                    $bot_sms = '<button type="button" class="dropdown-item bot_sms" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-message-circle"></i></div><div class="col-9 offset-1">Send BOT SMS</div></button>';

                    $dropdown .= $assign_agent;
                    $dropdown .= $bot_sms;

                    $dropdown .='</div></div>';
                return $dropdown;
                
            });

        return $datatables->make(true);
    }

    public function agents_call_monitoring_view()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 235);
        return view('admin.debriefing.agent_call_monitoring');
    }

    public function agents_call_monitoring_list (Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 236);
        }
        $settings = GlobalSettings::where('type', 'debriefing_time_setting');

        if ($settings->exists()) {
            $settings = $settings->first();
            $time = $settings->text;
        }
        else {
            $time = 0;
        }
        $next_time = Carbon::today()->endOfDay()->addHours($time);
          
        // $next_time = Carbon::today()->addHours($time);
        $prev_time = Carbon::today()->addHours($time);
        $data = AgentCallMonitoring::join('admins as agent','agent.id','=','agent_call_monitorings.agent_id')
            ->leftjoin('cities as hub','hub.id','=','agent.default_hub_id')
            ->select(['agent.id as agent_id','agent.name as agent_name','hub.name as hub'])
            ->where('agent_call_monitorings.created_at','>=',$prev_time)
            ->where('agent_call_monitorings.created_at','<=',$next_time)
            ->groupBy('agent_id');

        $datatables = Datatables::of($data)
            ->addColumn('assigned_calls_excel', function($calls) use ($next_time,$prev_time) {
                
               return AgentCallMonitoring::where('agent_id',$calls->agent_id)->where('created_at','>=',$prev_time)
               ->where('created_at','<=',$next_time)->count();
            })
            ->addColumn('completed_calls_excel', function($calls) use ($next_time,$prev_time) {
               
                return AgentCallMonitoring::where([['agent_id',$calls->agent_id],['completed',1]])->where('created_at','>=',$prev_time)
                ->where('created_at','<=',$next_time)->count();
            })
            ->addColumn('pending_calls_excel', function($calls) use ($next_time,$prev_time){
                
                return AgentCallMonitoring::where([['agent_id',$calls->agent_id],['completed',0]])->where('created_at','>=',$prev_time)
                ->where('created_at','<=',$next_time)->count();
            })
            ->addColumn('assigned_calls', function($calls) use ($next_time,$prev_time) {
               
                $count = AgentCallMonitoring::where('agent_id',$calls->agent_id)->where('created_at','>=',$prev_time)
                ->where('created_at','<=',$next_time)->count();
                if($count != 0)
                {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $count . '</button></div><h4 class="warning">100%</h4>';

                return $count_cell;
                }
                else{
                    return 0;
                }
            })
            ->addColumn('completed_calls', function($calls) use ($next_time,$prev_time) {
               
                $total_count =  AgentCallMonitoring::where('agent_id',$calls->agent_id)->where('created_at','>=',$prev_time)
                ->where('created_at','<=',$next_time)->count();
                $count =  AgentCallMonitoring::where([['agent_id',$calls->agent_id],['completed',1]])->where('created_at','>=',$prev_time)
                ->where('created_at','<=',$next_time)->count();
                if($count != 0)
                {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $count . '</button></div><h4 class="success">'. round(($count / $total_count) * 100, 2) .'%</h4>';
                return $count_cell;
                }
                else{
                    return 0;
                }
            })
            ->addColumn('pending_calls', function($calls) use ($next_time,$prev_time) {
               
                $total_count =  AgentCallMonitoring::where('agent_id',$calls->agent_id)->where('created_at','>=',$prev_time)
                ->where('created_at','<=',$next_time)->count();
                $count =  AgentCallMonitoring::where([['agent_id',$calls->agent_id],['completed',0]])->where('created_at','>=',$prev_time)
                ->where('created_at','<=',$next_time)->count();
                if($count != 0)
                {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $count . '</button></div><h4 class="danger">'. round(($count / $total_count) * 100, 2) .'%</h4>';
                    return $count_cell;
                }
                else{
                    return 0;
                }
            });

         return $datatables->make(true);
    }

    public function caller_agent_view()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 237);
        $settings = GlobalSettings::where('type', 'debriefing_time_setting');

        if ($settings->exists()) {
            $settings = $settings->first();
            $time = $settings->text;
        }
        else {
            $time = 0;
        }
        // $time = Carbon::today()->addHours(substr($time,0,2))->addMinutes(substr($time,3,2));
        // if(Carbon::now() > $time){
        //     $time->addDays(1);
        // }

        $next_time = Carbon::today()->endOfDay()->addHours($time);
          
        // $next_time = Carbon::today()->addHours($time);
        $prev_time = Carbon::today()->addHours($time);

        $calls = AgentCallMonitoring::where('agent_id',Auth::id())
            ->where('completed',0)->where('skip',0)->where('created_at','>=',$prev_time)
            ->where('created_at','<=',$next_time);
        if($calls->exists())
        {
            $data = $calls->first();
        }
        else{
            $calls = AgentCallMonitoring::where('agent_id',Auth::id())
                ->where('completed',0)->where('skip',1)->where('created_at','>=',$prev_time)
                ->where('created_at','<=',$next_time);

            if($calls->exists()) {
                $data = $calls->first();
            }
            else{
                return view('admin.debriefing.caller_agent')->with(['data'=>false]);
            }
        }
        $where = array(7, 8, 9, 15, 18, 56, 12);
        $statuses = ShipmentStatus::whereIn('id', $where)->select('id','name')->where('status', 1)->get();
        $shipment = Shipment::find($data->shipment_id);
        $delivery_note = DeliveryNote::find($data->delivery_note_id);
        $total_calls = AgentCallMonitoring::where('agent_id',Auth::id())->where('created_at','>=',Carbon::today())
        ->where('created_at','<=',$time)->count();
        $completed_calls = AgentCallMonitoring::where('agent_id',Auth::id())->where('created_at','>=',Carbon::today())
        ->where('created_at','<=',$time)->where('completed',1)->count();
        $pending_calls = AgentCallMonitoring::where('agent_id',Auth::id())->where('created_at','>=',Carbon::today())
        ->where('created_at','<=',$time)->where('completed',0)->count();

        $reattempt_count = ShipmentsJourney::where('shipment_id', $data->shipment_id)
                ->where('shipper_status_id','=',5)
                ->where('verification','=',1)
                ->select(DB::raw('count(shipment_id) as reattempts'))
                ->get()->first();

        $rider_status = ShipmentsJourney::where('shipment_id',$data->shipment_id)->whereNotNull('rider_id')->get()->last();
        if(!$rider_status){
            $rider_status = NULL;
        }

        $rider_deliveries = RiderDelivery::where('shipment_id', $data->shipment_id)->where('delivery_note_id', $data->delivery_note_id);
        if ($rider_deliveries->exists()) {
            $rider_deliveries = $rider_deliveries->latest('id')->first();
        }
        else{
            $rider_deliveries = NULL;
        }

        return view('admin.debriefing.caller_agent')->with(['data'=>true,'statuses'=>$statuses,'shipment'=>$shipment,'delivery_note'=>$delivery_note,'total_calls'=>$total_calls,'completed_calls'=>$completed_calls,'pending_calls'=>$pending_calls,'call'=>$data , 'reattempt_count' => $reattempt_count, 'rider_status' => $rider_status, 'rider_delivery' => $rider_deliveries]);
    }

    public function caller_agent_skip(Request $request)
    {
        $data = AgentCallMonitoring::find($request->id);
        if($data)
        {
            $data->skip = 1;
            $data->update();
            return response()->json(['status'=>1]);
        }
    }

    public function caller_agent_next(Request $request)
    {
        $data = AgentCallMonitoring::find($request->call_id);
        if($data){
            $delivery_note_id = $data->delivery_note_id;
            $delivery_note = DeliveryNote::find($delivery_note_id);
            $zero_cod_shipments = array();
            $shipment = $data->shipment_id;
            if ($delivery_note) {

                if ($delivery_note->status == 1) {
                    return redirect(route('admin.dashboard.index'))->with('error', 'Delivery note already verified!');
                }
                $verification = 1;
                $current_time = Carbon::now();

                $dispute_shipments = array();
                $delivered_status_array = array(14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 45, 46);
                $return_status_array = array(21, 22, 23, 24, 25, 44, 47, 48);

                $shipment_details = Shipment::find($shipment);
                if (!$shipment_details) {
                    return redirect()->back()->with('error', 'Shipment not found!');
                }
                $in_new_delivery_note = DeliveryNoteShipment::where('delivery_note_id', '>', $delivery_note_id)->where('shipment_id', $shipment)->exists();

                $shipper_status_id = NULL;
                $status_reason_id = NULL;
                $shipment_journey_remarks = NULL;
                $shipment_journey_remarks = $request->remarks;

                $shipper_status_id = $request->status;
                $status_reason_id = $request->reason;

                $open_box_shipment = "open_box.$shipment";
                $confirm_location_shipment = "confirm_location.$shipment";
                if ($shipment_details->booking_type_id == 5 &&  $shipper_status_id == 12) {
                    return redirect()->back()->with('error', 'Reverse Shipment can not updated as return confirmation pending!');
                }

                if (!$shipper_status_id) {
                    return redirect()->back()->with('error', 'Shipment Status not selected!');
                }

                if (!$status_reason_id) {
                    return redirect()->back()->with('error', 'Shipment Reason not selected!');
                }

                if ($shipper_status_id != 14) {
                    if (in_array($status_reason_id, [3, 4, 12, 34, 50])) {
                        $phone_number = $shipment_details->consignee_phone_number_1;
                        $previous_delivered_shipments = Shipment::where(function ($query) use ($phone_number) {
                            $query->where('consignee_phone_number_1', $phone_number)
                                ->orWhere('consignee_phone_number_2', $phone_number);
                        })
                            ->where('shipper_status_id', DB::raw(14));
                        if ($previous_delivered_shipments->exists()) {
                            return redirect()->back()->with('error', 'Shipment Reason invalid!');
                        }
                    }
                }

                if (!$in_new_delivery_note) {
                    if (!in_array($shipment_details->shipper_status_id, $return_status_array)) {

                        if (!in_array($shipment_details->shipper_status_id, $delivered_status_array)) {

                            $verify = DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment)->first();

                            $verify->call_verification = 1;

                            if ($request->has('fake_status')) {
                                $verify->fake_status = 1;
                                $verify->fake_status_updated_at = Carbon::now();
                            } else {
                                $verify->fake_status = 0;
                                $verify->fake_status_updated_at = Carbon::now();
                            }


                            $verify->save();

                            if ($shipper_status_id != null) {

                                //$shipment = Shipment::where('id', $shipment)->first();
                                $journey = ShipmentsJourney::where('shipment_id', $shipment)->latest()->first();
                                if ($shipment_details->shipper_status_id != $shipper_status_id) {
                                    if ($shipper_status_id == 7 || $shipper_status_id == 18) {
                                        ShipmentsJourneyController::add($shipment, $shipper_status_id, NULL, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);

                                        Shipment::where('id', $shipment)->update(['shipper_status_id' => $shipper_status_id]);
                                        DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                    }
                                    else if ($shipper_status_id == 56) {
                                        $parcel = Shipment::find($shipment);
                                        if ($parcel->booking_type_id == 2) {
                                            ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                            Shipment::where('id', $shipment)->update(['received_amount' => $parcel->amount, 'shipper_status_id' => 56, 'consignee_status_id' => 56]);
                                            DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                        }

                                    }
                                    else {
                                        if ($shipment_details->packaging_material_request == 0) {
                                            ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                            Shipment::where('id', $shipment)->update(['shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                                            DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                        } else if ($shipment_details->packaging_material_charges != '' && $shipment_details->packaging_material_request == 1) {
                                            ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                            Shipment::where('id', $shipment)->update(['shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                                            DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                        } else if ($shipment_details->packaging_material_charges == null && $shipment_details->packaging_material_request == 1) {
                                            if ($shipper_status_id != 12) {
                                                ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                                Shipment::where('id', $shipment)->update(['shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                                                DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                            }

                                        }

                                    }
                                    $dispute_shipments[] = $shipment;
                                }
                                else if (($shipment_details->shipper_status_id == $shipper_status_id) && ($journey->status_reason_id != $status_reason_id)) {

                                        ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);


                                }
                                else if (($shipment_details->shipper_status_id == $shipper_status_id) && ($journey->status_reason_id == $status_reason_id) && ($shipment_journey_remarks != $journey->remarks)) {

                                        ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);


                                }
                                else {
                                    if ($verification == 1) {

                                        ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);

                                    }
                                }
                            }//main if condition

                        }
                    }
//
                } else {
                    if ($shipper_status_id != null) {
                        if ($shipment_details->shipper_status_id != $shipper_status_id) {
                            $dispute_shipments[] = $shipment;
                        }
                    }
                }


                if ($verification == 1) {
                    if (!empty($dispute_shipments)) {
                        DisputeController::add_delivery_wrong_status_dispute($delivery_note_id, $dispute_shipments);
                    }

                    NotificationsController::send(13, $delivery_note_id);
                    NotificationsController::send(14, $delivery_note_id);


                    $data->completed = 1;
                    $data->save();

                    return redirect()->back()->with('success', 'Shipment verified successfully!');

                }


            } else {
                return redirect()->back()->with('error', 'Shipments not found!');
            }

        }
        else{
            return redirect()->back()->with('error', 'No Data found!');
        }

    }

    public function get_undelivered_shipments(Request $request){

        $delivery_note_id = $request->delivery_note_id;
        $delivery_note = DeliveryNote::find($delivery_note_id);
        if($delivery_note){
            $shipments_data = array();
            $delivery_note_shipments = $delivery_note->delivery_note_undelivered_shipments;

            if(count($delivery_note_shipments) > 0){
                foreach ($delivery_note_shipments as $delivery_note_shipment){
                    $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                    $shipments_data[$shipment->id]['tracking_number'] = $shipment->tracking_number;
                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('reference_1_id', $delivery_note_id)->latest()->first();
                    $reattempt = 0;
                    $reattempt_count = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 5)->count();
                    if($reattempt_count > 0){
                        $reattempt = $reattempt_count;
                    }
                    $status = '';
                    $reason = '';
                    if ($shipment_journey) {
                        $status = $shipment_journey->shipment_status_shipper->name;
                        if($shipment_journey->status_reason_id){
                            $reason = $shipment_journey->shipment_status_reason->name;
                        }
                    }
                    $shipments_data[$shipment->id]['status'] = $status;
                    $shipments_data[$shipment->id]['reason'] = $reason;
                    $shipments_data[$shipment->id]['reattempt'] = $reattempt;

                }

                return response()->json(['status' => 0, 'shipments_data' => $shipments_data]);
            }
            return response()->json(['status' => 1, 'error' => 'No undelived shipments found!']);
        }
        else{
            return response()->json(['status' => 1, 'error' => 'Something went wrong!!']);
        }
    }

    public function send_sms_to_undelivered_shipments(Request $request){

        $delivery_note_id = $request->delivery_note_id;
        $shipment_ids = $request->shipment_ids;
        $updated_shipments = FALSE;
        if(count($shipment_ids) > 0){
            foreach ($shipment_ids as $shipment_id){
                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment_id)->where('reference_1_id', $delivery_note_id)->latest()->first();


                $bot_admin_id = NULL;
                if ($shipment_journey) {
                    $shipment_status = Shipment::find($shipment_id)->shipper_status_id;

                    if($shipment_status != $shipment_journey->shipper_status_id){
                        continue;
                    }

                    $bot_sms = GlobalSettings::where('type', 'bot_sms_id')->first();

                    if($bot_sms){
                        $bot_admin_id = $bot_sms->setting_value;
                    }
                    ShipmentsJourneyController::add($shipment_id, $shipment_journey->shipper_status_id, $shipment_journey->consignee_status_id, $shipment_journey->status_reason_id, $shipment_journey->remarks, NULL, $bot_admin_id, $delivery_note_id, NULL,1);

                    $agent_call_monitoring = AgentCallMonitoring::where('shipment_id', $shipment_id)->where('delivery_note_id', $delivery_note_id);
                    if($agent_call_monitoring->exists()){
                        $agent_call_monitoring = $agent_call_monitoring->first();
                        $agent_call_monitoring->agent_id = $bot_admin_id;
                        $agent_call_monitoring->completed = 1;
                        $agent_call_monitoring->save();
                    }
                    else{
                        $agent_call_monitoring = new AgentCallMonitoring();
                        $agent_call_monitoring->agent_id = $bot_admin_id;
                        $agent_call_monitoring->shipment_id = $shipment_id;
                        $agent_call_monitoring->delivery_note_id = $delivery_note_id;
                        $agent_call_monitoring->completed = 1;
                        $agent_call_monitoring->save();
                    }
                    $updated_shipments = TRUE;
                    NotificationsController::send(145, $shipment_id, $delivery_note_id);
                }

            }
            if($updated_shipments){
                return response()->json(['status' => 0, 'success' => 'SMS send successfully!']);
            }
            else{
                return response()->json(['status' => 1, 'error' => 'SMS could not send!']);
            }
        }
        return response()->json(['status' => 1, 'error' => 'Something went wrong, try again!']);

    }
}
