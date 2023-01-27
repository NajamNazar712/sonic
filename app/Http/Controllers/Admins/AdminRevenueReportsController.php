<?php

namespace App\Http\Controllers\Admins;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PHPExcel_Style_NumberFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            $to = Carbon::today()->subMonth(1)->endOfMonth()->toDateTimeString();
        }
        if($report_type == 2){
            $from = Carbon::today()->firstOfMonth()->toDateTimeString();
            $to = Carbon::parse($from)->addDays(24)->endOfDay()->toDateTimeString();
        }
        if($report_type == 3){
            $from = Carbon::today()->subMonth(1)->firstOfMonth()->addDays(25)->toDateTimeString();
            $to = Carbon::today()->subMonth(1)->endOfMonth()->toDateTimeString();
        }
        $sales = DB::connection('reports')->table('shipments')->join('users as u', 'u.id', '=', 'shipments.user_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('zones as z', 'z.id', '=', 'oc.zone_id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('business_categories as bc', 'shipments.business_category_id', '=', 'bc.id')
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
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('pending_payment_shipments as pps', function ($join) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.id', '=',
                        DB::connection('reports')->raw('(select max(id) from pending_payment_shipments where pending_payment_shipments.shipment_id = shipments.id and pending_payment_shipments.type != 2)'));
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.id', '=',
                        DB::connection('reports')->raw('(select max(id) from done_payment_shipments where done_payment_shipments.shipment_id = shipments.id and done_payment_shipments.type != 2)'));
            })
            ->leftJoin('pending_invoice_shipments as pis', function ($join) {
                $join->on('pis.shipment_id', '=', 'shipments.id')
                    ->where('pis.id', '=',
                        DB::connection('reports')->raw('(select max(id) from pending_invoice_shipments where pending_invoice_shipments.shipment_id = shipments.id and pending_invoice_shipments.type != 2)'));
            })
            ->leftJoin('invoice_shipments as is', function ($join) {
                $join->on('is.shipment_id', '=', 'shipments.id')
                    ->where('is.id', '=',
                        DB::connection('reports')->raw('(select max(id) from invoice_shipments where invoice_shipments.shipment_id = shipments.id and invoice_shipments.type != 2)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37) and shipments_journey.verification = 1)'));
            })
            ->select('shipments.tracking_number', 'u.id as account_no', 'bc.name as buisness_category', 'u.name as shipper', 'shipments.order_id as order_id', 'ss.name as current_status', 'sps.name as payment_status', 'dps.done_payment_id as payment_number', 'dnsdn.station_deposit_note_id as sdn_number', 'bt.booking_type as service_type', 'sj.created_at as arrival_date', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'z.name as zone', 'zcc.class', 'sm.mode as shipping_mode', 'pps.amount as p_collection_amount', 'shipments.actual_weight', 'shipments.chargeable_weight', 'shipments.weight_charges', 'shipments.cash_handling_charges', 'shipments.insurance_charges', 'shipments.packaging_material_charges', 'shipments.fuel_surcharge', 'shipments.return_charges', 'shipments.replacement_charges', 'shipments.packaging_charges', 'shipments.try_and_buy_charges', 'shipments.nsa_osa_charges', 'shipments.intercept_charges', 'pps.gst as p_gst', 'pps.charges as p_total_charges', 'pps.payable as p_net_payable', 'shipments.amount as s_collection_amount', 'dps.amount as d_collection_amount', 'dps.gst as d_gst', 'dps.charges as d_total_charges', 'dps.payable as d_net_payable', 'dr.created_at as delivered_or_returned', 'oc.id as origin_city_id', 'dc.id as destination_city_id', 'shipments.booking_type_id', 'usi.poc', 'shipments.shipper_status_id as shipment_status', 'u.account_type_id as account_type_id', 'pis.gst as pis_gst', 'is.gst as is_gst', 'dr.shipper_status_id as dr_status_id', 'shipments.shipment_type')
            ->whereNotIn('shipments.shipper_status_id', [1, 17])
            ->whereNotIn('u.id', [8761, 9358])
            ->whereBetween('dr.created_at', [$from, $to])
            ->get();


        if($report_type == 1){
            $filename = 'revenue_report_by_delivery_first_to_last.xlsx';
        }
        if($report_type == 2){
            $filename = 'revenue_report_by_delivery_first_to_25.xlsx';
        }
        if($report_type == 3){
            $filename = 'revenue_report_by_delivery_26_to_last.xlsx';
        }


        $details = array();

        $details[] = ['S.No.', 'Tracking Number', 'Account No.', 'Business Category', 'Shipper', 'Order Id', 'Status', 'Payment Status', 'Payment Number', 'SDN Number', 'Service Type', 'Arrival Date', 'Origin', 'Destination', 'Hub', 'Zone', 'Class', 'Shipping Mode', 'Collection Amount', 'Actual Weight', 'Chargeable Weight', 'Weight Charges', 'Cash Handling Charges', 'Insurance Charges', 'Packaging Charges', 'Fuel Surcharge', 'Return Charges', 'Replacement Charges', 'Packing Charges', 'Try & Buy Charges', 'NSA/OSA Charges', 'Intercept Charges', 'GST', 'Total Charges', 'Estimated Charges', 'Net Payable', 'Delivered/Returned Date'];

        $serial_number = 1;
        foreach ($sales as $sale) {
            if ($sale->dr_status_id == 20) {
                $cash_handling_charges = "-";
                $return_charges = number_format($sale->return_charges, 2);
                $replacement_charges = "-";
                $try_and_buy_charges = "-";
            } else {
                if ($sale->cash_handling_charges != null) {
                    $cash_handling_charges = number_format($sale->cash_handling_charges, 2);
                } else {
                    $cash_handling_charges = "-";
                }
                $return_charges = "-";
                $replacement_charges = number_format($sale->replacement_charges, 2);
                $try_and_buy_charges = number_format($sale->try_and_buy_charges, 2);
            }

            $insurance_charges = number_format($sale->insurance_charges, 2);
            $account_number = str_pad($sale->account_no, 6, '0', STR_PAD_LEFT);
            $intercept_charges = number_format($sale->intercept_charges, 2);
            $weight_charges = number_format($sale->weight_charges, 2);
            $fuel_surcharge = number_format($sale->fuel_surcharge, 2);
            $nsa_osa_charges = number_format($sale->nsa_osa_charges, 2);
            $packaging_material_charges = number_format($sale->packaging_material_charges, 2);

            $packaging_charges = number_format($sale->packaging_charges, 2);

            if ($sale->booking_type_id == 4) {
                $shipper = $sale->shipper . ' (' . $sale->poc . ')';
            } else {
                $shipper = $sale->shipper;
            }
            $amount = 0;
            if ($sale->p_collection_amount != null) {
                $amount = $sale->p_collection_amount;
            } else if ($sale->d_collection_amount != null) {
                $amount = $sale->d_collection_amount;
            } else {
                $amount = $sale->s_collection_amount;
            }
            $collection_amount = number_format($amount);
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
            $gst = number_format((float)$gst, 2);
            $total = 0;
            if ($sale->p_total_charges != null) {
                $total = $sale->p_total_charges;
            } else if ($sale->d_total_charges != null) {
                $total = $sale->d_total_charges;
            }
            $total_charges = number_format((float)$total, 2);
            $estimated = 0;
            $estimated = (($sale->weight_charges != null) ? $sale->weight_charges : 0) + (($sale->cash_handling_charges != null) ? $sale->cash_handling_charges : 0) + (($sale->insurance_charges != null) ? $sale->insurance_charges : 0) + (($sale->insurance_charges != null) ? $sale->insurance_charges : 0) + (($sale->return_charges != null) ? $sale->return_charges : 0) + (($sale->replacement_charges != null) ? $sale->replacement_charges : 0) + (($sale->fuel_surcharge != null) ? $sale->fuel_surcharge : 0) + (($sale->try_and_buy_charges != null) ? $sale->try_and_buy_charges : 0) + (($sale->packaging_material_charges != null) ? $sale->packaging_material_charges : 0) + (($sale->intercept_charges != null) ? $sale->intercept_charges : 0);
            $estimated_charges = number_format((float)$estimated, 2);
            $payable = '';
            if ($sale->p_net_payable != null) {
                $payable = $sale->p_net_payable;
            } else if ($sale->d_net_payable != null) {
                $payable = $sale->d_net_payable;
            }
            $net_payable = number_format((float)$payable, 2);
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
        $spreadsheet->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('S')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('T')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
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

        $spreadsheet->getActiveSheet()->getStyle('A1:AK1')->getFont()->setBold(TRUE);

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        ob_start();
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
            $to = Carbon::today()->subMonth(1)->endOfMonth()->toDateTimeString();
        }
        if($report_type == 2){
            $from = Carbon::today()->firstOfMonth()->toDateTimeString();
            $to = Carbon::parse($from)->addDays(24)->endOfDay()->toDateTimeString();
        }
        if($report_type == 3){
            $from = Carbon::today()->subMonth(1)->firstOfMonth()->addDays(25)->toDateTimeString();
            $to = Carbon::today()->subMonth(1)->endOfMonth()->toDateTimeString();
        }

        $from_id = DB::table('shipments_journey')->select(DB::raw('MIN(id) as id'))->where('created_at', '>=', $from)->first()->id;
        $to_id = DB::table('shipments_journey')->select(DB::raw('MAX(id) as id'))->where('created_at', '>=', $from)->where('created_at', '<=', $to)->first()->id;

        $sales = DB::connection('reports')->table('shipments_journey as sj')
            ->join('shipments', 'shipments.id', 'sj.shipment_id')
            ->leftJoin('users as u', 'u.id', '=', 'shipments.user_id')
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
            ->leftJoin('pending_payment_shipments as pps', function ($join) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.id', '=',
                        DB::connection('reports')->raw('(select max(id) from pending_payment_shipments where pending_payment_shipments.shipment_id = shipments.id and pending_payment_shipments.type != 2)'));
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.id', '=',
                        DB::connection('reports')->raw('(select max(id) from done_payment_shipments where done_payment_shipments.shipment_id = shipments.id and done_payment_shipments.type != 2)'));
            })
            ->leftJoin('pending_invoice_shipments as pis', function ($join) {
                $join->on('pis.shipment_id', '=', 'shipments.id')
                    ->where('pis.id', '=',
                        DB::connection('reports')->raw('(select max(id) from pending_invoice_shipments where pending_invoice_shipments.shipment_id = shipments.id and pending_invoice_shipments.type != 2)'));
            })
            ->leftJoin('invoice_shipments as is', function ($join) {
                $join->on('is.shipment_id', '=', 'shipments.id')
                    ->where('is.id', '=',
                        DB::connection('reports')->raw('(select max(id) from invoice_shipments where invoice_shipments.shipment_id = shipments.id and invoice_shipments.type != 2)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37) and shipments_journey.verification = 1)'));
            })
            ->select('shipments.tracking_number', 'u.id as account_no', 'bc.name as buisness_category', 'u.name as shipper', 'shipments.order_id as order_id', 'ss.name as current_status', 'sps.name as payment_status', 'dps.done_payment_id as payment_number', 'dnsdn.station_deposit_note_id as sdn_number', 'bt.booking_type as service_type', 'sj.created_at as arrival_date', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'z.name as zone', 'zcc.class', 'sm.mode as shipping_mode', 'pps.amount as p_collection_amount', 'shipments.actual_weight', 'shipments.chargeable_weight', 'shipments.weight_charges', 'shipments.cash_handling_charges', 'shipments.insurance_charges', 'shipments.packaging_material_charges', 'shipments.fuel_surcharge', 'shipments.return_charges', 'shipments.replacement_charges', 'shipments.packaging_charges', 'shipments.try_and_buy_charges', 'shipments.nsa_osa_charges', 'shipments.intercept_charges', 'pps.gst as p_gst', 'pps.charges as p_total_charges', 'pps.payable as p_net_payable', 'shipments.amount as s_collection_amount', 'dps.amount as d_collection_amount', 'dps.gst as d_gst', 'dps.charges as d_total_charges', 'dps.payable as d_net_payable', 'dr.created_at as delivered_or_returned', 'oc.id as origin_city_id', 'dc.id as destination_city_id', 'shipments.booking_type_id', 'usi.poc', 'shipments.shipper_status_id as shipment_status', 'u.account_type_id as account_type_id', 'pis.gst as pis_gst', 'is.gst as is_gst', 'dr.shipper_status_id as dr_status_id', 'shipments.shipment_type')
            ->whereNotIn('shipments.shipper_status_id', [1, 17])
            ->whereNotIn('u.id', [8761, 9358])
            ->whereBetween('sj.created_at', [$from, $to])
            ->where('sj.id', '>=', $from_id)
            ->where('sj.id', '<=', $to_id)
            ->get();



        if($report_type == 1){
            $filename = 'revenue_report_by_arrival_first_to_last.xlsx';
        }
        if($report_type == 2){
            $filename = 'revenue_report_by_arrival_first_to_25.xlsx';
        }
        if($report_type == 3){
            $filename = 'revenue_report_by_arrival_26_to_last.xlsx';
        }

        $details = array();

        $details[] = ['S.No.', 'Tracking Number', 'Account No.', 'Business Category', 'Shipper', 'Order Id', 'Status', 'Payment Status', 'Payment Number', 'SDN Number', 'Service Type', 'Arrival Date', 'Origin', 'Destination', 'Hub', 'Zone', 'Class', 'Shipping Mode', 'Collection Amount', 'Actual Weight', 'Chargeable Weight', 'Weight Charges', 'Cash Handling Charges', 'Insurance Charges', 'Packaging Charges', 'Fuel Surcharge', 'Return Charges', 'Replacement Charges', 'Packing Charges', 'Try & Buy Charges', 'NSA/OSA Charges', 'Intercept Charges', 'GST', 'Total Charges', 'Estimated Charges', 'Net Payable', 'Delivered/Returned Date'];

        $serial_number = 1;
        foreach ($sales as $sale) {
            if ($sale->dr_status_id == 20) {
                $cash_handling_charges = "-";
                $return_charges = number_format($sale->return_charges, 2);
                $replacement_charges = "-";
                $try_and_buy_charges = "-";
            } else {
                if ($sale->cash_handling_charges != null) {
                    $cash_handling_charges = number_format($sale->cash_handling_charges, 2);
                } else {
                    $cash_handling_charges = "-";
                }
                $return_charges = "-";
                $replacement_charges = number_format($sale->replacement_charges, 2);
                $try_and_buy_charges = number_format($sale->try_and_buy_charges, 2);
            }

            $insurance_charges = number_format($sale->insurance_charges, 2);
            $account_number = str_pad($sale->account_no, 6, '0', STR_PAD_LEFT);
            $intercept_charges = number_format($sale->intercept_charges, 2);
            $weight_charges = number_format($sale->weight_charges, 2);
            $fuel_surcharge = number_format($sale->fuel_surcharge, 2);
            $nsa_osa_charges = number_format($sale->nsa_osa_charges, 2);
            $packaging_material_charges = number_format($sale->packaging_material_charges, 2);

            $packaging_charges = number_format($sale->packaging_charges, 2);

            if ($sale->booking_type_id == 4) {
                $shipper = $sale->shipper . ' (' . $sale->poc . ')';
            } else {
                $shipper = $sale->shipper;
            }
            $amount = 0;
            if ($sale->p_collection_amount != null) {
                $amount = $sale->p_collection_amount;
            } else if ($sale->d_collection_amount != null) {
                $amount = $sale->d_collection_amount;
            } else {
                $amount = $sale->s_collection_amount;
            }
            $collection_amount = number_format($amount);
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
            $gst = number_format((float)$gst, 2);
            $total = 0;
            if ($sale->p_total_charges != null) {
                $total = $sale->p_total_charges;
            } else if ($sale->d_total_charges != null) {
                $total = $sale->d_total_charges;
            }
            $total_charges = number_format((float)$total, 2);
            $estimated = 0;
            $estimated = (($sale->weight_charges != null) ? $sale->weight_charges : 0) + (($sale->cash_handling_charges != null) ? $sale->cash_handling_charges : 0) + (($sale->insurance_charges != null) ? $sale->insurance_charges : 0) + (($sale->insurance_charges != null) ? $sale->insurance_charges : 0) + (($sale->return_charges != null) ? $sale->return_charges : 0) + (($sale->replacement_charges != null) ? $sale->replacement_charges : 0) + (($sale->fuel_surcharge != null) ? $sale->fuel_surcharge : 0) + (($sale->try_and_buy_charges != null) ? $sale->try_and_buy_charges : 0) + (($sale->packaging_material_charges != null) ? $sale->packaging_material_charges : 0) + (($sale->intercept_charges != null) ? $sale->intercept_charges : 0);
            $estimated_charges = number_format((float)$estimated, 2);
            $payable = '';
            if ($sale->p_net_payable != null) {
                $payable = $sale->p_net_payable;
            } else if ($sale->d_net_payable != null) {
                $payable = $sale->d_net_payable;
            }
            $net_payable = number_format((float)$payable, 2);
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
        $spreadsheet->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('S')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('T')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
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

        $spreadsheet->getActiveSheet()->getStyle('A1:AK1')->getFont()->setBold(TRUE);

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        ob_start();
        $writer->save('php://output');
        $contents = ob_get_contents();
        ob_end_clean();
        $filePath = '/reports/revenue/' . $filename;
        Storage::disk('public')->put($filePath, $contents);
        $from = Carbon::parse($from)->toDateString();
        $to = Carbon::parse($to)->toDateString();
        return ['file_path' => $filePath, 'from' => $from, 'to' => $to];
    }
}
