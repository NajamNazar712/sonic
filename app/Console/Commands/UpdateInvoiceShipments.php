<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Models\InvoiceShipment;
use App\Http\Models\Shipment;
use App\Http\Models\Zone;
use Illuminate\Support\Facades\DB;

class UpdateInvoiceShipments extends Command
{
    protected $signature = 'update:invoice-shipments {invoiceId}';
    protected $description = 'Update charges, GST, and invoice amounts for invoice shipments based on calculations';

    public function handle()
    {
        $invoiceId = $this->argument('invoiceId');

        $invoiceShipments = DB::table('invoice_shipments')
            ->leftJoin('invoices', 'invoices.id', '=', 'invoice_shipments.invoice_id')
            ->leftJoin('shipments', 'shipments.id', '=', 'invoice_shipments.shipment_id')
            ->leftJoin('shipment_services_charges', 'shipments.id', '=', 'shipment_services_charges.shipment_id')
            ->leftJoin('user_shipping_infos', 'user_shipping_infos.id', '=', 'shipments.pickup_address_id')
            ->leftJoin('cities', 'cities.id', '=', 'user_shipping_infos.city_id')
            ->leftJoin('zones', 'zones.id', '=', 'cities.zone_id')
            ->leftJoin('shipment_additional_charges', 'shipment_additional_charges.shipment_id', '=', 'invoice_shipments.shipment_id')
            ->where('invoices.id', $invoiceId)
            ->where('invoice_shipments.type', 0)
            ->select([
                'invoice_shipments.id AS invoice_id',
                'invoice_shipments.shipment_id',
                DB::raw('IFNULL(shipments.cash_handling_charges, 0) + 
                         IFNULL(shipments.insurance_charges, 0) + 
                         IFNULL(shipments.replacement_charges, 0) + 
                         IFNULL(shipments.try_and_buy_charges, 0) + 
                         IFNULL(shipments.intercept_charges, 0) + 
                         IFNULL(shipments.nsa_osa_charges, 0) + 
                         IFNULL(shipments.esc_charges, 0) + 
                         IFNULL(shipment_services_charges.reverse_pickup_charges, 0) AS new_charges'),
                DB::raw('((IFNULL(shipments.cash_handling_charges, 0) + 
                           IFNULL(shipments.insurance_charges, 0) + 
                           IFNULL(shipments.replacement_charges, 0) + 
                           IFNULL(shipments.try_and_buy_charges, 0) + 
                           IFNULL(shipments.intercept_charges, 0) + 
                           IFNULL(shipments.nsa_osa_charges, 0) + 
                           IFNULL(shipments.esc_charges, 0) + 
                           IFNULL(shipment_services_charges.reverse_pickup_charges, 0)) * IFNULL(zones.gst, 0)) AS new_gst'),
                DB::raw('IFNULL(shipments.cash_handling_charges, 0) + 
                         IFNULL(shipments.insurance_charges, 0) + 
                         IFNULL(shipments.replacement_charges, 0) + 
                         IFNULL(shipments.try_and_buy_charges, 0) + 
                         IFNULL(shipments.intercept_charges, 0) + 
                         IFNULL(shipments.nsa_osa_charges, 0) + 
                         IFNULL(shipments.esc_charges, 0) + 
                         IFNULL(shipment_services_charges.reverse_pickup_charges, 0) + 
                         ((IFNULL(shipments.cash_handling_charges, 0) + 
                           IFNULL(shipments.insurance_charges, 0) + 
                           IFNULL(shipments.replacement_charges, 0) + 
                           IFNULL(shipments.try_and_buy_charges, 0) + 
                           IFNULL(shipments.intercept_charges, 0) + 
                           IFNULL(shipments.nsa_osa_charges, 0) + 
                           IFNULL(shipments.esc_charges, 0) + 
                           IFNULL(shipment_services_charges.reverse_pickup_charges, 0)) * IFNULL(zones.gst, 0)) AS new_charges_total'),
                'invoice_shipments.charges AS old_charges',
                'invoice_shipments.gst AS old_gst',
                'invoice_shipments.invoice_amount AS old_invoice_amount'
            ])
            ->havingRaw('new_charges != old_charges')
            ->get();

        foreach ($invoiceShipments as $shipment) {
            $newCharges = $shipment->new_charges;
            $newGst = $shipment->new_gst;
            $newTotal = $shipment->new_charges_total;

            DB::table('invoice_shipments')
                ->where('id', $shipment->invoice_id)
                ->update([
                    'charges' => $newCharges,
                    'gst' => $newGst,
                    'invoice_amount' => $newTotal,
                ]);

        }



        $invoiceShipments2 = DB::table('invoice_shipments')
            ->leftJoin('invoices', 'invoices.id', '=', 'invoice_shipments.invoice_id')
            ->leftJoin('shipments', 'shipments.id', '=', 'invoice_shipments.shipment_id')
            ->leftJoin('user_shipping_infos', 'user_shipping_infos.id', '=', 'shipments.pickup_address_id')
            ->leftJoin('cities', 'cities.id', '=', 'user_shipping_infos.city_id')
            ->leftJoin('zones', 'zones.id', '=', 'cities.zone_id')
            ->leftJoin('shipment_additional_charges', 'shipment_additional_charges.shipment_id', '=', 'invoice_shipments.shipment_id')
            ->where('invoices.id', $invoiceId)
            ->where('invoice_shipments.type', 1)
            ->select([
                'invoice_shipments.id AS invoice_id',
                'invoice_shipments.shipment_id',
                DB::raw('(IFNULL(shipments.insurance_charges, 0) + 
                  IFNULL(shipments.return_charges, 0) + 
                  IFNULL(shipments.intercept_charges, 0) + 
                  IFNULL(shipments.nsa_osa_charges, 0)) AS new_charges'),
                DB::raw('((IFNULL(shipments.insurance_charges, 0) + 
                   IFNULL(shipments.return_charges, 0) + 
                   IFNULL(shipments.intercept_charges, 0) + 
                   IFNULL(shipments.nsa_osa_charges, 0)) * IFNULL(zones.gst, 0)) AS new_gst'),
                DB::raw('(IFNULL(shipments.insurance_charges, 0) + 
                  IFNULL(shipments.return_charges, 0) + 
                  IFNULL(shipments.intercept_charges, 0) + 
                  IFNULL(shipments.nsa_osa_charges, 0) + 
                  ((IFNULL(shipments.insurance_charges, 0) + 
                    IFNULL(shipments.return_charges, 0) + 
                    IFNULL(shipments.intercept_charges, 0) + 
                    IFNULL(shipments.nsa_osa_charges, 0)) * IFNULL(zones.gst, 0))) AS new_charges_total'),
                'invoice_shipments.charges AS old_charges',
                'invoice_shipments.gst AS old_gst',
                'invoice_shipments.invoice_amount AS old_invoice_amount'
            ])
            ->havingRaw('new_charges != old_charges')
            ->get();

        foreach ($invoiceShipments2 as $shipment) {
            $newCharges = $shipment->new_charges;
            $newGst = $shipment->new_gst;
            $newTotal = $shipment->new_charges_total;

            DB::table('invoice_shipments')
                ->where('id', $shipment->invoice_id)
                ->update([
                    'charges' => $newCharges,
                    'gst' => $newGst,
                    'invoice_amount' => $newTotal,
                ]);

        }


        $invoiceShipments3 = DB::table('invoice_shipments')
            ->leftJoin('invoices', 'invoices.id', '=', 'invoice_shipments.invoice_id')
            ->leftJoin('shipments', 'shipments.id', '=', 'invoice_shipments.shipment_id')
            ->leftJoin('user_shipping_infos', 'user_shipping_infos.id', '=', 'shipments.pickup_address_id')
            ->leftJoin('cities', 'cities.id', '=', 'user_shipping_infos.city_id')
            ->leftJoin('zones', 'zones.id', '=', 'cities.zone_id')
            ->leftJoin('shipment_additional_charges', 'shipment_additional_charges.shipment_id', '=', 'invoice_shipments.shipment_id')
            ->where('invoices.id', $invoiceId)
            ->where('invoice_shipments.type', 3)
            ->select([
                'invoice_shipments.id AS invoice_id',
                'invoice_shipments.shipment_id',
                DB::raw('(IFNULL(shipments.weight_charges, 0) + 
                  IFNULL(shipments.fuel_surcharge, 0) + 
                  IFNULL(shipment_additional_charges.faf_charges, 0)) AS new_charges'),
                DB::raw('((IFNULL(shipments.weight_charges, 0) + 
                   IFNULL(shipments.fuel_surcharge, 0) + 
                   IFNULL(shipment_additional_charges.faf_charges, 0)) * IFNULL(zones.gst, 0)) AS new_gst'),
                DB::raw('(IFNULL(shipments.weight_charges, 0) + 
                  IFNULL(shipments.fuel_surcharge, 0) + 
                  IFNULL(shipment_additional_charges.faf_charges, 0) + 
                  ((IFNULL(shipments.weight_charges, 0) + 
                    IFNULL(shipments.fuel_surcharge, 0) + 
                    IFNULL(shipment_additional_charges.faf_charges, 0)) * IFNULL(zones.gst, 0))) AS new_charges_total'),
                'invoice_shipments.charges AS old_charges',
                'invoice_shipments.gst AS old_gst',
                'invoice_shipments.invoice_amount AS old_invoice_amount'
            ])
            ->havingRaw('new_charges != old_charges')
            ->get();

        foreach ($invoiceShipments3 as $shipment) {
            $newCharges = $shipment->new_charges;
            $newGst = $shipment->new_gst;
            $newTotal = $shipment->new_charges_total;

            DB::table('invoice_shipments')
                ->where('id', $shipment->invoice_id)
                ->update([
                    'charges' => $newCharges,
                    'gst' => $newGst,
                    'invoice_amount' => $newTotal,
                ]);

        }


        $invoiceSummary = DB::table('invoice_shipments')
            ->selectRaw('
            SUM(charges) AS total_charges,
            SUM(gst) AS total_gst,
            SUM(sms_charges) AS total_sms_charges,
            SUM(invoice_amount) AS total_invoice_amount')
            ->where('invoice_id', $invoiceId)
            ->first();

        $currentInvoice = DB::table('invoices')
            ->where('id', $invoiceId)
            ->select('total_charges', 'total_gst', 'total_sms_charges', 'total_invoice_amount')
            ->first();

        if ($invoiceSummary && $currentInvoice) {
            $updates = [];
            if ($invoiceSummary->total_charges != $currentInvoice->total_charges) {
                $updates['total_charges'] = $invoiceSummary->total_charges;
            }
            if ($invoiceSummary->total_gst != $currentInvoice->total_gst) {
                $updates['total_gst'] = $invoiceSummary->total_gst;
            }
            if ($invoiceSummary->total_sms_charges != $currentInvoice->total_sms_charges) {
                $updates['total_sms_charges'] = $invoiceSummary->total_sms_charges;
            }
            if ($invoiceSummary->total_invoice_amount != $currentInvoice->total_invoice_amount) {
                $updates['total_invoice_amount'] = $invoiceSummary->total_invoice_amount;
            }

            // Execute the update if there are changes
            if (!empty($updates)) {
                DB::table('invoices')->where('id', $invoiceId)->update($updates);
            }
        }


    }
}
