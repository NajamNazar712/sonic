<?php

namespace App\Http\Controllers\Admins;

use App\Console\Commands\DonePaymentReport;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\MasterCargo\Bag;
use App\Http\Models\Admin\MasterCargo\BagShipment;
use App\Http\Models\Admin\MasterCargo\MasterCargoBag;
use App\Http\Models\Admin\PettyCashStatement;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Admin\SalePersonTarget;
use App\Http\Models\Admin\StationDepositNote;
use App\Http\Models\CargoConsignment;
use App\Http\Models\CargoConsignmentShipment;
use App\Http\Models\City;
use App\Http\Models\CRM\CrmTatHolidays;
use App\Http\Models\DailyFakeStatus;
use App\Http\Models\DonePaymentCalculation;
use App\Http\Models\Excel_reports\DonePaymentsReport;
use App\Http\Models\Excel_reports\HubWiseSplit;
use App\Http\Models\Excel_reports\MonthAverage;
use App\Http\Models\Excel_reports\NotAttemptedShipmentAging;
use App\Http\Models\Excel_reports\QaReportPettyCash;
use App\Http\Models\Excel_reports\SalePersonNumbers;
use App\Http\Models\Holiday;
use App\Http\Models\OvernightOverlandReportData;
use App\Http\Models\OvernightOverlandReportOriginHubs;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\UserBankInfo;
use App\Http\Models\HR\Employee;
use App\Http\Models\Zone;
use App\Http\Models\Notification;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Excel_reports\RetailDonePaymentsReport;
use App\Http\Models\RetailDonePaymentCalculation;
use App\Mail\ReportsEmail;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPExcel_Style_Fill;
use PHPExcel_Cell;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\DeliveryLocationMappingKeyword;
use App\Http\Models\Rider;
use App\Http\Models\Admin\TraxPayTransaction;

class AdminReportsEmailController extends Controller
{
    static public function sale_person_numbers($date)
    {
        $date_from = Carbon::createFromFormat("Y-m-d H:i:s", $date)->format('Y-m-d 06:00A');
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s", $next_day)->format('Y-m-d 05:59A');
        $revenue = array();
        $avg_revenue = array();
        $contribution = array();
        $sale_person_array = array();
        $sale_person_shipments = SalePersonTag::leftjoin('admins as a', 'a.id', '=', 'sale_person_tags.admin_id')->leftjoin('shipments as s', 's.user_id', '=', 'sale_person_tags.user_id')->leftjoin('shipments_journey as sj', 'sj.shipment_id', '=', 's.id')->select('a.id as admin_id', 'a.name as admin', DB::raw('count(s.id) as shipment_count'), DB::raw('sum(s.weight_charges) as weight_charges'), DB::raw('sum(s.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(s.insurance_charges) as insurance_charges'), DB::raw('sum(s.return_charges) as return_charges'), DB::raw('sum(s.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(s.replacement_charges) as replacement_charges'), DB::raw('sum(s.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(s.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(s.intercept_charges) as intercept_charges'), DB::raw('sum(s.nsa_osa_charges) as nsa_osa_charges'))->groupBy('sale_person_tags.admin_id')->where('sale_person_tags.status', 0)->where('s.packaging_material_request', 0)->where('sj.shipper_status_id', 2)->whereBetween('sj.created_at', [$date_from, $date_to])->get();
        $walkin = GlobalSettings::where('type', 'Walk-In')->first();
        $walk_in_shipments = Shipment::leftjoin('shipments_journey as sj', 'sj.shipment_id', '=', 'shipments.id')
            ->select(DB::raw('count(shipments.id) as shipment_count'), DB::raw('sum(shipments.weight_charges) as weight_charges'), DB::raw('sum(shipments.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(shipments.insurance_charges) as insurance_charges'), DB::raw('sum(shipments.return_charges) as return_charges'), DB::raw('sum(shipments.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(shipments.replacement_charges) as replacement_charges'), DB::raw('sum(shipments.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(shipments.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(shipments.intercept_charges) as intercept_charges'), DB::raw('sum(shipments.nsa_osa_charges) as nsa_osa_charges'))->where('shipments.user_id', $walkin->setting_value)->where('sj.shipper_status_id', 2)->whereBetween('sj.created_at', [$date_from, $date_to])->get();

        foreach ($sale_person_shipments as $sale_person_shipment) {
            $revenue[$sale_person_shipment->admin_id] = $sale_person_shipment->weight_charges + $sale_person_shipment->cash_handling_charges + $sale_person_shipment->insurance_charges + $sale_person_shipment->return_charges + $sale_person_shipment->fuel_surcharge + $sale_person_shipment->replacement_charges + $sale_person_shipment->try_and_buy_charges + $sale_person_shipment->packaging_material_charges + $sale_person_shipment->intercept_charges + $sale_person_shipment->nsa_osa_charges;

            $avg_revenue[$sale_person_shipment->admin_id] = $revenue[$sale_person_shipment->admin_id] / $sale_person_shipment->shipment_count;
        }
        foreach ($walk_in_shipments as $walk_in_shipment) {
            $revenue[0] = $walk_in_shipment->weight_charges + $walk_in_shipment->cash_handling_charges + $walk_in_shipment->insurance_charges + $walk_in_shipment->return_charges + $walk_in_shipment->fuel_surcharge + $walk_in_shipment->replacement_charges + $walk_in_shipment->try_and_buy_charges + $walk_in_shipment->packaging_material_charges + $walk_in_shipment->intercept_charges + $walk_in_shipment->nsa_osa_charges;
            if ($walk_in_shipment->shipment_count != 0) {
                $avg_revenue[0] = $revenue[0] / $walk_in_shipment->shipment_count;
            } else {
                $avg_revenue[0] = 0;
            }
        }
        $total_revenue = 0;
        foreach ($revenue as $rev) {
            $total_revenue = $total_revenue + $rev;
        }
        foreach ($revenue as $index => $con_rev) {
            if ($total_revenue != 0) {
                $contribution[$index] = $con_rev / $total_revenue;
            } else {
                $contribution[$index] = 0;
            }
        }

        $sale_person_array['header'] = ['S. No.','Admin', 'Achieved Shipments', 'Target Shipments', 'Target Achieved %', 'Achieved Revenue','Target Revenue', 'Target Revenue Achieved %', 'Avg Revenue/Parcel', 'Contribution'];
        $serial = 1;

        $total_shipments_count = 0;
        $total_avg_revenue_count = 0;
        $total_contribution_count = 0;
        $total_target_shipments = 0;
        $total_target_shipments_achieved = 0;
        $total_target_revenue = 0;
        $total_target_revenue_avg = 0;
        $total_revenue_achieved = 0;
        $total_target_revenue_achieved = 0;
        SalePersonNumbers::truncate();
        foreach ($sale_person_shipments as $sale_person_shipment) {
            $all_shipments_target_revenue = 0;
            $target_shipments = 0;
            $target_shipments_achieved = 0;
            $target_revenue = 0;
            $target_revenue_achieved = 0;
            $target = SalePersonTarget::where('sales_person_id', $sale_person_shipment->admin_id);
            if ($target->exists()) {
                $target = $target->first();
                $target_shipments = $target->target_days;
                if ($target_shipments > 0) {
                    $target_shipments_achieved = ($sale_person_shipment->shipment_count / $target_shipments) * 100;
                }
                $target_revenue = $target->average_revenue;
                if ($target_revenue > 0) {
                    $all_shipments_target_revenue = $target_revenue * $target_shipments;
                    $total_target_revenue_avg += $all_shipments_target_revenue;
                    if($all_shipments_target_revenue > 0){
                        $target_revenue_achieved = ($revenue[$sale_person_shipment->admin_id] / $all_shipments_target_revenue) * 100;
                    }else{
                        $target_revenue_achieved = 0;
                    }
                }

                $target->achieved_shipments = $sale_person_shipment->shipment_count;
                $target->achieved_shipments_percentage = $target_shipments_achieved;
                $target->achieved_revenue = $revenue[$sale_person_shipment->admin_id];
                $target->achieved_revenue_percentage = $target_revenue_achieved;
                $target->save();
            }
            $sale_person_array[] = ['serial' => $serial, 'Admin' => $sale_person_shipment->admin, 'Achieved Shipments' => $sale_person_shipment->shipment_count, 'Target Shipments' => $target_shipments, 'Target Achieved %' => round($target_shipments_achieved, 2).'%', 'Achieved Revenue' => $revenue[$sale_person_shipment->admin_id], 'Target Revenue' => $all_shipments_target_revenue, 'Target Revenue Achieved %' => round($target_revenue_achieved, 2).'%', 'Avg Revenue/Parcel' => round($avg_revenue[$sale_person_shipment->admin_id], 2), 'Contribution' => ($contribution[$sale_person_shipment->admin_id]) * 100];
            $sale_person_entry = new SalePersonNumbers();
            $sale_person_entry->admin_id = $sale_person_shipment->admin_id;
            $sale_person_entry->shipments = $sale_person_shipment->shipment_count;
            $sale_person_entry->revenue = $revenue[$sale_person_shipment->admin_id];
            $sale_person_entry->avg_revenue = $avg_revenue[$sale_person_shipment->admin_id];
            $sale_person_entry->contribution = $contribution[$sale_person_shipment->admin_id] * 100;
            $sale_person_entry->target_shipments = $target_shipments;
            $sale_person_entry->target_revenue = $target_revenue;

            $sale_person_entry->save();
            $serial++;
            $total_revenue_achieved += $revenue[$sale_person_shipment->admin_id];
            $total_target_shipments += $target_shipments;
            $total_target_revenue += $target_revenue;



            $total_shipments_count = $total_shipments_count + $sale_person_shipment->shipment_count;
            $total_contribution_count = $total_contribution_count + $contribution[$sale_person_shipment->admin_id];
        }
        if($total_target_shipments > 0){
            $total_target_shipments_achieved = ($total_shipments_count / $total_target_shipments) * 100;
        }

        if($total_target_revenue_avg > 0){
            $total_target_revenue_achieved = ($total_revenue_achieved / $total_target_revenue_avg) * 100;
        }

        foreach ($walk_in_shipments as $walk_in_shipment) {
            $sale_person_array[] = ['serial' => $serial, 'Admin' => 'Walk-In', 'Achieved Shipments' => $walk_in_shipment->shipment_count, 'Target Shipments' => '', 'Target Achieved %' => '', 'Achieved Revenue' => $revenue[0], 'Target Revenue' => '', 'Target Revenue Achieved %' => '', 'Avg Revenue/Parcel' => round($avg_revenue[0], 2), 'Contribution' => $contribution[0] * 100];
            $sale_person_entry = new SalePersonNumbers();
            $sale_person_entry->admin_id = 0;
            $sale_person_entry->shipments = $walk_in_shipment->shipment_count;
            $sale_person_entry->revenue = $revenue[0];
            $sale_person_entry->avg_revenue = $avg_revenue[0];
            $sale_person_entry->contribution = $contribution[0];
            $sale_person_entry->save();
            $serial++;


            $total_shipments_count = $total_shipments_count + $walk_in_shipment->shipment_count;
            $total_contribution_count = $total_contribution_count + $contribution[0];
        }
        if ($total_shipments_count != 0) {
            $total_avg_revenue_count = $total_revenue / $total_shipments_count;
        } else {
            $total_avg_revenue_count = 0;
        }

        $cell_st =[
            'font' =>['bold' => true],
            'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        // $sps_count  = count($sale_person_shipments);
        $sps_count = $serial;
        $ts = "D3:D" . $sps_count;

        $tas = "E3:E" . $sps_count;
        $tr = "G3:G" . $sps_count;
        $tar = "H3:H" . $sps_count;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);

        $sheet->fromArray($sale_person_array, NULL, 'A2', true);
        $sheet->getStyle("A2:J2")->applyFromArray($cell_st);
        // $sheet->getStyle('G')->getFont()->getColor()->setARGB('FFFF00');
        $sheet->getStyle($ts)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFE699');
        $sheet->getStyle($tas)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('C7E0B4');
        $sheet->getStyle($tr)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('FFE699');
        $sheet->getStyle($tar)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('C7E0B4');
        // $sheet->getStyle('H')->getFont()->getColor()->setARGB('00FF00');
        $sheet->setTitle('Sale Person Numbers');
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');

        $file_name_without_path = "reports/sale_person_numbers_report_" . $date_file_name . ".xlsx";
        $file_name = public_path() . "/reports/sale_person_numbers_report_" . $date_file_name . ".xlsx";
        $writer->save($file_name);

        return url('/') . '/' . $file_name_without_path;
    }

    static public function hub_wise_split($date)
    {
        $date_from = Carbon::createFromFormat("Y-m-d H:i:s", $date)->format('Y-m-d 06:00A');
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s", $next_day)->format('Y-m-d 05:59A');
        $total_shipments = 0;
        $ratio = array();
        $avg_actual_weight = array();
        $hub_wise_split_array = array();

        $hub_wise_splits = City::leftjoin('cities as h', 'h.id', '=', 'cities.hub_id')
            ->leftjoin('shipments as s', function ($join) {
                $join->on('s.consignee_city_id', '=', 'cities.id')
                    ->where('s.packaging_material_request', 0);
            })
            ->leftjoin('user_shipping_infos as usi', 'usi.id','=', 's.pickup_address_id')
            ->leftjoin('cities as oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('shipments_journey as sj', 'sj.shipment_id', '=', 's.id')
            ->select('h.id as hub_id', 'h.name as hub', DB::raw('count(s.id) as shipment_count'), DB::raw('sum(s.actual_weight) as actual_weight'), 'oc.name as origin', 'oc.id as origin_id')
            ->where('sj.shipper_status_id', 2)
            ->whereBetween('sj.created_at', [$date_from, $date_to])
            ->groupBy('oc.id','h.id')
            ->get();
        foreach ($hub_wise_splits as $hub_wise_split) {
            $total_shipments = $total_shipments + $hub_wise_split->shipment_count;
        }
        foreach ($hub_wise_splits as $hub_wise_split) {
            if ($total_shipments != 0) {
                $ratio[$hub_wise_split->origin_id][$hub_wise_split->hub_id] = $hub_wise_split->shipment_count / $total_shipments;
            } else {
                $ratio[$hub_wise_split->origin_id][$hub_wise_split->hub_id] = 0;
            }
            if ($hub_wise_split->shipment_count != 0) {
                $avg_actual_weight[$hub_wise_split->origin_id][$hub_wise_split->hub_id] = $hub_wise_split->actual_weight / $hub_wise_split->shipment_count;
            } else {
                $avg_actual_weight[$hub_wise_split->origin_id][$hub_wise_split->hub_id] = 0;
            }
        }

        $hub_wise_split_array['header'] = ['S. No.', 'Origin', 'Hub', 'Count of Parcels', 'Ratio', 'Actual Weight', 'Avg Actual Weight/Shipment'];
        $serial = 1;

        $total_shipments_count = 0;
        $total_avg_ratio_count = 0;
        $total_actual_weight_count = 0;
        $total_avg_actual_weight_count = 0;
        HubWiseSplit::truncate();
        foreach ($hub_wise_splits as $hub_wise_split) {
            if ($hub_wise_split->actual_weight) {
                $actual_weight = $hub_wise_split->actual_weight;
            } else {
                $actual_weight = 0;
            }
            $hub_wise_split_array[] = ['serial' => $serial, 'Origin' => $hub_wise_split->origin, 'Hub' => $hub_wise_split->hub, 'Count of Parcels' => $hub_wise_split->shipment_count, 'Ratio' => round($ratio[$hub_wise_split->origin_id][$hub_wise_split->hub_id] * 100, 2), 'Actual Weight' => round($actual_weight, 2), 'Avg Actual Weight/Shipment' => round($avg_actual_weight[$hub_wise_split->origin_id][$hub_wise_split->hub_id], 2)];
            $hub_wise_split_entry = new HubWiseSplit();
            $hub_wise_split_entry->origin = $hub_wise_split->origin_id;
            $hub_wise_split_entry->hub_id = $hub_wise_split->hub_id;
            $hub_wise_split_entry->shipments = $hub_wise_split->shipment_count;
            $hub_wise_split_entry->ratio = $ratio[$hub_wise_split->origin_id][$hub_wise_split->hub_id] * 100;
            $hub_wise_split_entry->actual_weight = $actual_weight;
            $hub_wise_split_entry->avg_actual_weight = $avg_actual_weight[$hub_wise_split->origin_id][$hub_wise_split->hub_id];
            $hub_wise_split_entry->save();
            $serial++;


            $total_shipments_count = $total_shipments_count + $hub_wise_split->shipment_count;
            $total_avg_ratio_count = $total_avg_ratio_count + $ratio[$hub_wise_split->origin_id][$hub_wise_split->hub_id];
            $total_actual_weight_count = $total_actual_weight_count + $hub_wise_split->actual_weight;
        }
        if($total_shipments_count <= 0){
            $total_avg_actual_weight_count = 0;
        }
        else{
            $total_avg_actual_weight_count = $total_actual_weight_count / $total_shipments_count;
        }
        $hub_wise_split_array[] = ['serial' => '', 'Origin' => '', 'Hub' => '', 'Count of Parcels' => '', 'Ratio' => '', 'Actual Weight' => '', 'Avg Actual Weight/Shipment' => ''];
        $hub_wise_split_array[] = ['serial' => 'Total', 'Origin' => '', 'Hub' => '', 'Count of Parcels' => $total_shipments_count, 'Ratio' => $total_avg_ratio_count * 100, 'Actual Weight' => round($total_actual_weight_count, 2), 'Avg Actual Weight/Shipment' => round($total_avg_actual_weight_count, 2)];
        $cell_st = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);

        $sheet->fromArray($hub_wise_split_array, NULL, 'A2', true);
        $sheet->getStyle("A2:G2")->applyFromArray($cell_st);
        $sheet->setTitle('Sale Person Numbers');
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');

        $file_name_without_path = "reports/hub_wise_split_report_" . $date_file_name . ".xlsx";
        $file_name = public_path() . "/reports/hub_wise_split_report_" . $date_file_name . ".xlsx";
        $writer->save($file_name);

        return url('/') . '/' . $file_name_without_path;
    }

    static public function month_average($date)
    {
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s", $date);
        $first_day = Carbon::parse($date)->firstOfMonth();
        $last_day = Carbon::parse($date)->lastOfMonth();
        $date_from = Carbon::createFromFormat("Y-m-d H:i:s", $first_day);
        $new_date_from = $date_from;
        $week_holiday_date_from = $first_day->format('Y-m-d');
        $holiday_date_from = $new_date_from->format('Y-m-d');
        $new_date_from = $new_date_from->format('Y-m-d 08:00A');
        $new_date_to = $date_to;
        $week_holiday_date_to = Carbon::yesterday()->format('Y-m-d');
        $holiday_date_to = $new_date_to->format('Y-m-d');
        $new_date_to = $new_date_to->format('Y-m-d 05:59A');
        $total_shipments = 0;
        $revenue = array();
        $avg_revenue = array();
        $avg_shipment = array();
        $month_speed = array();
        $month_average_array = array();
        $avg_revenue_per_day = array();
        $dates = [];
        $total_dates = [];
        $week_holidays = CrmTatHolidays::whereBetween('holiday', [$week_holiday_date_from, $week_holiday_date_to])->count();
        $holiday_to_revamp = Carbon::yesterday()->endOfMonth()->format('Y-m-d');
        $holidays = CrmTatHolidays::whereBetween('holiday', [$holiday_date_from, $holiday_to_revamp])->count();
        while ($date_from->lte($date_to)) {
            if ($date_from->isWeekday() || $date_from->isSaturday()) {
                $dates[] = $date_from->copy()->format('Y-m-d');
            }

            $date_from->addDay();
        }
        while ($first_day->lte($last_day)) {
            if ($first_day->isWeekday() || $first_day->isSaturday()) {
                $total_dates[] = $date_from->copy()->format('Y-m-d');
            }

            $first_day->addDay();
        }
        $weekdays_count = count($dates);
        $weekdays_count = ($weekdays_count - ($week_holidays + 1));
        $total_month_weekdays_count = count($total_dates) - ($holidays);

        $months_average = City::leftjoin('shipments_journey as sj', 'sj.city_id', '=', 'cities.id')
            ->leftjoin('shipments as s', 's.id', '=', 'sj.shipment_id')
            ->select('cities.id as origin_id', 'cities.name as origin', DB::raw('count(s.id) as shipment_count'), DB::raw('sum(s.weight_charges) as weight_charges'), DB::raw('sum(s.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(s.insurance_charges) as insurance_charges'), DB::raw('sum(s.return_charges) as return_charges'), DB::raw('sum(s.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(s.replacement_charges) as replacement_charges'), DB::raw('sum(s.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(s.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(s.intercept_charges) as intercept_charges'), DB::raw('sum(s.nsa_osa_charges) as nsa_osa_charges'))
            ->groupBy('cities.id')
            ->where('cities.pickup', 1)
            ->where('s.packaging_material_request', 0)
            ->where('sj.shipper_status_id', 2)
            ->whereBetween('sj.created_at', [$new_date_from, $new_date_to])
            ->get();

        foreach ($months_average as $month_average) {
            $revenue[$month_average->origin_id] = $month_average->weight_charges + $month_average->cash_handling_charges + $month_average->insurance_charges + $month_average->return_charges + $month_average->fuel_surcharge + $month_average->replacement_charges + $month_average->try_and_buy_charges + $month_average->packaging_material_charges + $month_average->intercept_charges + $month_average->nsa_osa_charges;
            if ($month_average->shipment_count != 0) {
                $avg_revenue[$month_average->origin_id] = $revenue[$month_average->origin_id] / $month_average->shipment_count;
            } else {
                $avg_revenue[$month_average->origin_id] = 0;
            }
            if ($weekdays_count != 0) {
                $avg_shipment[$month_average->origin_id] = $month_average->shipment_count / $weekdays_count;
            } else {
                $avg_shipment[$month_average->origin_id] = 0;
            }
            if ($weekdays_count != 0) {
                $avg_revenue_per_day[$month_average->origin_id] = $revenue[$month_average->origin_id] / $weekdays_count;
            } else {
                $avg_revenue_per_day[$month_average->origin_id] = 0;
            }
            $month_speed[$month_average->origin_id] = $avg_shipment[$month_average->origin_id] * $total_month_weekdays_count;
            $total_shipments = $total_shipments + $month_average->shipment_count;
        }

        $month_average_array['header'] = ['S. No.', 'Origin', 'Total Parcel', 'Revenue', 'Avg Revenue/Parcel', 'Avg Shipments/Day', 'Avg Revenue/Day', 'Month Speed'];
        $serial = 1;

        $total_shipments_count = 0;
        $total_revenue_count = 0;
        $total_avg_revenue_count = 0;
        $total_avg_revenue_per_day_count = 0;
        $total_avg_shipment_count = 0;
        $total_month_speed_count = 0;
        MonthAverage::truncate();
        foreach ($months_average as $month_average) {
            $month_average_array[] = ['serial' => $serial, 'Origin' => $month_average->origin, 'Total Parcel' => $month_average->shipment_count, 'Revenue' => round($revenue[$month_average->origin_id], 2), 'Avg Revenue/Parcel' => round($avg_revenue[$month_average->origin_id], 2), 'Avg Shipments/Day' => round($avg_shipment[$month_average->origin_id], 2), 'Avg Revenue/Day' => round($avg_revenue_per_day[$month_average->origin_id], 2), 'Month Speed' => round($month_speed[$month_average->origin_id], 2)];
            $month_average_entry = new MonthAverage();
            $month_average_entry->origin_id = $month_average->origin_id;
            $month_average_entry->shipments = $month_average->shipment_count;
            $month_average_entry->revenue = round($revenue[$month_average->origin_id], 2);
            $month_average_entry->avg_revenue = round($avg_revenue[$month_average->origin_id], 2);
            $month_average_entry->avg_shipments = round($avg_shipment[$month_average->origin_id], 2);
            $month_average_entry->avg_revenue_per_day = round($avg_revenue_per_day[$month_average->origin_id], 2);
            $month_average_entry->month_speed = round($month_speed[$month_average->origin_id], 2);
            $month_average_entry->save();
            $serial++;


            $total_shipments_count = $total_shipments_count + $month_average->shipment_count;
            $total_revenue_count = $total_revenue_count + $revenue[$month_average->origin_id];
            $total_avg_shipment_count = $total_avg_shipment_count + $avg_shipment[$month_average->origin_id];
            $total_avg_revenue_per_day_count = $total_avg_revenue_per_day_count + $avg_revenue_per_day[$month_average->origin_id];
            $total_month_speed_count = $total_month_speed_count + $month_speed[$month_average->origin_id];
        }
        if($total_shipments_count != 0){
            $total_avg_revenue_count = $total_revenue_count / $total_shipments_count;
        }
        else{
            $total_avg_revenue_count = 0;
        }

        $month_average_array[] = ['serial' => '', 'Origin' => '', 'Total Parcel' => '', 'Revenue' => '', 'Avg Revenue/Parcel' => '', 'Avg Shipments/Day' => '', 'Avg Revenue/Day' => '', 'Month Speed' => ''];
        $month_average_array[] = ['serial' => 'Total', 'Origin' => '', 'Total Parcel' => $total_shipments_count, 'Revenue' => round($total_revenue_count, 2), 'Avg Revenue/Parcel' => round($total_avg_revenue_count, 2), 'Avg Shipments/Day' => round($total_avg_shipment_count, 2), 'Avg Revenue/Day' => round($total_avg_revenue_per_day_count, 2), 'Month Speed' => round($total_month_speed_count, 2)];
        $cell_st = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);

        $sheet->fromArray($month_average_array, NULL, 'A2', true);
        $sheet->getStyle("A2:H2")->applyFromArray($cell_st);
        $sheet->setTitle('Sale Person Numbers');
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');

        $file_name_without_path = "reports/month_average_report_" . $date_file_name . ".xlsx";
        $file_name = public_path() . "/reports/month_average_report_" . $date_file_name . ".xlsx";
        $writer->save($file_name);

        return url('/') . '/' . $file_name_without_path;
    }

    static public function daily_fake_status($date)
    {
        $date_from = Carbon::createFromFormat("Y-m-d H:i:s", $date)->format('Y-m-d 00:00:00');
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s", $date)->format('Y-m-d 23:59:59');
        $rider_data_array = array();
        $delivery_notes = DeliveryNote::leftjoin('delivery_note_shipments as dns', 'dns.delivery_note_id', '=', 'delivery_notes.id')
            ->leftjoin('riders as r', 'r.id', '=', 'delivery_notes.rider_id')
            ->leftjoin('cities as c', 'c.id', '=', 'delivery_notes.hub_id')
            ->select('delivery_notes.id', 'c.zone_id as zone_id', 'delivery_notes.rider_id', 'r.name as rider_name', 'delivery_notes.hub_id', 'delivery_notes.shipments_count', 'delivery_notes.delivered_shipments', DB::raw('(select count(delivery_note_shipments.shipment_id) from delivery_note_shipments where delivery_note_shipments.delivery_note_id = delivery_notes.id and delivery_note_shipments.fake_status = 1 and delivery_note_shipments.fake_status_updated_at between "' . $date_from . '" and "' . $date_to . '") as fake_status_count'))
            ->whereBetween('delivery_notes.updated_at', [$date_from, $date_to])
            ->whereBetween('dns.fake_status_updated_at', [$date_from, $date_to])
            ->groupBy('delivery_notes.id')->get();
        $riders_data = array();
        $hubs = array();
        foreach ($delivery_notes as $delivery_note) {
            if (array_key_exists($delivery_note->hub_id, $riders_data)) {
                if (array_key_exists($delivery_note->rider_id, $riders_data[$delivery_note->hub_id])) {
                    if (array_key_exists($delivery_note->id, $riders_data[$delivery_note->hub_id][$delivery_note->rider_id])) {
                        $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['delivery_note_count'] = $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['delivery_note_count'] + 1;
                        $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['total_shipments'] = $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['total_shipments'] + $delivery_note->shipments_count;
                        $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['undelivered_shipments'] = $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['undelivered_shipments'] + ($delivery_note->shipments_count - $delivery_note->delivered_shipments);
                        $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['fake_status_shipments'] = $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['fake_status_shipments'] + $delivery_note->fake_status_count;
                    } else {
                        $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['zone_id'] = $delivery_note->zone_id;
                        $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['delivery_note_id'] = $delivery_note->id;
                        $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['rider_id'] = $delivery_note->rider_id;
                        $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['name'] = $delivery_note->rider_name;
                        $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['delivery_note_count'] = 1;
                        $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['total_shipments'] = $delivery_note->shipments_count;
                        $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['undelivered_shipments'] = ($delivery_note->shipments_count - $delivery_note->delivered_shipments);
                        $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['fake_status_shipments'] = $delivery_note->fake_status_count;
                    }
                } else {
                    $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['zone_id'] = $delivery_note->zone_id;
                    $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['delivery_note_id'] = $delivery_note->id;
                    $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['rider_id'] = $delivery_note->rider_id;
                    $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['name'] = $delivery_note->rider_name;
                    $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['delivery_note_count'] = 1;
                    $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['total_shipments'] = $delivery_note->shipments_count;
                    $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['undelivered_shipments'] = ($delivery_note->shipments_count - $delivery_note->delivered_shipments);
                    $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['fake_status_shipments'] = $delivery_note->fake_status_count;
                }
            } else {
                $rider_count[$delivery_note->hub_id] = 0;
                $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['zone_id'] = $delivery_note->zone_id;
                $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['delivery_note_id'] = $delivery_note->id;
                $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['rider_id'] = $delivery_note->rider_id;
                $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['name'] = $delivery_note->rider_name;
                $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['delivery_note_count'] = 1;
                $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['total_shipments'] = $delivery_note->shipments_count;
                $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['undelivered_shipments'] = ($delivery_note->shipments_count - $delivery_note->delivered_shipments);
                $riders_data[$delivery_note->hub_id][$delivery_note->rider_id][$delivery_note->id]['fake_status_shipments'] = $delivery_note->fake_status_count;
            }
        }
        DailyFakeStatus::truncate();
        $rider_style = array();
        $rider_inputs = array();
        foreach ($riders_data as $index => $first_rider_data) {
            $rider_inputs[$index] = array();
            $rider_data_array[$index]['header'] = ['Row Label', 'Tracking Number(s)'];
            foreach ($riders_data[$index] as $new_index => $new_rider_data) {
                foreach ($new_rider_data as $latest_index => $rider_data) {
                    if (!in_array($rider_data['rider_id'], $rider_inputs[$index])) {
                        $rider_count[$index]++;
                        $rider_data_array[$index][] = ['Row Label' => $rider_data['name']];
                        $rider_inputs[$index][] = $rider_data['rider_id'];
                        $rider_style[$index][] = $rider_count[$index];
                    }
                    $delivery_note_shipments = DeliveryNoteShipment::leftjoin('shipments as s', 's.id', '=', 'delivery_note_shipments.shipment_id')
                        ->select('s.tracking_number as tracking_number')
                        ->where('delivery_note_id', $rider_data['delivery_note_id'])
                        ->where('delivery_note_shipments.fake_status', 1)
                        ->whereNotNull('delivery_note_shipments.fake_status_updated_at')
                        ->whereBetween('delivery_note_shipments.fake_status_updated_at', [$date_from, $date_to])
                        ->groupBy('s.id')
                        ->get();
                    foreach ($delivery_note_shipments as $delivery_note_shipment) {
                        $rider_count[$index]++;
                        $rider_data_array[$index][] = ['Row Label' => '', 'Tracking Number(s)' => strval($delivery_note_shipment->tracking_number)];
                    }
                    $daily_fake_status = new DailyFakeStatus();
                    $daily_fake_status->zone_id = $rider_data['zone_id'];
                    $daily_fake_status->delivery_note_id = $rider_data['delivery_note_id'];
                    $daily_fake_status->hub_id = $index;
                    $daily_fake_status->rider_id = $rider_data['rider_id'];
                    $daily_fake_status->total_delivery_notes = $rider_data['delivery_note_count'];
                    $daily_fake_status->total_shipments = $rider_data['total_shipments'];
                    $daily_fake_status->total_undelivered_shipments = $rider_data['undelivered_shipments'];
                    $daily_fake_status->total_fake_status_shipments = $rider_data['fake_status_shipments'];
                    $daily_fake_status->save();
                }
            }
            $hubs[] = $index;
        }

        foreach ($hubs as $hub) {
            $hub_name = City::find($hub);
            $cell_st = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getDefaultColumnDimension()->setWidth(40);

            $sheet->fromArray($rider_data_array[$hub], NULL, 'A2', true);
            $sheet->getStyle("A2:B2")->applyFromArray($cell_st);
            $sheet->getStyle("B2:B1000")->getNumberFormat()
                ->setFormatCode(
                    \PHPExcel_Style_NumberFormat::FORMAT_NUMBER
                );
            foreach ($rider_style[$hub] as $rst) {
                $ra = "A" . ($rst + 2) . ":B" . ($rst + 2);

                $sheet->getStyle($ra)
                    ->getFont()
                    ->setBold(true);
                $sheet->getStyle($ra)
                    ->getFill()
                    ->setFillType(\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('CECECE');
            }
            $sheet->setTitle('Fake Status Report');
            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="daily_fake_status_report.xlsx"');
            header('Cache-Control: max-age=0');
            $date_file_name = Carbon::parse($date)->format('Y_m_d');

            $file_name_without_path = "reports/daily_fake_status_report_" . strtolower($hub_name->name) . "_" . $date_file_name . ".xlsx";
            $file_name = public_path() . "/reports/daily_fake_status_report_" . strtolower($hub_name->name) . "_" . $date_file_name . ".xlsx";
            $writer->save($file_name);
            $response = url('/') . '/' . $file_name_without_path;
            NotificationsController::send(53, $hub_name->id, $response);
        }

        $zone_data_array = array();
        $hub_data_array = array();
        $zones_data = Zone::leftjoin('cities as c', 'c.zone_id', '=', 'zones.id')
            ->join('daily_fake_statuses as dfs', 'dfs.hub_id', '=', 'c.id')
            ->select('zones.id as id', 'c.id as hub_id', 'zones.name as zone_name', 'c.name as hub_name', DB::raw('sum(dfs.total_delivery_notes) as total_delivery_notes'), DB::raw('sum(dfs.total_shipments) as total_shipments'), DB::raw('sum(dfs.total_undelivered_shipments) as total_undelivered_shipments'), DB::raw('sum(dfs.total_fake_status_shipments) as total_fake_status_shipments'), 'dfs.delivery_note_id as delivery_note_id')->groupBy('dfs.delivery_note_id')->orderBy('c.id', 'asc')->get();
        $zones = Zone::leftjoin('cities as c', 'c.zone_id', '=', 'zones.id')
            ->join('daily_fake_statuses as dfs', 'dfs.hub_id', '=', 'c.id')->select('zones.id as id', 'zones.name as name')->groupBy('zones.id')->get();
        foreach ($zones as $zone) {
            $count[$zone->id] = 0;
        }
        $style = array();
        $hub_count = 0;
        $hub_style = array();
        $input_hubs = array();
        foreach ($zones_data as $zone_data) {

            if (array_key_exists($zone_data->id, $zone_data_array)) {
                if (!in_array($zone_data->hub_id, $input_hubs)) {
                    $zone_data_array[$zone_data->id][] = ['Row Label' => $zone_data->hub_name, 'Tracking Number(s)' => ''];
                    $count[$zone_data->id]++;
                    $style[$zone_data->id][] = $count[$zone_data->id];
                    $input_hubs[] = $zone_data->hub_id;
                }

                $delivery_note_shipments = DeliveryNoteShipment::leftjoin('shipments as s', 's.id', '=', 'delivery_note_shipments.shipment_id')
                    ->select('s.tracking_number as tracking_number')
                    ->where('delivery_note_id', $zone_data->delivery_note_id)
                    ->where('delivery_note_shipments.fake_status', 1)
                    ->whereNotNull('delivery_note_shipments.fake_status_updated_at')
                    ->whereBetween('delivery_note_shipments.fake_status_updated_at', [$date_from, $date_to])
                    ->groupBy('s.id')
                    ->get();
                foreach ($delivery_note_shipments as $delivery_note_shipment) {
                    $count[$zone_data->id]++;
                    $zone_data_array[$zone_data->id][] = ['Row Label' => '', 'Tracking Number(s)' => strval($delivery_note_shipment->tracking_number)];
                }

            } else {
                $zone_data_array[$zone_data->id]['header'] = ['Row Label', 'Tracking Number(s)'];
                if (!in_array($zone_data->hub_id, $input_hubs)) {
                    $zone_data_array[$zone_data->id][] = ['Row Label' => $zone_data->hub_name, 'Tracking Number(s)' => ''];
                    $count[$zone_data->id]++;
                    $style[$zone_data->id][] = $count[$zone_data->id];
                    $input_hubs[] = $zone_data->hub_id;
                }

                $delivery_note_shipments = DeliveryNoteShipment::leftjoin('shipments as s', 's.id', '=', 'delivery_note_shipments.shipment_id')
                    ->select('s.tracking_number as tracking_number')
                    ->where('delivery_note_id', $zone_data->delivery_note_id)
                    ->where('delivery_note_shipments.fake_status', 1)
                    ->whereNotNull('delivery_note_shipments.fake_status_updated_at')
                    ->whereBetween('delivery_note_shipments.fake_status_updated_at', [$date_from, $date_to])
                    ->groupBy('s.id')
                    ->get();
                foreach ($delivery_note_shipments as $delivery_note_shipment) {
                    $count[$zone_data->id]++;
                    $zone_data_array[$zone_data->id][] = ['Row Label' => '', 'Tracking Number(s)' => strval($delivery_note_shipment->tracking_number)];
                }
            }
        }
        foreach ($zones as $zone) {
            if (array_key_exists($zone_data->id, $zone_data_array)) {
                $cell_st = [
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                    'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
                ];
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getDefaultColumnDimension()->setWidth(40);

                $sheet->fromArray($zone_data_array[$zone->id], NULL, 'A2', true);
                $sheet->getStyle("A2:B2")->applyFromArray($cell_st);
                $sheet->getStyle("B2:B1000")->getNumberFormat()
                    ->setFormatCode(
                        \PHPExcel_Style_NumberFormat::FORMAT_NUMBER
                    );
//                $sheet->getStyle("B")->setWidth(40);
                foreach ($style[$zone->id] as $st) {
                    $a = "A" . ($st + 2) . ":B" . ($st + 2);

                    $sheet->getStyle($a)
                        ->getFont()
                        ->setBold(true);
                    $sheet->getStyle($a)
                        ->getFill()
                        ->setFillType(\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB('CECECE');
                }
                $sheet->setTitle('Fake Status Report');
                $writer = new Xlsx($spreadsheet);

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="daily_fake_status_report.xlsx"');
                header('Cache-Control: max-age=0');
                $date_file_name = Carbon::parse($date)->format('Y_m_d');

                $file_name_without_path = "reports/daily_fake_status_report_" . strtolower($zone->name) . "_" . $date_file_name . ".xlsx";
                $file_name = public_path() . "/reports/daily_fake_status_report_" . strtolower($zone->name) . "_" . $date_file_name . ".xlsx";
                $writer->save($file_name);
                $response = url('/') . '/' . $file_name_without_path;
                NotificationsController::send(54, $zone->id, $response);
            }
        }

        $overall_datas = Zone::leftjoin('cities as c', 'c.zone_id', '=', 'zones.id')
            ->join('daily_fake_statuses as dfs', 'dfs.hub_id', '=', 'c.id')
            ->select('zones.id as id', 'c.id as hub_id', 'zones.name as zone_name', 'c.name as hub_name', DB::raw('sum(dfs.total_delivery_notes) as total_delivery_notes'), DB::raw('sum(dfs.total_shipments) as total_shipments'), DB::raw('sum(dfs.total_undelivered_shipments) as total_undelivered_shipments'), DB::raw('sum(dfs.total_fake_status_shipments) as total_fake_status_shipments'), 'dfs.delivery_note_id as delivery_note_id')->groupBy('dfs.delivery_note_id')->orderBy('hub_id', 'asc')->get();
        $hub_count = 0;
        $hub_style = array();
        $input_hubs = array();
        $hub_data_array['header'] = ['Row Label', 'Tracking Number(s)'];
        foreach ($overall_datas as $overall_data) {
            if (!in_array($overall_data->hub_id, $input_hubs)) {
                $hub_count++;
                $hub_data_array[] = ['Row Label' => $overall_data->hub_name, 'Tracking Number(s)' => ''];
                $hub_style[] = $hub_count;
                $input_hubs[] = $overall_data->hub_id;
            }
            $delivery_note_shipments = DeliveryNoteShipment::leftjoin('shipments as s', 's.id', '=', 'delivery_note_shipments.shipment_id')
                ->select('s.tracking_number as tracking_number')
                ->where('delivery_note_id', $overall_data->delivery_note_id)
                ->where('delivery_note_shipments.fake_status', 1)
                ->whereNotNull('delivery_note_shipments.fake_status_updated_at')
                ->whereBetween('delivery_note_shipments.fake_status_updated_at', [$date_from, $date_to])
                ->groupBy('s.id')
                ->get();
            foreach ($delivery_note_shipments as $delivery_note_shipment) {
                $hub_count++;
                $hub_data_array[] = ['Row Label' => '', 'Tracking Number(s)' => strval($delivery_note_shipment->tracking_number)];
            }
        }


        $cell_st = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(40);

        $sheet->fromArray($hub_data_array, NULL, 'A2', true);
        $sheet->getStyle("A2:B2")->applyFromArray($cell_st);
        $sheet->getStyle("B2:B1000")->getNumberFormat()
            ->setFormatCode(
                \PHPExcel_Style_NumberFormat::FORMAT_NUMBER
            );
        foreach ($hub_style as $hst) {
            $ha = "A" . ($hst + 2) . ":B" . ($hst + 2);

            $sheet->getStyle($ha)
                ->getFont()
                ->setBold(true);
            $sheet->getStyle($ha)
                ->getFill()
                ->setFillType(\Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CECECE');
        }
        $sheet->setTitle('Fake Status Report');
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_fake_status_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');

        $file_name_without_path = "reports/daily_fake_status_report_hubs_" . $date_file_name . ".xlsx";
        $file_name = public_path() . "/reports/daily_fake_status_report_hubs_" . $date_file_name . ".xlsx";
        $writer->save($file_name);

        $response = url('/') . '/' . $file_name_without_path;
        NotificationsController::send(55, $date, $response);
    }

    static public function overnight_overland_cargo($date, $shipping_mode_id)
    {
        $origins = City::where('hub', 1)->where('status', 1)->get();
        $overnight_overland_cargo_array['header'] = ['S. No.', 'Cargo#', 'Origin', 'Destination', 'No. of Parcels', 'Mode of Shipment', 'Vendor', 'Cargo Created Date'];
        $serial = 1;
        $rad_tat = GlobalSettings::where('type', 'rad_tat_overnight')->first();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);
        $now = Carbon::now();
        OvernightOverlandReportData::where('shipping_mode_id', $shipping_mode_id)->delete();
        foreach($origins as $origin){
            $new_date = $date;
            $new_date = $new_date .  ' ' . $origin->cut_off_time;
            $date_to = Carbon::createFromFormat("Y-m-d h:ia", $new_date);
            $date_to = Carbon::createFromFormat("Y-m-d H:i:s", $date_to);
            $origin_hubs = OvernightOverlandReportOriginHubs::where('origin_id', $origin->id)->where('shipping_mode_id', $shipping_mode_id)->pluck('hub_id')->toArray();
            if($shipping_mode_id == 1){
                $shipping_mode = 'Rush';
            }
            else{
                $shipping_mode = 'Saver Plus';
            }
            if(count($origin_hubs) > 0){
                foreach ($origin_hubs as $origin_hub){
                    $cargo_consignments = CargoConsignment::where('origin_hub_id', $origin->id)->where('destination_hub_id', $origin_hub)->where('status_id', 1)->where('type', 1)->where('created_at', '<=', $date_to);
                    if($cargo_consignments->exists()){
                        $cargo_consignments = $cargo_consignments->get();
                        foreach($cargo_consignments as $cargo_consignment){
                            $cargo_consignment_shipments = CargoConsignmentShipment::where('cargo_consignment_id', $cargo_consignment->id)->count();
                            $overnight_overland_cargo_array[] = ['serial' => $serial, 'Cargo#' => str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT), 'Origin' => $origin->name, 'Destination' => $cargo_consignment->destination_hub->name, 'No. of Parcels' => $cargo_consignment_shipments, 'Mode of Shipment' => $shipping_mode, 'Vendor' => $cargo_consignment->transport_mode_vendor->name, 'Cargo Created Date' => $cargo_consignment->created_at];
                            $date_from = Carbon::createFromFormat("Y-m-d H:i:s", $cargo_consignment->created_at);
                            $dates = array();
                            while ($date_from->lte($now)) {
                                if ($date_from->isWeekday() || $date_from->isSaturday()) {
                                    $dates[] = $date_from->copy()->format('Y-m-d');
                                }
                                $date_from->addDay();
                            }
                            $weekdays_count = count($dates);
                            $overnight_overland_cargo_data = new OvernightOverlandReportData();
                            $overnight_overland_cargo_data->cargo_id = $cargo_consignment->id;
                            $overnight_overland_cargo_data->origin_id = $origin->id;
                            $overnight_overland_cargo_data->destination_id = $cargo_consignment->destination_hub_id;
                            $overnight_overland_cargo_data->total_parcels = $cargo_consignment_shipments;
                            $overnight_overland_cargo_data->shipping_mode_id = $shipping_mode_id;
                            $overnight_overland_cargo_data->vendor_id = $cargo_consignment->transport_mode_vendor_id;
                            $overnight_overland_cargo_data->cargo_created_at = $cargo_consignment->created_at;
                            if($weekdays_count > $rad_tat->setting_value){
                                $ha = "A" . ($serial + 2) . ":H" . ($serial + 2);
                                $styleArray = array(
                                    'font'  => array(
                                        'bold'  => true,
                                        'color' => array('rgb' => 'FF0000')
                                    ));
                                $sheet->getStyle($ha)->applyFromArray($styleArray);
                                $overnight_overland_cargo_data->status = 1;
                            }
                            $overnight_overland_cargo_data->save();
                                $serial++;
                        }
                    }
                }
            }
        }
//        usort($overnight_overland_cargo_array, function ($item1, $item2) {
//            return $item2['Cargo Created Date'] <=> $item1['Cargo Created Date'];
//        });

        $cell_st = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];

        $sheet->fromArray($overnight_overland_cargo_array, NULL, 'A2', true);
        $sheet->getStyle("A2:H2")->applyFromArray($cell_st);
        if($shipping_mode_id == 1){
            $sheet->setTitle('Overnight Cargo Report');
        }
        else{
            $sheet->setTitle('Overland Cargo Report');
        }
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::today()->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');

        if($shipping_mode_id == 1){
            $file_name_without_path = "reports/overnight_cargo_report_" . $date_file_name . ".xlsx";
            $file_name = public_path() . "/reports/overnight_cargo_report_" . $date_file_name . ".xlsx";
        }
        else{
            $file_name_without_path = "reports/overland_cargo_report_" . $date_file_name . ".xlsx";
            $file_name = public_path() . "/reports/overland_cargo_report_" . $date_file_name . ".xlsx";
        }
        $writer->save($file_name);

        return url('/') . '/' . $file_name_without_path;
    }

    static public function not_attempted_aging($date){
        $settings = GlobalSettings::where('type', 'not_attempted_cron_time');
        Carbon::setWeekendDays([
            Carbon::SUNDAY,
        ]);
        if($settings->exists()){
            $settings = $settings->first();
            $cut_off_time = $settings->setting_value;
            $time = $settings->setting_value . ':00';
            $formatted_date = Carbon::createFromFormat("Y-m-d H:i:s", $date . " " .$time .":00");
            $from = Carbon::today()->addHour($cut_off_time)->toDateTimeString();
            $formatted_date_from = Carbon::parse($date)->subDays(30)->addHour($cut_off_time)->toDateTimeString();
            $del_formatted_date_from = Carbon::parse($date)->subDays(30)->toDateTimeString();
            $to_cut = Carbon::tomorrow()->addHour($cut_off_time)->subSecond()->toDateTimeString();
            $hubs = City::where('hub', 1)->where('status', 1)->get();
            $from_id = DB::connection('reports')->table('shipments_journey')->select(DB::raw('MIN(id) as id'))->where('verification', 1)->where('created_at', '>=', $formatted_date_from)->first()->id;
            $to_id = DB::connection('reports')->table('shipments_journey')->select(DB::raw('MAX(id) as id'))->where('verification', 1)->where('created_at', '<=', $to_cut)->first()->id;
            $hub_shipments = array();
            $zone_hub_shipments = array();
            NotAttemptedShipmentAging::where('created_at', '<', $del_formatted_date_from)->delete();
            $shipments = array();
            foreach ($hubs as $hub){
                $hub_shipments[$hub->name]['id'] = $hub->id;
                $hub_shipments[$hub->name]['name'] = $hub->name;
                $hub_shipments[$hub->name]['zone_id'] = $hub->zone_id;
                $hub_shipments[$hub->name]['zone'] = $hub->zone->name;
                $hub_shipments[$hub->name]['zero'] = 0;
                $hub_shipments[$hub->name]['one'] = 0;
                $hub_shipments[$hub->name]['two'] = 0;
                $hub_shipments[$hub->name]['three'] = 0;
                $hub_shipments[$hub->name]['four'] = 0;
                $hub_shipments[$hub->name]['five'] = 0;
                $hub_shipments[$hub->name]['six_plus'] = 0;
                $cities_shipments = DB::connection('reports')->table('cities')->join('shipments as s', function($join) {
                    $join->where(function($query) {
                        $query->where('cities.id', '=', DB::connection('reports')->raw('s.consignee_city_id'))
                            ->orWhere(function ($sub_query) {
                                $sub_query->on('cities.id', '=', DB::connection('reports')->raw('(select usii.city_id from user_shipping_infos as usii where usii.id = s.pickup_address_id)'));
                            });
                    });
                })
                    ->join('user_shipping_infos as usi', 'usi.id', '=', 's.pickup_address_id')
                    ->join('cities as pc', 'usi.city_id', '=', 'pc.id')
                    ->leftjoin('cities as sch', 's.consignee_city_id', '=', 'sch.id')
                    ->leftjoin('zone_class_cities as zcc', function($join) {
                        $join->on('pc.zone_id', '=', 'zcc.zone_id')
                            ->on('s.consignee_city_id', '=', 'zcc.city_id');
                    })
                    ->join('shipments_journey as sj', function($join) use ($from_id, $to_id) {
                        $join->on('s.id', '=', 'sj.shipment_id')
                            ->where('sj.id', '=', DB::connection('reports')->raw('(select max(shipments_journey.id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.id >= "' . $from_id . '" and shipments_journey.id <= "' . $to_id . '")'));
                    })
                    ->join('shipments_journey as sja', function($join){
                        $join->on('s.id', '=', 'sja.shipment_id')
                            ->where('sja.id', '=',DB::connection('reports')->raw('(select max(shipments_journeya.id) from shipments_journey as shipments_journeya where shipments_journeya.shipment_id = s.id and shipments_journeya.shipper_status_id = 2)'));
                    })
                    ->select('s.id as shipment_id', 'sja.created_at as arrival_date')
                    ->where(function ($query) use ($cut_off_time, $from){
                        $query->where(function($sub_query){
                            $sub_query->where('cities.id', '=', DB::connection('reports')->raw('s.consignee_city_id'))
                                ->where('sj.shipper_status_id', '=', 7);
                        })
                            ->orWhere(function ($sub_query) use ($cut_off_time, $from) {
                                $sub_query->where(function ($sub_sub_query) use ($cut_off_time, $from) {
                                    $sub_sub_query->where(function ($sub_sub_sub_query){
                                        $sub_sub_sub_query->where(function ($sub_sub_sub_sub_query){
                                            $sub_sub_sub_sub_query->where(function ($sub_sub_sub_sub_sub_query) {
                                                $sub_sub_sub_sub_sub_query->where('usi.city_id', '=', DB::connection('reports')->raw('s.consignee_city_id'))
                                                    ->orWhereNull('zcc.class')
                                                    ->orWhereIn('zcc.class', [0, 1]);
                                            })
                                                ->where(function ($sub_sub_sub_sub_sub_sub_sub_query) {
                                                    $sub_sub_sub_sub_sub_sub_sub_query->where('cities.id', '=', DB::connection('reports')->raw('usi.city_id'))
                                                        ->where('sj.shipper_status_id', '=', 2);
                                                });
                                        });
                                    })
                                        ->where(function ($sub_sub_sub_query) use ($cut_off_time, $from) {
                                            $sub_sub_sub_query->whereRaw('date(`sj`.`created_at`) < date(?)', [$from])
                                                ->orWhere(function ($sub_sub_sub_sub_query) use ($cut_off_time, $from) {
                                                    $sub_sub_sub_sub_query->whereRaw('date(`sj`.`created_at`) = date(?)', [$from])
                                                        ->whereRaw('hour(`sj`.`created_at`) < ?', [$cut_off_time]);
                                                });
                                        });
                                });
                            })
                            ->orWhere(function ($sub_query) use ($cut_off_time, $from) {
                                $sub_query->where('cities.id', '=', DB::connection('reports')->raw('s.consignee_city_id'))
                                    ->whereIn('sj.shipper_status_id', [8, 13])
                                    ->whereRaw('date(`sj`.`created_at`) < date(?)', [$from]);
                            });
                    })
                    ->where('cities.hub_id', $hub->id);

                if($cities_shipments->exists()) {
                    $cities_shipments = $cities_shipments->groupBy('s.id')->get();
                    foreach ($cities_shipments as $cities_shipment) {
                        $arrival_date = Carbon::parse($cities_shipment->arrival_date);
                        $check_arrival_date = Carbon::parse($cities_shipment->arrival_date)->format("Y-m-d");
                        $now_date = Carbon::today()->format("Y-m-d");
                        $holidays = Holiday::whereBetween('holiday', [$check_arrival_date, $now_date])->count();
                        $count_without_holidays = $arrival_date->diffInWeekdays($formatted_date);
                        $count_with_holidays = $count_without_holidays - $holidays;
                        if ($count_with_holidays == 0) {
                            $hub_shipments[$hub->name]['zero']++;
                            $shipments[$hub->name]['id'][$cities_shipment->shipment_id] = 0;
                        } elseif ($count_with_holidays == 1) {
                            $hub_shipments[$hub->name]['one']++;
                            $shipments[$hub->name]['id'][$cities_shipment->shipment_id] = 1;
                        } elseif ($count_with_holidays == 2) {
                            $hub_shipments[$hub->name]['two']++;
                            $shipments[$hub->name]['id'][$cities_shipment->shipment_id] = 2;
                        } elseif ($count_with_holidays == 3) {
                            $hub_shipments[$hub->name]['three']++;
                            $shipments[$hub->name]['id'][$cities_shipment->shipment_id] = 3;
                        } elseif ($count_with_holidays == 4) {
                            $hub_shipments[$hub->name]['four']++;
                            $shipments[$hub->name]['id'][$cities_shipment->shipment_id] = 4;
                        } elseif ($count_with_holidays == 5) {
                            $hub_shipments[$hub->name]['five']++;
                            $shipments[$hub->name]['id'][$cities_shipment->shipment_id] = 5;
                        } elseif ($count_with_holidays >= 6) {
                            $hub_shipments[$hub->name]['six_plus']++;
                            $shipments[$hub->name]['id'][$cities_shipment->shipment_id] = 6;
                        }
                    }
                }
            }
            foreach($hub_shipments as $hub_shipment){
                $not_attempted_shipment_aging = new NotAttemptedShipmentAging();
                $not_attempted_shipment_aging->hub_id = $hub_shipment['id'];
                $not_attempted_shipment_aging->zone_id = $hub_shipment['zone_id'];
                $not_attempted_shipment_aging->zero = $hub_shipment['zero'];
                $not_attempted_shipment_aging->one = $hub_shipment['one'];
                $not_attempted_shipment_aging->two = $hub_shipment['two'];
                $not_attempted_shipment_aging->three = $hub_shipment['three'];
                $not_attempted_shipment_aging->four = $hub_shipment['four'];
                $not_attempted_shipment_aging->five = $hub_shipment['five'];
                $not_attempted_shipment_aging->six_plus = $hub_shipment['six_plus'];
                $not_attempted_shipment_aging->save();

                NotificationsController::send(68, $date, $hub_shipment);

                $zone_hub_shipments[$hub_shipment['zone_id']][$hub_shipment['name']] = $hub_shipment;
            }
            foreach ($zone_hub_shipments as $zone_hub_shipment){
                NotificationsController::send(69, $date, $zone_hub_shipment);
            }
            NotificationsController::send(70, $date, $hub_shipments);
        }
    }

    static public function qa_petty_cash_report(){
        $hubs = City::where('hub', 1)->where('status', 1)->get();
        $qa_report_petty_cash_array['header'] = ['S. No.', 'Hub Name', 'Station Approval', 'Operation Approval', 'Finance Approval'];
        $qa_report_petty_cash_array[] = ['S. No.' => '', 'Hub Name' => '', 'Station Approval' => '', 'Operation Approval' => '', 'Finance Approval' => ''];
        foreach ($hubs as $hub){
            $hub_approvals[$hub->id]['name'] = $hub->name;
            $hub_approvals[$hub->id]['station_approval'] = 0;
            $hub_approvals[$hub->id]['operation_approval'] = 0;
            $hub_approvals[$hub->id]['finance_approval'] = 0;
            $petty_cash_statements = PettyCashStatement::where('hub_id', $hub->id)->get();
            foreach ($petty_cash_statements as $petty_cash_statement){
                if($petty_cash_statement->station_approved_by == null){
                    $hub_approvals[$hub->id]['station_approval']++;
                }
                if($petty_cash_statement->operation_approved_by == null){
                    $hub_approvals[$hub->id]['operation_approval']++;
                }
                if($petty_cash_statement->finance_approved_by == null){
                    $hub_approvals[$hub->id]['finance_approval']++;
                }
            }
        }
        QaReportPettyCash::truncate();

        foreach ($hub_approvals as $index => $hub_approval){
            $qa_report_petty_cash = new QaReportPettyCash();
            $qa_report_petty_cash->hub_id = $index;
            $qa_report_petty_cash->hub_name = $hub_approval['name'];
            $qa_report_petty_cash->station_approval = $hub_approval['station_approval'];
            $qa_report_petty_cash->operation_approval = $hub_approval['operation_approval'];
            $qa_report_petty_cash->finance_approval = $hub_approval['finance_approval'];
            $qa_report_petty_cash->save();
            $qa_report_petty_cash_array[] = ['S. No.' => '', 'Hub Name' => $hub_approval['name'], 'Station Approval' => $hub_approval['station_approval'], 'Operation Approval' => $hub_approval['operation_approval'], 'Finance Approval' => $hub_approval['finance_approval']];
        }

        $cell_st = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);
        $sheet->fromArray($qa_report_petty_cash_array, NULL, 'A2', true);
        $sheet->getStyle("A2:E2")->applyFromArray($cell_st);
        $sheet->setTitle('QA Report Petty Cash');
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="qa_report_petty_cash.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::today()->format('Y_m_d');
        $file_name_without_path = "reports/qa_report_petty_cash_" . $date_file_name . ".xlsx";
        $file_name = public_path() . "/reports/qa_report_petty_cash_" . $date_file_name . ".xlsx";
        $writer->save($file_name);

        return url('/') . '/' . $file_name_without_path;
    }

    static public function outstanding_shipments($start_date, $end_date){
        $shipments = DB::connection('mysql')->table('delivery_note_shipments')->join('shipments as s', 'delivery_note_shipments.shipment_id', '=', 's.id')
            ->join('cities as dc', 's.consignee_city_id', '=', 'dc.id')
            ->join('delivery_notes as delivery_note', 'delivery_note_shipments.delivery_note_id', '=', 'delivery_note.id')
            ->join('riders as rider', 'delivery_note.rider_id', '=', 'rider.id')
            ->join('cities as hc', 'dc.hub_id', '=', 'hc.id')
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->leftJoin('booking_types as bt', 's.booking_type_id', '=', 'bt.id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->leftjoin('shipment_payment_status as sps', 's.payment_status_id', '=' , 'sps.id')
            ->leftjoin('shipments_journey as sj', function($join) {
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.id', '=', DB::connection('mysql')->raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id)'));
            })
            ->leftjoin('shipments_journey as sod', function($join) {
                $join->on('sod.shipment_id', '=', 's.id')
                    ->where('sod.id', '=', DB::connection('mysql')->raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id and shipments_journey.verification = 0 and shipments_journey.reference_1_id = delivery_note.id)'));
            })
            ->leftjoin('shipments_journey as svd', function($join) {
                $join->on('svd.shipment_id', '=', 's.id')
                    ->where('svd.id', '=', DB::connection('mysql')->raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.reference_1_id = delivery_note.id and shipments_journey.shipper_status_id != 5)'));
            })
            ->join('shipments_journey as sjd', function($join) {
                $join->on('sjd.shipment_id', '=', 's.id')
                    ->where('sjd.id', '=', DB::connection('mysql')->raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id AND shipper_status_id IN (14, 16, 30, 36))'));
            })
            ->join('shipment_status as ss', 'sj.shipper_status_id', '=', 'ss.id')
            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'delivery_note_shipments.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->select('s.id', 's.tracking_number', 's.consignee_name as consignee', 's.consignee_address as address', 'dc.name as destination', 'hc.name as hub', 'hc.id as hub_id', 'u.name as shipper', 'bt.booking_type as service_type', 's.amount','s.amount as sum_amount', 'ss.name as current_status', 'sod.created_at as operation_status_date','svd.created_at as verification_status_date', 'sj.remarks', 'delivery_note_shipments.delivery_note_id as dncc', 'delivery_note_shipments.delivery_note_id as dncc_link', 'dnsdn.station_deposit_note_id as sdn', 'dnsdn.station_deposit_note_id as sdn_link', 'sjd.created_at as delivered_at','delivery_note_shipments.status as recovery_status','sps.name as payment_status','rider.name as rider_name', 's.booking_type_id', 'usi.poc','u.id as account_no')
            ->whereIn('delivery_note_shipments.status', [4,5,6,7,8,11])
            ->where('s.booking_type_id', '!=', 4)
            ->whereIn('sj.shipper_status_id', [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46])
            ->where('sjd.created_at', '>=', $start_date)
            ->where('sjd.created_at', '<=', $end_date)
            ->get();

        $serial_overall = 0;
        $outstanding_shipments_array_overall['header'] = ['S. No.', 'Tracking Number', 'Consignee', 'Address', 'Destination', 'Hub', 'Account No.', 'Shipper', 'Service Type', 'Amount', 'Recovery Status', 'Current Status', 'Payment Status', 'Operation Status Date/Time', 'Verification Status Date/Time', 'Rider Name', 'Remarks', 'DNCC', 'SDN', 'Aging'];
        $outstanding_shipments_array_overall[] = ['S. No.' => '', 'Tracking Number' => '', 'Consignee' => '', 'Address' => '', 'Destination' => '', 'Hub' => '', 'Account No.' => '', 'Shipper' => '', 'Service Type' => '', 'Amount' => '', 'Recovery Status' => '', 'Current Status' => '', 'Payment Status' => '', 'Operation Status Date/Time' => '', 'Verification Status Date/Time' => '', 'Rider Name' => '', 'Remarks' => '', 'DNCC' => '', 'SDN' => '', 'Aging' => ''];

        $hubs = City::where('hub', 1)->where('status', 1)->get();
        foreach ($hubs as $hub){
            // $serial[$hub->name] = 0;
            // $outstanding_shipments_array[$hub->name]['header'] = ['S. No.', 'Tracking Number', 'Consignee', 'Address', 'Destination', 'Hub', 'Account No.', 'Shipper', 'Service Type', 'Amount', 'Recovery Status', 'Current Status', 'Payment Status', 'Operation Status Date/Time', 'Verification Status Date/Time', 'Rider Name', 'Remarks', 'DNCC', 'SDN', 'Aging'];
            // $outstanding_shipments_array[$hub->name][] = ['S. No.' => '', 'Tracking Number' => '', 'Consignee' => '', 'Address' => '', 'Destination' => '', 'Hub' => '', 'Account No.' => '', 'Shipper' => '', 'Service Type' => '', 'Amount' => '', 'Recovery Status' => '', 'Current Status' => '', 'Payment Status' => '', 'Operation Status Date/Time' => '', 'Verification Status Date/Time' => '', 'Rider Name' => '', 'Remarks' => '', 'DNCC' => '', 'SDN' => '', 'Aging' => ''];
            if(count($shipments) > 0){
                foreach ($shipments as $shipment){
                    if($hub->id == $shipment->hub_id){
                        // $serial[$hub->name]++;
                        $serial_overall++;
                        if(in_array($shipment->recovery_status, [4,5,6])){
                            $recovery_status = "Outstanding";
                        }else if($shipment->recovery_status == 7){
                            $recovery_status = "Resolved";
                        }else if($shipment->recovery_status == 8){
                            $recovery_status = "Payment Adjusted";
                        }else if($shipment->recovery_status == 11){
                            $recovery_status = "Revert Requested";
                        } else{
                            $recovery_status = "-";
                        }
                        $updated_at = Carbon::parse($shipment->operation_status_date)->startOfDay();

                        $now = Carbon::now()->startOfDay();

                        $aging = $updated_at->diffInDays($now) . 'd';

                        if ($shipment->booking_type_id == 4) {
                            $shipper = $shipment->shipper .' (' . $shipment->poc . ')';
                        }
                        else {
                            $shipper = $shipment->shipper;
                        }

                        if($shipment->sdn != null){
                            $sdn = str_pad($shipment->sdn, 6, '0', STR_PAD_LEFT);
                        }
                        else{
                            $sdn = '-';
                        }
                        // $outstanding_shipments_array[$hub->name][] = ['S. No.' => $serial[$hub->name], 'Tracking Number' => strval($shipment->tracking_number), 'Consignee' => $shipment->consignee, 'Address' => $shipment->address, 'Destination' => $shipment->destination, 'Hub' => $shipment->hub, 'Account No.' => str_pad($shipment->account_no, 6, '0', STR_PAD_LEFT), 'Shipper' => $shipper, 'Service Type' => $shipment->service_type, 'Amount' => $shipment->sum_amount, 'Recovery Status' => $recovery_status, 'Current Status' => $shipment->current_status, 'Payment Status' => $shipment->payment_status, 'Operation Status Date/Time' => $shipment->operation_status_date, 'Verification Status Date/Time' => $shipment->verification_status_date, 'Rider Name' => $shipment->rider_name, 'Remarks' => '', 'DNCC' => str_pad($shipment->dncc, 6, '0', STR_PAD_LEFT), 'SDN' => $sdn, 'Aging' => $aging];

                        $outstanding_shipments_array_overall[] = ['S. No.' => $serial_overall, 'Tracking Number' => strval($shipment->tracking_number), 'Consignee' => $shipment->consignee, 'Address' => $shipment->address, 'Destination' => $shipment->destination, 'Hub' => $shipment->hub, 'Account No.' => str_pad($shipment->account_no, 6, '0', STR_PAD_LEFT), 'Shipper' => $shipper, 'Service Type' => $shipment->service_type, 'Amount' => $shipment->sum_amount, 'Recovery Status' => $recovery_status, 'Current Status' => $shipment->current_status, 'Payment Status' => $shipment->payment_status, 'Operation Status Date/Time' => $shipment->operation_status_date, 'Verification Status Date/Time' => $shipment->verification_status_date, 'Rider Name' => $shipment->rider_name, 'Remarks' => '', 'DNCC' => str_pad($shipment->dncc, 6, '0', STR_PAD_LEFT), 'SDN' => $sdn, 'Aging' => $aging];
                    }
                }
            }
        }
        // foreach ($hubs as $hub){
        //     if(count($outstanding_shipments_array[$hub->name]) > 2){
        //         $cell_st = [
        //             'font' => ['bold' => true],
        //             'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        //             'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        //         ];

        //         $spreadsheet = new Spreadsheet();
        //         $sheet = $spreadsheet->getActiveSheet();
        //         $sheet->getDefaultColumnDimension()->setWidth(20);
        //         $sheet->getStyle("B2:B4000")->getNumberFormat()
        //             ->setFormatCode(
        //                 \PHPExcel_Style_NumberFormat::FORMAT_NUMBER
        //             );
        //         $sheet->fromArray($outstanding_shipments_array[$hub->name], NULL, 'A2', true);
        //         $sheet->getStyle("A2:T2")->applyFromArray($cell_st);
        //         $title = 'Outstanding Shipments ' . $hub->name;
        //         if(strlen($title) > 31){
        //             $title = substr($title, 0, 28);
        //             $title = $title . '...';
        //         }
        //         $sheet->setTitle($title);
        //         $writer = new Xlsx($spreadsheet);
        //         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //         header('Content-Disposition: attachment;filename="outstanding_shipment_report.xlsx"');
        //         header('Cache-Control: max-age=0');
        //         $date_file_name = Carbon::today()->format('Y_m_d');
        //         $file_name_without_path = "reports/outstanding_shipment_report_" . strtolower($hub->name) . "_" . $date_file_name . ".xlsx";
        //         $file_name = public_path() . "/reports/outstanding_shipment_report_" . strtolower($hub->name) . "_"  . $date_file_name . ".xlsx";
        //         $writer->save($file_name);

        //         NotificationsController::send(76, $hub->id, url('/') . '/' . $file_name_without_path);

        //     }
        // }

        if(count($outstanding_shipments_array_overall) > 2){
            $cell_st = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getDefaultColumnDimension()->setWidth(20);
            $sheet->getStyle("B")->getNumberFormat()
                ->setFormatCode(
                    \PHPExcel_Style_NumberFormat::FORMAT_NUMBER
                );
            $sheet->getStyle("C")->getNumberFormat()
            ->setFormatCode(
                \PHPExcel_Style_NumberFormat::FORMAT_TEXT
            );
            $sheet->getStyle("D")->getNumberFormat()
            ->setFormatCode(
                \PHPExcel_Style_NumberFormat::FORMAT_TEXT
            );
            $sheet->fromArray($outstanding_shipments_array_overall, NULL, 'A2', true);
            $sheet->getStyle("A2:T2")->applyFromArray($cell_st);
            $title = 'Outstanding Shipments';
            if(strlen($title) > 31){
                $title = substr($title, 0, 28);
                $title = $title . '...';
            }
            $sheet->setTitle($title);
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="outstanding_shipment_report.xlsx"');
            header('Cache-Control: max-age=0');

            if ($start_date != $end_date) {
                $date_file_name = Carbon::parse($start_date)->format('Y_m_d') . '_' .  Carbon::parse($end_date)->format('Y_m_d');
            }
            else {
                $date_file_name = Carbon::parse($start_date)->format('Y_m_d');
            }

            $file_name_without_path = "reports/outstanding_shipment_report_" . $date_file_name . ".xlsx";
            $file_name = public_path() . "/reports/outstanding_shipment_report_"  . $date_file_name . ".xlsx";
            $writer->setPreCalculateFormulas(false);
            $writer->save($file_name);

            NotificationsController::send(76, 0, url('/') . '/' . $file_name_without_path);
        }
    }

	static public function done_payment($date){
        $done_payments = DonePaymentCalculation::whereDate('created_at', $date);
        DonePaymentsReport::truncate();
        if($done_payments->exists()){
            $total_amount = 0;
            $total_ibft = 0;
            $done_payment_array = array();
            $done_payments_array = array();
            $done_payment_array['header'] = ['S No.', 'Payment ID', 'Shipper Name', 'IBAN Number', 'Amount', 'IBFT Charges'];
            $done_payment_array[] = ['S No.' => '', 'Payment ID' => '', 'Shipper Name' => '', 'IBAN Number' => '', 'Amount' => '', 'IBFT Charges' => ''];
            $done_payments = $done_payments->get();
            $shippers = array();
            $shipper_ids = array();
            $walk_in_shipper = GlobalSettings::where('type', 'Walk-In');
            if($walk_in_shipper->exists()){
                $walk_in_shipper = $walk_in_shipper->first();
                $shipper_ids[] = $walk_in_shipper->setting_value;
            }
            $foc_shippers = GlobalSettings::where('type', 'foc_account_tag');
            if($foc_shippers->exists()){
                $foc_shippers = $foc_shippers->first();
                $foc_account_tags = array_map('intval', explode(',', $foc_shippers->text));
                $shipper_ids = array_merge($shipper_ids, $foc_account_tags);
            }
            $serial = 0;
            foreach ($done_payments as $done_payment) {
                if(!isset($done_payment->done_payment->shipper->id)){
                    continue;
                }
                if (!in_array($done_payment->done_payment->shipper?->id, $shipper_ids)   ) {
                    if (!in_array($done_payment->done_payment->shipper->id, $shippers)) {
                        $shippers[$done_payment->done_payment->shipper->id] = $done_payment->done_payment->shipper->id;
                    }
                    if ($done_payment->done_payment->user_bank_info_id != null) {
                        $iban = $done_payment->done_payment->shipper_bank->iban;
                    } else {
                        $shipper_bank = UserBankInfo::where('user_id', $done_payment->done_payment->shipper->id)->where('default_bank', 1);
                        if ($shipper_bank->exists()) {
                            $shipper_bank = $shipper_bank->first();
                            $iban = $shipper_bank->iban;
                        } else {
                            $shipper_bank = UserBankInfo::where('user_id', $done_payment->done_payment->shipper->id);
                            if ($shipper_bank->exists()) {
                                $shipper_bank = $shipper_bank->first();
                                $iban = $shipper_bank->iban;
                            } else {
                                $iban = '-';
                            }
                        }
                    }
                    $done_payment_report = new DonePaymentsReport();
                    $done_payment_report->payment_id = $done_payment->done_payment_id;
                    $done_payment_report->shipper_id = $done_payment->done_payment->shipper->id;
                    $done_payment_report->shipper_name = $done_payment->done_payment->shipper->name;
                    $done_payment_report->amount = $done_payment->payable;
                    $done_payment_report->ibft_charges = $done_payment->ibft_charges;
                    $done_payment_report->iban_number = $iban;
                    $done_payment_report->save();
                    $serial++;
                    $done_payment_array[] = ['S No.' => $serial, 'Payment ID' => $done_payment->done_payment_id, 'Shipper Name' => $done_payment->done_payment->shipper->name, 'IBAN Number' => $iban, 'Amount' => number_format($done_payment->payable), 'IBFT Charges' => number_format($done_payment->ibft_charges)];
                    $total_amount = $total_amount + $done_payment->payable;
                    $total_ibft = $total_ibft + $done_payment->ibft_charges;
                }
            }
            $done_payments_array['summary_header'] = ['', 'Total Shippers', 'Total Amount'];
            $done_payments_array[] = ['' => '', 'Total Shippers' => '', 'Total Amount' => ''];
            $done_payments_array[] = ['' => '', 'Total Shippers' => count($shippers), 'Total Amount' => number_format($total_amount)];
            $done_payments_array[] = ['' => '', 'Total Shippers' => '', 'Total Amount' => ''];
            $done_payments_array[] = ['' => '', 'Total Shippers' => '', 'Total Amount' => ''];
            $done_payment_array[] = ['S No.' => '', 'Payment ID' => 'Total', 'Shipper Name' => '', 'IBAN Number' => '', 'Amount' => number_format($total_amount), 'IBFT Charges' => number_format($total_ibft)];
            $done_payment_array = array_merge($done_payments_array, $done_payment_array);

            $cell_s = [
                'font' => ['bold' => true],
                'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => array(
                    'outline' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => array('argb' => '000000'),
                    ),
                ),
            ];

            $cell_st = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getDefaultColumnDimension()->setWidth(20);
            $sheet->fromArray($done_payment_array, NULL, 'A2', true);
            $sheet->getStyle("B2:C4")->applyFromArray($cell_s);
            $sheet->getStyle("A7:F7")->applyFromArray($cell_st);
            $date_file_name = Carbon::now()->format('Y_m_d_s');
            $sheet->setTitle('Done Payments ' . $date_file_name);
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="done_payment_report.xlsx"');
            header('Cache-Control: max-age=0');
            $file_name_without_path = "reports/done_payment_report_" . $date_file_name . ".xlsx";
            $file_name = public_path() . "/reports/done_payment_report_" . $date_file_name . ".xlsx";
            $writer->save($file_name);
            $to = ['anas.mazhar@logiserves.com', 'aftab.qidwai@trax.pk'];
            $mail = Mail::to($to);
            $link = '<a href="' .  url('/') . '/' .$file_name_without_path . '" target="_blank">Report</a>';
            $mail->send(new ReportsEmail("Done Payment",'Done Payment Report Link '.' '.$link,null));
            
            NotificationsController::send(82, $date, url('/') . '/' . $file_name_without_path);
        }
    }



    static public function short_received_report_hub_wise($hub_ids){
        $today = Carbon::today();
        foreach ($hub_ids as $hub_id){
            $hub = City::find($hub_id);
            $serial = 1;
            $short_received_array = array();
            $short_received_array['header'] = ['S No.', 'Shipments', 'Bag No.', 'Master Cargo No.', 'Origin', 'Destination', 'Short Received Date'];
            $short_received_array[] = ['S No.' => '', 'Shipments' => '', 'Bag No.' => '', 'Master Cargo No.' => '', 'Origin' => '', 'Destination' => '', 'Short Received Date' => ''];
            $short_received_bags = Bag::where('origin_hub_id', $hub_id)->where('status_id', 7)->where('short_received', '>', 0)->whereDate('received_at', $today);
            if($short_received_bags->exists()){
                $short_received_bags = $short_received_bags->get();
                foreach ($short_received_bags as $bag) {
                    $bag_shipments = BagShipment::where('bag_id', $bag->id)->where('status', 0);
                    if($bag_shipments->exists()){
                        $bag_shipments = $bag_shipments->get();
                        foreach ($bag_shipments as $bag_shipment){
                            $master_cargo_bag = MasterCargoBag::where('bag_id', $bag->id)->latest()->first();
                            $tracking_number = $bag_shipment->shipment->tracking_number;
                            $seal_number = $bag->seal_number;
                            $origin_hub = $bag->origin_hub->name;
                            $destination_hub = $bag->destination_hub->name;
                            $received_at = $bag->received_at;
                            $short_received_array[] = ['S No.' => $serial, 'Shipments' => $tracking_number, 'Bag No.' => $seal_number, 'Master Cargo No.' => str_pad($master_cargo_bag->master_cargo_id, 6, '0', STR_PAD_LEFT), 'Origin' => $origin_hub, 'Destination' => $destination_hub, 'Short Received Date' => $received_at];
                            $serial++;
                        }
                    }
                }
                if($serial > 1){

                    $cell_st = [
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                        'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
                    ];
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->getDefaultColumnDimension()->setWidth(20);
                    $sheet->getStyle("B2:B4000")->getNumberFormat()
                        ->setFormatCode(
                            \PHPExcel_Style_NumberFormat::FORMAT_NUMBER
                        );
                    $sheet->fromArray($short_received_array, NULL, 'A2', true);
                    $sheet->getStyle("A2:G2")->applyFromArray($cell_st);
                    $title = 'Short Received Shipments ' . $hub->name;
                    if(strlen($title) > 31){
                        $title = substr($title, 0, 28);
                        $title = $title . '...';
                    }
                    $sheet->setTitle($title);
                    $writer = new Xlsx($spreadsheet);
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="outstanding_shipment_report.xlsx"');
                    header('Cache-Control: max-age=0');
                    $date_file_name = Carbon::today()->format('Y_m_d');
                    $file_name_without_path = "reports/short_received_shipment_report_" . strtolower($hub->name) . "_" . $date_file_name . ".xlsx";
                    $file_name = public_path() . "/reports/short_received_shipment_report_" . strtolower($hub->name) . "_"  . $date_file_name . ".xlsx";
                    $writer->save($file_name);

                    NotificationsController::send(86, $hub->id, url('/') . '/' . $file_name_without_path);
                }
            }
        }
    }

    static public function outstanding_sdn($date)
    {
        $outstanding_sdn_report_array[] = ['Trax Online Private Limited'];
        $outstanding_sdn_report_array[] = ['Outstanding SDN Report'];
        $outstanding_sdn_report_array['header'] = ['S. No.', 'Hub Name', 'Completed >2days'];
        $outstanding_sdn_report_array[] = ['S. No.' => '', 'Hub Name' => '', 'Completed >2days' => ''];

        $now = Carbon::now();
        $total_number = 0;
        $total = 0;
        $serial = 0;
        $hubs = City::where('hub', 1)->where('status', 1)->get();
        if (count($hubs) > 0) {
            foreach ($hubs as $hub) {
                $total_count = 0;
                $sdn_data[$hub->id]['name'] = $hub->name;
                $sdn_data[$hub->id]['count'] = 0;
                $outstanding_sdn = StationDepositNote::where('hub_id', $hub->id)->where('status', 1)->get();
                foreach ($outstanding_sdn as $sdn) {
                    $start = Carbon::parse($sdn->created_at);
                    $difference = $start->diffInDays($now);
                    if ($difference > 2) {
                        $total_count++;
                    }
                }
                $sdn_data[$hub->id]['count'] += $total_count;
            }

            foreach ($sdn_data as $index => $sdn){
                if($sdn['count'] != 0){
                    $serial++;
                    $total_number += $sdn['count'];
                    $outstanding_sdn_report_array[] = ['S. No.' => $serial, 'Hub Name' =>  $sdn['name'], 'Completed >2days' =>  $sdn['count']] ;
                }
            }
            $outstanding_sdn_report_array[] = ['S. No.' => 'Total Number', 'Hub Name' =>  '', 'Completed >2days' =>  $total_number] ;

            $cell_st = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];
            $serial = $serial + 6;
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getDefaultColumnDimension()->setWidth(20);
            $sheet->fromArray($outstanding_sdn_report_array, NULL, 'A2', true);
            $sheet->getStyle("A2:C2")->applyFromArray($cell_st);
            $sheet->getStyle("A3:C3")->applyFromArray($cell_st);
            $sheet->getStyle("A" . $serial . ":C" . $serial)->applyFromArray($cell_st);
            $sheet->setTitle('Outstanding SDN Report');
            $sheet->mergeCells('A2:C2');
            $sheet->mergeCells('A3:C3');
            $sheet->mergeCells('A' . $serial . ':B' . $serial);
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="outstanding_sdn_report.xlsx"');
            header('Cache-Control: max-age=0');
            $date_file_name = Carbon::today()->format('Y_m_d');
            $file_name_without_path = "reports/outstanding_sdn_report_" . $date_file_name . ".xlsx";
            $file_name = public_path() . "/reports/outstanding_sdn_report_" . $date_file_name . ".xlsx";
            $writer->save($file_name);
            return url('/') . '/' . $file_name_without_path;

        }
    }


    static public function retail_done_payment($date){
//        Log::channel('cronJobLog')->info('s ' .'report:retail Running');
        try{

            $retail_done_payments = RetailDonePaymentCalculation::whereDate('created_at', $date);
            RetailDonePaymentsReport::truncate();
            if($retail_done_payments->exists()){
                $total_amount = 0;
                $retail_done_payment_array = array();
                $retail_done_payments_array = array();
                $retail_done_payment_array['header'] = ['S No.', 'Payment ID', 'Shipper Name', 'IBAN Number', 'Amount'];
                $retail_done_payment_array[] = ['S No.' => '', 'Payment ID' => '', 'Shipper Name' => '', 'IBAN Number' => '', 'Amount' => ''];
                $retail_done_payments = $retail_done_payments->get();
                $shippers = array();
                $serial = 0;
                foreach ($retail_done_payments as $retail_done_payment) {
                    if (!in_array($retail_done_payment->retail_done_payment->shipper->id, $shippers)) {
                        $shippers[$retail_done_payment->retail_done_payment->shipper->id] = $retail_done_payment->retail_done_payment->shipper->id;
                    }
                    if ($retail_done_payment->retail_done_payment->user_bank_info_id != null) {
                        if($retail_done_payment->retail_done_payment->shipper->iban != null){
                            $iban = $retail_done_payment->retail_done_payment->shipper->iban;
                        }else{
                            $iban = '-';
                        }

                    } else {
                        $iban = '-';
                    }
                    $retail_done_payment_report = new RetailDonePaymentsReport();
                    $retail_done_payment_report->payment_id = $retail_done_payment->retail_done_payment_id;
                    $retail_done_payment_report->shipper_id = $retail_done_payment->retail_done_payment->shipper->id;
                    $retail_done_payment_report->shipper_name = $retail_done_payment->retail_done_payment->shipper->shipper_name;
                    $retail_done_payment_report->amount = $retail_done_payment->payable;
                    $retail_done_payment_report->iban_number = $iban;
                    $retail_done_payment_report->save();
                    $serial++;
                    $retail_done_payment_array[] = ['S No.' => $serial, 'Payment ID' => $retail_done_payment->retail_done_payment_id, 'Shipper Name' => $retail_done_payment->retail_done_payment->shipper->shipper_name, 'IBAN Number' => $iban, 'Amount' => number_format($retail_done_payment->payable)];
                    $total_amount = $total_amount + $retail_done_payment->payable;
                }
                $retail_done_payments_array['summary_header'] = ['', 'Total Shippers', 'Total Amount'];
                $retail_done_payments_array[] = ['' => '', 'Total Shippers' => '', 'Total Amount' => ''];
                $retail_done_payments_array[] = ['' => '', 'Total Shippers' => count($shippers), 'Total Amount' => number_format($total_amount)];
                $retail_done_payments_array[] = ['' => '', 'Total Shippers' => '', 'Total Amount' => ''];
                $retail_done_payments_array[] = ['' => '', 'Total Shippers' => '', 'Total Amount' => ''];
                $retail_done_payment_array[] = ['S No.' => '', 'Payment ID' => 'Total', 'Shipper Name' => '', 'IBAN Number' => '', 'Amount' => number_format($total_amount)];
                $retail_done_payment_array = array_merge($retail_done_payments_array, $retail_done_payment_array);
                $cell_s = [
                    'font' => ['bold' => true],
                    'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                    'borders' => array(
                        'outline' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => array('argb' => '000000'),
                        ),
                    ),
                ];
    
                $cell_st = [
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                    'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
                ];
    
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getDefaultColumnDimension()->setWidth(20);
                $sheet->fromArray($retail_done_payment_array, NULL, 'A2', true);
                $sheet->getStyle("B2:C4")->applyFromArray($cell_s);
                $sheet->getStyle("A7:E7")->applyFromArray($cell_st);
                $date_file_name = Carbon::now()->format('Y_m_d_s');
                $sheet->setTitle('Retail Done Payments');
                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="retail_done_payment_report.xlsx"');
                header('Cache-Control: max-age=0');
                $file_name_without_path = "reports/retail_done_payment_report_" . $date_file_name . ".xlsx";
                $file_name = public_path() . "/reports/retail_done_payment_report_" . $date_file_name . ".xlsx";   
                $writer->save($file_name);
                $to = ['anas.mazhar@logiserves.com', 'aftab.qidwai@trax.pk'];
                $mail = Mail::to($to);
                $link = '<a href="' .  url('/') . '/' . $file_name_without_path . '" target="_blank">Report</a>';
                $mail->send(new ReportsEmail("Retail Payment", 'Retail Payment Report Link ' . ' ' . $link, null));
            
                NotificationsController::send(141, $date, url('/') . '/' . $file_name_without_path);
//                Log::channel('cronJobLog')->info('s ' .'report:retail End');
            }
        }catch(\Throwable $th){
            Log::channel('cronJobLog')->info('s ' .'report:retail Failed'. $th->getMessage());
        }
    }

    static public function petty_cash_qa_report()
    {
        $hubs = City::where('hub', 1)->where('status', 1)->get();
        $qa_report_petty_cash_array['header'] = ['S. No.', 'Hub Name', 'Station Approval', 'Operation Approval', 'Finance Approval'];
        $qa_report_petty_cash_array[] = ['S. No.' => '', 'Hub Name' => '', 'Station Approval' => '', 'Operation Approval' => '', 'Finance Approval' => ''];
        $serial=1;
        foreach ($hubs as $hub){
            $hub_approvals[$hub->id]['name'] = $hub->name;
            $hub_approvals[$hub->id]['station_approval'] = 0;
            $hub_approvals[$hub->id]['operation_approval'] = 0;
            $hub_approvals[$hub->id]['finance_approval'] = 0;
            $petty_cash_statements = PettyCashStatement::where('hub_id', $hub->id)->get();
            foreach ($petty_cash_statements as $petty_cash_statement){
                if($petty_cash_statement->station_approved_by == null){
                    $hub_approvals[$hub->id]['station_approval']++;
                }
                if($petty_cash_statement->operation_approved_by == null){
                    $hub_approvals[$hub->id]['operation_approval']++;
                }
                if($petty_cash_statement->finance_approved_by == null){
                    $hub_approvals[$hub->id]['finance_approval']++;
                }

            }
        }
        QaReportPettyCash::truncate();

        foreach ($hub_approvals as $index => $hub_approval){
            $qa_report_petty_cash = new QaReportPettyCash();
            $qa_report_petty_cash->hub_id = $index;
            $qa_report_petty_cash->hub_name = $hub_approval['name'];
            $qa_report_petty_cash->station_approval = $hub_approval['station_approval'];
            $qa_report_petty_cash->operation_approval = $hub_approval['operation_approval'];
            $qa_report_petty_cash->finance_approval = $hub_approval['finance_approval'];
            $qa_report_petty_cash->save();
            $qa_report_petty_cash_array[] = ['S. No.' => $serial, 'Hub Name' => $hub_approval['name'], 'Station Approval' => $hub_approval['station_approval'], 'Operation Approval' => $hub_approval['operation_approval'], 'Finance Approval' => $hub_approval['finance_approval']];
            $serial++;
        }

        $cell_st = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);
        $sheet->fromArray($qa_report_petty_cash_array, NULL, 'A2', true);
        $sheet->getStyle("A2:E2")->applyFromArray($cell_st);
        $sheet->setTitle('QA Report Petty Cash');
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="qa_report_petty_cash.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::today()->format('Y_m_d');
        $file_name_without_path = "reports/qa_report_petty_cash_" . $date_file_name . ".xlsx";
        $file_name = public_path() . "/reports/qa_report_petty_cash_" . $date_file_name . ".xlsx";
        $writer->save($file_name);

        return url('/') . '/' . $file_name_without_path;
    }

    static public function telenor_sales_report($date){

        $date_from = Carbon::createFromFormat("Y-m-d", $date)->toDateString();
        $date_from = $date_from . ' 09:00:00';
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = $next_day->toDateString();
        $date_to = $date_to . ' 08:59:59';
        $serial = 0;
        $sales = DB::connection('reports')->table('shipments')->join('users as u','u.id','=','shipments.user_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('zones as z', 'z.id', '=', 'oc.zone_id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftjoin('zone_class_cities as zcc', function($join){
                $join->on('z.id', '=', 'zcc.zone_id')
                    ->on('dc.id', '=', 'zcc.city_id')
                    ->on('zone_classification_id', '=', DB::connection('reports')->raw('IF (shipments.shipping_mode_id IN (1, 4), 1, 2)'));
            })
            ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->leftjoin('delivery_note_shipments as ds',function($join){
                $join->on('ds.shipment_id','=','shipments.id')
                    ->where('ds.delivery_note_id','=',
                        DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id and delivery_note_shipments.status > 3 and  delivery_note_shipments.status != 8)')                        );
            })
            ->leftjoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->leftJoin('pending_payment_shipments as pps', function ($join) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.id','=',
                        DB::connection('reports')->raw('(select max(id) from pending_payment_shipments where pending_payment_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.id','=',
                        DB::connection('reports')->raw('(select max(id) from done_payment_shipments where done_payment_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37))'));
            })
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipment_items where shipment_items.shipment_id = shipments.id and shipment_items.type = 0)'));
            })
            ->leftjoin('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'shipments.user_id')
                    ->leftjoin('admins as adsp','adsp.id','=','spt.admin_id')
                    ->where('spt.status','=',0);
            })
            ->leftjoin('products as p','p.id','=','si.product_type_id')
            ->leftJoin('pending_invoice_shipments as pis', function ($join) {
                $join->on('pis.shipment_id', '=', 'shipments.id')
                    ->where('pis.id','=',
                        DB::connection('reports')->raw('(select max(id) from pending_invoice_shipments where pending_invoice_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('invoice_shipments as is', function ($join) {
                $join->on('is.shipment_id', '=', 'shipments.id')
                    ->where('is.id','=',
                        DB::connection('reports')->raw('(select max(id) from invoice_shipments where invoice_shipments.shipment_id = shipments.id)'));
            })
            ->select('p.product_name as category','si.description as description','shipments.id as shipment_id','shipments.tracking_number as tracking_number','shipments.order_id as order_id','shipments.tracking_number as tracking_number_link','u.id as account_no','u.name as shipper','ss.name as current_status','bt.booking_type as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','h.name as hub','shipments.amount as s_collection_amount','sps.name as payment_status','pps.amount as p_collection_amount','shipments.actual_weight','shipments.weight_charges','shipments.cash_handling_charges','shipments.insurance_charges','shipments.return_charges','shipments.replacement_charges','shipments.fuel_surcharge','shipments.try_and_buy_charges','shipments.packaging_material_charges','pps.gst as p_gst','pps.charges as p_total_charges','pps.payable as p_net_payable','dps.amount as d_collection_amount','dps.gst as d_gst','dps.charges as d_total_charges','dps.payable as d_net_payable', 'sm.mode as shipping_mode','shipments.chargeable_weight','dr.created_at as delivered_or_returned','z.name as zone','zcc.class', 'oc.id as origin_city_id', 'dc.id as destination_city_id', 'dnsdn.station_deposit_note_id as sdn_id', 'dps.done_payment_id as payment_id', 'shipments.booking_type_id', 'usi.poc', 'adsp.name as sales_person', 'shipments.shipper_status_id as shipment_status', 'shipments.nsa_osa_charges', 'u.account_type_id as account_type_id', 'pis.gst as pis_gst', 'is.gst as is_gst','shipments.packaging_charges', 'dr.received_or_refused_by', 'shipments.special_instructions','shipments.intercept_charges')
            ->whereNotIn('shipments.shipper_status_id',[1,17])
            ->whereBetween('sj.created_at', [$date_from,$date_to])
            ->where('u.id',3324)->get();


        $telenor_sales[] = ['Telenor Sales Report'];
        $telenor_sales['header'] = ['S. No.', 'Tracking Number', 'Account Number','Shipper','Order Id','Status','Payment Status','Payment Id','SDN Number','Service Type','Arrival Date','Origin','Destination','Hub','Zone','Class','Shipping Mode','Category','Description','Collection Amount','Actual Weight','Chargeable Weight','Weight Charges','Cash Handling Charges','Insurance Charges','Packaging Charges','Fuel Surcharge','Return Charges','Replacement Charges','Try & Buy Charges','NSA/OSA Charges','GST','Intercept Charges','Total Charges','Packing Charges','Net Payable','Delivered/Returned Date','Received/Refused By','Sales Person','Special Instructions'];

        $telenor_sales[] = ['S. No.' => '', 'Tracking Number' => '', 'Account Number' => '','Shipper' => '','Order Id' => '','Status' => '','Payment Status' => '','Payment Id' => '','SDN Number' => '','Service Type' => '','Arrival Date' => '','Origin' => '','Destination' => '','Hub' => '','Zone' => '','Class' => '','Shipping Mode' => '','Category' => '','Description' => '','Collection Amount' => '','Actual Weight' => '','Chargeable Weight' => '','Weight Charges' => '','Cash Handling Charges' => '','Insurance Charges' => '','Packaging Charges' => '','Fuel Surcharge' => '','Return Charges' => '','Replacement Charges' => '','Try & Buy Charges' => '','NSA/OSA Charges' => '','GST' => '','Intercept Charges' => '','Total Charges' => '','Packing Charges' => '','Net Payable' => '','Delivered/Returned Date' => '','Received/Refused By' => '','Sales Person' => '','Special Instructions' => ''];

        foreach($sales as $sale){
            $serial++;
            $tracking_number = $sale->tracking_number;
            $account_no = $sale->account_no;
            $shipper = $sale->shipper;
            $order_id = $sale->order_id;
            $current_status = $sale->current_status;
            $payment_status = $sale->payment_status;
            $payment_id = $sale->payment_id;
            $sdn_id = $sale->sdn_id;
            $service_type = $sale->service_type;
            $arrival_date = $sale->arrival_date;
            $origin = $sale->origin;
            $destination = $sale->destination;
            $hub = $sale->hub;
            $zone = $sale->zone;
            $class = $sale->class;
            $shipping_mode = $sale->shipping_mode;
            $category = $sale->category;
            $description = $sale->description;
            $p_collection_amount = $sale->p_collection_amount;
            $actual_weight = $sale->actual_weight;
            $chargeable_weight = $sale->chargeable_weight;
            $weight_charges = $sale->weight_charges;
            $cash_handling_charges = $sale->cash_handling_charges;
            $insurance_charges = $sale->insurance_charges;
            $packaging_material_charges = $sale->packaging_material_charges;
            $fuel_surcharge = $sale->fuel_surcharge;
            $return_charges = $sale->return_charges;
            $replacement_charges = $sale->replacement_charges;
            $try_and_buy_charges = $sale->try_and_buy_charges;
            $nsa_osa_charges = $sale->nsa_osa_charges;
            $p_gst = $sale->p_gst;
            $intercept_charges = $sale->intercept_charges;
            $p_total_charges = $sale->p_total_charges;
            $packaging_charges = $sale->packaging_charges;
            $p_net_payable = $sale->p_net_payable;
            $delivered_or_returned = $sale->delivered_or_returned;
            $received_or_refused_by = $sale->received_or_refused_by;
            $sales_person = $sale->sales_person;
            $special_instructions = $sale->special_instructions;

            if($class == 0){
                $class = 'Class A';
            }
            else if($class == 1){
                $class = 'Class B';
            }
            else if($class == 2){
                $class = 'Class C';
            }
            else if($class == 3){
                $class = 'Class D';
            }
            else{
                $class = 'Local';
            }

            $telenor_sales[] = ['S. No.' => $serial, 'Tracking Number' => $tracking_number, 'Account Number' => $account_no,'Shipper' => $shipper,'Order Id' => $order_id,'Status' => $current_status,'Payment Status' => $payment_status,'Payment Id' => $payment_id,'SDN Number' => $sdn_id,'Service Type' => $service_type,'Arrival Date' => $arrival_date,'Origin' => $origin,'Destination' => $destination,'Hub' => $hub,'Zone' => $zone,'Class' => $class,/*'Attempts' => $attempts,*/'Shipping Mode' => $shipping_mode,'Category' => $category,'Description' => $description,'Collection Amount' => $p_collection_amount,'Actual Weight' => $actual_weight,'Chargeable Weight' => $chargeable_weight,'Weight Charges' => $weight_charges,'Cash Handling Charges' => $cash_handling_charges,'Insurance Charges' => $insurance_charges,'Packaging Charges' => $packaging_material_charges,'Fuel Surcharge' => $fuel_surcharge,'Return Charges' => $return_charges,'Replacement Charges' => $replacement_charges,'Try & Buy Charges' => $try_and_buy_charges,'NSA/OSA Charges' => $nsa_osa_charges,'GST' => $p_gst,'Intercept Charges' => $intercept_charges,'Total Charges' => $p_total_charges,/*'Estimated Charges' => $estimated_charges,*/'Packing Charges' => $packaging_charges,'Net Payable' => $p_net_payable,'Delivered/Returned Date' => $delivered_or_returned,'Received/Refused By' => $received_or_refused_by,'Sales Person' => $sales_person,'Special Instructions' => $special_instructions];

        }
        $cell_st = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);
        $sheet->fromArray($telenor_sales, NULL, 'A2', true);
        $sheet->getStyle("A2:C2")->applyFromArray($cell_st);
        /*$sheet->getStyle("A3:C3")->applyFromArray($cell_st);
        $sheet->getStyle("A" . $serial . ":C" . $serial)->applyFromArray($cell_st);*/
        $sheet->setTitle('Telenor Sales Report');
        $sheet->mergeCells('A2:AP2');
        /*$sheet->mergeCells('A3:C3');
        $sheet->mergeCells('A' . $serial . ':B' . $serial);*/
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outstanding_sdn_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::today()->format('Y_m_d');
        $file_name_without_path = "reports/telenor_sales_report_" . $date_file_name . ".xlsx";
        $file_name = public_path() . "/reports/telenor_sales_report_" . $date_file_name . ".xlsx";
        $writer->save($file_name);
        return url('/') . '/' . $file_name_without_path;

    }

    static public function reverse_pickup_summary($start_date, $end_date){
        $date = Carbon::today()->toDateString();
        $shipments = DB::connection('reports')->table('shipments')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities as oc', 'oc.id', '=', 'usi.city_id')
            ->join('cities as h', 'h.id', '=', 'oc.hub_id')
            ->leftjoin('shipments_journey as sj', function($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=', DB::connection('reports')->raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = shipments.id AND verification = 1)'));
            })
            ->select('shipments.tracking_number as tracking_number', 'h.id as hub_id', 'h.name as hub_name', 'oc.id as origin_id', 'oc.name as origin_name', 'sj.shipper_status_id as status')
            ->where('shipments.booking_type_id', '=', DB::raw(5))
            ->whereBetween('sj.created_at', [$start_date, $end_date])
            ->get();
        $hubs = City::where('hub', 1)->where('status', 1)->get();
        $hub_shipments = array();
        $zone_shipments = array();
        foreach ($hubs as $hub){
            if(count($shipments) > 0){
                foreach ($shipments as $shipment){
                    if($shipment->status == 1 || $shipment->status == 2){
                        if($hub->id == $shipment->hub_id){
                            $hub_shipments[$hub->id][$shipment->origin_id][] = ['tracking_number' => $shipment->tracking_number, 'origin_id' => $shipment->origin_id, 'origin_name' => $shipment->origin_name, 'hub_id' => $hub->id, 'hub_name' => $hub->name];
                            $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id][] = ['tracking_number' => $shipment->tracking_number, 'zone_id' => $hub->zone_id, 'zone_name' => $hub->zone->name, 'origin_id' => $shipment->origin_id, 'origin_name' => $shipment->origin_name, 'hub_id' => $hub->id, 'hub_name' => $hub->name];
//                            if (array_key_exists($hub->id, $hub_shipments)) {
//                                if (array_key_exists($shipment->origin_id, $hub_shipments[$hub->id])) {
//                                    if($shipment->status == 1){
//                                        $hub_shipments[$hub->id][$shipment->origin_id]['pending']++;
//                                    }
//                                    else{
//                                        $hub_shipments[$hub->id][$shipment->origin_id]['picked']++;
//                                    }
//                                }
//                                else{
//                                    if($shipment->status == 1){
//                                        $hub_shipments[$hub->id][$shipment->origin_id]['pending'] = 1;
//                                        $hub_shipments[$hub->id][$shipment->origin_id]['picked'] = 0;
//                                    }
//                                    else{
//                                        $hub_shipments[$hub->id][$shipment->origin_id]['pending'] = 0;
//                                        $hub_shipments[$hub->id][$shipment->origin_id]['picked'] = 1;
//                                    }
//                                    $hub_shipments[$hub->id][$shipment->origin_id]['origin_id'] = $shipment->origin_id;
//                                    $hub_shipments[$hub->id][$shipment->origin_id]['origin_name'] = $shipment->origin_name;
//                                    $hub_shipments[$hub->id][$shipment->origin_id]['hub_id'] = $hub->id;
//                                    $hub_shipments[$hub->id][$shipment->origin_id]['hub_name'] = $hub->name;
//                                }
//                            }
//                            else{
//                                if($shipment->status == 1){
//                                    $hub_shipments[$hub->id][$shipment->origin_id]['pending'] = 1;
//                                    $hub_shipments[$hub->id][$shipment->origin_id]['picked'] = 0;
//                                }
//                                else{
//                                    $hub_shipments[$hub->id][$shipment->origin_id]['pending'] = 0;
//                                    $hub_shipments[$hub->id][$shipment->origin_id]['picked'] = 1;
//                                }
//                                $hub_shipments[$hub->id][$shipment->origin_id]['origin_id'] = $shipment->origin_id;
//                                $hub_shipments[$hub->id][$shipment->origin_id]['origin_name'] = $shipment->origin_name;
//                                $hub_shipments[$hub->id][$shipment->origin_id]['hub_id'] = $hub->id;
//                                $hub_shipments[$hub->id][$shipment->origin_id]['hub_name'] = $hub->name;
//                            }
//                            if($shipment->status == 1){
//                                if(array_key_exists($hub->zone_id, $zone_shipments)){
//                                    if(array_key_exists($hub->id, $zone_shipments[$hub->zone_id])){
//                                        if(array_key_exists($shipment->origin_id, $zone_shipments[$hub->zone_id][$hub->id])){
//                                            $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['pending']++;
//                                        }
//                                        else{
//                                            $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['pending'] = 1;
//                                            $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['zone_id'] = $hub->zone_id;
//                                            $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['zone_name'] = $hub->zone->name;
//                                            $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['hub_id'] = $hub->id;
//                                            $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['hub_name'] = $hub->name;
//                                            $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['origin_id'] = $shipment->origin_id;
//                                            $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['origin_name'] = $shipment->origin_name;
//                                        }
//                                    }
//                                    else{
//                                        $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['pending'] = 1;
//                                        $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['zone_id'] = $hub->zone_id;
//                                        $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['zone_name'] = $hub->zone->name;
//                                        $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['hub_id'] = $hub->id;
//                                        $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['hub_name'] = $hub->name;
//                                        $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['origin_id'] = $shipment->origin_id;
//                                        $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['origin_name'] = $shipment->origin_name;
//                                    }
//                                }
//                                else{
//                                    $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['pending'] = 1;
//                                    $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['zone_id'] = $hub->zone_id;
//                                    $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['zone_name'] = $hub->zone->name;
//                                    $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['hub_id'] = $hub->id;
//                                    $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['hub_name'] = $hub->name;
//                                    $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['origin_id'] = $shipment->origin_id;
//                                    $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id]['origin_name'] = $shipment->origin_name;
//                                }
//                            }
                        }
                    }
                }
            }
        }
        if(count($hub_shipments) > 0){
            foreach ($hub_shipments as $index => $hub_shipment){
                $hub_id = $index;
                NotificationsController::send(78, $hub_id, $hub_shipment);
            }
            foreach ($zone_shipments as $index => $zone_shipment){
                $zone_id = $index;
                NotificationsController::send(79, $zone_id, $zone_shipment);
            }
            NotificationsController::send(80, $date, $hub_shipments);
        }
//        if(count($hub_shipments) > 0){
//            foreach ($hub_shipments as $hub_shipment){
//                foreach ($hub_shipment as $origin_shipment){
//                    $hub_id = $origin_shipment['hub_id'];
//                    break;
//                }
//                NotificationsController::send(78, $hub_id, $hub_shipment);
//            }
//            foreach ($zone_shipments as $zone_shipment){
//                foreach ($zone_shipment as $hub_shipment){
//                    foreach ($hub_shipment as $origin_shipment){
//                        $zone_id = $origin_shipment['zone_id'];
//                        break;
//                    }
//                }
//                NotificationsController::send(79, $zone_id, $zone_shipment);
//            }
//            NotificationsController::send(80, $date, $hub_shipments);
//        }
    }
    static public function overall_pickup_vendor_wise($start_date, $end_date){
        $date = Carbon::today()->toDateString();
        $shipments = DB::connection('reports')->table('shipments')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities as oc', 'oc.id', '=', 'usi.city_id')
            ->join('cities as h', 'h.id', '=', 'oc.hub_id')
            ->join('shipments_journey as sj', function($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=', DB::connection('reports')->raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = shipments.id AND verification = 1)'));
            })
            ->select('h.id as hub_id', 'h.name as hub_name', 'oc.id as origin_id', 'oc.name as origin_name', 'sj.shipper_status_id as status', 'shipments.tracking_number as tracking_number', 'usi.vendor as vendor', 'usi.poc as poc', 'usi.phone as phone')
            ->whereNotNull('usi.vendor')
            ->where('shipments.shipper_status_id', '=', 1)
            ->whereBetween('sj.created_at', [$start_date, $end_date])
            ->get();
        $hubs = City::where('hub', 1)->where('status', 1)->get();
        $zone_shipments = array();
        $hub_shipments = array();
        foreach ($hubs as $hub){
            if(count($shipments) > 0){
                foreach ($shipments as $shipment){
                    if($shipment->status == 1){
                        if($hub->id == $shipment->hub_id){
                            $hub_shipments[$hub->id][$shipment->origin_id][] = ['tracking_number' => $shipment->tracking_number, 'poc' => $shipment->poc, 'vendor' => $shipment->vendor, 'phone' => $shipment->phone, 'origin_id' => $shipment->origin_id, 'origin_name' => $shipment->origin_name, 'hub_id' => $hub->id, 'hub_name' => $hub->name];
                            $zone_shipments[$hub->zone_id][$hub->id][$shipment->origin_id][] = ['tracking_number' => $shipment->tracking_number, 'poc' => $shipment->poc, 'vendor' => $shipment->vendor, 'phone' => $shipment->phone, 'zone_id' => $hub->zone_id, 'zone_name' => $hub->zone->name, 'origin_id' => $shipment->origin_id, 'origin_name' => $shipment->origin_name, 'hub_id' => $hub->id, 'hub_name' => $hub->name];
                        }
                    }
                }
            }
        }
        if(count($hub_shipments) > 0){
            foreach ($hub_shipments as $index => $hub_shipment){
                $hub_id = $index;
                NotificationsController::send(101, $hub_id, $hub_shipment);
            }
            foreach ($zone_shipments as $index => $zone_shipment){
                $zone_id = $index;
                NotificationsController::send(102, $zone_id, $zone_shipment);
            }
            NotificationsController::send(103, $date, $hub_shipments);
        }
    }

    static public function pending_deliveries($date)
    {
        $settings = DB::table('global_settings')->where('type', 'debriefing_report_day_cut_off_time')->first();

        if ($settings) {
            $day_cut_off_time = $settings->setting_value;
        }
        else {
            $day_cut_off_time = 12;
        }

        $date_from = Carbon::parse($date)->subMonth(2)->addHour($day_cut_off_time)->toDateTimeString();
        $date_to = Carbon::parse($date)->addDay()->addHour($day_cut_off_time)->subSecond()->toDateTimeString();

        $serial = 0;
        $status = array(2, 4, 6, 7, 8, 9, 10, 13, 15, 49, 55, 59);
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->join('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->join('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as sjd', function ($join) {
                $join->on('sjd.shipment_id', '=', 'shipments.id')
                    ->where('sjd.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 4)'));
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
            ->leftjoin('intercept_re_book_request_histories as irrh', 'irrh.shipment_id', '=', 'shipments.id')
            ->leftjoin('crm_requests as crm', function ($join) {
                $join->on('crm.shipment_id', '=', 'shipments.id')
                    ->whereIn('crm.status_id', [2, 3, 5])
                    ->where('crm.case_nature_id', 1);
            })
            ->select('shipments.id as shipment_id', 'shipments.tracking_number', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'shipments.consignee_name', 'shipments.consignee_phone_number_1 as phone', 'shipments.consignee_address',
                'shipments.amount', 'sm.mode as shipping_mode', 'bt.booking_type as service_type', 'ss.name as status', 'ssr.name as reason', 'shipments_journey.remarks as remarks', 'shipments_journey.created_at as status_date', 'shipments_journey.created_at as current_status_date','sjd.created_at as destination_arrival', 'sj.created_at as arrival', 'shipments.booking_type_id', 'usi.poc','crm.id as complaint')
            ->whereRaw('IF (shipments.shipper_status_id IN (2, 49), (oc.hub_id = dc.hub_id), TRUE)')
            ->whereRaw('IF (shipments.shipper_status_id = 55, (irrh.old_consignee_city_id = irrh.new_consignee_city_id), TRUE)')
            ->whereBetween('sj.created_at', [$date_from,$date_to])
            ->whereIn('shipments.shipper_status_id', $status)->get();


        $pending_deliveries_report_array[] = ['Pending Deliveries Report'];
        $pending_deliveries_report_array['header'] = ['S. No.', 'Tracking No.', 'Shipper', 'Origin', 'Destination', 'Hub', 'Consignee Name', 'Phone', 'Address', 'COD Amount', 'Shipping Mode', 'Service Type', 'Status', 'Reason', 'Remarks', 'Origin Arrival Date', 'Destination Arrival Date', 'Status Date'];
        $pending_deliveries_report_array[] = ['S. No.' => '', 'Tracking No.' => '', 'Shipper' => '', 'Origin' => '', 'Destination' => '', 'Hub' => '', 'Consignee Name' => '', 'Phone' => '', 'Address' => '', 'Consignee Amount' => '', 'Shipping Mode' => '', 'Service Type' => '', 'Status' => '', 'Reason' => '', 'Remarks' => '', 'Origin Arrival Date' => '', 'Destination Arrival Date' => '', 'Status Date' => ''];

        if(count($shipments) > 0) {
            foreach ($shipments as $shipment) {
                $serial++;
                $tracking_number = $shipment->tracking_number;
                $shipper = $shipment->shipper;
                $origin = $shipment->origin;
                $destination = $shipment->destination;
                $hub = $shipment->hub;
                $consignee_name = $shipment->consignee_name;
                $phone = $shipment->phone;
                $consignee_address = $shipment->consignee_address;
                $amount = $shipment->amount;
                $shipping_mode = $shipment->shipping_mode;
                $service_type = $shipment->service_type;
                $status = $shipment->status;
                $reason = $shipment->reason;
                $remarks = $shipment->remarks;
                $arrival = $shipment->arrival;
                $destination_arrival = $shipment->destination_arrival;
                $status_date = $shipment->status_date;

                $pending_deliveries_report_array[] = ['S. No.' => $serial, 'Tracking Number' => $tracking_number, 'Shipper' => $shipper, 'Origin' => $origin, 'Destination' => $destination, 'Hub' => $hub, 'Consignee Name' => $consignee_name, 'Phone' => $phone, 'Address' => $consignee_address, 'COD Amount' => $amount, 'Shipping Mode' => $shipping_mode, 'Service Type' => $service_type, 'Status' => $status, 'Reason' => $reason, 'Remarks' => $remarks, 'Original Arrival Date' => $arrival, 'Destination Arrival Date' => $destination_arrival, 'Status Date' => $status_date];
            }
                $cell_st = [
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                    'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
                ];
                $serial = $serial + 6;
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getDefaultColumnDimension()->setWidth(20);
                $sheet->fromArray($pending_deliveries_report_array, NULL, 'A2', true);
                $sheet->getStyle("A2:R2")->applyFromArray($cell_st);
                $sheet->getStyle("A3:R3")->applyFromArray($cell_st);
                $sheet->getStyle("B5:B2000")->getNumberFormat()
                    ->setFormatCode(
                        \PHPExcel_Style_NumberFormat::FORMAT_NUMBER
                    );
                $sheet->getStyle("A" . $serial . ":R" . $serial)->applyFromArray($cell_st);
                $sheet->setTitle('Pending Deliveries Report');
                $sheet->mergeCells('A2:R2');
                // $sheet->mergeCells('A3:C3');
                // $sheet->mergeCells('A' . $serial . ':B' . $serial);
                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename=daily_pending_deliveries_report_.xlsx"');
                header('Cache-Control: max-age=0');
                $date_file_name = Carbon::today()->format('Y_m_d');
                $file_name_without_path = "reports/daily_pending_deliveries_report_" . $date_file_name . ".xlsx";
                $file_name = public_path() . "/reports/daily_pending_deliveries_report_" . $date_file_name . ".xlsx";
                $writer->save($file_name);
                return url('/') . '/' . $file_name_without_path;

        }
    }

    static public function receive_deliveries($date)
    {
        $settings = DB::table('global_settings')->where('type', 'debriefing_report_day_cut_off_time')->first();

        if ($settings) {
            $day_cut_off_time = $settings->setting_value;
        }
        else {
            $day_cut_off_time = 12;
        }

        $date_from = Carbon::parse($date)->subMonth(2)->addHour($day_cut_off_time)->toDateTimeString();
        $date_to = Carbon::parse($date)->addDay()->addHour($day_cut_off_time)->subSecond()->toDateTimeString();

        $serial = 0;
        $status = array(2, 4, 6, 7, 8, 9, 10, 13, 15, 49, 55, 59);
        $deliveries = DeliveryNote::join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->leftjoin('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->join('admins', 'admins.id', '=', 'delivery_notes.admin_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'delivery_notes.updated_by')
            ->select(['delivery_notes.id as delivery_note', 'delivery_notes.id as delivery_note_id',  'oc.name as hub', 'riders.name as rider', 'routes.code as route', 'routes.start', 'routes.end', 'admins.name as assignee', 'delivery_notes.created_at', 'delivery_notes.total_cod_amount as amount', 'delivery_notes.shipments_count', 'delivery_notes.pending_status', 'delivery_notes.created_at','delivery_notes.last_updated_at','ad.name as updated_by','delivery_notes.special_rider','delivery_notes.special_rider_name','delivery_notes.special_rider_phone','delivery_notes.delivered_shipments as delivered_shipments',DB::raw('(SELECT COUNT(d.id) FROM delivery_notes AS d INNER JOIN delivery_note_shipments AS dns ON d.id = dns.delivery_note_id WHERE dns.delivery_note_id = delivery_notes.id AND dns.status = 0) AS shipments_unverified_count')])
            ->whereBetween('delivery_notes.created_at', [$date_from, $date_to])
            ->where('delivery_notes.status', 0)
            ->orderBy('delivery_notes.created_at', 'desc')->get();

        $receive_deliveries_report_array[] = ['Receive Deliveries Report'];

        $receive_deliveries_report_array['header'] = [
            'S. No.',
            'Delivery Note No.',
            'Hub',
            'Rider',
            'Route',
            'No. Of Shipments',
            'No. of Pending Shipments',
            'No. of Delivered Shipments',
            'Assigned By',
            'Assigned Date',
            'Total Collection',
            'Status',
            'Last Updated (Date)',
            'Last Updated By'
        ];
        
        $receive_deliveries_report_array[] = [
            'S. No.'                    => '',
            'Delivery Note No.'         => '',
            'Hub'                       => '',
            'Rider'                     => '',
            'Route'                     => '',
            'No. Of Shipments'          => '',
            'No. of Pending Shipments'  => '',
            'No. of Delivered Shipments'=> '',
            'Assigned By'               => '',
            'Assigned Date'             => '',
            'Total Collection'          => '',
            'Status'                    => '',
            'Last Updated (Date)'       => '',
            'Last Updated By'           => ''
        ];

        if(count($deliveries) > 0) {
            foreach ($deliveries as $shipment) {
                $serial++;
                $delivery_note_id = $shipment->delivery_note_id;
                $hub = $shipment->hub;
                $rider = $shipment->rider;
                $route = $shipment->route;
                $assignee = $shipment->assignee;
                $amount = $shipment->amount;
                $shipments_count = $shipment->shipments_count;
                $shipments_unverified_count = $shipment->shipments_unverified_count;
                if ($shipment->pending_status == 0) {
                    $pending_status = 'Pending for Update';
                } else {
                    $pending_status = 'Pending for Verification';
                }
                $delivered_shipments = $shipment->delivered_shipments;
                $created_at = $shipment->created_at;
                $updated_by = $shipment->updated_by;
                $last_updated_at = $shipment->last_updated_at;


                $receive_deliveries_report_array[] = ['S. No.' => $serial, 'Delivery Note No.' => $delivery_note_id, 'Hub' => $hub, 'Rider' => $rider, 'Route' => $route, 'No. Of Shipments' => $shipments_count, 'No. of Pending Shipments' => $shipments_unverified_count, 'No. of Delivered Shipments' => $delivered_shipments, 'Assigned By' => $assignee, 'Assigned Date' => $created_at, 'Total Collection' => $amount, 'Status' => $pending_status, 'Last Updated (Dated)' => $last_updated_at, 'Last Updated By' => $updated_by];
            }
                $cell_st = [
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                    'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
                ];
                $serial = $serial + 6;
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getDefaultColumnDimension()->setWidth(20);
                $sheet->fromArray($receive_deliveries_report_array, NULL, 'A2', true);
                $sheet->getStyle("A2:N2")->applyFromArray($cell_st);
                $sheet->getStyle("A3:N3")->applyFromArray($cell_st);
                $sheet->getStyle("A" . $serial . ":N" . $serial)->applyFromArray($cell_st);
                $sheet->setTitle('Receive Deliveries Report');
                $sheet->mergeCells('A2:N2');
                // $sheet->mergeCells('A3:C3');
                // $sheet->mergeCells('A' . $serial . ':B' . $serial);
                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename=daily_receive_deliveries_report_.xlsx"');
                header('Cache-Control: max-age=0');
                $date_file_name = Carbon::today()->format('Y_m_d');
                $file_name_without_path = "reports/daily_receive_deliveries_report_" . $date_file_name . ".xlsx";
                $file_name = public_path() . "/reports/daily_receive_deliveries_report_" . $date_file_name . ".xlsx";
                $writer->save($file_name);
                return url('/') . '/' . $file_name_without_path;


        }
    }

    static public function overland_aging_report()
    {
        $to = Carbon::yesterday()->toDateString();
        //$to = Carbon::parse($yesterday)->subDays(1)->toDateTimeString();
        $from = Carbon::parse($to)->subDays(6)->toDateString();
        $today = Carbon::now();

        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at', '=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })->select('shipments.id as shId', 'shipments.tracking_number as tracking', 'u.name as shipper','oc.id as origin_id','oc.name as origin', 'dc.name as destination','h.id as hub_id','h.name as hub',  'shipments.consignee_address','ss.name as current_status', 'shipments_journey.created_at as status_date', 'shipments_journey.created_at as current_status_date', 'sj.created_at as arrival_date','shipments.actual_weight as actual_weight','shipments.chargeable_weight as chargeable_weight')
            ->whereBetween('shipments_journey.created_at',[$from,$to])
            ->where('sm.id',2)
            ->where('shipments_journey.shipper_status_id',3)
            ->groupBy('shipments.id')->get();

        Carbon::setWeekendDays([
            Carbon::SUNDAY,]);


        if(count($shipments) > 0) {

            $overland_aging_day_wise_header['header'] = ['Days', 'Count', 'Ratio'];

            $overland_aging_day_wise_data[] = ['Days' => '', 'Count' => '', 'Ratio' => ''];

            $day_wise = '<div class="mb-2">';
            $day_wise = '<table style="width:100%;">';
            $day_wise .= '<thead><tr>
                       <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Days</th>
                       <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Count</th>
                       <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Ratio</th>';
            $day_wise .= '</tr></thead><tbody>';
            $days = array();
            $days['zero'] = 0;
            $days['one'] = 0;
            $days['two'] = 0;
            $days['three'] = 0;
            $days['four'] = 0;
            $days['five'] = 0;
            $days['six'] = 0;
            $days['total'] = 0;

            foreach ($shipments as $shipment) {
                $current_status_date = $shipment->current_status_date;
                $today = Carbon::parse($today);
                $current_status_date = Carbon::parse($current_status_date);
                $aging = $current_status_date->diffInDays($today);

                if ($aging == 0) {
                    $days['zero']++;
                    $days['total']++;
                }
                if ($aging == 1) {
                    $days['one']++;
                    $days['total']++;
                } elseif ($aging == 2) {
                    $days['two']++;
                    $days['total']++;
                } elseif ($aging == 3) {
                    $days['three']++;
                    $days['total']++;
                } elseif ($aging == 4) {
                    $days['four']++;
                    $days['total']++;
                } elseif ($aging == 5) {
                    $days['five']++;
                    $days['total']++;
                } elseif ($aging >= 6) {
                    $days['six']++;
                    $days['total']++;
                }

            }
            $days_ratio['zero'] = ($days['total'] != 0) ? $days['zero'] / $days['total'] : 0;

            $days_ratio['one'] = ($days['total'] != 0) ? $days['one'] / $days['total'] : 0;

            $days_ratio['two'] = ($days['total'] != 0) ? $days['two'] / $days['total'] : 0;

            $days_ratio['three'] = ($days['total'] != 0) ? $days['three'] / $days['total'] : 0;

            $days_ratio['four'] = ($days['total'] != 0) ? $days['four'] / $days['total'] : 0;

            $days_ratio['five'] = ($days['total'] != 0) ? $days['five'] / $days['total'] : 0;

            $days_ratio['six'] = ($days['total'] != 0) ? $days['six'] / $days['total'] : 0;

            $days_ratio['total'] =  $days_ratio['zero'] +  $days_ratio['one'] +  $days_ratio['two'] +  $days_ratio['three'] +  $days_ratio['four'] +  $days_ratio['five'] +  $days_ratio['six'];

            $overland_aging_day_wise_data['zero'] = ['Days' => '0', 'Count' => $days['zero'], 'Ratio' => round($days_ratio['zero'] * 100,2) . '%'];
            $overland_aging_day_wise_data['one'] = ['Days' => '1', 'Count' => $days['one'], 'Ratio' => round($days_ratio['one'] * 100,2) . '%'];
            $overland_aging_day_wise_data['one'] = ['Days' => '1', 'Count' => $days['one'], 'Ratio' => round($days_ratio['one'] * 100,2) . '%'];
            $overland_aging_day_wise_data['two'] = ['Days' => '2', 'Count' => $days['two'], 'Ratio' => round($days_ratio['two'] * 100,2) . '%'];
            $overland_aging_day_wise_data['three'] = ['Days' => '3', 'Count' => $days['three'], 'Ratio' => round($days_ratio['three'] * 100,2) . '%'];
            $overland_aging_day_wise_data['four'] = ['Days' => '4', 'Count' => $days['four'], 'Ratio' => round($days_ratio['four'] * 100,2) . '%'];
            $overland_aging_day_wise_data['five'] = ['Days' => '5', 'Count' => $days['five'], 'Ratio' => round($days_ratio['five'] * 100,2) . '%'];
            $overland_aging_day_wise_data['six'] = ['Days' => '5+', 'Count' => $days['six'], 'Ratio' => round($days_ratio['six'] * 100,2) . '%'];
            $overland_aging_day_wise_footer[] = ['Grand Total', 'Count' => $days['total'], 'Ratio' => $days_ratio['total'] * 100 . '%'];

            $overland_aging_day_wise = array_merge($overland_aging_day_wise_header, $overland_aging_day_wise_data, $overland_aging_day_wise_footer);


            $day_wise .= '<tr>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">0</td>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days['zero'] . '</td>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . round($days_ratio['zero'] * 100,2) . '%' . '</td>';
            $day_wise .= '</tr>';

            $day_wise .= '<tr>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">1</td>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days['one'] . '</td>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . round($days_ratio['one'] * 100,2) . '%' . '</td>';
            $day_wise .= '</tr>';

            $day_wise .= '<tr>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">2</td>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days['two'] . '</td>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . round($days_ratio['two'] * 100,2). '%' . '</td>';
            $day_wise .= '</tr>';

            $day_wise .= '<tr>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">3</td>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days['three'] . '</td>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' .  round($days_ratio['three'] * 100,2) . '%' . '</td>';
            $day_wise .= '</tr>';

            $day_wise .= '<tr>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">4</td>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days['four'] . '</td>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' .  round($days_ratio['four'] * 100,2) . '%' . '</td>';
            $day_wise .= '</tr>';

            $day_wise .= '<tr>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">5</td>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days['five'] . '</td>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' .  round($days_ratio['five'] * 100,2) . '%' . '</td>';
            $day_wise .= '</tr>';

            $day_wise .= '<tr>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">5+</td>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days['six'] . '</td>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' .  round($days_ratio['six'] * 100,2) . '%' . '</td>';
            $day_wise .= '</tr>';

            $day_wise .= '<tr>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">Grand Total</td>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days['total'] . '</td>';
            $day_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"> ' . $days_ratio['total'] * 100 . '%' . '</td>';
            $day_wise .= '</tr>';

            $day_wise .= '</tbody></table>';
            $day_wise .= '</div>';

            //shipments in transit
            $origin_wise = '<div class="mb-2">';
            $origin_wise .= '<table style="width:100%;">';
            $origin_wise .= '<thead><tr><th COLSPAN="10" style="border: 1px solid black; border-collapse: collapse;">Shipment-In Transit</th></tr><tr>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">0</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">1</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">2</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">3</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">4</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">5</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">5+</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Ratio</th>';
            $origin_wise .= '</tr></thead><tbody>';

            $days = array();
            $days['zero'] = 0;
            $days['one'] = 0;
            $days['two'] = 0;
            $days['three'] = 0;
            $days['four'] = 0;
            $days['five'] = 0;
            $days['six'] = 0;
            $days['total'] = 0;

            $origin_wise_shipment_header['name'] = ['Shipments In Transit'];
            $origin_wise_shipment_header['header'] = ['Origin', '0', '1', '2', '3', '4', '5', '5+', 'Total', 'Ratio'];
            $origin_wise_shipment_data[] = ['Origin' => '', '0' => '', '1' => '', '2' => '', '3' => '', '4' => '', '5' => '', '5+' => '', 'Total' => '', 'Ratio' => ''];


            foreach ($shipments as $shipment) {

                $origin_id = $shipment->origin_id;
                $current_status_date = $shipment->current_status_date;
                $today = Carbon::parse($today);
                $current_status_date = Carbon::parse($current_status_date);
                $aging = $current_status_date->diffInDays($today);

                if (!array_key_exists($origin_id, $days)) {
                    $days[$origin_id]['zero'] = 0;
                    $days[$origin_id]['one'] = 0;
                    $days[$origin_id]['two'] = 0;
                    $days[$origin_id]['three'] = 0;
                    $days[$origin_id]['four'] = 0;
                    $days[$origin_id]['five'] = 0;
                    $days[$origin_id]['six'] = 0;
                    $days[$origin_id]['total'] = 0;
                }
                if ($aging == 0) {
                    $days[$origin_id]['zero']++;
                    $days[$origin_id]['total']++;
                }
                if ($aging == 1) {
                    $days[$origin_id]['one']++;
                    $days[$origin_id]['total']++;
                } elseif ($aging == 2) {
                    $days[$origin_id]['two']++;
                    $days[$origin_id]['total']++;
                } elseif ($aging == 3) {
                    $days[$origin_id]['three']++;
                    $days[$origin_id]['total']++;
                } elseif ($aging == 4) {
                    $days[$origin_id]['four']++;
                    $days[$origin_id]['total']++;
                } elseif ($aging == 5) {
                    $days[$origin_id]['five']++;
                    $days[$origin_id]['total']++;
                } elseif ($aging >= 6) {
                    $days[$origin_id]['six']++;
                    $days[$origin_id]['total']++;
                }
            }

            $origins = City::where('pickup', 1)->where('status', 1)->get();
            $total_days['zero']['total'] = 0;
            $total_days['one']['total'] = 0;
            $total_days['two']['total'] = 0;
            $total_days['three']['total'] = 0;
            $total_days['four']['total'] = 0;
            $total_days['five']['total'] = 0;
            $total_days['six']['total'] = 0;
            $total_days['row']['total'] = 0;
            $ratio = 0;
            $total_ratio = 0;
            foreach($origins as $origin){
                if (array_key_exists($origin->id, $days)) {
                    $total_days['zero']['total'] = $total_days['zero']['total'] + $days[$origin->id]['zero'];
                    $total_days['one']['total'] = $total_days['one']['total'] + $days[$origin->id]['one'];
                    $total_days['two']['total'] = $total_days['two']['total'] + $days[$origin->id]['two'];
                    $total_days['three']['total'] = $total_days['three']['total'] + $days[$origin->id]['three'];
                    $total_days['four']['total'] = $total_days['four']['total'] + $days[$origin->id]['four'];
                    $total_days['five']['total'] = $total_days['five']['total'] + $days[$origin->id]['five'];
                    $total_days['six']['total'] = $total_days['six']['total'] + $days[$origin->id]['six'];
                    $total_days['row']['total'] = $total_days['row']['total'] + $days[$origin->id]['total'];
                }
            }


            foreach ($origins as $origin) {
                if (array_key_exists($origin->id, $days)) {

                    $origin_wise .= '<tr>';
                    $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $origin->name . '</td>';
                    $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days[$origin->id]['zero'] . '</td>';
                    $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days[$origin->id]['one'] . '</td>';
                    $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days[$origin->id]['two'] . '</td>';
                    $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days[$origin->id]['three'] . '</td>';
                    $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days[$origin->id]['four'] . '</td>';
                    $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days[$origin->id]['five'] . '</td>';
                    $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days[$origin->id]['six'] . '</td>';
                    $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days[$origin->id]['total'] . '</td>';

                    $ratio =  $days[$origin->id]['total'] / $total_days['row']['total'];

                    //$ratio = round($ratio,2);
                    $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . round($ratio * 100, 2) . '%' . '</td>';
                    $origin_wise .= '</tr>';

                    $origin_wise_shipment_data[] = ['Origin' => $origin->name, '0' => $days[$origin->id]['zero'], '1' => $days[$origin->id]['one'], '2' => $days[$origin->id]['two'], '3' => $days[$origin->id]['three'], '4' => $days[$origin->id]['four'], '5' => $days[$origin->id]['five'], '5+' => $days[$origin->id]['five'], 'Total' => $days[$origin->id]['total'], 'Ratio' => round($ratio * 100,2) . '%'];

                    $total_ratio = $total_ratio + $ratio;
                }
            }
            $origin_wise_shipment_footer[] = ['Grand Total' => 'Grand Total', '0' => $total_days['zero']['total'], '1' => $total_days['one']['total'], '2' => $total_days['two']['total'], '3' => $total_days['three']['total'], '4' => $total_days['four']['total'], '5' => $total_days['five']['total'], '5+' => $total_days['six']['total'], 'Total' => $total_days['row']['total'], 'Ratio' => $total_ratio * 100 . '%'];

            $origin_wise .= '<tr>';
            $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"> Grand Total </td>';
            $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_days['zero']['total'] . '</td>';
            $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_days['one']['total'] . '</td>';
            $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_days['two']['total'] . '</td>';
            $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_days['three']['total'] . '</td>';
            $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_days['four']['total'] . '</td>';
            $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_days['five']['total'] . '</td>';
            $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_days['six']['total'] . '</td>';
            $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_days['row']['total'] . '</td>';
            $origin_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_ratio * 100 . '%' . '</td>';
            $origin_wise .= '</tr>';
            $origin_wise .= '</tbody></table>';
            $origin_wise .= '</div>';


            $origin_wise_shipment = array_merge($origin_wise_shipment_header, $origin_wise_shipment_data, $origin_wise_shipment_footer);

            //In Transit Shipments Breakup
            $hub_wise = '<div class="mb-2">';
            $hub_wise .= '<table style="width:100%;">';
            $hub_wise .= '<thead><tr><th COLSPAN="10" style="border: 1px solid black; border-collapse: collapse;">In Transit Shipments Breakup</th></tr><tr>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Hub</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">0</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">1</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">2</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">3</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">4</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">5</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">5+</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Total</th>
                      <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Ratio</th>';
            $hub_wise .= '</tr></thead><tbody>';

            $days = array();
            $days['zero'] = 0;
            $days['one'] = 0;
            $days['two'] = 0;
            $days['three'] = 0;
            $days['four'] = 0;
            $days['five'] = 0;
            $days['six'] = 0;
            $days['total'] = 0;
            $ratio = 0;

            $hub_wise_shipment_header['name'] = ['In Transit Shipments Breakup'];
            $hub_wise_shipment_header['header'] = ['Hub', '0', '1', '2', '3', '4', '5', '5+', 'Total', 'Ratio'];
            $hub_wise_shipment_data[] = ['Hub' => '', '0' => '', '1' => '', '2' => '', '3' => '', '4' => '', '5' => '', '5+' => '', 'Total' => '', 'Ratio' => ''];


            foreach ($shipments as $shipment) {
                $hub_id = $shipment->hub_id;
                $current_status_date = $shipment->current_status_date;
                $today = Carbon::parse($today);
                $current_status_date = Carbon::parse($current_status_date);
                $aging = $current_status_date->diffInDays($today);

                if (!array_key_exists($hub_id, $days)) {
                    $days[$hub_id]['zero'] = 0;
                    $days[$hub_id]['one'] = 0;
                    $days[$hub_id]['two'] = 0;
                    $days[$hub_id]['three'] = 0;
                    $days[$hub_id]['four'] = 0;
                    $days[$hub_id]['five'] = 0;
                    $days[$hub_id]['six'] = 0;
                    $days[$hub_id]['total'] = 0;
                }
                if ($aging == 0) {
                    $days[$hub_id]['zero']++;
                    $days[$hub_id]['total']++;
                }
                if ($aging == 1) {
                    $days[$hub_id]['one']++;
                    $days[$hub_id]['total']++;
                } elseif ($aging == 2) {
                    $days[$hub_id]['two']++;
                    $days[$hub_id]['total']++;
                } elseif ($aging == 3) {
                    $days[$hub_id]['three']++;
                    $days[$hub_id]['total']++;
                } elseif ($aging == 4) {
                    $days[$hub_id]['four']++;
                    $days[$hub_id]['total']++;
                } elseif ($aging == 5) {
                    $days[$hub_id]['five']++;
                    $days[$hub_id]['total']++;
                } elseif ($aging >= 6) {
                    $days[$hub_id]['six']++;
                    $days[$hub_id]['total']++;
                }

            }
            $hubs = City::where('hub', 1)->where('status', 1)->get();
            $total_days['zero']['total'] = 0;
            $total_days['one']['total'] = 0;
            $total_days['two']['total'] = 0;
            $total_days['three']['total'] = 0;
            $total_days['four']['total'] = 0;
            $total_days['five']['total'] = 0;
            $total_days['six']['total'] = 0;
            $total_days['row']['total'] = 0;
            $total_ratio = 0;
            foreach($hubs as $hub){
                if (array_key_exists($hub->id, $days)) {
                    $total_days['zero']['total'] = $total_days['zero']['total'] + $days[$hub->id]['zero'];
                    $total_days['one']['total'] = $total_days['one']['total'] + $days[$hub->id]['one'];
                    $total_days['two']['total'] = $total_days['two']['total'] + $days[$hub->id]['two'];
                    $total_days['three']['total'] = $total_days['three']['total'] + $days[$hub->id]['three'];
                    $total_days['four']['total'] = $total_days['four']['total'] + $days[$hub->id]['four'];
                    $total_days['five']['total'] = $total_days['five']['total'] + $days[$hub->id]['five'];
                    $total_days['six']['total'] = $total_days['six']['total'] + $days[$hub->id]['six'];
                    $total_days['row']['total'] = $total_days['row']['total'] + $days[$hub->id]['total'];
                }
            }
            foreach ($hubs as $hub) {
                if (array_key_exists($hub->id, $days)) {
                    $hub_wise .= '<tr>';
                    $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub->name . '</td>';
                    $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days[$hub->id]['zero'] . '</td>';
                    $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days[$hub->id]['one'] . '</td>';
                    $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days[$hub->id]['two'] . '</td>';
                    $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days[$hub->id]['three'] . '</td>';
                    $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days[$hub->id]['four'] . '</td>';
                    $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days[$hub->id]['five'] . '</td>';
                    $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days[$hub->id]['six'] . '</td>';
                    $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $days[$hub->id]['total'] . '</td>';

                    $ratio =  $days[$hub->id]['total'] / $total_days['row']['total'];
                    $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . round($ratio * 100, 2) . '%' . '</td>';
                    $hub_wise .= '</tr>';

                    $hub_wise_shipment_data[] = ['Hub' => $hub->name, '0' => $days[$hub->id]['zero'], '1' => $days[$hub->id]['one'], '2' => $days[$hub->id]['two'], '3' => $days[$hub->id]['three'], '4' => $days[$hub->id]['four'], '5' => $days[$hub->id]['five'], '5+' => $days[$hub->id]['six'], 'Total' => $days[$hub->id]['total'], 'Ratio' => round($ratio * 100,2) . '%'];

                    $total_ratio = $total_ratio + $ratio;
                }
            }

            $hub_wise_shipment_footer[] = ['Grand Total' => 'Grand Total', '0' => $total_days['zero']['total'], '1' => $total_days['one']['total'], '2' => $total_days['two']['total'], '3' => $total_days['three']['total'], '4' => $total_days['four']['total'], '5' => $total_days['five']['total'], '5+' => $total_days['six']['total'], 'Total' => $total_days['row']['total'], 'Ratio' => $total_ratio * 100 . '%'];

            $hub_wise .= '<tr>';
            $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;"> Grand Total </td>';
            $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_days['zero']['total'] . '</td>';
            $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_days['one']['total'] . '</td>';
            $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_days['two']['total'] . '</td>';
            $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_days['three']['total'] . '</td>';
            $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_days['four']['total'] . '</td>';
            $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_days['five']['total'] . '</td>';
            $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_days['six']['total'] . '</td>';
            $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_days['row']['total'] . '</td>';
            $hub_wise .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $total_ratio * 100 . '%' . '</td>';
            $hub_wise .= '</tr>';
            $hub_wise .= '</tbody></table>';
            $hub_wise .= '</div>';

            $hub_wise_shipment = array_merge($hub_wise_shipment_header, $hub_wise_shipment_data, $hub_wise_shipment_footer);

            //table tracking_number wise
            $html = '<div class="mb-4">';
            $html .= '<table style="width:100%;">';
            $html .= '<thead><tr>
                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">S No.</th>
                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Tracking Number</th>
                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Shipper</th>
                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Origin</th>
                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Destination</th>
                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Hub</th>
                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Current Status</th>
                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Arrived At Origin Date</th>
                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Current Status Date</th>
                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Aging</th>
                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Actual Weight</th>
                   <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Chargeable Weight</th>';
            $html .= '</tr></thead><tbody>';
            $serial = 1;

            $tracking_number_wise_header['header'] = ['S.No', 'Tracking Number', 'Shipper', 'Origin', 'Destination', 'Hub', 'Current Status', 'Arrival At Origin Date', 'Current Status Date', 'Aging', 'Actual Weight', 'Chargeable Weight'];
            $tracking_number_wise_data[] = ['S.No' => '', 'Tracking Number' => '', 'Shipper' => '', 'Origin' => '', 'Destination' => '', 'Hub' => '', 'Current Status' => '', 'Arrival At Origin Date' => '', 'Current Status Date' => '', 'Aging' => '', 'Actual Weight' => '', 'Chargeable Weight' => ''];


            foreach ($shipments as $shipment) {
                $tracking_number = $shipment->tracking;
                $shipper = $shipment->shipper;
                $origin = $shipment->origin;
                $destination = $shipment->destination;
                $hub = $shipment->hub;
                $current_status = $shipment->current_status;
                $arrival_date = $shipment->arrival_date;
                $current_status_date = $shipment->current_status_date;
                $today = Carbon::parse($today);
                $current_status_date = Carbon::parse($current_status_date);
                $aging = $current_status_date->diffInDays($today) . ' days';
                $actual_weight = $shipment->actual_weight;
                $chargeable_weight = $shipment->chargeable_weight;

                $html .= '<tr>';
                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $serial . '</td>';
                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $tracking_number . '</td>';
                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $shipper . '</td>';
                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $origin . '</td>';
                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $destination . '</td>';
                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $hub . '</td>';
                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $current_status . '</td>';
                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $arrival_date . '</td>';
                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $current_status_date . '</td>';
                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $aging . '</td>';
                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $actual_weight . '</td>';
                $html .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $chargeable_weight . '</td>';
                $html .= '</tr>';

                $tracking_number_wise_data[] = ['S.No' => $serial, 'Tracking Number' => $tracking_number, 'Shipper' => $shipper, 'Origin' => $origin, 'Destination' => $destination, 'Hub' => $hub, 'Current Status' => $current_status, 'Arrival At Origin Date' => $arrival_date, 'Current Status Date' => $current_status_date, 'Aging' => $aging, 'Actual Weight' => $actual_weight, 'Chargeable Weight' => $chargeable_weight];

                $serial++;

            }

            $html .= '</tbody></table>';
            $html .= '</div>';

            $tracking_number_wise = array_merge($tracking_number_wise_header, $tracking_number_wise_data);


            $table = $day_wise;
            $table .= $origin_wise;
            $table .= $hub_wise;
            $table .= $html;
            $table .= '<br>';
            $table .= '<br>';
            $table .= '-';


            $cell_st = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];
            $total_cell_st = [
                'font' => ['bold' => true],
                'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]],
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('argb' => '000000'),
                    ),
                )
            ];

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getDefaultColumnDimension()->setWidth(20);
            $sheet->fromArray($overland_aging_day_wise, NULL, 'D4', true);
            $sheet->getStyle("D2:P2")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CECECE');

            $total_style_cell = 'D4:F4';
            $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
            $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);
            $sheet->getStyle($total_style_cell)->getAlignment()->setWrapText(true);

            $sheet->getStyle($total_style_cell)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CECECE');


            $sheet->fromArray($overland_aging_day_wise, NULL, 'D4', true);

            $total_style_cell = 'D13:F13';
            $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
            $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);
            $sheet->getStyle($total_style_cell)->getAlignment()->setWrapText(true);


            $sheet->getStyle($total_style_cell)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CECECE');

            $origin_wise_cell = 'D18';
            $origin_wise_last_cell = 'M18';
            $sheet->fromArray($origin_wise_shipment, NULL, $origin_wise_cell, true);
            $total_origin_wise_count = count($origin_wise_shipment);

            $total_style_cell = 'D18:M18';
            $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
            $sheet->mergeCells('D18:M18');
            $sheet->getStyle($total_style_cell)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CECECE');
            $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);

            $total_style_cell = 'D19:M19';
            $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);

            $sheet->getStyle($total_style_cell)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CECECE');

            $grand_total_count = 18 + $total_origin_wise_count - 1;


            $total_style_cell = "D$grand_total_count" . ":M" . $grand_total_count;
            $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
            $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);
            $sheet->getStyle($total_style_cell)->getAlignment()->setWrapText(true);

            $count_column = $grand_total_count + 6;

            $sheet->getStyle($total_style_cell)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CECECE');

            $origin_wise_cell = 'D' . $count_column;
            $origin_wise_last_cell = 'M' . $count_column;
            $sheet->fromArray($hub_wise_shipment, NULL, $origin_wise_cell, true);
            $total_hub_wise_count = count($hub_wise_shipment);


            $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);

            $total_style_cell = "D$count_column" . ":M" . $count_column;
            $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
            $sheet->mergeCells('D' . $count_column . ':M' . $count_column);
            $sheet->getStyle($total_style_cell)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CECECE');
            $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);


            $count_column = $count_column + 1;
            $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);

            $total_style_cell = "D$count_column" . ":M" . $count_column;
            $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);

            $sheet->getStyle($total_style_cell)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CECECE');
            $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);
            //dd($count_column);
            //dd($count_column);


            $grand_total_count = $count_column + $total_hub_wise_count - 2;

            $total_style_cell = "D$grand_total_count" . ":M" . $grand_total_count;
            $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
            $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);
            $sheet->getStyle($total_style_cell)->getAlignment()->setWrapText(true);

            $sheet->getStyle($total_style_cell)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CECECE');

            $count = $total_hub_wise_count + $count_column + 6;

            $tracking_wise_cell = 'D' . $count;
            $origin_wise_last_cell = 'O' . $count;
            $sheet->fromArray($tracking_number_wise, NULL, $tracking_wise_cell, true);
            $total_tracking_wise_count = count($hub_wise_shipment);

            $total_style_cell = "D$count" . ":O" . $count;
            $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);

            $sheet->getStyle($total_style_cell)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CECECE');


            /* $sheet->setTitle('Overland Aging Report');*/
            $sheet->mergeCells('A2:P2')->setTitle('Overland Aging Report');
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="outstanding_sdn_report.xlsx"');
            header('Cache-Control: max-age=0');
            $date_file_name = Carbon::today()->format('Y_m_d');
            $file_name_without_path = "reports/overland_aging_report_" . $date_file_name . ".xlsx";
            $file_name = public_path() . "/reports/overland_aging_report_" . $date_file_name . ".xlsx";
            $writer->save($file_name);
            $link = url('/') . '/' . $file_name_without_path;

            $data = $table;
            $data .= '<a href="' . $link . '"> Download </a>';
            return $data;
        }

    }
    static public function weekly_attendence_summary($employees_attendance)
    {
        $expected_clockin = Carbon::createFromFormat('Y-m-d H:i:s', $employees_attendance->attendance_date.$employees_attendance->start_time)->addMinutes((int)$employees_attendance->extension_minutes);
        $clock_in = Carbon::parse($employees_attendance->clock_in_datetime);
        $time_diff = $expected_clockin->diffInMinutes(Carbon::parse($clock_in), false);
        $leave_status = '';
        $late = '';
        $attendence_adjustment = '';
        if($employees_attendance['leave_status'] == 0)
        {
            $leave_status = "No";
        }
        else
        {
            $leave_status= "Yes";
        }
        if($time_diff > 0)
        {
            $late="Yes";
        }
        else
        {
            $late="No";  
        }
        if($employees_attendance['status'] == 0)
        {
            $attendence_adjustment="No";
        }
        else
        {
            $attendence_adjustment="Yes";
        }
    
        $weekly_attendance_summary = ['Trax ID' => $employees_attendance['trax_id'], 'Name' => $employees_attendance['name'], 'Designation' => $employees_attendance['designation'], 'Leaves Availed' => $leave_status , 'Late' => $late, 'Attendance Adjustment' => $attendence_adjustment,'Date' =>$employees_attendance['attendance_date'],'Day' =>Carbon::parse($employees_attendance['attendance_date'])->format('l'), 'Clock In' => $employees_attendance['clock_in_datetime'], 'Clock Out' => $employees_attendance['clock_out_datetime']];
        return $weekly_attendance_summary;
    }
    static public function weekly_attendence_summary_excel($summary,$line_manager)
    {
        
        $date = Carbon::now();
        $create_excel['header'] = ['Trax ID','Name', 'Designation', 'Leaves Availed', 'Late', 'Attendance Adjustment','Date','Day','Clock In','Clock Out'];
        foreach ($summary as $key => $value) {
            $create_excel[] = $value;
        }
        $cell_st =[
                'font' =>['bold' => true],
                'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $sps_count  = count($create_excel); 
        
        $ts = "D3:D" . $sps_count;
        $tas = "E3:E" . $sps_count;
        $tr = "G3:G" . $sps_count;
        $tar = "H3:H" . $sps_count;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);
        $sheet->fromArray($create_excel, NULL, 'A2', true);
        $sheet->getStyle("A2:J2")->applyFromArray($cell_st);
        $sheet->getStyle($tr)->getFill();
        $sheet->getStyle($tar)->getFill();
        $sheet->setTitle('Weekly Attendence Summary');
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d').'_'.$line_manager;
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');
        $file_name_without_path = "reports/weekly_attendence_summary_report_" . $date_file_name . ".xlsx";
        $file_name = public_path() . "/reports/weekly_attendence_summary_report_" . $date_file_name . ".xlsx";
        $writer->save($file_name);
        $file = url('/'). '/' . $file_name_without_path;
        $link = '<a href="' . $file . '" target="_blank"><u>Download</u></a>';
        return $link;
    }

    static public function qsr_daily_report()
    {
        $serial = 0;
        $connection = 'reports';
        $select = [
            'z.name  as zone',
            'si.description as description',
            'ssr.name as reason',
            'sjr.remarks as remarks',
            'ss.name as status',
            'shipments.id as shId',
            'shipments.tracking_number',
            'shipments.tracking_number as tracking_number_link',
            'u.name as shipper',
            'hss.name as history_status',
            'bt.booking_type as service_type',
            'sj.created_at as arrival',
            'oc.name as origin',
            'dc.name as destination',
            'h.name as hub',
            'ca.name as area',
            'shipments.amount',
            'journey.created_at as last_status_date',
            'shipments.consignee_name as name',
            'shipments.booking_type_id',
            'shipments.created_at',
            'usi.poc',
            'u.id as account_no',
            'sm.mode as shipping_mode',
            'shipments.order_id as order_id',
            'rc.name as return_city',
            'cmbh.name as current_hub_name',
            'cmbh.id as current_hub_id',
            'shipments.shipper_status_id as shipper_status_id',
            'cr.missing_product_price as missing_product_price',
            'cr.damage_product_price as damage_product_price',
            'scs.name as sub_segment',
            'cargo_status.name as cargo_status',
            'cmb.seal_number as seal_number',
            'bs.name as bag_status',
            'sjfa.created_at as first_attempt_date',
            'sjrp.created_at as rider_picked_status_date',
            'spt.admin_id as sales_person_id',
            'sales_person.name as sales_person_name',
            'stt.kam as stt_kam_id',
            'kam.name as stt_kam_name',
            'scu.user_id as scu_kam_id',
            'scun.name as scu_kam_name',
            'ssjal_last_location.updated_at as last_location_updated_at',
            'ca_scanning_last_location_name.name as ca_scanning_last_location_name',
            'ssj_last_location.user_type as scanned_by_user_type',
            'ssj_last_location.admin_id as scanned_by_id',
            'last_screen_location.name as last_location_screen_location_name',
            'ssj_last_location.entry_method as entry_method',
            'destination_sj.created_at as destination_arrival_date',
            'intercept_approved.name as intercept_city_name',
            'irb.intercept_type as intercepttype',
            'shipmentMisrouted.name as misroutedCityname',
        ];

        $sales = DB::connection('reports')->table('shipments')->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->leftJoin('sale_person_tags as spt', function($join){
                $join->on('spt.user_id','u.id')
                ->where(
                    'spt.id',
                    '=',
                    DB::raw('(select max(id) from sale_person_tags where sale_person_tags.user_id = u.id and sale_person_tags.status = 0 )')
                );
            })
            ->leftJoin('admins as sales_person', 'sales_person.id','spt.admin_id')
            ->leftJoin('sale_tier_tags as stt', function($join){
                $join->on('stt.user_id','u.id')
                ->where(
                    'stt.id',
                    '=',
                    DB::raw('(select max(id) from sale_tier_tags where sale_tier_tags.user_id = u.id)')
                );
            })
            ->leftJoin('admins as kam', 'kam.id','stt.kam')
            ->leftjoin('sales_commissions as sc', 'sc.shipper_id', '=', 'u.id')
            ->leftjoin('sales_commission_users as scu', function($join){
                $join->on('scu.sales_commission_id', '=','sc.id')
                ->where(
                    'scu.id',
                    '=',
                    DB::raw('(select max(id) from sales_commission_users where sales_commission_users.tier_id = 3)')
                );
            })
            ->leftjoin('admins as scun', 'scun.id', '=', 'scu.user_id')
            ->leftJoin('shipping_modes as sm', 'shipments.shipping_mode_id', '=', 'sm.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->leftjoin('user_shipping_infos AS rsi', 'shipments.return_address_id', '=', 'rsi.id')
            ->leftjoin('cities AS rc', 'rsi.city_id', '=', 'rc.id')
            ->leftjoin('zones as z', 'z.id', '=', 'h.zone_id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->join('shipment_status as hss', function($join) {
                $join->on('hss.id', '=', DB::raw('(SELECT shipment_status.id
                FROM shipments_journey
                JOIN shipment_status ON shipments_journey.shipper_status_id = shipment_status.id
                WHERE shipments_journey.shipment_id = shipments.id
                ORDER BY shipments_journey.updated_at DESC
                LIMIT 1 OFFSET 1)'));
            })
            ->join('sub_category_segments as scs', 'u.sub_segment_id', '=', 'scs.id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where(
                        'sj.id',
                        '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)')
                    );
            })
            ->leftJoin('shipments_journey as journey', function ($join) {
                $join->on('journey.shipment_id', '=', 'shipments.id')
                    ->where(
                        'journey.id',
                        '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)')
                    );
            })
            ->leftjoin('admins as admin', 'admin.id', '=', 'journey.admin_id')
            ->leftjoin('shipment_status_reason as ssr', 'ssr.id', '=', 'journey.status_reason_id')
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.type', '=', 0);
            })
            ->leftJoin('shipments_journey as sjr', function ($join) use ($connection) {
                $join->on('sjr.shipment_id', '=', 'shipments.id')
                    ->where(
                        'sjr.id',
                        '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(20,21,22,23,24,25,47,48,60,68))')
                    );
            })
            ->leftjoin('products as p', 'p.id', '=', 'si.product_type_id')
            ->leftJoin('cargo_manifest_bag_shipments as cmbs', function ($join) {
                $join->on('cmbs.shipment_id', '=', 'shipments.id')
                    ->where(
                        'cmbs.id',
                        '=',
                        DB::connection('reports')->raw('(select max(id) from cargo_manifest_bag_shipments where cargo_manifest_bag_shipments.shipment_id = shipments.id)')
                    );
            })
            ->leftjoin('cargo_manifest_bags as cmb', 'cmb.id', '=', 'cmbs.cargo_manifest_bag_id')
            ->leftjoin('cities as cmbh', 'cmbh.id', '=', 'cmb.current_hub_id')
            ->leftJoin('crm_requests as cr', function ($join) {
                $join->on('cr.shipment_id', '=', 'shipments.id')
                    ->where(
                        'cr.id',
                        '=',
                        DB::connection('reports')->raw('(select max(id) from crm_requests where crm_requests.shipment_id = shipments.id)')
                    );
            })
            ->leftjoin('cargo_manifest_bag_statuses as cargo_status', 'cargo_status.id', '=', 'cmb.status_id')
            ->leftjoin('bag_statuses as bs', 'bs.id', '=', 'cmb.status_id')
            ->leftJoin('shipments_journey as sjfa', function ($join) use ($connection) {
                $join->on('sjfa.shipment_id', '=', 'shipments.id')
                    ->where(
                        'sjfa.id',
                        '=',
                        DB::connection($connection)->raw('(select min(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id  = 5)')
                    );
            })
            ->leftJoin('shipments_journey as sjrp', function ($join) use ($connection) {
                $join->on('sjrp.shipment_id', '=', 'shipments.id')
                    ->where(
                        'sjrp.id',
                        '=',
                        DB::connection($connection)->raw('(select min(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id  = 53)')
                    );
            })
            ->leftjoin('consignee_address_areas as caa', 'caa.shipment_id', '=', 'shipments.id')
            ->leftjoin('city_areas as ca', 'ca.id', '=', 'caa.city_area_id')
            ->leftJoin('shipment_scanning_journeys as ssj_last_location', function ($join) {
                $join->on('ssj_last_location.shipment_id', '=', 'journey.shipment_id')
                    ->whereRaw('ssj_last_location.id = (
                                select max(id) 
                                from shipment_scanning_journeys 
                                where shipment_scanning_journeys.shipment_id = journey.shipment_id
                                and shipment_scanning_journeys.screen_location_id not in (9, 18)
                            )');
            })
            ->when(\DB::raw('ssj_last_location.user_type = 1'), function ($join) {
                $join->leftJoin('admins as adm', function ($join) {
                    $join->on('adm.id', '=', 'ssj_last_location.admin_id')
                        ->where('adm.role_id', '<>', 1);
                });
            })
            ->leftJoin('shipment_scanning_journey_area_logs as ssjal_last_location', function($join){
                $join->on('ssjal_last_location.shipment_scanning_journey_id', '=', 'ssj_last_location.id')
                    ->where('ssjal_last_location.hub_id', '=', DB::raw('journey.city_id'))
                    ->where('ssjal_last_location.shipment_id', '=', DB::raw('journey.shipment_id'));
            })
            ->leftJoin('intercept_re_book_request_histories as irb', function ($join) {
                $join->on('irb.shipment_id', '=', 'shipments.id')
                    ->whereRaw('irb.id = (
                                    select max(id) 
                                    from intercept_re_book_request_histories 
                                    where intercept_re_book_request_histories.shipment_id = shipments.id
                                    and intercept_re_book_request_histories.intercept_type = 1
                                )');
            })
            ->leftjoin('cities as intercept_approved', 'intercept_approved.id', '=', 'irb.old_consignee_city_id')
            ->leftJoin('shipments_journey as sjms', function ($join) use ($connection) {
                $join->on('sjms.shipment_id', '=', 'shipments.id')
                    ->where(
                        'sjms.id',
                        '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id  = 68)')
                    );
            })
            ->leftjoin('cities as shipmentMisrouted', function ($join) {
                $join->on('shipmentMisrouted.id', '=', 'sjr.city_id')
                    ->where('sjr.shipper_status_id', '=', 68);
            })
            ->leftJoin('city_areas as ca_scanning_last_location_name', 'ssjal_last_location.area_id', '=', 'ca_scanning_last_location_name.id')
            ->leftJoin('shipment_scanning_screen_locations as last_screen_location', 'last_screen_location.id', '=', 'ssj_last_location.screen_location_id')
            ->leftJoin('shipments_journey as destination_sj', function ($join) {
                $join->on('destination_sj.shipment_id', '=', 'shipments.id')
                        ->where(function ($query) {
                            // First check if status 4 (Arrived at Destination)
                            $query->where('destination_sj.shipper_status_id', '=', 4)
                                ->orWhere(function ($query) {
                                        // If not, check status 2 (Arrived at Origin)
                                        $query->where('destination_sj.shipper_status_id', '=', 2)
                                            ->whereNotExists(function ($query) {
                                                // Check if a status 4 record exists for this shipment_id
                                                $query->select(DB::raw(1))
                                                    ->from('shipments_journey')
                                                    ->whereRaw('shipments_journey.shipment_id = shipments.id')
                                                    ->where('shipments_journey.shipper_status_id', 4);
                                            });
                                    });
                        })
                ->where('destination_sj.id', '=', DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id in (4, 2))'));
            })
            ->whereNotIn('shipments.shipper_status_id', [1, 14, 17, 25, 31, 36, 38, 51])
            ->whereNotNull('shipments.tracking_number')
            // ->where(function ($query) {
            //     $query->whereNull('cr.status_id')
            //           ->orWhere('cr.status_id', '!=', 4);
            // })
            ->select($select)
            ->groupBy('shipments.id')
        ->get();

        // $receive_deliveries_report_array[] = ['Quality of Service Report'];
        $sales_array['header'] = 
        [
            'S. No.',
            'Tracking No.',
            'Order ID',
            'Account No.',
            'Shipper',
            'Sales Person',
            'KAM',
            'Sub Segment',
            'Consignee Name',
            'First Attempt Date',
            'Rider Picked Status Date',
            'Status',
            'Last Location Screen Name',
            'Sub Hub',
            'Last Location Updated At',
            'Entry Method',
            'Reason',
            'Remarks',
            'Total Attempt',
            'History Status',
            'Cargo Status',
            'Bag Seal Number',
            'Bag Status',
            'Service',
            'Origin Arrival Date',
            'Destination Arrival Date',
            'Last Status Date',
            'Booked Status Date',
            'Shipping Mode',
            'Origin',
            'Destination',
            'Hub',
            'Area',
            'Concerned Hub',
            'Return City',
            'Zone',
            'Product Description',
            'Collection Amount',
            'Aging (Arrival)',
            'Aging (Last Status)',
        ];

        $sales_array[] = [
            'S. No.'                     => '',
            'Tracking No.'               => '',
            'Order ID'                   => '',
            'Account No.'                => '',
            'Shipper'                    => '',
            'Sales Person'               => '',
            'KAM'                        => '',
            'Sub Segment'                => '',
            'Consignee Name'             => '',
            'First Attempt Date'         => '',
            'Rider Picked Status Date'   => '',
            'Status'                     => '',
            'Last Location Screen Name'  => '',
            'Sub Hub'                    => '',
            'Last Location Updated At'   => '',
            'Entry Method'               => '',
            'Reason'                     => '',
            'Remarks'                    => '',
            'Total Attempt'              => '',
            'History Status'             => '',
            'Cargo Status'               => '',
            'Bag Seal Number'            => '',
            'Bag Status'                 => '',
            'Service'                    => '',
            'Origin Arrival Date'        => '',
            'Destination Arrival Date'   => '',
            'Last Status Date'           => '',
            'Booked Status Date'         => '',
            'Shipping Mode'              => '',
            'Origin'                     => '',
            'Destination'                => '',
            'Hub'                        => '',
            'Area'                       => '',
            'Concerned Hub'              => '',
            'Return City'                => '',
            'Zone'                       => '',
            'Product Description'        => '',
            'Collection Amount'          => '',
            'Aging (Arrival)'            => '',
            'Aging (Last Status)'        => '',
        ];

        if($sales) {
            foreach ($sales as $sale) {
                $serial++;
                $total_attempt = '-';

                $delivery_note_ids = DeliveryNoteShipment::where('shipment_id', $sale->shId)
                    ->pluck('delivery_note_id');
                if ($delivery_note_ids->isNotEmpty()) {
                    $total_attempt = DeliveryNote::whereIn('id', $delivery_note_ids)
                        ->whereHas('rider', fn($query) => $query->where('operation_rider_id', 1))
                        ->count();
                }

                $hub_name = '-';
                if ($sale->current_hub_id) {
                    $hub_name = $sale->current_hub_name;
                } elseif (in_array($sale->shipper_status_id, [1, 2, 61])) {
                    $hub_name = $sale->origin;
                } else {
                    $hub_name = $sale->hub;
                }

                $aging = Carbon::now()->diffInDays($sale->arrival) ?: '-';
                $aging_last_status = Carbon::now()->diffInDays($sale->last_status_date) ?: '-';

                $kam = '-';
                if ($sale->stt_kam_id == null) {
                    $kam = $sale->scu_kam_name;
                } else {
                    $kam = $sale->stt_kam_name;
                }

                $sub_hub = '-';
                if ($sale->scanned_by_user_type == 5) {
                    $rider = Rider::find($sale->scanned_by_id);
                    $sub_hub = $rider->area->name ?? '-';
                } elseif (isset($sale->ca_scanning_last_location_name)) {
                    $sub_hub = $sale->ca_scanning_last_location_name;
                }

                $entry_method = '-';
                if ($sale->entry_method == 1){
                    $entry_method = 'Scanned';
                } else {
                    $entry_method = 'Manual';
                }

                $sales_array[] = [
                    'S. No.'                 => $serial,
                    'Tracking No.'           => $sale->tracking_number_link,
                    'Order ID'               => $sale->order_id,
                    'Account No.'            => $sale->account_no,
                    'Shipper'                => $sale->shipper,
                    'Sales Person'           => $sale->sales_person_name,
                    'KAM'                    => $kam,
                    'Sub Segment'            => $sale->sub_segment,
                    'Consignee Name'         => $sale->name,
                    'First Attempt Date'     => $sale->first_attempt_date,
                    'Rider Picked Status Date' => $sale->rider_picked_status_date,
                    'Status'                 => $sale->status,
                    'Last Location Screen name' => $sale->last_location_screen_location_name,
                    'Sub Hub'                => $sub_hub,
                    'Last Location Updated At' => $sale->last_location_updated_at,
                    'Entry Method'           => $entry_method,
                    'Reason'                 => $sale->reason,
                    'Remarks'                => $sale->remarks,
                    'Total Attempt'          => $total_attempt,
                    'History Status'         => $sale->history_status,
                    'Cargo Status'           => $sale->cargo_status,
                    'Bag Seal Number'        => $sale->seal_number,
                    'Bag Status'             => $sale->bag_status,
                    'Service'                => $sale->service_type,
                    'Origin Arrival Date'    => $sale->arrival,
                    'Destination Arrival Date' => $sale->destination_arrival_date,
                    'Last Status Date'       => $sale->last_status_date,
                    'Booked Status Date'     => $sale->created_at,
                    'Shipping Mode'          => $sale->shipping_mode,
                    'Origin'                 => $sale->origin,
                    'Destination'            => $sale->destination,
                    'Hub'                    => $sale->hub,
                    'Area'                   => $sale->area,
                    'Concerned Hub'          => $hub_name,
                    'Return City'            => $sale->return_city,
                    'Zone'                   => $sale->zone,
                    'Product Description'    => $sale->description,
                    'Collection Amount'      => $sale->amount,
                    'Aging (Arrival)'        => $aging,
                    'Aging (Last Status)'    => $aging_last_status,
                ];
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getDefaultColumnDimension()->setWidth(20);
            
            // Define last column dynamically
            $lastColumn = 'AN'; // Adjust based on actual column count
            
            // Set the title in the first row
            $sheet->setCellValue('A1', 'Quality of Service Report');
            
            // Merge cells for title
            $sheet->mergeCells("A1:{$lastColumn}1");
            
            // Apply styling to the title
            $titleStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 16, // Larger Font
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A1')->applyFromArray($titleStyle);
            
            // Populate headers in row 2
            $sheet->fromArray([$sales_array['header']], NULL, 'A2', true);
            
            // Apply styling to the header row
            $headerStyle = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];
            $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray($headerStyle);
            
            // Populate data in Excel (Starting from Row 3)
            $dataRows = array_values(array_diff_key($sales_array, ['header' => ''])); // Ensure only data rows

            $sheet->fromArray($dataRows, NULL, 'A3', true);
            
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename=quality_service_report_.xlsx"');
            header('Cache-Control: max-age=0');
            $date_file_name = Carbon::today()->format('Y_m_d');
            $file_name_without_path = "qsr_pending_deliveries_reports/quality_service_report_" . $date_file_name . ".xlsx";
            $file_path = public_path("qsr_pending_deliveries_reports");
            $file_name = $file_path . "/quality_service_report_" . $date_file_name . ".xlsx";

            // Ensure the directory exists
            if (!File::isDirectory($file_path)) {
                File::makeDirectory($file_path, 0755, true, true);
            }

            $writer->save($file_name);

            return url('/') . '/' . $file_name_without_path;
        }
    }

    static public function pending_deliveries_daily_report($to)
    {
        $serial = 0;

        $status = array(2, 4, 6, 7, 8, 9, 10, 13, 15, 49, 55, 59); //for pending deliveries
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->leftJoin('zones as z', 'z.id', '=', 'dc.zone_id')
            ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftJoin('consignee_address_areas as caa', 'caa.shipment_id', '=', 'shipments.id')
            ->leftJoin('city_areas as ca', 'ca.id', '=', 'caa.city_area_id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->join('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where(
                        'shipments_journey.id',
                        '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)')
                    );
            })
            ->leftjoin('shipments_journey as ras', function ($join) {
                $join->on('ras.shipment_id', '=', 'shipments.id')
                    ->where(
                        'ras.id',
                        '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 13)')
                    );
            })
            ->leftjoin('admins as agent', 'agent.id', '=', 'ras.admin_id')
            ->join('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where(
                        'sj.id',
                        '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)')
                    );
            })
            ->leftJoin('shipments_journey as sjd', function ($join) {
                $join->on('sjd.shipment_id', '=', 'shipments.id')
                    ->where(
                        'sjd.id',
                        '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 4)')
                    );
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
            ->leftjoin('intercept_re_book_request_histories as irrh', 'irrh.shipment_id', '=', 'shipments.id')
            ->leftjoin('crm_requests as crm', function ($join) {
                $join->on('crm.shipment_id', '=', 'shipments.id')
                    ->whereIn('crm.status_id', [DB::raw(2), DB::raw(3), DB::raw(5)])
                    ->where('crm.case_nature_id', DB::raw(1));
            })
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where(
                        'si.id',
                        '=',
                        DB::raw('(select max(id) from shipment_items where shipment_items.shipment_id = shipments.id)')
                    );
            })
            ->leftjoin('products as prod', 'prod.id', '=', 'si.product_type_id')
            ->leftjoin('star_shippers as sts', 'sts.user_id', '=', 'u.id')

            ->leftjoin('delivery_note_shipments as dns', function ($join) {
                $join->on('dns.shipment_id', '=', 'shipments.id')
                    ->where(
                        'dns.delivery_note_id',
                        '=',
                        DB::raw('(select max(delivery_note_id) from delivery_note_shipments WHERE shipment_id = shipments.id)')
                    );
            })
            ->leftJoin('delivery_notes as dn', 'dn.id', '=', 'dns.delivery_note_id')
            ->leftjoin('riders as r', 'r.id', '=', 'dn.rider_id')

            ->select(
                'agent.name as agent', 'shipments.id as shId', 'shipments.tracking_number as tracking_number_link', 'shipments.tracking_number', 'u.name as shipper', 'oc.name as origin',
                'dc.name as destination', 'dc.id as destination_city_id', 'h.name as hub', 'shipments.consignee_name', 'shipments.consignee_phone_number_1', 
                'shipments.consignee_phone_number_2', 'shipments.consignee_address', 'shipments.amount', 'sm.mode as shipping_mode', 'bt.booking_type as service_type',
                'ss.name as status', 'ssr.name as reason', 'shipments_journey.remarks as remarks', 'shipments_journey.created_at as status_date', 'shipments_journey.created_at as current_status_date',
                'sjd.created_at as destination_arrival', 'sj.created_at as arrival', 'shipments.booking_type_id',
                'usi.poc', 'crm.id as complaint', 'shipments.actual_weight as weight', 'si.description as shipment_description',
                'prod.product_name as product_type', 'sts.status as star_status', 'ca.name as area', 'r.name as last_rider', 'z.name as d_zone', 'r.trax_id as rider_trax_id'
            )
            ->whereIn('shipments.shipper_status_id', $status)
            // ->whereBetween('shipments_journey.created_at', [$from, $to])->get();
            ->where('shipments_journey.created_at','<=', $to)->get();

            $pending_deliveries_report_array[] = ['Pending Deliveries Report'];
            $pending_deliveries_report_array['header'] = ['S. No.', 'Tracking No.', 'Shipper', 'Origin', 'Destination', 'Hub', 'Area', 
            'Consignee Name', 'Consignee Phone', 'Reattempt By', 'Address', 'Sub Station', 'Weight', 'Collection Amount', 'Product Type', 'Product Description', 'Shipping Mode', 'Service Type', 
            'Status', 'Reason', 'Remarks', 'Origin Arrival Date', 'Destination Zone', 'Destination Arrival Date', 'Last Rider', 'Last Rider Trax ID', 'Status Date'];

            $pending_deliveries_report_array[] = ['S. No.' => '', 'Tracking No.' => '', 'Shipper' => '', 'Origin' => '', 'Destination' => '', 'Hub' => '', 'Area' => '', 
            'Consignee Name' => '', 'Consignee Phone' => '', 'Reattempt By' => '', 'Address' => '', 'Sub Station' => '', 'Weight' => '', 'Collection Amount' => '', 'Product Type' => '', 
            'Product Description' => '', 'Shipping Mode' => '', 'Service Type' => '', 'Status' => '', 'Reason' => '', 'Remarks' => '', 'Origin Arrival Date' => '', 'Destination Zone' => '', 
            'Destination Arrival Date' => '', 'Last Rider' => '', 'Last Rider Trax ID' => '', 'Status Date' => ''];

            if($shipments) {
                foreach ($shipments as $shipment) {
                    $serial++;
                    $tracking_number = $shipment->tracking_number;
                    $shipper = $shipment->shipper;
                    $origin = $shipment->origin;
                    $destination = $shipment->destination;
                    $hub = $shipment->hub;
                    $area = $shipment->area;
                    $consignee_name = $shipment->consignee_name;
                    $consignee_phone = $shipment->consignee_phone;
                    $reattempt_by = $shipment->agent;
                    $consignee_address = $shipment->consignee_address;
                    $sub_station = $shipment->sub_station;
                    $weight = $shipment->history_weight;
                    $amount = $shipment->amount;
                    $product_type = $shipment->product_type;
                    $shipment_description = $shipment->shipment_description;
                    $shipping_mode = $shipment->shipping_mode;
                    $service_type = $shipment->service_type;
                    $status = $shipment->status;
                    $reason = $shipment->reason;
                    $remarks = $shipment->remarks;
                    $origin_arrival_date = $shipment->arrival;
                    $destination_zone = $shipment->last_status_date;
                    $destination_arrival = $shipment->destination_arrival;
                    $last_rider = $shipment->last_rider;
                    $rider_trax_id = $shipment->rider_trax_id;
                    $current_status_date = $shipment->current_status_date;

                    $pending_deliveries_report_array[] = ['S. No.' => $serial, 'Tracking No.' => $tracking_number, 'Shipper' => $shipper ,'Origin' => $origin ,'Destination' => $destination, 
                    'Hub' => $hub ,'Area' => $area ,'Consignee Name' => $consignee_name , 'Consignee Phone' => $consignee_phone , 'Reattempt By' => $reattempt_by, 'Address' => $consignee_address, 
                    'Sub Station' => $sub_station, 'Weight' => $weight, 'Collection Amount' => $amount, 'Product Type' => $product_type ,'Product Description' => $shipment_description, 
                    'Shipping Mode' => $shipping_mode, 'Service Type' => $service_type, 'Status' => $status, 'Reason' => $reason, 'Remarks' => $remarks,'Origin Arrival Date' => $origin_arrival_date , 
                    'Destination Zone' => $destination_zone ,'Destination Arrival Date' => $destination_arrival , 'Last Rider' => $last_rider,'Last Rider Trax ID' => $rider_trax_id, 
                    'Status Date' => $current_status_date ];
                }

                $cell_st = [
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                    'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
                ];

                $serial = $serial + 6;
                
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getDefaultColumnDimension()->setWidth(27);
                $sheet->fromArray($pending_deliveries_report_array, NULL, 'A2', true);
                $sheet->getStyle("A2:AA2")->applyFromArray($cell_st);
                $sheet->getStyle("A3:AA3")->applyFromArray($cell_st);
                $sheet->getStyle("A" . $serial . ":N" . $serial)->applyFromArray($cell_st);
                $sheet->setTitle('Quality of Service Report');
                $sheet->mergeCells('A2:AL2');

                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename=quality_service_report_.xlsx"');
                header('Cache-Control: max-age=0');
                $date_file_name = Carbon::today()->format('Y_m_d');
                $file_name_without_path = "qsr_pending_deliveries_reports/pending_deliveries_report_" . $date_file_name . ".xlsx";
                $file_path = public_path("qsr_pending_deliveries_reports"); // Specify the directory where you want to save the file
                $file_name = $file_path . "/pending_deliveries_report_" . $date_file_name . ".xlsx";

                // Ensure the directory exists
                if (!File::isDirectory($file_path)) {
                    File::makeDirectory($file_path, 0755, true, true);
                }

                $writer->save($file_name);

                return url('/') . '/' . $file_name_without_path;
            }
    }

    static public function deliveries_receive() {
        $pending_deliveries = DeliveryNote::join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->leftjoin('city_areas as cas', 'cas.id', '=', 'riders.area_id')
            ->join('rider_types', 'rider_types.id', '=', 'riders.rider_type_id')
            ->leftjoin('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->join('admins', 'admins.id', '=', 'delivery_notes.admin_id')
            ->leftjoin('zones as z', 'oc.zone_id', '=', 'z.id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'delivery_notes.updated_by')
            ->leftjoin('delivery_note_shipments', 'delivery_note_shipments.delivery_note_id', 'delivery_notes.id')
            ->leftjoin('shipments', 'delivery_note_shipments.shipment_id', '=', 'shipments.id') // Join with shipments table
            ->select(
                'delivery_notes.id as delivery_note',
                'delivery_notes.id as delivery_note_id',
                'oc.name as hub',
                'riders.name as rider',
                'routes.code as route_code',
                'routes.start as route_start',
                'routes.end  as route_end',
                'admins.name as assignee',
                'delivery_notes.created_at',
                'delivery_notes.total_cod_amount as amount',
                'delivery_notes.shipments_count',
                'delivery_notes.shipments_count as shipments_count_link',
                'delivery_notes.pending_status',
                'delivery_notes.last_updated_at',
                'ad.name as updated_by',
                'delivery_notes.special_rider',
                'delivery_notes.special_rider_name',
                'delivery_notes.special_rider_phone',
                'delivery_notes.delivered_shipments as delivered_shipments',
                DB::raw('(SELECT COUNT(d.id) FROM delivery_notes AS d INNER JOIN delivery_note_shipments AS dns ON d.id = dns.delivery_note_id WHERE dns.delivery_note_id = delivery_notes.id AND dns.status = 0) AS shipments_unverified_count'),
                'oc.business_category_id as business_category',
                'z.name as zone_name',
                'riders.operation_rider_id',
                'riders.rider_type_id',
                'rider_types.name as rt',
                'delivery_notes.created_via_app as created_via',
                'riders.trax_id as rider_trax_id',
                'cas.name as city_area_name',
                DB::raw('SUM(shipments.actual_weight) as total_weight') // Calculate total weight

            )
            ->where('delivery_notes.status', 0)
            ->groupBy('delivery_notes.id')
            ->orderBy('delivery_notes.created_at', 'desc')
        ->get();

        $receive_deliveries_report_array = [];

        // Report Title
        $receive_deliveries_report_array[] = ['Receive Deliveries Report'];
        
        // Header Row
        $headers = [
            'S. No.', 
            'Delivery Note No.', 
            'Hub', 
            'Zone', 
            'Business Category', 
            'Rider ID', 
            'Rider', 
            'Area',
            'Rider Type', 
            'Rider Category', 
            'Route', 
            'No. Of Shipments', 
            'E-Comm (COD)',
            'General Logistics (Retail)',
            'General Logistics - E-Comm (Express)',
            'Other Sub-Segments',
            'Total Weight', 
            'No. Of Pending Shipments',
            'No. Of Delivered Shipments',
            'E-Comm (COD)', 
            'General Logistics (Retail)', 
            'General Logistics - E-Comm (Express)', 
            'Other Sub-Segments', 
            'Assigned By', 
            'Assigned Date', 
            'Total Collection', 
            'Status', 
            'Last Updated (Date)',
            'Last Updated By', 
            'Created Via'
        ];
        
        // Add headers to the report array
        $receive_deliveries_report_array[] = $headers;
        
        if (count($pending_deliveries) > 0) {
            $serial = 0;
            $receive_deliveries_report_array = [[
                'S. No.', 
                'Delivery Note No.', 
                'Hub', 
                'Zone', 
                'Business Category', 
                'Rider ID', 
                'Rider', 
                'Area',
                'Rider Type', 
                'Rider Category', 
                'Route', 
                'No. Of Shipments', 
                'E-Comm (COD)',
                'General Logistics (Retail)',
                'General Logistics - E-Comm (Express)',
                'Other Sub-Segments',
                'Total Weight', 
                'No. Of Pending Shipments',
                'No. Of Delivered Shipments',
                'E-Comm (COD)', 
                'General Logistics (Retail)', 
                'General Logistics - E-Comm (Express)', 
                'Other Sub-Segments', 
                'Assigned By', 
                'Assigned Date',
                'Total COD', 
                'Status', 
                'Last Updated (Date)',
                'Last Updated By', 
                'Created Via'
            ]];

            foreach ($pending_deliveries as $pending_delivery) {
                $serial++;

                $deliveryNoteId = [$pending_delivery->delivery_note_id];

                // ECOM COD
                $excel_ecom_cod = self::get_segment_type('delivery_note_shipments', $deliveryNoteId, 2, 5, null, 'delivery_note_id');
                $excel_ecom_cod = $excel_ecom_cod > 0 ? $excel_ecom_cod : '-';

                // GENERAL RETAIL
                $excel_general_retail = self::get_segment_type('delivery_note_shipments', $deliveryNoteId, 1, 12, null, 'delivery_note_id');
                $excel_general_retail = $excel_general_retail > 0 ? $excel_general_retail : '-';

                // GENERAL LOGISTICS ECOM EXPRESS
                $count_general = self::get_segment_type('delivery_note_shipments', $deliveryNoteId, 1, 2, null, 'delivery_note_id');
                $count_ecomm = self::get_segment_type('delivery_note_shipments', $deliveryNoteId, 2, 7, null, 'delivery_note_id');
                $excel_general_ecom_express = $count_general + $count_ecomm;
                $excel_general_ecom_express = $excel_general_ecom_express > 0 ? $excel_general_ecom_express : '-';

                // OTHERS
                $excel_others = self::get_segment_type('delivery_note_shipments', $deliveryNoteId, [1, 2], [1, 3, 4, 6, 8, 9, 10, 11], null, 'delivery_note_id');
                $excel_others = $excel_others > 0 ? $excel_others : '-';

                // DELIVERED ECOM COD
                $delivered_excel_ecom_cod = self::get_delivered_shipments($deliveryNoteId);
                if (!empty($delivered_excel_ecom_cod)) {
                    $delivered_excel_ecom_cod = self::get_segment_type('delivery_note_shipments', $delivered_excel_ecom_cod, 2, 5, 'delivered', 'delivery_note_id');
                }
                $delivered_excel_ecom_cod = $delivered_excel_ecom_cod > 0 ? $delivered_excel_ecom_cod : '-';

                // DELIVERED GENERAL RETAIL
                $delivered_excel_general_retail = self::get_delivered_shipments($deliveryNoteId);
                if (!empty($delivered_excel_general_retail)) {
                    $delivered_excel_general_retail = self::get_segment_type('delivery_note_shipments', $delivered_excel_general_retail, 1, 12, 'delivered', 'delivery_note_id');
                }
                $delivered_excel_general_retail = $delivered_excel_general_retail > 0 ? $delivered_excel_general_retail : '-';

                // DELIVERED GENERAL ECOM EXPRESS
                $delivered_shipments = self::get_delivered_shipments($deliveryNoteId);
                $delivered_excel_general_ecom_express = !empty($delivered_shipments)
                    ? self::get_segment_type('delivery_note_shipments', $delivered_shipments, [1,2], [1,3,4,6,8,9,10,11], 'delivered', 'delivery_note_id') 
                    : 0;

                $delivered_excel_general_ecom_express = $delivered_excel_general_ecom_express > 0 
                    ? $delivered_excel_general_ecom_express 
                    : '-';

                // DELIVERED EXCEL OTHERS
                $delivered_ids = self::get_delivered_shipments($deliveryNoteId);
                $delivered_excel_others = !empty($delivered_ids)
                    ? self::get_segment_type('delivery_note_shipments', $delivered_ids, [1, 2], [1, 3, 4, 6, 8, 9, 10, 11], 'delivered', 'delivery_note_id')
                    : 0;
                
                $total_cod_amount = DeliveryNote::where('id', $pending_delivery->delivery_note_id)->first()->total_cod_amount;

                $receive_deliveries_report_array[] = [
                    $serial,
                    $pending_delivery->delivery_note_id,
                    $pending_delivery->hub,
                    $pending_delivery->zone_name,
                    $pending_delivery->business_category == 1 ? 'Domestic' : 'International',
                    $pending_delivery->rider_trax_id,
                    $pending_delivery->rider,
                    $pending_delivery->city_area_name,
                    $pending_delivery->rt,
                    $pending_delivery->operation_rider_id == 1 ? 'Field In Operations' : 'Hold In Operations',
                    $pending_delivery->route_code . ' (' .  $pending_delivery->route_start . ' to ' . $pending_delivery->route_end . ') ',
                    $pending_delivery->shipments_count,
                    $excel_ecom_cod,
                    $excel_general_retail,
                    $excel_general_ecom_express,
                    $excel_others,
                    $pending_delivery->total_weight,
                    $pending_delivery->shipments_unverified_count,
                    $pending_delivery->delivered_shipments,
                    $delivered_excel_ecom_cod,
                    $delivered_excel_general_retail,
                    $delivered_excel_ecom_cod,
                    $delivered_excel_others,
                    $pending_delivery->assignee,
                    $pending_delivery->created_at,
                    $total_cod_amount,
                    $pending_delivery->pending_status == 0 ? 'Pending for Update' : 'Pending for Verification',
                    $pending_delivery->last_updated_at,
                    $pending_delivery->updated_by,
                    $pending_delivery->created_via == 0 ? 'Sonic' : 'App'
                ];
            }
        
            // Create Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
        
            // Set default column width
            $sheet->getDefaultColumnDimension()->setWidth(20);
        
            // Populate data in Excel
            $sheet->fromArray($receive_deliveries_report_array, NULL, 'A2', true);
        
            // Total Columns
            $lastColumn = 'AD'; // Adjust based on actual column count
        
            // Apply styling to the header row
            $headerStyle = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];
            $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray($headerStyle);
        
            // Set the title in the first row
            $sheet->setCellValue('A1', 'Receive Deliveries Report');
        
            // Merge cells for title
            $sheet->mergeCells("A1:{$lastColumn}1");
        
            // Apply styling: Bold, Centered, and Larger Font
            $titleStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 16, // Larger Font
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A1')->applyFromArray($titleStyle);
        
            // Save and Output
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="new_daily_receive_deliveries_report_.xlsx"');
            header('Cache-Control: max-age=0');
        
            $date_file_name = Carbon::today()->format('Y_m_d');
            $file_name_without_path = "reports/new_daily_receive_deliveries_report_" . $date_file_name . ".xlsx";
            $file_name = public_path($file_name_without_path);
            $writer->save($file_name);
        
            return url($file_name_without_path);
        }
        
    }

    public static function get_segment_type($table, $ids, $segment, $subSegment, $type = null, $column)
    {
        $shipmentIdsQuery = DB::connection('reports')->table($table)->whereIn($column, $ids);

        if ($type === null) {
            $shipmentIds = $shipmentIdsQuery->pluck('shipment_id');
        } else {
            $shipmentIds = $ids;
        }

        $query = DB::connection('reports')->table('shipments')
            ->join('users', 'users.id', '=', 'shipments.user_id')
            ->whereIn('shipments.id', $shipmentIds);

        if ($segment) {
            $query->whereIn('users.segment_id', (array) $segment);
        }

        if ($subSegment) {
            $query->whereIn('users.sub_segment_id', (array) $subSegment);
        }

        return $query->count();
    }

    public static function get_delivered_shipments($dn_ids){
        $dncc_status = array(14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38);
        $shipment_ids = DeliveryNoteShipment::whereIn('delivery_note_id', $dn_ids)->where('status', '>', 1)->whereNotIn('status', [8, 10, 11])->select('shipment_id')->get();
        return Shipment::whereIn('id', $shipment_ids)->whereIn('shipper_status_id', $dncc_status)->pluck('id');
    }


    static public function return_deliveries_receive() {
        $return_deliveries = ReturnNote::join('cities AS oc', 'return_notes.hub_id', '=', 'oc.id')
        ->join('riders', 'return_notes.rider_id', '=', 'riders.id')
        ->leftjoin('city_areas as ca', 'ca.id', '=', 'riders.area_id')
        ->join('admins', 'admins.id', '=', 'return_notes.admin_id')
        ->select([
            'return_notes.id as return_note', 
            'return_notes.id', 
            'return_notes.id as return_note_id', 
            'oc.name as hub', 
            'riders.name as rider',
            'admins.name as assignee', 
            'return_notes.created_at', 
            'return_notes.shipments_count', 
            'return_notes.shipments_count as shipments_count_link',
            'return_notes.status', 
            'riders.trax_id as rider_trax_id', 
            DB::raw('(SELECT COUNT(shipment_id) FROM return_note_shipments WHERE return_note_id = return_notes.id AND status = 0) AS shipments_unverified_count'), 
            DB::raw('(SELECT COUNT(id) FROM shipments_journey where shipper_status_id in (25, 31, 38) and reference_1_id = return_notes.id and verification = 1 ) as delivered_to_shipper_count'), 
            'ca.name as area', 
            'return_notes.created_at as created'
        ])
        ->whereIn('return_notes.status', [0, 3])
        ->orderBy('return_notes.id', 'desc')
        ->get();

        $return_deliveries_report_array = [];

        // Report Title
        $return_deliveries_report_array[] = ['Receive Return Deliveries Report'];
        
        // Header Row
        $headers = [
            'S. No.', 
            'Return Note No.', 
            'Hub', 
            'Rider ID', 
            'Area',
            'Rider', 
            'No. Of Shipments', 
            'E-Comm (COD)', 
            'General Logistics (Retail)', 
            'General Logistics - E-Comm (Express)',
            'Other Sub-Segments', 
            'No. Of Pending Shipments',
            'Assigned By', 
            'Assigned Date',  
            'Status',
        ];
        
        // Add headers to the report array
        $return_deliveries_report_array[] = $headers; 
        
        if (count($return_deliveries) > 0) {
            $serial = 0;
            
            foreach ($return_deliveries as $pending_delivery) {
                $serial++;
                $rider_trax_id = $pending_delivery->rider_trax_id;

                $returnId = [$pending_delivery->return_note];

                // EXCEL ECOM COD
                $excel_ecom_cod = 0;
                $excel_ecom_cod = DeliveryController::get_segment_type('return_note_shipments', $returnId, 2, 5, null,'return_note_id');
                $excel_ecom_cod = $excel_ecom_cod > 0 ? $excel_ecom_cod : '-';

                // EXCEL GENERAL RETAIL
                $excel_general_retail = 0;
                $excel_general_retail = DeliveryController::get_segment_type('return_note_shipments', $returnId, 1, 12, null,'return_note_id');
                $excel_general_retail = $excel_general_retail > 0 ? $excel_general_retail : '-';

                // EXCEL GENERAL ECOM EXPRESS
                $excel_general_ecom_express = 0;
                $count_general = 0;
                $count_ecomm = 0;
                $count_general = DeliveryController::get_segment_type('return_note_shipments', $returnId, 1, 2, null,'return_note_id');
                $count_ecomm = DeliveryController::get_segment_type('return_note_shipments', $returnId, 2, 7, null,'return_note_id');
                $excel_general_ecom_express = $count_general + $count_ecomm;
                $excel_general_ecom_express = $excel_general_ecom_express > 0 ? $excel_general_ecom_express : '-';

                // EXCEL OTHERS
                $excel_others = 0;
                $excel_others = DeliveryController::get_segment_type('return_note_shipments', $returnId, [1,2], [1,3,4,6,8,9,10,11], null,'return_note_id');
                $excel_others = $excel_others > 0 ? $excel_others : '-';

                $return_deliveries_report_array[] = [
                    $serial,
                    $pending_delivery->return_note,
                    $pending_delivery->hub,
                    $rider_trax_id,
                    $pending_delivery->area,
                    $pending_delivery->rider,
                    $pending_delivery->shipments_count,
                    $excel_ecom_cod,
                    $excel_general_retail,
                    $excel_general_ecom_express,
                    $excel_others,
                    $pending_delivery->shipments_unverified_count,
                    $pending_delivery->assignee,
                    $pending_delivery->created_at,
                    $pending_delivery->status == 0 ? 'Created' : 'Updated',
                ];
            }
        
            // Apply Excel formatting
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
        
            // Set default column width
            $sheet->getDefaultColumnDimension()->setWidth(20);
        
            // Populate data in Excel
            $sheet->fromArray($return_deliveries_report_array, NULL, 'A1', true);
        
            // Define last column dynamically
            $lastColumn = 'O'; // Adjust based on actual column count
        
            // Apply styling to the header row
            $headerStyle = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];
            $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray($headerStyle);
        
            // Set the title in the first row
            $sheet->setCellValue('A1', 'Receive Return Deliveries Report');
        
            // Merge cells for title
            $sheet->mergeCells("A1:{$lastColumn}1");
        
            // Apply styling to the title
            $titleStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 16, // Larger Font
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A1')->applyFromArray($titleStyle);
        
            // Save and Output
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="new_daily_return_receive_deliveries_report_.xlsx"');
            header('Cache-Control: max-age=0');
        
            $date_file_name = Carbon::today()->format('Y_m_d');
            $file_name_without_path = "reports/new_daily_return_receive_deliveries_report_" . $date_file_name . ".xlsx";
            $file_name = public_path($file_name_without_path);
            $writer->save($file_name);
        
            return url($file_name_without_path);
        }
        
        
    }

    static public function delivery_note_history($day) {
        $connection = 'reports_2';
        $deliveries = DB::connection($connection)->table('delivery_notes')
            ->leftjoin('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->leftjoin('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->leftjoin('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->leftjoin('admins as ccb', 'delivery_notes.cash_collected_by', '=', 'ccb.id')
            ->leftjoin('admins', 'admins.id', '=', 'delivery_notes.admin_id')
            ->leftjoin('admins as ub', 'ub.id', '=', 'delivery_notes.updated_by')
            ->leftjoin('rider_delivery_note_statuses as rdns', 'rdns.delivery_note_id', '=', 'delivery_notes.id')
            ->leftjoin('rider_deliveries as rd', 'rd.delivery_note_id', '=', 'delivery_notes.id')
            ->leftjoin('rider_types as rt', 'rt.id', '=', 'riders.rider_type_id')
            ->leftjoin('cities as c', 'c.id', '=', 'riders.city_id')
            ->leftJoin('city_areas as ca', function ($join) {
                $join->on('ca.id', '=', DB::raw("
                    CASE 
                        WHEN (riders.operation_rider_id = 2 AND riders.status = 1) THEN admins.area_id
                        ELSE riders.area_id
                    END
                "));
            })
            ->leftJoin('zones as zn', function ($join) {
                $join->on('zn.id', '=', DB::raw("CASE 
                    WHEN (riders.operation_rider_id = 2 and riders.status = 1) THEN oc.zone_id
                    ELSE c.zone_id 
                END"));
            })
            ->leftjoin('hbl_konnect_transaction_delivery_notes as hktdn', 'hktdn.delivery_note_id', '=', 'delivery_notes.id')
            ->leftjoin('one_link_out_for_delivery_shipment_payments as one_link_cash', 'delivery_notes.id', '=', 'one_link_cash.delivery_note_id')
            ->leftjoin('delivery_note_shipments', 'delivery_note_shipments.delivery_note_id', 'delivery_notes.id')
            ->leftjoin('shipments', 'delivery_note_shipments.shipment_id', '=', 'shipments.id')
            ->select([
                'delivery_notes.id as delivery_note', 
                'delivery_notes.id as delivery_note_id', 
                'oc.id as hub_id', 
                'oc.name as hub',
                'riders.trax_id as rider_trax_id',
                'riders.name as rider', 
                'routes.code as route', 
                'routes.start as route_start', 
                'routes.end as route_end', 
                'admins.name as assignee',
                'ub.name as updated_by',
                'delivery_notes.updated_at as updated_at',
                'delivery_notes.delivered_shipments', 
                'delivery_notes.delivered_shipments as delivered_shipments_link', 
                'delivery_notes.created_at', 
                'delivery_notes.received_cod_amount as amount',
                'delivery_notes.shipments_count', 
                'delivery_notes.shipments_count as shipments_count_link', 
                'delivery_notes.status', 
                'delivery_notes.pending_status',
                'delivery_notes.cash_collection_status', 
                'delivery_notes.dncc_status', 
                'delivery_notes.last_updated_at',
                'delivery_notes.cash_collected_by',
                'ccb.name as cash_collected', 
                'delivery_notes.cash_collected_at', 
                'delivery_notes.special_rider as dn_special_rider', 
                'delivery_notes.special_rider_name as dn_special_rider_name', 
                'delivery_notes.special_rider_phone as dn_special_rider_phone',
                'rdns.status as updated_via_app', 
                'rd.id as rider_delivery_id', 
                'rd.delivered_status as delivered_status', 
                'rd.picture_path as picture_path', 
                'rt.name as rider_type',
                'zn.name as zone_name', 
                'hktdn.transactions_amount as transactions_amount', 
                'hktdn.cash_amount as cash_amount', 
                'delivery_notes.one_link_payment_count',
                'delivery_notes.created_via_app as created_via', 
                'riders.operation_rider_id', 
                'ca.name as area',
                'one_link_cash.transaction_amount as one_link_amount',
                DB::raw('SUM(shipments.actual_weight) as total_weight')
            ])
            ->whereIn('riders.operation_rider_id', [1,2])
            ->whereBetween('delivery_notes.created_at', [
                Carbon::parse($day)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($day)->endOfDay()->format('Y-m-d H:i:s')
            ])
            ->orderBy('admins.name', 'desc')
            ->groupBy('delivery_notes.id')
        ->get();

        $delivery_note_history_report_array = [];

        // Report Title
        $delivery_note_history_report_array[] = ['Delivery Note History Report'];
        
        // Header Row
        $headers = [
            'S.No',
            'Delivery Note No.',
            'Status',
            'Hub',
            'Zone',
            'Rider ID',
            'Rider',
            'Area',
            'Rider Type',
            'Rider Category',
            'Route',
            'No. Of Shipments',
            'E-Comm (COD)',
            'General Logistics (Retail)',
            'General Logistics - E-Comm (Express)',
            'Other Sub-Segments',
            'Total Weight',
            'No. Of Shipments Delivered',
            'E-Comm (COD)',
            'General Logistics (Retail)',
            'General Logistics - E-Comm (Express)',
            'Other Sub-Segments',
            'Assigned By',
            'Assigned Date',
            'Updated By',
            'Updated Date',
            'Cash Collected By',
            'Cash Collection Date',
            'DNCC Amount',
            'Fintech Amount',
            'Fintech Amount %',
            'HBL Konnect Amount',
            'HBL Konnect Amount %',
            'Cash Amount',
            'One Link Payment Count',
            'Created Via',
            'Updated Via App',
            'Last Updated At',
        ];
        
        
        // Add headers to the report array
        $delivery_note_history_report_array[] = $headers; 

        if (count($deliveries) >= 0) {
            $serial = 0;
            
            foreach ($deliveries as $delivery) {
                $serial++;
                $rider_trax_id = $delivery->rider_trax_id;

                if ($delivery->status == 0) {
                    if ($delivery->pending_status == 0) {
                        $status = 'Pending for Update';
                    } elseif ($delivery->pending_status == 1) {
                        $status = 'Pending for Verification';
                    } else {
                        $status = '-';
                    }
                } elseif ($delivery->status == 1) {
                    if ($delivery->dncc_status == 1) {
                        $status = 'Completed';
                    } elseif ($delivery->cash_collection_status == 1) {
                        $status = 'Cash Collected';
                    } else {
                        $status = 'Verified';
                    }
                } elseif ($delivery->status == 4) {
                    $status = 'Canceled';
                } else {
                    $status = '-';
                }

                $rider = '-';
                if ($delivery->dn_special_rider) {
                    $rider = $delivery->rider . ' (' . $delivery->dn_special_rider_name . ')';
                } else {
                    $rider = $delivery->rider;
                }

                $route = $delivery->route . ' (' . $delivery->route_start . ' to ' . $delivery->route_end . ')';

                $updated_via_app = '-';
                if ($delivery->updated_via_app == 1) {
                    $updated_via_app = 'Partial';
                } else if ($delivery->updated_via_app == 2 ) {
                    $updated_via_app = 'Yes';
                } else if ($delivery->updated_via_app == 0) {
                    $updated_via_app = 'No';
                }

                $deliveryId = [$delivery->delivery_note];

                // EXCEL ECOM COD
                $excel_ecom_cod = 0;
                $excel_ecom_cod = self::get_segment_type('delivery_note_shipments',$deliveryId, 2, 5, null,'delivery_note_id');
                $excel_ecom_cod = $excel_ecom_cod > 0 ? $excel_ecom_cod : '-';

                // GENERAL LOGISTICS RETAIL
                $excel_general_retail = 0;
                $excel_general_retail = self::get_segment_type('delivery_note_shipments',$deliveryId, 1, 12, null,'delivery_note_id');
                $excel_general_retail = $excel_general_retail > 0 ? $excel_general_retail : '-';

                // GENERAL ECOM EXPRESS
                $excel_general_ecom_express = 0;
                $count_general = 0;
                $count_ecomm = 0;
                $count_general = self::get_segment_type('delivery_note_shipments',$deliveryId, 1, 2, null,'delivery_note_id');
                $count_ecomm = self::get_segment_type('delivery_note_shipments',$deliveryId, 2, 7, null,'delivery_note_id');
                $excel_general_ecom_express = $count_general + $count_ecomm;
                $excel_general_ecom_express = $excel_general_ecom_express > 0 ? $excel_general_ecom_express : '-';

                // EXCEL OTHERS
                $excel_others = 0;
                $excel_others = self::get_segment_type('delivery_note_shipments',$deliveryId, [1,2], [1,3,4,6,8,9,10,11], null, 'delivery_note_id');
                $excel_others = $excel_others > 0 ? $excel_others : '-';

                // DELIVERED EXCEL ECOM COD
                $delivered_excel_ecom_cod = 0;
                $delivered_excel_ecom_cod_shipments = self::get_delivered_shipments($deliveryId);
                if (!empty($delivered_excel_ecom_cod_shipments)) {
                    $delivered_excel_ecom_cod = self::get_segment_type('delivery_note_shipments', $delivered_excel_ecom_cod_shipments, 2, 5, 'delivered', 'delivery_note_id');
                }
                $delivered_excel_ecom_cod = $delivered_excel_ecom_cod > 0 ? $delivered_excel_ecom_cod : '-';

                // DELIVERED EXCEL GENERAL RETAIL
                $delivered_excel_general_retail = 0;
                $delivered_excel_general_retail_shipments = self::get_delivered_shipments($deliveryId);
                if (!empty($delivered_excel_general_retail_shipments)) {
                    $delivered_excel_general_retail = self::get_segment_type('delivery_note_shipments', $delivered_excel_general_retail_shipments, 1, 12, 'delivered', 'delivery_note_id');
                }
                $delivered_excel_general_retail = $delivered_excel_general_retail > 0 ? $delivered_excel_general_retail : '-';

                // DELIVERED EXCEL GENERAL ECOM EXPRESS
                $delivered_excel_general_ecom_express = 0;
                $delivered_general_count = 0;
                $delivered_ecom_count = 0;
                $delivered_excel_general_ecom_express_shipments = self::get_delivered_shipments($deliveryId);
                if (!empty($delivered_excel_general_ecom_express_shipments)) {
                    $delivered_general_count = self::get_segment_type('delivery_note_shipments', $delivered_excel_general_ecom_express_shipments, 1, 2, 'delivered', 'delivery_note_id');
                    $delivered_ecom_count = self::get_segment_type('delivery_note_shipments', $delivered_excel_general_ecom_express_shipments, 2, 7, 'delivered', 'delivery_note_id');
                }
                $delivered_excel_general_ecom_express = $delivered_general_count + $delivered_ecom_count;
                $delivered_excel_general_ecom_express = $delivered_excel_general_ecom_express > 0 ? $delivered_excel_general_ecom_express : '-';

                // DELIVERED EXCEL OTHERS
                $delivered_excel_others = 0;
                $delivered_excel_others_shipments = self::get_delivered_shipments($deliveryId);
                if (!empty($delivered_excel_others_shipments)) {
                    $delivered_excel_others = self::get_segment_type('delivery_note_shipments', $delivered_excel_others_shipments, [1,2], [1,3,4,6,8,9,10,11], 'delivered', 'delivery_note_id'); 
                }
                $delivered_excel_others = $delivered_excel_others > 0 ? $delivered_excel_others : '-';

                // FINTECH AMOUNT
                $dncc_amount = $delivery->amount;
                $fintech_amount_percent = '-';

                $delivery_note_shipment = DeliveryNoteShipment::where('delivery_note_id', $delivery->delivery_note)
                ->pluck('shipment_id')
                ->toArray();

                $fintech_shipments_charges = TraxPayTransaction::join('fintech_payment_details as fpd', 'fpd.trax_pay_id', '=', 'trax_pay_transactions.id')
                ->whereIn('trax_pay_transactions.shipment_id', $delivery_note_shipment)
                ->sum('fpd.cod_amount');

                $fintech_shipments_charges = $fintech_shipments_charges > 0 ? $fintech_shipments_charges : 0;

                if ($fintech_shipments_charges > 0 && $dncc_amount > 0) {
                    $fintech_amount_percent = $fintech_shipments_charges / $dncc_amount *100;
                }

                // HBL KONNECT
                $hbl_konnect_amount_percentage = '-';
                if ($delivery->transactions_amount > 0 && $dncc_amount) {
                    $hbl_konnect_amount_percentage = $delivery->transactions_amount / $dncc_amount *100;
                }

                $delivery_note_history_report_array[] = [
                    $serial,
                    $delivery->delivery_note,
                    $status,
                    $delivery->hub,
                    $delivery->zone_name,
                    $rider_trax_id,
                    $rider,
                    $delivery->area,
                    $delivery->rider_type,
                    $delivery->operation_rider_id == 1 ? 'Field In Operations' : 'Hold In Operations',
                    $route,
                    $delivery->shipments_count,
                    $excel_ecom_cod,
                    $excel_general_retail,
                    $excel_general_ecom_express,
                    $excel_others,
                    $delivery->total_weight,
                    $delivery->delivered_shipments,
                    $delivered_excel_ecom_cod,
                    $delivered_excel_general_retail,
                    $delivered_excel_general_ecom_express,
                    $delivered_excel_others,
                    $delivery->assignee,
                    $delivery->created_at,
                    $delivery->updated_by,
                    $delivery->updated_at,
                    $delivery->cash_collected,
                    $delivery->cash_collected_at,
                    $dncc_amount,
                    $fintech_shipments_charges,
                    $fintech_amount_percent,
                    $delivery->transactions_amount,
                    $hbl_konnect_amount_percentage,
                    $delivery->cash_amount,
                    $delivery->one_link_payment_count,
                    $delivery->created_via == 0 ? 'Sonic' : 'App',
                    $updated_via_app,
                    $delivery->last_updated_at,
                ];
            }
        
            // Apply Excel formatting
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
        
            // Set default column width
            $sheet->getDefaultColumnDimension()->setWidth(20);
        
            // Populate data in Excel
            $sheet->fromArray($delivery_note_history_report_array, NULL, 'A1', true);
        
            // Define last column dynamically
            $lastColumn = 'AL'; // Adjust based on actual column count
        
            // Apply styling to the header row
            $headerStyle = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];
            $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray($headerStyle);
        
            // Set the title in the first row
            $sheet->setCellValue('A1', 'Delivery Note History Report');
        
            // Merge cells for title
            $sheet->mergeCells("A1:{$lastColumn}1");
        
            // Apply styling to the title
            $titleStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 16, // Larger Font
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A1')->applyFromArray($titleStyle);
        
            // Save and Output
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="new_daily_delivery_note_history_report_.xlsx"');
            header('Cache-Control: max-age=0');
        
            $date_file_name = Carbon::today()->format('Y_m_d');
            $file_name_without_path = "reports/new_daily_delivery_note_history_report_" . $date_file_name . ".xlsx";
            $file_name = public_path($file_name_without_path);
            $writer->save($file_name);
            return url($file_name_without_path);
        }
    }

    static public function weight_qc_report($day) {
        $connection = 'reports_2';
        $shipments = DB::connection($connection)->table('shipments')->leftJoin('shipments_weight_types as sw', function ($join) {
            $join->on('shipments.id', '=', 'sw.shipment_id')
                    ->whereRaw('sw.id = (SELECT MIN(id) FROM shipments_weight_types WHERE shipment_id = shipments.id)');
        })
        ->leftJoin('weight_types as wt', 'sw.weight_type', '=', 'wt.id')->join('users as u', 'shipments.user_id', '=', 'u.id')
        ->leftJoin('shipping_modes as sm', 'shipments.shipping_mode_id', '=', 'sm.id')
        ->leftJoin('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
        ->leftJoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
        ->leftJoin('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
        ->leftJoin('sub_category_segments as scs', 'u.sub_segment_id', '=', 'scs.id')
        ->leftJoin('shipments_journey as arv_date', 'arv_date.shipment_id', '=', 'shipments.id')
        ->leftJoin('admins as user', 'user.id', '=', 'arv_date.admin_id')
        ->leftJoin('cities as hub', 'hub.id', '=', 'user.default_hub_id')
        ->leftJoin('city_areas as area', 'area.id', '=', 'user.area_id')
        ->select([
                'shipments.id as shId', 
                'shipments.tracking_number', 
                'u.name as shipper', 
                'oc.name as origin', 
                'dc.name as destination', 
                'shipments.created_at as booking_date', 
                'arv_date.created_at as arrival_date',
                'sm.mode as shipping_mode', 
                'shipments.estimated_weight', 
                'shipments.actual_weight', 
                'shipments.length', 
                'shipments.breadth', 
                'shipments.height', 
                'scs.name as sub_segment', 
                'sw.weight_type', 
                'wt.name as weight_type_name',
                'shipments.chargeable_weight', 
                'shipments.replacement_weight as replacement_weight', 
                'hub.name as hub_name',
                'area.name as area_name'
            ])
        ->where('arv_date.shipper_status_id', '=', 2)
        ->whereNotNull('shipments.actual_weight')
        ->whereBetween('arv_date.created_at', [
            Carbon::parse($day)->startOfDay()->format('Y-m-d H:i:s'),
            Carbon::parse($day)->endOfDay()->format('Y-m-d H:i:s')
        ])
        ->orderBy('hub_name', 'desc')
        ->get();

        $weight_qc_report = [];

        // Report Title
        $weight_qc_report[] = ['Weight QC Report'];
        
        // Header Row
        $headers = [
            'S. No.', 
            'Tracking Number',
            'Shipper Name',
            'Sub Segment',
            'Shipping Mode',
            'Origin',
            'Destination',
            'Arrival Date',
            'User Hub',
            'User Area',
            'Weight Input by Shipper',
            'Arrival Weight',
            'Weight Diffrence (Arrival Weight vs Shipper Weight)',
            'Replacement Weight',
            'Weighted As',
            'Weight Recorded As'
        ];
        
        // Add headers to the report array
        $weight_qc_report[] = $headers; 

        if (count($shipments) >= 0) {
            $serial = 0;
            
            foreach ($shipments as $shipment) {
                $serial++;
                $weight_difference = $shipment->actual_weight - $shipment->estimated_weight;
                $shipment->length;
                $weight_qc_report[] = [
                    $serial,
                    $shipment->tracking_number,
                    $shipment->shipper,
                    $shipment->sub_segment,
                    $shipment->shipping_mode,
                    $shipment->origin,
                    $shipment->destination,
                    $shipment->arrival_date,
                    $shipment->hub_name,
                    $shipment->area_name,
                    $shipment->estimated_weight,
                    $shipment->actual_weight,
                    $weight_difference,
                    $shipment->replacement_weight,
                    $shipment->length != null ? 'Volumetric' : 'Dense',
                    $shipment->weight_type_name
                ];
            }

            // Apply Excel formatting
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
        
            // Set default column width
            $sheet->getDefaultColumnDimension()->setWidth(20);

            // Set B column (tracking number) format to text
            $sheet->getStyle('B')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        
            // Populate data in Excel
            $sheet->fromArray($weight_qc_report, NULL, 'A1', true);
        
            // Define last column dynamically
            $lastColumn = 'P'; // Adjust based on actual column count
        
            // Apply styling to the header row
            $headerStyle = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];
            $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray($headerStyle);
        
            // Set the title in the first row
            $sheet->setCellValue('A1', 'Weight QC Report');
        
            // Merge cells for title
            $sheet->mergeCells("A1:{$lastColumn}1");
        
            // Apply styling to the title
            $titleStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 16, // Larger Font
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A1')->applyFromArray($titleStyle);
        
            // Save and Output
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="new_daily_weight_qc_report_.xlsx"');
            header('Cache-Control: max-age=0');
        
            $date_file_name = Carbon::today()->format('Y_m_d');
            $file_name_without_path = "reports/new_daily_weight_qc_report_" . $date_file_name . ".xlsx";
            $file_name = public_path($file_name_without_path);
            $writer->save($file_name);
            return url($file_name_without_path);
        }
    }

    static public function overall_sales_report($day) {
        $connection = 'reports';
        $sales = DB::connection($connection)->table('shipments')->join('users as u', 'u.id', '=', 'shipments.user_id')
            ->leftJoin('shipment_services_charges as ss_charge', 'ss_charge.shipment_id', '=', 'shipments.id')
            ->leftJoin('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->leftJoin('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->leftJoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('user_shipping_infos AS rsi', 'shipments.return_address_id', '=', 'rsi.id')
            ->leftjoin('cities AS rc', 'rsi.city_id', '=', 'rc.id')
            ->leftjoin('zones as z', 'z.id', '=', 'oc.zone_id')
            ->leftJoin('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftJoin('cities as h', 'dc.hub_id', '=', 'h.id')
            ->leftJoin('cities as och', 'oc.hub_id', 'och.id') // och for origin city hub
            ->leftjoin('zones as ocz', 'ocz.id', 'oc.zone_id') // ocz for origin city zone
            ->leftjoin('zone_class_cities as zcc', function ($join) use ($connection) {
                $join->on('z.id', '=', 'zcc.zone_id')
                    ->on('dc.id', '=', 'zcc.city_id')
                    ->on('zone_classification_id', '=', DB::connection($connection)->raw('IF (shipments.shipping_mode_id IN (1, 4), 1, 2)'));
            })
            ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=', 'sps.id')
            ->leftJoin('shipments_journey as sj', function ($join) use ($connection) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.shipper_status_id', 2)
                    ->where(
                        'sj.id',
                        '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)')
                    );
            })
            ->leftJoin('shipments_journey as sjr', function ($join) use ($connection) {
                $join->on('sjr.shipment_id', '=', 'shipments.id')
                    ->whereIn('shipments.shipper_status_id', [20, 21, 22, 23, 24, 25, 44, 47, 48, 57, 60])
                    ->where(
                        'sjr.id',
                        '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id IN (12, 20) and shipments_journey.verification = 1 and shipments_journey.status_reason_id is not null)')
                    );
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'sjr.status_reason_id')
            ->leftJoin('pending_payment_shipments as pps', function ($join) use ($connection) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.type', '!=',2 );
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) use ($connection) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.type', '!=',2 );
            })
            ->leftJoin('shipments_journey as dr', function ($join) use ($connection) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->whereIn('dr.shipper_status_id', [14, 25, 30, 36, 37])
                    ->where(
                        'dr.id',
                        '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,25,30,36,37) and shipments_journey.verification = 1)')
                    );
            })
            ->leftjoin('shipment_items as si', function ($join) use ($connection) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where(
                        'si.id',
                        '=',
                        DB::connection($connection)->raw('(select max(id) from shipment_items where shipment_items.shipment_id = shipments.id and shipment_items.type = 0)')
                    );
            })
            ->leftjoin('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'shipments.user_id')
                    ->leftjoin('admins as adsp', 'adsp.id', '=', 'spt.admin_id')
                    ->where('spt.status', '=', 0);
            })
            ->leftjoin('products as p', 'p.id', '=', 'si.product_type_id')
            ->leftJoin('pending_invoice_shipments as pis', function ($join) use ($connection) {
                $join->on('pis.shipment_id', '=', 'shipments.id')
                    ->where('pis.type', '!=',2 );
            })
            ->leftJoin('invoice_shipments as is', function ($join) use ($connection) {
                $join->on('is.shipment_id', '=', 'shipments.id')
                    ->where('is.type', '!=',2 );
            })
            ->leftjoin('invoices', 'is.invoice_id', '=', 'invoices.id')
            ->leftjoin('international_shipments as ibs', 'ibs.shipment_id', '=', 'shipments.id')
            ->leftjoin('riders as r', 'r.id', '=', 'sj.rider_id')
            ->leftJoin('business_categories as bc', 'bc.id', '=', 'shipments.business_category_id')
            ->leftJoin('sub_category_segments as scs', 'u.sub_segment_id', '=', 'scs.id')
            ->leftJoin('shipments_journey as sjfa', function ($join) use ($connection) {
                $join->on('sjfa.shipment_id', '=', 'shipments.id')
                    ->where(
                        'sjfa.id',
                        '=',
                        DB::connection($connection)->raw('(select min(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id  = 5)')
                    );
            })
            ->leftJoin('shipments_payment_journey as spjproceed_date', function ($join) use ($connection) {
                $join->on('spjproceed_date.shipment_id', '=', 'shipments.id')
                    ->where(
                        'spjproceed_date.id',
                        '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_payment_journey where shipments_payment_journey.shipment_id = shipments.id and shipments_payment_journey.status_id  = 1)')
                    );
            })
            ->leftJoin('shipments_payment_journey as spjpaid_date', function ($join) use ($connection) {
                $join->on('spjpaid_date.shipment_id', '=', 'shipments.id')
                    ->where(
                        'spjpaid_date.id',
                        '=',
                        DB::connection($connection)->raw('(select max(id) from shipments_payment_journey where shipments_payment_journey.shipment_id = shipments.id and shipments_payment_journey.status_id  = 3)')
                    );
            })
            ->leftJoin('sales_commissions as sc', 'sc.shipper_id', '=', 'u.id')
            ->leftJoin('sales_commission_users as scu','scu.sales_commission_id', '=', 'sc.id')
            ->leftJoin('admins as scun', 'scun.id', '=', 'scu.user_id')
            ->leftJoin('sale_tier_tags as st', 'st.user_id', '=', 'u.id')
            ->leftJoin('admins as rf', 'rf.id', '=', 'st.ref')
            ->leftjoin('shipment_additional_charges as faf_charges', 'faf_charges.shipment_id', '=', 'shipments.id')
            ->select( 'invoices.invoice_number', 'r.name as ridername', 'ssr.name as reason', 'sjr.remarks as remark', 'p.product_name as category', 'si.description as description', 'shipments.id as shipment_id', 'shipments.fintech_charges as fintech_charges', 'shipments.order_id as order_id', 'shipments.tracking_number as tracking_number_link', 'u.id as account_no', 'u.name as shipper', 'usi.pickup_address as shipper_address', 'ss.name as current_status', 'bt.booking_type as service_type', 'sj.created_at as arrival_date', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'shipments.amount as s_collection_amount', 'sps.name as payment_status', 'shipments.actual_weight', 'shipments.weight_charges', 'shipments.cash_handling_charges', 'shipments.insurance_charges', 'shipments.return_charges', 'shipments.replacement_charges', 'shipments.fuel_surcharge', 'shipments.try_and_buy_charges', 'shipments.packaging_material_charges', DB::raw('SUM(pps.charges) as p_total_charges'), DB::raw('SUM(pps.amount) as p_collection_amount'), DB::raw('SUM(pps.payable) as p_net_payable'), DB::raw('SUM(pps.gst) as p_gst'), DB::raw('SUM(dps.amount) as d_collection_amount'), DB::raw('SUM(dps.charges) as d_total_charges'), DB::raw('SUM(dps.payable) as d_net_payable'), DB::raw('SUM(dps.gst) as d_gst'), 'sm.mode as shipping_mode', 'sm.id as shipping_mode_id', 'shipments.chargeable_weight', 'dr.created_at as delivered_or_returned', 'z.name as zone', 'zcc.class', 'oc.id as origin_city_id', 'dc.id as destination_city_id', 'dps.done_payment_id as payment_id', 'shipments.booking_type_id', 'usi.poc', 'adsp.id', 'adsp.name as sales_person', 'shipments.shipper_status_id as shipment_status', 'shipments.nsa_osa_charges', 'u.account_type_id as account_type_id', DB::raw('SUM(is.gst) as is_gst'), DB::raw('SUM(pis.gst) as pis_gst'), 'shipments.packaging_charges', 'dr.received_or_refused_by', 'shipments.special_instructions', 'shipments.intercept_charges', 'bc.name as business_shipment_type', 'ibs.international_tracking_number', 'usi.vendor', 'dr.shipper_status_id as dr_status_id', 'shipments.shipment_type', 'rc.name as return_city', 'dr.cnic as dr_cnic', 'dr.relation as dr_relation', 'shipments.consignee_address as consignee_address', 'scs.name as sub_segment', 'sjfa.created_at as first_attempt_date', 'spjpaid_date.created_at as paid_date', 'spjproceed_date.created_at as processed_date', 'si.quantity as item_quantity', 'shipments.pieces as pieces', 'scun.id as scun_id', 'rf.id as ref_id', 'rf.name as ref', 'och.name as origin_hub', 'shipments.tracking_number as tracking_number_excel', DB::raw('SUM(pps.sms_charges) as pps_sms_charges'), DB::raw('SUM(dps.sms_charges) as dps_sms_charges'), DB::raw('SUM(pis.sms_charges) as pis_sms_charges'), DB::raw('SUM(is.sms_charges) as is_sms_charges'), DB::raw('SUM(faf_charges.faf_charges) as faf_charges'), DB::raw('SUM(ss_charge.reverse_pickup_charges) as reverse_pickup_charges'), 'ibs.cost')
            ->whereNotIn('shipments.shipper_status_id', [1, 17])
            ->whereNotIn('u.id', [8761, 9358])
            ->whereBetween('sj.created_at', [
                Carbon::parse($day)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($day)->endOfDay()->format('Y-m-d H:i:s')
            ])
            ->orderBy('sps.name', 'desc')
            ->groupBy('shipments.id')
        ->get();

        $overall_sales_report = [];

        // Report Title
        $overall_sales_report[] = ['Overall Sales Report'];
        
        // Header Row
        $headers = [
            "S. No.",
            "Tracking No.",
            "Account No.",
            "Shipper",
            "Sub Segment",
            "Vendor",
            "Order ID",
            "First Attempt Date",
            "Item Quantity",
            "Pieces",
            "Status",
            "Reason",
            "Remark",
            "Total Attempt",
            "Payment Status",
            "Invoice No.",
            "Payment ID",
            "Processed Date",
            "Paid Date",
            "Service Type",
            "Arrival Date",
            "Rider",
            "Origin",
            "Origin Hub",
            "Destination",
            "Destination Hub",
            "Return City",
            "Origin Zone",
            "Class",
            "Attempts",
            "Shipping Mode",
            "Category",
            "Description",
            "International Tracking No.",
            "Collection Amount",
            "Actual Weight",
            "Chargeable Weight",
            "Weight Charges",
            "Cash Handling Charges",
            "Insurance Charges",
            "Packaging Charges",
            "FAF Charges",
            "Fuel Surcharge",
            "Return Charges",
            "Replacement Charges",
            "Try & Buy Charges",
            "Reverse Pickup Charges",
            "NSA/OSA Charges",
            "GST",
            "SMS Charges",
            "Intercept Charges",
            "Fintech Charges",
            "Total Charges",
            "Estimated Charges",
            "Packing Charges",
            "Net Payable",
            "Delivered/Returned Date",
            "Received/Refused By",
            "Sales Person",
            "Referral Name",
            "Special Instructions",
            "Cost"
        ];

        // Add headers to the report array
        $overall_sales_report[] = $headers; 

        if (count($sales) >= 0) {
            $serial = 0;
            foreach ($sales as $sale) {
                $serial++;

                $attempt = DB::connection('reports')
                ->table('shipments_journey')
                ->where('shipment_id', $sale->shipment_id)
                ->where('shipper_status_id', 5)
                ->count();

                $delivery_notes = '-';
                $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $sale->shipment_id);
                if ($delivery_note_shipment->exists()) {
                    $delivery_note_ids = $delivery_note_shipment->pluck('delivery_note_id');
                    if ($delivery_note_ids->isNotEmpty()) {
                        $delivery_notes = DeliveryNote::whereIn('id', $delivery_note_ids)
                            ->whereHas('rider', function ($query) {
                                $query->where('operation_rider_id', 1);
                            })
                            ->count();
                    }
                }
                
                $estimated = '-';
                $total = 0;
                $total += ($sale->weight_charges ?? 0);
                $total += ($sale->cash_handling_charges ?? 0);
                $total += ($sale->insurance_charges ?? 0); // Appears twice in original, kept as is
                $total += ($sale->insurance_charges ?? 0);
                $total += ($sale->return_charges ?? 0);
                $total += ($sale->replacement_charges ?? 0);
                $total += ($sale->fuel_surcharge ?? 0);
                $total += ($sale->try_and_buy_charges ?? 0);
                $total += ($sale->packaging_material_charges ?? 0);
                $total += ($sale->intercept_charges ?? 0);

                if ($total > 0) {
                    $estimated = number_format((float) $total, 2);
                }

                $overall_sales_report[] = [
                    $serial,
                    $sale->tracking_number_link,  
                    $sale->account_no,  
                    $sale->shipper,  
                    $sale->sub_segment,  
                    $sale->vendor,  
                    $sale->order_id,  
                    $sale->first_attempt_date,  
                    $sale->item_quantity,  
                    $sale->pieces,  
                    $sale->current_status,  
                    $sale->reason,  
                    $sale->remark,  
                    $delivery_notes,
                    $sale->payment_status,  
                    $sale->invoice_number,  
                    $sale->payment_id,  
                    $sale->processed_date,  
                    $sale->paid_date,  
                    $sale->service_type,  
                    $sale->arrival_date,  
                    $sale->ridername,  
                    $sale->origin,  
                    $sale->origin_hub,  
                    $sale->destination,  
                    $sale->hub,  
                    $sale->return_city,  
                    $sale->zone,  
                    $sale->class,  
                    $attempt,  
                    $sale->shipping_mode,  
                    $sale->category,  
                    $sale->description,  
                    $sale->international_tracking_number,  
                    $sale->p_collection_amount,  
                    $sale->actual_weight,  
                    $sale->chargeable_weight,  
                    $sale->weight_charges,  
                    $sale->cash_handling_charges,  
                    $sale->insurance_charges,  
                    $sale->packaging_material_charges,  
                    $sale->faf_charges,  
                    $sale->fuel_surcharge,  
                    $sale->return_charges,  
                    $sale->replacement_charges,  
                    $sale->try_and_buy_charges,  
                    $sale->reverse_pickup_charges,  
                    $sale->nsa_osa_charges,  
                    $sale->p_gst,  
                    $sale->pps_sms_charges,  
                    $sale->intercept_charges,  
                    $sale->fintech_charges,  
                    $sale->p_total_charges,  
                    $estimated,  
                    $sale->packaging_charges,  
                    $sale->p_net_payable,  
                    $sale->delivered_or_returned,  
                    $sale->received_or_refused_by,  
                    $sale->sales_person,  
                    $sale->ref,  
                    $sale->special_instructions,  
                    $sale->cost,
                ];
            }

            // Apply Excel formatting
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
        
            // Set default column width
            $sheet->getDefaultColumnDimension()->setWidth(20);

            // Set B column (tracking number) format to text
            $sheet->getStyle('B')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        
            // Populate data in Excel
            $sheet->fromArray($overall_sales_report, NULL, 'A1', true);
        
            // Define last column dynamically
            $lastColumn = 'BJ'; // Adjust based on actual column count
        
            // Apply styling to the header row
            $headerStyle = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];
            $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray($headerStyle);
        
            // Set the title in the first row
            $sheet->setCellValue('A1', 'Overall Sales Report');
        
            // Merge cells for title
            $sheet->mergeCells("A1:{$lastColumn}1");
        
            // Apply styling to the title
            $titleStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 16, // Larger Font
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A1')->applyFromArray($titleStyle);
        
            // Save and Output
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="new_daily_overall_sales_report_.xlsx"');
            header('Cache-Control: max-age=0');
        
            $date_file_name = Carbon::today()->format('Y_m_d');
            $file_name_without_path = "reports/new_daily_overall_sales_report_" . $date_file_name . ".xlsx";
            $file_name = public_path($file_name_without_path);
            $writer->save($file_name);
            return url($file_name_without_path);
        }
    }

    static public function daily_pending_deliveries () 
    {
        $status = array(2, 4, 6, 7, 8, 9, 10, 13, 15, 49, 55, 59);
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->leftJoin('sub_category_segments as scs', 'scs.id' , 'u.sub_segment_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->leftJoin('zones as z', 'z.id', '=', 'dc.zone_id')
            ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftJoin('consignee_address_areas as caa', 'caa.shipment_id', '=', 'shipments.id')
            ->leftJoin('city_areas as ca', 'ca.id', '=', 'caa.city_area_id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->join('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where(
                        'shipments_journey.id',
                        '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)')
                    );
            })
            ->leftjoin('shipments_journey as ras', function ($join) {
                $join->on('ras.shipment_id', '=', 'shipments.id')
                    ->where(
                        'ras.id',
                        '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 13)')
                    );
            })
            ->leftjoin('admins as agent', 'agent.id', '=', 'ras.admin_id')
            ->join('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where(
                        'sj.id',
                        '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)')
                    );
            })
            ->leftJoin('shipments_journey as sjd', function ($join) {
                $join->on('sjd.shipment_id', '=', 'shipments.id')
                    ->where(
                        'sjd.id',
                        '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 4)')
                    );
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
            ->leftjoin('intercept_re_book_request_histories as irrh', 'irrh.shipment_id', '=', 'shipments.id')
            ->leftjoin('crm_requests as crm', function ($join) {
                $join->on('crm.shipment_id', '=', 'shipments.id')
                    ->whereIn('crm.status_id', [DB::raw(2), DB::raw(3), DB::raw(5)])
                    ->where('crm.case_nature_id', DB::raw(1));
            })
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where(
                        'si.id',
                        '=',
                        DB::raw('(select max(id) from shipment_items where shipment_items.shipment_id = shipments.id)')
                    );
            })
            ->leftjoin('products as prod', 'prod.id', '=', 'si.product_type_id')
            ->leftjoin('star_shippers as sts', 'sts.user_id', '=', 'u.id')

            ->leftjoin('delivery_note_shipments as dns', function ($join) {
                $join->on('dns.shipment_id', '=', 'shipments.id')
                    ->where(
                        'dns.delivery_note_id',
                        '=',
                        DB::raw('(select max(delivery_note_id) from delivery_note_shipments WHERE shipment_id = shipments.id)')
                    );
            })
            ->leftJoin('delivery_notes as dn', 'dn.id', '=', 'dns.delivery_note_id')
            ->leftjoin('riders as r', 'r.id', '=', 'dn.rider_id')
        
            ->leftJoin('shipments_journey as sjl', function ($join) {
                $join->on('sjl.shipment_id', '=', 'shipments.id')
                    ->where(
                        'sjl.id',
                        '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id IN (2,3,4,5,11,23,53))')
                    );
            })

            ->leftJoin('shipments_journey as journey', function ($join) {
                $join->on('journey.shipment_id', '=', 'shipments.id')
                    ->where(
                        'journey.id',
                        '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)')
                    );
            })
            ->leftJoin('shipment_scanning_journeys as ssj_last_location', function ($join) {
                $join->on('ssj_last_location.shipment_id', '=', 'journey.shipment_id')
                    ->whereRaw('ssj_last_location.id = (
                                    select max(id) 
                                    from shipment_scanning_journeys 
                                    where shipment_scanning_journeys.shipment_id = journey.shipment_id
                                    and shipment_scanning_journeys.screen_location_id not in (9, 18)
                    )');
            })        
            ->when(DB::raw('ssj_last_location.user_type = 1'), function ($join) {
                $join->leftJoin('admins as adm', function ($join) {
                    $join->on('adm.id', '=', 'ssj_last_location.admin_id')
                        ->where('adm.role_id', '<>', 1);
                });
            })
            ->leftJoin('shipment_scanning_journey_area_logs as ssjal_last_location', function($join){
                $join->on('ssjal_last_location.shipment_scanning_journey_id', '=', 'ssj_last_location.id')
                    ->where('ssjal_last_location.hub_id', '=', DB::raw('journey.city_id'))
                    ->where('ssjal_last_location.shipment_id', '=', DB::raw('journey.shipment_id'));
            })                 
            ->leftJoin('city_areas as ca_scanning_last_location_name', 'ssjal_last_location.area_id', '=', 'ca_scanning_last_location_name.id')
            ->leftJoin('shipment_scanning_screen_locations as last_screen_location', 'last_screen_location.id', '=', 'ssj_last_location.screen_location_id')
            ->select(
                'agent.name as agent',
                'shipments.id as shId',
                'shipments.tracking_number as tracking_number_link',
                'shipments.tracking_number',
                'u.name as shipper',
                'oc.name as origin',
                'dc.name as destination',
                'dc.id as destination_city_id',
                'h.name as hub',
                'shipments.consignee_name',
                'shipments.consignee_phone_number_1',
                'shipments.consignee_phone_number_2',
                'shipments.consignee_address',
                'shipments.amount',
                'sm.mode as shipping_mode',
                'bt.booking_type as service_type',
                'ss.name as status',
                'ssr.name as reason',
                'shipments_journey.remarks as remarks',
                'shipments_journey.created_at as status_date',
                'shipments_journey.created_at as current_status_date',
                'sjd.created_at as destination_arrival',
                'sj.created_at as arrival',
                'shipments.booking_type_id',
                'usi.poc',
                'crm.id as complaint',
                'shipments.actual_weight as weight',
                'si.description as shipment_description',
                'prod.product_name as product_type',
                'sts.status as star_status',
                'ca.name as area',
                'r.name as last_rider',
                'z.name as d_zone',
                'r.trax_id as rider_trax_id',
                'shipments.shipper_status_id as shipper_status_id',
                'sjl.shipment_id as journey_latest_id',
                'sjl.updated_at as journey_latest_updated_at',
                'sjl.shipper_status_id as latest_shipper_status_id',
                'scs.name as sub_segment_name',
                'ssjal_last_location.updated_at as last_location_updated_at',
                'ca_scanning_last_location_name.name as ca_scanning_last_location_name',
                'ssj_last_location.user_type as scanned_by_user_type',
                'ssj_last_location.admin_id as scanned_by_id',
                'last_screen_location.name as last_location_screen_location_name',
                'ssj_last_location.entry_method as entry_method'
            )

            ->whereRaw('IF (shipments.shipper_status_id IN (2, 49), (oc.hub_id = dc.hub_id), TRUE)')
            ->whereRaw('IF (shipments.shipper_status_id = 55, (irrh.old_consignee_city_id = irrh.new_consignee_city_id), TRUE)')
            ->whereRaw('IF (shipments.shipper_status_id = 55, (irrh.old_consignee_city_id = irrh.new_consignee_city_id), TRUE)')
            ->whereIn('shipments.shipper_status_id', $status)
            ->whereNotNull('shipments.tracking_number')
            ->orderBy('shipments_journey.remarks', 'desc')
            ->groupBy('shipments.id')
        ->get();

        $pending_deliveries_report_array = [];
       // Report Title
        $report_title = 'Pending Deliveries Report';
        $pending_deliveries_report_array[] = [$report_title];

        // Header Row
        $headers = [
            'S.No',
            'Tracking .No',
            'Shipper',
            'Sub-Segment',
            'Origin',
            'Destination',
            'Hub',
            'Area',
            'Consignee Name',
            'Consignee Phone',
            'Reattempt By',
            'Address',
            'Sub Station',
            'Weight',
            'Collection Amount',
            'Product Type',
            'Product Description',
            'Shipping Mode',
            'Service Type',
            'Status',
            'Last Location Screen Name',
            'Entry Method',
            'Sub Hub',
            'Last Location Updated At',
            'Reason',
            'Remarks',
            'Origin Arrival Date',
            'Destination Zone',
            'Destination Arrival Date',
            'Last Rider',
            'Last Rider Trax ID',
            'Status Date'
        ];

        // Add headers to the report array
        $pending_deliveries_report_array[] = $headers;

        if(count($shipments) > 0) {
            $serial = 0;
            foreach ($shipments as $shipment) {
                $serial++;
                $sub_station = '-';
                $check = DeliveryLocationMappingKeyword::pluck('keyword')->map(fn($k) => strtolower($k))->toArray();
                $words = preg_split("/[ ,]+/", strtolower($shipment->consignee_address));
                
                $msg_string = collect($words)->first(fn($word) => in_array($word, $check));
                
                $delivery_area = '-';
                
                if ($msg_string) {
                    $found = DeliveryLocationMappingKeyword::join('delivery_location_mappings as dlm', 'delivery_location_mapping_keywords.mapping_id', '=', 'dlm.id')
                        ->where('delivery_location_mapping_keywords.keyword', $msg_string)
                        ->where('dlm.city_id', $shipment->destination_city_id)
                        ->select('dlm.area_name')
                        ->first();
                
                    $delivery_area = $found->area_name ?? '-';
                }
                $sub_station = $delivery_area; 
                
                $sub_hub = '-';
                if ($shipment->scanned_by_user_type == 5) {
                    $rider = Rider::find($shipment->scanned_by_id);
                    $sub_hub = $rider->area->name ?? '-';
                } elseif (isset($shipment->ca_scanning_last_location_name)) {
                    $sub_hub = $shipment->ca_scanning_last_location_name;
                }
                $pending_deliveries_report_array[] = [
                    $serial,
                    $shipment->tracking_number,
                    $shipment->shipper,
                    $shipment->sub_segment_name,
                    $shipment->origin,
                    $shipment->destination,
                    $shipment->hub,
                    $shipment->area,
                    $shipment->consignee_name,
                    $shipment->consignee_phone_number_1,
                    $shipment->agent,
                    $shipment->consignee_address,
                    $sub_station,
                    $shipment->weight,
                    $shipment->amount,
                    $shipment->product_type,
                    $shipment->shipment_description,
                    $shipment->shipping_mode,
                    $shipment->service_type,
                    $shipment->status,
                    $shipment->last_location_screen_location_name,
                    $shipment->entry_method == 1 ? 'Scanned' : 'Manual',
                    $sub_hub,
                    $shipment->last_location_updated_at,
                    $shipment->reason,
                    $shipment->remarks,
                    $shipment->arrival,
                    $shipment->d_zone,
                    $shipment->destination_arrival ?? '-',
                    $shipment->last_rider,
                    $shipment->rider_trax_id,
                    $shipment->status_date,
                ];
            }

            // Create Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
        
            // Set default column width
            $sheet->getDefaultColumnDimension()->setWidth(20);

            // Set default column width
            $sheet->getDefaultColumnDimension()->setWidth(20);
        
            // Populate data in Excel
            $sheet->fromArray($pending_deliveries_report_array, NULL, 'A1', true);
        
            // Total Columns
            $lastColumn = 'AF'; // Adjust based on actual column count
        
            // Apply styling to the header row
            $headerStyle = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];
            $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray($headerStyle);
        
            // Set the title in the first row
            $sheet->setCellValue('A1', $report_title);
        
            // Merge cells for title
            $sheet->mergeCells("A1:{$lastColumn}1");
        
            // Apply styling: Bold, Centered, and Larger Font
            $titleStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 16, // Larger Font
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A1')->applyFromArray($titleStyle);
        
            // Populate data in Excel
            $sheet->fromArray($pending_deliveries_report_array, NULL, 'A1', true);
        
            // Total Columns
            $lastColumn = 'V'; // Adjust based on actual column count
        
            // Apply styling to the header row
            $headerStyle = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];
            $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray($headerStyle);
        
            // Set the title in the first row
            $sheet->setCellValue('A1', 'Pending Deliveries Report');
        
            // Merge cells for title
            $sheet->mergeCells("A1:{$lastColumn}1");
        
            // Apply styling: Bold, Centered, and Larger Font
            $titleStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 16, // Larger Font
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A1')->applyFromArray($titleStyle);

            // Save and Output
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename=daily_pending_deliveries_report_.xlsx"');
            header('Cache-Control: max-age=0');
            $date_file_name = Carbon::today()->format('Y_m_d');
            $file_name_without_path = "reports/daily_new_pending_deliveries_report_" . $date_file_name . ".xlsx";
            $file_name = public_path() . "/reports/daily_new_pending_deliveries_report_" . $date_file_name . ".xlsx";
            $writer->save($file_name);
            return url('/') . '/' . $file_name_without_path;

        }
    }
}
