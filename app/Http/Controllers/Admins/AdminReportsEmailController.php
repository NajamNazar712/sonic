<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\City;
use App\Http\Models\Excel_reports\HubWiseSplit;
use App\Http\Models\Excel_reports\MonthAverage;
use App\Http\Models\Excel_reports\SalePersonNumbers;
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
        $sale_person_shipments = SalePersonTag::leftjoin('admins as a', 'a.id', '=', 'sale_person_tags.admin_id')->leftjoin('shipments as s', 's.user_id', '=', 'sale_person_tags.user_id')->select('a.id as admin_id', 'a.name as admin', DB::raw('count(s.id) as shipment_count'), DB::raw('sum(s.weight_charges) as weight_charges'), DB::raw('sum(s.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(s.insurance_charges) as insurance_charges'), DB::raw('sum(s.return_charges) as return_charges'), DB::raw('sum(s.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(s.replacement_charges) as replacement_charges'), DB::raw('sum(s.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(s.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(s.intercept_charges) as intercept_charges'), DB::raw('sum(s.nsa_osa_charges) as nsa_osa_charges'))->groupBy('sale_person_tags.admin_id')->where('sale_person_tags.status', 0)->whereBetween('s.created_at', [$date_from, $date_to])->get();

        foreach ($sale_person_shipments as $sale_person_shipment){
            $revenue[$sale_person_shipment->admin_id] = $sale_person_shipment->weight_charges + $sale_person_shipment->cash_handling_charges + $sale_person_shipment->insurance_charges + $sale_person_shipment->return_charges + $sale_person_shipment->fuel_surcharge + $sale_person_shipment->replacement_charges + $sale_person_shipment->try_and_buy_charges + $sale_person_shipment->packaging_material_charges + $sale_person_shipment->intercept_charges + $sale_person_shipment->nsa_osa_charges;

            $avg_revenue[$sale_person_shipment->admin_id] = $revenue[$sale_person_shipment->admin_id] / $sale_person_shipment->shipment_count;
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

        $sale_person_array['header'] = ['S. No.','Admin', 'Shipments', 'Revenue', 'Avg Revenue', 'Contribution'];
        $serial = 1;

        $total_shipments_count = 0;
        $total_avg_revenue_count = 0;
        $total_contribution_count = 0;
        SalePersonNumbers::truncate();
        foreach ($sale_person_shipments as $sale_person_shipment) {
            $sale_person_array[] = ['serial' => $serial, 'Admin' => $sale_person_shipment->admin, 'Shipments' => $sale_person_shipment->shipment_count, 'Revenue' => $revenue[$sale_person_shipment->admin_id], 'Avg Revenue' => round($avg_revenue[$sale_person_shipment->admin_id], 2), 'Contribution' => round($contribution[$sale_person_shipment->admin_id], 2)];
            $sale_person_entry = new SalePersonNumbers();
            $sale_person_entry->admin_id = $sale_person_shipment->admin_id;
            $sale_person_entry->shipments = $sale_person_shipment->shipment_count;
            $sale_person_entry->revenue = $revenue[$sale_person_shipment->admin_id];
            $sale_person_entry->avg_revenue = $avg_revenue[$sale_person_shipment->admin_id];
            $sale_person_entry->contribution = $contribution[$sale_person_shipment->admin_id];
            $sale_person_entry->save();
            $serial++;


            $total_shipments_count = $total_shipments_count + $sale_person_shipment->shipment_count;
            $total_avg_revenue_count = $total_avg_revenue_count + $avg_revenue[$sale_person_shipment->admin_id];
            $total_contribution_count = $total_contribution_count + $contribution[$sale_person_shipment->admin_id];
        }
        $sale_person_array[] = ['serial' => '', 'Admin' => '', 'Shipments' => '', 'Revenue' => '', 'Avg Revenue' => '', 'Contribution' => ''];
        $sale_person_array[] = ['serial' => 'Total', 'Admin' => '', 'Shipments' => $total_shipments_count, 'Revenue' => $total_revenue, 'Avg Revenue' => round($total_avg_revenue_count, 2), 'Contribution' => round($total_contribution_count, 2)];
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
            ->leftjoin('shipments as s', function($join) use($date_from, $date_to){
                $join->on('s.consignee_city_id', '=', 'cities.id')
                    ->where('s.shipper_status_id', 2)
                    ->where('s.packaging_material_request', 0)
                    ->whereBetween('s.created_at', [$date_from, $date_to]);
                    })
            ->select('h.id as hub_id', 'h.name as hub', DB::raw('count(s.id) as shipment_count'), DB::raw('sum(s.actual_weight) as actual_weight'))
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
            if($hub_wise_split->shipment_count != 0){
                $avg_actual_weight[$hub_wise_split->hub_id] = $hub_wise_split->actual_weight / $hub_wise_split->shipment_count;
            }
            else{
                $avg_actual_weight[$hub_wise_split->hub_id] = 0;
            }
        }

        $hub_wise_split_array['header'] = ['S. No.','Hub', 'Count of Parcels', 'Ratio', 'Actual Weight', 'Avg Actual Weight'];
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
            $hub_wise_split_array[] = ['serial' => $serial, 'Hub' => $hub_wise_split->hub, 'Count of Parcels' => $hub_wise_split->shipment_count, 'Ratio' => round($ratio[$hub_wise_split->hub_id], 2), 'Actual Weight' => round($actual_weight, 2), 'Avg Actual Weight' => round($avg_actual_weight[$hub_wise_split->hub_id], 2)];
            $hub_wise_split_entry = new HubWiseSplit();
            $hub_wise_split_entry->hub_id = $hub_wise_split->hub_id;
            $hub_wise_split_entry->shipments = $hub_wise_split->shipment_count;
            $hub_wise_split_entry->ratio = round($ratio[$hub_wise_split->hub_id], 2);
            $hub_wise_split_entry->actual_weight = round($actual_weight, 2);
            $hub_wise_split_entry->avg_actual_weight = round($avg_actual_weight[$hub_wise_split->hub_id], 2);
            $hub_wise_split_entry->save();
            $serial++;


            $total_shipments_count = $total_shipments_count + $hub_wise_split->shipment_count;
            $total_avg_ratio_count = $total_avg_ratio_count + $ratio[$hub_wise_split->hub_id];
            $total_actual_weight_count = $total_actual_weight_count + $hub_wise_split->actual_weight;
            $total_avg_actual_weight_count = $total_avg_actual_weight_count + $avg_actual_weight[$hub_wise_split->hub_id];
        }
        $hub_wise_split_array[] = ['serial' => '', 'Hub' => '', 'Count of Parcels' => '', 'Ratio' => '', 'Actual Weight' => '', 'Avg Actual Weight' => ''];
        $hub_wise_split_array[] = ['serial' => 'Total', 'Hub' => '', 'Count of Parcels' => $total_shipments_count, 'Ratio' => round($total_avg_ratio_count, 2), 'Actual Weight' => round($total_actual_weight_count, 2), 'Avg Actual Weight' => round($total_avg_actual_weight_count, 2)];
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
        $new_date_from = $new_date_from->format('Y-m-d 00:00:00');
        $new_date_to = $date_to;
        $new_date_to = $new_date_to->format('Y-m-d 23:59:59');
        $total_shipments = 0;
        $revenue = array();
        $avg_revenue = array();
        $avg_shipment = array();
        $month_speed = array();
        $month_average_array = array();
        $dates = [];
        $total_dates = [];

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
        $total_month_weekdays_count = count($total_dates);

        $months_average = City::leftjoin('shipments_journey as sj', function($join) use($new_date_from, $new_date_to) {
                $join->on('sj.city_id', '=', 'cities.id')
                    ->where('sj.shipper_status_id', 2)
                    ->whereBetween('sj.created_at', [$new_date_from, $new_date_to]);
                    })
                ->leftjoin('shipments as s', 's.id', '=', 'sj.shipment_id')
                ->select('cities.id as origin_id', 'cities.name as origin', DB::raw('count(s.id) as shipment_count'), DB::raw('sum(s.weight_charges) as weight_charges'), DB::raw('sum(s.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(s.insurance_charges) as insurance_charges'), DB::raw('sum(s.return_charges) as return_charges'), DB::raw('sum(s.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(s.replacement_charges) as replacement_charges'), DB::raw('sum(s.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(s.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(s.intercept_charges) as intercept_charges'), DB::raw('sum(s.nsa_osa_charges) as nsa_osa_charges'))
                ->groupBy('cities.id')
                ->where('cities.pickup', 1)
                ->get();

        foreach ($months_average as $month_average){
            $revenue[$month_average->origin_id] = $month_average->weight_charges + $month_average->cash_handling_charges + $month_average->insurance_charges + $month_average->return_charges + $month_average->fuel_surcharge + $month_average->replacement_charges + $month_average->try_and_buy_charges + $month_average->packaging_material_charges + $month_average->intercept_charges + $month_average->nsa_osa_charges;
            if($month_average->shipment_count != 0){
                $avg_revenue[$month_average->origin_id] = $revenue[$month_average->origin_id] / $month_average->shipment_count;
            }
            else{
                $avg_revenue[$month_average->origin_id] = 0;
            }
            $avg_shipment[$month_average->origin_id] = $month_average->shipment_count / $weekdays_count;
            $month_speed[$month_average->origin_id] = $avg_shipment[$month_average->origin_id] * $total_month_weekdays_count;
            $total_shipments = $total_shipments + $month_average->shipment_count;
        }

        $month_average_array['header'] = ['S. No.','Origin', 'Total Parcel', 'Revenue', 'Avg Revenue', 'Avg Shipments', 'Month Speed'];
        $serial = 1;

        $total_shipments_count = 0;
        $total_revenue_count = 0;
        $total_avg_revenue_count = 0;
        $total_avg_shipment_count = 0;
        $total_month_speed_count = 0;
        MonthAverage::truncate();
        foreach ($months_average as $month_average) {
            $month_average_array[] = ['serial' => $serial, 'Origin' => $month_average->origin, 'Total Parcel' => $month_average->shipment_count, 'Revenue' => round($revenue[$month_average->origin_id], 2), 'Avg Revenue' => round($avg_revenue[$month_average->origin_id], 2), 'Avg Shipments' => round($avg_shipment[$month_average->origin_id], 2), 'Month Speed' => round($month_speed[$month_average->origin_id], 2)];
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
            $total_avg_revenue_count = $total_avg_revenue_count + $avg_revenue[$month_average->origin_id];
            $total_avg_shipment_count = $total_avg_shipment_count + $avg_shipment[$month_average->origin_id];
            $total_month_speed_count = $total_month_speed_count + $month_speed[$month_average->origin_id];
        }

        $month_average_array[] = ['serial' => '', 'Origin' => '', 'Total Parcel' => '', 'Revenue' => '', 'Avg Revenue' => '', 'Avg Shipments' => '', 'Month Speed' => ''];
        $month_average_array[] = ['serial' => 'Total', 'Origin' => '', 'Total Parcel' => $total_shipments_count, 'Revenue' => round($total_revenue_count, 2), 'Avg Revenue' => round($total_avg_revenue_count, 2), 'Avg Shipments' => round($total_avg_shipment_count, 2), 'Month Speed' => round($total_month_speed_count, 2)];
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
