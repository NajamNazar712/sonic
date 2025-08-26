<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\Invoice;
use App\Http\Models\InvoiceShipment;
use App\Http\Models\PendingPaymentCalculation;
use App\Http\Models\PendingPaymentShipment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateArrivalChargesIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:pending_payment_shipment_arrival_charges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $startDate =  Carbon::now()->subMonth(12)->format('Y-m-d 00:00:00');
        $endDate = Carbon::now()->format('Y-m-d 23:59:59');

        $query = DB::table('pending_payment_shipments')
            ->select(
                'shipments.tracking_number',
                'shipments.id as shipment_id',
                'shipments.business_category_id',
                'pending_payment_shipments.id as pending_payment_shipment_id',
                'pending_payment_shipments.pending_payment_id',
                'z.id AS zone_id',
                'oc.id AS city_id',
                'cswl.id AS cswl_id',
                DB::raw('COALESCE(shipments.weight_charges, 0) AS weight_charges'),
                DB::raw('COALESCE(sac.faf_charges, 0) AS faf_charges'),
                DB::raw('COALESCE(shipments.fuel_surcharge, 0) AS fuel_surcharge'),
                DB::raw('(
                    COALESCE(shipments.weight_charges, 0) +
                    COALESCE(shipments.fuel_surcharge, 0) +
                    COALESCE(sac.faf_charges, 0)
                 ) AS new_charges'),
                DB::raw('COALESCE(pending_payment_shipments.charges, 0) AS invoice_charges'),
                DB::raw('COALESCE(pending_payment_shipments.gst, 0) AS invoice_gst'),
                DB::raw('COALESCE(pending_payment_shipments.payable, 0) AS invoice_amount'),
                DB::raw('COALESCE(z.gst, 0.13) AS gst')
            )
            ->leftJoin('shipments', 'pending_payment_shipments.shipment_id', '=', 'shipments.id')
            ->leftjoin('change_shipment_weight_logs as cswl', function ($join) {
                $join->on('cswl.shipment_id', '=', 'shipments.id')
                    ->where('cswl.id', '=', DB::raw('(SELECT MAX(id) FROM change_shipment_weight_logs WHERE shipment_id = shipments.id)'));
            })
            ->leftJoin('shipment_additional_charges AS sac', 'sac.shipment_id', '=', 'shipments.id')
            ->leftJoin('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')

            ->leftJoin('users', 'shipments.user_id', '=', 'users.id')
            ->leftJoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftJoin('zones AS z', 'z.id', '=', 'oc.zone_id')
            ->where('pending_payment_shipments.type', 3)
            ->where('shipments.shipment_type', 1)
            ->where('users.account_type_id', 1)
            ->whereBetween('pending_payment_shipments.created_at', [$startDate,$endDate])
            ->having('new_charges', '!=', DB::raw('invoice_charges'))
            ->groupBy('pending_payment_shipments.shipment_id')
            ->get();

//        $query2 = DB::table('pending_payment_shipments')
//            ->select(
//                'shipments.tracking_number',
//                'shipments.id as shipment_id',
//                'shipments.business_category_id',
//                'pending_payment_shipments.id as pending_payment_shipment_id',
//                'pending_payment_shipments.pending_payment_id',
//                'z.id AS zone_id',
//                'oc.id AS city_id',
//                'cswl.id AS cswl_id',
//                DB::raw('COALESCE(shipments.weight_charges, 0) AS weight_charges'),
//                DB::raw('COALESCE(sac.faf_charges, 0) AS faf_charges'),
//                DB::raw('COALESCE(shipments.fuel_surcharge, 0) AS fuel_surcharge'),
//                DB::raw('(
//            COALESCE(shipments.weight_charges, 0) +
//            COALESCE(shipments.fuel_surcharge, 0) +
//            COALESCE(sac.faf_charges, 0)
//        ) AS new_charges'),
//                DB::raw('(
//            COALESCE(shipments.weight_charges, 0) +
//            COALESCE(shipments.fuel_surcharge, 0) +
//            COALESCE(sac.faf_charges, 0) +
//            COALESCE(pending_payment_shipments.gst, 0)
//        ) AS new_charges_with_gst'),
//                DB::raw('COALESCE(pending_payment_shipments.charges, 0) AS invoice_charges'),
//                DB::raw('COALESCE(pending_payment_shipments.gst, 0) AS invoice_gst'),
//                DB::raw('COALESCE(pending_payment_shipments.payable, 0) AS invoice_amount'),
//                DB::raw('COALESCE(z.gst, 0.13) AS gst')
//            )
//            ->leftJoin('shipments', 'pending_payment_shipments.shipment_id', '=', 'shipments.id')
//            ->leftJoin('change_shipment_weight_logs as cswl', function ($join) {
//                $join->on('cswl.shipment_id', '=', 'shipments.id')
//                    ->whereRaw('cswl.id = (
//                 SELECT MAX(id) FROM change_shipment_weight_logs
//                 WHERE shipment_id = shipments.id
//             )');
//            })
//            ->leftJoin('shipment_additional_charges AS sac', 'sac.shipment_id', '=', 'shipments.id')
//            ->leftJoin('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
//            ->leftJoin('users', 'shipments.user_id', '=', 'users.id')
//            ->leftJoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
//            ->leftJoin('zones AS z', 'z.id', '=', 'oc.zone_id')
//            ->where('pending_payment_shipments.type', 3)
//            ->where('shipments.shipment_type', 1)
//            ->where('users.account_type_id', 1)
//            ->whereBetween('pending_payment_shipments.created_at', [$startDate,$endDate])
//            ->groupBy('pending_payment_shipments.shipment_id')
//            ->havingRaw('(-1 * (
//        COALESCE(shipments.weight_charges, 0) +
//        COALESCE(shipments.fuel_surcharge, 0) +
//        COALESCE(sac.faf_charges, 0) +
//        COALESCE(pending_payment_shipments.gst, 0)
//    )) != COALESCE(pending_payment_shipments.payable, 0)')
//            ->get();

        $pending_payment_id = array();
        foreach ($query as $value){
            if(empty($value->cswl_id)) {
                $new_weight_charges = $value->new_charges;
                if ($value->business_category_id == 1) {
                    $new_gst = ROUND(($new_weight_charges * AdminFinanceController::gst($value->zone_id, $value->city_id)), 2, PHP_ROUND_HALF_DOWN);
                } else {
                    $new_gst = ROUND(($new_weight_charges * AdminFinanceController::international_gst()), 2, PHP_ROUND_HALF_DOWN);
                }
                $payable = 0 - ($new_weight_charges + $new_gst);
                $pending_payment_id[$value->pending_payment_id] = $value->pending_payment_id;


                if (!empty($value->pending_payment_shipment_id)) {
                    PendingPaymentShipment::where('id', $value->pending_payment_shipment_id)->update(['charges' => $new_weight_charges, 'gst' => $new_gst, 'payable' => $payable]);
                }
            }
        }

        if(count($pending_payment_id) > 0) {
            $pendingPaymentsCalc = DB::table('pending_payment_shipments')
                ->select(
                    'pending_payment_shipments.pending_payment_id',
                    'pending_payment_calculations.payable',
                    DB::raw('SUM(pending_payment_shipments.amount) as total_amount'),
                    DB::raw('SUM(pending_payment_shipments.charges) as total_charges'),
                    DB::raw('SUM(pending_payment_shipments.gst) as total_gst'),
                    DB::raw('SUM(pending_payment_shipments.sms_charges) as total_sms_charges'),
                    DB::raw('SUM(pending_payment_shipments.payable) as total_payable'),
                    DB::raw('SUM(pending_payment_shipments.wht) as total_wht'),
                    DB::raw('SUM(pending_payment_shipments.cod_sst) as total_cod_sst')
                )
                ->join('pending_payment_calculations', 'pending_payment_shipments.pending_payment_id', '=', 'pending_payment_calculations.pending_payment_id')
                ->join('pending_payments', 'pending_payment_calculations.pending_payment_id', '=', 'pending_payments.id')
                ->whereIn('pending_payment_calculations.pending_payment_id', $pending_payment_id)
                ->groupBy('pending_payment_shipments.pending_payment_id')
                ->havingRaw('SUM(pending_payment_shipments.payable) != pending_payment_calculations.payable')
                ->get();


            foreach ($pendingPaymentsCalc as $payment) {
                DB::table('pending_payment_calculations')
                    ->where('pending_payment_id', $payment->pending_payment_id)
                    ->update([
                        'amount' => $payment->total_amount,
                        'charges' => $payment->total_charges,
                        'gst' => $payment->total_gst,
                        'sms_charges' => $payment->total_sms_charges,
                        'payable' => $payment->total_payable,
                        'wht' => $payment->total_wht,
                        'cod_sst' => $payment->total_cod_sst,
                    ]);
            }

        }
    }
}
