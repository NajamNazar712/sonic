<?php

namespace App\Http\Controllers\Admins;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PHPExcel_Style_NumberFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\Datatables\Datatables;

class AdminRevenueReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public static function revenue_report_by_delivery_date($report_type)
    {
        if($report_type == 1){
            $from = Carbon::today()->subMonth(1)->firstOfMonth()->toDateTimeString();
            $to = Carbon::parse($from)->addDays(9)->endOfDay()->toDateTimeString();
        }
        if($report_type == 2){
            $from = Carbon::today()->subMonth(1)->startOfMonth()->addDays(10)->toDateTimeString();
            $to = Carbon::parse($from)->addDays(9)->endOfDay()->toDateTimeString();
        }
        if($report_type == 3){
            $from = Carbon::today()->subMonth(1)->firstOfMonth()->addDays(20)->toDateTimeString();
            $to = Carbon::today()->subMonth(1)->endOfMonth()->toDateTimeString();
        }

        $from_id = DB::table('shipments_journey')->select(DB::raw('MIN(id) as id'))->where('created_at', '>=', $from)->first()->id;

        $to_id = DB::table('shipments_journey')->select(DB::raw('MAX(id) as id'))->where('created_at', '>=', $from)->where('created_at', '<=', $to)->first()->id;

        $connection = 'reports';
        $sales = DB::connection($connection)->table('shipments_journey as sj')
            ->join('shipments', 'shipments.id', 'sj.shipment_id')
            ->leftJoin('users as u', 'u.id', '=', 'shipments.user_id')
            ->leftjoin('segments as seg', 'seg.id', '=', 'u.segment_id')
            ->leftjoin('sub_category_segments as seg_sub', 'seg_sub.id', '=', 'u.sub_segment_id')
            ->leftJoin('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->leftJoin('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->leftJoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('zones as z', 'z.id', '=', 'oc.zone_id')
            ->leftJoin('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftJoin('cities as h', 'dc.hub_id', '=', 'h.id')
            ->leftJoin('business_categories as bc', 'shipments.business_category_id', '=', 'bc.id')
            ->leftjoin('zone_class_cities as zcc', function ($join) {
                $join->on('z.id', '=', 'zcc.zone_id')
                    ->on('dc.id', '=', 'zcc.city_id')
                    ->on('zone_classification_id', '=', DB::connection('reports')->raw('IF (shipments.shipping_mode_id IN (1, 4), 1, 2)'));
            })
            ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=', 'sps.id')
            ->leftjoin('delivery_note_shipments as ds', function ($join) {
                $join->on('ds.shipment_id', '=', 'shipments.id')
                    ->where('ds.delivery_note_id', '=',
                        DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id and delivery_note_shipments.status > 3 and  delivery_note_shipments.status != 8)'));
            })
            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->leftJoin('pending_payment_shipments as pps', function ($join) use ($connection) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.type', '!=',2 );
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) use ($connection) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.type', '!=',2 );
            })
            ->leftJoin('pending_invoice_shipments as pis', function ($join) use ($connection) {
                $join->on('pis.shipment_id', '=', 'shipments.id')
                    ->where('pis.type', '!=',2 );
            })
            ->leftJoin('invoice_shipments as is', function ($join) use ($connection) {
                $join->on('is.shipment_id', '=', 'shipments.id')
                    ->where('is.type', '!=',2 );
            })
            ->select('shipments.tracking_number','u.id as account_no','bc.name as buisness_category','u.name as shipper','shipments.order_id as order_id','ss.name as current_status','sps.name as payment_status','dps.done_payment_id as payment_number','dnsdn.station_deposit_note_id as sdn_number','bt.booking_type as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','h.name as hub','z.name as zone','zcc.class','sm.mode as shipping_mode',DB::raw('SUM(DISTINCT pps.amount) as p_collection_amount'),'shipments.actual_weight','shipments.chargeable_weight','shipments.weight_charges','shipments.cash_handling_charges','shipments.insurance_charges','shipments.packaging_material_charges','shipments.fuel_surcharge','shipments.return_charges','shipments.replacement_charges','shipments.packaging_charges','shipments.try_and_buy_charges','shipments.nsa_osa_charges','shipments.intercept_charges',DB::raw('SUM(DISTINCT pps.gst) as p_gst'),DB::raw('SUM(DISTINCT pps.charges) as p_total_charges'),DB::raw('SUM(DISTINCT pps.payable) as p_net_payable'),DB::raw('SUM(DISTINCT shipments.amount) as s_collection_amount'),DB::raw('SUM(DISTINCT dps.amount) as d_collection_amount'),DB::raw('SUM(DISTINCT dps.gst) as d_gst'),DB::raw('SUM(DISTINCT dps.charges) as d_total_charges'),DB::raw('SUM(DISTINCT dps.payable) as d_net_payable'),'sj.created_at as delivered_or_returned','oc.id as origin_city_id','dc.id as destination_city_id','shipments.booking_type_id','usi.poc','shipments.shipper_status_id as shipment_status','u.account_type_id as account_type_id',DB::raw('SUM(DISTINCT pis.gst) as pis_gst'),DB::raw('SUM(DISTINCT is.gst) as is_gst'),'sj.shipper_status_id as dr_status_id','shipments.shipment_type','seg.name as segment','seg_sub.name as sub_segment')
            ->whereIn('sj.shipper_status_id', [14,20,30,36,37])
            ->where('sj.verification', '=', 1)
            ->whereNotIn('shipments.shipper_status_id', [1, 17])
            ->whereNotIn('u.id', [8761, 9358])
            ->whereBetween('sj.created_at', [$from, $to])
            ->where('sj.id', '>=', $from_id)
            ->where('sj.id', '<=', $to_id)
            ->groupBy('shipments.id')
            ->get();


        if($report_type == 1){
            $filename = 'revenue_report_by_delivery_first_ten_days.xlsx';
        }
        if($report_type == 2){
            $filename = 'revenue_report_by_delivery_second_ten_days.xlsx';
        }
        if($report_type == 3){
            $filename = 'revenue_report_by_delivery_last_ten_days.xlsx';
        }


        $details = array();

        $details[] = ['S.No.', 'Tracking Number', 'Account No.', 'Business Category', 'Shipper', 'Segment', 'Sub Segment', 'Order Id', 'Status', 'Payment Status', 'Payment Number', 'SDN Number', 'Service Type', 'Arrival Date', 'Origin', 'Destination', 'Hub', 'Zone', 'Class', 'Shipping Mode', 'Collection Amount', 'Actual Weight', 'Chargeable Weight', 'Weight Charges', 'Cash Handling Charges', 'Insurance Charges', 'Packaging Charges', 'Fuel Surcharge', 'Return Charges', 'Replacement Charges', 'Packing Charges', 'Try & Buy Charges', 'NSA/OSA Charges', 'Intercept Charges', 'GST', 'Total Charges', 'Estimated Charges', 'Net Payable', 'Delivered/Returned Date'];

        $serial_number = 1;
        foreach ($sales as $sale) {
            if ($sale->dr_status_id == 20) {
                $cash_handling_charges = 0;
                $return_charges = $sale->return_charges;
                $replacement_charges = 0;
                $try_and_buy_charges = 0;
            } else {
                if ($sale->cash_handling_charges != null) {
                    $cash_handling_charges = $sale->cash_handling_charges;
                } else {
                    $cash_handling_charges = 0;
                }
                $return_charges = 0;
                $replacement_charges = $sale->replacement_charges;
                $try_and_buy_charges = $sale->try_and_buy_charges;
            }

            $insurance_charges = $sale->insurance_charges;
            $account_number = str_pad($sale->account_no, 6, '0', STR_PAD_LEFT);
            $intercept_charges = $sale->intercept_charges;
            $weight_charges = $sale->weight_charges;
            $fuel_surcharge = $sale->fuel_surcharge;
            $nsa_osa_charges = $sale->nsa_osa_charges;
            $packaging_material_charges = $sale->packaging_material_charges;

            $packaging_charges = $sale->packaging_charges;

            if ($sale->booking_type_id == 4) {
                $shipper = $sale->shipper . ' (' . $sale->poc . ')';
            } else {
                $shipper = $sale->shipper;
            }
            $amount = 0;
            if ($sale->p_collection_amount != null && $sale->p_collection_amount !=0) {
                $amount = $sale->p_collection_amount;
            } else if ($sale->d_collection_amount != null && $sale->d_collection_amount !=0) {
                $amount = $sale->d_collection_amount;
            } else {
                $amount = $sale->s_collection_amount;
            }
            $collection_amount = $amount;
            $gst = '';
            if ($sale->account_type_id == 1) {
                if ($sale->p_gst != null) {
                    $gst = $sale->p_gst;
                } else if ($sale->d_gst != null) {
                    $gst = $sale->d_gst;
                }
            } else {
                if ($sale->pis_gst != null) {
                    $gst = $sale->pis_gst;
                } else if ($sale->is_gst != null) {
                    $gst = $sale->is_gst;
                }
            }
            $gst = (float)$gst;
            $total = 0;
            if ($sale->p_total_charges != null) {
                $total = $sale->p_total_charges;
            } else if ($sale->d_total_charges != null) {
                $total = $sale->d_total_charges;
            }
            $total_charges = (float)$total;
            $estimated = 0;
            $estimated = (($sale->weight_charges != null) ? $sale->weight_charges : 0) + (($sale->cash_handling_charges != null) ? $sale->cash_handling_charges : 0) + (($sale->insurance_charges != null) ? $sale->insurance_charges : 0) + (($sale->insurance_charges != null) ? $sale->insurance_charges : 0) + (($sale->return_charges != null) ? $sale->return_charges : 0) + (($sale->replacement_charges != null) ? $sale->replacement_charges : 0) + (($sale->fuel_surcharge != null) ? $sale->fuel_surcharge : 0) + (($sale->try_and_buy_charges != null) ? $sale->try_and_buy_charges : 0) + (($sale->packaging_material_charges != null) ? $sale->packaging_material_charges : 0) + (($sale->intercept_charges != null) ? $sale->intercept_charges : 0);
            $estimated_charges = (float)$estimated;
            $payable = '';
            if ($sale->p_net_payable != null) {
                $payable = $sale->p_net_payable;
            } else if ($sale->d_net_payable != null) {
                $payable = $sale->d_net_payable;
            }
            $net_payable = (float)$payable;
            $class = '';
            if ($sale->origin_city_id != $sale->destination_city_id) {
                switch ($sale->class) {
                    case 0:
                        $class = 'Class A';
                        break;
                    case 1:
                        $class = 'Class B';
                        break;
                    case 2:
                        $class = 'Class C';
                        break;
                    case 3:
                        $class = 'Class D';
                        break;
                }
            } else {
                $class = 'Local';
            }

            $row = array();

            $row[] = $serial_number;
            $row[] = $sale->tracking_number;
            $row[] = $account_number;
            $row[] = $sale->buisness_category;
            $row[] = $shipper;
            $row[] = $sale->segment;
            $row[] = $sale->sub_segment;
            $row[] = $sale->order_id;
            $row[] = $sale->current_status;
            $row[] = $sale->payment_status;
            $row[] = $sale->payment_number;
            $row[] = $sale->sdn_number;
            $row[] = $sale->service_type;
            $row[] = $sale->arrival_date;
            $row[] = $sale->origin;
            $row[] = $sale->destination;
            $row[] = $sale->hub;
            $row[] = $sale->zone;
            $row[] = $class;
            $row[] = $sale->shipping_mode;
            $row[] = $collection_amount;
            $row[] = $sale->actual_weight;
            $row[] = $sale->chargeable_weight;
            $row[] = $weight_charges;
            $row[] = $cash_handling_charges;
            $row[] = $insurance_charges;
            $row[] = $packaging_material_charges;
            $row[] = $fuel_surcharge;
            $row[] = $return_charges;
            $row[] = $replacement_charges;
            $row[] = $packaging_charges;
            $row[] = $try_and_buy_charges;
            $row[] = $nsa_osa_charges;
            $row[] = $intercept_charges;
            $row[] = $gst;
            $row[] = $total_charges;
            $row[] = $estimated_charges;
            $row[] = $net_payable;
            $row[] = $sale->delivered_or_returned;

            $details[] = $row;
            $serial_number++;
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        $spreadsheet->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('U')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('V')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('W')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('X')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('Y')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('Z')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AA')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AB')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AC')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AD')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AE')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AF')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AG')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AH')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AI')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AJ')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AK')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AL')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

        $spreadsheet->getActiveSheet()->getStyle('A1:AM1')->getFont()->setBold(TRUE);

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        ob_start();
        $writer->setPreCalculateFormulas(false);
        $writer->save('php://output');
        $contents = ob_get_contents();
        ob_end_clean();
        $filePath = '/reports/revenue/' . $filename;
        Storage::disk('public')->put($filePath, $contents);
        $from = Carbon::parse($from)->toDateString();
        $to = Carbon::parse($to)->toDateString();
        return ['file_path' => $filePath, 'from' => $from, 'to' => $to];
    }

    public static function revenue_report($report_type)
    {
        if($report_type == 1){
            $from = Carbon::today()->subMonth(1)->firstOfMonth()->toDateTimeString();
            $to = Carbon::parse($from)->addDays(9)->endOfDay()->toDateTimeString();
        }
        if($report_type == 2){
            $from = Carbon::today()->subMonth(1)->startOfMonth()->addDays(10)->toDateTimeString();
            $to = Carbon::parse($from)->addDays(9)->endOfDay()->toDateTimeString();
        }
        if($report_type == 3){
            $from = Carbon::today()->subMonth(1)->firstOfMonth()->addDays(20)->toDateTimeString();
            $to = Carbon::today()->subMonth(1)->endOfMonth()->toDateTimeString();
        }
        if($report_type == 4){
            $from = Carbon::today()->subDay()->toDateTimeString();
            $to = Carbon::parse($from)->endOfDay()->toDateTimeString();
        }

        $from_id = DB::table('shipments_journey')->select(DB::raw('MIN(id) as id'))->where('created_at', '>=', $from)->first()->id;

        $to_id = DB::table('shipments_journey')->select(DB::raw('MAX(id) as id'))->where('created_at', '>=', $from)->where('created_at', '<=', $to)->first()->id;
        $connection = 'reports';
        $sales = DB::connection($connection)->table('shipments_journey as sj')
            ->join('shipments', 'shipments.id', 'sj.shipment_id')
            ->leftJoin('users as u', 'u.id', '=', 'shipments.user_id')
            ->leftjoin('segments as seg', 'seg.id', '=', 'u.segment_id')
            ->leftjoin('sub_category_segments as seg_sub', 'seg_sub.id', '=', 'u.sub_segment_id')
            ->leftJoin('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->leftJoin('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->leftJoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('zones as z', 'z.id', '=', 'oc.zone_id')
            ->leftJoin('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftJoin('cities as h', 'dc.hub_id', '=', 'h.id')
            ->leftJoin('business_categories as bc', 'shipments.business_category_id', '=', 'bc.id')
            ->leftjoin('zone_class_cities as zcc', function ($join) {
                $join->on('z.id', '=', 'zcc.zone_id')
                    ->on('dc.id', '=', 'zcc.city_id')
                    ->on('zone_classification_id', '=', DB::connection('reports')->raw('IF (shipments.shipping_mode_id IN (1, 4), 1, 2)'));
            })
            ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=', 'sps.id')
            ->leftjoin('delivery_note_shipments as ds', function ($join) {
                $join->on('ds.shipment_id', '=', 'shipments.id')
                    ->where('ds.delivery_note_id', '=',
                        DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id and delivery_note_shipments.status > 3 and  delivery_note_shipments.status != 8)'));
            })
            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->leftJoin('pending_payment_shipments as pps', function ($join) use ($connection) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.type', '!=',2 );
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) use ($connection) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.type', '!=',2 );
            })
            ->leftJoin('pending_invoice_shipments as pis', function ($join) use ($connection) {
                $join->on('pis.shipment_id', '=', 'shipments.id')
                    ->where('pis.type', '!=',2 );
            })
            ->leftJoin('invoice_shipments as is', function ($join) use ($connection) {
                $join->on('is.shipment_id', '=', 'shipments.id')
                    ->where('is.type', '!=',2 );
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37) and shipments_journey.verification = 1)'));
            })
            ->select('shipments.tracking_number','u.id as account_no','bc.name as buisness_category','u.name as shipper','shipments.order_id as order_id','ss.name as current_status','sps.name as payment_status','dps.done_payment_id as payment_number','dnsdn.station_deposit_note_id as sdn_number','bt.booking_type as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','h.name as hub','z.name as zone','zcc.class','sm.mode as shipping_mode',DB::raw('SUM(DISTINCT pps.amount) as p_collection_amount'),'shipments.actual_weight','shipments.chargeable_weight','shipments.weight_charges','shipments.cash_handling_charges','shipments.insurance_charges','shipments.packaging_material_charges','shipments.fuel_surcharge','shipments.return_charges','shipments.replacement_charges','shipments.packaging_charges','shipments.try_and_buy_charges','shipments.nsa_osa_charges','shipments.intercept_charges',DB::raw('SUM(DISTINCT pps.gst) as p_gst'),DB::raw('SUM(DISTINCT pps.charges) as p_total_charges'),DB::raw('SUM(DISTINCT pps.payable) as p_net_payable'),'shipments.amount as s_collection_amount',DB::raw('SUM(DISTINCT dps.amount) as d_collection_amount'),DB::raw('SUM(DISTINCT dps.gst) as d_gst'),DB::raw('SUM(DISTINCT dps.charges) as d_total_charges'),DB::raw('SUM(DISTINCT dps.payable) as d_net_payable'),'dr.created_at as delivered_or_returned','oc.id as origin_city_id','dc.id as destination_city_id','shipments.booking_type_id','usi.poc','shipments.shipper_status_id as shipment_status','u.account_type_id as account_type_id',DB::raw('SUM(DISTINCT pis.gst) as pis_gst'),DB::raw('SUM(DISTINCT is.gst) as is_gst'),'dr.shipper_status_id as dr_status_id','shipments.shipment_type','seg.name as segment','seg_sub.name as sub_segment'
            )
            ->where('sj.shipper_status_id', '=', 2)
            ->whereNotIn('shipments.shipper_status_id', [1, 17])
            ->whereNotIn('u.id', [8761, 9358])
            ->whereBetween('sj.created_at', [$from, $to])
            ->where('sj.id', '>=', $from_id)
            ->where('sj.id', '<=', $to_id)
            ->groupBy('shipments.id')
            ->get();


        if($report_type == 1){
            $filename = 'revenue_report_by_arrival_first_ten_days.xlsx';
        }
        if($report_type == 2){
            $filename = 'revenue_report_by_arrival_second_ten_days.xlsx';
        }
        if($report_type == 3){
            $filename = 'revenue_report_by_arrival_last_ten_days.xlsx';
        }
        if($report_type == 4 ){
            $filename = 'revenue_report_by_arrival_on_daily_basis.xlsx';
        }

        $details = array();

        $details[] = ['S.No.', 'Tracking Number', 'Account No.', 'Business Category', 'Shipper', 'Segment', 'Sub Segment', 'Order Id', 'Status', 'Payment Status', 'Payment Number', 'SDN Number', 'Service Type', 'Arrival Date', 'Origin', 'Destination', 'Hub', 'Zone', 'Class', 'Shipping Mode', 'Collection Amount', 'Actual Weight', 'Chargeable Weight', 'Weight Charges', 'Cash Handling Charges', 'Insurance Charges', 'Packaging Charges', 'Fuel Surcharge', 'Return Charges', 'Replacement Charges', 'Packing Charges', 'Try & Buy Charges', 'NSA/OSA Charges', 'Intercept Charges', 'GST', 'Total Charges', 'Estimated Charges', 'Net Payable', 'Delivered/Returned Date'];

        $serial_number = 1;
        foreach ($sales as $sale) {
            if ($sale->dr_status_id == 20) {
                $cash_handling_charges = 0;
                $return_charges = $sale->return_charges;
                $replacement_charges = 0;
                $try_and_buy_charges = 0;
            } else {
                if ($sale->cash_handling_charges != null) {
                    $cash_handling_charges = $sale->cash_handling_charges;
                } else {
                    $cash_handling_charges = 0;
                }
                $return_charges = 0;
                $replacement_charges = $sale->replacement_charges;
                $try_and_buy_charges = $sale->try_and_buy_charges;
            }

            $insurance_charges = $sale->insurance_charges;
            $account_number = str_pad($sale->account_no, 6, '0', STR_PAD_LEFT);
            $intercept_charges = $sale->intercept_charges;
            $weight_charges = $sale->weight_charges;
            $fuel_surcharge = $sale->fuel_surcharge;
            $nsa_osa_charges = $sale->nsa_osa_charges;
            $packaging_material_charges = $sale->packaging_material_charges;

            $packaging_charges = $sale->packaging_charges;

            if ($sale->booking_type_id == 4) {
                $shipper = $sale->shipper . ' (' . $sale->poc . ')';
            } else {
                $shipper = $sale->shipper;
            }
            $amount = 0;
            if ($sale->p_collection_amount != null && $sale->p_collection_amount !=0) {
                $amount = $sale->p_collection_amount;
            } else if ($sale->d_collection_amount != null && $sale->d_collection_amount !=0) {
                $amount = $sale->d_collection_amount;
            } else {
                $amount = $sale->s_collection_amount;
            }
            $collection_amount = $amount;
            $gst = 0;
            if ($sale->account_type_id == 1) {
                if ($sale->p_gst != null) {
                    $gst = $sale->p_gst;
                } else if ($sale->d_gst != null) {
                    $gst = $sale->d_gst;
                }
            } else {
                if ($sale->pis_gst != null) {
                    $gst = $sale->pis_gst;
                } else if ($sale->is_gst != null) {
                    $gst = $sale->is_gst;
                }
            }
            $gst = (float)$gst;
            $total = 0;
            if ($sale->p_total_charges != null) {
                $total = $sale->p_total_charges;
            } else if ($sale->d_total_charges != null) {
                $total = $sale->d_total_charges;
            }
            $total_charges = (float)$total;
            $estimated = 0;
            $estimated = (($sale->weight_charges != null) ? $sale->weight_charges : 0) + (($sale->cash_handling_charges != null) ? $sale->cash_handling_charges : 0) + (($sale->insurance_charges != null) ? $sale->insurance_charges : 0) + (($sale->insurance_charges != null) ? $sale->insurance_charges : 0) + (($sale->return_charges != null) ? $sale->return_charges : 0) + (($sale->replacement_charges != null) ? $sale->replacement_charges : 0) + (($sale->fuel_surcharge != null) ? $sale->fuel_surcharge : 0) + (($sale->try_and_buy_charges != null) ? $sale->try_and_buy_charges : 0) + (($sale->packaging_material_charges != null) ? $sale->packaging_material_charges : 0) + (($sale->intercept_charges != null) ? $sale->intercept_charges : 0);
            $estimated_charges = (float)$estimated;
            $payable = '';
            if ($sale->p_net_payable != null) {
                $payable = $sale->p_net_payable;
            } else if ($sale->d_net_payable != null) {
                $payable = $sale->d_net_payable;
            }
            $net_payable = (float)$payable;
            $class = '';
            if ($sale->origin_city_id != $sale->destination_city_id) {
                switch ($sale->class) {
                    case 0:
                        $class = 'Class A';
                        break;
                    case 1:
                        $class = 'Class B';
                        break;
                    case 2:
                        $class = 'Class C';
                        break;
                    case 3:
                        $class = 'Class D';
                        break;
                }
            } else {
                $class = 'Local';
            }

            $row = array();

            $row[] = $serial_number;
            $row[] = $sale->tracking_number;
            $row[] = $account_number;
            $row[] = $sale->buisness_category;
            $row[] = $shipper;
            $row[] = $sale->segment;
            $row[] = $sale->sub_segment;
            $row[] = $sale->order_id;
            $row[] = $sale->current_status;
            $row[] = $sale->payment_status;
            $row[] = $sale->payment_number;
            $row[] = $sale->sdn_number;
            $row[] = $sale->service_type;
            $row[] = $sale->arrival_date;
            $row[] = $sale->origin;
            $row[] = $sale->destination;
            $row[] = $sale->hub;
            $row[] = $sale->zone;
            $row[] = $class;
            $row[] = $sale->shipping_mode;
            $row[] = $collection_amount;
            $row[] = $sale->actual_weight;
            $row[] = $sale->chargeable_weight;
            $row[] = $weight_charges;
            $row[] = $cash_handling_charges;
            $row[] = $insurance_charges;
            $row[] = $packaging_material_charges;
            $row[] = $fuel_surcharge;
            $row[] = $return_charges;
            $row[] = $replacement_charges;
            $row[] = $packaging_charges;
            $row[] = $try_and_buy_charges;
            $row[] = $nsa_osa_charges;
            $row[] = $intercept_charges;
            $row[] = $gst;
            $row[] = $total_charges;
            $row[] = $estimated_charges;
            $row[] = $net_payable;
            $row[] = $sale->delivered_or_returned;

            $details[] = $row;
            $serial_number++;
        }

        $spreadsheet = new Spreadsheet();

// Define columns for number formatting
        $columns = [
            'B', 'C', 'K', 'L', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH',
            'AI', 'AJ', 'AK', 'AL'
        ];

// Set number format for specified columns
        foreach ($columns as $column) {
            $spreadsheet->getActiveSheet()
                ->getStyle($column)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER);
        }

// Set text format for column H
        $spreadsheet->getActiveSheet()
            ->getStyle('H')
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_TEXT);

// Apply bold formatting to the first row
        $spreadsheet->getActiveSheet()
            ->getStyle('A1:AM1')
            ->getFont()
            ->setBold(true);

// Add data to the spreadsheet
        $spreadsheet->getActiveSheet()->fromArray($details);

// Save the spreadsheet to an XLSX file
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        ob_start();
        $writer->setPreCalculateFormulas(false);
        $writer->save('php://output');
        $contents = ob_get_contents();
        ob_end_clean();

        $filePath = '/reports/revenue/' . $filename;
        Storage::disk('public')->put($filePath, $contents);
        $from = Carbon::parse($from)->toDateString();
        $to = Carbon::parse($to)->toDateString();

        return ['file_path' => $filePath, 'from' => $from, 'to' => $to];
    }
    public static function revenue_report_last_month($report_type)
    {
        if($report_type == 1){
            $from = Carbon::today()->subMonth(1)->firstOfMonth()->toDateTimeString();
            $to = Carbon::parse($from)->addDays(9)->endOfDay()->toDateTimeString();
        }
        if($report_type == 2){
            $from = Carbon::today()->subMonth(1)->startOfMonth()->addDays(10)->toDateTimeString();
            $to = Carbon::parse($from)->addDays(9)->endOfDay()->toDateTimeString();
        }
        if($report_type == 3){
            $from = Carbon::today()->subMonth(1)->firstOfMonth()->addDays(20)->toDateTimeString();
            $to = Carbon::parse($from)->endOfMonth()->toDateTimeString();
        }
        if($report_type == 4){
            $from = Carbon::today()->subDay()->toDateTimeString();
            $to = Carbon::parse($from)->endOfDay()->toDateTimeString();
        }

        $from_id = DB::table('shipments_journey')->select(DB::raw('MIN(id) as id'))->where('created_at', '>=', $from)->first()->id;

        $to_id = DB::table('shipments_journey')->select(DB::raw('MAX(id) as id'))->where('created_at', '>=', $from)->where('created_at', '<=', $to)->first()->id;
        $connection = 'reports';
        $sales = DB::connection($connection)->table('shipments_journey as sj')
            ->join('shipments', 'shipments.id', 'sj.shipment_id')
            ->leftJoin('users as u', 'u.id', '=', 'shipments.user_id')
            ->leftjoin('segments as seg', 'seg.id', '=', 'u.segment_id')
            ->leftjoin('sub_category_segments as seg_sub', 'seg_sub.id', '=', 'u.sub_segment_id')
            ->leftJoin('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->leftJoin('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->leftJoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('zones as z', 'z.id', '=', 'oc.zone_id')
            ->leftJoin('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftJoin('cities as h', 'dc.hub_id', '=', 'h.id')
            ->leftJoin('business_categories as bc', 'shipments.business_category_id', '=', 'bc.id')
            ->leftjoin('zone_class_cities as zcc', function ($join) {
                $join->on('z.id', '=', 'zcc.zone_id')
                    ->on('dc.id', '=', 'zcc.city_id')
                    ->on('zone_classification_id', '=', DB::connection('reports')->raw('IF (shipments.shipping_mode_id IN (1, 4), 1, 2)'));
            })
            ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=', 'sps.id')
            ->leftjoin('delivery_note_shipments as ds', function ($join) {
                $join->on('ds.shipment_id', '=', 'shipments.id')
                    ->where('ds.delivery_note_id', '=',
                        DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id and delivery_note_shipments.status > 3 and  delivery_note_shipments.status != 8)'));
            })
            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->leftJoin('pending_payment_shipments as pps', function ($join) use ($connection) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.type', '!=',2 );
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) use ($connection) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.type', '!=',2 );
            })
            ->leftJoin('pending_invoice_shipments as pis', function ($join) use ($connection) {
                $join->on('pis.shipment_id', '=', 'shipments.id')
                    ->where('pis.type', '!=',2 );
            })
            ->leftJoin('invoice_shipments as is', function ($join) use ($connection) {
                $join->on('is.shipment_id', '=', 'shipments.id')
                    ->where('is.type', '!=',2 );
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37) and shipments_journey.verification = 1)'));
            })
            ->select('shipments.tracking_number','u.id as account_no','bc.name as buisness_category','u.name as shipper','shipments.order_id as order_id','ss.name as current_status','sps.name as payment_status','dps.done_payment_id as payment_number','dnsdn.station_deposit_note_id as sdn_number','bt.booking_type as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','h.name as hub','z.name as zone','zcc.class','sm.mode as shipping_mode',DB::raw('SUM(DISTINCT pps.amount) as p_collection_amount'),'shipments.actual_weight','shipments.chargeable_weight','shipments.weight_charges','shipments.cash_handling_charges','shipments.insurance_charges','shipments.packaging_material_charges','shipments.fuel_surcharge','shipments.return_charges','shipments.replacement_charges','shipments.packaging_charges','shipments.try_and_buy_charges','shipments.nsa_osa_charges','shipments.intercept_charges',DB::raw('SUM(DISTINCT pps.gst) as p_gst'),DB::raw('SUM(DISTINCT pps.charges) as p_total_charges'),DB::raw('SUM(DISTINCT pps.payable) as p_net_payable'),'shipments.amount as s_collection_amount',DB::raw('SUM(DISTINCT dps.amount) as d_collection_amount'),DB::raw('SUM(DISTINCT dps.gst) as d_gst'),DB::raw('SUM(DISTINCT dps.charges) as d_total_charges'),DB::raw('SUM(DISTINCT dps.payable) as d_net_payable'),'dr.created_at as delivered_or_returned','oc.id as origin_city_id','dc.id as destination_city_id','shipments.booking_type_id','usi.poc','shipments.shipper_status_id as shipment_status','u.account_type_id as account_type_id',DB::raw('SUM(DISTINCT pis.gst) as pis_gst'),DB::raw('SUM(DISTINCT is.gst) as is_gst'),'dr.shipper_status_id as dr_status_id','shipments.shipment_type','seg.name as segment','seg_sub.name as sub_segment'
            )
            ->where('sj.shipper_status_id', '=', 2)
            ->whereNotIn('shipments.shipper_status_id', [1, 17])
            ->whereNotIn('u.id', [8761, 9358])
            ->whereBetween('sj.created_at', [$from, $to])
            ->where('sj.id', '>=', $from_id)
            ->where('sj.id', '<=', $to_id)
            ->groupBy('shipments.id')
            ->get();


        if($report_type == 1){
            $filename = 'revenue_report_by_arrival_first_ten_days.xlsx';
        }
        if($report_type == 2){
            $filename = 'revenue_report_by_arrival_second_ten_days.xlsx';
        }
        if($report_type == 3){
            $filename = 'revenue_report_by_arrival_last_ten_days.xlsx';
        }
        if($report_type == 4 ){
            $filename = 'revenue_report_by_arrival_on_daily_basis.xlsx';
        }

        $details = array();

        $details[] = ['S.No.', 'Tracking Number', 'Account No.', 'Business Category', 'Shipper', 'Segment', 'Sub Segment', 'Order Id', 'Status', 'Payment Status', 'Payment Number', 'SDN Number', 'Service Type', 'Arrival Date', 'Origin', 'Destination', 'Hub', 'Zone', 'Class', 'Shipping Mode', 'Collection Amount', 'Actual Weight', 'Chargeable Weight', 'Weight Charges', 'Cash Handling Charges', 'Insurance Charges', 'Packaging Charges', 'Fuel Surcharge', 'Return Charges', 'Replacement Charges', 'Packing Charges', 'Try & Buy Charges', 'NSA/OSA Charges', 'Intercept Charges', 'GST', 'Total Charges', 'Estimated Charges', 'Net Payable', 'Delivered/Returned Date'];

        $serial_number = 1;
        foreach ($sales as $sale) {
            if ($sale->dr_status_id == 20) {
                $cash_handling_charges = 0;
                $return_charges = $sale->return_charges;
                $replacement_charges = 0;
                $try_and_buy_charges = 0;
            } else {
                if ($sale->cash_handling_charges != null) {
                    $cash_handling_charges = $sale->cash_handling_charges;
                } else {
                    $cash_handling_charges = 0;
                }
                $return_charges = 0;
                $replacement_charges = $sale->replacement_charges;
                $try_and_buy_charges = $sale->try_and_buy_charges;
            }

            $insurance_charges = $sale->insurance_charges;
            $account_number = str_pad($sale->account_no, 6, '0', STR_PAD_LEFT);
            $intercept_charges = $sale->intercept_charges;
            $weight_charges = $sale->weight_charges;
            $fuel_surcharge = $sale->fuel_surcharge;
            $nsa_osa_charges = $sale->nsa_osa_charges;
            $packaging_material_charges = $sale->packaging_material_charges;

            $packaging_charges = $sale->packaging_charges;

            if ($sale->booking_type_id == 4) {
                $shipper = $sale->shipper . ' (' . $sale->poc . ')';
            } else {
                $shipper = $sale->shipper;
            }
            $amount = 0;
            if ($sale->p_collection_amount != null && $sale->p_collection_amount !=0) {
                $amount = $sale->p_collection_amount;
            } else if ($sale->d_collection_amount != null && $sale->d_collection_amount !=0) {
                $amount = $sale->d_collection_amount;
            } else {
                $amount = $sale->s_collection_amount;
            }
            $collection_amount = $amount;
            $gst = 0;
            if ($sale->account_type_id == 1) {
                if ($sale->p_gst != null) {
                    $gst = $sale->p_gst;
                } else if ($sale->d_gst != null) {
                    $gst = $sale->d_gst;
                }
            } else {
                if ($sale->pis_gst != null) {
                    $gst = $sale->pis_gst;
                } else if ($sale->is_gst != null) {
                    $gst = $sale->is_gst;
                }
            }
            $gst = (float)$gst;
            $total = 0;
            if ($sale->p_total_charges != null) {
                $total = $sale->p_total_charges;
            } else if ($sale->d_total_charges != null) {
                $total = $sale->d_total_charges;
            }
            $total_charges = (float)$total;
            $estimated = 0;
            $estimated = (($sale->weight_charges != null) ? $sale->weight_charges : 0) + (($sale->cash_handling_charges != null) ? $sale->cash_handling_charges : 0) + (($sale->insurance_charges != null) ? $sale->insurance_charges : 0) + (($sale->insurance_charges != null) ? $sale->insurance_charges : 0) + (($sale->return_charges != null) ? $sale->return_charges : 0) + (($sale->replacement_charges != null) ? $sale->replacement_charges : 0) + (($sale->fuel_surcharge != null) ? $sale->fuel_surcharge : 0) + (($sale->try_and_buy_charges != null) ? $sale->try_and_buy_charges : 0) + (($sale->packaging_material_charges != null) ? $sale->packaging_material_charges : 0) + (($sale->intercept_charges != null) ? $sale->intercept_charges : 0);
            $estimated_charges = (float)$estimated;
            $payable = '';
            if ($sale->p_net_payable != null) {
                $payable = $sale->p_net_payable;
            } else if ($sale->d_net_payable != null) {
                $payable = $sale->d_net_payable;
            }
            $net_payable = (float)$payable;
            $class = '';
            if ($sale->origin_city_id != $sale->destination_city_id) {
                switch ($sale->class) {
                    case 0:
                        $class = 'Class A';
                        break;
                    case 1:
                        $class = 'Class B';
                        break;
                    case 2:
                        $class = 'Class C';
                        break;
                    case 3:
                        $class = 'Class D';
                        break;
                }
            } else {
                $class = 'Local';
            }

            $row = array();

            $row[] = $serial_number;
            $row[] = $sale->tracking_number;
            $row[] = $account_number;
            $row[] = $sale->buisness_category;
            $row[] = $shipper;
            $row[] = $sale->segment;
            $row[] = $sale->sub_segment;
            $row[] = $sale->order_id;
            $row[] = $sale->current_status;
            $row[] = $sale->payment_status;
            $row[] = $sale->payment_number;
            $row[] = $sale->sdn_number;
            $row[] = $sale->service_type;
            $row[] = $sale->arrival_date;
            $row[] = $sale->origin;
            $row[] = $sale->destination;
            $row[] = $sale->hub;
            $row[] = $sale->zone;
            $row[] = $class;
            $row[] = $sale->shipping_mode;
            $row[] = $collection_amount;
            $row[] = $sale->actual_weight;
            $row[] = $sale->chargeable_weight;
            $row[] = $weight_charges;
            $row[] = $cash_handling_charges;
            $row[] = $insurance_charges;
            $row[] = $packaging_material_charges;
            $row[] = $fuel_surcharge;
            $row[] = $return_charges;
            $row[] = $replacement_charges;
            $row[] = $packaging_charges;
            $row[] = $try_and_buy_charges;
            $row[] = $nsa_osa_charges;
            $row[] = $intercept_charges;
            $row[] = $gst;
            $row[] = $total_charges;
            $row[] = $estimated_charges;
            $row[] = $net_payable;
            $row[] = $sale->delivered_or_returned;

            $details[] = $row;
            $serial_number++;
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        $spreadsheet->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('U')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('V')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('W')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('X')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('Y')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('Z')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AA')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AB')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AC')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AD')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AE')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AF')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AG')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AH')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AI')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AJ')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AK')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('AL')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

        $spreadsheet->getActiveSheet()->getStyle('A1:AM1')->getFont()->setBold(TRUE);

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        ob_start();
        $writer->setPreCalculateFormulas(false);
        $writer->save('php://output');
        $contents = ob_get_contents();
        ob_end_clean();

        $filePath = '/reports/revenue/'.date('Ymd',strtotime($from)).'_'.date('Ymd',strtotime($to)).'_' . $filename;
        Storage::disk('public')->put($filePath, $contents);
        $from = Carbon::parse($from)->toDateString();
        $to = Carbon::parse($to)->toDateString();

        return ['file_path' => $filePath, 'from' => $from, 'to' => $to];
    }

    public function revenue_report_by_invoice_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 637);
        $shippers = DB::connection('reports')->table('users')->whereIn('status', [3, 4])->select('id', 'name')->get();
        $cities = DB::connection('reports')->table('cities')->select('id', 'name')->get();
        $shipping_modes = DB::connection('reports')->table('shipping_modes')->get(['id', 'mode']);
        $segments = DB::connection('reports')->table('segments')->select('id', 'name')->get();
        $sub_segments = DB::connection('reports')->table('sub_category_segments')->select('id', 'name')->get();
        $account_types = DB::connection('reports')->table('account_types')->select('id', 'name')->get();
        $business_categories = DB::connection('reports')->table('business_categories')->select('id', 'name')->get();
        return view('admin.reports.revenue_report_by_invoice')->with(['business_categories' => $business_categories, 'shippers' => $shippers, 'cities' => $cities, 'account_types' => $account_types, 'segments' => $segments, 'sub_segments' => $sub_segments, 'shipping_modes' => $shipping_modes]);
    }

    public function revenue_report_by_invoice_list(Request $request){
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 638);
        }

        $from = $request->get('search_date_from');
        $from = Carbon::parse($from)->toDateTimeString();
        $to = $request->get('search_date_to');
        $to = Carbon::parse($to)->toDateTimeString();

        $invoice = DB::connection('reports')->table('revenue_by_invoice_reports as rbi')
            ->leftjoin('users', 'rbi.user_id', '=', 'users.id')
            ->leftjoin('segments as seg', 'seg.id', '=', 'users.segment_id')
            ->leftjoin('sub_category_segments as seg_sub', 'seg_sub.id', '=', 'users.sub_segment_id')
            ->leftjoin('account_types as at', 'at.id', '=', 'users.account_type_id')
            ->leftjoin('business_categories as bc', 'bc.id', '=', 'rbi.business_category_id')
            ->leftjoin('cities as oc', 'oc.id', '=', 'rbi.origin_id');

        $invoice->select('at.name as account_type', 'bc.name as business_category', 'seg.name as segment', 'seg_sub.name as sub_segment', 'users.id as account_no', 'users.name as shipper', 'oc.name as origin', 'rbi.invoice_number', 'rbi.invoicing_date', 'rbi.weight_charges', 'rbi.cash_handling_charges', 'rbi.insurance_charges', 'rbi.return_charges', 'rbi.replacement_charges', 'rbi.fuel_surcharge', 'rbi.try_buy_charges', 'rbi.packaging_charges', 'rbi.gst', 'rbi.total_charges', 'rbi.nsa_osa_charges', 'rbi.packing_charges', 'rbi.intercept_charges','rbi.payment_type as payment_type');

        if ($request->get('search_date_from') && $request->get('search_date_to')) {

            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $invoice->whereBetween('rbi.invoicing_date', [$from, $to]);
        }

        $datatable = Datatables::of($invoice)
            ->editColumn('weight_charges', function ($invoice) {
                return number_format($invoice->weight_charges,2);
            })
            ->editColumn('cash_handling_charges', function ($invoice) {
                return number_format($invoice->cash_handling_charges,2);
            })
            ->editColumn('insurance_charges', function ($invoice) {
                return number_format($invoice->insurance_charges,2);
            })
            ->editColumn('return_charges', function ($invoice) {
                return number_format($invoice->return_charges,2);
            })
            ->editColumn('replacement_charges', function ($invoice) {
                return number_format($invoice->replacement_charges,2);
            })
            ->editColumn('fuel_surcharge', function ($invoice) {
                return number_format($invoice->fuel_surcharge,2);
            })
            ->editColumn('packaging_charges', function ($invoice) {
                return number_format($invoice->packaging_charges,2);
            })
            ->editColumn('try_buy_charges', function ($invoice) {
                return number_format($invoice->try_buy_charges,2);
            })
            ->editColumn('packing_charges', function ($invoice) {
                return number_format($invoice->packing_charges,2);
            })
            ->editColumn('total_charges', function ($invoice) {
                return number_format($invoice->total_charges,2);
            })
            ->editColumn('nsa_osa_charges', function ($invoice) {
                return number_format($invoice->nsa_osa_charges,2);
            })
            ->editColumn('gst', function ($invoice) {
                return number_format($invoice->gst,2);
            })
            ->editColumn('intercept_charges', function ($invoice) {
                return number_format($invoice->intercept_charges,2);
            })
            ->editColumn('payment_type', function ($invoice) {
                if (!is_null($invoice->payment_type)) {
                    if ($invoice->payment_type == 1) {
                        return "Done";
                    } else {
                        return "Make";
                    }
                } else {
                    return '';
                }
            })
            ->addColumn('total_invoice_amount', function ($invoice) {
                return number_format(($invoice->total_charges + $invoice->gst),2);
            });

            if($search_invoice_number = $request->get('search_invoice_number')){
                $invoice->where('rbi.invoice_number','=', $search_invoice_number);
            }
            if($shipper = $request->get('search_shipper')){
                $invoice->where('rbi.user_id','=', $shipper);
            }
            if ($origin = $request->get('search_origin')) {
                $invoice->where('oc.id', '=', $origin);
            }
            if ($search_segment = $request->get('search_segment')) {
                $invoice->where('users.segment_id', '=', $search_segment);
            }
            if ($search_sub_segment = $request->get('search_sub_segment')) {
                $invoice->where('users.sub_segment_id', '=', $search_sub_segment);
            }
            if ($search_account_type = $request->get('search_account_type')) {
                $invoice->where('users.account_type_id', '=', $search_account_type);
            }
            if ($search_business_category = $request->get('search_business_category')) {
                $invoice->where('rbi.business_category_id', '=', $search_business_category);
            }

            return $datatable->make(true);
    }
}
