<?php

namespace App\Http\Controllers;

use App\Http\Models\DonePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipperFinanceApiController extends Controller
{
    public function payment_list(Request $request)
    {
        $shipper_id = $request->shipper_id;
        $payment_list = DB::table('done_payments')->join('users as u', 'done_payments.user_id', '=', 'u.id')
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
            ->select('done_payments.id as payment_id', 'u.id as user_id', 'u.name as shipper', 'c.name as city', 'u.phone', 'u.phone2', 'u.address', 'done_payments.total_shipments', 'done_payments.delivered_shipments', 'done_payments.delivered_shipments as delivered_shipments_count', 'done_payments.returned_shipments', 'done_payments.returned_shipments as returned_shipments_count', 'done_payments.adjusted_shipments', 'done_payments.adjusted_shipments as adjusted_shipments_count', DB::raw('SUM(dps.amount) as total_amount'), DB::raw('SUM(dps.charges) as total_charges'), DB::raw('SUM(dps.gst) as total_gst'),DB::raw('SUM(dps.wht) as total_wht'), DB::raw('SUM(dps.payable) as total_payable'), 'ub.name as bank', 'done_payments.reference_number', 'done_payments.created_at as done_at', 'b.name as company_bank', 'done_payments.status', 'done_payments.ibft_charges', 'done_payments.arrival_shipment as arrival_shipments_count', 'done_payments.arrival_shipment as arrival_shipments')
            ->where('done_payments.user_id',$shipper_id)
            ->groupBy('done_payments.id')
             ->orderBy('done_payments.id', 'desc');

        if($payment_list->isNotEmpty()) {
            return response()->json(['status' => 0 , 'message' => 'Success' ,'payment_list'=>$payment_list]);
        }
        return response()->json(['status' => 1 , 'message' => 'Invoice Payments not found!']);

    }
}
