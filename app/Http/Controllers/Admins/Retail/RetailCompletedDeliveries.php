<?php

namespace App\Http\Controllers\Admins\Retail;

use App\Http\Controllers\Controller;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\RetailPickupNote;
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
        $deliveries = DeliveryNote::
        join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->join('admins', 'admins.id', '=', 'delivery_notes.admin_id')
            ->leftjoin('admins as ccb', 'ccb.id', '=', 'delivery_notes.cash_collected_by')
            ->leftjoin('admins as ub', 'ub.id', '=', 'delivery_notes.updated_by')
            ->leftjoin('retail_franchises as rf','rf.default_hub','=','delivery_notes.hub_id')  //to be removed in future
            ->select(['delivery_notes.id as delivery_note', 'delivery_notes.id as delivery_note_id', 'oc.id as hub_id', 'oc.name as hub', 'riders.name as rider', 'routes.code as route', 'routes.start', 'routes.end', 'admins.name as assignee', 'ub.name as updated_by', 'delivery_notes.updated_at as updated_at', 'delivery_notes.delivered_shipments', 'delivery_notes.delivered_shipments as delivered_shipments_link', 'delivery_notes.created_at', 'delivery_notes.received_cod_amount as amount', 'delivery_notes.shipments_count', 'delivery_notes.shipments_count as shipments_count_link', 'delivery_notes.cash_collected_by','ccb.name as cash_collected', 'delivery_notes.cash_collected_at','delivery_notes.special_rider','delivery_notes.special_rider_name','delivery_notes.special_rider_phone', 'delivery_notes.status','rf.name as franchise','rf.code as code'])
            ->where('delivery_notes.cash_collection_status', 1)
            ->where('delivery_notes.dncc_status', 0);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('delivery_notes.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($deliveries)
            ->editColumn('delivery_note', function ($deliveries) {
                return "<a href='javascript:void(0);' class='printdeliverynote'><u>" . str_pad($deliveries->delivery_note, 6, '0', STR_PAD_LEFT) . "</u></a><br><a href='javascript:void(0);' class='printDNCC'><u>DNCC</u></a>";
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->addColumn('delivery_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('delivery_notes.id', function ($query, $keyword) {
                return $query->where('delivery_notes.id', '=', $keyword);
            })
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
            ->addColumn('action', function ($deliveries) {
                if (session('role_id') == 1 || in_array(106, session('permissions'))) {
                    $dropdown = '
                          <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                                <a href="javascript:void(0);" class="dropdown-item cash_collect"><i class="la la-money primary"></i> Collect Cash</a>
                            </div>
                          </div>
                        ';

                    return $dropdown;
                }
                return '';
            });
        if ($tracking_number = $request->get('search_tracking')) {
            $datatable->join('delivery_note_shipments as dns', 'delivery_notes.id', '=', 'dns.delivery_note_id')
                ->join('shipments as s', 'dns.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
        }

        return $datatable->make(true);

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
