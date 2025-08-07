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
            $payment_list = $this->retail_payment_list($request->retail_shipper_id,$payment_id);
        } else {
            $payment_list = $this->shipper_payment_list($request->shipper_id,$payment_id);
        }
        if(!empty($payment_list)) {
            return response()->json(['status' => 0 , 'message' => 'Success' ,'payment_list'=>$payment_list]);
        }
        return response()->json(['status' => 1 , 'message' => 'Invoice Payments not found!']);

    }

    private function shipper_payment_list($shipper_id, $payment_id = null)
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
                'arrival_shipments' => $shipmentGroup->count(),
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



    private function retail_payment_list($retail_id, $payment_id = null)
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
                'arrival_shipments' => $shipmentGroup->count(),
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

}
