<?php

namespace App\Http\Controllers;

use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipperOrderManagementApiController extends Controller
{
   public  function order_list(Request $request)
   {
       if ($request->app_type == 2 ) {
           $shipments_summary = $this->shipments_summary($request->retail_shipper_id,$request->app_type);
           $order_list = $this->retail_shipper_list($request->retail_shipper_id);
       } else {
           $shipments_summary = $this->shipments_summary($request->shipper_id,$request->app_type);
           $order_list = $this->shipper_order_list($request->shipper_id);
       }

       if ($order_list && $order_list->count() > 0) {
           return response()->json([
               'status' => 0,
               'message' => 'Success',
               'order_list' => $order_list,
               'shipments_summary' =>$shipments_summary
           ]);
       }

       return response()->json([
           'status' => 1,
           'message' => 'Shipments Order not found!',
           'shipments_summary' =>$shipments_summary
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

    private function shipments_summary($user_id,$app_type)
    {

        $startDate = Carbon::now()->subMonths(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $last_start_day =  Carbon::yesterday()->startOfDay();
        $last_end_day =  Carbon::yesterday()->endOfDay();
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $shipment_status = [14, 25, 17];
        if ($app_type == 1):
            $shipment_status[] = 1;
        endif;

        // 1. Over all in process
        $over_all_in_process = Shipment::whereNotIn('shipper_status_id', $shipment_status)
            ->where('user_id', $user_id)
            ->whereBetween('pickup_date', [$startDate, $endDate])
            ->count();

        // 2. Today bookings
        $today_bookings = Shipment::where('shipper_status_id', 1)
            ->where('user_id', $user_id)
            ->whereBetween('pickup_date', [$todayStart,$todayEnd])
            ->count();

        // 3. Last day arrivals
        $last_day_arrivals = Shipment::whereIn('shipper_status_id', [2, 4])
            ->where('user_id', $user_id)
            ->whereBetween('pickup_date', [ $last_start_day, $last_end_day ])
            ->count();

        // 4. Last day delivered
        $last_day_delivered = Shipment::where('shipper_status_id', 14)
            ->where('user_id', $user_id)
            ->whereBetween('pickup_date', [ $last_start_day, $last_end_day ])
            ->count();

        // 5. Last day returns
        $last_day_returns = Shipment::where('shipper_status_id', 25)
            ->where('user_id', $user_id)
            ->whereBetween('pickup_date', [ $last_start_day, $last_end_day ])
            ->count();

        $shipment_summary = [
            'over_all_in_process' =>$over_all_in_process,
            'today_bookings' =>$today_bookings,
            'last_day_arrivals' =>$last_day_arrivals,
            'last_day_delivered' =>$last_day_delivered,
            'last_day_returns' =>$last_day_returns,
        ];

        return $shipment_summary;
    }

}
