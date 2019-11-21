<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\SalePersonTag;
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
        $only_date = Carbon::parse($date)->toDateString();
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
            $contribution[$index] = $con_rev / $total_revenue;
        }

        $sale_person_array['header'] = ['S. No.','Admin', 'Shipments', 'Revenue', 'Avg Revenue', 'Contribution'];
        $serial = 1;

        foreach ($sale_person_shipments as $sale_person_shipment) {
            $sale_person_array[] = ['serial' => $serial, 'Admin' => $sale_person_shipment->admin, 'Shipments' => $sale_person_shipment->shipment_count, 'Revenue' => $revenue[$sale_person_shipment->admin_id], 'Avg Revenue' => $avg_revenue[$sale_person_shipment->admin_id], 'Contribution' => $contribution[$sale_person_shipment->admin_id]];
            $sale_person_entry = new SalePersonNumbers();
            $sale_person_entry->admin_id = $sale_person_shipment->admin_id;
            $sale_person_entry->shipments = $sale_person_shipment->shipment_count;
            $sale_person_entry->revenue = $revenue[$sale_person_shipment->admin_id];
            $sale_person_entry->avg_revenue = $avg_revenue[$sale_person_shipment->admin_id];
            $sale_person_entry->contribution = $contribution[$sale_person_shipment->admin_id];
            $sale_person_entry->total_revenue = $total_revenue;
            $sale_person_entry->save();
            $serial++;
        }
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
}
