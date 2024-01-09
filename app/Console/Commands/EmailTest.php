<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Invoice;
use App\Http\Models\ShipmentsJourney;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EmailTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test';

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

        $invoice = Invoice::find(4122);

        $filename = 'sonic_invoice_details_' . $request->id . '.xlsx';

        $details = array();

        $details[] = ['S. No.', 'Tracking No.', 'Origin', 'Destination', 'Arrival Date', 'Weight (kg)', 'Weight Charges (PKR)', 'Fuel Surcharge (PKR)', 'OSA Charges (PKR)', 'Adjustment Charges (PKR)', 'Total Charges (PKR)', 'GST (PKR)', 'Invoice Amount (PKR)', 'Intercept Charges  (PKR)'];

        $serial_number = 1;

        foreach ($invoice->invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

            if ($shipment_journey->exists()) {
                $date = $shipment_journey->first()->created_at;
            } else {
                $date = $shipment->created_at;
            }

            $date = Carbon::parse($date)->format('Y-m-d');

            $row = array();

            $row[] = $serial_number;
            $row[] = $shipment->tracking_number;
            $row[] = $shipment->pickup_address->city->name;
            $row[] = $shipment->consignee_city->name;
            $row[] = $shipment->created_at;
            $row[] = $shipment->actual_weight;
            $row[] = (($invoice_shipment->type != 2) ? $shipment->weight_charges : 0);
            $row[] = (($invoice_shipment->type != 2) ? $shipment->fuel_surcharge : 0);
            $row[] = (($invoice_shipment->type != 2) ? $shipment->nsa_osa_charges : 0);
            $row[] = (($invoice_shipment->type == 2) ? $shipment->adjustment_charges : 0);
            $row[] = $invoice_shipment->charges;
            $row[] = $invoice_shipment->gst;
            $row[] = $invoice_shipment->invoice_amount;
            $row[] = $shipment->intercept_charges;

            $details[] = $row;

            $serial_number++;
        }
        print_r($details);
        die('here');




        Log::channel('code_test_log')->info('Noman bhai ka log in logs!');
        echo "Noman bhai ka log!";
//        $ref = 'nothing';
//        NotificationsController::send(219, $ref);
    }
}
