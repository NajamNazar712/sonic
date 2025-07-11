<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminDashboardController;
use App\Http\Models\Admin\shipmentFintechCharges;
use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\RetailDonePaymentShipment;
use App\Http\Models\RetailPendingPaymentShipment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DonePaymentReportUserExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'done_payment_report_user_excel';

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

        $users = [15151, 15150];

        $years = [2022,2023,2024,2025];
        foreach ($users as $u_id) {

            foreach ($years as $year) {
                $start = Carbon::create($year)->startOfYear()->format('Y-m-d 00:00:00');
                $end = Carbon::create($year)->endOfYear()->format('Y-m-d 23:59:59');

                $done_payments = DonePayment::join('users as u', 'done_payments.user_id', '=', 'u.id')
                    ->join('cities as c', 'u.city_id', '=', 'c.id')
                    ->leftjoin('done_payment_calculations as dpc', 'dpc.done_payment_id', '=', 'done_payments.id')
                    ->leftJoin('admins as ad', function ($join) {
                        $join->on('ad.id', '=', 'done_payments.status_updated_by');
                    })
                    ->leftJoin('user_bank_infos as ubi', function ($join) {
                        $join->on('ubi.id', '=', 'done_payments.user_bank_info_id');
                    })
                    ->leftJoin('user_bank_infos as ubi_default', function ($join) {
                        $join->on('ubi_default.user_id', '=', 'u.id')
                            ->where('ubi_default.id', '=', DB::raw(
                                '(select max(id) from user_bank_infos where user_id = u.id and default_bank = 1)'
                            ));
                    })
                    ->leftJoin('banks_lists as ub', function ($join) {
                        $join->where(function ($sub_query) {
                            $sub_query->whereNotNull('done_payments.user_bank_info_id')
                                ->where('ubi.bank_name', '=', DB::raw('`ub`.`id`'));
                        })->orWhere(function ($sub_query) {
                            $sub_query->whereNull('done_payments.user_bank_info_id')
                                ->where('ubi_default.bank_name', '=', DB::raw('`ub`.`id`'));
                        });
                    })
                    ->leftjoin('banks_lists as b', 'done_payments.company_bank_id', '=', 'b.id')
                    ->join('payment_cycles as pc', 'u.payment_cycle_id', '=', 'pc.id')
                    ->leftjoin('star_shippers as sts', 'sts.user_id', '=', 'u.id')
                    ->leftJoin('sale_person_tags as spt', function ($join) {
                        $join->on('spt.user_id', 'u.id')
                            ->where(
                                'spt.id',
                                '=',
                                DB::raw('(select max(id) from sale_person_tags where sale_person_tags.user_id = u.id and sale_person_tags.status = 0 )')
                            );
                    })
                    //leftJoin to join as admin will always present
                    ->join('admins as sale_admin', 'sale_admin.id', '=', 'spt.admin_id')
                    ->leftjoin('territories as t', 't.id', '=', 'u.territory_id')
                    ->leftjoin('wallet_users as wu', function ($join) {
                        $join->on('wu.user_id', '=', 'u.id')
                            ->where('wu.substitute_user_id', '0');
                    })
                    ->select('done_payments.user_id as user_id', 'done_payments.id as id', 'done_payments.id as payment_id', 'u.name as shipper',
                        'c.name as city', 'u.phone', 'u.phone2', 'u.address', 'done_payments.total_shipments', 'done_payments.delivered_shipments',
                        'done_payments.delivered_shipments as delivered_shipments_count', 'done_payments.returned_shipments',
                        'done_payments.returned_shipments as returned_shipments_count', 'done_payments.adjusted_shipments',
                        'done_payments.adjusted_shipments as adjusted_shipments_count', 'dpc.amount as total_amount', 'dpc.charges as total_charges',
                        'dpc.gst as total_gst', 'dpc.payable as total_payable', 'ub.name as bank', 'done_payments.reference_number',
                        'done_payments.created_at as done_at', 'b.name as company_bank', 'done_payments.status', 'done_payments.ibft_charges',
                        'dpc.packaging_charges', 'dpc.adjustment as adjustment_charges', 'done_payments.status_updated_at as status_updated_at',
                        'dpc.wht as total_wht', 'done_payments.created_at as start_date', 'done_payments.updated_at as end_date', 'ad.name as admin_name',
                        'done_payments.updated_at as updated_at', 'sts.status as star_status', 'u.payment_cycle_days as payment_cycle_days', 'pc.name as payment_cycle', 'pc.id as payment_cycle_id', 'dpc.sms_charges as total_sms_charges', 'done_payments.arrival_shipment as arrival_shipment_shipments_count', 'done_payments.arrival_shipment', 'sale_admin.name as sale_person_name', 'wu.id as wallet_user', 'done_payments.is_wallet_payment', 'wu.finova_account_type as finova_account_type', 'dpc.cod_sst as total_cod_sst', 't.name as territory', 'done_payments.tax_status')
                    ->whereBetween('done_payments.created_at', [$start, $end])
                    ->where('done_payments.user_id', $u_id);

                $query = $done_payments->get()->map(function ($done_payment)  {

                    $req = 'done_payments';
                    $fintechCharges = $this->calculate_fintech_charges_bulk($done_payment, $req);
                    return [
                        'Payment ID' => str_pad($done_payment->id, 6, '0', STR_PAD_LEFT),
                        'Account ID' => str_pad($done_payment->user_id, 6, '0', STR_PAD_LEFT),
                        'Shipper' => $done_payment->star_status == 1
                            ? '<p><i class="star_shippers_icon"></i>' . $done_payment->shipper . '</p>'
                            : $done_payment->shipper,
                        'Sale Person' => $done_payment->sale_person_name,
                        'City' => $done_payment->city,
                        'Territory' => $done_payment->territory,
                        'Phone No(s).' => !empty($done_payment->phone2)
                            ? $done_payment->phone . ' - ' . $done_payment->phone2
                            : $done_payment->phone,
                        'Financing Product Type' => match ($done_payment->finova_account_type) {
                            1, 2, 3, 4 => 'Arrival',
                            5 => 'Delivered',
                            0 => 'Basic',
                            default => '-',
                        },
                        'Address' => $done_payment->address,
                        'Total Shipments' => $done_payment->total_shipments + $done_payment->arrival_shipment,
                        'Delivered Shipments' => $done_payment->delivered_shipments ?: 0,
                        'Returned Shipments' => $done_payment->returned_shipments ?: 0,
                        'Adjusted Shipments' => $done_payment->adjusted_shipments ?: 0,
                        'Fintech Charges' => $fintechCharges,
                        'Arrival Shipments' => $done_payment->arrival_shipment ?: 0,
                        'Total Amount' => number_format($done_payment->total_amount, 2),
                        'Total Charges' => number_format($done_payment->total_charges + $done_payment->ibft_charges, 2),
                        'Total GST' => number_format($done_payment->total_gst, 2),
                        'Total WHT' => number_format($done_payment->total_wht, 2),
                        'Total COD SST' => number_format($done_payment->total_cod_sst, 2),
                        'Total Per SMS Charges' => number_format($done_payment->total_sms_charges, 2),
                        'Packing Charges' => number_format($done_payment->packaging_charges, 2),
                        'Total Deductable' => number_format(
                            $done_payment->total_charges +
                            $done_payment->total_gst +
                            $done_payment->total_sms_charges +
                            $done_payment->ibft_charges +
                            $done_payment->total_wht +
                            $done_payment->total_cod_sst,
                            2
                        ),
                        'Ibft Charges' => number_format($done_payment->ibft_charges, 2),
                        'Adjustment Charges' => number_format($done_payment->adjustment_charges, 2),
                        'Total Payable' => number_format(round($done_payment->total_payable - $done_payment->ibft_charges, 0, PHP_ROUND_HALF_DOWN)),
                        'Bank' => $done_payment->bank,
                        'Reference No.' => $done_payment->reference_number,
                        'Done Datetime' => $done_payment->done_at,
                        'Company Bank' => $done_payment->company_bank,
                        'Payment Cycle' => $done_payment->payment_cycle,
                        'Payment Cycle Days' => $this->formatPaymentCycleDays($done_payment->payment_cycle_id, $done_payment->payment_cycle_days),
                        'Status' => match ($done_payment->status) {
                            0 => 'Processed',
                            1 => 'Paid',
                            2 => 'Reverted',
                            3 => 'Settlement Requested',
                            default => 'Unknown',
                        },
                        'Paid / Reverted Datetime' => !empty($done_payment->status_updated_at)
                            ? $done_payment->status_updated_at
                            : '-',
                    ];
                });


                if (count($query) > 0) {
                    $data = $query->toArray();  // Assuming $sales is your mapped collection

                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();

                    $headings = array_keys($data[0]); // Get headings from the first row keys
                    $sheet->fromArray($headings, null, 'A1');

                    $sheet->fromArray($data, null, 'A2');

                    $writer = new Xlsx($spreadsheet);
                    $tempFile = tempnam(sys_get_temp_dir(), 'done_payment_excel') . '.xlsx';
                    $writer->save($tempFile);

                    $filePath = 'done_payment_excel/' . $u_id . '/done_payment_excel' . $year . '.xlsx'; // Fixed file name
                    Storage::disk('public')->put($filePath, file_get_contents($tempFile));

                    unlink($tempFile);

                    echo $filePath;
                }
            }

        }


    }

    function calculate_fintech_charges_bulk($shipments, $req)
    {
        if ($req == 'pending_payments') {
            $fintech = PendingPaymentShipment::where('pending_payment_id', $shipments->id)->where('type', 1)->pluck('shipment_id')->toArray();
        } else if ($req == 'retail_pending_payments') {
            $fintech = RetailPendingPaymentShipment::where('retail_pending_payment_id', $shipments->id)->where('type', 1)->pluck('shipment_id')->toArray();
        } else if ($req == 'retail_payment_done') {
            $fintech = RetailDonePaymentShipment::where('retail_done_payment_id', $shipments->id)->where('type', 2)->pluck('shipment_id')->toArray();
        } else {
            $fintech = DonePaymentShipment::where('done_payment_id', $shipments->id)->where('type', 0)->pluck('shipment_id')->toArray();
        }
        $totalSum = 0;

        if (!empty($fintech)) {
            $chunks = array_chunk($fintech, 1000); // Adjust chunk size if needed

            foreach ($chunks as $chunk) {
                $totalSum += ShipmentFintechCharges::whereIn('shipment_id', $chunk)
                    ->where('applied_to', 1)
                    ->sum('fintech_charges');
            }
        }

        return $totalSum > 0 ? number_format($totalSum, 2) : 0;

        // $values = shipmentFintechCharges::whereIn('shipment_id', $fintech)->where('applied_to', 1)->sum('fintech_charges');

        // if ($values > 0) {
        //     return number_format($values, 2);
        // } else {
        //     return 0;
        // }
    }

    private function formatPaymentCycleDays($payment_cycle_id, $payment_cycle_days)
    {
        $dayMap = AdminDashboardController::$paymentCycleDays;

        if (in_array($payment_cycle_id, [2, 4, 5])) { // Weekly, Twice a Week, Thrice a Week
            $days = explode(',', $payment_cycle_days);
            return $this->getCycleText($days, $dayMap);
        }

        if (in_array($payment_cycle_id, [3, 6]) && $payment_cycle_days !== '0') { // Monthly, Fortnightly
            $days = explode(',', $payment_cycle_days);

            if (count($days) === 1) {
                return $this->getDayOfMonthText((int)$days[0]);
            } elseif (count($days) === 2) {
                return $this->getDayOfMonthText((int)$days[0]) . ' And ' . $this->getDayOfMonthText((int)$days[1]);
            }
        }

        if ($payment_cycle_id == 1) { // Daily
            return '-';
        }

        return '-';
    }

    static public function getDayOfMonthText($day) {
        if ($day % 100 >= 11 && $day % 100 <= 13) {
            return $day . 'th';
        } else {
            switch ($day % 10) {
                case 1:
                    return $day . 'st';
                case 2:
                    return $day . 'nd';
                case 3:
                    return $day . 'rd';
                default:
                    return $day . 'th';
            }
        }
    }

    static public function getCycleText($days, $dayMap, $cycleSuffix = 'Of The Week') {
        $dayNames = [];

        foreach ($days as $day) {
            if (array_key_exists($day, $dayMap)) {
                $dayName = $dayMap[$day];
                $dayNames[] = $dayName;
            }
        }

        if (count($dayNames) === 0) {
            return '-';
        } elseif (count($dayNames) === 1) { //weekly
            return "Every $dayNames[0] $cycleSuffix";
        } elseif (count($dayNames) === 2) { //twice a day
            return "Every $dayNames[0] and $dayNames[1] $cycleSuffix";
        } else { //thrice a week
            return "Every " . implode(', ', $dayNames) . " $cycleSuffix";
        }
    }

}
