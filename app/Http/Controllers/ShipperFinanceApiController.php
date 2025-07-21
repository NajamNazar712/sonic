<?php

namespace App\Http\Controllers;

use App\Http\Models\DonePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipperFinanceApiController extends Controller
{
    public function payment_list(Request $request)
    {

        if ($request->app_type == 2 ) {
            $payment_list = $this->retail_payment_list($request->retail_shipper_id);
        } else {
            $payment_list = $this->shipper_payment_list($request->shipper_id);
        }

        if($payment_list && $payment_list->count() > 0) {
            return response()->json(['status' => 0 , 'message' => 'Success' ,'payment_list'=>$payment_list]);
        }
        return response()->json(['status' => 1 , 'message' => 'Invoice Payments not found!']);

    }

    private function shipper_payment_list($shipper_id)
    {
        $shipper_payments = DB::table('done_payments')->join('users as u', 'done_payments.user_id', '=', 'u.id')
            ->join('cities as c', 'u.city_id', '=', 'c.id')
            ->join('done_payment_shipments as dps', 'done_payments.id', '=', 'dps.done_payment_id')
            ->leftJoin('user_bank_infos as ubi', function ($join) {
                $join->on('ubi.id', '=', 'done_payments.user_bank_info_id');
            })
            ->leftJoin('user_bank_infos as ubi_default', function ($join) {
                $join->on('ubi_default.user_id', '=', 'u.id')
                    ->where('ubi_default.default_bank', DB::raw(1));
            })
            ->leftJoin('banks_lists as ub', function ($join) {
                $join->where(function ($sub_query) {
                    $sub_query->whereNotNull('done_payments.user_bank_info_id')
                        ->where('ubi.bank_name', '=', DB::raw('`ub`.`id`'));
                })->orWhere(function ($sub_query) {
                    $sub_query->whereNull('done_payments.user_bank_info_id')
                        ->where('ubi_default.bank_name', '=', DB::raw('`ub`.`id`'));
                });
            })
            ->leftjoin('banks_lists as b', 'done_payments.company_bank_id', '=', 'b.id')
            ->select('done_payments.id','done_payments.id as payment_id', 'u.id as user_id', 'u.name as shipper', 'c.name as city', 'u.phone', 'u.phone2', 'u.address', 'done_payments.total_shipments', 'done_payments.delivered_shipments', 'done_payments.returned_shipments', 'done_payments.adjusted_shipments', 'done_payments.arrival_shipment as arrival_shipments', DB::raw('SUM(dps.amount) as total_amount'), DB::raw('SUM(dps.charges) as total_charges'), DB::raw('SUM(dps.gst) as total_gst'),DB::raw('SUM(dps.wht) as total_wht'),DB::raw('SUM(dps.cod_sst) as total_cod_sst'), DB::raw('SUM(dps.payable) as total_payable'), 'done_payments.ibft_charges', 'ub.name as bank', 'done_payments.reference_number', 'done_payments.created_at as done_at', 'b.name as company_bank', 'done_payments.status')
            ->where('done_payments.user_id',$shipper_id)
            ->groupBy('done_payments.id')
            ->orderBy('done_payments.id', 'desc')
            ->cursorPaginate(20);

        return $shipper_payments;
    }

    private function retail_payment_list($retail_id)
    {

        $retail_payments = DB::table('retail_done_payments')->join('retail_shipper_infos as rsi', 'retail_done_payments.user_id', '=', 'rsi.id')
            ->join('cities as c', 'rsi.city_id', '=', 'c.id')
            ->join('retail_done_payment_shipments as rdps', 'retail_done_payments.id', '=', 'rdps.retail_done_payment_id')
            ->join('retail_shipments as rs', 'rs.shipment_id', '=', 'rdps.shipment_id')
            ->join('banks_lists as ub','ub.id','=','rsi.bank_id')
             ->select('retail_done_payments.id','retail_done_payments.id as payment_id', 'rsi.id as user_id', 'rsi.shipper_name as shipper', 'c.name as city', 'rsi.shipper_phone_no as phone', 'rsi.shipper_address as address', 'retail_done_payments.total_shipments', 'retail_done_payments.delivered_shipments', 'retail_done_payments.adjusted_shipments', DB::raw('SUM(rdps.amount) as total_amount') ,DB::raw('SUM(rs.gst) as total_gst'),DB::raw('SUM(rs.wht) as total_wht'),DB::raw('SUM(rs.cod_sst) as total_cod_sst'), DB::raw('SUM(rdps.payable) as total_payable'), 'retail_done_payments.ibft_charges','ub.name as company_bank', 'retail_done_payments.reference_number', 'retail_done_payments.created_at as done_at', 'retail_done_payments.status')
            ->where('retail_done_payments.user_id',$retail_id)
            ->groupBy('retail_done_payments.id')
            ->orderBy('retail_done_payments.id', 'desc')
            ->cursorPaginate(20);

        return $retail_payments;
    }
}
