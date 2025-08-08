<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Models\Shipment;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ShipperOrderManagementApiController extends Controller
{
   public  function order_list(Request $request)
   {
       $tracking_number = $request->input('tracking_number');
       $shipper_statuses = [];
       if(in_array($request->status_id,[1,3,14,25,17])) {

            if($request->status_id == 1) {
               //booked
               $shipper_statuses=[1,19];
           }
            else if($request->status_id == 3) {
               //in process
               $shipper_statuses=[2,3,4,5,6,7,8,9,10,11,12,13,15,44,49,50,52,53,54,55,58,59,61,62,63,64,65,66,67,68,151,152];
           } else if($request->status_id == 14) {
               //delivered
               $shipper_statuses=[14,26,27,28,29,30,31,32,33,34,35,36,37,38,45,46,56,69,70,71,72,73,74 ];
           }
           else if($request->status_id == 17) {
               //cancelled
               $shipper_statuses=[17,18,51];
           }
           else if($request->status_id == 25) {
               //return
               $shipper_statuses=[20,21,22,23,24,25,47,48,57,60,75,76,77];
           }
       }

       if ($request->app_type == 2 ) {
           $order_list = $this->retail_shipper_list($request->retail_shipper_id,$tracking_number,$shipper_statuses);
       } else {
           $order_list = $this->shipper_order_list($request->shipper_id,$tracking_number,$shipper_statuses);
       }

       if ($order_list && $order_list->count() > 0) {
           return response()->json([
               'status' => 0,
               'message' => 'Success',
               'order_list' => $order_list,
           ]);
       }

       return response()->json([
           'status' => 1,
           'message' => 'Shipments Order not found!',
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
            ->whereBetween('shipments.created_at', [Carbon::now()->subMonths(12)->startOfMonth(), Carbon::now()->endOfDay()])
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


    private function shipper_order_list($shipper_id,$tracking_number,$shipper_statuses)
    {

        $shipper_order_list = $this->commonShipmentQuery()
            ->leftjoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->addSelect('sm.mode as service_type','shipments.id')
            ->where('shipments.user_id', $shipper_id);

        if ($tracking_number) {
            $shipper_order_list->where('shipments.tracking_number', $tracking_number);
        }

        if (!empty($shipper_statuses)) {
            $shipper_order_list->whereIn('shipments.shipper_status_id', $shipper_statuses);
        }

        return $shipper_order_list->orderBy('shipments.id', 'desc')->cursorPaginate(20);

    }

    private function retail_shipper_list($retail_id,$tracking_number,$shipper_statuses)
    {
        $retail_shipper_list = $this->commonShipmentQuery()
            ->leftjoin('retail_shipments as rs', 'rs.shipment_id', '=', 'shipments.id')
            ->leftjoin('retail_shipper_infos as rsi', 'rsi.id', '=', 'rs.shipper_account_no')
            ->leftjoin('retail_shipping_modes as sm', 'sm.id', '=', 'rs.shipping_mode')
            ->addSelect('rs.id','sm.name as service_type')
            ->where('rsi.id', $retail_id);

            if ($tracking_number) {
                $retail_shipper_list->where('shipments.tracking_number', $tracking_number);
            }

            if (!empty($shipper_statuses)) {
                $retail_shipper_list->whereIn('shipments.shipper_status_id', $shipper_statuses);
            }

            return $retail_shipper_list->orderBy('rs.id', 'desc')->cursorPaginate(20);
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

    public function shipments_summary(Request $request)
    {
        if ($request->has('status_id') && !in_array($request->status_id, [2, 14, 25])) {

            return response()->json(['status' => 1, 'message' => 'status not found!']);
        }

        $app_type = $request->app_type;
        $user_id = $app_type == 2 ? $request->retail_shipper_id : $request->shipper_id;
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();

        $process_and_booking = (int)$request->input('process_and_booking', 1);
        $today_bookings = 0;
        $over_all_in_process = 0;
        $exclude_statuses = [14, 18, 19, 36, 38, 51, 31, 25, 17];

        if ($process_and_booking === 1) {
            // Common where clauses for both types
            $conditions = function ($query) use ($user_id, $app_type) {
                if ($app_type == 2) {
                    $query->leftJoin('retail_shipments', 'retail_shipments.shipment_id', '=', 'shipments.id')
                        ->where('retail_shipments.shipper_account_no', $user_id);
                } else {
                    $query->where('shipments.user_id', $user_id);
                }
            };

            // Count of all in-process shipments (excluding certain statuses)
            $over_all_in_process = DB::table('shipments')
                ->when(true, $conditions)
                ->whereNotIn('shipments.shipper_status_id', $exclude_statuses)
                ->where('shipments.created_at', '>=', $startDate)
                ->count();

            // Count of bookings for today with shipper_status_id = 1
            $today_bookings = DB::table('shipments')
                ->when(true, $conditions)
                ->where('shipments.shipper_status_id', 1)
                ->where('shipments.created_at','>=', Carbon::now()->startOfDay())
                ->count();
        }


        $baseQuery = DB::table('shipments as s');
        $minId = DB::table('shipments_journey')
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->min('id');

        if ($app_type == 2) {
            $baseQuery->leftjoin('retail_shipments as rs', 'rs.shipment_id', '=', 's.id')
                ->where('s.shipment_type', 2)
                ->where('rs.shipper_account_no', $user_id);
        } else {
            $baseQuery->where('s.user_id', $user_id);
        }

        $arrival_flag = !$request->has('status_id') || $request->status_id == 2;
        $delivered_return_flag = !$request->has('status_id') || in_array($request->status_id, [14, 25]);

        $statuses_arrival = [2, 4];
        $statuses_delivered = [14, 30, 36, 37];
        $statuses_returned = [25];

// Step 1: Prepare last 7 days data structure
        $grouped = [];
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->toDateString();
            $grouped[$day] = ['arrivals' => 0, 'delivered' => 0, 'returns' => 0];
            $last7Days[] = $day;
        }

// Step 2: Get journey data
        $query = $baseQuery->clone()
            ->join('shipments_journey as sj2', function ($join) use ($minId, $request) {
                $join->on('sj2.shipment_id', '=', 's.id');
                if ($request->status_id == 14) {
                    $join->whereIn('sj2.shipper_status_id', [14, 30, 36, 37]);
                } elseif ($request->status_id == 25) {
                    $join->whereIn('sj2.shipper_status_id', [25]);
                }elseif ($request->status_id == 2) {
                    $join->whereIn('sj2.shipper_status_id', [2,4]);
                } elseif (!$request->has('status_id')) {
                    $join->whereIn('sj2.shipper_status_id', [2, 4, 14, 30, 36, 37, 25]);
                }
                $join->where('sj2.id', '>=', $minId);
            })
            ->select(
                DB::raw('DATE(sj2.created_at) as day'),
                'sj2.id as journey_id',
                'sj2.shipper_status_id',
                's.id as shipment_id'
            )
            ->get();

// Step 3: Group journey data by shipment and day
        $journeysPerShipment = [];
        foreach ($query as $row) {
            $shipmentId = $row->shipment_id;
            $day = $row->day;

            if (!in_array($day, $last7Days)) {
                continue; // Skip dates not in the last 7 days
            }

            if (!isset($journeysPerShipment[$shipmentId][$day])) {
                $journeysPerShipment[$shipmentId][$day] = [];
            }

            $journeysPerShipment[$shipmentId][$day][] = $row;
        }

// Step 4: Process each shipment per day
        foreach ($journeysPerShipment as $shipmentJourneysPerDay) {
            foreach ($shipmentJourneysPerDay as $day => $journeys) {
                $hasArrival = false;
                $latestDeliveryOrReturn = null;

                foreach ($journeys as $j) {
                    $status_id = $j->shipper_status_id;

                    // Check for arrival
                    if (in_array($status_id, $statuses_arrival)) {
                        $hasArrival = true;
                    }

                    // Check for latest delivery or return
                    if (in_array($status_id, $statuses_delivered) || in_array($status_id, $statuses_returned)) {
                        if (!$latestDeliveryOrReturn || $j->journey_id > $latestDeliveryOrReturn->journey_id) {
                            $latestDeliveryOrReturn = $j;
                        }
                    }
                }

                // Count arrival if present
                if ($hasArrival) {
                    $grouped[$day]['arrivals']++;
                }

                // Count latest delivery or return
                if ($latestDeliveryOrReturn) {
                    $latestStatusId = $latestDeliveryOrReturn->shipper_status_id;

                    if (in_array($latestStatusId, $statuses_returned)) {
                        $grouped[$day]['returns']++;
                    } else {
                        $grouped[$day]['delivered']++;
                    }
                }
            }
        }

// Step 5: Final Response
        $response = [
            'status' => 0,
            'message' => 'Success',
            'shipments_summary' => [
                'over_all_in_process' => $over_all_in_process,
                'today_bookings' => $today_bookings,
                'last_6_day_summary' => $grouped,
            ]
        ];


        return response()->json($response);


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
