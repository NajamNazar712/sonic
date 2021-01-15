<?php

namespace App\Http\Controllers\Admins\Retail;

use App\Http\Controllers\Controller;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\RetailPickupNote;
use App\Http\Models\Admin\RetailPickupNoteShipment;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class RetailCompletedDeliveries extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin')->except('cancel');

        $this->middleware('Permission');
    }


    public function index(){
        return view('admin.retail.completed.index');
    }

    public function list(Request $request)
    {
        $deliveries = RetailPickupNote::join('cities AS oc', 'retail_pickup_notes.hub_id', '=', 'oc.id')
            ->leftjoin('riders as r', 'retail_pickup_notes.rider_id', '=', 'r.id')
            ->join('admins as a', 'a.id', '=', 'retail_pickup_notes.assigned_by')
            ->join('admins as h', 'h.id', '=', 'retail_pickup_notes.cash_collected_by')
            ->join('retail_users as ru', 'ru.id', '=', 'retail_pickup_notes.retail_user_id')
            ->leftJoin('retail_franchises as rf', function ($join) {
                $join->on('rf.id', '=', 'ru.category_id')
                    ->where('ru.category', '=',1);
            })
            ->leftJoin('retail_trax_centers as rc', function ($join) {
                $join->on('rc.id', '=', 'ru.category')
                    ->where('ru.category', '=',2);
            })
            ->select(['retail_pickup_notes.id', 'retail_pickup_notes.id as retail_pickup_note_id', 'oc.id as hub_id', 'oc.name as hub','r.name as rider','a.name as assignee',  'retail_pickup_notes.assigned_at as time', 'retail_pickup_notes.shipments as count', 'retail_pickup_notes.amount as amount','rf.name as franchise','rf.id as franchise_code','rc.name as center','rc.id as center_code','ru.category as category','h.name as collected_by','retail_pickup_notes.cash_collected_at as cash_collected_at'])
            ->where('retail_pickup_notes.status', 4)
            ->where('retail_pickup_notes.pncc_status', '=', 0);


        $datatable = Datatables::of($deliveries)
            ->editColumn('count', function($deliveries) {
                if ($deliveries->count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->count . '</button>';
                }
                else {
                    return 0;
                }
            })
            ->editColumn('store', function ($user) {
                if($user->category == 1){
                    return $user->franchise;
                }
                else{
                    return $user->center;
                }
            })
            ->editColumn('code', function ($user) {
                if($user->category == 1){
                    return $user->franchise_code;
                }
                else{
                    return $user->center_code;
                }
            });
        if ($tracking_number = $request->get('search_tracking')) {
            $datatable->join('retail_pickup_note_shipments as rpns', 'retail_pickup_notes.id', '=', 'rpns.retail_pickup_note_id')
                ->join('shipments as s', 'rpns.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
        }

        return $datatable->make(true);

    }

    public function shipments_delivered(Request $request){

        $pickup_note_id = $request->retail_pickup_note_id;
        $pickup_note_shipments= RetailPickupNoteShipment::where('retail_pickup_note_id',$pickup_note_id);
        if($pickup_note_shipments->exists()){
            $pickup_note_shipments = $pickup_note_shipments->get();
            $shipments = array();
            foreach ($pickup_note_shipments as $note){
                $shipment = Shipment::find($note->shipment_id);

                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Pickup Note  Shipments', 'shipments' => $shipments];

        }else{
            return ['status' => 0, 'success' => 'No Pickup Note Shipments', 'shipments' => FALSE];
        }

    }


}
