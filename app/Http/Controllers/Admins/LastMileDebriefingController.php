<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\ShipmentStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        return view('admin.debriefing.supervisor');
    }

    public function supervisor_list(Request $request)
    {
        $deliveries = DeliveryNote::join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->select(['delivery_notes.id as delivery_note', 'delivery_notes.id as delivery_note_id',  'oc.name as hub', 'riders.name as rider', 'delivery_notes.total_cod_amount as amount', 'delivery_notes.received_cod_amount as pending_cash_collection', 'delivery_notes.shipments_count', 'delivery_notes.shipments_count', 'delivery_notes.special_rider','delivery_notes.special_rider_name','delivery_notes.special_rider_phone','delivery_notes.delivered_shipments as delivered_shipments',DB::raw('(SELECT COUNT(d.id) FROM delivery_notes AS d INNER JOIN delivery_note_shipments AS dns ON d.id = dns.delivery_note_id WHERE dns.delivery_note_id = delivery_notes.id AND dns.status = 1) AS shipments_undelivered_count'), DB::raw('(SELECT COUNT(p.id) FROM delivery_notes AS p INNER JOIN delivery_note_shipments AS pdns ON p.id = pdns.delivery_note_id WHERE pdns.delivery_note_id = delivery_notes.id AND pdns.status = 0) AS shipments_pending_count')])
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
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipments_count . '</button>';
                }
                else {
                    return 0;
                }
            })
            ->addColumn('pending_shipments_link', function($deliveries) {
                if ($deliveries->shipments_pending_count != 0) {
                    return $deliveries->shipments_pending_count;
                }
                else {
                    return 0;
                }
            })
            ->addColumn('undelivered_shipments_link', function($deliveries) {
                if ($deliveries->shipments_undelivered_count != 0) {
                    return $deliveries->shipments_undelivered_count;
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
            });

        return $datatables->make(true);
    }

    public function agents_call_monitoring_view()
    {
        return view('admin.debriefing.agent_call_monitoring');
    }

    public function agents_call_monitoring_list ()
    {

    }

    public function caller_agent_view()
    {
        $where = array(7, 8, 9, 10, 12, 14, 15, 18, 56);
        $statuses = ShipmentStatus::whereIn('id', $where)->select('id','name')->where('status', 1)->get();
        return view('admin.debriefing.caller_agent')->with(['statuses'=>$statuses]);
    }
}
