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

                // Initialize variables for pending payments and calculations
                $pending_payments = null;
                $pending_payment_calculation = null;
                $totalPendingShipments = 0;

                // Retrieve pending payments if available
                $pending_payments = PendingPayment::where('user_id', $shipment->user_id)->first();
                if ($pending_payments) {
                    $pending_payment_calculation = PendingPaymentCalculation::where('pending_payment_id', $pending_payments->id)->first();
                    $totalPendingShipments = $pending_payments->total_shipments;
                }

                // Initialize $pending_invoice_shipment
                $pending_invoice_shipment = null;

                // Retrieve pending invoice shipment if available
                if ($shipment->id) {
                    $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment->id)->first();
                }

                // Initialize $pending_payment_shipment
                $pending_payment_shipment = null;

                // Retrieve pending payment shipment if available
                if ($shipment->id) {
                    $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment->id)->first();
                }
                
                // Initialize $type_of_charges
                $type_of_charges = 'Type Not Found';

                // Determine type of charges
                if ($pending_invoice_shipment) {
                    $type_of_charges = $this->getTypeOfCharges($pending_invoice_shipment->type);
                } elseif ($pending_payment_shipment) {
                    $type_of_charges = $this->getTypeOfCharges($pending_payment_shipment->type);
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

                // Handle other ledger entries (Charges, Payments) similarly...
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
