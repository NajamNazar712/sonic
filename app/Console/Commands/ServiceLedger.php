<?php

namespace App\Console\Commands;

use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentCalculation;
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
            $shipments = Shipment::where('user_id', $user->id)->get();
            $totalShipmentsPicked = DB::table('shipments')
                ->join('shipments_journey', 'shipments.id', '=', 'shipments_journey.shipment_id')
                ->where('shipments.user_id', $user->id)
                ->where('shipments_journey.shipper_status_id', 2)
                ->count();
            
            $balance = 0; // Initialize balance
    
            foreach ($shipments as $shipment) {
                $shipments_journey = ShipmentsJourney::where('shipment_id', $shipment->id)
                    ->where('shipper_status_id', 2)
                    ->first();
    
                $pending_payments = PendingPayment::where('user_id', $shipment->user_id)->first();
                $pending_payment_calculation = PendingPaymentCalculation::where('pending_payment_id', $pending_payments->id)->first();
                $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment->id)->first();
                $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment->id)->first();
                
                $done_payments = DonePayment::where('user_id', $user->id)->get();
                $done_payment_shipments = DonePaymentShipment::where('shipment_id', $shipment->id)->latest()->get();
    
                if ($pending_invoice_shipment) {
                    $type_of_charges = $this->getTypeOfCharges($pending_invoice_shipment->type);
                } elseif ($pending_payment_shipment) {
                    $type_of_charges = $this->getTypeOfCharges($pending_payment_shipment->type);
                } else {
                    $type_of_charges = 'Type Not Found';
                }
    
                // Ledger entry for "Shipment Picked"
                if ($shipments_journey && $shipments_journey->shipper_status_id == 2) {
                    $credit = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->fuel_surcharge;
                    $balance += $credit;
    
                    ShipmentLedger::create([
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'shipment_book_date' => $shipment->created_at,
                        'particular_id' => 1,
                        'particulars' => 'Shipment Picked',
                        'debit' => 0,
                        'credit' => $credit,
                        'balance' => $balance,
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
                if ($pending_payment_calculation) {
                    $new_debit = $pending_payment_calculation->charges;
                    $old_balance = $balance;
                    $balance -= $new_debit;
                    $totalPendingShipments = $pending_payments->total_shipments;
    
                    ShipmentLedger::create([
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'shipment_book_date' => $shipment->created_at,
                        'particular_id' => 2,
                        'particulars' => 'Charges',
                        'debit' => $new_debit,
                        'credit' => $old_balance,
                        'balance' => $balance,
                        'ledger_time' => now(),
                        'number_of_shipments' => $totalPendingShipments,
                        'tracking_number' => $shipment->tracking_number,
                        'origin' => $user->city_id,
                        'destination' => $shipment->consignee_city_id,
                        'cod_amount' => $pending_payment_calculation->amount,
                        'type_of_charges' => $type_of_charges,
                        'weight_charges' => $shipment->weight_charges,
                        'fuel_surcharge' => $shipment->fuel_surcharge,
                        'gst' => $shipment->gst,
                        'net_payable' => $shipment->net_payable,
                        'reference_id' => null
                    ]);
                }
    
                // Ledger entries for "Payment"
                foreach ($done_payment_shipments as $payment) {
                    $debit = $payment->charges;
                    $old_updated_balance = $balance;
                    $balance -= $debit;
                    $totalDoneShipments = $done_payments->sum('total_shipments');
    
                    ShipmentLedger::create([
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'shipment_book_date' => $shipment->created_at,
                        'particular_id' => 3,
                        'particulars' => 'Payment',
                        'debit' => $debit,
                        'credit' => $old_updated_balance,
                        'balance' => $balance,
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

    private function getTypeOfCharges($type)
    {
        switch ($type) {
            case 0:
                return 'Delivered';
            case 1:
                return 'Returned';
            case 2:
                return 'Adjusted';
            case 3:
                return 'Arrived';
            default:
                return 'Unknown Type';
        }
    }
}
