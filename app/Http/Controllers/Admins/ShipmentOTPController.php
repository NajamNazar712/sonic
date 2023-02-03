<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\RiderDelivery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Auth;
class ShipmentOTPController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function otp_history_index(){

        ActivityTrailController::createActivityTrailLog(Auth::id(),625);

        return view('admin.delivery.otp.history');
    }

    public function otp_history_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),626);
        }
        $admins = RiderDelivery::leftJoin('shipments', 'shipments.id', '=', 'rider_deliveries.shipment_id')
            ->leftJoin('riders as r', 'r.id', '=', 'rider_deliveries.rider_id')
            ->leftJoin('shipment_otps as so', 'so.shipment_id', '=', 'rider_deliveries.shipment_id')
            ->leftJoin('shipment_otp_verifications as sov', 'sov.shipment_id', '=', 'so.shipment_id')
            ->leftJoin('shipment_status as ss', 'ss.id', '=', 'rider_deliveries.rider_status_id')
            ->select('rider_deliveries.created_at as date', 'rider_deliveries.delivery_note_id', 'rider_deliveries.rider_status_id', 'rider_deliveries.otp_entered', 'shipments.tracking_number', 'r.name as rider_name', 'ss.name as purpose', 'so.otp as consignee_otp', 'so.dbf_otp', 'sov.via_dbf_otp');

        $admins = $admins->where(function ($query) {
            $query->where(function ($sub_query) {
                $sub_query->where('rider_deliveries.rider_status_id', 12)
                    ->where('rider_deliveries.rider_status_reason_id', 8)
                    ->where('rider_deliveries.otp_entered', 1);
            })
                ->orWhere(function ($sub_query) {
                    $sub_query->where('rider_deliveries.rider_status_id', 14);
                });
        });

        $datatable = Datatables::of($admins)
        ->addColumn('tracking_number_link', function ($shipments) {
            $route = route('admin.tracking.index');
            return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
        })
        ->addColumn('otp', function ($shipment){
            if($shipment->rider_status_id == 14){
                if($shipment->via_dbf_otp){
                    return $shipment->dbf_otp;
                }
                else{
                    return $shipment->consignee_otp;
                }
            }
            else{
                return $shipment->consignee_otp;
            }
        });
        return $datatable->make(true);
    }
}
