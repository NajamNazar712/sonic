<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DoneToPendingMove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'done_to_pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move Back done to make if any issue raise by finance';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $donePaymentIds = [
            1701430
        ];

        DB::transaction(function () use ($donePaymentIds) {

            $donePayments = DB::table('done_payments')->whereIn('id', $donePaymentIds)->get();

            foreach ($donePayments as $donePayment) {

                $pendingPayment = DB::table('pending_payments')->where('user_id', $donePayment->user_id)->latest()->first();

                if ($pendingPayment) {
                    DB::table('pending_payments')->where('id', $pendingPayment->id)->update([
                        'total_shipments'    => $pendingPayment->total_shipments + $donePayment->total_shipments,
                        'delivered_shipments'=> $pendingPayment->delivered_shipments + $donePayment->delivered_shipments,
                        'returned_shipments' => $pendingPayment->returned_shipments + $donePayment->returned_shipments,
                        'adjusted_shipments' => $pendingPayment->adjusted_shipments + $donePayment->adjusted_shipments,
                        'arrival_shipment'   => $pendingPayment->arrival_shipment + $donePayment->arrival_shipment,
                        'updated_at'         => now(),
                    ]);
                } else {
                    $pendingPaymentId = DB::table('pending_payments')->insertGetId([
                        'user_id'            => $donePayment->user_id,
                        'total_shipments'    => $donePayment->total_shipments,
                        'delivered_shipments'=> $donePayment->delivered_shipments,
                        'returned_shipments' => $donePayment->returned_shipments,
                        'adjusted_shipments' => $donePayment->adjusted_shipments,
                        'arrival_shipment'   => $donePayment->arrival_shipment,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                    $pendingPayment = (object)['id' => $pendingPaymentId];
                }

                // Move done_payment_shipments to pending_payment_shipments
                $doneShipments = DB::table('done_payment_shipments')->where('done_payment_id', $donePayment->id)->get();
                $transaction_id = (string)Str::uuid();
                foreach ($doneShipments as $shipment) {
                    DB::table('pending_payment_shipments')->insert([
                        'pending_payment_id' => $pendingPayment->id,
                        'shipment_id'        => $shipment->shipment_id,
                        'type'               => $shipment->type,
                        'amount'             => $shipment->amount,
                        'charges'            => $shipment->charges,
                        'gst'                => $shipment->gst,
                        'sms_charges'        => $shipment->sms_charges,
                        'payable'            => $shipment->payable,
                        'wht'                => $shipment->wht,
                        'transaction_id'     => $transaction_id,
                        'cod_sst'            => $shipment->cod_sst,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }

                // Run pending payments recalculation SP if needed
                DB::select('CALL update_pending_payment_statistics(?)', [$pendingPayment->id]);

                // Delete done_payment_shipments and done_payment
                DB::table('done_payment_shipments')->where('done_payment_id', $donePayment->id)->delete();
                DB::table('done_payments')->where('id', $donePayment->id)->delete();
            }
        });

        $this->info('Done payments and shipments moved back to pending payments.');
    }


}
