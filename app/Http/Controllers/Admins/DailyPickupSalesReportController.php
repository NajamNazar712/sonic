<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\ShippingMode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DailyPickupSalesReportController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    static public function daily_pickup_sales_report_overall($date){
        $date_from = Carbon::createFromFormat("Y-m-d H:i:s",$date)->format('Y-m-d 06:00A');
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s",$next_day)->format('Y-m-d 05:59A');
        $only_date = Carbon::parse($date)->toDateString();

        $search_city_hub = '';
        $hub_ids = DB::connection('reports')->table('shipments_journey')
            ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
            ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
            ->where('s.packaging_material_request', 0)
            ->where('s.user_id', '!=', 1690)
            ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
            ->whereIn('shipments_journey.shipper_status_id', [1, 2])
            ->distinct()
            ->pluck('usi.city_id');
        $hubs = DB::connection('reports')->table('cities')->whereIn('id', $hub_ids)->select('id', 'name')->get();

        $details = array();
        $sorted_details_array = array();
        $shipping_wise_details = array();
        $sorted_shipper_array = array();
        $details_shipper = array();
        $shippers = array();
        $details['header'] = ['S. No.','Origin '.$only_date, 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Avg/Parcel Revenue','Actual Weight','Avg. Actual Weight/Parcel','Avg. Revenue On Actual Weight','Chargeable Weight','Avg. Chargeable Weight/Parcel','Avg. Revenue On Chargeable Weight','Collection Amount','Avg. Amount Collection','% Rev. on Amount Collection'];

        $sort_support_array = array();
        $total_booked = 0;
        $total_received = 0;
        $total_cod_collection = 0;
        $total_actual_weight = 0;
        $total_avg_actual_weight = 0;
        $total_avg_rev_actual_weight = 0;
        $total_chargeable_weight = 0;
        $total_avg_chargeable_weight = 0;
        $total_avg_rev_chargeable_weight = 0;
        $total_revenue_wo_gst = 0;
        $total_avg_revenue = 0;
        $total_avg_cash_collection = 0;
        $total_rev_on_cash_collection = 0;
        $serial_number_hubs = 0;
        $booked = 0;
        $received = 0;
        $revenue_wo_gst = 0;
        $cod_collection = 0;
        $actual_weight = 0;
        $chargeable_weight = 0;

        foreach ($hubs as $hub) {

            $booked = DB::connection('reports')->table('shipments')
            ->join('user_shipping_infos as usi', 'usi.id', 'shipments.pickup_address_id')
            ->where('shipments.packaging_material_request', 0)
            ->where('shipments.user_id', '!=', 1690)
            ->whereBetween('shipments.created_at', [$date_from, $date_to])
            ->where('usi.city_id', $hub->id)
            ->count();

            $received = DB::connection('reports')->table('shipments_journey')
            ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
            ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
            ->where('s.packaging_material_request', 0)
            ->where('s.user_id', '!=', 1690)
            ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
            ->where('shipments_journey.shipper_status_id', 2)
            ->where('usi.city_id', $hub->id)
            ->count();

            if($received > 0){
                $shipments_data = array();

                $shipments_data = DB::connection('reports')->table('shipments_journey')
                ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                ->where('s.packaging_material_request', 0)
                ->where('s.user_id', '!=', 1690)
                ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                ->where('shipments_journey.shipper_status_id', 2)
                ->where('usi.city_id', $hub->id)
                ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                $cod_collection = $shipments_data->cod_collection;
                $actual_weight = $shipments_data->actual_weight;
                $chargeable_weight = $shipments_data->chargeable_weight;
                $row = array();
                $avg_revenue = ($received != 0)? $revenue_wo_gst/$received:0;
                $avg_cash_collection = ($received != 0)? $cod_collection/$received:0;
                $rev_on_cash_collection = (($avg_cash_collection != 0)? $avg_revenue/$avg_cash_collection:0)*100;
                //changes add columns
                $avg_actual_weight = ($received != 0)? $actual_weight/$received:0;
                $avg_rev_actual_weight = ($actual_weight != 0 && $revenue_wo_gst != 0)? $revenue_wo_gst/$actual_weight:0;
                $avg_chargeable_weight = ($received != 0)? $chargeable_weight/$received:0;
                $avg_rev_chargeable_weight = ($chargeable_weight != 0 && $revenue_wo_gst != 0)? $revenue_wo_gst/$chargeable_weight:0;

                //end changes
                $row['serials'] = $serial_number_hubs;
                $row['hub'] = $hub->name;
                $row['booked'] = number_format($booked);
                $row['received'] = number_format($received);
                $row['revenue_wo_gst'] = number_format($revenue_wo_gst);
                $avg_rev = round($avg_revenue);
                $row['average_revenue'] = number_format($avg_rev);
                $row['actual_weight'] =number_format($actual_weight);
                $string_avg_actual_weight = (string) $avg_actual_weight;
                $row['avg_actual_weight'] = number_format((float)$string_avg_actual_weight,2,'.','');
                $row['avg_rev_actual_weight'] = round($avg_rev_actual_weight);

                $row['chargeable_weight'] = number_format($chargeable_weight);
                $string_avg_chargeable_weight = (string) $avg_chargeable_weight;
                $row['avg_chargeable_weight'] = number_format((float)$string_avg_chargeable_weight,2,'.','');
                $row['avg_rev_chargeable_weight'] = round($avg_rev_chargeable_weight);
                $row['cod_collection'] = number_format($cod_collection);

                $avg_cc = round($avg_cash_collection);
                $row['average_cash_collection'] = number_format($avg_cc);
                $rev_occ =   round($rev_on_cash_collection);
                $row['revenue_cash_collection'] = number_format($rev_occ).'%';
                $sort_support_array[] = $received;

                $sorted_details_array[] = $row;
                $total_booked += $booked;
                $total_received += $received;
                $total_revenue_wo_gst += $revenue_wo_gst;
                $total_cod_collection += $cod_collection;
                $total_actual_weight += $actual_weight;

                $total_chargeable_weight += $chargeable_weight;


            }

        }

        $total_avg_revenue = ($total_received != 0) ? $total_revenue_wo_gst / $total_received:0;
        $total_avg_actual_weight = ($total_received != 0) ? $total_actual_weight / $total_received:0;
        $total_avg_rev_actual_weight = ($total_actual_weight != 0) ? $total_revenue_wo_gst / $total_actual_weight:0;
        $total_avg_chargeable_weight = ($total_received != 0) ? $total_chargeable_weight / $total_received:0;
        $total_avg_rev_chargeable_weight = ($total_chargeable_weight != 0) ? $total_revenue_wo_gst / $total_chargeable_weight:0;
        $total_avg_cash_collection = ($total_received != 0) ? $total_cod_collection / $total_received:0;
        $total_rev_on_cash_collection = ($total_cod_collection != 0) ? $total_revenue_wo_gst / $total_cod_collection:0;
        $total_rev_on_cash_collection = $total_rev_on_cash_collection * 100;
        array_multisort($sort_support_array, SORT_DESC, $sorted_details_array);

        $counter = 1;
        foreach ($sorted_details_array as $item) {
            $item['serials'] = $counter;
            $details[] = $item;
            $counter++;
        }

        $details[] = ['Grand Total','Origin '.$only_date, number_format($total_booked), number_format($total_received), number_format($total_revenue_wo_gst),number_format($total_avg_revenue),number_format($total_actual_weight),number_format((float) $total_avg_actual_weight,2,'.',''),round($total_avg_rev_actual_weight),number_format($total_chargeable_weight),number_format((float) $total_avg_chargeable_weight,2,'.',''), round($total_avg_rev_chargeable_weight), number_format($total_cod_collection),number_format($total_avg_cash_collection),number_format($total_rev_on_cash_collection).'%'];

        $details_shipper['header'] = ['S. No.','Origin','Sales Person','Shipper Name(s) (Account No(s))', 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Avg/Parcel Revenue','Actual Weight','Avg. Actual Weight/Parcel','Avg. Revenue On Actual Weight','Chargeable Weight','Avg. Chargeable Weight/Parcel','Avg. Revenue On Chargeable Weight','Collection Amount','Avg. Amount Collection','% Rev. on Amount Collection'];

        $serial_number_shippers = 0;

        foreach($hubs as $hub){

            $pickup_request_shippers = DB::connection('reports')->table('shipments_journey')
            ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
            ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
            ->where('s.packaging_material_request', 0)
            ->where('s.user_id', '!=', 1690)
            ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
            ->where('shipments_journey.shipper_status_id', 2)
            ->where('usi.city_id', $hub->id);

            if($pickup_request_shippers->exists()) {
                $pickup_request_shippers_ids = $pickup_request_shippers->pluck('s.user_id')->toArray();

                $pickup_request_shippers_ids = array_unique($pickup_request_shippers_ids);

                $shippers[$hub->id] = DB::connection('reports')->table('users')->select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->get();
            }

        }

        $shipper_sort_support_array = array();
        $total_shipper_booked = 0;
        $total_shipper_received = 0;
        $total_shipper_cod_collection = 0;
        $total_shipper_actual_weight = 0;
        $total_shipper_avg_actual_weight = 0;
        $total_shipper_avg_rev_actual_weight = 0;
        $total_shipper_chargeable_weight = 0;
        $total_shipper_avg_chargeable_weight = 0;
        $total_shipper_avg_rev_chargeable_weight = 0;
        $total_shipper_revenue_wo_gst = 0;
        $total_shipper_avg_revenue = 0;
        $total_shipper_avg_cash_collection = 0;
        $total_shipper_rev_on_cash_collection = 0;

        if(count($shippers) > 0) {

            foreach ($shippers as $origin => $shipper_row) {
                foreach ($shipper_row as $shipper) {

                    $shipper_booked = 0;
                    $shipper_received = 0;
                    $shipper_rev_wo_gst = 0;
                    $shipper_cod = 0;
                    $shipper_actual_weight = 0;
                    $shipper_chargeable_weight = 0;

                    $origin_name =  DB::connection('reports')->table('cities')->where('id', $origin)->select('name')->first();
                    $origin_name = $origin_name->name;


                    $shipper_sales_person_name = '';
                    $shipper_sales_person = SalePersonTag::where('user_id', $shipper->id)->where('status', 0);
                    if ($shipper_sales_person->exists()) {
                        $shipper_sales_person = $shipper_sales_person->first();
                        $shipper_sales_person_name = Admin::find($shipper_sales_person->admin_id)->name;
                    }

                    $shipper_booked = DB::connection('reports')->table('shipments')
                    ->join('user_shipping_infos as usi', 'usi.id', 'shipments.pickup_address_id')
                    ->where('shipments.packaging_material_request', 0)
                    ->whereBetween('shipments.created_at', [$date_from, $date_to])
                    ->where('usi.city_id', $origin)
                    ->where('shipments.user_id', $shipper->id)
                    ->count();

                    $shipper_received = DB::connection('reports')->table('shipments_journey')
                    ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                    ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                    ->where('s.packaging_material_request', 0)
                    ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                    ->where('shipments_journey.shipper_status_id', 2)
                    ->where('usi.city_id', $origin)
                    ->where('s.user_id', $shipper->id)
                    ->count();

                    if($shipper_received > 0){
                        $shipment_data = array();

                        $shipment_data = DB::connection('reports')->table('shipments_journey')
                        ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                        ->join('user_shipping_infos as usi', 'usi.id', 's.pickup_address_id')
                        ->where('s.packaging_material_request', 0)
                        ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                        ->where('shipments_journey.shipper_status_id', 2)
                        ->where('usi.city_id', $origin)
                        ->where('s.user_id', $shipper->id)
                        ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as shipper_cod'), DB::connection('reports')->raw('SUM(actual_weight) as shipper_actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as shipper_chargeable_weight'))->first();

                        $shipper_rev_wo_gst = $shipment_data->revenue_wo_gst;
                        $shipper_cod = $shipment_data->shipper_cod;
                        $shipper_actual_weight = $shipment_data->shipper_actual_weight;
                        $shipper_chargeable_weight = $shipment_data->shipper_chargeable_weight;
                    }



                    $shipper_avg_revenue = ($shipper_received != 0) ? $shipper_rev_wo_gst / $shipper_received : 0;
                    $shipper_avg_cash_collection = ($shipper_received != 0) ? $shipper_cod / $shipper_received : 0;
                    $shipper_rev_on_cash_collection = (($shipper_avg_cash_collection != 0) ? $shipper_avg_revenue / $shipper_avg_cash_collection : 0) * 100;

                    //changes add columns
                    $shipper_avg_actual_weight = ($shipper_received != 0) ? $shipper_actual_weight / $shipper_received : 0;
                    $shipper_avg_rev_actual_weight = ($shipper_actual_weight != 0 && $shipper_rev_wo_gst != 0) ? $shipper_rev_wo_gst / $shipper_actual_weight : 0;
                    $shipper_avg_chargeable_weight = ($shipper_received != 0) ? $shipper_chargeable_weight / $shipper_received : 0;
                    $shipper_avg_rev_chargeable_weight = ($shipper_chargeable_weight != 0 && $shipper_rev_wo_gst != 0) ? $shipper_rev_wo_gst / $shipper_chargeable_weight : 0;

                    //end
                    $shipper_row = array();
                    $shipper_row['shipper_serial'] = $serial_number_shippers;
                    $shipper_row['origin_name'] = $origin_name;
                    $shipper_row['shipper_sale_person'] = $shipper_sales_person_name;
                    $shipper_row['name'] = $shipper->name . ' (' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . ')';
                    $shipper_row['shipper_booked'] = number_format($shipper_booked);
                    $shipper_row['shipper_received'] = number_format($shipper_received);
                    $shipper_row['shipper_rev_wo_gst'] = number_format($shipper_rev_wo_gst);
                    $s_avg_revenue = round($shipper_avg_revenue);
                    $shipper_row['shipper_avg_revenue'] = number_format($s_avg_revenue);
                    $shipper_row['shipper_actual_weight'] = number_format($shipper_actual_weight);
                    $shipper_row['shipper_avg_actual_weight'] = number_format((float)$shipper_avg_actual_weight, 2, '.', '');
                    $shipper_row['$shipper_avg_rev_actual_weight'] = round($shipper_avg_rev_actual_weight);
                    $shipper_row['shipper_chargeable_weight'] = number_format($shipper_chargeable_weight);
                    $shipper_row['shipper_avg_chargeable_weight'] = number_format((float)$shipper_avg_chargeable_weight, 2, '.', '');
                    $shipper_row['shipper_avg_rev_chargeable_weight'] = round($shipper_avg_rev_chargeable_weight);
                    $shipper_row['shipper_cod'] = number_format($shipper_cod);

                    $avg_cash_coll = round($shipper_avg_cash_collection);
                    $shipper_row['shipper_avg_cc'] = number_format($avg_cash_coll);
                    $avg_rev_cc = round($shipper_rev_on_cash_collection);
                    $shipper_row['shipper_rcc'] = number_format($avg_rev_cc) . '%';

                    $sorted_shipper_array[] = $shipper_row;
                    $shipper_sort_support_array[] = $shipper_received;
                    $total_shipper_booked += $shipper_booked;
                    $total_shipper_received += $shipper_received;
                    $total_shipper_cod_collection += $shipper_cod;
                    $total_shipper_revenue_wo_gst += $shipper_rev_wo_gst;
                    $total_shipper_actual_weight += $shipper_actual_weight;

                    $total_shipper_chargeable_weight += $shipper_chargeable_weight;


                }
            }
        }

        $total_shipper_avg_revenue = ($total_shipper_received != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_received:0;
        $total_shipper_avg_actual_weight = ($total_shipper_received != 0) ? $total_shipper_actual_weight / $total_shipper_received:0;
        $total_shipper_avg_rev_actual_weight = ($total_shipper_actual_weight != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_actual_weight:0;
        $total_shipper_avg_chargeable_weight = ($total_shipper_received != 0) ? $total_shipper_chargeable_weight / $total_shipper_received:0;
        $total_shipper_avg_rev_chargeable_weight = ($total_shipper_chargeable_weight != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_chargeable_weight:0;
        $total_shipper_avg_cash_collection = ($total_shipper_received != 0) ? $total_shipper_cod_collection / $total_shipper_received:0;

        $total_shipper_rev_on_cash_collection = ($total_shipper_cod_collection != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_cod_collection:0;
        $total_shipper_rev_on_cash_collection = $total_shipper_rev_on_cash_collection * 100;
        array_multisort($shipper_sort_support_array, SORT_DESC, $sorted_shipper_array);
        $shipper_counter = 1;
        foreach ($sorted_shipper_array as $shipper) {
            $shipper['shipper_serial'] = $shipper_counter;
            $details_shipper[] = $shipper;
            $shipper_counter++;
        }
        $details_shipper[] = ['Grand Total','','','DSR '.$only_date, number_format($total_shipper_booked), number_format($total_shipper_received), number_format($total_shipper_revenue_wo_gst),number_format($total_shipper_avg_revenue),number_format($total_shipper_actual_weight),number_format((float) $total_shipper_avg_actual_weight,2,'.',''),round($total_shipper_avg_rev_actual_weight),number_format($total_shipper_chargeable_weight),number_format((float) $total_shipper_avg_chargeable_weight,2,'.',''),round($total_shipper_avg_rev_chargeable_weight), number_format($total_shipper_cod_collection),number_format($total_shipper_avg_cash_collection),number_format($total_shipper_rev_on_cash_collection).'%'];
        //shipping_mode_wise
        $shipping_mode_wise_header['header'] = ['S. No.','Shipping Mode', 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Avg/Parcel Revenue','Actual Weight','Avg Actual Weight/Parcel','Avg Revenue on Actual Weight','Chargeable Weight','Avg. Chargeable Weight/Parcel','Avg. Revenue On Chargeable Weight','Collection Amount','Avg. Amount Collection','% Rev. on Amount Collection'];

        $shipping_mode_wise_details = self::daily_pickup_sales_shipping_mode_wise($date_from,$date_to, FALSE, NULL);


        $total_shipping_booked = 0;
        $total_shipping_received = 0;
        $total_shipping_revenue_wo_gst = 0;
        $total_shipping_avg_parcel_revenue = 0;
        $total_shipping_actual_weight = 0;
        $total_shipping_avg_actual_weight = 0;
        $total_shipping_avg_rev_actual_weight = 0;
        $total_shipping_chargeable_weight = 0;
        $total_shipping_avg_chargeable_weight = 0;
        $total_shipping_avg_rev_chargeable_weight = 0;
        $total_shipping_collection_amount = 0;
        $total_shipping_avg_amount_collection = 0;
        $total_shipping_rev_amount_collection = 0;
        $total_avg_revenue = 0;
        $total_avg_actual_weight = 0;
        $total_avg_rev_actual_weight = 0;
        $total_avg_chargeable_weight = 0;
        $shipping_mode_wise_data = array();
        foreach($shipping_mode_wise_details as $shipping_mode_total){
            $total_shipping_booked += $shipping_mode_total['booked'];
            $total_shipping_received += $shipping_mode_total['received'];
            $total_shipping_revenue_wo_gst += $shipping_mode_total['revenue_wo_gst'];
            $total_shipping_avg_parcel_revenue  += $shipping_mode_total['avg_parcel_rev'];
            $total_shipping_actual_weight += $shipping_mode_total['actual_weight'];
            $total_shipping_avg_actual_weight += $shipping_mode_total['avg_actual_weight'];
            $total_shipping_avg_rev_actual_weight += $shipping_mode_total['avg_rev_actual_weight'];
            $total_shipping_chargeable_weight += $shipping_mode_total['chargeable_weight'];
            $total_shipping_avg_chargeable_weight += $shipping_mode_total['avg_chargeable_weight'];
            $total_shipping_avg_rev_chargeable_weight += $shipping_mode_total['avg_rev_chargeable_weight'];
            $total_shipping_collection_amount += $shipping_mode_total['collection_amount'];
            $total_shipping_avg_amount_collection += $shipping_mode_total['avg_amount_collection'];
            $total_shipping_rev_amount_collection += $shipping_mode_total['revenue_amount_collection'];

            $shipping_mode_wise_data[] = [$shipping_mode_total['serial'],$shipping_mode_total['mode'],number_format(round($shipping_mode_total['booked'])),$shipping_mode_total['received'],number_format($shipping_mode_total['revenue_wo_gst']),number_format($shipping_mode_total['avg_parcel_rev']) ,number_format($shipping_mode_total['actual_weight']),number_format($shipping_mode_total['avg_actual_weight']),round($shipping_mode_total['avg_rev_actual_weight']),number_format($shipping_mode_total['chargeable_weight']),number_format($shipping_mode_total['avg_chargeable_weight']),round($shipping_mode_total['avg_rev_chargeable_weight']),$shipping_mode_total['collection_amount'],$shipping_mode_total['avg_amount_collection'],round( $shipping_mode_total['revenue_amount_collection']) .'%'];
        }

        $total_shipping_avg_parcel_revenue = ($total_shipping_received != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_received:0;
        $total_avg_actual_weight = ($total_shipping_received != 0) ? $total_shipping_actual_weight / $total_shipping_received:0;
        $total_avg_rev_actual_weight = ($total_shipping_actual_weight != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_actual_weight:0;
        $total_avg_chargeable_weight = ($total_shipping_received != 0) ? $total_shipping_chargeable_weight / $total_shipping_received:0;
        $total_avg_rev_chargeable_weight = ($total_shipping_chargeable_weight != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_chargeable_weight:0;
        $total_avg_cash_collection = ($total_shipping_received != 0) ? $total_shipping_collection_amount / $total_shipping_received:0;
        $total_rev_on_cash_collection = ($total_shipping_collection_amount != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_collection_amount:0;
        $total_rev_on_cash_collection = $total_rev_on_cash_collection * 100;

        $shipping_mode_wise_footer[] = ['Grand Total.',' ',number_format(round($total_shipping_booked)),$total_shipping_received,number_format(round($total_shipping_revenue_wo_gst)),number_format($total_shipping_avg_parcel_revenue) ,number_format($total_shipping_actual_weight),number_format($total_avg_actual_weight),number_format($total_avg_rev_actual_weight),number_format($total_shipping_chargeable_weight),number_format($total_avg_chargeable_weight),number_format($total_avg_rev_chargeable_weight),$total_shipping_collection_amount,number_format($total_avg_cash_collection),number_format($total_rev_on_cash_collection) .'%'];

        $shipping_mode_wise_details = array_merge($shipping_mode_wise_header,$shipping_mode_wise_data,$shipping_mode_wise_footer);

        $spreadsheet = new Spreadsheet();
        $cell_st =[
            'font' =>['bold' => true],
            'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                )
            ),
        ];
        $total_cell_st =[
            'font' =>['bold' => true],
            'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            )
        ];
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getDefaultColumnDimension()->setWidth(20);
        $sheet->getStyle('D3:R3')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
        $sheet->getStyle('D3:R3')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D3:R3')->applyFromArray($cell_st);
        $sheet->fromArray($shipping_mode_wise_details,NULL,'D3',true);
        $sheet->getStyle('D8:R8')->applyFromArray($total_cell_st);
        $count_shipping_mode = count($shipping_mode_wise_details);
        $count_shipping_mode += 7;
        //details
        $total_style_cell = "D$count_shipping_mode".":R".$count_shipping_mode;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->getStyle($total_style_cell)->getAlignment()->setWrapText(true);
        $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);
        $sheet->getStyle($total_style_cell)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
        $shipper_cell = 'D'.$count_shipping_mode; //D13
//        $shipper_last_cell = 'R'.$count_shipping_mode; //R13
        $sheet->fromArray($details,NULL,$shipper_cell,true);
        $count_details = count($details);
        $total_hub = $count_shipping_mode + $count_details-1;
        $total_style_cell = "D$total_hub".":R".$total_hub;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $count_details = $count_details + $count_shipping_mode + 4;

        //shipper_details
        $total_style_cell = "D$count_details".":T".$count_details;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);
        $sheet->getStyle($total_style_cell)->getAlignment()->setWrapText(true);


        $sheet->getStyle($total_style_cell)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
        $details_shipper_cell = 'D'.$count_details;
        $details_shipper_last_cell = 'R'.$count_details;
        // $sheet->fromArray($details_shipper,NULL,'D15',true);
        $sheet->fromArray($details_shipper,NULL,$details_shipper_cell,true);
        $total_shipper_count = count($details_shipper);
        $total_shipper = $total_shipper_count + $count_details -1;
        $total_style_cell = "D$total_shipper".":T".$total_shipper;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->setTitle('Daily Pickup Sales Report');


        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');
        $city_name = '';
        $file_name_without_path = '';

        $file_name_without_path = "reports/daily_pickup_sales_report_".$date_file_name.'_'.$time_string.".xlsx";
        $file_name = public_path() . "/reports/daily_pickup_sales_report_".$date_file_name.'_'.$time_string.".xlsx";

        $writer->save($file_name);

        return url('/').'/'.$file_name_without_path;

    }

    static public function daily_pickup_sales_report_individual($date, $sale_person_id)
    {
        $date_from = Carbon::createFromFormat("Y-m-d H:i:s", $date)->format('Y-m-d 06:00A');
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s", $next_day)->format('Y-m-d 05:59A');
        $only_date = Carbon::parse($date)->toDateString();
        $hubs = array();
        $city = array();

        $hubs = DB::connection('reports')->table('cities')->where('pickup', 1)->select('id', 'name')->get();

        $details = array();
        $shipping_wise_details = array();
        $sorted_details_array = array();
        $sorted_shipper_array = array();
        $details_shipper = array();
        $shippers = array();

        $details['header'] = ['S. No.', 'Origin ' . $only_date, 'No. of Parcels Booked', 'No of Parcels Received', 'Revenue without GST', 'Avg/Parcel Revenue', 'Actual Weight', 'Avg. Actual Weight/Parcel', 'Avg. Revenue On Actual Weight', 'Chargeable Weight', 'Avg. Chargeable Weight/Parcel', 'Avg. Revenue On Chargeable Weight', 'Collection Amount', 'Avg. Amount Collection', '% Rev. on Amount Collection'];

        $sort_support_array = array();
        $total_booked = 0;
        $total_received = 0;
        $total_cod_collection = 0;
        $total_actual_weight = 0;
        $total_avg_actual_weight = 0;
        $total_avg_rev_actual_weight = 0;
        $total_chargeable_weight = 0;
        $total_avg_chargeable_weight = 0;
        $total_avg_rev_chargeable_weight = 0;
        $total_revenue_wo_gst = 0;
        $total_avg_revenue = 0;
        $total_avg_cash_collection = 0;
        $total_rev_on_cash_collection = 0;
        $serial_number_hubs = 0;
        $booked = 0;
        $received = 0;
        $revenue_wo_gst = 0;
        $cod_collection = 0;
        $actual_weight = 0;
        $chargeable_weight = 0;

        $tagged_shippers = DB::connection('reports')->table('sale_person_tags')->where('admin_id', $sale_person_id)->where('status', 0)->select('user_id')->pluck('user_id')->toArray();

        $assigned_admins = DB::connection('reports')->table('multiple_sale_leads')->leftjoin('multiple_sale_taggings as mst', 'mst.lead_id', '=', 'multiple_sale_leads.id')
            ->leftjoin('sale_person_tags as spt', 'spt.admin_id', '=', 'mst.admin_id')
            ->where('multiple_sale_leads.admin_id', $sale_person_id)
            ->where('spt.status', 0)
            ->whereNotNull('spt.user_id')->select('spt.user_id')->pluck('spt.user_id')->toArray();
        if ($assigned_admins) {
            $tagged_shippers = array_merge($tagged_shippers, $assigned_admins);
        }
        foreach ($hubs as $hub) {

            $booked = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                    });
            })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->whereBetween('created_at',[$date_from,$date_to])->whereIn('shipments.user_id', $tagged_shippers)->select('shipments.id')->get()->count();
            $received = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                    });
            })->whereExists(function ($query) use ($date_from, $date_to) {
                $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$date_from, $date_to])
                    ->where('shipper_status_id', 2);
            })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->whereIn('shipments.user_id', $tagged_shippers)->select('shipments.id')->get()->count();
            if($booked > 0 || $received > 0){
                $shipments_data = array();
                $shipments_data = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                    $query->from('user_shipping_infos')
                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                        ->whereExists(function($sub_query) use ($hub) {
                            $sub_query->from('cities')
                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                ->where('cities.id', $hub->id);
                        });
                })->whereExists(function ($query) use ($date_from, $date_to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$date_from, $date_to])
                        ->where('shipper_status_id', 2);
                })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->whereIn('shipments.user_id', $tagged_shippers)->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                $cod_collection = $shipments_data->cod_collection;
                $actual_weight = $shipments_data->actual_weight;
                $chargeable_weight = $shipments_data->chargeable_weight;
                $row = array();
                $avg_revenue = ($received != 0)? $revenue_wo_gst/$received:0;
                $avg_cash_collection = ($received != 0)? $cod_collection/$received:0;
                $rev_on_cash_collection = (($avg_cash_collection != 0)? $avg_revenue/$avg_cash_collection:0)*100;
                //changes add columns
                $avg_actual_weight = ($received != 0)? $actual_weight/$received:0;
                $avg_rev_actual_weight = ($actual_weight != 0 && $revenue_wo_gst != 0)? $revenue_wo_gst/$actual_weight:0;
                $avg_chargeable_weight = ($received != 0)? $chargeable_weight/$received:0;
                $avg_rev_chargeable_weight = ($chargeable_weight != 0 && $revenue_wo_gst != 0)? $revenue_wo_gst/$chargeable_weight:0;

                //end changes
                $row['serials'] = $serial_number_hubs;
                $row['hub'] = $hub->name;
                $row['booked'] = number_format($booked);
                $row['received'] = number_format($received);
                $row['revenue_wo_gst'] = number_format($revenue_wo_gst);
                $avg_rev = round($avg_revenue);
                $row['average_revenue'] = number_format($avg_rev);
                $row['actual_weight'] =number_format($actual_weight);
                $string_avg_actual_weight = (string) $avg_actual_weight;
                $row['avg_actual_weight'] = number_format((float)$string_avg_actual_weight,2,'.','');
                $row['avg_rev_actual_weight'] = round($avg_rev_actual_weight);

                $row['chargeable_weight'] = number_format($chargeable_weight);
                $string_avg_chargeable_weight = (string) $avg_chargeable_weight;
                $row['avg_chargeable_weight'] = number_format((float)$string_avg_chargeable_weight,2,'.','');
                $row['avg_rev_chargeable_weight'] = round($avg_rev_chargeable_weight);
                $row['cod_collection'] = number_format($cod_collection);

                $avg_cc = round($avg_cash_collection);
                $row['average_cash_collection'] = number_format($avg_cc);
                $rev_occ =   round($rev_on_cash_collection);
                $row['revenue_cash_collection'] = number_format($rev_occ).'%';
                $sort_support_array[] = $received;

                $sorted_details_array[] = $row;
                $total_booked += $booked;
                $total_received += $received;
                $total_revenue_wo_gst += $revenue_wo_gst;
                $total_cod_collection += $cod_collection;
                $total_actual_weight += $actual_weight;

                $total_chargeable_weight += $chargeable_weight;

            }
        }
        $total_avg_revenue = ($total_received != 0) ? $total_revenue_wo_gst / $total_received:0;
        $total_avg_actual_weight = ($total_received != 0) ? $total_actual_weight / $total_received:0;
        $total_avg_rev_actual_weight = ($total_actual_weight != 0) ? $total_revenue_wo_gst / $total_actual_weight:0;
        $total_avg_chargeable_weight = ($total_received != 0) ? $total_chargeable_weight / $total_received:0;
        $total_avg_rev_chargeable_weight = ($total_chargeable_weight != 0) ? $total_revenue_wo_gst / $total_chargeable_weight:0;
        $total_avg_cash_collection = ($total_received != 0) ? $total_cod_collection / $total_received:0;
        $total_rev_on_cash_collection = ($total_cod_collection != 0) ? $total_revenue_wo_gst / $total_cod_collection:0;
        $total_rev_on_cash_collection = $total_rev_on_cash_collection * 100;
        array_multisort($sort_support_array, SORT_DESC, $sorted_details_array);
        $counter = 1;
        foreach ($sorted_details_array as $item) {
            $item['serials'] = $counter;
            $details[] = $item;
            $counter++;
        }
        $details[] = ['Grand Total','Origin '.$only_date, number_format($total_booked), number_format($total_received), number_format($total_revenue_wo_gst),number_format($total_avg_revenue),number_format($total_actual_weight),number_format((float) $total_avg_actual_weight,2,'.',''),round($total_avg_rev_actual_weight),number_format($total_chargeable_weight),number_format((float) $total_avg_chargeable_weight,2,'.',''), round($total_avg_rev_chargeable_weight), number_format($total_cod_collection),number_format($total_avg_cash_collection),number_format($total_rev_on_cash_collection).'%'];
        $details_shipper['header'] = ['S. No.','Origin','Sales Person','Shipper Name(s) (Account No(s))', 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Avg/Parcel Revenue','Actual Weight','Avg. Actual Weight/Parcel','Avg. Revenue On Actual Weight','Chargeable Weight','Avg. Chargeable Weight/Parcel','Avg. Revenue On Chargeable Weight','Collection Amount','Avg. Amount Collection','% Rev. on Amount Collection'];

        $serial_number_shippers = 0;

        foreach($hubs as $hub){

            $pickup_request_shippers = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($hub) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                    });
            })->whereExists(function ($query) use ($date_from, $date_to) {
                $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$date_from, $date_to]);
            })->where('shipments.user_id', '!=', 1690);

            if($pickup_request_shippers->exists()) {
                $pickup_request_shippers_ids = $pickup_request_shippers->pluck('user_id')->toArray();

                $pickup_request_shippers_ids = array_unique($pickup_request_shippers_ids);

                $shippers[$hub->id] = DB::connection('reports')->table('users')->select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->whereIn('id', $tagged_shippers)->get();

                }

            }

        $shipper_sort_support_array = array();
        $total_shipper_booked = 0;
        $total_shipper_received = 0;
        $total_shipper_cod_collection = 0;
        $total_shipper_actual_weight = 0;
        $total_shipper_avg_actual_weight = 0;
        $total_shipper_avg_rev_actual_weight = 0;
        $total_shipper_chargeable_weight = 0;
        $total_shipper_avg_chargeable_weight = 0;
        $total_shipper_avg_rev_chargeable_weight = 0;
        $total_shipper_revenue_wo_gst = 0;
        $total_shipper_avg_revenue = 0;
        $total_shipper_avg_cash_collection = 0;
        $total_shipper_rev_on_cash_collection = 0;
        $shipper_sales_person_name = '';
        $shipper_sales_person_name = Admin::find($sale_person_id)->name;
        if(count($shippers) > 0) {
            foreach ($shippers as $origin => $shipper_row) {
                foreach ($shipper_row as $shipper) {

                    $shipper_booked = 0;
                    $shipper_received = 0;
                    $shipper_rev_wo_gst = 0;
                    $shipper_cod = 0;
                    $shipper_actual_weight = 0;
                    $shipper_chargeable_weight = 0;
                    $origin_name =  DB::connection('reports')->table('cities')->where('id', $origin)->select('name')->first();
                    $origin_name = $origin_name->name;



                    $shipper_booked = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                        ->whereExists(function ($query) use ($origin) {
                            $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function ($sub_query) use ($origin) {
                                    $sub_query->from('cities')
                                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                        ->where('cities.id',$origin);
                                });
                        })->whereBetween('created_at', [$date_from, $date_to])->where('shipments.packaging_material_request', '=', 0)->count();
                    $shipper_received = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                        ->whereExists(function ($query) use ($origin) {
                            $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function ($sub_query) use ($origin) {
                                    $sub_query->from('cities')
                                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                        ->where('cities.id',$origin);
                                });
                        })->whereExists(function ($query) use ($date_from, $date_to) {
                            $query->from('shipments_journey')
                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$date_from, $date_to])
                                ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', '=', 0)->count();
                    if($shipper_booked > 0 || $shipper_received > 0){
                        $shipments_data = array();
                        $shipments_data = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                            ->whereExists(function ($query) use ($origin) {
                                $query->from('user_shipping_infos')
                                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                    ->whereExists(function ($sub_query) use ($origin) {
                                        $sub_query->from('cities')
                                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                            ->where('cities.id',$origin);
                                    });
                            })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                    ->whereBetween('created_at', [$date_from, $date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                        $shipper_rev_wo_gst = $shipments_data->revenue_wo_gst;
                        $shipper_cod = $shipments_data->cod_collection;
                        $shipper_actual_weight = $shipments_data->actual_weight;
                        $shipper_chargeable_weight = $shipments_data->chargeable_weight;


                    }



                    $shipper_avg_revenue = ($shipper_received != 0) ? $shipper_rev_wo_gst / $shipper_received : 0;
                    $shipper_avg_cash_collection = ($shipper_received != 0) ? $shipper_cod / $shipper_received : 0;
                    $shipper_rev_on_cash_collection = (($shipper_avg_cash_collection != 0) ? $shipper_avg_revenue / $shipper_avg_cash_collection : 0) * 100;

                    //changes add columns
                    $shipper_avg_actual_weight = ($shipper_received != 0) ? $shipper_actual_weight / $shipper_received : 0;
                    $shipper_avg_rev_actual_weight = ($shipper_actual_weight != 0 && $shipper_rev_wo_gst != 0) ? $shipper_rev_wo_gst / $shipper_actual_weight : 0;
                    $shipper_avg_chargeable_weight = ($shipper_received != 0) ? $shipper_chargeable_weight / $shipper_received : 0;
                    $shipper_avg_rev_chargeable_weight = ($shipper_chargeable_weight != 0 && $shipper_rev_wo_gst != 0) ? $shipper_rev_wo_gst / $shipper_chargeable_weight : 0;

                    //end
                    $shipper_row = array();
                    $shipper_row['shipper_serial'] = $serial_number_shippers;
                    $shipper_row['origin_name'] = $origin_name;
                    $shipper_row['shipper_sale_person'] = $shipper_sales_person_name;
                    $shipper_row['name'] = $shipper->name . ' (' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . ')';
                    $shipper_row['shipper_booked'] = number_format($shipper_booked);
                    $shipper_row['shipper_received'] = number_format($shipper_received);
                    $shipper_row['shipper_rev_wo_gst'] = number_format($shipper_rev_wo_gst);
                    $s_avg_revenue = round($shipper_avg_revenue);
                    $shipper_row['shipper_avg_revenue'] = number_format($s_avg_revenue);
                    $shipper_row['shipper_actual_weight'] = number_format($shipper_actual_weight);
                    $shipper_row['shipper_avg_actual_weight'] = number_format((float)$shipper_avg_actual_weight, 2, '.', '');
                    $shipper_row['$shipper_avg_rev_actual_weight'] = round($shipper_avg_rev_actual_weight);
                    $shipper_row['shipper_chargeable_weight'] = number_format($shipper_chargeable_weight);
                    $shipper_row['shipper_avg_chargeable_weight'] = number_format((float)$shipper_avg_chargeable_weight, 2, '.', '');
                    $shipper_row['shipper_avg_rev_chargeable_weight'] = round($shipper_avg_rev_chargeable_weight);
                    $shipper_row['shipper_cod'] = number_format($shipper_cod);

                    $avg_cash_coll = round($shipper_avg_cash_collection);
                    $shipper_row['shipper_avg_cc'] = number_format($avg_cash_coll);
                    $avg_rev_cc = round($shipper_rev_on_cash_collection);
                    $shipper_row['shipper_rcc'] = number_format($avg_rev_cc) . '%';

                    $sorted_shipper_array[] = $shipper_row;
                    $shipper_sort_support_array[] = $shipper_received;
                    $total_shipper_booked += $shipper_booked;
                    $total_shipper_received += $shipper_received;
                    $total_shipper_cod_collection += $shipper_cod;
                    $total_shipper_revenue_wo_gst += $shipper_rev_wo_gst;
                    $total_shipper_actual_weight += $shipper_actual_weight;
//                $total_shipper_avg_actual_weight += $shipper_avg_actual_weight;
//                $total_shipper_avg_rev_actual_weight += $shipper_avg_rev_actual_weight;
                    $total_shipper_chargeable_weight += $shipper_chargeable_weight;
//                $total_shipper_avg_chargeable_weight += $shipper_avg_chargeable_weight;
//                $total_shipper_avg_rev_chargeable_weight += $shipper_avg_rev_chargeable_weight;
//                $total_shipper_avg_revenue += $s_avg_revenue;

//                $total_shipper_rev_on_cash_collection += $avg_rev_cc;


//                $serial_number_shippers++;
                }
            }
        }

        $total_shipper_avg_revenue = ($total_shipper_received != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_received:0;
        $total_shipper_avg_actual_weight = ($total_shipper_received != 0) ? $total_shipper_actual_weight / $total_shipper_received:0;
        $total_shipper_avg_rev_actual_weight = ($total_shipper_actual_weight != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_actual_weight:0;
        $total_shipper_avg_chargeable_weight = ($total_shipper_received != 0) ? $total_shipper_chargeable_weight / $total_shipper_received:0;
        $total_shipper_avg_rev_chargeable_weight = ($total_shipper_chargeable_weight != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_chargeable_weight:0;
        $total_shipper_avg_cash_collection = ($total_shipper_received != 0) ? $total_shipper_cod_collection / $total_shipper_received:0;

        $total_shipper_rev_on_cash_collection = ($total_shipper_cod_collection != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_cod_collection:0;
        $total_shipper_rev_on_cash_collection = $total_shipper_rev_on_cash_collection * 100;
        array_multisort($shipper_sort_support_array, SORT_DESC, $sorted_shipper_array);
        $shipper_counter = 1;
        foreach ($sorted_shipper_array as $shipper) {
            $shipper['shipper_serial'] = $shipper_counter;
            $details_shipper[] = $shipper;
            $shipper_counter++;
        }
        $details_shipper[] = ['Grand Total','','','DSR '.$only_date, number_format($total_shipper_booked), number_format($total_shipper_received), number_format($total_shipper_revenue_wo_gst),number_format($total_shipper_avg_revenue),number_format($total_shipper_actual_weight),number_format((float) $total_shipper_avg_actual_weight,2,'.',''),round($total_shipper_avg_rev_actual_weight),number_format($total_shipper_chargeable_weight),number_format((float) $total_shipper_avg_chargeable_weight,2,'.',''),round($total_shipper_avg_rev_chargeable_weight), number_format($total_shipper_cod_collection),number_format($total_shipper_avg_cash_collection),number_format($total_shipper_rev_on_cash_collection).'%'];

        $shipping_mode_wise_header['header'] = ['S. No.','Shipping Mode', 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Avg/Parcel Revenue','Actual Weight','Avg Actual Weight/Parcel','Avg Revenue on Actual Weight','Chargeable Weight','Avg. Chargeable Weight/Parcel','Avg. Revenue On Chargeable Weight','Collection Amount','Avg. Amount Collection','% Rev. on Amount Collection'];

        $shipping_mode_wise_details = self::daily_pickup_sales_shipping_mode_wise($date_from,$date_to,TRUE, $sale_person_id);

        $total_shipping_booked = 0;
        $total_shipping_received = 0;
        $total_shipping_revenue_wo_gst = 0;
        $total_shipping_avg_parcel_revenue = 0;
        $total_shipping_actual_weight = 0;
        $total_shipping_avg_actual_weight = 0;
        $total_shipping_avg_rev_actual_weight = 0;
        $total_shipping_chargeable_weight = 0;
        $total_shipping_avg_chargeable_weight = 0;
        $total_shipping_avg_rev_chargeable_weight = 0;
        $total_shipping_collection_amount = 0;
        $total_shipping_avg_amount_collection = 0;
        $total_shipping_rev_amount_collection = 0;
        $total_avg_revenue = 0;
        $total_avg_actual_weight = 0;
        $total_avg_rev_actual_weight = 0;
        $total_avg_chargeable_weight = 0;
        $shipping_mode_wise_data = array();
        foreach($shipping_mode_wise_details as $shipping_mode_total){
            $total_shipping_booked += $shipping_mode_total['booked'];
            $total_shipping_received += $shipping_mode_total['received'];
            $total_shipping_revenue_wo_gst += $shipping_mode_total['revenue_wo_gst'];
            $total_shipping_avg_parcel_revenue  += $shipping_mode_total['avg_parcel_rev'];
            $total_shipping_actual_weight += $shipping_mode_total['actual_weight'];
            $total_shipping_avg_actual_weight += $shipping_mode_total['avg_actual_weight'];
            $total_shipping_avg_rev_actual_weight += $shipping_mode_total['avg_rev_actual_weight'];
            $total_shipping_chargeable_weight += $shipping_mode_total['chargeable_weight'];
            $total_shipping_avg_chargeable_weight += $shipping_mode_total['avg_chargeable_weight'];
            $total_shipping_avg_rev_chargeable_weight += $shipping_mode_total['avg_rev_chargeable_weight'];
            $total_shipping_collection_amount += $shipping_mode_total['collection_amount'];
            $total_shipping_avg_amount_collection += $shipping_mode_total['avg_amount_collection'];
            $total_shipping_rev_amount_collection += $shipping_mode_total['revenue_amount_collection'];

            $shipping_mode_wise_data[] = [$shipping_mode_total['serial'],$shipping_mode_total['mode'],number_format(round($shipping_mode_total['booked'])),$shipping_mode_total['received'],number_format($shipping_mode_total['revenue_wo_gst']),number_format($shipping_mode_total['avg_parcel_rev']) ,number_format($shipping_mode_total['actual_weight']),number_format($shipping_mode_total['avg_actual_weight']),round($shipping_mode_total['avg_rev_actual_weight']),number_format($shipping_mode_total['chargeable_weight']),number_format($shipping_mode_total['avg_chargeable_weight']),round($shipping_mode_total['avg_rev_chargeable_weight']),$shipping_mode_total['collection_amount'],$shipping_mode_total['avg_amount_collection'],round( $shipping_mode_total['revenue_amount_collection']) .'%'];
        }

        $total_shipping_avg_parcel_revenue = ($total_shipping_received != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_received:0;
        $total_avg_actual_weight = ($total_shipping_received != 0) ? $total_shipping_actual_weight / $total_shipping_received:0;
        $total_avg_rev_actual_weight = ($total_shipping_actual_weight != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_actual_weight:0;
        $total_avg_chargeable_weight = ($total_shipping_received != 0) ? $total_shipping_chargeable_weight / $total_shipping_received:0;
        $total_avg_rev_chargeable_weight = ($total_shipping_chargeable_weight != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_chargeable_weight:0;
        $total_avg_cash_collection = ($total_shipping_received != 0) ? $total_shipping_collection_amount / $total_shipping_received:0;
        $total_rev_on_cash_collection = ($total_shipping_collection_amount != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_collection_amount:0;
        $total_rev_on_cash_collection = $total_rev_on_cash_collection * 100;

        $shipping_mode_wise_footer[] = ['Grand Total.',' ',number_format(round($total_shipping_booked)),$total_shipping_received,number_format(round($total_shipping_revenue_wo_gst)),number_format($total_shipping_avg_parcel_revenue) ,number_format($total_shipping_actual_weight),number_format($total_avg_actual_weight),number_format($total_avg_rev_actual_weight),number_format($total_shipping_chargeable_weight),number_format($total_avg_chargeable_weight),number_format($total_avg_rev_chargeable_weight),$total_shipping_collection_amount,number_format($total_avg_cash_collection),number_format($total_rev_on_cash_collection) .'%'];

        $shipping_mode_wise_details = array_merge($shipping_mode_wise_header,$shipping_mode_wise_data,$shipping_mode_wise_footer);

        $spreadsheet = new Spreadsheet();
        $cell_st =[
            'font' =>['bold' => true],
            'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                )
            ),
        ];
        $total_cell_st =[
            'font' =>['bold' => true],
            'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            )
        ];
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getDefaultColumnDimension()->setWidth(20);
        $sheet->getStyle('D3:R3')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
        $sheet->getStyle('D3:R3')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D3:R3')->applyFromArray($cell_st);
        $sheet->fromArray($shipping_mode_wise_details,NULL,'D3',true);
        $sheet->getStyle('D8:R8')->applyFromArray($total_cell_st);
        $count_shipping_mode = count($shipping_mode_wise_details);
        $count_shipping_mode += 7;
        /*  $count_hubs = count($hubs);
          $count_hub_rows = count($details);
          $count_hub_rows += 2;

          $count_hubs += 3;
          $total_shipper_rows = count($details_shipper);
          $total_shipper_rows += $count_hubs;
          $total_shipper_rows = $total_shipper_rows - 1;*/

        //details
        $total_style_cell = "D$count_shipping_mode".":R".$count_shipping_mode;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->getStyle($total_style_cell)->getAlignment()->setWrapText(true);
        $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);
        $sheet->getStyle($total_style_cell)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
        $shipper_cell = 'D'.$count_shipping_mode; //D13
        $shipper_last_cell = 'R'.$count_shipping_mode; //R13
        $sheet->fromArray($details,NULL,$shipper_cell,true);
        $count_details = count($details);
        $total_hub = $count_shipping_mode + $count_details-1;
        $total_style_cell = "D$total_hub".":R".$total_hub;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $count_details = $count_details + $count_shipping_mode + 4;

        //shipper_details
        $total_style_cell = "D$count_details".":T".$count_details;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);
        $sheet->getStyle($total_style_cell)->getAlignment()->setWrapText(true);


        $sheet->getStyle($total_style_cell)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
        $details_shipper_cell = 'D'.$count_details;
        $details_shipper_last_cell = 'R'.$count_details;
        // $sheet->fromArray($details_shipper,NULL,'D15',true);
        $sheet->fromArray($details_shipper,NULL,$details_shipper_cell,true);
        $total_shipper_count = count($details_shipper);
        $total_shipper = $total_shipper_count + $count_details -1;
        $total_style_cell = "D$total_shipper".":T".$total_shipper;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->setTitle('Daily Pickup Sales Report');

        /* $hub_all_rows = "D3".":R".$count_hub_rows;

         $sheet->getStyle($hub_all_rows)->applyFromArray($cell_st);

         $shipper_style_cell = "D$count_hubs".":T".$count_hubs;
         $shipper_all_rows = "D$count_hubs".":T".$total_shipper_rows;*/

        /* $total_style_cell = "D$count_hub_rows".":R".$count_hub_rows;
         $total_shipper_style_cell = "D$total_shipper_rows".":T".$total_shipper_rows;
         $sheet->getStyle($shipper_style_cell)
             ->getFill()
             ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
             ->getStartColor()
             ->setRGB('CECECE');
 //        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
         $sheet->getStyle($shipper_all_rows)->applyFromArray($cell_st);*/

        /* $sheet->getStyle($shipper_style_cell)->getAlignment()->setWrapText(true);*/
//        $sheet->getStyle($total_shipper_style_cell)->applyFromArray($total_cell_st);

        /* $set_shipper_actual_number_format = 'K'.$count_hubs.':K'.$total_shipper_rows;
         $set_shipper_chargeable_number_format = 'N'.$count_hubs.':N'.$total_shipper_rows;*/
        /* $sheet->getStyle($set_shipper_actual_number_format)->getNumberFormat()->setFormatCode('0.00');
         $sheet->getStyle($set_shipper_chargeable_number_format)->getNumberFormat()->setFormatCode('0.00');*/


        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');

        $file_name_without_path = '';


        $file_name_without_path = "reports/daily_pickup_sales_report_".$date_file_name.'_'.$sale_person_id.'_'.$time_string.".xlsx";
        $file_name = public_path() .'/'.$file_name_without_path ;


        $writer->save($file_name);


        return url('/').'/'.$file_name_without_path;

}
    static public function daily_pickup_sales_report_individual_kae($date, $sale_person_id)
    {
        $date_from = Carbon::createFromFormat("Y-m-d H:i:s", $date)->format('Y-m-d 06:00A');
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s", $next_day)->format('Y-m-d 05:59A');
        $only_date = Carbon::parse($date)->toDateString();
        $hubs = array();
        $city = array();

        $hubs = DB::connection('reports')->table('cities')->where('pickup', 1)->select('id', 'name')->get();

        $details = array();
        $shipping_wise_details = array();
        $sorted_details_array = array();
        $sorted_shipper_array = array();
        $details_shipper = array();
        $shippers = array();

        $details['header'] = ['S. No.', 'Origin ' . $only_date, 'No. of Parcels Booked', 'No of Parcels Received', 'Revenue without GST', 'Avg/Parcel Revenue', 'Actual Weight', 'Avg. Actual Weight/Parcel', 'Avg. Revenue On Actual Weight', 'Chargeable Weight', 'Avg. Chargeable Weight/Parcel', 'Avg. Revenue On Chargeable Weight', 'Collection Amount', 'Avg. Amount Collection', '% Rev. on Amount Collection'];

        $sort_support_array = array();
        $total_booked = 0;
        $total_received = 0;
        $total_cod_collection = 0;
        $total_actual_weight = 0;
        $total_avg_actual_weight = 0;
        $total_avg_rev_actual_weight = 0;
        $total_chargeable_weight = 0;
        $total_avg_chargeable_weight = 0;
        $total_avg_rev_chargeable_weight = 0;
        $total_revenue_wo_gst = 0;
        $total_avg_revenue = 0;
        $total_avg_cash_collection = 0;
        $total_rev_on_cash_collection = 0;
        $serial_number_hubs = 0;
        $booked = 0;
        $received = 0;
        $revenue_wo_gst = 0;
        $cod_collection = 0;
        $actual_weight = 0;
        $chargeable_weight = 0;


        $tagged_shippers = DB::connection('reports')->table('sale_tier_tags')->where('kam', $sale_person_id)->select('user_id')->distinct()->pluck('user_id')->toArray();

        if(count($tagged_shippers) < 1)
        {
            return null;
        }

        foreach ($hubs as $hub) {

            $booked = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                    });
            })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->whereBetween('created_at',[$date_from,$date_to])->whereIn('shipments.user_id', $tagged_shippers)->select('shipments.id')->get()->count();
            $received = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                    });
            })->whereExists(function ($query) use ($date_from, $date_to) {
                $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$date_from, $date_to])
                    ->where('shipper_status_id', 2);
            })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->whereIn('shipments.user_id', $tagged_shippers)->select('shipments.id')->get()->count();
            if($booked > 0 || $received > 0){
                $shipments_data = array();
                $shipments_data = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                    $query->from('user_shipping_infos')
                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                        ->whereExists(function($sub_query) use ($hub) {
                            $sub_query->from('cities')
                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                ->where('cities.id', $hub->id);
                        });
                })->whereExists(function ($query) use ($date_from, $date_to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$date_from, $date_to])
                        ->where('shipper_status_id', 2);
                })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->whereIn('shipments.user_id', $tagged_shippers)->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                $cod_collection = $shipments_data->cod_collection;
                $actual_weight = $shipments_data->actual_weight;
                $chargeable_weight = $shipments_data->chargeable_weight;
                $row = array();
                $avg_revenue = ($received != 0)? $revenue_wo_gst/$received:0;
                $avg_cash_collection = ($received != 0)? $cod_collection/$received:0;
                $rev_on_cash_collection = (($avg_cash_collection != 0)? $avg_revenue/$avg_cash_collection:0)*100;
                //changes add columns
                $avg_actual_weight = ($received != 0)? $actual_weight/$received:0;
                $avg_rev_actual_weight = ($actual_weight != 0 && $revenue_wo_gst != 0)? $revenue_wo_gst/$actual_weight:0;
                $avg_chargeable_weight = ($received != 0)? $chargeable_weight/$received:0;
                $avg_rev_chargeable_weight = ($chargeable_weight != 0 && $revenue_wo_gst != 0)? $revenue_wo_gst/$chargeable_weight:0;

                //end changes
                $row['serials'] = $serial_number_hubs;
                $row['hub'] = $hub->name;
                $row['booked'] = number_format($booked);
                $row['received'] = number_format($received);
                $row['revenue_wo_gst'] = number_format($revenue_wo_gst);
                $avg_rev = round($avg_revenue);
                $row['average_revenue'] = number_format($avg_rev);
                $row['actual_weight'] =number_format($actual_weight);
                $string_avg_actual_weight = (string) $avg_actual_weight;
                $row['avg_actual_weight'] = number_format((float)$string_avg_actual_weight,2,'.','');
                $row['avg_rev_actual_weight'] = round($avg_rev_actual_weight);

                $row['chargeable_weight'] = number_format($chargeable_weight);
                $string_avg_chargeable_weight = (string) $avg_chargeable_weight;
                $row['avg_chargeable_weight'] = number_format((float)$string_avg_chargeable_weight,2,'.','');
                $row['avg_rev_chargeable_weight'] = round($avg_rev_chargeable_weight);
                $row['cod_collection'] = number_format($cod_collection);

                $avg_cc = round($avg_cash_collection);
                $row['average_cash_collection'] = number_format($avg_cc);
                $rev_occ =   round($rev_on_cash_collection);
                $row['revenue_cash_collection'] = number_format($rev_occ).'%';
                $sort_support_array[] = $received;

                $sorted_details_array[] = $row;
                $total_booked += $booked;
                $total_received += $received;
                $total_revenue_wo_gst += $revenue_wo_gst;
                $total_cod_collection += $cod_collection;
                $total_actual_weight += $actual_weight;

                $total_chargeable_weight += $chargeable_weight;

            }
        }
        $total_avg_revenue = ($total_received != 0) ? $total_revenue_wo_gst / $total_received:0;
        $total_avg_actual_weight = ($total_received != 0) ? $total_actual_weight / $total_received:0;
        $total_avg_rev_actual_weight = ($total_actual_weight != 0) ? $total_revenue_wo_gst / $total_actual_weight:0;
        $total_avg_chargeable_weight = ($total_received != 0) ? $total_chargeable_weight / $total_received:0;
        $total_avg_rev_chargeable_weight = ($total_chargeable_weight != 0) ? $total_revenue_wo_gst / $total_chargeable_weight:0;
        $total_avg_cash_collection = ($total_received != 0) ? $total_cod_collection / $total_received:0;
        $total_rev_on_cash_collection = ($total_cod_collection != 0) ? $total_revenue_wo_gst / $total_cod_collection:0;
        $total_rev_on_cash_collection = $total_rev_on_cash_collection * 100;
        array_multisort($sort_support_array, SORT_DESC, $sorted_details_array);
        $counter = 1;
        foreach ($sorted_details_array as $item) {
            $item['serials'] = $counter;
            $details[] = $item;
            $counter++;
        }
        $details[] = ['Grand Total','Origin '.$only_date, number_format($total_booked), number_format($total_received), number_format($total_revenue_wo_gst),number_format($total_avg_revenue),number_format($total_actual_weight),number_format((float) $total_avg_actual_weight,2,'.',''),round($total_avg_rev_actual_weight),number_format($total_chargeable_weight),number_format((float) $total_avg_chargeable_weight,2,'.',''), round($total_avg_rev_chargeable_weight), number_format($total_cod_collection),number_format($total_avg_cash_collection),number_format($total_rev_on_cash_collection).'%'];
        $details_shipper['header'] = ['S. No.','Origin','Sales Person','Shipper Name(s) (Account No(s))', 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Avg/Parcel Revenue','Actual Weight','Avg. Actual Weight/Parcel','Avg. Revenue On Actual Weight','Chargeable Weight','Avg. Chargeable Weight/Parcel','Avg. Revenue On Chargeable Weight','Collection Amount','Avg. Amount Collection','% Rev. on Amount Collection'];

        $serial_number_shippers = 0;

        foreach($hubs as $hub){

            $pickup_request_shippers = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($hub) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                    });
            })->whereExists(function ($query) use ($date_from, $date_to) {
                $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$date_from, $date_to]);
            })->where('shipments.user_id', '!=', 1690);

            if($pickup_request_shippers->exists()) {
                $pickup_request_shippers_ids = $pickup_request_shippers->pluck('user_id')->toArray();

                $pickup_request_shippers_ids = array_unique($pickup_request_shippers_ids);

                $shippers[$hub->id] = DB::connection('reports')->table('users')->select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->whereIn('id', $tagged_shippers)->get();

                }

            }

        $shipper_sort_support_array = array();
        $total_shipper_booked = 0;
        $total_shipper_received = 0;
        $total_shipper_cod_collection = 0;
        $total_shipper_actual_weight = 0;
        $total_shipper_avg_actual_weight = 0;
        $total_shipper_avg_rev_actual_weight = 0;
        $total_shipper_chargeable_weight = 0;
        $total_shipper_avg_chargeable_weight = 0;
        $total_shipper_avg_rev_chargeable_weight = 0;
        $total_shipper_revenue_wo_gst = 0;
        $total_shipper_avg_revenue = 0;
        $total_shipper_avg_cash_collection = 0;
        $total_shipper_rev_on_cash_collection = 0;
        $shipper_sales_person_name = '';
        $shipper_sales_person_name = Admin::find($sale_person_id)->name;
        if(count($shippers) > 0) {
            foreach ($shippers as $origin => $shipper_row) {
                foreach ($shipper_row as $shipper) {

                    $shipper_booked = 0;
                    $shipper_received = 0;
                    $shipper_rev_wo_gst = 0;
                    $shipper_cod = 0;
                    $shipper_actual_weight = 0;
                    $shipper_chargeable_weight = 0;
                    $origin_name =  DB::connection('reports')->table('cities')->where('id', $origin)->select('name')->first();
                    $origin_name = $origin_name->name;



                    $shipper_booked = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                        ->whereExists(function ($query) use ($origin) {
                            $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function ($sub_query) use ($origin) {
                                    $sub_query->from('cities')
                                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                        ->where('cities.id',$origin);
                                });
                        })->whereBetween('created_at', [$date_from, $date_to])->where('shipments.packaging_material_request', '=', 0)->count();
                    $shipper_received = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                        ->whereExists(function ($query) use ($origin) {
                            $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function ($sub_query) use ($origin) {
                                    $sub_query->from('cities')
                                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                        ->where('cities.id',$origin);
                                });
                        })->whereExists(function ($query) use ($date_from, $date_to) {
                            $query->from('shipments_journey')
                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$date_from, $date_to])
                                ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', '=', 0)->count();
                    if($shipper_booked > 0 || $shipper_received > 0){
                        $shipments_data = array();
                        $shipments_data = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                            ->whereExists(function ($query) use ($origin) {
                                $query->from('user_shipping_infos')
                                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                    ->whereExists(function ($sub_query) use ($origin) {
                                        $sub_query->from('cities')
                                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                            ->where('cities.id',$origin);
                                    });
                            })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                    ->whereBetween('created_at', [$date_from, $date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                        $shipper_rev_wo_gst = $shipments_data->revenue_wo_gst;
                        $shipper_cod = $shipments_data->cod_collection;
                        $shipper_actual_weight = $shipments_data->actual_weight;
                        $shipper_chargeable_weight = $shipments_data->chargeable_weight;


                    }



                    $shipper_avg_revenue = ($shipper_received != 0) ? $shipper_rev_wo_gst / $shipper_received : 0;
                    $shipper_avg_cash_collection = ($shipper_received != 0) ? $shipper_cod / $shipper_received : 0;
                    $shipper_rev_on_cash_collection = (($shipper_avg_cash_collection != 0) ? $shipper_avg_revenue / $shipper_avg_cash_collection : 0) * 100;

                    //changes add columns
                    $shipper_avg_actual_weight = ($shipper_received != 0) ? $shipper_actual_weight / $shipper_received : 0;
                    $shipper_avg_rev_actual_weight = ($shipper_actual_weight != 0 && $shipper_rev_wo_gst != 0) ? $shipper_rev_wo_gst / $shipper_actual_weight : 0;
                    $shipper_avg_chargeable_weight = ($shipper_received != 0) ? $shipper_chargeable_weight / $shipper_received : 0;
                    $shipper_avg_rev_chargeable_weight = ($shipper_chargeable_weight != 0 && $shipper_rev_wo_gst != 0) ? $shipper_rev_wo_gst / $shipper_chargeable_weight : 0;

                    //end
                    $shipper_row = array();
                    $shipper_row['shipper_serial'] = $serial_number_shippers;
                    $shipper_row['origin_name'] = $origin_name;
                    $shipper_row['shipper_sale_person'] = $shipper_sales_person_name;
                    $shipper_row['name'] = $shipper->name . ' (' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . ')';
                    $shipper_row['shipper_booked'] = number_format($shipper_booked);
                    $shipper_row['shipper_received'] = number_format($shipper_received);
                    $shipper_row['shipper_rev_wo_gst'] = number_format($shipper_rev_wo_gst);
                    $s_avg_revenue = round($shipper_avg_revenue);
                    $shipper_row['shipper_avg_revenue'] = number_format($s_avg_revenue);
                    $shipper_row['shipper_actual_weight'] = number_format($shipper_actual_weight);
                    $shipper_row['shipper_avg_actual_weight'] = number_format((float)$shipper_avg_actual_weight, 2, '.', '');
                    $shipper_row['$shipper_avg_rev_actual_weight'] = round($shipper_avg_rev_actual_weight);
                    $shipper_row['shipper_chargeable_weight'] = number_format($shipper_chargeable_weight);
                    $shipper_row['shipper_avg_chargeable_weight'] = number_format((float)$shipper_avg_chargeable_weight, 2, '.', '');
                    $shipper_row['shipper_avg_rev_chargeable_weight'] = round($shipper_avg_rev_chargeable_weight);
                    $shipper_row['shipper_cod'] = number_format($shipper_cod);

                    $avg_cash_coll = round($shipper_avg_cash_collection);
                    $shipper_row['shipper_avg_cc'] = number_format($avg_cash_coll);
                    $avg_rev_cc = round($shipper_rev_on_cash_collection);
                    $shipper_row['shipper_rcc'] = number_format($avg_rev_cc) . '%';

                    $sorted_shipper_array[] = $shipper_row;
                    $shipper_sort_support_array[] = $shipper_received;
                    $total_shipper_booked += $shipper_booked;
                    $total_shipper_received += $shipper_received;
                    $total_shipper_cod_collection += $shipper_cod;
                    $total_shipper_revenue_wo_gst += $shipper_rev_wo_gst;
                    $total_shipper_actual_weight += $shipper_actual_weight;
//                $total_shipper_avg_actual_weight += $shipper_avg_actual_weight;
//                $total_shipper_avg_rev_actual_weight += $shipper_avg_rev_actual_weight;
                    $total_shipper_chargeable_weight += $shipper_chargeable_weight;
//                $total_shipper_avg_chargeable_weight += $shipper_avg_chargeable_weight;
//                $total_shipper_avg_rev_chargeable_weight += $shipper_avg_rev_chargeable_weight;
//                $total_shipper_avg_revenue += $s_avg_revenue;

//                $total_shipper_rev_on_cash_collection += $avg_rev_cc;


//                $serial_number_shippers++;
                }
            }
        }

        $total_shipper_avg_revenue = ($total_shipper_received != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_received:0;
        $total_shipper_avg_actual_weight = ($total_shipper_received != 0) ? $total_shipper_actual_weight / $total_shipper_received:0;
        $total_shipper_avg_rev_actual_weight = ($total_shipper_actual_weight != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_actual_weight:0;
        $total_shipper_avg_chargeable_weight = ($total_shipper_received != 0) ? $total_shipper_chargeable_weight / $total_shipper_received:0;
        $total_shipper_avg_rev_chargeable_weight = ($total_shipper_chargeable_weight != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_chargeable_weight:0;
        $total_shipper_avg_cash_collection = ($total_shipper_received != 0) ? $total_shipper_cod_collection / $total_shipper_received:0;

        $total_shipper_rev_on_cash_collection = ($total_shipper_cod_collection != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_cod_collection:0;
        $total_shipper_rev_on_cash_collection = $total_shipper_rev_on_cash_collection * 100;
        array_multisort($shipper_sort_support_array, SORT_DESC, $sorted_shipper_array);
        $shipper_counter = 1;
        foreach ($sorted_shipper_array as $shipper) {
            $shipper['shipper_serial'] = $shipper_counter;
            $details_shipper[] = $shipper;
            $shipper_counter++;
        }
        $details_shipper[] = ['Grand Total','','','DSR '.$only_date, number_format($total_shipper_booked), number_format($total_shipper_received), number_format($total_shipper_revenue_wo_gst),number_format($total_shipper_avg_revenue),number_format($total_shipper_actual_weight),number_format((float) $total_shipper_avg_actual_weight,2,'.',''),round($total_shipper_avg_rev_actual_weight),number_format($total_shipper_chargeable_weight),number_format((float) $total_shipper_avg_chargeable_weight,2,'.',''),round($total_shipper_avg_rev_chargeable_weight), number_format($total_shipper_cod_collection),number_format($total_shipper_avg_cash_collection),number_format($total_shipper_rev_on_cash_collection).'%'];

        $shipping_mode_wise_header['header'] = ['S. No.','Shipping Mode', 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Avg/Parcel Revenue','Actual Weight','Avg Actual Weight/Parcel','Avg Revenue on Actual Weight','Chargeable Weight','Avg. Chargeable Weight/Parcel','Avg. Revenue On Chargeable Weight','Collection Amount','Avg. Amount Collection','% Rev. on Amount Collection'];

        $shipping_mode_wise_details = self::daily_pickup_sales_shipping_mode_wise_kae($date_from,$date_to,TRUE, $sale_person_id);

        $total_shipping_booked = 0;
        $total_shipping_received = 0;
        $total_shipping_revenue_wo_gst = 0;
        $total_shipping_avg_parcel_revenue = 0;
        $total_shipping_actual_weight = 0;
        $total_shipping_avg_actual_weight = 0;
        $total_shipping_avg_rev_actual_weight = 0;
        $total_shipping_chargeable_weight = 0;
        $total_shipping_avg_chargeable_weight = 0;
        $total_shipping_avg_rev_chargeable_weight = 0;
        $total_shipping_collection_amount = 0;
        $total_shipping_avg_amount_collection = 0;
        $total_shipping_rev_amount_collection = 0;
        $total_avg_revenue = 0;
        $total_avg_actual_weight = 0;
        $total_avg_rev_actual_weight = 0;
        $total_avg_chargeable_weight = 0;
        $shipping_mode_wise_data = array();
        foreach($shipping_mode_wise_details as $shipping_mode_total){
            $total_shipping_booked += $shipping_mode_total['booked'];
            $total_shipping_received += $shipping_mode_total['received'];
            $total_shipping_revenue_wo_gst += $shipping_mode_total['revenue_wo_gst'];
            $total_shipping_avg_parcel_revenue  += $shipping_mode_total['avg_parcel_rev'];
            $total_shipping_actual_weight += $shipping_mode_total['actual_weight'];
            $total_shipping_avg_actual_weight += $shipping_mode_total['avg_actual_weight'];
            $total_shipping_avg_rev_actual_weight += $shipping_mode_total['avg_rev_actual_weight'];
            $total_shipping_chargeable_weight += $shipping_mode_total['chargeable_weight'];
            $total_shipping_avg_chargeable_weight += $shipping_mode_total['avg_chargeable_weight'];
            $total_shipping_avg_rev_chargeable_weight += $shipping_mode_total['avg_rev_chargeable_weight'];
            $total_shipping_collection_amount += $shipping_mode_total['collection_amount'];
            $total_shipping_avg_amount_collection += $shipping_mode_total['avg_amount_collection'];
            $total_shipping_rev_amount_collection += $shipping_mode_total['revenue_amount_collection'];

            $shipping_mode_wise_data[] = [$shipping_mode_total['serial'],$shipping_mode_total['mode'],number_format(round($shipping_mode_total['booked'])),$shipping_mode_total['received'],number_format($shipping_mode_total['revenue_wo_gst']),number_format($shipping_mode_total['avg_parcel_rev']) ,number_format($shipping_mode_total['actual_weight']),number_format($shipping_mode_total['avg_actual_weight']),round($shipping_mode_total['avg_rev_actual_weight']),number_format($shipping_mode_total['chargeable_weight']),number_format($shipping_mode_total['avg_chargeable_weight']),round($shipping_mode_total['avg_rev_chargeable_weight']),$shipping_mode_total['collection_amount'],$shipping_mode_total['avg_amount_collection'],round( $shipping_mode_total['revenue_amount_collection']) .'%'];
        }

        $total_shipping_avg_parcel_revenue = ($total_shipping_received != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_received:0;
        $total_avg_actual_weight = ($total_shipping_received != 0) ? $total_shipping_actual_weight / $total_shipping_received:0;
        $total_avg_rev_actual_weight = ($total_shipping_actual_weight != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_actual_weight:0;
        $total_avg_chargeable_weight = ($total_shipping_received != 0) ? $total_shipping_chargeable_weight / $total_shipping_received:0;
        $total_avg_rev_chargeable_weight = ($total_shipping_chargeable_weight != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_chargeable_weight:0;
        $total_avg_cash_collection = ($total_shipping_received != 0) ? $total_shipping_collection_amount / $total_shipping_received:0;
        $total_rev_on_cash_collection = ($total_shipping_collection_amount != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_collection_amount:0;
        $total_rev_on_cash_collection = $total_rev_on_cash_collection * 100;

        $shipping_mode_wise_footer[] = ['Grand Total.',' ',number_format(round($total_shipping_booked)),$total_shipping_received,number_format(round($total_shipping_revenue_wo_gst)),number_format($total_shipping_avg_parcel_revenue) ,number_format($total_shipping_actual_weight),number_format($total_avg_actual_weight),number_format($total_avg_rev_actual_weight),number_format($total_shipping_chargeable_weight),number_format($total_avg_chargeable_weight),number_format($total_avg_rev_chargeable_weight),$total_shipping_collection_amount,number_format($total_avg_cash_collection),number_format($total_rev_on_cash_collection) .'%'];

        $shipping_mode_wise_details = array_merge($shipping_mode_wise_header,$shipping_mode_wise_data,$shipping_mode_wise_footer);

        $spreadsheet = new Spreadsheet();
        $cell_st =[
            'font' =>['bold' => true],
            'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                )
            ),
        ];
        $total_cell_st =[
            'font' =>['bold' => true],
            'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            )
        ];
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getDefaultColumnDimension()->setWidth(20);
        $sheet->getStyle('D3:R3')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
        $sheet->getStyle('D3:R3')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D3:R3')->applyFromArray($cell_st);
        $sheet->fromArray($shipping_mode_wise_details,NULL,'D3',true);
        $sheet->getStyle('D8:R8')->applyFromArray($total_cell_st);
        $count_shipping_mode = count($shipping_mode_wise_details);
        $count_shipping_mode += 7;
        /*  $count_hubs = count($hubs);
          $count_hub_rows = count($details);
          $count_hub_rows += 2;

          $count_hubs += 3;
          $total_shipper_rows = count($details_shipper);
          $total_shipper_rows += $count_hubs;
          $total_shipper_rows = $total_shipper_rows - 1;*/

        //details
        $total_style_cell = "D$count_shipping_mode".":R".$count_shipping_mode;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->getStyle($total_style_cell)->getAlignment()->setWrapText(true);
        $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);
        $sheet->getStyle($total_style_cell)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
        $shipper_cell = 'D'.$count_shipping_mode; //D13
        $shipper_last_cell = 'R'.$count_shipping_mode; //R13
        $sheet->fromArray($details,NULL,$shipper_cell,true);
        $count_details = count($details);
        $total_hub = $count_shipping_mode + $count_details-1;
        $total_style_cell = "D$total_hub".":R".$total_hub;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $count_details = $count_details + $count_shipping_mode + 4;

        //shipper_details
        $total_style_cell = "D$count_details".":T".$count_details;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);
        $sheet->getStyle($total_style_cell)->getAlignment()->setWrapText(true);


        $sheet->getStyle($total_style_cell)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
        $details_shipper_cell = 'D'.$count_details;
        $details_shipper_last_cell = 'R'.$count_details;
        // $sheet->fromArray($details_shipper,NULL,'D15',true);
        $sheet->fromArray($details_shipper,NULL,$details_shipper_cell,true);
        $total_shipper_count = count($details_shipper);
        $total_shipper = $total_shipper_count + $count_details -1;
        $total_style_cell = "D$total_shipper".":T".$total_shipper;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->setTitle('Daily Pickup Sales Report');

        /* $hub_all_rows = "D3".":R".$count_hub_rows;

         $sheet->getStyle($hub_all_rows)->applyFromArray($cell_st);

         $shipper_style_cell = "D$count_hubs".":T".$count_hubs;
         $shipper_all_rows = "D$count_hubs".":T".$total_shipper_rows;*/

        /* $total_style_cell = "D$count_hub_rows".":R".$count_hub_rows;
         $total_shipper_style_cell = "D$total_shipper_rows".":T".$total_shipper_rows;
         $sheet->getStyle($shipper_style_cell)
             ->getFill()
             ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
             ->getStartColor()
             ->setRGB('CECECE');
 //        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
         $sheet->getStyle($shipper_all_rows)->applyFromArray($cell_st);*/

        /* $sheet->getStyle($shipper_style_cell)->getAlignment()->setWrapText(true);*/
//        $sheet->getStyle($total_shipper_style_cell)->applyFromArray($total_cell_st);

        /* $set_shipper_actual_number_format = 'K'.$count_hubs.':K'.$total_shipper_rows;
         $set_shipper_chargeable_number_format = 'N'.$count_hubs.':N'.$total_shipper_rows;*/
        /* $sheet->getStyle($set_shipper_actual_number_format)->getNumberFormat()->setFormatCode('0.00');
         $sheet->getStyle($set_shipper_chargeable_number_format)->getNumberFormat()->setFormatCode('0.00');*/


        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');

        $file_name_without_path = '';


        $file_name_without_path = "reports/daily_pickup_sales_report_".$date_file_name.'_'.$shipper_sales_person_name.'_'.$time_string.".xlsx";
        $file_name = public_path() .'/'.$file_name_without_path ;


        $writer->save($file_name);


        return url('/').'/'.$file_name_without_path;

}

    static public function daily_pickup_sales_report_rm($date, $rm_id){
        $date_from = Carbon::createFromFormat("Y-m-d H:i:s",$date)->format('Y-m-d 06:00A');
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s",$next_day)->format('Y-m-d 05:59A');
        $only_date = Carbon::parse($date)->toDateString();
        $hubs = array();
        $city = array();

        $assigned_hubs = AdminHub::where('admin_id', $rm_id)->pluck('hub_id')->toArray();
        $hubs = DB::connection('reports')->table('cities')->whereIn('hub_id', $assigned_hubs)->where('pickup', 1)->select('id', 'name')->get();

        $details = array();
        $shipping_wise_details = array();
        $sorted_details_array = array();
        $sorted_shipper_array = array();
        $details_shipper = array();
        $shippers = array();

        $details['header'] = ['S. No.','Origin '.$only_date, 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Avg/Parcel Revenue','Actual Weight','Avg. Actual Weight/Parcel','Avg. Revenue On Actual Weight','Chargeable Weight','Avg. Chargeable Weight/Parcel','Avg. Revenue On Chargeable Weight','Collection Amount','Avg. Amount Collection','% Rev. on Amount Collection'];

        $sort_support_array = array();
        $total_booked = 0;
        $total_received = 0;
        $total_cod_collection = 0;
        $total_actual_weight = 0;
        $total_avg_actual_weight = 0;
        $total_avg_rev_actual_weight = 0;
        $total_chargeable_weight = 0;
        $total_avg_chargeable_weight = 0;
        $total_avg_rev_chargeable_weight = 0;
        $total_revenue_wo_gst = 0;
        $total_avg_revenue = 0;
        $total_avg_cash_collection = 0;
        $total_rev_on_cash_collection = 0;
        $serial_number_hubs = 0;
        $booked = 0;
        $received = 0;
        $revenue_wo_gst = 0;
        $cod_collection = 0;
        $actual_weight = 0;
        $chargeable_weight = 0;


        foreach ($hubs as $hub) {

            $booked = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                    });
            })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->whereBetween('created_at',[$date_from,$date_to])->select('shipments.id')->get()->count();
            $received = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                    });
            })->whereExists(function ($query) use ($date_from, $date_to) {
                $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$date_from, $date_to])
                    ->where('shipper_status_id', 2);
            })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->select('shipments.id')->get()->count();
            if($booked > 0 || $received > 0){
                $shipments_data = array();
                $shipments_data = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                    $query->from('user_shipping_infos')
                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                        ->whereExists(function($sub_query) use ($hub) {
                            $sub_query->from('cities')
                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                ->where('cities.id', $hub->id);
                        });
                })->whereExists(function ($query) use ($date_from, $date_to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$date_from, $date_to])
                        ->where('shipper_status_id', 2);
                })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                $cod_collection = $shipments_data->cod_collection;
                $actual_weight = $shipments_data->actual_weight;
                $chargeable_weight = $shipments_data->chargeable_weight;
                $row = array();
                $avg_revenue = ($received != 0)? $revenue_wo_gst/$received:0;
                $avg_cash_collection = ($received != 0)? $cod_collection/$received:0;
                $rev_on_cash_collection = (($avg_cash_collection != 0)? $avg_revenue/$avg_cash_collection:0)*100;
                //changes add columns
                $avg_actual_weight = ($received != 0)? $actual_weight/$received:0;
                $avg_rev_actual_weight = ($actual_weight != 0 && $revenue_wo_gst != 0)? $revenue_wo_gst/$actual_weight:0;
                $avg_chargeable_weight = ($received != 0)? $chargeable_weight/$received:0;
                $avg_rev_chargeable_weight = ($chargeable_weight != 0 && $revenue_wo_gst != 0)? $revenue_wo_gst/$chargeable_weight:0;

                //end changes
                $row['serials'] = $serial_number_hubs;
                $row['hub'] = $hub->name;
                $row['booked'] = number_format($booked);
                $row['received'] = number_format($received);
                $row['revenue_wo_gst'] = number_format($revenue_wo_gst);
                $avg_rev = round($avg_revenue);
                $row['average_revenue'] = number_format($avg_rev);
                $row['actual_weight'] =number_format($actual_weight);
                $string_avg_actual_weight = (string) $avg_actual_weight;
                $row['avg_actual_weight'] = number_format((float)$string_avg_actual_weight,2,'.','');
                $row['avg_rev_actual_weight'] = round($avg_rev_actual_weight);

                $row['chargeable_weight'] = number_format($chargeable_weight);
                $string_avg_chargeable_weight = (string) $avg_chargeable_weight;
                $row['avg_chargeable_weight'] = number_format((float)$string_avg_chargeable_weight,2,'.','');
                $row['avg_rev_chargeable_weight'] = round($avg_rev_chargeable_weight);
                $row['cod_collection'] = number_format($cod_collection);

                $avg_cc = round($avg_cash_collection);
                $row['average_cash_collection'] = number_format($avg_cc);
                $rev_occ =   round($rev_on_cash_collection);
                $row['revenue_cash_collection'] = number_format($rev_occ).'%';
                $sort_support_array[] = $received;

                $sorted_details_array[] = $row;
                $total_booked += $booked;
                $total_received += $received;
                $total_revenue_wo_gst += $revenue_wo_gst;
                $total_cod_collection += $cod_collection;
                $total_actual_weight += $actual_weight;

                $total_chargeable_weight += $chargeable_weight;

            }
        }
        $total_avg_revenue = ($total_received != 0) ? $total_revenue_wo_gst / $total_received:0;
        $total_avg_actual_weight = ($total_received != 0) ? $total_actual_weight / $total_received:0;
        $total_avg_rev_actual_weight = ($total_actual_weight != 0) ? $total_revenue_wo_gst / $total_actual_weight:0;
        $total_avg_chargeable_weight = ($total_received != 0) ? $total_chargeable_weight / $total_received:0;
        $total_avg_rev_chargeable_weight = ($total_chargeable_weight != 0) ? $total_revenue_wo_gst / $total_chargeable_weight:0;
        $total_avg_cash_collection = ($total_received != 0) ? $total_cod_collection / $total_received:0;
        $total_rev_on_cash_collection = ($total_cod_collection != 0) ? $total_revenue_wo_gst / $total_cod_collection:0;
        $total_rev_on_cash_collection = $total_rev_on_cash_collection * 100;
        array_multisort($sort_support_array, SORT_DESC, $sorted_details_array);
        $counter = 1;
        foreach ($sorted_details_array as $item) {
            $item['serials'] = $counter;
            $details[] = $item;
            $counter++;
        }
        $details[] = ['Grand Total','Origin '.$only_date, number_format($total_booked), number_format($total_received), number_format($total_revenue_wo_gst),number_format($total_avg_revenue),number_format($total_actual_weight),number_format((float) $total_avg_actual_weight,2,'.',''),round($total_avg_rev_actual_weight),number_format($total_chargeable_weight),number_format((float) $total_avg_chargeable_weight,2,'.',''), round($total_avg_rev_chargeable_weight), number_format($total_cod_collection),number_format($total_avg_cash_collection),number_format($total_rev_on_cash_collection).'%'];
        $details_shipper['header'] = ['S. No.','Origin','Sales Person','Shipper Name(s) (Account No(s))', 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Avg/Parcel Revenue','Actual Weight','Avg. Actual Weight/Parcel','Avg. Revenue On Actual Weight','Chargeable Weight','Avg. Chargeable Weight/Parcel','Avg. Revenue On Chargeable Weight','Collection Amount','Avg. Amount Collection','% Rev. on Amount Collection'];

        $serial_number_shippers = 0;

        foreach($hubs as $hub){

            $pickup_request_shippers = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($hub) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                    });
            })->whereExists(function ($query) use ($date_from, $date_to) {
                $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$date_from, $date_to]);
            })->where('shipments.user_id', '!=', 1690);

            if($pickup_request_shippers->exists()) {
                $pickup_request_shippers_ids = $pickup_request_shippers->pluck('user_id')->toArray();

                $pickup_request_shippers_ids = array_unique($pickup_request_shippers_ids);

                $shippers[$hub->id] = DB::connection('reports')->table('users')->select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->get();

                }

            }

        $shipper_sort_support_array = array();
        $total_shipper_booked = 0;
        $total_shipper_received = 0;
        $total_shipper_cod_collection = 0;
        $total_shipper_actual_weight = 0;
        $total_shipper_avg_actual_weight = 0;
        $total_shipper_avg_rev_actual_weight = 0;
        $total_shipper_chargeable_weight = 0;
        $total_shipper_avg_chargeable_weight = 0;
        $total_shipper_avg_rev_chargeable_weight = 0;
        $total_shipper_revenue_wo_gst = 0;
        $total_shipper_avg_revenue = 0;
        $total_shipper_avg_cash_collection = 0;
        $total_shipper_rev_on_cash_collection = 0;

        if(count($shippers) > 0) {
            foreach ($shippers as $origin => $shipper_row) {
                foreach ($shipper_row as $shipper) {

                    $shipper_booked = 0;
                    $shipper_received = 0;
                    $shipper_rev_wo_gst = 0;
                    $shipper_cod = 0;
                    $shipper_actual_weight = 0;
                    $shipper_chargeable_weight = 0;
                    $origin_name =  DB::connection('reports')->table('cities')->where('id', $origin)->select('name')->first();
                    $origin_name = $origin_name->name;
                    $shipper_sales_person_name = '';
                    $shipper_sales_person = SalePersonTag::where('user_id', $shipper->id)->where('status', 0);
                    if ($shipper_sales_person->exists()) {
                        $shipper_sales_person = $shipper_sales_person->first();
                        $shipper_sales_person_name = Admin::find($shipper_sales_person->admin_id)->name;
                    }


                    $shipper_booked = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                        ->whereExists(function ($query) use ($origin) {
                            $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function ($sub_query) use ($origin) {
                                    $sub_query->from('cities')
                                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                        ->where('cities.id',$origin);
                                });
                        })->whereBetween('created_at', [$date_from, $date_to])->where('shipments.packaging_material_request', '=', 0)->count();
                    $shipper_received = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                        ->whereExists(function ($query) use ($origin) {
                            $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function ($sub_query) use ($origin) {
                                    $sub_query->from('cities')
                                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                        ->where('cities.id',$origin);
                                });
                        })->whereExists(function ($query) use ($date_from, $date_to) {
                            $query->from('shipments_journey')
                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$date_from, $date_to])
                                ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', '=', 0)->count();
                    if($shipper_booked > 0 || $shipper_received > 0){
                        $shipments_data = array();
                        $shipments_data = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                            ->whereExists(function ($query) use ($origin) {
                                $query->from('user_shipping_infos')
                                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                    ->whereExists(function ($sub_query) use ($origin) {
                                        $sub_query->from('cities')
                                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                            ->where('cities.id',$origin);
                                    });
                            })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                    ->whereBetween('created_at', [$date_from, $date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                        $shipper_rev_wo_gst = $shipments_data->revenue_wo_gst;
                        $shipper_cod = $shipments_data->cod_collection;
                        $shipper_actual_weight = $shipments_data->actual_weight;
                        $shipper_chargeable_weight = $shipments_data->chargeable_weight;

                    }

                    $shipper_avg_revenue = ($shipper_received != 0) ? $shipper_rev_wo_gst / $shipper_received : 0;
                    $shipper_avg_cash_collection = ($shipper_received != 0) ? $shipper_cod / $shipper_received : 0;
                    $shipper_rev_on_cash_collection = (($shipper_avg_cash_collection != 0) ? $shipper_avg_revenue / $shipper_avg_cash_collection : 0) * 100;

                    //changes add columns
                    $shipper_avg_actual_weight = ($shipper_received != 0) ? $shipper_actual_weight / $shipper_received : 0;
                    $shipper_avg_rev_actual_weight = ($shipper_actual_weight != 0 && $shipper_rev_wo_gst != 0) ? $shipper_rev_wo_gst / $shipper_actual_weight : 0;
                    $shipper_avg_chargeable_weight = ($shipper_received != 0) ? $shipper_chargeable_weight / $shipper_received : 0;
                    $shipper_avg_rev_chargeable_weight = ($shipper_chargeable_weight != 0 && $shipper_rev_wo_gst != 0) ? $shipper_rev_wo_gst / $shipper_chargeable_weight : 0;

                    //end
                    $shipper_row = array();
                    $shipper_row['shipper_serial'] = $serial_number_shippers;
                    $shipper_row['origin_name'] = $origin_name;
                    $shipper_row['shipper_sale_person'] = $shipper_sales_person_name;
                    $shipper_row['name'] = $shipper->name . ' (' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . ')';
                    $shipper_row['shipper_booked'] = number_format($shipper_booked);
                    $shipper_row['shipper_received'] = number_format($shipper_received);
                    $shipper_row['shipper_rev_wo_gst'] = number_format($shipper_rev_wo_gst);
                    $s_avg_revenue = round($shipper_avg_revenue);
                    $shipper_row['shipper_avg_revenue'] = number_format($s_avg_revenue);
                    $shipper_row['shipper_actual_weight'] = number_format($shipper_actual_weight);
                    $shipper_row['shipper_avg_actual_weight'] = number_format((float)$shipper_avg_actual_weight, 2, '.', '');
                    $shipper_row['$shipper_avg_rev_actual_weight'] = round($shipper_avg_rev_actual_weight);
                    $shipper_row['shipper_chargeable_weight'] = number_format($shipper_chargeable_weight);
                    $shipper_row['shipper_avg_chargeable_weight'] = number_format((float)$shipper_avg_chargeable_weight, 2, '.', '');
                    $shipper_row['shipper_avg_rev_chargeable_weight'] = round($shipper_avg_rev_chargeable_weight);
                    $shipper_row['shipper_cod'] = number_format($shipper_cod);

                    $avg_cash_coll = round($shipper_avg_cash_collection);
                    $shipper_row['shipper_avg_cc'] = number_format($avg_cash_coll);
                    $avg_rev_cc = round($shipper_rev_on_cash_collection);
                    $shipper_row['shipper_rcc'] = number_format($avg_rev_cc) . '%';

                    $sorted_shipper_array[] = $shipper_row;
                    $shipper_sort_support_array[] = $shipper_received;
                    $total_shipper_booked += $shipper_booked;
                    $total_shipper_received += $shipper_received;
                    $total_shipper_cod_collection += $shipper_cod;
                    $total_shipper_revenue_wo_gst += $shipper_rev_wo_gst;
                    $total_shipper_actual_weight += $shipper_actual_weight;
//                $total_shipper_avg_actual_weight += $shipper_avg_actual_weight;
//                $total_shipper_avg_rev_actual_weight += $shipper_avg_rev_actual_weight;
                    $total_shipper_chargeable_weight += $shipper_chargeable_weight;
//                $total_shipper_avg_chargeable_weight += $shipper_avg_chargeable_weight;
//                $total_shipper_avg_rev_chargeable_weight += $shipper_avg_rev_chargeable_weight;
//                $total_shipper_avg_revenue += $s_avg_revenue;

//                $total_shipper_rev_on_cash_collection += $avg_rev_cc;


//                $serial_number_shippers++;
                }
            }
        }

        $total_shipper_avg_revenue = ($total_shipper_received != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_received:0;
        $total_shipper_avg_actual_weight = ($total_shipper_received != 0) ? $total_shipper_actual_weight / $total_shipper_received:0;
        $total_shipper_avg_rev_actual_weight = ($total_shipper_actual_weight != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_actual_weight:0;
        $total_shipper_avg_chargeable_weight = ($total_shipper_received != 0) ? $total_shipper_chargeable_weight / $total_shipper_received:0;
        $total_shipper_avg_rev_chargeable_weight = ($total_shipper_chargeable_weight != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_chargeable_weight:0;
        $total_shipper_avg_cash_collection = ($total_shipper_received != 0) ? $total_shipper_cod_collection / $total_shipper_received:0;

        $total_shipper_rev_on_cash_collection = ($total_shipper_cod_collection != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_cod_collection:0;
        $total_shipper_rev_on_cash_collection = $total_shipper_rev_on_cash_collection * 100;
        array_multisort($shipper_sort_support_array, SORT_DESC, $sorted_shipper_array);
        $shipper_counter = 1;
        foreach ($sorted_shipper_array as $shipper) {
            $shipper['shipper_serial'] = $shipper_counter;
            $details_shipper[] = $shipper;
            $shipper_counter++;
        }
        $details_shipper[] = ['Grand Total','','','DSR '.$only_date, number_format($total_shipper_booked), number_format($total_shipper_received), number_format($total_shipper_revenue_wo_gst),number_format($total_shipper_avg_revenue),number_format($total_shipper_actual_weight),number_format((float) $total_shipper_avg_actual_weight,2,'.',''),round($total_shipper_avg_rev_actual_weight),number_format($total_shipper_chargeable_weight),number_format((float) $total_shipper_avg_chargeable_weight,2,'.',''),round($total_shipper_avg_rev_chargeable_weight), number_format($total_shipper_cod_collection),number_format($total_shipper_avg_cash_collection),number_format($total_shipper_rev_on_cash_collection).'%'];

        $shipping_mode_wise_header['header'] = ['S. No.','Shipping Mode', 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Avg/Parcel Revenue','Actual Weight','Avg Actual Weight/Parcel','Avg Revenue on Actual Weight','Chargeable Weight','Avg. Chargeable Weight/Parcel','Avg. Revenue On Chargeable Weight','Collection Amount','Avg. Amount Collection','% Rev. on Amount Collection'];

        $shipping_mode_wise_details = self::daily_pickup_sales_shipping_mode_wise($date_from,$date_to,FALSE, NULL, $rm_id);

        $total_shipping_booked = 0;
        $total_shipping_received = 0;
        $total_shipping_revenue_wo_gst = 0;
        $total_shipping_avg_parcel_revenue = 0;
        $total_shipping_actual_weight = 0;
        $total_shipping_avg_actual_weight = 0;
        $total_shipping_avg_rev_actual_weight = 0;
        $total_shipping_chargeable_weight = 0;
        $total_shipping_avg_chargeable_weight = 0;
        $total_shipping_avg_rev_chargeable_weight = 0;
        $total_shipping_collection_amount = 0;
        $total_shipping_avg_amount_collection = 0;
        $total_shipping_rev_amount_collection = 0;
        $total_avg_revenue = 0;
        $total_avg_actual_weight = 0;
        $total_avg_rev_actual_weight = 0;
        $total_avg_chargeable_weight = 0;
        $shipping_mode_wise_data = array();
        foreach($shipping_mode_wise_details as $shipping_mode_total){
            $total_shipping_booked += $shipping_mode_total['booked'];
            $total_shipping_received += $shipping_mode_total['received'];
            $total_shipping_revenue_wo_gst += $shipping_mode_total['revenue_wo_gst'];
            $total_shipping_avg_parcel_revenue  += $shipping_mode_total['avg_parcel_rev'];
            $total_shipping_actual_weight += $shipping_mode_total['actual_weight'];
            $total_shipping_avg_actual_weight += $shipping_mode_total['avg_actual_weight'];
            $total_shipping_avg_rev_actual_weight += $shipping_mode_total['avg_rev_actual_weight'];
            $total_shipping_chargeable_weight += $shipping_mode_total['chargeable_weight'];
            $total_shipping_avg_chargeable_weight += $shipping_mode_total['avg_chargeable_weight'];
            $total_shipping_avg_rev_chargeable_weight += $shipping_mode_total['avg_rev_chargeable_weight'];
            $total_shipping_collection_amount += $shipping_mode_total['collection_amount'];
            $total_shipping_avg_amount_collection += $shipping_mode_total['avg_amount_collection'];
            $total_shipping_rev_amount_collection += $shipping_mode_total['revenue_amount_collection'];

            $shipping_mode_wise_data[] = [$shipping_mode_total['serial'],$shipping_mode_total['mode'],number_format(round($shipping_mode_total['booked'])),$shipping_mode_total['received'],number_format($shipping_mode_total['revenue_wo_gst']),number_format($shipping_mode_total['avg_parcel_rev']) ,number_format($shipping_mode_total['actual_weight']),number_format($shipping_mode_total['avg_actual_weight']),round($shipping_mode_total['avg_rev_actual_weight']),number_format($shipping_mode_total['chargeable_weight']),number_format($shipping_mode_total['avg_chargeable_weight']),round($shipping_mode_total['avg_rev_chargeable_weight']),$shipping_mode_total['collection_amount'],$shipping_mode_total['avg_amount_collection'],round( $shipping_mode_total['revenue_amount_collection']) .'%'];
        }

        $total_shipping_avg_parcel_revenue = ($total_shipping_received != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_received:0;
        $total_avg_actual_weight = ($total_shipping_received != 0) ? $total_shipping_actual_weight / $total_shipping_received:0;
        $total_avg_rev_actual_weight = ($total_shipping_actual_weight != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_actual_weight:0;
        $total_avg_chargeable_weight = ($total_shipping_received != 0) ? $total_shipping_chargeable_weight / $total_shipping_received:0;
        $total_avg_rev_chargeable_weight = ($total_shipping_chargeable_weight != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_chargeable_weight:0;
        $total_avg_cash_collection = ($total_shipping_received != 0) ? $total_shipping_collection_amount / $total_shipping_received:0;
        $total_rev_on_cash_collection = ($total_shipping_collection_amount != 0) ? $total_shipping_revenue_wo_gst / $total_shipping_collection_amount:0;
        $total_rev_on_cash_collection = $total_rev_on_cash_collection * 100;

        $shipping_mode_wise_footer[] = ['Grand Total.',' ',number_format(round($total_shipping_booked)),$total_shipping_received,number_format(round($total_shipping_revenue_wo_gst)),number_format($total_shipping_avg_parcel_revenue) ,number_format($total_shipping_actual_weight),number_format($total_avg_actual_weight),number_format($total_avg_rev_actual_weight),number_format($total_shipping_chargeable_weight),number_format($total_avg_chargeable_weight),number_format($total_avg_rev_chargeable_weight),$total_shipping_collection_amount,number_format($total_avg_cash_collection),number_format($total_rev_on_cash_collection) .'%'];

        $shipping_mode_wise_details = array_merge($shipping_mode_wise_header,$shipping_mode_wise_data,$shipping_mode_wise_footer);

        $spreadsheet = new Spreadsheet();
        $cell_st =[
            'font' =>['bold' => true],
            'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                )
            ),
        ];
        $total_cell_st =[
            'font' =>['bold' => true],
            'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            )
        ];
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getDefaultColumnDimension()->setWidth(20);
        $sheet->getStyle('D3:R3')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
        $sheet->getStyle('D3:R3')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D3:R3')->applyFromArray($cell_st);
        $sheet->fromArray($shipping_mode_wise_details,NULL,'D3',true);
        $sheet->getStyle('D8:R8')->applyFromArray($total_cell_st);
        $count_shipping_mode = count($shipping_mode_wise_details);
        $count_shipping_mode += 7;
        /*  $count_hubs = count($hubs);
          $count_hub_rows = count($details);
          $count_hub_rows += 2;

          $count_hubs += 3;
          $total_shipper_rows = count($details_shipper);
          $total_shipper_rows += $count_hubs;
          $total_shipper_rows = $total_shipper_rows - 1;*/

        //details
        $total_style_cell = "D$count_shipping_mode".":R".$count_shipping_mode;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->getStyle($total_style_cell)->getAlignment()->setWrapText(true);
        $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);
        $sheet->getStyle($total_style_cell)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
        $shipper_cell = 'D'.$count_shipping_mode; //D13
        $shipper_last_cell = 'R'.$count_shipping_mode; //R13
        $sheet->fromArray($details,NULL,$shipper_cell,true);
        $count_details = count($details);
        $total_hub = $count_shipping_mode + $count_details-1;
        $total_style_cell = "D$total_hub".":R".$total_hub;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $count_details = $count_details + $count_shipping_mode + 4;

        //shipper_details
        $total_style_cell = "D$count_details".":T".$count_details;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->getStyle($total_style_cell)->applyFromArray($cell_st);
        $sheet->getStyle($total_style_cell)->getAlignment()->setWrapText(true);


        $sheet->getStyle($total_style_cell)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
        $details_shipper_cell = 'D'.$count_details;
        $details_shipper_last_cell = 'R'.$count_details;
        // $sheet->fromArray($details_shipper,NULL,'D15',true);
        $sheet->fromArray($details_shipper,NULL,$details_shipper_cell,true);
        $total_shipper_count = count($details_shipper);
        $total_shipper = $total_shipper_count + $count_details -1;
        $total_style_cell = "D$total_shipper".":T".$total_shipper;
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->setTitle('Daily Pickup Sales Report');

        /* $hub_all_rows = "D3".":R".$count_hub_rows;

         $sheet->getStyle($hub_all_rows)->applyFromArray($cell_st);

         $shipper_style_cell = "D$count_hubs".":T".$count_hubs;
         $shipper_all_rows = "D$count_hubs".":T".$total_shipper_rows;*/

        /* $total_style_cell = "D$count_hub_rows".":R".$count_hub_rows;
         $total_shipper_style_cell = "D$total_shipper_rows".":T".$total_shipper_rows;
         $sheet->getStyle($shipper_style_cell)
             ->getFill()
             ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
             ->getStartColor()
             ->setRGB('CECECE');
 //        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
         $sheet->getStyle($shipper_all_rows)->applyFromArray($cell_st);*/

        /* $sheet->getStyle($shipper_style_cell)->getAlignment()->setWrapText(true);*/
//        $sheet->getStyle($total_shipper_style_cell)->applyFromArray($total_cell_st);

        /* $set_shipper_actual_number_format = 'K'.$count_hubs.':K'.$total_shipper_rows;
         $set_shipper_chargeable_number_format = 'N'.$count_hubs.':N'.$total_shipper_rows;*/
        /* $sheet->getStyle($set_shipper_actual_number_format)->getNumberFormat()->setFormatCode('0.00');
         $sheet->getStyle($set_shipper_chargeable_number_format)->getNumberFormat()->setFormatCode('0.00');*/


        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');

        $file_name_without_path = '';

        $file_name_without_path = "reports/daily_pickup_sales_report_".$date_file_name.'_'.$rm_id.'_'.$time_string.".xlsx";
        $file_name = public_path() .'/'.$file_name_without_path ;


        $writer->save($file_name);


        return url('/').'/'.$file_name_without_path;

}

    static public function daily_pickup_sales_shipping_mode_wise($date_from,$date_to, $sales_tagging = FALSE, $sale_person_id = NULL, $rm_id = NULL){

        $data = array();
        $shipping_modes = ShippingMode::all();


        if($sales_tagging == true){

            $tagged_shippers = DB::connection('reports')->table('sale_person_tags')->where('admin_id', $sale_person_id)->where('status', 0)->select('user_id')->pluck('user_id')->toArray();

            $assigned_admins = DB::connection('reports')->table('multiple_sale_leads')->leftjoin('multiple_sale_taggings as mst', 'mst.lead_id', '=', 'multiple_sale_leads.id')
                ->leftjoin('sale_person_tags as spt', 'spt.admin_id', '=', 'mst.admin_id')
                ->where('multiple_sale_leads.admin_id', $sale_person_id)
                ->where('spt.status', 0)
                ->whereNotNull('spt.user_id')->select('spt.user_id')->pluck('spt.user_id')->toArray();
            if($assigned_admins) {
                $tagged_shippers = array_merge($tagged_shippers, $assigned_admins);
            }

            $serial = 1;
            foreach($shipping_modes as $mode){

                $booked = 0;
                $received = 0;
                $cod_collection = 0;
                $actual_weight = 0;
                $chargeable_weight = 0;
                $revenue_wo_gst = 0;

                $booked= DB::connection('reports')->table('shipments')
                    ->whereBetween('shipments.created_at',[$date_from,$date_to])
                    ->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id','!=',1690)
                    ->where('shipments.shipping_mode_id', $mode->id)->whereIn('user_id', $tagged_shippers)->count();


                $received = DB::connection('reports')->table('shipments')
                    ->whereExists(function ($query) use ($date_from, $date_to) {
                        $query->from('shipments_journey')
                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                            ->whereBetween('created_at', [$date_from, $date_to])
                            ->where('shipper_status_id', 2);
                    })->whereIn('user_id', $tagged_shippers)->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->where('shipments.shipping_mode_id', $mode->id)->count();

                $shipments_data = array();
                $shipments_data = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($date_from, $date_to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$date_from, $date_to])
                        ->where('shipper_status_id', 2);
                })->whereIn('user_id', $tagged_shippers)->where('shipments.packaging_material_request', '=', 0)->where('shipments.shipping_mode_id', $mode->id)->where('shipments.user_id', '!=', 1690)->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();


                $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                $actual_weight = $shipments_data->actual_weight;
                $chargeable_weight = $shipments_data->chargeable_weight;
                $cod_collection = $shipments_data->cod_collection;

                $data[$mode->id]['serial'] = $serial;
                $data[$mode->id]['mode'] = $mode->mode;
                $data[$mode->id]['booked'] = $booked;
                $data[$mode->id]['received'] = $received;
                $data[$mode->id]['revenue_wo_gst'] = $revenue_wo_gst;
                $data[$mode->id]['avg_parcel_rev'] = ($received != 0) ? $revenue_wo_gst/$received:0;
                $data[$mode->id]['actual_weight'] = $actual_weight;
                $data[$mode->id]['avg_actual_weight'] = ($received != 0) ? $actual_weight / $received : 0;
                $data[$mode->id]['avg_rev_actual_weight'] = ($actual_weight != 0) ? $revenue_wo_gst / $actual_weight:0;
                $data[$mode->id]['chargeable_weight'] = $chargeable_weight;
                $data[$mode->id]['avg_chargeable_weight'] = ($received != 0) ? $chargeable_weight / $received:0;
                $data[$mode->id]['avg_rev_chargeable_weight'] = ($chargeable_weight != 0) ? $revenue_wo_gst / $chargeable_weight:0;
                $data[$mode->id]['collection_amount'] = $cod_collection;
                $data[$mode->id]['avg_amount_collection'] = ($received != 0) ? $cod_collection / $received : 0;
                $data[$mode->id]['revenue_amount_collection'] = ($cod_collection != 0) ? $revenue_wo_gst / $cod_collection : 0;
                $data[$mode->id]['revenue_amount_collection'] = $data[$mode->id]['revenue_amount_collection'] * 100;
                $serial++;
            }
        }
        else if($rm_id != null){

            $assigned_hubs = AdminHub::where('admin_id', $rm_id)->pluck('hub_id')->toArray();
            $hubs = DB::connection('reports')->table('cities')->whereIn('hub_id', $assigned_hubs)->where('pickup', 1)->pluck('id')->toArray();

            $serial = 1;
            foreach($shipping_modes as $mode){

                $booked = 0;
                $received = 0;
                $cod_collection = 0;
                $actual_weight = 0;
                $chargeable_weight = 0;
                $revenue_wo_gst = 0;

                $booked= DB::connection('reports')->table('shipments')
                    ->whereExists(function ($query) use ($hubs) {
                        $query->from('user_shipping_infos')
                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                            ->whereExists(function ($sub_query) use ($hubs) {
                                $sub_query->from('cities')
                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                    ->whereIn('cities.id', $hubs);
                            });
                    })
                    ->whereBetween('shipments.created_at',[$date_from,$date_to])
                    ->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id','!=',1690)
                    ->where('shipments.shipping_mode_id', $mode->id)->select('shipments.id')->get()->count();


                $received = DB::connection('reports')->table('shipments')
                    ->whereExists(function ($query) use ($hubs) {
                        $query->from('user_shipping_infos')
                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                            ->whereExists(function ($sub_query) use ($hubs) {
                                $sub_query->from('cities')
                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                    ->whereIn('cities.id', $hubs);
                            });
                    })
                    ->whereExists(function ($query) use ($date_from, $date_to) {
                        $query->from('shipments_journey')
                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                            ->whereBetween('created_at', [$date_from, $date_to])
                            ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->where('shipments.shipping_mode_id', $mode->id)->select('shipments.id')->get()->count();

                if($booked > 0 || $received > 0){

                    $shipments_data = array();

                    $shipments_data = DB::connection('reports')->table('shipments')
                        ->whereExists(function ($query) use ($hubs) {
                            $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function ($sub_query) use ($hubs) {
                                    $sub_query->from('cities')
                                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                        ->whereIn('cities.id', $hubs);
                                });
                        })
                        ->whereExists(function ($query) use ($date_from, $date_to) {
                        $query->from('shipments_journey')
                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                            ->whereBetween('created_at', [$date_from, $date_to])
                            ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->where('shipments.shipping_mode_id', $mode->id)->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                    $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                    $actual_weight = $shipments_data->actual_weight;
                    $chargeable_weight = $shipments_data->chargeable_weight;
                    $cod_collection = $shipments_data->cod_collection;
                }

                $data[$mode->id]['serial'] = $serial;
                $data[$mode->id]['mode'] = $mode->mode;
                $data[$mode->id]['booked'] = $booked;
                $data[$mode->id]['received'] = $received;
                $data[$mode->id]['revenue_wo_gst'] = $revenue_wo_gst;
                $data[$mode->id]['avg_parcel_rev'] = ($received != 0) ? $revenue_wo_gst/$received:0;
                $data[$mode->id]['actual_weight'] = $actual_weight;
                $data[$mode->id]['avg_actual_weight'] = ($received != 0) ? $actual_weight / $received : 0;
                $data[$mode->id]['avg_rev_actual_weight'] = ($actual_weight != 0) ? $revenue_wo_gst / $actual_weight:0;
                $data[$mode->id]['chargeable_weight'] = $chargeable_weight;
                $data[$mode->id]['avg_chargeable_weight'] = ($received != 0) ? $chargeable_weight / $received:0;
                $data[$mode->id]['avg_rev_chargeable_weight'] = ($chargeable_weight != 0) ? $revenue_wo_gst / $chargeable_weight:0;
                $data[$mode->id]['collection_amount'] = $cod_collection;
                $data[$mode->id]['avg_amount_collection'] = ($received != 0) ? $cod_collection / $received : 0;
                $data[$mode->id]['revenue_amount_collection'] = ($received != 0) ? $revenue_wo_gst / $cod_collection : 0;
                $data[$mode->id]['revenue_amount_collection'] = $data[$mode->id]['revenue_amount_collection'] * 100;
                $serial++;
            }
        }
        else{

            $serial = 1;
            foreach($shipping_modes as $mode){

                $booked = 0;
                $received = 0;
                $cod_collection = 0;
                $actual_weight = 0;
                $chargeable_weight = 0;
                $revenue_wo_gst = 0;

                $booked = DB::connection('reports')->table('shipments')
                ->where('shipments.packaging_material_request', 0)
                ->where('shipments.user_id', '!=', 1690)
                ->whereBetween('shipments.created_at', [$date_from, $date_to])
                ->where('shipments.shipping_mode_id', $mode->id)
                ->count();

                $received = DB::connection('reports')->table('shipments_journey')
                ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                ->where('s.packaging_material_request', 0)
                ->where('s.user_id', '!=', 1690)
                ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                ->where('shipments_journey.shipper_status_id', 2)
                ->where('s.shipping_mode_id', $mode->id)
                ->count();

                if($received > 0){

                    $shipments_data = array();

                    $shipments_data = DB::connection('reports')->table('shipments_journey')
                    ->join('shipments as s', 's.id', 'shipments_journey.shipment_id')
                    ->where('s.packaging_material_request', 0)
                    ->where('s.user_id', '!=', 1690)
                    ->whereBetween('shipments_journey.created_at', [$date_from, $date_to])
                    ->where('shipments_journey.shipper_status_id', 2)
                    ->where('s.shipping_mode_id', $mode->id)
                    ->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();

                    $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                    $actual_weight = $shipments_data->actual_weight;
                    $chargeable_weight = $shipments_data->chargeable_weight;
                    $cod_collection = $shipments_data->cod_collection;
                }

                $data[$mode->id]['serial'] = $serial;
                $data[$mode->id]['mode'] = $mode->mode;
                $data[$mode->id]['booked'] = $booked;
                $data[$mode->id]['received'] = $received;
                $data[$mode->id]['revenue_wo_gst'] = $revenue_wo_gst;
                $data[$mode->id]['avg_parcel_rev'] = ($received != 0) ? $revenue_wo_gst/$received:0;
                $data[$mode->id]['actual_weight'] = $actual_weight;
                $data[$mode->id]['avg_actual_weight'] = ($received != 0) ? $actual_weight / $received : 0;
                $data[$mode->id]['avg_rev_actual_weight'] = ($actual_weight != 0) ? $revenue_wo_gst / $actual_weight:0;
                $data[$mode->id]['chargeable_weight'] = $chargeable_weight;
                $data[$mode->id]['avg_chargeable_weight'] = ($received != 0) ? $chargeable_weight / $received:0;
                $data[$mode->id]['avg_rev_chargeable_weight'] = ($chargeable_weight != 0) ? $revenue_wo_gst / $chargeable_weight:0;
                $data[$mode->id]['collection_amount'] = $cod_collection;
                $data[$mode->id]['avg_amount_collection'] = ($received != 0) ? $cod_collection / $received : 0;
                $data[$mode->id]['revenue_amount_collection'] = ($cod_collection != 0) ? $revenue_wo_gst / $cod_collection : 0;
                $data[$mode->id]['revenue_amount_collection'] = $data[$mode->id]['revenue_amount_collection'] * 100;
                $serial++;
            }

        }
        return $data;
    }

    static public function daily_pickup_sales_shipping_mode_wise_kae($date_from,$date_to, $sales_tagging = FALSE, $sale_person_id = NULL){

        $data = array();
        $shipping_modes = ShippingMode::all();


        if($sales_tagging == true){

            $tagged_shippers = DB::connection('reports')->table('sale_tier_tags')->where('kam', $sale_person_id)->select('user_id')->pluck('user_id')->toArray();

//            $assigned_admins = DB::connection('reports')->table('multiple_sale_leads')->leftjoin('multiple_sale_taggings as mst', 'mst.lead_id', '=', 'multiple_sale_leads.id')
//                ->leftjoin('sale_person_tags as spt', 'spt.admin_id', '=', 'mst.admin_id')
//                ->where('multiple_sale_leads.admin_id', $sale_person_id)
//                ->where('spt.status', 0)
//                ->whereNotNull('spt.user_id')->select('spt.user_id')->pluck('spt.user_id')->toArray();
//            if($assigned_admins) {
//                $tagged_shippers = array_merge($tagged_shippers, $assigned_admins);
//            }

            $serial = 1;
            foreach($shipping_modes as $mode){

                $booked = 0;
                $received = 0;
                $cod_collection = 0;
                $actual_weight = 0;
                $chargeable_weight = 0;
                $revenue_wo_gst = 0;

                $booked= DB::connection('reports')->table('shipments')
                    ->whereBetween('shipments.created_at',[$date_from,$date_to])
                    ->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id','!=',1690)
                    ->where('shipments.shipping_mode_id', $mode->id)->whereIn('user_id', $tagged_shippers)->count();


                $received = DB::connection('reports')->table('shipments')
                    ->whereExists(function ($query) use ($date_from, $date_to) {
                        $query->from('shipments_journey')
                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                            ->whereBetween('created_at', [$date_from, $date_to])
                            ->where('shipper_status_id', 2);
                    })->whereIn('user_id', $tagged_shippers)->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->where('shipments.shipping_mode_id', $mode->id)->count();

                $shipments_data = array();
                $shipments_data = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($date_from, $date_to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$date_from, $date_to])
                        ->where('shipper_status_id', 2);
                })->whereIn('user_id', $tagged_shippers)->where('shipments.packaging_material_request', '=', 0)->where('shipments.shipping_mode_id', $mode->id)->where('shipments.user_id', '!=', 1690)->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();


                $revenue_wo_gst = $shipments_data->revenue_wo_gst;
                $actual_weight = $shipments_data->actual_weight;
                $chargeable_weight = $shipments_data->chargeable_weight;
                $cod_collection = $shipments_data->cod_collection;

                $data[$mode->id]['serial'] = $serial;
                $data[$mode->id]['mode'] = $mode->mode;
                $data[$mode->id]['booked'] = $booked;
                $data[$mode->id]['received'] = $received;
                $data[$mode->id]['revenue_wo_gst'] = $revenue_wo_gst;
                $data[$mode->id]['avg_parcel_rev'] = ($received != 0) ? $revenue_wo_gst/$received:0;
                $data[$mode->id]['actual_weight'] = $actual_weight;
                $data[$mode->id]['avg_actual_weight'] = ($received != 0) ? $actual_weight / $received : 0;
                $data[$mode->id]['avg_rev_actual_weight'] = ($actual_weight != 0) ? $revenue_wo_gst / $actual_weight:0;
                $data[$mode->id]['chargeable_weight'] = $chargeable_weight;
                $data[$mode->id]['avg_chargeable_weight'] = ($received != 0) ? $chargeable_weight / $received:0;
                $data[$mode->id]['avg_rev_chargeable_weight'] = ($chargeable_weight != 0) ? $revenue_wo_gst / $chargeable_weight:0;
                $data[$mode->id]['collection_amount'] = $cod_collection;
                $data[$mode->id]['avg_amount_collection'] = ($received != 0) ? $cod_collection / $received : 0;
                $data[$mode->id]['revenue_amount_collection'] = ($cod_collection != 0) ? $revenue_wo_gst / $cod_collection : 0;
                $data[$mode->id]['revenue_amount_collection'] = $data[$mode->id]['revenue_amount_collection'] * 100;
                $serial++;
            }
        }
//        else if($rm_id != null){
//
//            $assigned_hubs = AdminHub::where('admin_id', $rm_id)->pluck('hub_id')->toArray();
//            $hubs = DB::connection('reports')->table('cities')->whereIn('hub_id', $assigned_hubs)->where('pickup', 1)->pluck('id')->toArray();
//
//            $serial = 1;
//            foreach($shipping_modes as $mode){
//
//                $booked = 0;
//                $received = 0;
//                $cod_collection = 0;
//                $actual_weight = 0;
//                $chargeable_weight = 0;
//                $revenue_wo_gst = 0;
//
//                $booked= DB::connection('reports')->table('shipments')
//                    ->whereExists(function ($query) use ($hubs) {
//                        $query->from('user_shipping_infos')
//                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
//                            ->whereExists(function ($sub_query) use ($hubs) {
//                                $sub_query->from('cities')
//                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
//                                    ->whereIn('cities.id', $hubs);
//                            });
//                    })
//                    ->whereBetween('shipments.created_at',[$date_from,$date_to])
//                    ->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id','!=',1690)
//                    ->where('shipments.shipping_mode_id', $mode->id)->select('shipments.id')->get()->count();
//
//
//                $received = DB::connection('reports')->table('shipments')
//                    ->whereExists(function ($query) use ($hubs) {
//                        $query->from('user_shipping_infos')
//                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
//                            ->whereExists(function ($sub_query) use ($hubs) {
//                                $sub_query->from('cities')
//                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
//                                    ->whereIn('cities.id', $hubs);
//                            });
//                    })
//                    ->whereExists(function ($query) use ($date_from, $date_to) {
//                        $query->from('shipments_journey')
//                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
//                            ->whereBetween('created_at', [$date_from, $date_to])
//                            ->where('shipper_status_id', 2);
//                    })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->where('shipments.shipping_mode_id', $mode->id)->select('shipments.id')->get()->count();
//
//                if($booked > 0 || $received > 0){
//
//                    $shipments_data = array();
//
//                    $shipments_data = DB::connection('reports')->table('shipments')
//                        ->whereExists(function ($query) use ($hubs) {
//                            $query->from('user_shipping_infos')
//                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
//                                ->whereExists(function ($sub_query) use ($hubs) {
//                                    $sub_query->from('cities')
//                                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
//                                        ->whereIn('cities.id', $hubs);
//                                });
//                        })
//                        ->whereExists(function ($query) use ($date_from, $date_to) {
//                            $query->from('shipments_journey')
//                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
//                                ->whereBetween('created_at', [$date_from, $date_to])
//                                ->where('shipper_status_id', 2);
//                        })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->where('shipments.shipping_mode_id', $mode->id)->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();
//
//                    $revenue_wo_gst = $shipments_data->revenue_wo_gst;
//                    $actual_weight = $shipments_data->actual_weight;
//                    $chargeable_weight = $shipments_data->chargeable_weight;
//                    $cod_collection = $shipments_data->cod_collection;
//                }
//
//                $data[$mode->id]['serial'] = $serial;
//                $data[$mode->id]['mode'] = $mode->mode;
//                $data[$mode->id]['booked'] = $booked;
//                $data[$mode->id]['received'] = $received;
//                $data[$mode->id]['revenue_wo_gst'] = $revenue_wo_gst;
//                $data[$mode->id]['avg_parcel_rev'] = ($received != 0) ? $revenue_wo_gst/$received:0;
//                $data[$mode->id]['actual_weight'] = $actual_weight;
//                $data[$mode->id]['avg_actual_weight'] = ($received != 0) ? $actual_weight / $received : 0;
//                $data[$mode->id]['avg_rev_actual_weight'] = ($actual_weight != 0) ? $revenue_wo_gst / $actual_weight:0;
//                $data[$mode->id]['chargeable_weight'] = $chargeable_weight;
//                $data[$mode->id]['avg_chargeable_weight'] = ($received != 0) ? $chargeable_weight / $received:0;
//                $data[$mode->id]['avg_rev_chargeable_weight'] = ($chargeable_weight != 0) ? $revenue_wo_gst / $chargeable_weight:0;
//                $data[$mode->id]['collection_amount'] = $cod_collection;
//                $data[$mode->id]['avg_amount_collection'] = ($received != 0) ? $cod_collection / $received : 0;
//                $data[$mode->id]['revenue_amount_collection'] = ($received != 0) ? $revenue_wo_gst / $cod_collection : 0;
//                $data[$mode->id]['revenue_amount_collection'] = $data[$mode->id]['revenue_amount_collection'] * 100;
//                $serial++;
//            }
//        }
//        else{
//
//            $serial = 1;
//            foreach($shipping_modes as $mode){
//
//                $booked = 0;
//                $received = 0;
//                $cod_collection = 0;
//                $actual_weight = 0;
//                $chargeable_weight = 0;
//                $revenue_wo_gst = 0;
//
//                $booked= DB::connection('reports')->table('shipments')
//                    ->whereBetween('shipments.created_at',[$date_from,$date_to])
//                    ->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id','!=',1690)
//                    ->where('shipments.shipping_mode_id', $mode->id)->select('shipments.id')->get()->count();
//
//
//                $received = DB::connection('reports')->table('shipments')
//                    ->whereExists(function ($query) use ($date_from, $date_to) {
//                        $query->from('shipments_journey')
//                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
//                            ->whereBetween('created_at', [$date_from, $date_to])
//                            ->where('shipper_status_id', 2);
//                    })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->where('shipments.shipping_mode_id', $mode->id)->select('shipments.id')->get()->count();
//
//                if($booked > 0 || $received > 0){
//
//                    $shipments_data = array();
//
//                    $shipments_data = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($date_from, $date_to) {
//                        $query->from('shipments_journey')
//                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
//                            ->whereBetween('created_at', [$date_from, $date_to])
//                            ->where('shipper_status_id', 2);
//                    })->where('shipments.packaging_material_request', '=', 0)->where('shipments.user_id', '!=', 1690)->where('shipments.shipping_mode_id', $mode->id)->select(DB::connection('reports')->raw('SUM(IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)) as revenue_wo_gst'), DB::connection('reports')->raw('SUM(amount) as cod_collection'), DB::connection('reports')->raw('SUM(actual_weight) as actual_weight'), DB::connection('reports')->raw('SUM(chargeable_weight) as chargeable_weight'))->first();
//
//                    $revenue_wo_gst = $shipments_data->revenue_wo_gst;
//                    $actual_weight = $shipments_data->actual_weight;
//                    $chargeable_weight = $shipments_data->chargeable_weight;
//                    $cod_collection = $shipments_data->cod_collection;
//                }
//
//                $data[$mode->id]['serial'] = $serial;
//                $data[$mode->id]['mode'] = $mode->mode;
//                $data[$mode->id]['booked'] = $booked;
//                $data[$mode->id]['received'] = $received;
//                $data[$mode->id]['revenue_wo_gst'] = $revenue_wo_gst;
//                $data[$mode->id]['avg_parcel_rev'] = ($received != 0) ? $revenue_wo_gst/$received:0;
//                $data[$mode->id]['actual_weight'] = $actual_weight;
//                $data[$mode->id]['avg_actual_weight'] = ($received != 0) ? $actual_weight / $received : 0;
//                $data[$mode->id]['avg_rev_actual_weight'] = ($actual_weight != 0) ? $revenue_wo_gst / $actual_weight:0;
//                $data[$mode->id]['chargeable_weight'] = $chargeable_weight;
//                $data[$mode->id]['avg_chargeable_weight'] = ($received != 0) ? $chargeable_weight / $received:0;
//                $data[$mode->id]['avg_rev_chargeable_weight'] = ($chargeable_weight != 0) ? $revenue_wo_gst / $chargeable_weight:0;
//                $data[$mode->id]['collection_amount'] = $cod_collection;
//                $data[$mode->id]['avg_amount_collection'] = ($received != 0) ? $cod_collection / $received : 0;
//                $data[$mode->id]['revenue_amount_collection'] = ($cod_collection != 0) ? $revenue_wo_gst / $cod_collection : 0;
//                $data[$mode->id]['revenue_amount_collection'] = $data[$mode->id]['revenue_amount_collection'] * 100;
//                $serial++;
//            }
//
//        }
        return $data;
    }

}
