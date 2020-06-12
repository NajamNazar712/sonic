<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\City;
use App\Http\Models\Shipment;
use App\Http\Models\Handover\Handover;
use App\Http\Models\Handover\HandoverShipments;
use App\Http\Models\Handover\HandoverResponsibilities;
use App\Http\Models\Handover\HandoverStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class AdminShipmentHandoverController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function handover_create_index(){
        $hub=HandoverResponsibilities::leftjoin('cities as c','c.id','=','handover_responsibilities.hub_id')
        ->select(['c.id','c.name'])->groupBy('handover_responsibilities.hub_id')->get();
        return view('admin.handover.index')->with(['hubs'=>$hub]);
    }

    public function handover_dropdown_val_fetch_from(Request $request){
        $value = $request->get('value');
        $dependent = $request->get('dependent');
        $dependent = "select Person";
        $data = HandoverResponsibilities::where('hub_id',$value)->get();
        $output = '<option value ="">' .ucfirst($dependent). '</option> ';
        foreach($data as $row){
            $output .= '<option value ="'.$row->id.'">' .$row->name. '</option> ';
        }
        echo $output;
    }
    public function handover_dropdown_val_fetch_to(Request $request){
        $value = $request->get('value');
        $dependent = $request->get('dependent');
        $data = HandoverResponsibilities::where('id',$value)->get();
        $output = '<option value ="">Select ' .ucfirst($dependent). '</option> ';
        foreach($data as $row){
            $output .= '<option value ="'.$row->id.'">' .$row->name. '</option> ';
        }
        echo $output;
    }

    public function arrival_bulk_shipment_details(Request $request){

            $shipment = Shipment::where('tracking_number', $request->tracking_number);

            if ($shipment->exists()) {
                $shipment = $shipment->first();
                $phone_no = NULL;
                $pickup_date = NULL;
                $special_instructions = NULL;
                // if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17) {
                  
                        $details = array();

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['shipper'] = $shipment->user->name;
                        $details['phone_number'] = $shipment->user->phone;
                        $details['pickup_date'] = $shipment->pickup_date;
                        $details['special_instructions'] = $shipment->special_instructions;

                        return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];

                // } else {
                //     return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
                // }
            } else {
                return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
            }
    }

    public function bulk_handover_submit(Request $request){
        $shipment_ids = explode(',', $request->shipment_ids);
        // $hub_id = explode(',', $request->hub);
        $total= '';
        foreach ($shipment_ids as $shipment_id) {
            $shipment = Shipment::find($shipment_id);
            $total++;
        }
            $handover = new Handover;
            $handover->created_by = Auth::id();
            $handover->from = $request->from;
            $handover->to = $request->to;
            $handover->hub = $request->hub_id;
            $handover->status_id = 1 ;
            $handover->shipments =$total;

            $handover->save();

            foreach ($shipment_ids as $shipment_id) {
            $handover_id = Handover::latest('id')->first();

            $handover_shipments = new HandoverShipments;

            $handover_shipments->handover_id=$handover_id->id;
            $handover_shipments->shipment_id= $shipment_id;
            $handover_shipments->status=0;

            $handover_shipments->save();
        }

        return redirect()->route('admin.handover.create.index')->with('success','Handover Note created Successfully!');

    }

//receive
    public function handover_receive_index(){
         return view('admin.handover.receive');
    }
    public function bulk_handover_submit_receive(Request $request){
        $shipment_ids = explode(',', $request->shipment_ids);
        $today = Carbon::now();

        foreach ($shipment_ids as $shipment_id) {
            HandoverShipments::where('shipment_id', $shipment_id)->update(['status' => 1]);
        }
   

        $received_counts =DB::table('handover_shipments')->where('status',1)->select('handover_id', DB::raw('count(*) as total_received'))
        ->groupBy(DB::raw('handover_id'))->get();

        foreach ($received_counts as $received_count){

            $created_counts =DB::table('handovers')->select('id','shipments')
            ->where('id',$received_count->handover_id)
            ->first();

            if(($received_count->total_received < $created_counts->shipments) && ($received_count->total_received >0))
            {
                Handover::where('id', $created_counts->id)->update(['status_id' => 3]);
                Handover::where('id', $created_counts->id)->update(['received' => $received_count->total_received, 'received_at' => $today,'received_by' => Auth::id()]);
            }
            else if($received_count->total_received == $created_counts->shipments){
                Handover::where('id', $created_counts->id)->update(['status_id' => 4]);
                Handover::where('id', $created_counts->id)->update(['received' => $received_count->total_received, 'received_at' => $today,'received_by' => Auth::id()]);      
            }
        }

        return redirect()->route('admin.handover.receive.index')->with('success','Handover Note Received Successfully!');

    }

//list
    public function handover_list_index(){
        return view('admin.handover.list');   
    }

    public function handover_list(Request $request){
        $handover_list = Handover::leftjoin('cities as c','c.id','=','handovers.hub')
        ->join('admins as a', 'a.id', '=', 'handovers.created_by')
        ->join('admins as ad', 'ad.id', '=', 'handovers.received_by')
        ->leftjoin('handover_statuses as hs','hs.id','=','handovers.status_id')
        ->leftjoin('handover_responsibilities as hr','hr.id','=','handovers.from')
        ->leftjoin('handover_responsibilities as hor','hor.id','=','handovers.to')
        ->select(['handovers.id','handovers.id as handover_id','a.name as created_by','ad.name as received_by','hr.name as from','hor.name as to','c.name as hub',
        'handovers.shipments as shipment_count','hs.name as status','handovers.received as received_shipments',
        'handovers.received_at as received_at']);

        $datatable = Datatables::of($handover_list)

        ->editColumn('shipment_count', function($handover_list) {
            if ($handover_list->shipment_count != 0) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $handover_list->shipment_count . '</button>';
            }
            else {
                return 0;
            }
            });

            if ($tracking_number = $request->get('search_tracking')) {
                $datatable->join('handover_shipments as hsh', 'hsh.handover_id', '=', 'handovers.id')
                ->join('shipments as s', 'hsh.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
                
            }
    
           
            
            
        return  $datatable->make(true);

    }

    public function handover_shipments_count(Request $request){

        $handover_id = $request->input('id');
        $handover_shipments = HandoverShipments::where('handover_id', $handover_id)->get();
        $shipments = array();
        if($handover_shipments->count() != 0){
            foreach ($handover_shipments as $handover_shipment){
                $shipment = Shipment::find($handover_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
                return ['status' => 0, 'success' => 'Handover Note Shipments', 'shipments' => $shipments];
        }else{
                return ['status' => 0, 'success' => 'No Handover Note Shipments', 'shipments' => FALSE];
        }
    }

    public function handover_shipments_delivered(Request $request){

        $handover_ids = $request->input('handover_ids');
        //$handover_ids = explode(',', $request->handover_ids);

        foreach ($handover_ids as $handover_id) {
          $handover_request = Handover::find($handover_id);
  
          if ($handover_request->status_id == 2) {
            return ['status' => 1, 'error' => 'One of the Handover has already been modified'];
          }
        }
  
        foreach ($handover_ids as $handover_id) {
          $handover_request = Handover::find($handover_id);
  
          $handover_request->status_id = 2;
  
          $handover_request->save();
        }
  
        return ['status' => 0, 'success' => 'Handover Shipments has been Delivered'];
    }



//Responsible
    public function responsibles_index(){
        $hubs = City::select(['id','name'])->where('hub',1)->get();
        return view('admin.handover.responsibles')->with(['hubs'=>$hubs]);
    }

    public function responsibles_list(){

        $responsibles_list = HandoverResponsibilities::leftjoin('cities as c','c.id','=','handover_responsibilities.hub_id')
        ->join('admins as a', 'a.id', '=', 'handover_responsibilities.created_by')
        ->leftjoin('admins as u', 'u.id', '=', 'handover_responsibilities.updated_by')
        ->select('handover_responsibilities.id as responsible_id','handover_responsibilities.name as name','c.name as hub','c.id as hub_id','a.name as created','u.name as updated','handover_responsibilities.status as status');

        $datatable = Datatables::of($responsibles_list)
        ->setRowAttr([
            'hub' => function ($data) {
                return $data->hub_id;
            }
        ])
            ->addColumn('status', function ($data){
                if($data->status == 0){
                    return 'Disable';
                }else{
                    return 'Enable';
                }
            })
            ->addColumn('action', function ($data){

                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                        $dropdown .= '<button type="button"  class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                    if ($data->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item disable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    } else {
                            $dropdown .= '<button type="button" class="dropdown-item enable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    }

                    return $dropdown;

            });

        return  $datatable->make(true);

    }

    public function responsibles_add(Request $request){
        $responsibles = new HandoverResponsibilities();
        $responsibles->name = $request->name;
        $responsibles->hub_id = $request->hub;
        $responsibles->created_by = Auth::id();
        $responsibles->updated_by = Auth::id();
        $responsibles->status = 1;
        $responsibles->save();
        return redirect()->back()->with(['status'=>1,'success'=>"Responsible has been Added successfully!"]);
    }

    public function responsibles_status(Request $request){
        $id = $request->id;
        $status = $request->status;
        $responsible = HandoverResponsibilities::find($id);
        if(!$responsible){
            return response()->json(['status' => 1, 'error' => 'Not found!']);
        }

        if($status == 1){
            $responsible->status = 1;
        }else if($status == 0){
            $responsible->status = 0;
        }
        $responsible->save();

        return response()->json(['status' => 0, 'success' => 'Status updated successfully!']);
    }

    public function responsibles_editview(Request $request){

        $responsible_id = HandoverResponsibilities::where('id',$request->id)->first();
        // $hub_id=HandoverResponsibilities::where('hub_id',$responsible_id->hub)->first();
        return response()->json(['status' => 1, 'responsible' => $responsible_id]);
    }
    public function responsibles_edit(Request $request){
        $responsible = HandoverResponsibilities::find($request->id);
         if($responsible){
             $responsible->name = $request->name;
             $responsible->hub_id = $request->hub;
             $responsible->updated_by = Auth::id();
             $responsible->save();
             return redirect()->back()->with(['status'=>1,'success'=>"Responsible has been Edited successfully!"]);
         }
         return redirect()->back()->with(['status'=>0,'error'=>"Responsible not found!"]);
    }
}
