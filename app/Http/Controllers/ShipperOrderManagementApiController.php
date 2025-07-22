<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Models\Shipment;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ShipperOrderManagementApiController extends Controller
{
   public  function order_list(Request $request)
   {
       if ($request->app_type == 2 ) {
           $shipments_summary = $this->shipments_summary($request->retail_shipper_id,$request->app_type);
           $order_list = $this->retail_shipper_list($request->retail_shipper_id);
       } else {
//           $shipments_summary = $this->shipments_summary($request->shipper_id,$request->app_type);
           $order_list = $this->shipper_order_list($request->shipper_id);
       }

       if ($order_list && $order_list->count() > 0) {
           return response()->json([
               'status' => 0,
               'message' => 'Success',
               'order_list' => $order_list,
               'shipments_summary' =>[]
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

        return Shipment::leftjoin('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftjoin('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->leftjoin('cities as oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('cities as dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftJoin('shipment_payment_status as sps', 'sps.id', '=', 'shipments.payment_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=', DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.verification = 1)'));
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'sj.status_reason_id')
            ->leftJoin('booking_types as bt', 'shipments.booking_type_id', '=', 'bt.id')
            ->whereBetween('shipments.created_at', [Carbon::now()->subMonths(8)->startOfMonth(), Carbon::now()->endOfDay()])
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
                'shipments.booking_type_id',
                'bt.booking_type'
            ]);
    }


    private function shipper_order_list($shipper_id)
    {

        return $this->commonShipmentQuery()
            ->leftjoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->addSelect('sm.mode as service_type','shipments.id')
            ->where('shipments.user_id', $shipper_id)
            ->orderBy('shipments.id', 'desc')
            ->cursorPaginate(20);
    }

    private function retail_shipper_list($retail_id)
    {
        return $this->commonShipmentQuery()
            ->leftjoin('retail_shipments as rs', 'rs.shipment_id', '=', 'shipments.id')
            ->leftjoin('retail_shipper_infos as rsi', 'rsi.id', '=', 'rs.shipper_account_no')
            ->leftjoin('retail_shipping_modes as sm', 'sm.id', '=', 'rs.shipping_mode')
            ->addSelect('rs.id','sm.name as service_type')
            ->where('rsi.id', $retail_id)
            ->orderBy('rs.id', 'desc')
            ->cursorPaginate(20);
    }

    private function shipments_summary_od($user_id,$app_type)
    {


        $last_start_day = Carbon::now()->subDays(6)->startOfDay();
        $last_end_day = Carbon::now()->subDays(6)->endOfDay();
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $shipment_status = [14,18,19,36,38,51,31,25,17];
        if ($app_type == 1):
            $shipment_status[] = 1;
        endif;

        // 1. Over all in process
        $over_all_in_process = Shipment::whereNotIn('shipper_status_id', $shipment_status);
//            ->whereBetween('pickup_date', [$startDate, $endDate]);

        if ($app_type == 2) {
            $over_all_in_process = $over_all_in_process
                ->join('retail_shipments', 'retail_shipments.shipment_id', '=', 'shipments.id')
                ->where('shipment_type', 2)
                ->where('retail_shipments.shipper_account_no', $user_id);
        } else {
            $over_all_in_process = $over_all_in_process->where('shipment_type', 1)
                ->where('user_id', $user_id);
        }
        $over_all_in_process = $over_all_in_process->count();

        // 2. Today bookings
        $today_bookings = Shipment::where('shipper_status_id', 1)
            ->whereBetween('pickup_date', [$todayStart,$todayEnd]);

        if($app_type == 2) {
            $today_bookings = $today_bookings->join('retail_shipments', 'retail_shipments.shipment_id', '=', 'shipments.id')
                ->where('shipment_type', 2)
                ->where('retail_shipments.shipper_account_no', $user_id);
        } else{
            $today_bookings =  $today_bookings->where('shipment_type', 1)
                ->where('user_id', $user_id);
        }
        $today_bookings = $today_bookings->count();

        // 3. Last day arrivals
        $last_day_arrivals = Shipment::whereIn('shipper_status_id', [2, 4])
            ->whereBetween('pickup_date', [ $last_start_day, $last_end_day ]);

        if($app_type == 2) {
            $last_day_arrivals = $last_day_arrivals->join('retail_shipments', 'retail_shipments.shipment_id', '=', 'shipments.id')
                ->where('shipment_type', 2)
                ->where('retail_shipments.shipper_account_no', $user_id);
        } else {
            $last_day_arrivals = $last_day_arrivals->where('shipment_type', 1)
                 ->where('user_id', $user_id);
        }

        $last_day_arrivals = $last_day_arrivals->count();

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

    private function shipments_summary($user_id,$app_type)
    {

        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $shipment_status = [14,18,19,36,38,51,31,25,17];
        if ($app_type == 1):
            $shipment_status[] = 1;
        endif;

        // 1. Over all in process
        $over_all_in_process = Shipment::whereNotIn('shipper_status_id', $shipment_status);
//            ->whereBetween('pickup_date', [$startDate, $endDate]);

        if ($app_type == 2) {
            $over_all_in_process = $over_all_in_process
                ->leftjoin('retail_shipments', 'retail_shipments.shipment_id', '=', 'shipments.id')
                ->where('shipment_type', 2)
                ->where('retail_shipments.shipper_account_no', $user_id);
        } else {
            $over_all_in_process = $over_all_in_process->where('shipment_type', 1)
                ->where('user_id', $user_id);
        }
        $over_all_in_process = $over_all_in_process->count();
        // 2. Today bookings
        $today_bookings = Shipment::where('shipments.shipper_status_id', 1)
            ->whereBetween('shipments.created_at', [$todayStart,$todayEnd]);

        if($app_type == 2) {
            $today_bookings = $today_bookings->leftjoin('retail_shipments', 'retail_shipments.shipment_id', '=', 'shipments.id')
                ->where('shipments.shipment_type', 2)
                ->where('retail_shipments.shipper_account_no', $user_id);
        } else{
            $today_bookings =  $today_bookings->where('shipments.shipment_type', 1)
                ->where('shipments.user_id', $user_id);
        }
        $today_bookings = $today_bookings->count();

        $last_6_day_summary = [];
        $dateRange = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $dateRange[] = $day->toDateString();

            $last_6_day_summary[$day->toDateString()] = [
                'arrivals' => 0,
                'delivered' => 0,
                'returns' => 0,
            ];
        }

// Determine base shipments
        $baseQuery = DB::table('shipments as s');

        if ($app_type == 2) {
            $baseQuery->leftjoin('retail_shipments as rs', 'rs.shipment_id', '=', 's.id')
                ->where('s.shipment_type', 2)
                ->where('rs.shipper_account_no', $user_id);
        } else {
            $baseQuery->where('s.shipment_type', 1)
                ->where('s.user_id', $user_id);
        }

// --- Arrivals ---
        $arrivals = $baseQuery->clone()
            ->leftjoin('shipments_journey as sj', 'sj.shipment_id', '=', 's.id')
            ->whereIn('sj.shipper_status_id', [2, 4])
            ->whereBetween('sj.created_at', [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()])
            ->select(
                DB::raw('DATE(sj.created_at) as day'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('day')
            ->get();

        foreach ($arrivals as $row) {
            if (isset($last_6_day_summary[$row->day])) {
                $last_6_day_summary[$row->day]['arrivals'] = $row->total;
            }
        }

// --- Delivered / Returned ---
        $subquery = DB::table('shipments_journey as sj1')
            ->select('sj1.shipment_id', DB::raw('MAX(sj1.id) as max_id'))
            ->whereBetween('sj1.created_at', [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()])
            ->groupBy('sj1.shipment_id');

        $journeyQuery = $baseQuery->clone()
            ->leftJoinSub($subquery, 'last_journeys', function ($join) {
                $join->on('s.id', '=', 'last_journeys.shipment_id');
            })
            ->leftjoin('shipments_journey as sj2', 'sj2.id', '=', 'last_journeys.max_id')
            ->whereBetween('sj2.created_at', [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()])
            ->whereIN('sj2.shipper_status_id', [14, 30, 36, 37,25])
            ->select(
                DB::raw('DATE(sj2.created_at) as day'),
                'sj2.shipper_status_id',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('day', 'sj2.shipper_status_id')
            ->get();

        foreach ($journeyQuery as $row) {
            if (!isset($last_6_day_summary[$row->day])) continue;

            if (in_array($row->shipper_status_id, [14, 30, 36, 37])) {
                $last_6_day_summary[$row->day]['delivered'] += $row->total;
            } elseif ($row->shipper_status_id == 25) {
                $last_6_day_summary[$row->day]['returns'] += $row->total;
            }
        }


        return [
            'over_all_in_process' => $over_all_in_process,
            'today_bookings' => $today_bookings,
            'last_6_day_summary' => $last_6_day_summary,
        ];
    }


    public function shipment_air_waybill(Request $request)
    {

            $tracking_number = $request->tracking_number;
            $user_id = $request->shipper_id;

            $shipment = Shipment::where('tracking_number', $tracking_number)->where('user_id',$user_id)->first();
            if($shipment && $request->app_type == 1) {
                $air_waybill = ShipperShipmentBookController::air_waybill(4, $shipment->user_id, [$shipment->id]);
                $pdf = SnappyPdf::loadHTML($air_waybill);

                $filename = 'air_waybill' . '.pdf';
                return $pdf->setOption('enable-local-file-access', true)->download($filename);
            }

            return response()->json(['status' => 1 , 'message' => 'Shipment not found!']);

    }


}
