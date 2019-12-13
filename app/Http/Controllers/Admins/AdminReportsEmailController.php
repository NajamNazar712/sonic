<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\City;
use App\http\Models\CRM\CrmTatHolidays;
use App\Http\Models\Excel_reports\HubWiseSplit;
use App\Http\Models\Excel_reports\MonthAverage;
use App\Http\Models\Excel_reports\SalePersonNumbers;
use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminReportsEmailController extends Controller
{
    static public function sale_person_numbers($date){
        $date_from = Carbon::createFromFormat("Y-m-d H:i:s",$date)->format('Y-m-d 08:00A');
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s",$next_day)->format('Y-m-d 07:59A');
        $revenue = array();
        $avg_revenue = array();
        $contribution = array();
        $sale_person_array = array();
        $sale_person_shipments = SalePersonTag::leftjoin('admins as a', 'a.id', '=', 'sale_person_tags.admin_id')->leftjoin('shipments as s', 's.user_id', '=', 'sale_person_tags.user_id')->leftjoin('shipments_journey as sj', 'sj.shipment_id', '=', 's.id')->select('a.id as admin_id', 'a.name as admin', DB::raw('count(s.id) as shipment_count'), DB::raw('sum(s.weight_charges) as weight_charges'), DB::raw('sum(s.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(s.insurance_charges) as insurance_charges'), DB::raw('sum(s.return_charges) as return_charges'), DB::raw('sum(s.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(s.replacement_charges) as replacement_charges'), DB::raw('sum(s.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(s.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(s.intercept_charges) as intercept_charges'), DB::raw('sum(s.nsa_osa_charges) as nsa_osa_charges'))->groupBy('sale_person_tags.admin_id')->where('sale_person_tags.status', 0)->where('s.packaging_material_request', 0)->where('sj.shipper_status_id', 2)->whereBetween('sj.created_at', [$date_from, $date_to])->get();
        $walkin = GlobalSettings::where('type', 'Walk-In')->first();
        $walk_in_shipments = Shipment::leftjoin('shipments_journey as sj', 'sj.shipment_id', '=', 'shipments.id')
            ->select(DB::raw('count(shipments.id) as shipment_count'), DB::raw('sum(shipments.weight_charges) as weight_charges'), DB::raw('sum(shipments.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(shipments.insurance_charges) as insurance_charges'), DB::raw('sum(shipments.return_charges) as return_charges'), DB::raw('sum(shipments.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(shipments.replacement_charges) as replacement_charges'), DB::raw('sum(shipments.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(shipments.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(shipments.intercept_charges) as intercept_charges'), DB::raw('sum(shipments.nsa_osa_charges) as nsa_osa_charges'))->where('shipments.user_id', $walkin->setting_value)->where('sj.shipper_status_id', 2)->whereBetween('sj.created_at', [$date_from, $date_to])->get();

        foreach ($sale_person_shipments as $sale_person_shipment){
            $revenue[$sale_person_shipment->admin_id] = $sale_person_shipment->weight_charges + $sale_person_shipment->cash_handling_charges + $sale_person_shipment->insurance_charges + $sale_person_shipment->return_charges + $sale_person_shipment->fuel_surcharge + $sale_person_shipment->replacement_charges + $sale_person_shipment->try_and_buy_charges + $sale_person_shipment->packaging_material_charges + $sale_person_shipment->intercept_charges + $sale_person_shipment->nsa_osa_charges;

            $avg_revenue[$sale_person_shipment->admin_id] = $revenue[$sale_person_shipment->admin_id] / $sale_person_shipment->shipment_count;
        }
        foreach ($walk_in_shipments as $walk_in_shipment){
            $revenue[0] = $walk_in_shipment->weight_charges + $walk_in_shipment->cash_handling_charges + $walk_in_shipment->insurance_charges + $walk_in_shipment->return_charges + $walk_in_shipment->fuel_surcharge + $walk_in_shipment->replacement_charges + $walk_in_shipment->try_and_buy_charges + $walk_in_shipment->packaging_material_charges + $walk_in_shipment->intercept_charges + $walk_in_shipment->nsa_osa_charges;
            if($walk_in_shipment->shipment_count != 0){
                $avg_revenue[0] = $revenue[0] / $walk_in_shipment->shipment_count;
            }
            else{
                $avg_revenue[0] = 0;
            }
        }
        $total_revenue = 0;
        foreach ($revenue as $rev){
            $total_revenue = $total_revenue + $rev;
        }
        foreach ($revenue as $index => $con_rev){
            if($total_revenue != 0) {
                $contribution[$index] = $con_rev / $total_revenue;
            }
            else{
                $contribution[$index] = 0;
            }
        }

        $sale_person_array['header'] = ['S. No.','Admin', 'Shipments', 'Revenue', 'Avg Revenue/Parcel', 'Contribution'];
        $serial = 1;

        $total_shipments_count = 0;
        $total_avg_revenue_count = 0;
        $total_contribution_count = 0;
        SalePersonNumbers::truncate();
        foreach ($sale_person_shipments as $sale_person_shipment) {
            $sale_person_array[] = ['serial' => $serial, 'Admin' => $sale_person_shipment->admin, 'Shipments' => $sale_person_shipment->shipment_count, 'Revenue' => $revenue[$sale_person_shipment->admin_id], 'Avg Revenue/Parcel' => round($avg_revenue[$sale_person_shipment->admin_id], 2), 'Contribution' => ($contribution[$sale_person_shipment->admin_id]) * 100];
            $sale_person_entry = new SalePersonNumbers();
            $sale_person_entry->admin_id = $sale_person_shipment->admin_id;
            $sale_person_entry->shipments = $sale_person_shipment->shipment_count;
            $sale_person_entry->revenue = $revenue[$sale_person_shipment->admin_id];
            $sale_person_entry->avg_revenue = $avg_revenue[$sale_person_shipment->admin_id];
            $sale_person_entry->contribution = $contribution[$sale_person_shipment->admin_id] * 100;
            $sale_person_entry->save();
            $serial++;


            $total_shipments_count = $total_shipments_count + $sale_person_shipment->shipment_count;
            $total_contribution_count = $total_contribution_count + $contribution[$sale_person_shipment->admin_id];
        }
        foreach ($walk_in_shipments as $walk_in_shipment) {
            $sale_person_array[] = ['serial' => $serial, 'Admin' => 'Walk-In', 'Shipments' => $walk_in_shipment->shipment_count, 'Revenue' => $revenue[0], 'Avg Revenue/Parcel' => round($avg_revenue[0], 2), 'Contribution' => $contribution[0] * 100];
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
        if($total_shipments_count != 0){
            $total_avg_revenue_count = $total_revenue / $total_shipments_count;
        }
        else{
            $total_avg_revenue_count = 0;
        }
        $sale_person_array[] = ['serial' => '', 'Admin' => '', 'Shipments' => '', 'Revenue' => '', 'Avg Revenue/Parcel' => '', 'Contribution' => ''];
        $sale_person_array[] = ['serial' => 'Total', 'Admin' => '', 'Shipments' => $total_shipments_count, 'Revenue' => $total_revenue, 'Avg Revenue/Parcel' => round($total_avg_revenue_count, 2), 'Contribution' => $total_contribution_count * 100];
        $cell_st =[
            'font' =>['bold' => true],
            'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);

        $sheet->fromArray($sale_person_array,NULL,'A2',true);
        $sheet->getStyle("A2:F2")->applyFromArray($cell_st);
        $sheet->setTitle('Sale Person Numbers');
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');

        $file_name_without_path = "reports/sale_person_numbers_report_".$date_file_name.".xlsx";
        $file_name = public_path() . "/reports/sale_person_numbers_report_".$date_file_name.".xlsx";
        $writer->save($file_name);

        return url('/').'/'.$file_name_without_path;
    }

    static public function hub_wise_split($date){
        $date_from = Carbon::createFromFormat("Y-m-d H:i:s",$date)->format('Y-m-d 08:00A');
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s",$next_day)->format('Y-m-d 07:59A');
        $total_shipments = 0;
        $ratio = array();
        $avg_actual_weight = array();
        $hub_wise_split_array = array();

        $hub_wise_splits = City::leftjoin('cities as h', 'h.id', '=', 'cities.hub_id')
                ->leftjoin('shipments as s', function($join){
                $join->on('s.consignee_city_id', '=', 'cities.id')
                    ->where('s.packaging_material_request', 0);
                    })
                ->leftjoin('shipments_journey as sj', 'sj.shipment_id', '=', 's.id')
                ->select('h.id as hub_id', 'h.name as hub', DB::raw('count(s.id) as shipment_count'), DB::raw('sum(s.actual_weight) as actual_weight'))
                ->where('sj.shipper_status_id', 2)
                ->whereBetween('sj.created_at', [$date_from, $date_to])
                ->groupBy('h.id')
                ->get();
        foreach ($hub_wise_splits as $hub_wise_split){
            $total_shipments = $total_shipments + $hub_wise_split->shipment_count;
        }
        foreach ($hub_wise_splits as $hub_wise_split){
            if($total_shipments != 0){
                $ratio[$hub_wise_split->hub_id] = $hub_wise_split->shipment_count / $total_shipments;
            }
            else{
                $ratio[$hub_wise_split->hub_id] = 0;
            }
            dd($ratio);
            if($hub_wise_split->shipment_count != 0){
                $avg_actual_weight[$hub_wise_split->hub_id] = $hub_wise_split->actual_weight / $hub_wise_split->shipment_count;
            }
            else{
                $avg_actual_weight[$hub_wise_split->hub_id] = 0;
            }
        }

        $hub_wise_split_array['header'] = ['S. No.','Hub', 'Count of Parcels', 'Ratio', 'Actual Weight', 'Avg Actual Weight/Shipment'];
        $serial = 1;

        $total_shipments_count = 0;
        $total_avg_ratio_count = 0;
        $total_actual_weight_count = 0;
        $total_avg_actual_weight_count = 0;
        HubWiseSplit::truncate();
        foreach ($hub_wise_splits as $hub_wise_split) {
            if($hub_wise_split->actual_weight){
                $actual_weight = $hub_wise_split->actual_weight;
            }
            else{
                $actual_weight = 0;
            }
            $hub_wise_split_array[] = ['serial' => $serial, 'Hub' => $hub_wise_split->hub, 'Count of Parcels' => $hub_wise_split->shipment_count, 'Ratio' => $ratio[$hub_wise_split->hub_id], 'Actual Weight' => round($actual_weight, 2), 'Avg Actual Weight/Shipment' => round($avg_actual_weight[$hub_wise_split->hub_id], 2)];
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
        $total_avg_actual_weight_count = $total_actual_weight_count / $total_shipments_count;
        $hub_wise_split_array[] = ['serial' => '', 'Hub' => '', 'Count of Parcels' => '', 'Ratio' => '', 'Actual Weight' => '', 'Avg Actual Weight/Shipment' => ''];
        $hub_wise_split_array[] = ['serial' => 'Total', 'Hub' => '', 'Count of Parcels' => $total_shipments_count, 'Ratio' => $total_avg_ratio_count, 'Actual Weight' => round($total_actual_weight_count, 2), 'Avg Actual Weight/Shipment' => round($total_avg_actual_weight_count, 2)];
        $cell_st =[
            'font' =>['bold' => true],
            'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);

        $sheet->fromArray($hub_wise_split_array,NULL,'A2',true);
        $sheet->getStyle("A2:F2")->applyFromArray($cell_st);
        $sheet->setTitle('Sale Person Numbers');
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');

        $file_name_without_path = "reports/hub_wise_split_report_".$date_file_name.".xlsx";
        $file_name = public_path() . "/reports/hub_wise_split_report_".$date_file_name.".xlsx";
        $writer->save($file_name);

        return url('/').'/'.$file_name_without_path;
    }
    static public function month_average($date){
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s",$date);
        $first_day = Carbon::parse($date)->firstOfMonth();
        $last_day = Carbon::parse($date)->lastOfMonth();
        $date_from = Carbon::createFromFormat("Y-m-d H:i:s",$first_day);
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
        $holidays = CrmTatHolidays::whereBetween('holiday', [$holiday_date_from, $holiday_date_to])->count();
        while ($date_from->lte($date_to)) {
            if($date_from->isWeekday() || $date_from->isSaturday()) {
                $dates[] = $date_from->copy()->format('Y-m-d');
            }

            $date_from->addDay();
        }
        while ($first_day->lte($last_day)) {
            if($first_day->isWeekday() || $first_day->isSaturday()) {
                $total_dates[] = $date_from->copy()->format('Y-m-d');
            }

            $first_day->addDay();
        }
        $weekdays_count = count($dates);
        $weekdays_count = ($weekdays_count - ($week_holidays + 1));
        $total_month_weekdays_count = count($total_dates) - ($holidays + 1);

        $months_average = City::leftjoin('shipments_journey as sj', 'sj.city_id', '=', 'cities.id')
                ->leftjoin('shipments as s', 's.id', '=', 'sj.shipment_id')
                ->select('cities.id as origin_id', 'cities.name as origin', DB::raw('count(s.id) as shipment_count'), DB::raw('sum(s.weight_charges) as weight_charges'), DB::raw('sum(s.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(s.insurance_charges) as insurance_charges'), DB::raw('sum(s.return_charges) as return_charges'), DB::raw('sum(s.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(s.replacement_charges) as replacement_charges'), DB::raw('sum(s.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(s.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(s.intercept_charges) as intercept_charges'), DB::raw('sum(s.nsa_osa_charges) as nsa_osa_charges'))
                ->groupBy('cities.id')
                ->where('cities.pickup', 1)
                ->where('s.packaging_material_request', 0)
                ->where('sj.shipper_status_id', 2)
                ->whereBetween('sj.created_at', [$new_date_from, $new_date_to])
                ->get();

        foreach ($months_average as $month_average){
            $revenue[$month_average->origin_id] = $month_average->weight_charges + $month_average->cash_handling_charges + $month_average->insurance_charges + $month_average->return_charges + $month_average->fuel_surcharge + $month_average->replacement_charges + $month_average->try_and_buy_charges + $month_average->packaging_material_charges + $month_average->intercept_charges + $month_average->nsa_osa_charges;
            if($month_average->shipment_count != 0){
                $avg_revenue[$month_average->origin_id] = $revenue[$month_average->origin_id] / $month_average->shipment_count;
            }
            else{
                $avg_revenue[$month_average->origin_id] = 0;
            }
            if($weekdays_count != 0){
                $avg_shipment[$month_average->origin_id] = $month_average->shipment_count / $weekdays_count;
            }
            else{
                $avg_shipment[$month_average->origin_id] = 0;
            }
            $month_speed[$month_average->origin_id] = $avg_shipment[$month_average->origin_id] * $total_month_weekdays_count;
            $total_shipments = $total_shipments + $month_average->shipment_count;
        }

        $month_average_array['header'] = ['S. No.','Origin', 'Total Parcel', 'Revenue', 'Avg Revenue/Parcel', 'Avg Shipments/Day', 'Month Speed'];
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
        $total_avg_revenue_count = $total_revenue_count / $total_shipments_count;

        $month_average_array[] = ['serial' => '', 'Origin' => '', 'Total Parcel' => '', 'Revenue' => '', 'Avg Revenue/Parcel' => '', 'Avg Shipments/Day' => '', 'Month Speed' => ''];
        $month_average_array[] = ['serial' => 'Total', 'Origin' => '', 'Total Parcel' => $total_shipments_count, 'Revenue' => round($total_revenue_count, 2), 'Avg Revenue/Parcel' => round($total_avg_revenue_count, 2), 'Avg Shipments/Day' => round($total_avg_shipment_count, 2), 'Month Speed' => round($total_month_speed_count, 2)];
        $cell_st =[
            'font' =>['bold' => true],
            'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);

        $sheet->fromArray($month_average_array,NULL,'A2',true);
        $sheet->getStyle("A2:G2")->applyFromArray($cell_st);
        $sheet->setTitle('Sale Person Numbers');
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');

        $file_name_without_path = "reports/month_average_report_".$date_file_name.".xlsx";
        $file_name = public_path() . "/reports/month_average_report_".$date_file_name.".xlsx";
        $writer->save($file_name);

        return url('/').'/'.$file_name_without_path;
    }
}
