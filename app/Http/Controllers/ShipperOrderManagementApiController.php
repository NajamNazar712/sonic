<?php

namespace App\Http\Controllers;

use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipperOrderManagementApiController extends Controller
{
   public  function order_list(Request $request)
   {

       $order_list = Shipment::join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
           ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
           ->join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
           ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
           ->join('cities as dc', 'shipments.consignee_city_id', '=', 'dc.id')
           ->leftJoin('shipment_payment_status as sps', 'sps.id', '=', 'shipments.payment_status_id')
           ->leftJoin('shipments_journey as sj', function ($join) {
               $join->on('sj.shipment_id', '=', 'shipments.id')
                   ->where('sj.id', '=', DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.verification = 1)'));
           })
           ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'sj.status_reason_id')
//           ->select([
//               'shipments.tracking_number',
//               'shipments.order_id',
//               'shipments.booked_by',
//               'shipments.shipping_mode_id',
//               'sm.mode as service_type',
//               'shipments.shipper_status_id',
//               'ss.name as status',
//               'sj.status_reason_id',
//               'ssr.name as reason',
//               'shipments.payment_status_id',
//               'sps.name as payment_status',
//               'usi.city_id as origin_id',
//               'oc.name as origin',
//               'shipments.consignee_city_id',
//               'dc.name as destination',
//               'shipments.consignee_name',
//               'shipments.consignee_phone_number_1',
//               'shipments.amount as collection_amount',
//               'shipments.pickup_date as booking_date',
//               'sj.remarks as cancellation_remarks',
//           ])
           ->where('shipments.user_id',$request->shipper_id)
           ->groupBy('shipments.id')
           ->orderBy('shipments.id', 'desc')
           ->count();
dd($order_list);
       if($order_list->isNotEmpty()) {
           return response()->json(['status' => 0 , 'message' => 'Success' ,'order_list'=>$order_list]);
       }
       return response()->json(['status' => 1 , 'message' => 'Shipments Order not found!']);



//       with('shipping_mode','status_shipper','payment_status')
   }
}
