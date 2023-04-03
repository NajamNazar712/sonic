<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Rider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\Datatables\Datatables;

class AdminRetailReportController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function sales_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),239);

        $retail_centers = DB::connection('reports')->table('retail_trax_centers')->where('status', 1)->select('id','name')->get();
        $retail_franchises = DB::connection('reports')->table('retail_franchises')->where('status', 1)->select('id','name')->get();


        $cities = DB::connection('reports')->table('cities')->select('id','name')->get();
        $hubs = DB::connection('reports')->table('cities')->where('hub',1)->select('id','name')->get();
        $statuses = DB::connection('reports')->table('shipment_status')->whereNotIn('id',[1,17])->get();
        $sales_persons = DB::connection('reports')->table('admins')->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();

        $business_categories = DB::connection('reports')->table('business_categories')->select('id', 'name')->get();
        return view('admin.reports.retail.sales')->with(['retail_centers' => $retail_centers, 'retail_franchises' => $retail_franchises ,'cities'=>$cities,'hubs'=>$hubs,'statuses'=>$statuses]);
    }
    public function sales_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),240);
        }

        $from = $request->get('search_date_from');
        $from = Carbon::parse($from)->setTimeFromTimeString('05:59:59');
        $to = $request->get('search_date_to');
        $to = Carbon::parse($to)->addDay()->setTimeFromTimeString('06:00:00');

        $sales = DB::connection('reports')->table('shipments')->join('retail_shipments as rs', 'rs.shipment_id', '=','shipments.id')
            ->leftjoin('retail_users as ru','ru.id','=','rs.retail_user_id')
            ->leftjoin('retail_shipper_infos as rsi', 'rsi.id', '=', 'rs.shipper_account_no')
            ->leftJoin('retail_franchises as rf', function ($join) {
                $join->on('rf.id', '=', 'ru.category_id')
                    ->where('ru.category','=', DB::raw(1));
            })
            ->leftJoin('retail_trax_centers as rc', function ($join) {
                $join->on('rc.id', '=', 'ru.category_id')
                    ->where('ru.category','=', DB::raw(2));
            })
            ->leftJoin('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('retail_shipping_modes as rsm','rsm.id','=','rs.shipping_mode')
            ->leftjoin('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->leftjoin('retail_trax_centers as rtc', 'rtc.pickup_address_id', '=', 'shipments.pickup_address_id')
            ->leftjoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('zones as oz', 'oz.id', '=', 'oc.zone_id')
            ->leftjoin('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftjoin('zones as dz', 'dz.id', '=', 'dc.zone_id')
            ->leftjoin('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->leftjoin('retail_pickup_note_shipments as pns',function($join){
                $join->on('pns.shipment_id','=','shipments.id')
                    ->where('pns.retail_pickup_note_id','=',
                        DB::connection('reports')->raw('(select max(retail_pickup_note_id) from retail_pickup_note_shipments where retail_pickup_note_shipments.shipment_id = shipments.id)'));
            })
//            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->join('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('retail_pending_payment_shipments as pps', function ($join) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.id','=',
                        DB::connection('reports')->raw('(select max(id) from retail_pending_payment_shipments where retail_pending_payment_shipments.shipment_id = shipments.id and retail_pending_payment_shipments.type != 2)'));
            })
            ->leftJoin('retail_done_payment_shipments as dps', function ($join) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.id','=',
                        DB::connection('reports')->raw('(select max(id) from retail_done_payment_shipments where retail_done_payment_shipments.shipment_id = shipments.id and retail_done_payment_shipments.type != 2)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37) and shipments_journey.verification = 1)'));
            })
            ->leftjoin('products as p','p.id','=','rs.product_type_id')
            ->leftjoin('retail_references as rref','rref.shipment_id','=','shipments.id')
            ->leftjoin('shipment_items as si','si.shipment_id','=','rs.shipment_id')
            ->select('p.product_name as category','shipments.id as shipment_id','shipments.tracking_number','shipments.tracking_number as tracking_number_link', 'ru.name as booked_by', 'ru.category as retail_category','ru.id as booked_by_id', 'rsi.shipper_name', 'rf.id as franchise_account_id','rf.name as franchise', 'rc.id as retail_account_id','rc.name as retail_center','ss.name as current_status','rsm.name as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','h.name as hub', 'oz.name as origin_zone', 'dz.name as destination_zone','shipments.amount as collection_amount','sps.name as payment_status','pps.amount as p_collection_amount','shipments.actual_weight','rs.weight_charges','rs.cash_handling_charges','rs.fuel_surcharge','rs.total_charges as total_charges', 'rs.gst as gst','pps.payable as p_net_payable','dps.amount as d_collection_amount','dps.payable as d_net_payable','dr.created_at as delivered_or_returned', 'dps.retail_done_payment_id as payment_id', 'shipments.shipper_status_id as shipment_status' , 'dr.shipper_status_id as dr_status_id', 'pns.retail_pickup_note_id as pncc_id','rtc.name as retail_trax_center_name', 'rref.ref as retail_reference','rf.discount as franchise_discount','rf.insurance as franchise_insurance','rtc.discount as trax_discount','rtc.insurance as trax_insurance','rs.discount as discount_amount','rs.insurance_charges as insurance_charges','rs.packaging_charges as packaging_charges','si.price as product_value')
            ->whereNotIn('shipments.shipper_status_id',[1,17])
            ->whereBetween('sj.created_at', [$from,$to])
            ->where('shipments.shipment_type', 2);

            $from_id = DB::connection('reports')->table('shipments_journey')->select('id')->where('created_at', '>=', $from);
            if ($from_id->exists()) {
                $from_id = $from_id->first()->id;

                $to_id = DB::connection('reports')->table('shipments_journey')->select(DB::raw('MAX(id) as id'))->where('created_at', '>=', $from)->where('created_at', '<=', $to);

                if ($to_id->exists()) {
                    $to_id = $to_id->first()->id;

                    $sales->where('sj.id', '>=', $from_id)
                        ->where('sj.id', '<=', $to_id);
                }
            }
        $datatable = Datatables::of($sales)
            ->addColumn('attempts', function($shipment){
                $out_for_delivery = DB::connection('reports')->table('shipments_journey')->where('shipment_id',$shipment->shipment_id)->where('shipper_status_id',5)->count();
                return $out_for_delivery;
            })

            ->editColumn('booked_by_id', function ($shipment) {
                return str_pad($shipment->booked_by_id, 6, '0', STR_PAD_LEFT);
            })

            ->editColumn('total_charges', function($shipment){
                return number_format($shipment->total_charges, 2);
            })
            ->editColumn('gst', function($shipment){
                return number_format($shipment->gst, 2);
            })
            ->editColumn('p_net_payable', function($shipment){
                return number_format($shipment->p_net_payable, 2);
            })
            ->editColumn('d_net_payable', function($shipment){
                return number_format($shipment->d_net_payable, 2);
            })
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('collection_amount', function($shipment){
                return number_format($shipment->collection_amount);
            })
            ->addColumn('franchise_center', function ($shipment) {
                if($shipment->retail_category){

                    if ($shipment->retail_category == 2) {
                        return $shipment->retail_center;
                    }
                    else {
                        return $shipment->franchise;
                    }
                }
                else{
                    return $shipment->retail_trax_center_name;
                }
            })
            ->addColumn('estimated_charges',function($sale){
                $estimated = '';
                $estimated = (($sale->weight_charges != null)? $sale->weight_charges:0) + (($sale->fuel_surcharge != null)? $sale->fuel_surcharge:0);
                return number_format((float)$estimated, 2);
            })
            ->editColumn('p_net_payable',function($sale){
                $payable = '';
                if($sale->p_net_payable != null){
                    $payable = $sale->p_net_payable;
                }else if($sale->d_net_payable != null){
                    $payable = $sale->d_net_payable;
                }
                return number_format((float)$payable, 2);
            });

        if($tracking = $request->get('search_tracking')){
            $datatable->where('shipments.tracking_number', '=', $tracking);
        }
        if($center = $request->get('search_retail_center')){
            $datatable->where('rf.id', '=', $center);
        }
        if($franchise = $request->get('search_retail_franchise')){
            $datatable->where('rf.id', '=', $franchise);
        }
        if($origin = $request->get('search_origin')){
            $datatable->where('oc.id', '=', $origin);
        }
        if($destination = $request->get('search_destination')){
            $datatable->where('dc.id', '=', $destination);
        }
        if($hub = $request->get('search_hub')){
            $datatable->where('h.id', '=', $hub);
        }
        if($status = $request->get('search_status')){
            $datatable->where('ss.id', '=', $status);
        }
        return $datatable->make(true);
    }

    public static function retail_sales_report($report_type){

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

        $sales = DB::connection('reports')->table('shipments')->join('retail_shipments as rs', 'rs.shipment_id', '=','shipments.id')
            ->leftjoin('retail_users as ru','ru.id','=','rs.retail_user_id')
            ->leftjoin('retail_shipper_infos as rsi', 'rsi.id', '=', 'rs.shipper_account_no')
            ->leftJoin('retail_franchises as rf', function ($join) {
                $join->on('rf.id', '=', 'ru.category_id')
                    ->where('ru.category','=', DB::raw(1));
            })
            ->leftJoin('retail_trax_centers as rc', function ($join) {
                $join->on('rc.id', '=', 'ru.category_id')
                    ->where('ru.category','=', DB::raw(2));
            })
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->join('retail_shipping_modes as rsm','rsm.id','=','rs.shipping_mode')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->leftjoin('retail_trax_centers as rtc', 'rtc.pickup_address_id', '=', 'shipments.pickup_address_id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('zones as oz', 'oz.id', '=', 'oc.zone_id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftjoin('zones as dz', 'dz.id', '=', 'dc.zone_id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->leftjoin('retail_pickup_note_shipments as pns',function($join){
                $join->on('pns.shipment_id','=','shipments.id')
                    ->where('pns.retail_pickup_note_id','=',
                        DB::connection('reports')->raw('(select max(retail_pickup_note_id) from retail_pickup_note_shipments where retail_pickup_note_shipments.shipment_id = shipments.id)'));
            })
//            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('retail_pending_payment_shipments as pps', function ($join) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.id','=',
                        DB::connection('reports')->raw('(select max(id) from retail_pending_payment_shipments where retail_pending_payment_shipments.shipment_id = shipments.id and retail_pending_payment_shipments.type != 2)'));
            })
            ->leftJoin('retail_done_payment_shipments as dps', function ($join) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.id','=',
                        DB::connection('reports')->raw('(select max(id) from retail_done_payment_shipments where retail_done_payment_shipments.shipment_id = shipments.id and retail_done_payment_shipments.type != 2)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37) and shipments_journey.verification = 1)'));
            })
            ->leftjoin('products as p','p.id','=','rs.product_type_id')
            ->leftjoin('retail_references as rref','rref.shipment_id','=','shipments.id')
            ->leftjoin('shipment_items as si','si.shipment_id','=','rs.shipment_id')
            ->select('p.product_name as category','shipments.id as shipment_id','shipments.tracking_number','shipments.tracking_number as tracking_number_link', 'ru.name as booked_by', 'ru.category as retail_category','ru.id as booked_by_id', 'rsi.shipper_name', 'rf.id as franchise_account_id','rf.name as franchise', 'rc.id as retail_account_id','rc.name as retail_center','ss.name as current_status','rsm.name as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','h.name as hub', 'oz.name as origin_zone', 'dz.name as destination_zone','shipments.amount as collection_amount','sps.name as payment_status','pps.amount as p_collection_amount','shipments.actual_weight','rs.weight_charges','rs.cash_handling_charges','rs.fuel_surcharge','rs.total_charges as total_charges', 'rs.gst as gst','pps.payable as p_net_payable','dps.amount as d_collection_amount','dps.payable as d_net_payable','dr.created_at as delivered_or_returned', 'dps.retail_done_payment_id as payment_id', 'shipments.shipper_status_id as shipment_status' , 'dr.shipper_status_id as dr_status_id', 'pns.retail_pickup_note_id as pncc_id','rtc.name as retail_trax_center_name', 'rref.ref as retail_reference','rf.discount as franchise_discount','rf.insurance as franchise_insurance','rtc.discount as trax_discount','rtc.insurance as trax_insurance','rs.discount as discount_amount','rs.insurance_charges as insurance_charges','rs.packaging_charges as packaging_charges','si.price as product_value')
            ->whereNotIn('shipments.shipper_status_id',[1,17])
            ->whereBetween('sj.created_at', [$from,$to])
            ->where('shipments.shipment_type', 2)
        ->get();


        if($report_type == 1){
            $filename = 'retail_sales_report_by_arrival_first_to_last.xlsx';
        }
        if($report_type == 2){
            $filename = 'retail_sales_report_by_arrival_first_to_25.xlsx';
        }
        if($report_type == 3){
            $filename = 'retail_sales_report_by_arrival_26_to_last.xlsx';
        }

        $details = array();

        $details[] = ['S.No.', 'Tracking Number', 'Shipper', 'Franchise/Trax Center', 'Booking Staff Name', 'Status', 'Payment Status', 'Payment ID', 'RNCC Number', 'Service Type', 'Arrival Date', 'Origin', 'Destination', 'Hub', 'Origin Zone', 'Destination Zone', 'Attempts', 'Product Type', 'Collection Amount', 'Actual Weight', 'Weight Charges', 'Fuel Surcharge', 'Product Insurance Value', 'Insurance Charges', 'Discount Amount', 'Packaging Charges', 'GST', 'Total Charges', 'Net Payable', 'Delivered Date', 'Booking Staff ID', 'Reference'];

        $serial_number = 1;
        foreach ($sales as $sale){

            $collection_amount = number_format($sale->collection_amount);
            $weight_charges = number_format($sale->weight_charges, 2);
            $fuel_surcharge = number_format($sale->fuel_surcharge, 2);
            $gst = number_format((float)$sale->gst, 2);
            $total_charges = number_format($sale->total_charges, 2);
            $p_net_charges = number_format($sale->p_net_payable, 2);
            $attempts = 0;
            $out_for_delivery = DB::connection('reports')->table('shipments_journey')->where('shipment_id',$sale->shipment_id)->where('shipper_status_id',5)->count();
            $attempts = $out_for_delivery;

            $franchise_center = '';
            if($sale->retail_category){

                if ($sale->retail_category == 2) {
                    $franchise_center = $sale->retail_center;
                }
                else {
                    $franchise_center = $sale->franchise;
                }
            }
            else{
                $franchise_center = $sale->retail_trax_center_name;
            }

            $net_payable = '';
            if($sale->p_net_payable != null){
                $net_payable = $sale->p_net_payable;
            }else if($sale->d_net_payable != null){
                $net_payable = $sale->d_net_payable;
            }
            $net_payable = number_format((float)$net_payable, 2);

            $row = array();

            $row[] = $serial_number;
            $row[] = $sale->tracking_number;
            $row[] = $sale->shipper_name;
            $row[] = $franchise_center;
            $row[] = $sale->booked_by;
            $row[] = $sale->current_status;
            $row[] = $sale->payment_status;
            $row[] = $sale->payment_id;
            $row[] = $sale->pncc_id;
            $row[] = $sale->service_type;
            $row[] = $sale->arrival_date;
            $row[] = $sale->origin;
            $row[] = $sale->destination;
            $row[] = $sale->hub;
            $row[] = $sale->origin_zone;
            $row[] = $sale->destination_zone;
            $row[] = $attempts;
            $row[] = $sale->category;
            $row[] = $collection_amount;
            $row[] = $sale->actual_weight;
            $row[] = $weight_charges;
            $row[] = $fuel_surcharge;
            $row[] = $sale->product_value;
            $row[] = $sale->insurance_charges;
            $row[] = $sale->discount_amount;
            $row[] = $sale->packaging_charges;
            $row[] = $gst;
            $row[] = $total_charges;
            $row[] = $net_payable;
            $row[] = $sale->delivered_or_returned;
            $row[] = str_pad($sale->booked_by_id, 6, '0', STR_PAD_LEFT);
            $row[] = $sale->retail_reference;

            $details[] = $row;
            $serial_number++;
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('Q')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
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
        $spreadsheet->getActiveSheet()->getStyle('AE')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

        $spreadsheet->getActiveSheet()->getStyle('A1:AF1')->getFont()->setBold(TRUE);
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
        $filePath = '/reports/retail/' . $filename;
        Storage::disk('public')->put($filePath, $contents);
        $from = Carbon::parse($from)->toDateString();
        $to = Carbon::parse($to)->toDateString();
        return ['file_path' => $filePath, 'from' => $from, 'to' => $to];
    }

    public static function retail_sales_report_by_delivery($report_type){

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

        $sales = DB::connection('reports')->table('shipments')->join('retail_shipments as rs', 'rs.shipment_id', '=','shipments.id')
            ->leftjoin('retail_users as ru','ru.id','=','rs.retail_user_id')
            ->leftjoin('retail_shipper_infos as rsi', 'rsi.id', '=', 'rs.shipper_account_no')
            ->leftJoin('retail_franchises as rf', function ($join) {
                $join->on('rf.id', '=', 'ru.category_id')
                    ->where('ru.category','=', DB::raw(1));
            })
            ->leftJoin('retail_trax_centers as rc', function ($join) {
                $join->on('rc.id', '=', 'ru.category_id')
                    ->where('ru.category','=', DB::raw(2));
            })
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->join('retail_shipping_modes as rsm','rsm.id','=','rs.shipping_mode')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->leftjoin('retail_trax_centers as rtc', 'rtc.pickup_address_id', '=', 'shipments.pickup_address_id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('zones as oz', 'oz.id', '=', 'oc.zone_id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftjoin('zones as dz', 'dz.id', '=', 'dc.zone_id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->leftjoin('retail_pickup_note_shipments as pns',function($join){
                $join->on('pns.shipment_id','=','shipments.id')
                    ->where('pns.retail_pickup_note_id','=',
                        DB::connection('reports')->raw('(select max(retail_pickup_note_id) from retail_pickup_note_shipments where retail_pickup_note_shipments.shipment_id = shipments.id)'));
            })
//            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('retail_pending_payment_shipments as pps', function ($join) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.id','=',
                        DB::connection('reports')->raw('(select max(id) from retail_pending_payment_shipments where retail_pending_payment_shipments.shipment_id = shipments.id and retail_pending_payment_shipments.type != 2)'));
            })
            ->leftJoin('retail_done_payment_shipments as dps', function ($join) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.id','=',
                        DB::connection('reports')->raw('(select max(id) from retail_done_payment_shipments where retail_done_payment_shipments.shipment_id = shipments.id and retail_done_payment_shipments.type != 2)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37) and shipments_journey.verification = 1)'));
            })
            ->leftjoin('products as p','p.id','=','rs.product_type_id')
            ->leftjoin('retail_references as rref','rref.shipment_id','=','shipments.id')
            ->leftjoin('shipment_items as si','si.shipment_id','=','rs.shipment_id')
            ->select('p.product_name as category','shipments.id as shipment_id','shipments.tracking_number','shipments.tracking_number as tracking_number_link', 'ru.name as booked_by', 'ru.category as retail_category','ru.id as booked_by_id', 'rsi.shipper_name', 'rf.id as franchise_account_id','rf.name as franchise', 'rc.id as retail_account_id','rc.name as retail_center','ss.name as current_status','rsm.name as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','h.name as hub', 'oz.name as origin_zone', 'dz.name as destination_zone','shipments.amount as collection_amount','sps.name as payment_status','pps.amount as p_collection_amount','shipments.actual_weight','rs.weight_charges','rs.cash_handling_charges','rs.fuel_surcharge','rs.total_charges as total_charges', 'rs.gst as gst','pps.payable as p_net_payable','dps.amount as d_collection_amount','dps.payable as d_net_payable','dr.created_at as delivered_or_returned', 'dps.retail_done_payment_id as payment_id', 'shipments.shipper_status_id as shipment_status' , 'dr.shipper_status_id as dr_status_id', 'pns.retail_pickup_note_id as pncc_id','rtc.name as retail_trax_center_name', 'rref.ref as retail_reference','rf.discount as franchise_discount','rf.insurance as franchise_insurance','rtc.discount as trax_discount','rtc.insurance as trax_insurance','rs.discount as discount_amount','rs.insurance_charges as insurance_charges','rs.packaging_charges as packaging_charges','si.price as product_value')
            ->whereNotIn('shipments.shipper_status_id',[1,17])
            ->whereBetween('dr.created_at', [$from,$to])
            ->where('shipments.shipment_type', 2)
            ->get();

        if($report_type == 1){
            $filename = 'retail_sales_report_by_delivery_first_to_last.xlsx';
        }
        if($report_type == 2){
            $filename = 'retail_sales_report_by_delivery_first_to_25.xlsx';
        }
        if($report_type == 3){
            $filename = 'retail_sales_report_by_delivery_to_last.xlsx';
        }

        $details = array();

        $details[] = ['S.No.', 'Tracking Number', 'Shipper', 'Franchise/Trax Center', 'Booking Staff Name', 'Status', 'Payment Status', 'Payment ID', 'RNCC Number', 'Service Type', 'Arrival Date', 'Origin', 'Destination', 'Hub', 'Origin Zone', 'Destination Zone', 'Attempts', 'Product Type', 'Collection Amount', 'Actual Weight', 'Weight Charges', 'Fuel Surcharge', 'Product Insurance Value', 'Insurance Charges', 'Discount Amount', 'Packaging Charges', 'GST', 'Total Charges', 'Net Payable', 'Delivered Date', 'Booking Staff ID', 'Reference'];

        $serial_number = 1;
        foreach ($sales as $sale){

            $collection_amount = number_format($sale->collection_amount);
            $weight_charges = number_format($sale->weight_charges, 2);
            $fuel_surcharge = number_format($sale->fuel_surcharge, 2);
            $gst = number_format((float)$sale->gst, 2);
            $total_charges = number_format($sale->total_charges, 2);

            $attempts = 0;
            $out_for_delivery = DB::connection('reports')->table('shipments_journey')->where('shipment_id',$sale->shipment_id)->where('shipper_status_id',5)->count();
            $attempts = $out_for_delivery;

            $franchise_center = '';
            if($sale->retail_category){

                if ($sale->retail_category == 2) {
                    $franchise_center = $sale->retail_center;
                }
                else {
                    $franchise_center = $sale->franchise;
                }
            }
            else{
                $franchise_center = $sale->retail_trax_center_name;
            }

            $net_payable = '';
            if($sale->p_net_payable != null){
                $net_payable = $sale->p_net_payable;
            }else if($sale->d_net_payable != null){
                $net_payable = $sale->d_net_payable;
            }
            $net_payable = number_format((float)$net_payable, 2);

            $row = array();

            $row[] = $serial_number;
            $row[] = $sale->tracking_number;
            $row[] = $sale->shipper_name;
            $row[] = $franchise_center;
            $row[] = $sale->booked_by;
            $row[] = $sale->current_status;
            $row[] = $sale->payment_status;
            $row[] = $sale->payment_id;
            $row[] = $sale->pncc_id;
            $row[] = $sale->service_type;
            $row[] = $sale->arrival_date;
            $row[] = $sale->origin;
            $row[] = $sale->destination;
            $row[] = $sale->hub;
            $row[] = $sale->origin_zone;
            $row[] = $sale->destination_zone;
            $row[] = $attempts;
            $row[] = $sale->category;
            $row[] = $collection_amount;
            $row[] = $sale->actual_weight;
            $row[] = $weight_charges;
            $row[] = $fuel_surcharge;
            $row[] = $sale->product_value;
            $row[] = $sale->insurance_charges;
            $row[] = $sale->discount_amount;
            $row[] = $sale->packaging_charges;
            $row[] = $gst;
            $row[] = $total_charges;
            $row[] = $net_payable;
            $row[] = $sale->delivered_or_returned;
            $row[] = str_pad($sale->booked_by_id, 6, '0', STR_PAD_LEFT);
            $row[] = $sale->retail_reference;

            $details[] = $row;
            $serial_number++;
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('Q')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
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
        $spreadsheet->getActiveSheet()->getStyle('AE')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

        $spreadsheet->getActiveSheet()->getStyle('A1:AF1')->getFont()->setBold(TRUE);
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
        $filePath = '/reports/retail/' . $filename;
        Storage::disk('public')->put($filePath, $contents);
        $from = Carbon::parse($from)->toDateString();
        $to = Carbon::parse($to)->toDateString();
        return ['file_path' => $filePath, 'from' => $from, 'to' => $to];
    }
}
