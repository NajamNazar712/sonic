<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\Shipment;
use App\ShipmentAdditionalCharges;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class WalletChargesUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet_charges_update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
            Log::channel('cronJobLog')->info('wallet_charges_update started');   
            $filePath = storage_path('app/FinovaChargesMay.csv');

            // Load Excel file
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(); 

            $headers = ['tracking_number','charges'];
            $shipmentsData2 = [];

            foreach (array_slice($rows, 1) as $row) {
                $shipmentsData2[] = array_combine($headers, $row);
            }

            $errors = [];
            $success = [];

            $chunkSize = 5000;

            // Process shipmentsData in chunks
            collect($shipmentsData2)->chunk($chunkSize)->each(function ($chunk) use (&$errors, &$success) {
                // Get the tracking numbers for this chunk
                $trackingNumbers = collect($chunk)->pluck('tracking_number');

                // Get all shipments for the chunk in one go
                $shipments = Shipment::whereIn('tracking_number', $trackingNumbers)->get()->keyBy('tracking_number');

                foreach ($chunk as $key => $shipmentData) {
                    $tracking_number = $shipmentData['tracking_number'];
                    $charges = $shipmentData['charges'];

                    // Check if the shipment exists in the database
                    if (!isset($shipments[$tracking_number])) {
                        $errors[$key] = [
                            'tracking_number' => $tracking_number,
                            'error' => 'Shipment not found'
                        ];
                        continue; // Skip to the next iteration if shipment not found
                    }

                    $shipment = $shipments[$tracking_number];
                    $shipment_id = $shipment->id;

                    // Check for existing charges
                    $existingCharge = ShipmentAdditionalCharges::where('shipment_id', $shipment_id)->first();

                    if ($existingCharge) {
                        // If charges are 0, 0.00, or null, update both wallet charges and timestamp
                        if ($existingCharge->wallet_charges == 0 || $existingCharge->wallet_charges == 0.00 || is_null($existingCharge->wallet_charges)) {
                            $existingCharge->update([
                                'wallet_charges' => $charges,
                                'wallet_charges_updated_at' => now(),
                            ]);
                        } elseif ($charges > 0) {
                            // If charges are greater than 0, update only wallet charges (no timestamp change)
                            $existingCharge->update([
                                'wallet_charges' => $charges,
                            ]);
                        }
                    } else {
                        // If no existing charge, create a new entry
                        ShipmentAdditionalCharges::create([
                            'shipment_id' => $shipment_id,
                            'wallet_charges' => $charges,
                            'wallet_charges_updated_at' => now(),
                        ]);
                    }

                    // Check if there is a pending payment
                    $pending_payment = PendingPaymentShipment::where('shipment_id', $shipment_id)
                        ->whereIn('type', [0, 1])
                        ->latest()
                        ->first();

                    if ($pending_payment) {
                        // Update payment if there's a pending payment
                        AdminFinanceController::update_payment($shipment_id, $pending_payment->type);
                        $success[] = $tracking_number;
                        continue; // Skip further checks for this shipment
                    }

                    // Check for done payments and pending processes
                    $done_payment_query = DonePaymentShipment::where('shipment_id', $shipment_id)
                        ->whereIn('type', [0, 1])
                        ->latest();

                    $check_pending_process = (clone $done_payment_query)->whereHas('done_payment', function ($query) {
                        $query->whereIn('status', [0, 1, 3])->where('is_wallet_payment', 1);
                    })->exists();

                    if ($check_pending_process) {
                        // If there's a pending process, update the payment
                        $done_payment = (clone $done_payment_query)->first();
                        AdminFinanceController::update_payment_done_payment($shipment_id, $done_payment->type, $done_payment->done_payment_id);
                        $success[] = $tracking_number;
                        continue; // Skip further checks for this shipment
                    }

                    // Check if payment has been paid late
                    $check_paid_late = (clone $done_payment_query)->whereHas('done_payment', function ($query) {
                        $query->where('status', 1)->where('is_wallet_payment', 1);
                    })->exists();

                    if ($check_paid_late) {
                        $errors[$key] = [
                            'tracking_number' => $tracking_number,
                            'error' => 'Payment cannot be processed now'
                        ];
                    } else {
                        // If no issues, mark this shipment as successful
                        $success[] = $tracking_number;
                    }
                }
            });

            // Final response
            $response = [
                'status' => empty($errors) ? 1 : 0,
                'message' => empty($errors) ? 'All charges updated successfully.' : 'Some charges could not be updated due to errors.',
                'success' => $success,
                'errors' => $errors,
            ];
            Log::channel('cronJobLog')->info('wallet_charges_update result:', $response);

            echo json_encode($response);

    }
}
