<?php

namespace App\Console\Commands;

use App\Http\Models\DonePaymentShipment;
use App\Http\Models\PendingInvoiceShipment;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentCalculation;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentLedger;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ServiceLedger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:shipment_ledger';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generates a ledger for the shipments';

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
        $this->store_shipment_ledger();
    }

    protected function store_shipment_ledger()
    {
        $users = User::where('status', 3)->get();
        foreach ($users as $user) {

            $pending_payments = PendingPayment::where('user_id', $user->id)->first();
            $pending_payment_calculations = PendingPaymentCalculation::where('pending_payment_id', $pending_payments->id)->first();

            $shipments = Shipment::where('user_id', $user->id)->get();
            // $totalShipments = $shipments->count();
            $totalShipmentsPicked = DB::table('shipments')
            ->join('shipments_journey', 'shipments.id', '=', 'shipments_journey.shipment_id')
            ->where('shipments.user_id', $user->id)
            ->where('shipments_journey.shipper_status_id', 2)
            ->count();

            foreach ($shipments as $shipment) {
                $shipments_journey = ShipmentsJourney::where('shipment_id', $shipment->id)
                    ->where('shipper_status_id', 2)
                    ->first();
                $done_shipment_payment = DonePaymentShipment::where('shipment_id', $shipment->id)->get();

                // Calculate credit
                $credit = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->fuel_surcharge;

                // Calculate debit and total debit amount
                $totalDebit = $done_shipment_payment->sum('charges');
                $balance = $credit - $totalDebit;

                $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment->id)->first();
                $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment->id)->first();

                if ($pending_invoice_shipment) {
                    if ($pending_invoice_shipment->type == 0) {
                        $type_of_charges = 'Delivered';
                    } elseif ($pending_invoice_shipment->type == 1) {
                        $type_of_charges = 'Returned';
                    } elseif ($pending_invoice_shipment->type == 2) {
                        $type_of_charges = 'Adjusted';
                    } elseif ($pending_invoice_shipment->type == 3) {
                        $type_of_charges = 'Arrived';
                    } else {
                        $type_of_charges = 'Unknown Type';
                    }
                } elseif ($pending_payment_shipment) {
                    if ($pending_payment_shipment->type == 0) {
                        $type_of_charges = 'Delivered';
                    } elseif ($pending_payment_shipment->type == 1) {
                        $type_of_charges = 'Returned';
                    } elseif ($pending_payment_shipment->type == 2) {
                        $type_of_charges = 'Adjusted';
                    } elseif ($pending_payment_shipment->type == 3) {
                        $type_of_charges = 'Arrived';
                    } else {
                        $type_of_charges = 'Unknown Type';
                    }
                } else {
                    $type_of_charges = 'Type Not Found';
                }

                // Ledger entry for "Shipment Picked"
                if ($shipments_journey && $shipments_journey->shipper_status_id == 2) {
                    ShipmentLedger::create([
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'shipment_book_date' => $shipment->created_at,
                        'particular_id' => 1,
                        'particulars' => 'Shipment Picked',
                        'debit' => 0,
                        'credit' => $credit,
                        'balance' => $credit,
                        'ledger_time' => now(),
                        'number_of_shipments' => $totalShipmentsPicked,
                        'tracking_number' => $shipment->tracking_number,
                        'origin' => $user->city_id,
                        'destination' => $shipment->consignee_city_id,
                        'cod_amount' => $shipment->amount,
                        'type_of_charges' => $type_of_charges,
                        'weight_charges' => $shipment->weight_charges,
                        'fuel_surcharge' => $shipment->fuel_surcharge,
                        'gst' => $shipment->gst,
                        'net_payable' => $shipment->net_payable,
                        'reference_id' => null
                    ]);
                }

                // Ledger entry for "Charges"
                if ($totalDebit > 0) {
                    $totalPendingShipments = 0;
                    if ($pending_invoice_shipment){
                        $totalPendingShipments = PendingInvoiceShipment::whereIn('shipment_id', $shipments->pluck('id'))->count();
                    } else if ($pending_payment_shipment){
                        $totalPendingShipments = PendingPaymentShipment::whereIn('shipment_id', $shipments->pluck('id'))->count();
                    }
                    ShipmentLedger::create([
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'shipment_book_date' => $shipment->created_at,
                        'particular_id' => 2,
                        'particulars' => 'Charges',
                        'debit' => $totalDebit,
                        'credit' => $credit,
                        'balance' => $credit - $totalDebit,
                        'ledger_time' => now(),
                        'number_of_shipments' => $totalPendingShipments,
                        'tracking_number' => $shipment->tracking_number,
                        'origin' => $user->city_id,
                        'destination' => $shipment->consignee_city_id,
                        'cod_amount' => $pending_payment_calculations->amount,
                        'type_of_charges' => $type_of_charges,
                        'weight_charges' => $shipment->weight_charges,
                        'fuel_surcharge' => $shipment->fuel_surcharge,
                        'gst' => $shipment->gst,
                        'net_payable' => $shipment->net_payable,
                        'reference_id' => null
                    ]);
                }

                // Ledger entries for "Payment"
                $totalDoneShipments = DonePaymentShipment::whereIn('shipment_id', $shipments->pluck('id'))->count();
                foreach ($done_shipment_payment as $payment) {
                    $remainingBalance = $balance - $payment->charges;
                    ShipmentLedger::create([
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'shipment_book_date' => $shipment->created_at,
                        'particular_id' => 3, 
                        'particulars' => 'Payment',
                        'debit' => $payment->charges, 
                        'credit' => $remainingBalance, 
                        'balance' => $remainingBalance,
                        'ledger_time' => now(),
                        'number_of_shipments' => $totalDoneShipments,
                        'tracking_number' => $shipment->tracking_number,
                        'origin' => $user->city_id,
                        'destination' => $shipment->consignee_city_id,
                        'cod_amount' => $payment->amount,
                        'type_of_charges' => $type_of_charges,
                        'weight_charges' => $shipment->weight_charges,
                        'fuel_surcharge' => $shipment->fuel_surcharge,
                        'gst' => $payment->gst,
                        'net_payable' => $payment->payable,
                        'reference_id' => $payment->done_payment_id
                    ]);
                }
            }
        }
    }

}
