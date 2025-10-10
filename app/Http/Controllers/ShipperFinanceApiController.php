<?php

namespace App\Http\Controllers;

use App\Http\Models\DonePayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ShipperFinanceApiController extends Controller
{
    public function payment_list(Request $request,$payment_id = null)
    {
        $payment_id = $request->input('id',$payment_id);

        if ($request->app_type == 2 ) {
            $payment_list = $this->retail_payment_list_v2($request->retail_shipper_id,$payment_id);
        } else {
            $payment_list = $this->shipper_payment_list_v2($request->shipper_id,$payment_id);
        }
        if(!empty($payment_list)) {
            return response()->json(['status' => 0 , 'message' => 'Success' ,'payment_list'=>$payment_list]);
        }
        return response()->json(['status' => 1 , 'message' => 'Invoice Payments not found!']);

    }

    private function shipper_payment_list_v1($shipper_id, $payment_id = null)
{
    $payments = DB::table('done_payments')
        ->leftJoin('users as u', 'done_payments.user_id', '=', 'u.id')
        ->leftJoin('cities as c', 'u.city_id', '=', 'c.id')
        ->leftJoin('user_bank_infos as ubi', function ($join) {
            $join->on('ubi.id', '=', 'done_payments.user_bank_info_id');
        })
        ->leftJoin('user_bank_infos as ubi_default', function ($join) {
            $join->on('ubi_default.user_id', '=', 'u.id')
                ->where('ubi_default.default_bank', DB::raw(1));
        })
        ->leftJoin('banks_lists as ub', function ($join) {
            $join->on('ub.id', '=', DB::raw('COALESCE(ubi.bank_name, ubi_default.bank_name)'));
        })
        ->leftJoin('banks_lists as b', 'done_payments.company_bank_id', '=', 'b.id')
        ->select(
            'done_payments.*',
            'u.name as shipper', 'u.phone', 'u.phone2', 'u.address', 'u.id as user_id',
            'c.name as city',
            'ub.name as bank',
            'b.name as company_bank'
        )
        ->where('done_payments.user_id', $shipper_id)
        ->when($payment_id, fn($q) => $q->where('done_payments.id', $payment_id))
        ->where('done_payments.created_at', '>=', Carbon::now()->subMonths(12)->startOfMonth())
        ->orderByDesc('done_payments.id')
        ->cursorPaginate(20);

    $paymentIds = $payments->pluck('id')->toArray();

    $shipments = DB::table('done_payment_shipments as dps')
        ->join('shipments as s', 's.id', '=', 'dps.shipment_id')
        ->select(
            'dps.done_payment_id',
            's.tracking_number',
            'dps.type',
            'dps.amount',
            'dps.charges',
            'dps.gst',
            'dps.wht',
            'dps.cod_sst',
            'dps.payable'
        )
        ->whereIn('dps.done_payment_id', $paymentIds)
        ->get()
        ->groupBy('done_payment_id');

    $finalResults = [];

    foreach ($payments as $payment) {
        $paymentId = $payment->id;
        $shipmentGroup = $shipments[$paymentId] ?? collect();

        $delivered = [];
        $returned = [];
        $adjusted = [];
        $arrival = [];

        $totalAmount = 0;
        $totalCharges = 0;
        $totalGst = 0;
        $totalWht = 0;
        $totalCodSst = 0;
        $totalPayable = 0;

        foreach ($shipmentGroup as $s) {
            switch ($s->type) {
                case 0: $delivered[] = trim($s->tracking_number); break;
                case 1: $returned[] = trim($s->tracking_number); break;
                case 2: $adjusted[] = trim($s->tracking_number); break;
                case 3: $arrival[] = trim($s->tracking_number); break;
            }

            $totalAmount += floatval($s->amount);
            $totalCharges += floatval($s->charges);
            $totalGst += floatval($s->gst);
            $totalWht += floatval($s->wht);
            $totalCodSst += floatval($s->cod_sst);
            $totalPayable += floatval($s->payable);
        }

        $finalResults[] = [
            'delivered_tracking_numbers' => $delivered,
            'return_tracking_numbers' => $returned,
            'adjusted_tracking_numbers' => $adjusted,
            'arrival_tracking_numbers' => $arrival,
            'id' => $paymentId,
            'payment_id' => $paymentId,
            'user_id' => $payment->user_id,
            'shipper' => $payment->shipper,
            'city' => $payment->city,
            'phone' => $payment->phone,
            'phone2' => $payment->phone2,
            'address' => $payment->address,
            'total_shipments' => $shipmentGroup->count(),
            'delivered_shipments' => count($delivered),
            'returned_shipments' => count($returned),
            'adjusted_shipments' => count($adjusted),
            'arrival_shipments' => count($arrival),
            'total_amount' => round($totalAmount, 2),
            'total_charges' => round($totalCharges, 2),
            'total_gst' => round($totalGst, 2),
            'total_wht' => round($totalWht, 2),
            'total_cod_sst' => round($totalCodSst, 2),
            'total_payable' => round($totalPayable, 2),
            'ibft_charges' => $payment->ibft_charges,
            'bank' => $payment->bank,
            'reference_number' => $payment->reference_number,
            'done_at' => $payment->created_at,
            'company_bank' => $payment->company_bank,
            'status' => $payment->status,
        ];
    }

    return [
        'data' => array_values($finalResults),
        'path' => $payments->path(),
        'per_page' => $payments->perPage(),
        'next_cursor' => $payments->nextCursor()?->encode(),
        'next_page_url' => $payments->nextPageUrl(),
        'prev_cursor' => $payments->previousCursor()?->encode(),
        'prev_page_url' => $payments->previousPageUrl(),
    ];
}
    private function shipper_payment_list_v2($shipper_id, $payment_id = null)
    {
        $payments = DB::table('done_payments')
            ->leftJoin('users as u', 'done_payments.user_id', '=', 'u.id')
            ->leftJoin('cities as c', 'u.city_id', '=', 'c.id')
            ->leftJoin('user_bank_infos as ubi', function ($join) {
                $join->on('ubi.id', '=', 'done_payments.user_bank_info_id');
            })
            ->leftJoin('user_bank_infos as ubi_default', function ($join) {
                $join->on('ubi_default.user_id', '=', 'u.id')
                    ->where('ubi_default.default_bank', DB::raw(1));
            })
            ->leftJoin('banks_lists as ub', function ($join) {
                $join->on('ub.id', '=', DB::raw('COALESCE(ubi.bank_name, ubi_default.bank_name)'));
            })
            ->leftJoin('banks_lists as b', 'done_payments.company_bank_id', '=', 'b.id')
            ->leftJoin('done_payment_shipments as dps', 'dps.done_payment_id', '=', 'done_payments.id')
            ->select(
                'done_payments.id',
                'done_payments.id as payment_id',
                'u.id as user_id',
                'u.name as shipper',
                'u.phone',
                'u.phone2',
                'u.address',
                'c.name as city',
                'ub.name as bank',
                'b.name as company_bank',
                'done_payments.ibft_charges',
                'done_payments.reference_number',
                'done_payments.created_at as done_at',
                'done_payments.status',
                DB::raw('COUNT(dps.id) as total_shipments'),
                DB::raw('SUM(CASE WHEN dps.type = 0 THEN 1 ELSE 0 END) as delivered_shipments'),
                DB::raw('SUM(CASE WHEN dps.type = 1 THEN 1 ELSE 0 END) as returned_shipments'),
                DB::raw('SUM(CASE WHEN dps.type = 2 THEN 1 ELSE 0 END) as adjusted_shipments'),
                DB::raw('SUM(CASE WHEN dps.type = 3 THEN 1 ELSE 0 END) as arrival_shipments'),
                DB::raw('ROUND(SUM(dps.amount), 2) as total_amount'),
                DB::raw('ROUND(SUM(dps.charges), 2) as total_charges'),
                DB::raw('ROUND(SUM(dps.gst), 2) as total_gst'),
                DB::raw('ROUND(SUM(dps.wht), 2) as total_wht'),
                DB::raw('ROUND(SUM(dps.cod_sst), 2) as total_cod_sst'),
                DB::raw('ROUND(SUM(dps.payable), 2) as total_payable')
            )
            ->where('done_payments.user_id', $shipper_id)
            ->when($payment_id, fn($q) => $q->where('done_payments.id', $payment_id))
            ->where('done_payments.created_at', '>=', Carbon::now()->subMonths(12)->startOfMonth())
            ->groupBy(
                'done_payments.id',
            )
            ->orderByDesc('done_payments.id')
            ->cursorPaginate(20);

        return [
            'data' => $payments->items(),
            'path' => $payments->path(),
            'per_page' => $payments->perPage(),
            'next_cursor' => $payments->nextCursor()?->encode(),
            'next_page_url' => $payments->nextPageUrl(),
            'prev_cursor' => $payments->previousCursor()?->encode(),
            'prev_page_url' => $payments->previousPageUrl(),
        ];
    }

    private function retail_payment_list_v1($retail_id, $payment_id = null)
    {
        $payments = DB::table('retail_done_payments')
            ->leftJoin('retail_shipper_infos as rsi', 'retail_done_payments.user_id', '=', 'rsi.id')
            ->leftJoin('cities as c', 'rsi.city_id', '=', 'c.id')
            ->leftJoin('banks_lists as ub', 'ub.id', '=', 'rsi.bank_id')
            ->select(
                'retail_done_payments.*',
                'rsi.id as user_id',
                'rsi.shipper_name as shipper',
                'rsi.shipper_phone_no as phone',
                'rsi.shipper_address as address',
                'c.name as city',
                'ub.name as company_bank'
            )
            ->where('retail_done_payments.user_id', $retail_id)
            ->when($payment_id, fn($q) => $q->where('retail_done_payments.id', $payment_id))
            ->where('retail_done_payments.created_at', '>=', Carbon::now()->subMonths(12)->startOfMonth())
            ->orderByDesc('retail_done_payments.id')
            ->cursorPaginate(20);

        $paymentIds = $payments->pluck('id')->toArray();

        $shipments = DB::table('retail_done_payment_shipments as rdps')
            ->join('retail_shipments as rs', 'rs.shipment_id', '=', 'rdps.shipment_id')
            ->join('shipments as s', 's.id', '=', 'rs.shipment_id')
            ->select(
                'rdps.retail_done_payment_id',
                's.tracking_number',
                'rdps.type',
                'rdps.amount',
                'rdps.charges',
                'rs.gst',
                'rs.wht',
                'rs.cod_sst',
                'rdps.payable'
            )
            ->whereIn('rdps.retail_done_payment_id', $paymentIds)
            ->get()
            ->groupBy('retail_done_payment_id');

        $finalResults = [];

        foreach ($payments as $payment) {
            $paymentId = $payment->id;
            $shipmentGroup = $shipments[$paymentId] ?? collect();

            $delivered = [];
            $returned = [];
            $adjusted = [];
            $arrival = [];

            $totalAmount = 0;
            $totalCharges = 0;
            $totalGst = 0;
            $totalWht = 0;
            $totalCodSst = 0;
            $totalPayable = 0;

            foreach ($shipmentGroup as $s) {
                switch ($s->type) {
                    case 0: $delivered[] = trim($s->tracking_number); break;
                    case 1: $returned[] = trim($s->tracking_number); break;
                    case 2: $adjusted[] = trim($s->tracking_number); break;
                    case 3: $arrival[] = trim($s->tracking_number); break;
                }

                $totalAmount += floatval($s->amount);
                $totalCharges += floatval($s->charges);
                $totalGst += floatval($s->gst);
                $totalWht += floatval($s->wht);
                $totalCodSst += floatval($s->cod_sst);
                $totalPayable += floatval($s->payable);
            }

            $finalResults[] = [
                'delivered_tracking_numbers' => $delivered,
                'return_tracking_numbers' => $returned,
                'adjusted_tracking_numbers' => $adjusted,
                'arrival_tracking_numbers' => $arrival,
                'id' => $paymentId,
                'payment_id' => $paymentId,
                'user_id' => $payment->user_id,
                'shipper' => $payment->shipper,
                'city' => $payment->city,
                'phone' => $payment->phone,
                'address' => $payment->address,
                'total_shipments' => $shipmentGroup->count(),
                'delivered_shipments' => count($delivered),
                'returned_shipments' => count($returned),
                'adjusted_shipments' => count($adjusted),
                'arrival_shipments' => count($arrival),
                'total_amount' => round($totalAmount, 2),
                'total_charges' => round($totalCharges, 2),
                'total_gst' => round($totalGst, 2),
                'total_wht' => round($totalWht, 2),
                'total_cod_sst' => round($totalCodSst, 2),
                'total_payable' => round($totalPayable, 2),
                'ibft_charges' => $payment->ibft_charges,
                'bank' => $payment->bank ?? null,
                'reference_number' => $payment->reference_number,
                'done_at' => $payment->created_at,
                'company_bank' => $payment->company_bank,
                'status' => $payment->status,
            ];
        }

        return [
            'data' => array_values($finalResults),
            'path' => $payments->path(),
            'per_page' => $payments->perPage(),
            'next_cursor' => $payments->nextCursor()?->encode(),
            'next_page_url' => $payments->nextPageUrl(),
            'prev_cursor' => $payments->previousCursor()?->encode(),
            'prev_page_url' => $payments->previousPageUrl(),
        ];
    }
    private function retail_payment_list_v2($retail_id, $payment_id = null)
    {
        $payments = DB::table('retail_done_payments')
            ->leftJoin('retail_shipper_infos as rsi', 'retail_done_payments.user_id', '=', 'rsi.id')
            ->leftJoin('cities as c', 'rsi.city_id', '=', 'c.id')
            ->leftJoin('banks_lists as ub', 'ub.id', '=', 'rsi.bank_id')
            ->leftJoin('retail_done_payment_shipments as rdps', 'rdps.retail_done_payment_id', '=', 'retail_done_payments.id')
            ->leftJoin('retail_shipments as rs', 'rs.shipment_id', '=', 'rdps.shipment_id')
            ->leftJoin('shipments as s', 's.id', '=', 'rs.shipment_id')
            ->select(
                'retail_done_payments.id',
                'retail_done_payments.id as payment_id',
                'rsi.id as user_id',
                'rsi.shipper_name as shipper',
                'rsi.shipper_phone_no as phone',
                'rsi.shipper_address as address',
                'c.name as city',
                'ub.name as company_bank',
                'retail_done_payments.ibft_charges',
                'retail_done_payments.reference_number',
                'retail_done_payments.created_at as done_at',
                'retail_done_payments.status',
                DB::raw('COUNT(rdps.id) as total_shipments'),
                DB::raw('SUM(CASE WHEN rdps.type = 0 THEN 1 ELSE 0 END) as delivered_shipments'),
                DB::raw('SUM(CASE WHEN rdps.type = 1 THEN 1 ELSE 0 END) as returned_shipments'),
                DB::raw('SUM(CASE WHEN rdps.type = 2 THEN 1 ELSE 0 END) as adjusted_shipments'),
                DB::raw('SUM(CASE WHEN rdps.type = 3 THEN 1 ELSE 0 END) as arrival_shipments'),
                DB::raw('ROUND(SUM(rdps.amount), 2) as total_amount'),
                DB::raw("'0' as total_gst"),
                DB::raw('ROUND(SUM(rs.wht), 2) as total_wht'),
                DB::raw('ROUND(SUM(rs.cod_sst), 2) as total_cod_sst'),
                DB::raw('ROUND(SUM(rdps.payable), 2) as total_payable')
            )
            ->where('retail_done_payments.user_id', $retail_id)
            ->when($payment_id, fn($q) => $q->where('retail_done_payments.id', $payment_id))
            ->where('retail_done_payments.created_at', '>=', Carbon::now()->subMonths(12)->startOfMonth())
            ->groupBy(
                'retail_done_payments.id'
            )
            ->orderBy('retail_done_payments.id','desc')
            ->cursorPaginate(20);

        return [
            'data' => $payments->items(),
            'path' => $payments->path(),
            'per_page' => $payments->perPage(),
            'next_cursor' => $payments->nextCursor()?->encode(),
            'next_page_url' => $payments->nextPageUrl(),
            'prev_cursor' => $payments->previousCursor()?->encode(),
            'prev_page_url' => $payments->previousPageUrl(),
        ];
    }

    public function GetPaymentShipments(Request $request)
    {
        $paymentId = $request->input('payment_id');
        $type = $request->input('type', 0);
        $search = $request->input('search', null);
        $appType = $request->input('app_type', 1); // 1 = corporate, 2 = retail
        if (!$paymentId || $type === null) {
            return response()->json(['error' => 'payment_id and type are required'], 400);
        }

        if ($appType == 2) {
            // Retail payments
            $shipments = DB::table('retail_done_payment_shipments as rdps')
                ->join('retail_shipments as rs', 'rs.shipment_id', '=', 'rdps.shipment_id')
                ->join('shipments as s', 's.id', '=', 'rs.shipment_id')
                ->where('rdps.retail_done_payment_id', $paymentId)
                ->where('rdps.type', $type)
                ->when($search, function ($q) use ($search) {
                    $q->where('s.tracking_number', 'like', "%{$search}%");
                })
                ->select('s.id', 's.tracking_number','rdps.total_charges as total_charges' )
                ->orderBy('s.id', 'desc')
                ->cursorPaginate(500, ['*'], 'cursor');
        } else {
            // Corporate payments
            $shipments = DB::table('done_payment_shipments as dps')
                ->join('shipments as s', 's.id', '=', 'dps.shipment_id')
                ->where('dps.done_payment_id', $paymentId)
                ->where('dps.type', $type)
                ->when($search, function ($q) use ($search) {
                    $q->where('s.tracking_number', 'like', "%{$search}%");
                })
                ->select('s.id', 's.tracking_number', 'dps.charges as total_charges'  )
                ->orderBy('s.id', 'desc')
                ->cursorPaginate(500, ['*'], 'cursor');
        }

        return response()->json($shipments);
    }







}
