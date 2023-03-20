<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Admin\SalePersonTarget;
use App\Http\Models\Excel_reports\KaeNumber;
use App\Http\Models\Excel_reports\SalePersonNumbers;
use App\Http\Models\SaleTierTag;
use App\Http\Models\Shipment;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use DB;

class SalesPersonNumbersReportController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    static public function sale_person_numbers_overall($date)
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
        /*if($total_target_shipments > 0){
            $total_target_shipments_achieved = ($total_shipments_count / $total_target_shipments) * 100;
        }

        if($total_target_revenue_avg > 0){
            $total_target_revenue_achieved = ($total_revenue_achieved / $total_target_revenue_avg) * 100;
        }*/

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

    static public function kae_numbers_overall($date)
    {
        $date_from = Carbon::createFromFormat("Y-m-d H:i:s", $date)->format('Y-m-d 06:00A');
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s", $next_day)->format('Y-m-d 05:59A');
        $revenue = array();
        $avg_revenue = array();
        $contribution = array();
        $sale_person_array = array();
        $sale_person_shipments = SaleTierTag::leftjoin('admins as a', 'a.id', '=', 'sale_tier_tags.kam')
            ->leftjoin('shipments as s', 's.user_id', '=', 'sale_tier_tags.user_id')
            ->leftjoin('shipments_journey as sj', 'sj.shipment_id', '=', 's.id')
            ->select('a.id as admin_id', 'a.name as admin', DB::raw('count(s.id) as shipment_count'), DB::raw('sum(s.weight_charges) as weight_charges'), DB::raw('sum(s.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(s.insurance_charges) as insurance_charges'), DB::raw('sum(s.return_charges) as return_charges'), DB::raw('sum(s.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(s.replacement_charges) as replacement_charges'), DB::raw('sum(s.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(s.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(s.intercept_charges) as intercept_charges'), DB::raw('sum(s.nsa_osa_charges) as nsa_osa_charges'))
            ->groupBy('sale_tier_tags.kam')
            ->where('s.packaging_material_request', 0)
            ->where('sj.shipper_status_id', 2)
            ->whereBetween('sj.created_at', [$date_from, $date_to])
            ->get();

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
        KaeNumber::truncate();
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
            }
            $sale_person_array[] = ['serial' => $serial, 'Admin' => $sale_person_shipment->admin, 'Achieved Shipments' => $sale_person_shipment->shipment_count, 'Target Shipments' => $target_shipments, 'Target Achieved %' => round($target_shipments_achieved, 2).'%', 'Achieved Revenue' => $revenue[$sale_person_shipment->admin_id], 'Target Revenue' => $all_shipments_target_revenue, 'Target Revenue Achieved %' => round($target_revenue_achieved, 2).'%', 'Avg Revenue/Parcel' => round($avg_revenue[$sale_person_shipment->admin_id], 2), 'Contribution' => ($contribution[$sale_person_shipment->admin_id]) * 100];
            $sale_person_entry = new KaeNumber();
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
        /*if($total_target_shipments > 0){
            $total_target_shipments_achieved = ($total_shipments_count / $total_target_shipments) * 100;
        }

        if($total_target_revenue_avg > 0){
            $total_target_revenue_achieved = ($total_revenue_achieved / $total_target_revenue_avg) * 100;
        }*/

        foreach ($walk_in_shipments as $walk_in_shipment) {
            $sale_person_array[] = ['serial' => $serial, 'Admin' => 'Walk-In', 'Achieved Shipments' => $walk_in_shipment->shipment_count, 'Target Shipments' => '', 'Target Achieved %' => '', 'Achieved Revenue' => $revenue[0], 'Target Revenue' => '', 'Target Revenue Achieved %' => '', 'Avg Revenue/Parcel' => round($avg_revenue[0], 2), 'Contribution' => $contribution[0] * 100];
            $sale_person_entry = new KaeNumber();
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
        $sheet->setTitle('KAE Numbers');
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');

        $file_name_without_path = "reports/kae_numbers_report_" . $date_file_name . ".xlsx";
        $file_name = public_path() . "/reports/kae_numbers_report_" . $date_file_name . ".xlsx";
        $writer->save($file_name);

        return url('/') . '/' . $file_name_without_path;
    }

    static public function sale_person_numbers_individual($date, $sale_person_id)
    {
        $revenue = 0;
        $average_revenue = 0;
        $sale_person_array = array();
        $serial = 1;
        $sale_person_name = Admin::find($sale_person_id)->name;
        $sale_person_shipments = SalePersonNumbers::where('admin_id', $sale_person_id);

        $sale_person_array['header'] = ['S. No.','Sale Person', 'Achieved Shipments', 'Target Shipments', 'Target Achieved %', 'Achieved Revenue','Target Revenue', 'Target Revenue Achieved %', 'Avg Revenue/Parcel', 'Contribution'];


        if($sale_person_shipments->exists()){
            $sale_person_shipments = $sale_person_shipments->first();
            $sale_person_shipment_count = $sale_person_shipments->shipments;
            $revenue = $sale_person_shipments->revenue;
            $average_revenue = $sale_person_shipments->avg_revenue;
            $contribution = $sale_person_shipments->contribution;
            $all_shipments_target_revenue = 0;
            $target_shipments = 0;
            $target_shipments_achieved = 0;
            $target_revenue = 0;
            $target_revenue_achieved = 0;
            $target = SalePersonTarget::where('sales_person_id', $sale_person_id);
            if ($target->exists()) {
                $target = $target->first();
                $target_shipments = $target->target_days;
                if ($target_shipments > 0) {
                    $target_shipments_achieved = ($sale_person_shipment_count / $target_shipments) * 100;
                }
                $target_revenue = $target->average_revenue;
                if ($target_revenue > 0) {
                    $all_shipments_target_revenue = $target_revenue * $target_shipments;

                    if($all_shipments_target_revenue > 0){
                        $target_revenue_achieved = ($revenue / $all_shipments_target_revenue) * 100;
                    }else{
                        $target_revenue_achieved = 0;
                    }
                }

                $sale_person_array[] = ['serial' => 1, 'Sale Person' => $sale_person_name, 'Achieved Shipments' => $sale_person_shipment_count, 'Target Shipments' => $target_shipments, 'Target Achieved %' => round($target_shipments_achieved, 2).'%', 'Achieved Revenue' => $revenue, 'Target Revenue' => $all_shipments_target_revenue, 'Target Revenue Achieved %' => round($target_revenue_achieved, 2).'%', 'Avg Revenue/Parcel' => round($average_revenue, 2), 'Contribution' => $contribution];
            }

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

        $file_name_without_path = "reports/sale_person_numbers_report_" .$sale_person_id .'_'. $date_file_name . ".xlsx";
        $file_name = public_path() . "/reports/sale_person_numbers_report_".$sale_person_id .'_'. $date_file_name . ".xlsx";
        $writer->save($file_name);

        return url('/') . '/' . $file_name_without_path;
    }

    static public function sale_person_numbers_rm($date, $rm_id)
    {
        $date_from = Carbon::createFromFormat("Y-m-d H:i:s", $date)->format('Y-m-d 06:00A');
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s", $next_day)->format('Y-m-d 05:59A');

        $assigned_hubs = AdminHub::where('admin_id', $rm_id)->pluck('hub_id')->toArray();

        $revenue = array();
        $avg_revenue = array();
        $contribution = array();
        $sale_person_array = array();
        $sale_person_shipments = SalePersonTag::leftjoin('admins as a', 'a.id', '=', 'sale_person_tags.admin_id')->leftjoin('shipments as s', 's.user_id', '=', 'sale_person_tags.user_id')->leftjoin('user_shipping_infos as usi', 'usi.id', '=', 's.pickup_address_id')->leftjoin('cities as c', 'c.id', '=', 'usi.city_id')->leftjoin('shipments_journey as sj', 'sj.shipment_id', '=', 's.id')->select('a.id as admin_id', 'a.name as admin', DB::raw('count(s.id) as shipment_count'), DB::raw('sum(s.weight_charges) as weight_charges'), DB::raw('sum(s.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(s.insurance_charges) as insurance_charges'), DB::raw('sum(s.return_charges) as return_charges'), DB::raw('sum(s.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(s.replacement_charges) as replacement_charges'), DB::raw('sum(s.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(s.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(s.intercept_charges) as intercept_charges'), DB::raw('sum(s.nsa_osa_charges) as nsa_osa_charges'))->groupBy('sale_person_tags.admin_id')->where('sale_person_tags.status', 0)->where('s.packaging_material_request', 0)->where('sj.shipper_status_id', 2)->whereIn('c.id', $assigned_hubs)->whereBetween('sj.created_at', [$date_from, $date_to])->get();
        $walkin = GlobalSettings::where('type', 'Walk-In')->first();
        $walk_in_shipments = Shipment::leftjoin('shipments_journey as sj', 'sj.shipment_id', '=', 'shipments.id')->leftjoin('user_shipping_infos as usi', 'usi.id', '=', 'shipments.pickup_address_id')->leftjoin('cities as c', 'c.id', '=', 'usi.city_id')
            ->select(DB::raw('count(shipments.id) as shipment_count'), DB::raw('sum(shipments.weight_charges) as weight_charges'), DB::raw('sum(shipments.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(shipments.insurance_charges) as insurance_charges'), DB::raw('sum(shipments.return_charges) as return_charges'), DB::raw('sum(shipments.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(shipments.replacement_charges) as replacement_charges'), DB::raw('sum(shipments.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(shipments.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(shipments.intercept_charges) as intercept_charges'), DB::raw('sum(shipments.nsa_osa_charges) as nsa_osa_charges'))->where('shipments.user_id', $walkin->setting_value)->where('sj.shipper_status_id', 2)->whereIn('c.id', $assigned_hubs)->whereBetween('sj.created_at', [$date_from, $date_to])->get();

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
            }
            $sale_person_array[] = ['serial' => $serial, 'Admin' => $sale_person_shipment->admin, 'Achieved Shipments' => $sale_person_shipment->shipment_count, 'Target Shipments' => $target_shipments, 'Target Achieved %' => round($target_shipments_achieved, 2).'%', 'Achieved Revenue' => $revenue[$sale_person_shipment->admin_id], 'Target Revenue' => $all_shipments_target_revenue, 'Target Revenue Achieved %' => round($target_revenue_achieved, 2).'%', 'Avg Revenue/Parcel' => round($avg_revenue[$sale_person_shipment->admin_id], 2), 'Contribution' => ($contribution[$sale_person_shipment->admin_id]) * 100];

            $serial++;

        }


        foreach ($walk_in_shipments as $walk_in_shipment) {
            $sale_person_array[] = ['serial' => $serial, 'Admin' => 'Walk-In', 'Achieved Shipments' => $walk_in_shipment->shipment_count, 'Target Shipments' => '', 'Target Achieved %' => '', 'Achieved Revenue' => $revenue[0], 'Target Revenue' => '', 'Target Revenue Achieved %' => '', 'Avg Revenue/Parcel' => round($avg_revenue[0], 2), 'Contribution' => $contribution[0] * 100];

            $serial++;
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

        $file_name_without_path = "reports/sale_person_numbers_report_" . $rm_id .'_'. $date_file_name . ".xlsx";
        $file_name = public_path() . "/reports/sale_person_numbers_report_" . $rm_id .'_'. $date_file_name . ".xlsx";
        $writer->save($file_name);

        return url('/') . '/' . $file_name_without_path;
    }
}
