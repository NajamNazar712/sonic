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


    
    public function completed_deliveries_selected_pncc(Request $request)
    {
        $pncc_ids = explode(',', $request->pncc_ids);
        $updated = RetailPickupNote::where('dncc_status', 1)->whereIn('id', $pncc_ids)->exists();
        if (!$updated) {
            session(['pncc_ids' => $pncc_ids]);
            $pickup_note = RetailPickupNote::find($pncc_ids[0]);
            $hub_name = $pickup_note->hub->name;
            $banks_list = BanksList::where(['affiliate' => 1, 'status' => 1])->select('id', 'name')->get();
            return view('admin.delivery.complete.sdn_create')->with(['hub_name' => $hub_name, 'banks_list' => $banks_list, 'dncc_ids' => session('dncc_ids')]);
        } else {
            return redirect(route('admin.delivery.sdn.index'))->with('error', 'SDN already created!');
        }
    }

    public function create_sdn_view(Request $request) {
        return redirect(route('admin.delivery.completed.index'))->with('error', 'Kindly reselect the Delivery Notes for Deposit!');
    }

    public function get_sdn_list(Request $request)
    {
        $dncc_ids = session('pncc_ids');
        $deliveries = RetailPickupNote::
        join('cities AS oc', 'retail_pickup_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'retail_pickup_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'retail_pickup_notes.route_id', '=', 'routes.id')
            ->join('admins', 'admins.id', '=', 'retail_pickup_notes.admin_id')
            ->select(['retail_pickup_notes.id as delivery_note_id', 'oc.id as hub_id', 'oc.name as hub', 'riders.name as rider', 'routes.code as route', 'routes.start', 'routes.end', 'retail_pickupnotes.received_cod_amount', 'retail_pickup_notes.shipments_count'])
            ->whereIn('delivery_notes.id', $dncc_ids);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('oc.hub_id', session('hubs'));
        }

        return Datatables::of($deliveries)
            ->addColumn('delivery_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('received_cod_amount', function($shipment){
                return number_format($shipment->received_cod_amount);
            })
            ->filterColumn('delivery_notes.id', function ($query, $keyword) {
                return $query->where('delivery_notes.id', '=', $keyword);
            })
            ->setRowAttr([
                'data-hub' => function ($deliveries) {
                    return $deliveries->hub_id;
                },
            ])
            ->editColumn('route', function ($rider) {
                return $rider->route . ' (' . $rider->start . ' to ' . $rider->end . ')';
            })
            ->filterColumn('route', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%' . $keyword . '%')->orWhere('routes.start', 'like', '%' . $keyword . '%')->orWhere('routes.end', 'like', '%' . $keyword . '%');
                } else {
                    $query->whereRaw('false');
                }
            })
//            ->editColumn('expense',function($deliveries){
//                return "<input id='expense' class='form-control expense numeric' placeholder='Expense' name='expense[{$deliveries->delivery_note_id}]'>";
//            })
//            ->editColumn('net_amount',function($deliveries){
//                return "<input class='form-control net_amount' readonly placeholder='Net Amount' name='net_amount[{$deliveries->delivery_note_id}]'>";
//            })
            ->addColumn('remarks', function ($deliveries) {
                $reason = '<input class="form-control" name="remarks[' . $deliveries->delivery_note_id . ']" placeholder="Enter Remarks">';
                return $reason;
            })
            ->make(true);
    }

    public function create_sdn_submit(Request $request)
    {
        if ($request->sdn_hub_id) {
            $dncc_ids = explode(',', $request->sdn_dncc_ids);
            $check_status = DeliveryNote::whereIn('id', $dncc_ids)->where('dncc_status', 1)->exists();
            if (!$check_status) {
                $expense = $request->has('total_expenses') ? $request->total_expenses : 0;
                $total_amount = $request->total_dncc_amount;
//                $total_amount = $request->has('total_amount') ? $request->total_amount : $request->total_dncc_amount;
                $sdn_id = StationDepositNote::create([
                    'hub_id' => $request->sdn_hub_id,
                    'dncc_count' => $request->sdn_count,
                    'sdn_delivered_shipments' => $request->sdn_delivered_shipments,
                    'sdn_amount' => $request->total_dncc_amount,
                    'sdn_net_amount' => $total_amount,
                    'deposited_by' => Auth::id()
                ]);
                foreach ($dncc_ids as $dncc) {
                    DeliveryNoteStationDepositNote::create([
                        'station_deposit_note_id' => $sdn_id->id,
                        'delivery_note_id' => $dncc
                    ]);
                    DeliveryNote::where('id', $dncc)->update(['expense' => $request->expense[$dncc], 'net_amount' => $request->net_amount[$dncc], 'remarks' => $request->remarks[$dncc], 'dncc_status' => 1]);
                }

                return redirect(route('admin.delivery.sdn.index'));
            } else {
                return redirect(route('admin.delivery.sdn.index'))->with('error', 'SDN already created!');
            }
        }
    }
}
