<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\RevenueByInvoiceReport;
use App\Http\Models\Invoice;
use App\Http\Models\InvoiceForReimbursement;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class InvoiceByOriginReportController extends Controller
{

    static public function create_invoice($invoice){
        $data_category_wise = array();


        $invoice_shipment_ids = $invoice->invoice_shipments->pluck('shipment_id')->toArray();
        $shipments_by_category = Shipment::whereIn('id', $invoice_shipment_ids)->get()->groupBy('business_category_id');


        foreach ($shipments_by_category as $category_id => $shipments){
            $origins = array();
            foreach ($shipments as $shipment){

                $origin = $shipment->pickup_address->city->id;
                if (!in_array($origin, $origins)) {
                    $origins[] = $origin;
                }

                if (!isset($total_weight_charges[$origin])) {
                    $total_weight_charges[$origin] = 0;
                }

                if (!isset($total_cash_handling_charges[$origin])) {
                    $total_cash_handling_charges[$origin] = 0;
                }

                if (!isset($total_insurance_charges[$origin])) {
                    $total_insurance_charges[$origin] = 0;
                }

                if (!isset($total_packaging_material_charges[$origin])) {
                    $total_packaging_material_charges[$origin] = 0;
                }

                if (!isset($total_fuel_surcharge[$origin])) {
                    $total_fuel_surcharge[$origin] = 0;
                }

                if (!isset($total_return_charges[$origin])) {
                    $total_return_charges[$origin] = 0;
                }

                if (!isset($total_replacement_charges[$origin])) {
                    $total_replacement_charges[$origin] = 0;
                }

                if (!isset($total_packaging_charges[$origin])) {
                    $total_packaging_charges[$origin] = 0;
                }

                if (!isset($total_try_and_buy_charges[$origin])) {
                    $total_try_and_buy_charges[$origin] = 0;
                }

                if (!isset($total_nsa_osa_charges[$origin])) {
                    $total_nsa_osa_charges[$origin] = 0;
                }

                if (!isset($total_intercept_charges[$origin])) {
                    $total_intercept_charges[$origin] = 0;
                }

                if (!isset($total_gst[$origin])) {
                    $total_gst[$origin] = 0;
                }

                if (!isset($total_charges[$origin])) {
                    $total_charges[$origin] = 0;
                }
                $invoice_shipment = $invoice->invoice_shipments->where('shipment_id', $shipment->id)->first();

                if ($invoice_shipment->type != 2) {
                    if ($invoice_shipment->type == 0) {
                        $total_cash_handling_charges[$origin] += $shipment->cash_handling_charges;
                        $total_replacement_charges[$origin] += $shipment->replacement_charges;
                        $total_try_and_buy_charges[$origin] += $shipment->try_and_buy_charges;


                    } else {
                        $total_return_charges[$origin] += $shipment->return_charges;
                    }


                    $total_weight_charges[$origin] += $shipment->weight_charges;

                    if ($shipment->packaging_material_request) {
                        $total_packaging_material_charges[$origin] += $shipment->packaging_material_charges;
                    }

                    if ($shipment->packaging_charges) {
                        $total_packaging_charges[$origin] += $shipment->packaging_charges;
                    }


                    $total_insurance_charges[$origin] += $shipment->insurance_charges;
                    $total_fuel_surcharge[$origin] += $shipment->fuel_surcharge;
                    $total_intercept_charges[$origin] += $shipment->intercept_charges;
                    $total_nsa_osa_charges[$origin] += $shipment->nsa_osa_charges;
                }

                $total_charges[$origin] += $invoice_shipment->charges;
                $total_gst[$origin] += $invoice_shipment->gst;
            }
            $data_category_wise[$category_id] = $origins;
        }

        if (count($data_category_wise) > 0) {
            foreach ($data_category_wise as $category_id => $origins) {
                foreach ($origins as $origin){
                    $revenue_by_invoice = new RevenueByInvoiceReport();
                    $revenue_by_invoice->business_category_id = $category_id;
                    $revenue_by_invoice->user_id = $invoice->user_id;
                    $revenue_by_invoice->invoice_number = $invoice->invoice_number;
                    $revenue_by_invoice->invoicing_date = $invoice->invoicing_date;
                    $revenue_by_invoice->origin_id = $origin;
                    $revenue_by_invoice->weight_charges = $total_weight_charges[$origin];
                    $revenue_by_invoice->cash_handling_charges = $total_cash_handling_charges[$origin];
                    $revenue_by_invoice->insurance_charges = $total_insurance_charges[$origin];
                    $revenue_by_invoice->packaging_charges = $total_packaging_material_charges[$origin];
                    $revenue_by_invoice->fuel_surcharge = $total_fuel_surcharge[$origin];
                    $revenue_by_invoice->return_charges = $total_return_charges[$origin];
                    $revenue_by_invoice->replacement_charges = $total_replacement_charges[$origin];
                    $revenue_by_invoice->packing_charges = $total_packaging_charges[$origin];
                    $revenue_by_invoice->try_buy_charges = $total_try_and_buy_charges[$origin];
                    $revenue_by_invoice->nsa_osa_charges = $total_nsa_osa_charges[$origin];
                    $revenue_by_invoice->intercept_charges = $total_intercept_charges[$origin];
                    $revenue_by_invoice->gst = $total_gst[$origin];
                    $revenue_by_invoice->total_charges = $total_charges[$origin];
                    $revenue_by_invoice->save();

                }
            }
        }
    }

    static public function update_invoices(){
        $revenue_invoices = DB::table('revenue_by_invoice_reports')->where('id', '>', 1608)->get();

        foreach ($revenue_invoices as $ri){
            $revenue_by_invoice_id = $ri->id;
            $invoice_number = $ri->invoice_number;
            $user_id = $ri->user_id;
            $business_category_id = $ri->business_category_id;
            $origin_id = $ri->origin_id;

            $invoice = Invoice::where(['user_id' => $user_id, 'invoice_number' => $invoice_number])->first();
            if ($invoice) {
                $invoice_shipment_ids = $invoice->invoice_shipments->pluck('shipment_id')->toArray();
                $shipments = Shipment::whereIn('id', $invoice_shipment_ids)->where('business_category_id', $business_category_id)->select('id','pickup_address_id','packaging_material_charges', 'packaging_material_request')->get();

                $total_packaging_material_charges = 0;
                foreach ($shipments as $shipment){

                    $origin = $shipment->pickup_address->city->id;

                    if ($origin_id != $origin) {
                        continue;
                    }

                    $invoice_shipment = $invoice->invoice_shipments->where('shipment_id', $shipment->id)->where('type', '!=', 2)->first();

                    if ($invoice_shipment) {
                        if ($shipment->packaging_material_request) {
                            $total_packaging_material_charges += $shipment->packaging_material_charges;
                        }
                    }
                }

                $revenue_by_invoice = RevenueByInvoiceReport::find($revenue_by_invoice_id);

                $revenue_by_invoice->packaging_charges = $total_packaging_material_charges;

                $revenue_by_invoice->save();


            }
            else{
                $reim_invoice = InvoiceForReimbursement::where('user_id', $user_id)->where('invoice_number', $invoice_number)->first();
                if($reim_invoice){
                    $invoice_shipment_ids = $reim_invoice->invoice_shipments->pluck('shipment_id')->toArray();
                    $shipments = Shipment::whereIn('id', $invoice_shipment_ids)->where('business_category_id', $business_category_id)->select('id','pickup_address_id','packaging_material_charges', 'packaging_material_request')->get();

                    $total_packaging_material_charges = 0;
                    foreach ($shipments as $shipment){

                        $origin = $shipment->pickup_address->city->id;

                        if ($origin_id != $origin) {
                            continue;
                        }

                        $invoice_shipment = $reim_invoice->invoice_shipments->where('shipment_id', $shipment->id)->where('type', '!=', 2)->first();

                        if ($invoice_shipment) {
                            if ($shipment->packaging_material_request) {
                                $total_packaging_material_charges += $shipment->packaging_material_charges;
                            }
                        }
                    }

                    $revenue_by_invoice = RevenueByInvoiceReport::find($revenue_by_invoice_id);

                    $revenue_by_invoice->packaging_charges = $total_packaging_material_charges;

                    $revenue_by_invoice->save();
                }
            }
        }
    }
}
