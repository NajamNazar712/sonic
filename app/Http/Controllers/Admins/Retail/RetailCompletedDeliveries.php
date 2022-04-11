<?php

namespace App\Http\Controllers\Admins\Retail;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\Admins\DeliveryController;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\PickupNoteStationDepositNote;
use App\Http\Models\Admin\Retail\RetailCashDeposit;
use App\Http\Models\Admin\Retail\RetailCashDepositShipment;
use App\Http\Models\Admin\RetailPickupNote;
use App\Http\Models\Admin\RetailPickupNoteShipment;
use App\Http\Models\Admin\StationDepositNote;
use App\Http\Models\BanksList;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Auth;
use Illuminate\Support\Facades\DB;

class RetailCompletedDeliveries extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin')->except('cancel');

        $this->middleware('Permission');
    }


    public function index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),24);
        return view('admin.retail.completed.index');
    }

    public function list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),84);
        }

        $deliveries = RetailCashDeposit::leftjoin('retail_users as ru', 'ru.id', '=', 'retail_cash_deposits.retail_user_id')
                        ->leftjoin('retail_franchises as rf', 'rf.id', '=', 'ru.category_id')
                        ->leftjoin('retail_trax_centers as rc', 'rc.id', '=', 'ru.category_id')
                        ->leftjoin('retail_cash_deposit_shipments as rcds', function ($join) {
                            $join->on('rcds.cash_deposit_id', '=', 'retail_cash_deposits.id')
                                ->where('rcds.id', '=',
                                    DB::raw('(select max(id) from retail_cash_deposit_shipments where retail_cash_deposit_shipments.cash_deposit_id = retail_cash_deposits.id )'));
                        })
                        ->leftjoin('retail_pickup_note_shipments as rpns','rpns.shipment_id','=','rcds.shipment_id')
                        ->leftjoin('retail_pickup_notes as rpn','rpn.id','=','rpns.retail_pickup_note_id')
                        ->join('cities AS oc', 'rpn.hub_id', '=', 'oc.id')
                        ->leftjoin('riders as r', 'rpn.rider_id', '=', 'r.id')
                        ->join('admins as a', 'a.id', '=', 'rpn.assigned_by')
                        ->join('admins as cc', 'cc.id', '=', 'rpn.cash_collected_by')
                        ->leftjoin('retail_trax_centers as rtc', 'rtc.pickup_address_id', '=', 'rpn.pickup_address_id')
                        ->select(['rpn.id', 'retail_cash_deposits.id as retail_pickup_note_id', 'oc.id as hub_id', 'oc.name as hub','r.name as rider','a.name as assignee',  'rpn.assigned_at as assigned_at', 'rpn.shipments as shipment_count', 'rpn.amount as amount','rf.name as franchise','rf.code as franchise_code','rc.name as center','rc.code as center_code','ru.category as category','cc.name as collected_by','rpn.cash_collected_at', 'rtc.name as retail_trax_center_name', 'rtc.code as retail_trax_center_code'])
                        ->where('rpn.status', 4)
                        ->where('rpn.pncc_status', '=', 0);

            // $deliveries = RetailPickupNote::join('cities AS oc', 'retail_pickup_notes.hub_id', '=', 'oc.id')
            //     ->leftjoin('riders as r', 'retail_pickup_notes.rider_id', '=', 'r.id')
            //     ->join('admins as a', 'a.id', '=', 'retail_pickup_notes.assigned_by')
            //     ->join('admins as cc', 'cc.id', '=', 'retail_pickup_notes.cash_collected_by')
            //     ->leftjoin('retail_users as ru', 'ru.id', '=', 'retail_pickup_notes.retail_user_id')
            //     ->leftJoin('retail_franchises as rf', 'rf.id', '=', 'ru.category_id')
            //     ->leftJoin('retail_trax_centers as rc', 'rc.id', '=', 'ru.category_id')
            //     ->leftjoin('retail_trax_centers as rtc', 'rtc.pickup_address_id', '=', 'retail_pickup_notes.pickup_address_id')
            //     ->select(['retail_pickup_notes.id', 'retail_pickup_notes.id as retail_pickup_note_id', 'oc.id as hub_id', 'oc.name as hub','r.name as rider','a.name as assignee',  'retail_pickup_notes.assigned_at as assigned_at', 'retail_pickup_notes.shipments as shipment_count', 'retail_pickup_notes.amount as amount','rf.name as franchise','rf.code as franchise_code','rc.name as center','rc.code as center_code','ru.category as category','cc.name as collected_by','retail_pickup_notes.cash_collected_at', 'rtc.name as retail_trax_center_name', 'rtc.code as retail_trax_center_code'])
            //     ->where('retail_pickup_notes.status', 4)
            //     ->where('retail_pickup_notes.pncc_status', '=', 0);


        $datatable = Datatables::of($deliveries)
            ->setRowAttr([
                'data-hub' => function ($deliveries) {
                    return $deliveries->hub_id;
                },
            ])
            ->addColumn('count', function($deliveries) {
                if ($deliveries->shipment_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipment_count . '</button>';
                }
                else {
                    return 0;
                }
            })
            ->addColumn('store', function ($user) {
                if($user->category){
                    if($user->category == 1){
                        return $user->franchise;
                    }
                    else{
                        return $user->center;
                    }
                }else{
                    return $user->retail_trax_center_name;
                }
            })
            ->editColumn('code', function ($user) {
                if($user->category){
                    if($user->category == 1){
                        return $user->franchise_code;
                    }
                    else{
                        return $user->center_code;
                    }
                }else{
                    return $user->retail_trax_center_code;
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
        $pncc_ids = explode(',', $request->pickup_note_ids);
        if(count($pncc_ids) > 0){
            $updated = RetailPickupNote::where('pncc_status', 1)->whereIn('id', $pncc_ids)->exists();
            if (!$updated) {
                session(['pncc_ids' => $pncc_ids]);
                $pickup_note = RetailPickupNote::find($pncc_ids[0]);
                $hub_name = $pickup_note->hub->name;
                $banks_list = BanksList::where(['affiliate' => 1, 'status' => 1])->select('id', 'name')->get();
                return view('admin.retail.completed.sdn_create')->with(['hub_name' => $hub_name, 'banks_list' => $banks_list, 'pncc_ids' => session('pncc_ids')]);
            } else {
                return redirect(route('admin.delivery.completed.retail.index'))->with('error', 'SDN already created!');
            }
        }
        else{
            return redirect(route('admin.delivery.completed.retail.index'))->with('error', 'SDN already created!');
        }

    }

    public function create_sdn_view(Request $request) {
        return redirect(route('admin.delivery.completed.retail.index'))->with('error', 'Kindly reselect the Pickup Notes for Deposit!');
    }

    public function get_sdn_list(Request $request)
    {
        $pncc_ids = session('pncc_ids');
        $deliveries = RetailPickupNote::
        join('cities AS oc', 'retail_pickup_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'retail_pickup_notes.rider_id', '=', 'riders.id')
            ->select(['retail_pickup_notes.id as retail_pickup_note_id', 'oc.id as hub_id', 'oc.name as hub', 'riders.name as rider', 'retail_pickup_notes.amount', 'retail_pickup_notes.shipments as shipment_count'])
            ->whereIn('retail_pickup_notes.id', $pncc_ids);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('oc.hub_id', session('hubs'));
        }

        return Datatables::of($deliveries)
            ->addColumn('retail_pickup_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->retail_pickup_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->setRowAttr([
                'data-hub' => function ($deliveries) {
                    return $deliveries->hub_id;
                },
            ])
            ->addColumn('remarks', function ($deliveries) {
                $reason = '<input class="form-control" name="remarks[' . $deliveries->retail_pickup_note_id . ']" placeholder="Enter Remarks">';
                return $reason;
            })
            ->make(true);
    }

    public function create_sdn_submit(Request $request)
    {
        if ($request->sdn_hub_id) {
            $pncc_ids = explode(',', $request->sdn_pncc_ids);
            $check_status = RetailPickupNote::whereIn('id', $pncc_ids)->where('pncc_status', 1)->exists();
            if (!$check_status) {
//                $expense = $request->has('total_expenses') ? $request->total_expenses : 0;
                $total_amount = $request->total_pncc_amount;
//                $total_amount = $request->has('total_amount') ? $request->total_amount : $request->total_dncc_amount;
                $created_by = Auth::id();
                $sdn = StationDepositNote::create([
                    'hub_id' => $request->sdn_hub_id,
                    'dncc_count' => $request->sdn_count,
                    'sdn_delivered_shipments' => $request->sdn_delivered_shipments,
                    'sdn_amount' => $request->total_pncc_amount,
                    'sdn_net_amount' => $total_amount,
                    'deposited_by' => $created_by,
                    'sdn_type' => 2
                ]);
                foreach ($pncc_ids as $pncc) {
                    PickupNoteStationDepositNote::create([
                        'station_deposit_note_id' => $sdn->id,
                        'retail_pickup_note_id' => $pncc
                    ]);
                    RetailPickupNote::where('id', $pncc)->update(['expense' => $request->expense[$pncc], 'net_amount' => $request->net_amount[$pncc], 'remarks' => $request->remarks[$pncc], 'pncc_status' => 1]);
                    
                }
                DeliveryController::add_sdn_logs($sdn->id, 0, $created_by);

                return redirect(route('admin.delivery.sdn.index'))->with('success', 'SDN created successfully!');
            } else {
                return redirect(route('admin.delivery.sdn.index'))->with('error', 'SDN already created!');
            }
        }
    }

    public function sdn_details(Request $request, $id)
    {
        return view('admin.retail.sdn.details')->with('sdn_id', $id);
    }

    public function sdn_details_ajax(Request $request, $id)
    {
        $deliveries = StationDepositNote::
        join('pickup_note_station_deposit_notes as dnsdn', 'dnsdn.station_deposit_note_id', '=', 'station_deposit_notes.id')
            ->join('retail_pickup_notes', 'retail_pickup_notes.id', '=', 'dnsdn.retail_pickup_note_id')
            ->join('cities AS oc', 'retail_pickup_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'retail_pickup_notes.rider_id', '=', 'riders.id')
            ->select(['retail_pickup_notes.id as pncc', 'retail_pickup_notes.id', 'oc.id as hub_id', 'oc.name as hub', 'riders.name as rider', 'retail_pickup_notes.amount', 'retail_pickup_notes.shipments', 'retail_pickup_notes.remarks'])
            ->where('station_deposit_notes.id', $id);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('station_deposit_notes.hub_id', session('hubs'));
        }

        return Datatables::of($deliveries)
            ->editColumn('pncc', function ($deliveries) {
                return str_pad($deliveries->pncc, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->make(true);
    }
}
