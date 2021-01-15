<?php

namespace App\Http\Controllers\Admins\Retail;

use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\RetailPickupNote;
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
            ->leftjoin('riders', 'retail_pickup_notes.rider_id', '=', 'riders.id')
            ->join('admins', 'admins.id', '=', 'retail_pickup_notes.assigned_by')
            ->join('retail_users as ru', 'ru.id', '=', 'retail_pickup_notes.retail_user_id')
            ->leftJoin('retail_franchises as rf', function ($join) {
                $join->on('rf.id', '=', 'ru.category_id')
                    ->where('ru.category', '=',1);
            })
            ->leftJoin('retail_trax_centers as rc', function ($join) {
                $join->on('rc.id', '=', 'ru.category')
                    ->where('ru.category', '=',2);
            })
            ->select(['retail_pickup_notes.id', 'retail_pickup_notes.id as retail_pickup_note_id', 'oc.id as hub_id', 'oc.name as hub', 'riders.name as rider', 'admins.name as assignee',  'retail_pickup_notes.assigned_at', 'retail_pickup_notes.shipments', 'retail_pickup_notes.amount','rf.name as franchise','rf.id as franchise_code','rc.name as center','rc.id as center_code','ru.category as category'])
//            ->where('delivery_notes.cash_collection_status', 0)
            ->where('retail_pickup_notes.status', '=', 1);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('delivery_notes.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($deliveries)
            ->editColumn('delivery_note', function ($deliveries) {
                return "<a href='javascript:void(0);' class='printdeliverynote'><u>" . str_pad($deliveries->delivery_note, 6, '0', STR_PAD_LEFT) . "</u></a><br><a href='javascript:void(0);' class='printDNCC'><u>PNCC</u></a>";
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
//            ->addColumn('delivery_note_id_padded', function ($deliveries) {
//                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
//            })
//            ->filterColumn('delivery_notes.id', function ($query, $keyword) {
//                return $query->where('delivery_notes.id', '=', $keyword);
//            })
            ->setRowAttr([
                'data-hub' => function ($deliveries) {
                    return $deliveries->hub_id;
                },
            ])
            ->editColumn('shipments_count_link', function($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipments_count . '</button>';
                }
                else {
                    return 0;
                }
            })
            ->editColumn('delivered_shipments_link', function($deliveries) {
                if ($deliveries->delivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->delivered_shipments . '</button>';
                }
                else {
                    return 0;
                }
            })
            ->editColumn('rider', function ($rider) {
                if($rider->special_rider){
                    return $rider->rider . ' (' . $rider->special_rider_name . ')';
                }else{
                    return $rider->rider;
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
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments;
        $shipments = array();
        if($delivery_note_shipments->count() != 0){
            foreach ($delivery_note_shipments as $delivery_note_shipment){
                $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function shipments_delivered(Request $request){
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments()->where('status','>',1)->get();
        $shipments = array();
        if($delivery_note_shipments->count() != 0){
            foreach ($delivery_note_shipments as $delivery_note_shipment){
                $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function pending_cash_collect(Request $request)
    {
        $delivery_note_id = $request->delivery_note_id;
        if ($delivery_note_id != null) {
            $delivery_note_details = DeliveryNote::find($delivery_note_id);
            if ($delivery_note_details->cash_collection_status == 0) {
                $delivery_note_details = DeliveryNote::where('id', $delivery_note_id)->where('cash_collection_status', 0)->first();
                $delivery_note_details->cash_collection_status = 1;
                $delivery_note_details->cash_collected_by = Auth::id();
                $delivery_note_details->cash_collected_at = Carbon::now();
                $delivery_note_details->save();
                return response()->json(['status' => 1, 'success' => 'Cash collected successfully!']);
            } else {
                return response()->json(['status' => 0, 'error' => 'Cash is already collected!']);
            }
        } else {
            return response()->json(['status' => 0, 'error' => 'Delivery note not found!']);
        }
    }

    public function pending_cash_collect_all(Request $request)
    {
        $note_ids = explode(',', $request->delivery_note_ids);
        $notes = array();
        foreach ($note_ids as $note_id) {
            $note_details = DeliveryNote::where('id', $note_id)->where('cash_collection_status', 0)->first();
            if ($note_details) {
                $note_details->cash_collection_status = 1;
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
            return response()->json(['status' => 0, 'error' => 'These delivery notes could not be updated!', 'notes' => $notes]);
        }

    }

}
