<?php

namespace App\Console\Commands;

use App\Http\Models\PendingPayment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteDuplicateArrival extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:duplicate_arrival';

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

        $startDate =  Carbon::now()->subDays(1)->format('Y-m-d 00:00:00');
        $startDate2 =  Carbon::now()->subDays(30)->format('Y-m-d 00:00:00');
        $endDate = Carbon::now()->format('Y-m-d 23:59:59');
        $type = 3;


        $duplicates_pending_invoice = DB::table('pending_invoice_shipments')
            ->selectRaw('MIN(pending_invoice_shipments.id) AS min_id, shipments.user_id')
            ->join('shipments', 'shipments.id', '=', 'pending_invoice_shipments.shipment_id')
            ->join('users', 'users.id', '=', 'shipments.user_id')
            ->whereBetween('pending_invoice_shipments.created_at', [$startDate, $endDate])
            ->where('pending_invoice_shipments.type', $type)
            ->groupBy(
                'pending_invoice_shipments.shipment_id',
                'pending_invoice_shipments.invoice_amount',
                'pending_invoice_shipments.charges',
                'pending_invoice_shipments.type'
            )
            ->havingRaw('COUNT(pending_invoice_shipments.shipment_id) > 1')
            ->havingRaw('COUNT(pending_invoice_shipments.invoice_amount) > 1')
            ->havingRaw('COUNT(pending_invoice_shipments.charges) > 1')
            ->havingRaw('COUNT(pending_invoice_shipments.type) > 1')
            ->pluck('user_id', 'min_id')
            ->toArray();

        if (count($duplicates_pending_invoice) > 0) {
            $duplicate_value_invoice = array_keys($duplicates_pending_invoice);
            $user_ids_2 = array_values($duplicates_pending_invoice);
            DB::table('pending_invoice_shipments')->whereIn('id', $duplicate_value_invoice)->delete();
        }

        $duplicates = DB::table('pending_payment_shipments')
            ->selectRaw('MIN(pending_payment_shipments.id) AS min_id,pending_payments.user_id')
            ->whereBetween('pending_payment_shipments.created_at', [$startDate, $endDate])
            ->where('pending_payment_shipments.type', $type)
            ->join('pending_payments', 'pending_payments.id', '=', 'pending_payment_shipments.pending_payment_id')
            ->join('users', 'users.id', '=', 'pending_payments.user_id')
            ->groupBy('pending_payment_shipments.shipment_id', 'pending_payment_shipments.amount', 'pending_payment_shipments.payable', 'pending_payment_shipments.charges', 'pending_payment_shipments.type')
            ->havingRaw('COUNT(pending_payment_shipments.shipment_id) > 1')
            ->havingRaw('COUNT(pending_payment_shipments.amount) > 1')
            ->havingRaw('COUNT(pending_payment_shipments.payable) > 1')
            ->havingRaw('COUNT(pending_payment_shipments.charges) > 1')
            ->havingRaw('COUNT(pending_payment_shipments.type) > 1')
            ->pluck('user_id','min_id')->toArray();



        if (count($duplicates) > 0) {
            $duplicate_value = array_keys($duplicates);
            $user_ids = array_values($duplicates);

            DB::table('pending_payment_shipments')->whereIn('id', $duplicate_value)->delete();


            $pendingPayments = DB::table('pending_payment_shipments')
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
                ->whereIn('pending_payments.user_id', $user_ids)
                ->groupBy('pending_payment_shipments.pending_payment_id')
                ->havingRaw('SUM(pending_payment_shipments.payable) != pending_payment_calculations.payable')
                ->get();


            foreach ($pendingPayments as $payment) {
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


            $totalShipments = DB::table('pending_payment_shipments')
                ->join('pending_payments', 'pending_payment_shipments.pending_payment_id', '=', 'pending_payments.id')
                ->selectRaw('COUNT(pending_payment_shipments.shipment_id) AS total_shipments, pending_payment_shipments.pending_payment_id')
                ->whereIn('pending_payments.user_id', $user_ids)
                ->whereIn('pending_payment_shipments.type', [0, 1, 2])
                ->groupBy('pending_payment_id')
                ->pluck('total_shipments', 'pending_payment_id');

            $deliveredShipments = DB::table('pending_payment_shipments')
                ->join('pending_payments', 'pending_payment_shipments.pending_payment_id', '=', 'pending_payments.id')
                ->selectRaw('COUNT(pending_payment_shipments.shipment_id) AS total_delivered, pending_payment_shipments.pending_payment_id')
                ->whereIn('pending_payments.user_id', $user_ids)
                ->where('pending_payment_shipments.type', 0)
                ->groupBy('pending_payment_id')
                ->pluck('total_delivered', 'pending_payment_id');

            $returnedShipments = DB::table('pending_payment_shipments')
                ->join('pending_payments', 'pending_payment_shipments.pending_payment_id', '=', 'pending_payments.id')
                ->selectRaw('COUNT(pending_payment_shipments.shipment_id) AS total_return, pending_payment_shipments.pending_payment_id')
                ->whereIn('pending_payments.user_id', $user_ids)
                ->where('pending_payment_shipments.type', 1)
                ->groupBy('pending_payment_id')
                ->pluck('total_return', 'pending_payment_id');

            $adjustedShipments = DB::table('pending_payment_shipments')
                ->join('pending_payments', 'pending_payment_shipments.pending_payment_id', '=', 'pending_payments.id')
                ->selectRaw('COUNT(pending_payment_shipments.shipment_id) AS total_adjust, pending_payment_shipments.pending_payment_id')
                ->whereIn('pending_payments.user_id', $user_ids)
                ->where('pending_payment_shipments.type', 2)
                ->groupBy('pending_payment_id')
                ->pluck('total_adjust', 'pending_payment_id');

            $arrivalShipments = DB::table('pending_payment_shipments')
                ->join('pending_payments', 'pending_payment_shipments.pending_payment_id', '=', 'pending_payments.id')
                ->selectRaw('COUNT(pending_payment_shipments.shipment_id) AS total_arrival, pending_payment_shipments.pending_payment_id')
                ->whereIn('pending_payments.user_id', $user_ids)
                ->where('pending_payment_shipments.type', 3)
                ->groupBy('pending_payment_id')
                ->pluck('total_arrival', 'pending_payment_id');


            foreach ($totalShipments as $pendingPaymentId => $totalShipmentCount) {
                PendingPayment::where('id', $pendingPaymentId)->update([
                    'total_shipments' => $totalShipmentCount,
                    'delivered_shipments' => $deliveredShipments[$pendingPaymentId] ?? 0,
                    'returned_shipments' => $returnedShipments[$pendingPaymentId] ?? 0,
                    'adjusted_shipments' => $adjustedShipments[$pendingPaymentId] ?? 0,
                    'arrival_shipment' => $arrivalShipments[$pendingPaymentId] ?? 0,
                ]);
            }

            echo "Deleted/Update duplicate records successfully.";
        } else {
            echo "No duplicate records found for deletion.";
        }

        $results = DB::table('pending_payment_shipments')
            ->select(
                'pending_payments.arrival_shipment',
                DB::raw('COUNT(pending_payment_shipments.shipment_id) AS total_arrival'),
                'pending_payment_shipments.pending_payment_id'
            )
            ->join('pending_payments', 'pending_payments.id', '=', 'pending_payment_shipments.pending_payment_id')
            ->whereBetween('pending_payments.created_at', [$startDate2, $endDate])
            ->whereIn('pending_payment_shipments.type', [3])
            ->groupBy('pending_payment_shipments.pending_payment_id')
            ->havingRaw('pending_payments.arrival_shipment != total_arrival')
            ->get();

// Prepare updates in bulk
        $updateData = [];
        foreach ($results as $payment) {
            $updateData[$payment->pending_payment_id] = [
                'arrival_shipment' => $payment->total_arrival,
            ];
        }

// Perform updates
        foreach ($updateData as $pendingPaymentId => $data) {
            DB::table('pending_payments')
                ->where('id', $pendingPaymentId)
                ->update($data);
        }





    }
}
