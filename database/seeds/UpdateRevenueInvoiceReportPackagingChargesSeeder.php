<?php

use App\Http\Models\Admin\RevenueByInvoiceReport;
use App\Http\Models\Invoice;
use App\Http\Models\InvoiceForReimbursement;
use App\Http\Models\Shipment;
use Illuminate\Database\Seeder;

class UpdateRevenueInvoiceReportPackagingChargesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
