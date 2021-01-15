<?php

namespace App\Http\Controllers\Admins\Retail;

use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\RetailPickupNote;
use App\Http\Models\Admin\RetailPickupNoteShipment;
use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class RetailCashCollectionController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin')->except('cancel');

        $this->middleware('Permission');
    }

    public function retail_index(){
        return view('admin.retail.pending_cash_collection.index');
    }

    public function retail_list(Request $request)
    {
        $deliveries = RetailPickupNote::join('cities AS oc', 'retail_pickup_notes.hub_id', '=', 'oc.id')
            ->leftjoin('riders as r', 'retail_pickup_notes.rider_id', '=', 'r.id')
            ->join('admins as a', 'a.id', '=', 'retail_pickup_notes.assigned_by')
            ->join('retail_users as ru', 'ru.id', '=', 'retail_pickup_notes.retail_user_id')
            ->leftJoin('retail_franchises as rf', function ($join) {
                $join->on('rf.id', '=', 'ru.category_id')
                    ->where('ru.category', '=',1);
            })
            ->leftJoin('retail_trax_centers as rc', function ($join) {
                $join->on('rc.id', '=', 'ru.category')
                    ->where('ru.category', '=',2);
            })
            ->select(['retail_pickup_notes.id', 'retail_pickup_notes.id as retail_pickup_note_id', 'oc.id as hub_id', 'oc.name as hub','r.name as rider','a.name as assignee',  'retail_pickup_notes.assigned_at as time', 'retail_pickup_notes.shipments as count', 'retail_pickup_notes.amount as amount','rf.name as franchise','rf.id as franchise_code','rc.name as center','rc.id as center_code','ru.category as category'])
           ->whereIn('retail_pickup_notes.status', [1,2,3])
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
//            ->editColumn('delivered_shipments_link', function($deliveries) {
//                if ($deliveries->delivered_shipments != 0) {
//                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->delivered_shipments . '</button>';
//                }
//                else {
//                    return 0;
//                }
//            })
            ->editColumn('time', function ($user) {
                if($user->time == null) {
                    return '-';
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
            })
            ->addColumn('action', function ($deliveries) {
//                if (session('role_id') == 1 || in_array(106, session('permissions'))) {
                    $dropdown = '
                          <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                                <a href="javascript:void(0);" class="dropdown-item cash_collect"><i class="la la-money primary"></i> Collect Cash</a>
                            </div>
                          </div>
                        ';

                    return $dropdown;
            });
        if ($tracking_number = $request->get('search_tracking')) {
            $datatable->join('retail_pickup_note_shipments as rpns', 'retail_pickup_notes.id', '=', 'rpns.retail_pickup_note_id')
                ->join('shipments as s', 'rpns.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
        }
        return $datatable->make(true);
    }

    public function number_of_shipments(Request $request){
        $pickup_note_id = $request->input('pickup_note_id');
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



//    public function pending_cash_collect(Request $request)
//    {
//        $delivery_note_id = $request->delivery_note_id;
//        if ($delivery_note_id != null) {
//            $delivery_note_details = DeliveryNote::find($delivery_note_id);
//            if ($delivery_note_details->cash_collection_status == 0) {
//                $delivery_note_details = DeliveryNote::where('id', $delivery_note_id)->where('cash_collection_status', 0)->first();
//                $delivery_note_details->cash_collection_status = 1;
//                $delivery_note_details->cash_collected_by = Auth::id();
//                $delivery_note_details->cash_collected_at = Carbon::now();
//                $delivery_note_details->save();
//                return response()->json(['status' => 1, 'success' => 'Cash collected successfully!']);
//            } else {
//                return response()->json(['status' => 0, 'error' => 'Cash is already collected!']);
//            }
//        } else {
//            return response()->json(['status' => 0, 'error' => 'Delivery note not found!']);
//        }
//    }

    public function pending_cash_collect_all(Request $request)
    {
//        $note_ids = explode(',', $request->pickup_note_ids);
        $note_ids =  $request->pickup_note_ids;
        $notes = array();
        foreach ($note_ids as $note_id) {
            $note_details = RetailPickupNote::where('id', $note_id)->where('status', 2)->first();
            if ($note_details) {
                $note_details->pncc_status = 3;
                $note_details->cash_collected_by = Auth::id();
                $note_details->cash_collected_at = Carbon::now();
                $note_details->save();
            } else {
                $notes[] = $note_id;
            }
        }
        if (empty($notes)) {
            return response()->json(['status' => 1, 'success' => 'Cash collected successfully!']);
        } else {
            return response()->json(['status' => 0, 'error' => 'These Pickup notes could not be updated!', 'notes' => $notes]);
        }

    }

}
