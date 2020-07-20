<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Admin\SalePersonTarget;
use App\Http\Models\CargoConsignment;
use App\Http\Models\CargoConsignmentShipment;
use App\Http\Models\City;
use App\http\Models\CRM\CrmTatHolidays;
use App\Http\Models\DailyFakeStatus;
use App\Http\Models\Excel_reports\HubWiseSplit;
use App\Http\Models\Excel_reports\MonthAverage;
use App\Http\Models\Excel_reports\NotAttemptedShipmentAging;
use App\Http\Models\Excel_reports\SalePersonNumbers;
use App\Http\Models\Holiday;
use App\Http\Models\OvernightOverlandReportData;
use App\Http\Models\OvernightOverlandReportOriginHubs;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPExcel_Style_Fill;
use PHPExcel_Cell;

class AdminReportsEmailController extends Controller
{
    static public function sale_person_numbers($date)
    {
        $date_from = Carbon::createFromFormat("Y-m-d H:i:s", $date)->format('Y-m-d 08:00A');
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s", $next_day)->format('Y-m-d 07:59A');
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
                    $target_revenue_achieved = ($revenue[$sale_person_shipment->admin_id] / $all_shipments_target_revenue) * 100;
                }
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
        $sheet->getStyle($ts)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFE699');
        $sheet->getStyle($tas)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C7E0B4');
        $sheet->getStyle($tr)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('FFE699');
        $sheet->getStyle($tar)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
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
        $date_from = Carbon::createFromFormat("Y-m-d H:i:s", $date)->format('Y-m-d 08:00A');
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s", $next_day)->format('Y-m-d 07:59A');
        $total_shipments = 0;
        $ratio = array();
        $avg_actual_weight = array();
        $hub_wise_split_array = array();

        $hub_wise_splits = City::leftjoin('cities as h', 'h.id', '=', 'cities.hub_id')
            ->leftjoin('shipments as s', function ($join) {
                $join->on('s.consignee_city_id', '=', 'cities.id')
                    ->where('s.packaging_material_request', 0);
            })
            ->leftjoin('shipments_journey as sj', 'sj.shipment_id', '=', 's.id')
            ->select('h.id as hub_id', 'h.name as hub', DB::raw('count(s.id) as shipment_count'), DB::raw('sum(s.actual_weight) as actual_weight'))
            ->where('sj.shipper_status_id', 2)
            ->whereBetween('sj.created_at', [$date_from, $date_to])
            ->groupBy('h.id')
            ->get();
        foreach ($hub_wise_splits as $hub_wise_split) {
            $total_shipments = $total_shipments + $hub_wise_split->shipment_count;
        }
        foreach ($hub_wise_splits as $hub_wise_split) {
            if ($total_shipments != 0) {
                $ratio[$hub_wise_split->hub_id] = $hub_wise_split->shipment_count / $total_shipments;
            } else {
                $ratio[$hub_wise_split->hub_id] = 0;
            }
            if ($hub_wise_split->shipment_count != 0) {
                $avg_actual_weight[$hub_wise_split->hub_id] = $hub_wise_split->actual_weight / $hub_wise_split->shipment_count;
            } else {
                $avg_actual_weight[$hub_wise_split->hub_id] = 0;
            }
        }

        $hub_wise_split_array['header'] = ['S. No.', 'Hub', 'Count of Parcels', 'Ratio', 'Actual Weight', 'Avg Actual Weight/Shipment'];
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
            $hub_wise_split_array[] = ['serial' => $serial, 'Hub' => $hub_wise_split->hub, 'Count of Parcels' => $hub_wise_split->shipment_count, 'Ratio' => round($ratio[$hub_wise_split->hub_id] * 100, 2), 'Actual Weight' => round($actual_weight, 2), 'Avg Actual Weight/Shipment' => round($avg_actual_weight[$hub_wise_split->hub_id], 2)];
            $hub_wise_split_entry = new HubWiseSplit();
            $hub_wise_split_entry->hub_id = $hub_wise_split->hub_id;
            $hub_wise_split_entry->shipments = $hub_wise_split->shipment_count;
            $hub_wise_split_entry->ratio = $ratio[$hub_wise_split->hub_id] * 100;
            $hub_wise_split_entry->actual_weight = $actual_weight;
            $hub_wise_split_entry->avg_actual_weight = $avg_actual_weight[$hub_wise_split->hub_id];
            $hub_wise_split_entry->save();
            $serial++;


            $total_shipments_count = $total_shipments_count + $hub_wise_split->shipment_count;
            $total_avg_ratio_count = $total_avg_ratio_count + $ratio[$hub_wise_split->hub_id];
            $total_actual_weight_count = $total_actual_weight_count + $hub_wise_split->actual_weight;
        }
        if($total_shipments_count <= 0){
            $total_avg_actual_weight_count = 0;
        }
        else{
            $total_avg_actual_weight_count = $total_actual_weight_count / $total_shipments_count;
        }
        $hub_wise_split_array[] = ['serial' => '', 'Hub' => '', 'Count of Parcels' => '', 'Ratio' => '', 'Actual Weight' => '', 'Avg Actual Weight/Shipment' => ''];
        $hub_wise_split_array[] = ['serial' => 'Total', 'Hub' => '', 'Count of Parcels' => $total_shipments_count, 'Ratio' => $total_avg_ratio_count * 100, 'Actual Weight' => round($total_actual_weight_count, 2), 'Avg Actual Weight/Shipment' => round($total_avg_actual_weight_count, 2)];
        $cell_st = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);

        $sheet->fromArray($hub_wise_split_array, NULL, 'A2', true);
        $sheet->getStyle("A2:F2")->applyFromArray($cell_st);
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
        $new_date_to = $new_date_to->format('Y-m-d 07:59A');
        $total_shipments = 0;
        $revenue = array();
        $avg_revenue = array();
        $avg_shipment = array();
        $month_speed = array();
        $month_average_array = array();
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
            $month_speed[$month_average->origin_id] = $avg_shipment[$month_average->origin_id] * $total_month_weekdays_count;
            $total_shipments = $total_shipments + $month_average->shipment_count;
        }

        $month_average_array['header'] = ['S. No.', 'Origin', 'Total Parcel', 'Revenue', 'Avg Revenue/Parcel', 'Avg Shipments/Day', 'Month Speed'];
        $serial = 1;

        $total_shipments_count = 0;
        $total_revenue_count = 0;
        $total_avg_revenue_count = 0;
        $total_avg_shipment_count = 0;
        $total_month_speed_count = 0;
        MonthAverage::truncate();
        foreach ($months_average as $month_average) {
            $month_average_array[] = ['serial' => $serial, 'Origin' => $month_average->origin, 'Total Parcel' => $month_average->shipment_count, 'Revenue' => round($revenue[$month_average->origin_id], 2), 'Avg Revenue/Parcel' => round($avg_revenue[$month_average->origin_id], 2), 'Avg Shipments/Day' => round($avg_shipment[$month_average->origin_id], 2), 'Month Speed' => round($month_speed[$month_average->origin_id], 2)];
            $month_average_entry = new MonthAverage();
            $month_average_entry->origin_id = $month_average->origin_id;
            $month_average_entry->shipments = $month_average->shipment_count;
            $month_average_entry->revenue = round($revenue[$month_average->origin_id], 2);
            $month_average_entry->avg_revenue = round($avg_revenue[$month_average->origin_id], 2);
            $month_average_entry->avg_shipments = round($avg_shipment[$month_average->origin_id], 2);
            $month_average_entry->month_speed = round($month_speed[$month_average->origin_id], 2);
            $month_average_entry->save();
            $serial++;


            $total_shipments_count = $total_shipments_count + $month_average->shipment_count;
            $total_revenue_count = $total_revenue_count + $revenue[$month_average->origin_id];
            $total_avg_shipment_count = $total_avg_shipment_count + $avg_shipment[$month_average->origin_id];
            $total_month_speed_count = $total_month_speed_count + $month_speed[$month_average->origin_id];
        }
        if($total_shipments_count != 0){
            $total_avg_revenue_count = $total_revenue_count / $total_shipments_count;
        }
        else{
            $total_avg_revenue_count = 0;
        }

        $month_average_array[] = ['serial' => '', 'Origin' => '', 'Total Parcel' => '', 'Revenue' => '', 'Avg Revenue/Parcel' => '', 'Avg Shipments/Day' => '', 'Month Speed' => ''];
        $month_average_array[] = ['serial' => 'Total', 'Origin' => '', 'Total Parcel' => $total_shipments_count, 'Revenue' => round($total_revenue_count, 2), 'Avg Revenue/Parcel' => round($total_avg_revenue_count, 2), 'Avg Shipments/Day' => round($total_avg_shipment_count, 2), 'Month Speed' => round($total_month_speed_count, 2)];
        $cell_st = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);

        $sheet->fromArray($month_average_array, NULL, 'A2', true);
        $sheet->getStyle("A2:G2")->applyFromArray($cell_st);
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
                    ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
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
                        ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
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
                ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
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
                $shipping_mode = 'Overnight';
            }
            else{
                $shipping_mode = 'Overland';
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
            $from_id = ShipmentsJourney::select(DB::raw('MIN(id) as id'))->where('verification', 1)->where('created_at', '>=', $formatted_date_from)->first()->id;
            $to_id = ShipmentsJourney::select(DB::raw('MAX(id) as id'))->where('verification', 1)->where('created_at', '<=', $to_cut)->first()->id;
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
                $cities_shipments = City::join('shipments as s', function($join) {
                    $join->where(function($query) {
                        $query->where('cities.id', '=','s.consignee_city_id')
                            ->orWhere(function ($sub_query) {
                                $sub_query->on('cities.id', '=', DB::raw('(select usii.city_id from user_shipping_infos as usii where usii.id = s.pickup_address_id)'));
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
                            ->where('sj.id', '=', DB::raw('(select max(shipments_journey.id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.id >= "' . $from_id . '" and shipments_journey.id <= "' . $to_id . '")'));
                    })
                    ->join('shipments_journey as sja', function($join){
                        $join->on('s.id', '=', 'sja.shipment_id')
                            ->where('sja.id', '=', DB::raw('(select max(shipments_journeya.id) from shipments_journey as shipments_journeya where shipments_journeya.shipment_id = s.id and shipments_journeya.shipper_status_id = 2)'));
                    })
                    ->select('s.id as shipment_id', 'sja.created_at as arrival_date')
                    ->where(function ($query) use ($cut_off_time, $from){
                        $query->where(function($sub_query){
                            $sub_query->where('cities.id', '=', DB::raw('s.consignee_city_id'))
                                ->where('sj.shipper_status_id', '=', 7);
                        })
                            ->orWhere(function ($sub_query) use ($cut_off_time, $from) {
                                $sub_query->where(function ($sub_sub_query) use ($cut_off_time, $from) {
                                    $sub_sub_query->where(function ($sub_sub_sub_query){
                                        $sub_sub_sub_query->where(function ($sub_sub_sub_sub_query){
                                            $sub_sub_sub_sub_query->where(function ($sub_sub_sub_sub_sub_query) {
                                                $sub_sub_sub_sub_sub_query->where('usi.city_id', '=', DB::raw('s.consignee_city_id'))
                                                    ->orWhereNull('zcc.class')
                                                    ->orWhereIn('zcc.class', [0, 1]);
                                            })
                                                ->where(function ($sub_sub_sub_sub_sub_sub_sub_query) {
                                                    $sub_sub_sub_sub_sub_sub_sub_query->where('cities.id', '=', DB::raw('usi.city_id'))
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

                if($cities_shipments->exists()){
                    $cities_shipments = $cities_shipments->groupBy('s.id')->get();
                }
                foreach ($cities_shipments as $cities_shipment){
                    $arrival_date = Carbon::parse($cities_shipment->arrival_date);
                    $holidays = Holiday::whereBetween('holiday', [$arrival_date, $formatted_date])->count();
                    $count_without_holidays = $arrival_date->diffInWeekdays($formatted_date);
                    $count_with_holidays = $count_without_holidays - $holidays;
                    if($count_with_holidays == 0){
                        $hub_shipments[$hub->name]['zero']++;
                        $shipments[$hub->name]['id'][$cities_shipment->shipment_id] = 0;
                    }
                    elseif ($count_with_holidays == 1){
                        $hub_shipments[$hub->name]['one']++;
                        $shipments[$hub->name]['id'][$cities_shipment->shipment_id] = 1;
                    }
                    elseif ($count_with_holidays == 2){
                        $hub_shipments[$hub->name]['two']++;
                        $shipments[$hub->name]['id'][$cities_shipment->shipment_id] = 2;
                    }
                    elseif ($count_with_holidays == 3){
                        $hub_shipments[$hub->name]['three']++;
                        $shipments[$hub->name]['id'][$cities_shipment->shipment_id] = 3;
                    }
                    elseif ($count_with_holidays == 4){
                        $hub_shipments[$hub->name]['four']++;
                        $shipments[$hub->name]['id'][$cities_shipment->shipment_id] = 4;
                    }
                    elseif ($count_with_holidays == 5){
                        $hub_shipments[$hub->name]['five']++;
                        $shipments[$hub->name]['id'][$cities_shipment->shipment_id] = 5;
                    }
                    elseif ($count_with_holidays >= 6){
                        $hub_shipments[$hub->name]['six_plus']++;
                        $shipments[$hub->name]['id'][$cities_shipment->shipment_id] = 6;
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
}
