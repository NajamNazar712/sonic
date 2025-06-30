<?php

namespace App\Http\Controllers;

use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipperOrderManagementApiController extends Controller
{
   public  function order_list(Request $request)
   {

       if ($request->app_type == 2 ) {
           $order_list = $this->retail_shipper_list($request->retail_shipper_id);
       } else {
           $order_list = $this->shipper_order_list($request->shipper_id);
       }

       if ($order_list && $order_list->count() > 0) {
           return response()->json([
               'status' => 0,
               'message' => 'Success',
               'order_list' => $order_list
           ]);
       }

       return response()->json([
           'status' => 1,
           'message' => 'Shipments Order not found!'
       ]);

   }

    private function commonShipmentQuery()
    {

        return Shipment::join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities as dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftJoin('shipment_payment_status as sps', 'sps.id', '=', 'shipments.payment_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=', DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.verification = 1)'));
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'sj.status_reason_id')
            ->select([
                'shipments.id as shipment_id',
                'shipments.tracking_number',
                'shipments.order_id',
                'shipments.booked_by',
                'shipments.shipping_mode_id',
                // 'sm.mode as service_type', to be added later per context
                'shipments.shipper_status_id',
                'ss.name as status',
                'sj.status_reason_id',
                'ssr.name as reason',
                'shipments.payment_status_id',
                'sps.name as payment_status',
                'usi.city_id as origin_id',
                'oc.name as origin',
                'shipments.consignee_city_id',
                'dc.name as destination',
                'shipments.consignee_name',
                'shipments.consignee_phone_number_1',
                'shipments.amount as collection_amount',
                'shipments.pickup_date as booking_date',
                'sj.remarks as cancellation_remarks',
            ]);
    }


    private function shipper_order_list($shipper_id)
    {

        return $this->commonShipmentQuery()
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->addSelect('sm.mode as service_type','shipments.id')
            ->where('shipments.user_id', $shipper_id)
            ->orderBy('shipments.id', 'desc')
            ->cursorPaginate(20);
    }

    private function retail_shipper_list($retail_id)
    {
        return $this->commonShipmentQuery()
            ->join('retail_shipments as rs', 'rs.shipment_id', '=', 'shipments.id')
            ->join('retail_shipper_infos as rsi', 'rsi.id', '=', 'rs.shipper_account_no')
            ->join('retail_shipping_modes as sm', 'sm.id', '=', 'rs.shipping_mode')
            ->addSelect('rs.id','sm.name as service_type')
            ->where('rsi.id', $retail_id)
            ->orderBy('rs.id', 'desc')
            ->cursorPaginate(20);
    }

}
