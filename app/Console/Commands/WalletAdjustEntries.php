<?php

namespace App\Console\Commands;

use App\Http\Models\DonePaymentShipment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentServicesCharges;
use App\Models\FinjaLogSettlementRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class WalletAdjustEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet_adjust_entries';

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

        $query = DB::table('done_payment_shipments as dps')
            ->selectRaw('
                dps.*,
                shipments.tracking_number, 
                shipments.user_id, 
                shipments.cash_handling_charges, 
                shipments.insurance_charges, 
                shipments.replacement_charges, 
                shipments.try_and_buy_charges, 
                shipments.intercept_charges, 
                shipments.nsa_osa_charges, 
                shipments.esc_charges, 
                shipments.return_charges, 
                shipments.weight_charges, 
                shipments.fuel_surcharge, 
                shipments.packaging_material_charges,
                shipments.tracking_number,
                shipments.id as shipment_id,
                sac.wallet_charges,
                sac.faf_charges,
                ssc.reverse_pickup_charges,
                wu.wallet_id as wallet_id,
                flsr.wallet_settlement_updated,
                flsr.wallet_log_updated,
                flsr.wallet_log_charges_updated,
                shipments.tracking_number,
                shipments.id as shipment_id,
                (dps.charges + dps.gst) AS payable2,
                dps2.payable AS arrival_charges,
                latest_fpl.details,
                (
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(latest_fpl.details, "$.charges.esc_charges")), 0) +
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(latest_fpl.details, "$.charges.faf_charges")), 0) +
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(latest_fpl.details, "$.charges.gst_charges")), 0) +
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(latest_fpl.details, "$.charges.sms_charges")), 0) +
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(latest_fpl.details, "$.charges.fuel_surcharge")), 0) +
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(latest_fpl.details, "$.charges.return_charges")), 0) +
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(latest_fpl.details, "$.charges.weight_charges")), 0) +
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(latest_fpl.details, "$.charges.insurance_charges")), 0) +
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(latest_fpl.details, "$.charges.intercept_charges")), 0) +
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(latest_fpl.details, "$.charges.replacement_charges")), 0) +
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(latest_fpl.details, "$.charges.try_and_buy_charges")), 0) +
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(latest_fpl.details, "$.charges.cash_handling_charges")), 0) +
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(latest_fpl.details, "$.charges.reverse_pickup_charges")), 0) +
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(latest_fpl.details, "$.charges.non_service_area_charges")), 0) +
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(latest_fpl.details, "$.charges.packaging_material_charges")), 0) +
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(latest_fpl.details, "$.charges.reverse_pickup_charges")), 0)
                ) AS total_charges
    ')
            ->join('shipments', 'dps.shipment_id', '=', 'shipments.id')
            ->leftjoin('wallet_users as wu', function ($join) {
                $join->on('wu.user_id', '=', 'shipments.user_id')
                    ->where('wu.substitute_user_id', '0');
            })
            ->leftJoin('finja_log_settlement_records as flsr', 'flsr.shipment_id', '=', 'dps.shipment_id')
            ->leftJoin('shipment_additional_charges as sac', 'sac.shipment_id', '=', 'dps.shipment_id')
            ->leftJoin('shipment_services_charges as ssc', 'ssc.shipment_id', '=', 'dps.shipment_id')
            ->leftJoin('done_payment_shipments as dps2', function ($join) {
                $join->on('dps2.shipment_id', '=', 'dps.shipment_id')
                    ->where('dps2.type', '=', 3);
            })
            ->leftJoin(DB::raw("(
        SELECT merged.shipment_id, merged.details FROM (
            SELECT shipment_id, details, id
            FROM finga_api_logs
            WHERE nature_id IN (7, 21) OR nature = 'settlement-request'
            UNION ALL
            SELECT shipment_id, details, id
            FROM finga_api_logs_archive
            WHERE nature_id IN (7, 21) OR nature = 'settlement-request'
        ) AS merged
        INNER JOIN (
            SELECT shipment_id, MAX(id) AS max_id FROM (
                SELECT shipment_id, id
                FROM finga_api_logs
                WHERE nature_id IN (7, 21) OR nature = 'settlement-request'
                UNION ALL
                SELECT shipment_id, id
                FROM finga_api_logs_archive
                WHERE nature_id IN (7, 21) OR nature = 'settlement-request'
            ) AS all_logs
            GROUP BY shipment_id
        ) AS latest
        ON merged.shipment_id = latest.shipment_id AND merged.id = latest.max_id
    ) AS latest_fpl"), 'latest_fpl.shipment_id', '=', 'dps.shipment_id')
            ->where('dps.wallet_action_bid', 3)
            ->where('dps.charges', '>', 0)
            ->whereIn('dps.type', [0, 1])
            ->where('shipments.packaging_material_request', 0)
            ->where('flsr.wallet_log_charges_updated', 0)
            ->whereBetween('dps.created_at', ['2025-01-01 00:00:00', '2025-04-18 23:59:59'])
            ->havingRaw('ABS(payable2) != ABS(total_charges)')
            ->havingRaw('payable != 0')->get();

        $OtherIssuePayload = [];
        $ArrivalIssuePayload = [];
        foreach ($query as $dps) {

            $LogChargeStatus = true;
            $shipmentId = $dps->shipment_id;
            $shipment = Shipment::find($shipmentId);
            $service_charges = ShipmentServicesCharges::where('shipment_id', $shipmentId);

            if ($service_charges->exists()) {
                $service_charges = $service_charges->first();
                $service_charges = $service_charges->reverse_pickup_charges;
            } else {
                $service_charges = 0;
            }

            $logCharged = FinjaLogSettlementRecord::where('shipment_id', $shipmentId)->first();
            $logCharged2 = !empty($logCharged) ? $logCharged->wallet_log_charges_updated : 0;

            $check_arrival_paid_done = DonePaymentShipment::where('shipment_id', $shipmentId)->where('type', 3)->exists();
            $check_arrival_paid_pending = PendingPaymentShipment::where('shipment_id', $shipmentId)->where('type', 3)->exists();

            if ($logCharged2 == 0 && ($check_arrival_paid_done || $check_arrival_paid_pending)) {
                $LogChargeStatus = false;
            }

            $charges = [];
            if ($shipment->packaging_material_request == 1) {
                $charges = [
                    'packaging_material_charges' => floatval($shipment->packaging_material_charges),
                    'gst_charges' => floatval($dps->gst),
                    'sms_charges' => floatval($dps->sms_charges),
                ];
            } else {
                if ($dps->type == 0) {
                    $charges = [
                        'faf_charges' => $LogChargeStatus ? floatval($dps->faf_charges) : 0,
                        'weight_charges' => $LogChargeStatus ? floatval($dps->weight_charges) : 0,
                        'fuel_surcharge' => $LogChargeStatus ? floatval($dps->fuel_surcharge) : 0,
                        'cash_handling_charges' => floatval($dps->cash_handling_charges),
                        'insurance_charges' => floatval($dps->insurance_charges),
                        'replacement_charges' => floatval($dps->replacement_charges),
                        'try_and_buy_charges' => floatval($dps->try_and_buy_charges),
                        'intercept_charges' => floatval($dps->intercept_charges),
                        'non_service_area_charges' => floatval($dps->nsa_osa_charges),
                        'esc_charges' => floatval($dps->esc_charges),
                        'gst_charges' => floatval($dps->gst),
                        'sms_charges' => floatval($dps->sms_charges),
                        'reverse_pickup_charges' => floatval($service_charges),
                    ];
                } elseif ($dps->type == 1) {
                    $charges = [
                        'faf_charges' => $LogChargeStatus ? floatval($dps->faf_charges) : 0,
                        'weight_charges' => $LogChargeStatus ? floatval($dps->weight_charges) : 0,
                        'fuel_surcharge' => $LogChargeStatus ? floatval($dps->fuel_surcharge) : 0,
                        'insurance_charges' => floatval($dps->insurance_charges),
                        'return_charges' => floatval($dps->return_charges),
                        'intercept_charges' => floatval($dps->intercept_charges),
                        'non_service_area_charges' => floatval($dps->nsa_osa_charges),
                        'gst_charges' => floatval($dps->gst),
                        'sms_charges' => floatval($dps->sms_charges),
                    ];
                }
            }

            $oldDetails = json_decode($dps->details, true);
            $oldCharges = $oldDetails['charges'] ?? [];

            $missingKeys = [];
            $mismatchedValues = [];

            foreach ($charges as $key => $value) {
                if (!array_key_exists($key, $oldCharges)) {
                    $missingKeys[] = $key;
                } elseif (round(floatval($oldCharges[$key]), 2) !== round(floatval($value), 2)) {
                    $mismatchedValues[$key] = [
                        'old' => floatval($oldCharges[$key]),
                        'new' => floatval($value),
                    ];
                }
            }

            $extraKeysInOld = array_diff_key($oldCharges, $charges);

            // New logic for totals and difference
            $oldTotal = round(array_sum(array_map('floatval', $oldCharges)), 2);
            $newTotal = round(array_sum(array_map('floatval', $charges)), 2);
            $diffAmount = round($newTotal - $oldTotal, 2);

            $hasKeyDiscrepancy = false;
            foreach (['faf_charges', 'weight_charges', 'fuel_surcharge'] as $key) {
                if (array_key_exists($key, $mismatchedValues)) {
                    $hasKeyDiscrepancy = true;
                    break;
                }
            }
            if ($hasKeyDiscrepancy) {

                $ArrivalIssuePayload[$shipmentId] = [
                    "client_id" => $dps->user_id,
                    "wallet_id" => $dps->wallet_id,
                    "reference_id" => $dps->id,
                    "shipment_id" => $dps->tracking_number,
                    "amount" => $dps->type == 0 ? $dps->amount : 0,
                    "charges" => $charges,
                    'wallet_log_updated' => $dps->wallet_log_updated,
                    'dps_id' => $dps->id,
                    'dps_type' => $dps->type,
                    'missing_keys_in_old' => $missingKeys,
                    'mismatched_values' => $mismatchedValues,
                    'extra_keys_in_old' => array_keys($extraKeysInOld),
                    'old_total' => $oldTotal,
                    'new_total' => $newTotal,
                    'difference' => $diffAmount,
                    'payable' => $dps->payable2,
                    'arrival_charges_issue' => $hasKeyDiscrepancy,
                ];
            } else {
                $OtherIssuePayload[$shipmentId] = [
                    "client_id" => $dps->user_id,
                    "wallet_id" => $dps->wallet_id,
                    "reference_id" => $dps->id,
                    "shipment_id" => $dps->tracking_number,
                    "amount" => $dps->type == 0 ? $dps->amount : 0,
                    "charges" => $charges,
                    'wallet_log_updated' => $dps->wallet_log_updated,
                    'dps_id' => $dps->id,
                    'dps_type' => $dps->type,
                    'missing_keys_in_old' => $missingKeys,
                    'mismatched_values' => $mismatchedValues,
                    'extra_keys_in_old' => array_keys($extraKeysInOld),
                    'old_total' => $oldTotal,
                    'new_total' => $newTotal,
                    'difference' => $diffAmount,
                    'payable' => $dps->payable2,
                    'arrival_charges_issue' => $hasKeyDiscrepancy,
                ];
            }
        }

        self::generateExcelFile($ArrivalIssuePayload,$OtherIssuePayload);
    }
    static function generateExcelFile($ArrivalIssuePayload, $OtherIssuePayload)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $headers = [
            'A1' => 'Client ID',
            'B1' => 'Wallet ID',
            'C1' => 'Reference ID',
            'D1' => 'Shipment ID',
            'E1' => 'Amount',
            'F1' => 'Charges',
            'G1' => 'Wallet Log Updated',
            'H1' => 'DPS ID',
            'I1' => 'DPS Type',
            'J1' => 'Missing Keys In Old',
            'K1' => 'Mismatched Values',
            'L1' => 'Extra Keys In Old',
            'M1' => 'Old Total',
            'N1' => 'New Total',
            'O1' => 'Difference',
            'P1' => 'Payable',
            'Q1' => 'Arrival Charges Issue',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Write data rows
        $row = 2;
        foreach ([$ArrivalIssuePayload, $OtherIssuePayload] as $payload) {
            foreach ($payload as $data) {
                $sheet->setCellValue('A' . $row, $data['client_id'] ?? '');
                $sheet->setCellValue('B' . $row, $data['wallet_id'] ?? '');
                $sheet->setCellValue('C' . $row, $data['reference_id'] ?? '');
                $sheet->setCellValue('D' . $row, $data['shipment_id'] ?? '');
                $sheet->setCellValue('E' . $row, $data['amount'] ?? 0);
                $sheet->setCellValue('F' . $row, json_encode($data['charges'] ?? []));
                $sheet->setCellValue('G' . $row, $data['wallet_log_updated'] ?? '');
                $sheet->setCellValue('H' . $row, $data['dps_id'] ?? '');
                $sheet->setCellValue('I' . $row, $data['dps_type'] ?? '');
                $sheet->setCellValue('J' . $row, json_encode($data['missing_keys_in_old'] ?? []));
                $sheet->setCellValue('K' . $row, json_encode($data['mismatched_values'] ?? []));
                $sheet->setCellValue('L' . $row, json_encode($data['extra_keys_in_old'] ?? []));
                $sheet->setCellValue('M' . $row, $data['old_total'] ?? 0);
                $sheet->setCellValue('N' . $row, $data['new_total'] ?? 0);
                $sheet->setCellValue('O' . $row, $data['difference'] ?? 0);
                $sheet->setCellValue('P' . $row, $data['payable'] ?? 0);
                $sheet->setCellValue('Q' . $row, $data['arrival_charges_issue'] ? 'Yes' : 'No');
                $row++;
            }
        }

        // Save file
        $directory = storage_path('app/public/test2');
        if (!file_exists($directory)) {
            mkdir($directory, 0775, true);
        }

        $fileName = 'shipment_data_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $filePath = $directory . '/' . $fileName;

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }


}
