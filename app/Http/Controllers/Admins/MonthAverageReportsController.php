<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\AdminHub;
use App\Http\Models\City;
use App\Http\Models\CRM\CrmTatHolidays;
use App\Http\Models\Excel_reports\MonthAverage;
use App\Http\Models\Excel_reports\MonthAverageDestination;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MonthAverageReportsController extends Controller
{


    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    static public function month_average_overall($date)
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
        header('Content-Disposition: attachment;filename="month_average_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');

        $file_name_without_path = "reports/month_average_report_" . $date_file_name . ".xlsx";
        $file_name = public_path() . "/reports/month_average_report_" . $date_file_name . ".xlsx";
        $writer->save($file_name);

        return url('/') . '/' . $file_name_without_path;
    }
    static public function month_average_destination_overall($date)
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
            ->leftjoin('cities as ct', 'ct.id', '=', 's.consignee_city_id')
            ->select('ct.id as destination_id', 'ct.name as destination', DB::raw('count(s.id) as shipment_count'), DB::raw('sum(s.weight_charges) as weight_charges'), DB::raw('sum(s.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(s.insurance_charges) as insurance_charges'), DB::raw('sum(s.return_charges) as return_charges'), DB::raw('sum(s.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(s.replacement_charges) as replacement_charges'), DB::raw('sum(s.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(s.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(s.intercept_charges) as intercept_charges'), DB::raw('sum(s.nsa_osa_charges) as nsa_osa_charges'))
            ->groupBy('cities.id')
            ->where('s.packaging_material_request', 0)
            ->where('sj.shipper_status_id', 4)
            ->whereBetween('sj.created_at', [$new_date_from, $new_date_to])
            ->get();

        foreach ($months_average as $month_average) {
            $revenue[$month_average->destination_id] = $month_average->weight_charges + $month_average->cash_handling_charges + $month_average->insurance_charges + $month_average->return_charges + $month_average->fuel_surcharge + $month_average->replacement_charges + $month_average->try_and_buy_charges + $month_average->packaging_material_charges + $month_average->intercept_charges + $month_average->nsa_osa_charges;
            if ($month_average->shipment_count != 0) {
                $avg_revenue[$month_average->destination_id] = $revenue[$month_average->destination_id] / $month_average->shipment_count;
            } else {
                $avg_revenue[$month_average->destination_id] = 0;
            }
            if ($weekdays_count != 0) {
                $avg_shipment[$month_average->destination_id] = $month_average->shipment_count / $weekdays_count;
            } else {
                $avg_shipment[$month_average->destination_id] = 0;
            }
            if ($weekdays_count != 0) {
                $avg_revenue_per_day[$month_average->destination_id] = $revenue[$month_average->destination_id] / $weekdays_count;
            } else {
                $avg_revenue_per_day[$month_average->destination_id] = 0;
            }
            $month_speed[$month_average->destination_id] = $avg_shipment[$month_average->destination_id] * $total_month_weekdays_count;
            $total_shipments = $total_shipments + $month_average->shipment_count;
        }

        $month_average_array['header'] = ['S. No.', 'Destination', 'Total Parcel', 'Revenue', 'Avg Revenue/Parcel', 'Avg Shipments/Day', 'Avg Revenue/Day', 'Month Speed'];
        $serial = 1;

        $total_shipments_count = 0;
        $total_revenue_count = 0;
        $total_avg_revenue_count = 0;
        $total_avg_revenue_per_day_count = 0;
        $total_avg_shipment_count = 0;
        $total_month_speed_count = 0;
        MonthAverageDestination::truncate();
        foreach ($months_average as $month_average) {
            $month_average_array[] = ['serial' => $serial, 'Destination' => $month_average->destination, 'Total Parcel' => $month_average->shipment_count, 'Revenue' => round($revenue[$month_average->destination_id], 2), 'Avg Revenue/Parcel' => round($avg_revenue[$month_average->destination_id], 2), 'Avg Shipments/Day' => round($avg_shipment[$month_average->destination_id], 2), 'Avg Revenue/Day' => round($avg_revenue_per_day[$month_average->destination_id], 2), 'Month Speed' => round($month_speed[$month_average->destination_id], 2)];
            $month_average_entry = new MonthAverageDestination();
            $month_average_entry->destination_id = $month_average->destination_id;
            $month_average_entry->shipments = $month_average->shipment_count;
            $month_average_entry->revenue = round($revenue[$month_average->destination_id], 2);
            $month_average_entry->avg_revenue = round($avg_revenue[$month_average->destination_id], 2);
            $month_average_entry->avg_shipments = round($avg_shipment[$month_average->destination_id], 2);
            // $month_average_entry->avg_revenue_per_day = round($avg_revenue_per_day[$month_average->destination_id], 2);
            $month_average_entry->month_speed = round($month_speed[$month_average->destination_id], 2);
            $month_average_entry->save();
            $serial++;


            $total_shipments_count = $total_shipments_count + $month_average->shipment_count;
            $total_revenue_count = $total_revenue_count + $revenue[$month_average->destination_id];
            $total_avg_shipment_count = $total_avg_shipment_count + $avg_shipment[$month_average->destination_id];
            $total_avg_revenue_per_day_count = $total_avg_revenue_per_day_count + $avg_revenue_per_day[$month_average->destination_id];
            $total_month_speed_count = $total_month_speed_count + $month_speed[$month_average->destination_id];
        }
        if($total_shipments_count != 0){
            $total_avg_revenue_count = $total_revenue_count / $total_shipments_count;
        }
        else{
            $total_avg_revenue_count = 0;
        }

        $month_average_array[] = ['serial' => '', 'Destination' => '', 'Total Parcel' => '', 'Revenue' => '', 'Avg Revenue/Parcel' => '', 'Avg Shipments/Day' => '', 'Avg Revenue/Day' => '', 'Month Speed' => ''];
        $month_average_array[] = ['serial' => 'Total', 'Destination' => '', 'Total Parcel' => $total_shipments_count, 'Revenue' => round($total_revenue_count, 2), 'Avg Revenue/Parcel' => round($total_avg_revenue_count, 2), 'Avg Shipments/Day' => round($total_avg_shipment_count, 2), 'Avg Revenue/Day' => round($total_avg_revenue_per_day_count, 2), 'Month Speed' => round($total_month_speed_count, 2)];
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
        header('Content-Disposition: attachment;filename="month_average_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');

        $file_name_without_path = "reports/month_average_destination_report_" . $date_file_name . ".xlsx";
        $file_name = public_path() . "/reports/month_average_destination_report_" . $date_file_name . ".xlsx";
        $writer->save($file_name);

        return url('/') . '/' . $file_name_without_path;
    }

    static public function month_average_individual($date, $sale_person_id)
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

        $tagged_shippers = array();
        $tagged_shippers = DB::connection('reports')->table('sale_person_tags')->where('admin_id', $sale_person_id)->where('status', 0)->select('user_id')->pluck('user_id')->toArray();
        $assigned_admins = DB::connection('reports')->table('multiple_sale_leads')->leftjoin('multiple_sale_taggings as mst', 'mst.lead_id', '=', 'multiple_sale_leads.id')
            ->leftjoin('sale_person_tags as spt', 'spt.admin_id', '=', 'mst.admin_id')
            ->where('multiple_sale_leads.admin_id', $sale_person_id)
            ->where('spt.status', 0)
            ->whereNotNull('spt.user_id')->select('spt.user_id')->pluck('spt.user_id')->toArray();
        if($assigned_admins) {
            $tagged_shippers = array_merge($tagged_shippers, $assigned_admins);
        }


        $months_average = City::leftjoin('shipments_journey as sj', 'sj.city_id', '=', 'cities.id')
            ->leftjoin('shipments as s', 's.id', '=', 'sj.shipment_id')
            ->select('cities.id as origin_id', 'cities.name as origin', DB::raw('count(s.id) as shipment_count'), DB::raw('sum(s.weight_charges) as weight_charges'), DB::raw('sum(s.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(s.insurance_charges) as insurance_charges'), DB::raw('sum(s.return_charges) as return_charges'), DB::raw('sum(s.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(s.replacement_charges) as replacement_charges'), DB::raw('sum(s.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(s.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(s.intercept_charges) as intercept_charges'), DB::raw('sum(s.nsa_osa_charges) as nsa_osa_charges'))
            ->where('cities.pickup', 1)
            ->where('s.packaging_material_request', 0)
            ->where('sj.shipper_status_id', 2)
            ->whereIn('s.user_id', $tagged_shippers)
            ->whereBetween('sj.created_at', [$new_date_from, $new_date_to])
            ->groupBy('cities.id')
            ->get();
        if(count($months_average) > 0){
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

            foreach ($months_average as $month_average) {
                $month_average_array[] = ['serial' => $serial, 'Origin' => $month_average->origin, 'Total Parcel' => $month_average->shipment_count, 'Revenue' => round($revenue[$month_average->origin_id], 2), 'Avg Revenue/Parcel' => round($avg_revenue[$month_average->origin_id], 2), 'Avg Shipments/Day' => round($avg_shipment[$month_average->origin_id], 2), 'Avg Revenue/Day' => round($avg_revenue_per_day[$month_average->origin_id], 2), 'Month Speed' => round($month_speed[$month_average->origin_id], 2)];

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
            header('Content-Disposition: attachment;filename="month_average_report.xlsx"');
            header('Cache-Control: max-age=0');
            $date_file_name = Carbon::parse($date)->format('Y_m_d');
            $time_string = Carbon::now()->toTimeString();
            $time_string = Carbon::parse($time_string)->format('h_i_s');

            $file_name_without_path = "reports/month_average_report_" . $sale_person_id .'_' .$date_file_name . ".xlsx";
            $file_name = public_path() . "/reports/month_average_report_" . $sale_person_id .'_' . $date_file_name . ".xlsx";
            $writer->save($file_name);

            return url('/') . '/' . $file_name_without_path;
        }

    }

    static public function month_average_rm($date, $rm_id)
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

        $assigned_hubs = AdminHub::where('admin_id', $rm_id)->pluck('hub_id')->toArray();
        $hubs = DB::connection('reports')->table('cities')->whereIn('hub_id', $assigned_hubs)->where('pickup', 1)->pluck('id')->toArray();

        $months_average = City::leftjoin('shipments_journey as sj', 'sj.city_id', '=', 'cities.id')
            ->leftjoin('shipments as s', 's.id', '=', 'sj.shipment_id')
            ->select('cities.id as origin_id', 'cities.name as origin', DB::raw('count(s.id) as shipment_count'), DB::raw('sum(s.weight_charges) as weight_charges'), DB::raw('sum(s.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(s.insurance_charges) as insurance_charges'), DB::raw('sum(s.return_charges) as return_charges'), DB::raw('sum(s.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(s.replacement_charges) as replacement_charges'), DB::raw('sum(s.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(s.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(s.intercept_charges) as intercept_charges'), DB::raw('sum(s.nsa_osa_charges) as nsa_osa_charges'))
            ->whereIn('cities.id', $hubs)
            ->where('cities.pickup', 1)
            ->where('s.packaging_material_request', 0)
            ->where('sj.shipper_status_id', 2)
            ->whereBetween('sj.created_at', [$new_date_from, $new_date_to])
            ->groupBy('cities.id')
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

        foreach ($months_average as $month_average) {
            $month_average_array[] = ['serial' => $serial, 'Origin' => $month_average->origin, 'Total Parcel' => $month_average->shipment_count, 'Revenue' => round($revenue[$month_average->origin_id], 2), 'Avg Revenue/Parcel' => round($avg_revenue[$month_average->origin_id], 2), 'Avg Shipments/Day' => round($avg_shipment[$month_average->origin_id], 2), 'Avg Revenue/Day' => round($avg_revenue_per_day[$month_average->origin_id], 2), 'Month Speed' => round($month_speed[$month_average->origin_id], 2)];
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
        header('Content-Disposition: attachment;filename="month_average_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');

        $file_name_without_path = "reports/month_average_report_" . $rm_id . '_' . $date_file_name . ".xlsx";
        $file_name = public_path() . "/reports/month_average_report_" . $rm_id . '_' . $date_file_name . ".xlsx";
        $writer->save($file_name);

        return url('/') . '/' . $file_name_without_path;
    }

}
