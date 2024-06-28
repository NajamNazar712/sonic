<?php

namespace App\Console\Commands;

use App\Http\Models\DonePaymentShipment;
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
            $shipments = Shipment::where('user_id', $user->id)->get();
            $type_of_charges = '';

            $totalShipments = $shipments->count();
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
                        'number_of_shipments' => $totalShipments,
                        'tracking_number' => $shipment->tracking_number,
                        'origin' => $user->city_id,
                        'destination' => $shipment->consignee_city_id,
                        'cod_amount' => $shipment->amount,
                        'type_of_charges' => null,
                        'weight_charges' => $shipment->weight_charges,
                        'fuel_surcharge' => $shipment->fuel_surcharge,
                        'gst' => $shipment->gst,
                        'net_payable' => $shipment->net_payable,
                        'reference_id' => null
                    ]);
                }

                // Ledger entry for "Charges"
                if ($totalDebit > 0) {
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
                        'number_of_shipments' => $totalShipments,
                        'tracking_number' => $shipment->tracking_number,
                        'origin' => $user->city_id,
                        'destination' => $shipment->consignee_city_id,
                        'cod_amount' => $shipment->amount,
                        'type_of_charges' => null,
                        'weight_charges' => $shipment->weight_charges,
                        'fuel_surcharge' => $shipment->fuel_surcharge,
                        'gst' => $shipment->gst,
                        'net_payable' => $shipment->net_payable,
                        'reference_id' => null
                    ]);
                }

                // Ledger entries for "Payment"
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
                        'number_of_shipments' => $totalShipments,
                        'tracking_number' => $shipment->tracking_number,
                        'origin' => $user->city_id,
                        'destination' => $shipment->consignee_city_id,
                        'cod_amount' => $shipment->amount,
                        'type_of_charges' => null,
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
