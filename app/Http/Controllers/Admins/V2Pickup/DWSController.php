<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\City;
use App\Http\Models\DwsDetail;
use App\Http\Models\PickupAction;
use App\Http\Models\Rider;
use App\Http\Models\Shipment;
use App\Http\Models\V2Pickup\DwsPickupNote;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2PickupReceivedShipment;
use App\Http\Models\V2Pickup\V2PickupRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class DWSController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except('cancel');

        $this->middleware('Permission');
    }

    public function rider_receiving_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 491);
        $riders = Rider::select('id', 'name')->where('status', 1)->get();

        $default_date = Carbon::now();
        return view('admin.v2_pickups.rider_receiving_dws')->with(['riders' => $riders, 'default_date' => $default_date]);
    }

    public function rider_receiving_list(Request $request){

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 491);
        }

        $from = $request->get('search_date_from');

        $to = strval(Carbon::parse($request->get('search_date_to'))->addDay());

        $riders = DwsPickupNote::join('riders as r', 'r.id', '=', 'dws_pickup_notes.rider_id')
            ->select('dws_pickup_notes.pickup_note_id' , 'dws_pickup_notes.pickup_note_id as  note_id', 'r.name as rider', 'dws_pickup_notes.shipments_count', 'dws_pickup_notes.created_at as date', 'r.id as rider_id')
            ->whereBetween('dws_pickup_notes.created_at', [$from, $to]);

        if ($search_rider = $request->get('search_rider')) {
            $riders = $riders->where('r.id', '=', $search_rider);
        }

        $datatable = Datatables::of($riders)
            ->editColumn('note_id', function ($rider) {
                return '<button class="btn btn-sm btn-outline-info align-middle print "><i class="la la-lg la-print align-middle "></i> <span class="align-middle id">' . str_pad($rider->pickup_note_id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->addColumn('total_shipment', function ($data) {
                if ($data->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $data->shipments_count . '</button>';
                } else {
                    return 0;
                }
            });

        return $datatable->make(true);
    }

    public function total_dws_shipments(Request $request){
        $note_id = $request->note_id;
        $note = V2PickupNote::find($note_id);
        $bookings = array();
        if ($note) {
            $pickup_received_shipments = V2PickupReceivedShipment::join('dws_details', 'dws_details.shipment_id', '=', 'v2_pickup_received_shipments.shipment_id')->where('v2_pickup_received_shipments.pickup_note_id', $note_id)->pluck('dws_details.shipment_id')->toArray();

            foreach ($pickup_received_shipments as $shipment_id) {
                $bookings[] = Shipment::find($shipment_id)->tracking_number;
            }

            return ['status' => 0, 'success' => 'Booked Shipments', 'booked' => $bookings];

        } else {
            return ['status' => 0, 'success' => 'No Booked Shipments', 'booked' => false];
        }
    }
}
