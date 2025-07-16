<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\FintechPaymentDetails;
use App\Http\Models\Admin\TraxPayTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RevenueReportUserWiseExcelGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revenue_report_by_user_excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $users = [15151, 15150];
        $years = [2023, 2024, 2025];
        $connection = 'reports';
        foreach ($users as $u_id) {

            foreach ($years as $year) {
                $start = Carbon::create($year)->startOfYear()->format('Y-m-d 00:00:00');
                $end = Carbon::create($year)->endOfYear()->format('Y-m-d 23:59:59');

                $query = DB::connection($connection)->table('shipments')
                    ->select([
                        'shipments.id AS shipment_id',
                        'shipments.tracking_number',
                        'shipments.fintech_charges AS fintech_amount',
                        'shipments.order_id',
                        'shipments.tracking_number AS tracking_number_link',
                        'u.id AS account_no',
                        'u.name AS shipper',
                        'ss.name AS current_status',
                        'bt.booking_type AS service_type',
                        'sj.created_at AS arrival_date',
                        'oc.name AS origin',
                        'dc.name AS destination',
                        'h.name AS hub',
                        'shipments.amount AS s_collection_amount',
                        'sps.name AS payment_status',
                        'shipments.actual_weight',
                        'shipments.weight_charges',
                        'shipments.cash_handling_charges',
                        'shipments.insurance_charges',
                        'shipments.return_charges',
                        'shipments.replacement_charges',
                        'shipments.fuel_surcharge',
                        'shipments.try_and_buy_charges',
                        'shipments.packaging_material_charges',
                        DB::raw('SUM(DISTINCT pps.charges) AS p_total_charges'),
                        DB::raw('SUM(DISTINCT pps.amount) AS p_collection_amount'),
                        DB::raw('SUM(DISTINCT pps.payable) AS p_net_payable'),
                        DB::raw('SUM(DISTINCT pps.gst) AS p_gst'),
                        DB::raw('SUM(DISTINCT dps.amount) AS d_collection_amount'),
                        DB::raw('SUM(DISTINCT dps.charges) AS d_total_charges'),
                        DB::raw('SUM(DISTINCT dps.payable) AS d_net_payable'),
                        DB::raw('SUM(DISTINCT dps.gst) AS d_gst'),
                        'sm.mode AS shipping_mode',
                        'shipments.chargeable_weight',
                        'dr.created_at AS delivered_or_returned',
                        'z.name AS zone',
                        'zcc.class',
                        'oc.id AS origin_city_id',
                        'dc.id AS destination_city_id',
                        'dnsdn.station_deposit_note_id AS sdn_id',
                        'dps.done_payment_id AS payment_id',
                        'shipments.booking_type_id',
                        'usi.poc',
                        'shipments.shipper_status_id AS shipment_status',
                        'shipments.nsa_osa_charges',
                        'u.account_type_id',
                        DB::raw('SUM(DISTINCT pis.gst) AS pis_gst'),
                        DB::raw('SUM(DISTINCT inv_ship.gst) AS is_gst'),
                        'shipments.packaging_charges',
                        'shipments.intercept_charges',
                        'bc.name',
                        'dr.shipper_status_id AS dr_status_id',
                        'shipments.shipment_type',
                        'invoices.invoice_number',
                        'rc.name AS return_city',
                        DB::raw('SUM(DISTINCT ss_charge.reverse_pickup_charges) AS reverse_pickup_charges'),
                        DB::raw('SUM(DISTINCT pps.sms_charges) AS pps_sms_charges'),
                        DB::raw('SUM(DISTINCT dps.sms_charges) AS dps_sms_charges'),
                        DB::raw('SUM(DISTINCT pis.sms_charges) AS pis_sms_charges'),
                        DB::raw('SUM(DISTINCT inv_ship.sms_charges) AS is_sms_charges'),
                        'faf_charges.faf_charges',
                        'provinces.name AS province_name',
                        DB::raw('SUM(DISTINCT dps.wht) AS dps_wht'),
                        DB::raw('SUM(DISTINCT pps.wht) AS pps_wht'),
                        DB::raw('SUM(DISTINCT pis.wht) AS pis_wht'),
                        DB::raw('SUM(DISTINCT inv_ship.wht) AS is_wht'),
                        DB::raw('SUM(DISTINCT dps.cod_sst) AS dps_cod_sst'),
                        DB::raw('SUM(DISTINCT pps.cod_sst) AS pps_cod_sst'),
                        DB::raw('SUM(DISTINCT pis.cod_sst) AS pis_cod_sst'),
                        DB::raw('SUM(DISTINCT inv_ship.cod_sst) AS is_cod_sst'),
                    ])
                    ->leftJoin('shipment_services_charges AS ss_charge', 'ss_charge.shipment_id', '=', 'shipments.id')
                    ->leftJoin('users AS u', 'u.id', '=', 'shipments.user_id')
                    ->leftjoin('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
                    ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
                    ->leftjoin('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
                    ->leftjoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
                    ->leftjoin('zones as z', 'z.id', '=', 'oc.zone_id')
                    ->leftjoin('user_shipping_infos as rsi', 'shipments.return_address_id', '=', 'rsi.id')
                    ->leftjoin('cities as rc', 'rsi.city_id', '=', 'rc.id')
                    ->leftjoin('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
                    ->leftjoin('cities as h', 'dc.hub_id', '=', 'h.id')
                    ->leftjoin('business_categories as bc', 'shipments.business_category_id', '=', 'bc.id')
                    ->leftjoin('zone_class_cities as zcc', function ($join) use ($connection) {
                        $join->on('z.id', '=', 'zcc.zone_id')
                            ->on('dc.id', '=', 'zcc.city_id')
                            ->on('zone_classification_id', '=', DB::connection($connection)->raw('IF (shipments.shipping_mode_id IN (1, 4), 1, 2)'));
                    })
                    ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
                    ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=', 'sps.id')
                    ->leftjoin('delivery_note_shipments as ds', function ($join) {
                        $join->on('ds.shipment_id', '=', 'shipments.id')
                            ->where(
                                'ds.delivery_note_id',
                                '=',
                                DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id and delivery_note_shipments.status > 3 and  delivery_note_shipments.status != 8)')
                            );
                    })
                    ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
                    ->leftJoin('shipments_journey as sj', function ($join) use ($connection) {
                        $join->on('sj.shipment_id', '=', 'shipments.id')
                            ->where('sj.shipper_status_id', 2)
                            ->where(
                                'sj.id',
                                '=',
                                DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)')
                            );
                    })
                    ->leftJoin('pending_payment_shipments as pps', function ($join) use ($connection) {
                        $join->on('pps.shipment_id', '=', 'shipments.id')
                            ->where('pps.type', '!=', 2);
                    })
                    ->leftJoin('done_payment_shipments as dps', function ($join) use ($connection) {
                        $join->on('dps.shipment_id', '=', 'shipments.id')
                            ->where('dps.type', '!=', 2);
                    })
                    ->leftJoin('pending_invoice_shipments as pis', function ($join) use ($connection) {
                        $join->on('pis.shipment_id', '=', 'shipments.id')
                            ->where('pis.type', '!=', 2);
                    })
                    ->leftJoin('invoice_shipments AS inv_ship', function ($join) {
                        $join->on('inv_ship.shipment_id', '=', 'shipments.id')->where('inv_ship.type', '!=', 2);
                    })
                    ->leftJoin('shipment_additional_charges AS faf_charges', 'faf_charges.shipment_id', '=', 'shipments.id')
                    ->leftJoin('invoices', 'inv_ship.invoice_id', '=', 'invoices.id')
                    ->leftJoin('provinces', 'provinces.id', '=', 'dc.province_id')
                    ->leftJoin('shipments_journey as dr', function ($join) use ($start, $end, $connection) {
                        $join->on('dr.shipment_id', '=', 'shipments.id')
                            ->whereIn('dr.shipper_status_id', [14, 20, 30, 36, 37])
                            ->where(
                                'dr.id',
                                '=',
                                DB::connection($connection)->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37) and shipments_journey.verification = 1 and shipments_journey.created_at between "' . $start . '" and "' . $end . '")')
                            );
                    })
                    ->whereNotIn('shipments.shipper_status_id', [1, 17])
                    ->whereBetween('shipments.created_at', [$start, $end])
                    ->where('u.id', $u_id)
                    ->groupBy('shipments.id');

                $sales = $query->get()->map(function ($shipment) {
                    return [
                        'Tracking No.' => $shipment->tracking_number,
                        'Account Type.' => $shipment->account_type_id == 1 ? "Reimbursement" : "Corporate",
                        'Account No.' => str_pad($shipment->account_no, 6, '0', STR_PAD_LEFT),
                        'Business Category' => $shipment->name,
                        'Shipper' => $shipment->booking_type_id == 4 ? "{$shipment->shipper} ({$shipment->poc})" : $shipment->shipper,
                        'Order ID' => $shipment->order_id,
                        'Status' => $shipment->current_status,
                        'Payment Status' => $shipment->payment_status,
                        'Invoice No.' => $shipment->invoice_number,
                        'Payment Number' => $shipment->payment_id,
                        'SDN Number' => $shipment->sdn_id,
                        'Service Type' => $shipment->service_type,
                        'Arrival Date' => $shipment->arrival_date,
                        'Origin' => $shipment->origin,
                        'Destination' => $shipment->destination,
                        'Hub' => $shipment->hub,
                        'Return City' => $shipment->return_city,
                        'Zone' => $shipment->zone,
                        'Province' => $shipment->province_name,
                        'Class' => ($shipment->origin_city_id != $shipment->destination_city_id)
                            ? ['ClassA', 'ClassB', 'ClassC', 'ClassD'][$shipment->class] ?? 'Unknown'
                            : 'Local',
                        'Shipping Mode' => $shipment->shipping_mode,
                        'Collection Amount' => number_format($shipment->p_collection_amount ?: $shipment->d_collection_amount ?: $shipment->s_collection_amount, 2),
                        'Actual Weight' => number_format($shipment->actual_weight, 2),
                        'Chargeable Weight' => number_format($shipment->chargeable_weight, 2),
                        'Weight Charges' => number_format($shipment->weight_charges, 2),
                        'Cash Handling Charges' => number_format($shipment->cash_handling_charges ?? 0, 2),
                        'Insurance Charges' => number_format($shipment->insurance_charges, 2),
                        'Packaging Charges' => number_format($shipment->packaging_material_charges, 2),
                        'Fuel Surcharge' => number_format($shipment->fuel_surcharge, 2),
                        'Return Charges' => number_format($shipment->return_charges, 2),
                        'Fintech Charges' => number_format($shipment->fintech_amount, 2),
                        'Fintech Revenue' => $shipment->fintech_amount !== null
                            ? optional(FintechPaymentDetails::where('trax_pay_id', optional(TraxPayTransaction::where('shipment_id', $shipment->shipment_id)->first())->id)->first())->revenue ?? '-'
                            : '-',
                        'Replacement Charges' => number_format($shipment->replacement_charges, 2),
                        'Packing Charges' => number_format($shipment->packaging_charges, 2),
                        'Try & Buy Charges' => number_format($shipment->try_and_buy_charges, 2),
                        'Reverse Pickup Charges' => number_format($shipment->reverse_pickup_charges, 2),
                        'NSA/OSA Charges' => number_format($shipment->nsa_osa_charges, 2),
                        'Intercept Charges' => number_format($shipment->intercept_charges, 2),
                        'GST' => number_format(
                            ($shipment->account_type_id == 1)
                                ? (($shipment->p_gst ?? 0) + ($shipment->d_gst ?? 0))
                                : (($shipment->pis_gst ?? 0) + ($shipment->is_gst ?? 0))
                            , 2),
                        'SMS Charges' => number_format(
                            ($shipment->account_type_id == 1)
                                ? (($shipment->pps_sms_charges ?? 0) + ($shipment->dps_sms_charges ?? 0))
                                : (($shipment->pis_sms_charges ?? 0) + ($shipment->is_sms_charges ?? 0))
                            , 2),
                        'WHT' => number_format(
                            ($shipment->account_type_id == 1)
                                ? (($shipment->pps_wht ?? 0) + ($shipment->dps_wht ?? 0))
                                : (($shipment->pis_wht ?? 0) + ($shipment->is_wht ?? 0))
                            , 2),
                        'COD SST' => number_format(
                            ($shipment->account_type_id == 1)
                                ? (($shipment->pps_cod_sst ?? 0) + ($shipment->dps_cod_sst ?? 0))
                                : (($shipment->pis_cod_sst ?? 0) + ($shipment->is_cod_sst ?? 0))
                            , 2),
                        'Total Charges' => number_format(($shipment->p_total_charges ?? 0) + ($shipment->d_total_charges ?? 0) + $shipment->fintech_amount, 2),
                        'Estimated Charges' => number_format(
                            array_sum([
                                $shipment->weight_charges ?? 0,
                                $shipment->faf_charges ?? 0,
                                $shipment->cash_handling_charges ?? 0,
                                $shipment->insurance_charges ?? 0,
                                $shipment->return_charges ?? 0,
                                $shipment->replacement_charges ?? 0,
                                $shipment->fuel_surcharge ?? 0,
                                $shipment->try_and_buy_charges ?? 0,
                                $shipment->packaging_material_charges ?? 0,
                                $shipment->intercept_charges ?? 0,
                            ]), 2),
                        'FAF Charges' => number_format($shipment->faf_charges ?? 0, 2),
                        'Net Payable' => number_format(($shipment->p_net_payable ?? 0) + ($shipment->d_net_payable ?? 0), 2),
                        'Delivered / Returned Date' => in_array($shipment->shipment_status, [14, 20, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46, 25])
                            ? $shipment->delivered_or_returned
                            : '',
                    ];
                });


                $data = $sales->toArray();  // Assuming $sales is your mapped collection

                if (count($sales) > 0) {

                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();

                    $headings = array_keys($data[0]); // Get headings from the first row keys
                    $sheet->fromArray($headings, null, 'A1');

                    $sheet->fromArray($data, null, 'A2');

                    $writer = new Xlsx($spreadsheet);
                    $tempFile = tempnam(sys_get_temp_dir(), 'revenue_excel') . '.xlsx';
                    $writer->save($tempFile);

                    $filePath = 'revenue_excel/' . $u_id . '/revenue_excel_' . $year . '.xlsx'; // Fixed file name
                    Storage::disk('public')->put($filePath, file_get_contents($tempFile));

                    unlink($tempFile);

                    echo $filePath;

                }
            }
        }

        $years = [2018,2019,2020,2021,2022];
        foreach ($users as $u_id) {

            foreach ($years as $year) {
                $start = Carbon::create($year)->startOfYear()->format('Y-m-d 00:00:00');
                $end = Carbon::create($year)->endOfYear()->format('Y-m-d 23:59:59');

                $query = DB::connection($connection)->table('shipments_archive as shipments')
                    ->select([
                        'shipments.id AS shipment_id',
                        'shipments.tracking_number',
                        'shipments.fintech_charges AS fintech_amount',
                        'shipments.order_id',
                        'shipments.tracking_number AS tracking_number_link',
                        'u.id AS account_no',
                        'u.name AS shipper',
                        'ss.name AS current_status',
                        'bt.booking_type AS service_type',
                        'sj.created_at AS arrival_date',
                        'oc.name AS origin',
                        'dc.name AS destination',
                        'h.name AS hub',
                        'shipments.amount AS s_collection_amount',
                        'sps.name AS payment_status',
                        'shipments.actual_weight',
                        'shipments.weight_charges',
                        'shipments.cash_handling_charges',
                        'shipments.insurance_charges',
                        'shipments.return_charges',
                        'shipments.replacement_charges',
                        'shipments.fuel_surcharge',
                        'shipments.try_and_buy_charges',
                        'shipments.packaging_material_charges',
                        DB::raw('SUM(DISTINCT pps.charges) AS p_total_charges'),
                        DB::raw('SUM(DISTINCT pps.amount) AS p_collection_amount'),
                        DB::raw('SUM(DISTINCT pps.payable) AS p_net_payable'),
                        DB::raw('SUM(DISTINCT pps.gst) AS p_gst'),
                        DB::raw('SUM(DISTINCT dps.amount) AS d_collection_amount'),
                        DB::raw('SUM(DISTINCT dps.charges) AS d_total_charges'),
                        DB::raw('SUM(DISTINCT dps.payable) AS d_net_payable'),
                        DB::raw('SUM(DISTINCT dps.gst) AS d_gst'),
                        'sm.mode AS shipping_mode',
                        'shipments.chargeable_weight',
                        'dr.created_at AS delivered_or_returned',
                        'z.name AS zone',
                        'zcc.class',
                        'oc.id AS origin_city_id',
                        'dc.id AS destination_city_id',
                        'dnsdn.station_deposit_note_id AS sdn_id',
                        'dps.done_payment_id AS payment_id',
                        'shipments.booking_type_id',
                        'usi.poc',
                        'shipments.shipper_status_id AS shipment_status',
                        'shipments.nsa_osa_charges',
                        'u.account_type_id',
                        DB::raw('SUM(DISTINCT pis.gst) AS pis_gst'),
                        DB::raw('SUM(DISTINCT inv_ship.gst) AS is_gst'),
                        'shipments.packaging_charges',
                        'shipments.intercept_charges',
                        'bc.name',
                        'dr.shipper_status_id AS dr_status_id',
                        'shipments.shipment_type',
                        'invoices.invoice_number',
                        'rc.name AS return_city',
                        DB::raw('SUM(DISTINCT ss_charge.reverse_pickup_charges) AS reverse_pickup_charges'),
                        DB::raw('SUM(DISTINCT pps.sms_charges) AS pps_sms_charges'),
                        DB::raw('SUM(DISTINCT dps.sms_charges) AS dps_sms_charges'),
                        DB::raw('SUM(DISTINCT pis.sms_charges) AS pis_sms_charges'),
                        DB::raw('SUM(DISTINCT inv_ship.sms_charges) AS is_sms_charges'),
                        'faf_charges.faf_charges',
                        'provinces.name AS province_name',
                        DB::raw('SUM(DISTINCT dps.wht) AS dps_wht'),
                        DB::raw('SUM(DISTINCT pps.wht) AS pps_wht'),
                        DB::raw('SUM(DISTINCT pis.wht) AS pis_wht'),
                        DB::raw('SUM(DISTINCT inv_ship.wht) AS is_wht'),
                        DB::raw('SUM(DISTINCT dps.cod_sst) AS dps_cod_sst'),
                        DB::raw('SUM(DISTINCT pps.cod_sst) AS pps_cod_sst'),
                        DB::raw('SUM(DISTINCT pis.cod_sst) AS pis_cod_sst'),
                        DB::raw('SUM(DISTINCT inv_ship.cod_sst) AS is_cod_sst'),
                    ])
                    ->leftJoin('shipment_services_charges AS ss_charge', 'ss_charge.shipment_id', '=', 'shipments.id')
                    ->leftJoin('users AS u', 'u.id', '=', 'shipments.user_id')
                    ->leftjoin('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
                    ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
                    ->leftjoin('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
                    ->leftjoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
                    ->leftjoin('zones as z', 'z.id', '=', 'oc.zone_id')
                    ->leftjoin('user_shipping_infos as rsi', 'shipments.return_address_id', '=', 'rsi.id')
                    ->leftjoin('cities as rc', 'rsi.city_id', '=', 'rc.id')
                    ->leftjoin('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
                    ->leftjoin('cities as h', 'dc.hub_id', '=', 'h.id')
                    ->leftjoin('business_categories as bc', 'shipments.business_category_id', '=', 'bc.id')
                    ->leftjoin('zone_class_cities as zcc', function ($join) use ($connection) {
                        $join->on('z.id', '=', 'zcc.zone_id')
                            ->on('dc.id', '=', 'zcc.city_id')
                            ->on('zone_classification_id', '=', DB::connection($connection)->raw('IF (shipments.shipping_mode_id IN (1, 4), 1, 2)'));
                    })
                    ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
                    ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=', 'sps.id')
                    ->leftjoin('delivery_note_shipments as ds', function ($join) {
                        $join->on('ds.shipment_id', '=', 'shipments.id')
                            ->where(
                                'ds.delivery_note_id',
                                '=',
                                DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id and delivery_note_shipments.status > 3 and  delivery_note_shipments.status != 8)')
                            );
                    })
                    ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
                    ->leftJoin('shipments_journey_archive as sj', function ($join) use ($connection) {
                        $join->on('sj.shipment_id', '=', 'shipments.id')
                            ->where('sj.shipper_status_id', 2)
                            ->where(
                                'sj.id',
                                '=',
                                DB::connection($connection)->raw('(select max(id) from shipments_journey_archive where shipments_journey_archive.shipment_id = shipments.id and shipments_journey_archive.shipper_status_id = 2)')
                            );
                    })
                    ->leftJoin('pending_payment_shipments as pps', function ($join) use ($connection) {
                        $join->on('pps.shipment_id', '=', 'shipments.id')
                            ->where('pps.type', '!=', 2);
                    })
                    ->leftJoin('done_payment_shipments as dps', function ($join) use ($connection) {
                        $join->on('dps.shipment_id', '=', 'shipments.id')
                            ->where('dps.type', '!=', 2);
                    })
                    ->leftJoin('pending_invoice_shipments as pis', function ($join) use ($connection) {
                        $join->on('pis.shipment_id', '=', 'shipments.id')
                            ->where('pis.type', '!=', 2);
                    })
                    ->leftJoin('invoice_shipments AS inv_ship', function ($join) {
                        $join->on('inv_ship.shipment_id', '=', 'shipments.id')->where('inv_ship.type', '!=', 2);
                    })
                    ->leftJoin('shipment_additional_charges AS faf_charges', 'faf_charges.shipment_id', '=', 'shipments.id')
                    ->leftJoin('invoices', 'inv_ship.invoice_id', '=', 'invoices.id')
                    ->leftJoin('provinces', 'provinces.id', '=', 'dc.province_id')
                    ->leftJoin('shipments_journey_archive as dr', function ($join) use ($start, $end, $connection) {
                        $join->on('dr.shipment_id', '=', 'shipments.id')
                            ->whereIn('dr.shipper_status_id', [14, 20, 30, 36, 37])
                            ->where(
                                'dr.id',
                                '=',
                                DB::connection($connection)->raw('(select max(id) from shipments_journey_archive where shipments_journey_archive.shipment_id = shipments.id and shipments_journey_archive.shipper_status_id In(14,20,30,36,37) and shipments_journey_archive.verification = 1 and shipments_journey_archive.created_at between "' . $start . '" and "' . $end . '")')
                            );
                    })
                    ->whereNotIn('shipments.shipper_status_id', [1, 17])
                    ->whereBetween('shipments.created_at', [$start, $end])
                    ->where('u.id', $u_id)
                    ->groupBy('shipments.id');

                $sales = $query->get()->map(function ($shipment) {
                    return [
                        'Tracking No.' => $shipment->tracking_number,
                        'Account Type.' => $shipment->account_type_id == 1 ? "Reimbursement" : "Corporate",
                        'Account No.' => str_pad($shipment->account_no, 6, '0', STR_PAD_LEFT),
                        'Business Category' => $shipment->name,
                        'Shipper' => $shipment->booking_type_id == 4 ? "{$shipment->shipper} ({$shipment->poc})" : $shipment->shipper,
                        'Order ID' => $shipment->order_id,
                        'Status' => $shipment->current_status,
                        'Payment Status' => $shipment->payment_status,
                        'Invoice No.' => $shipment->invoice_number,
                        'Payment Number' => $shipment->payment_id,
                        'SDN Number' => $shipment->sdn_id,
                        'Service Type' => $shipment->service_type,
                        'Arrival Date' => $shipment->arrival_date,
                        'Origin' => $shipment->origin,
                        'Destination' => $shipment->destination,
                        'Hub' => $shipment->hub,
                        'Return City' => $shipment->return_city,
                        'Zone' => $shipment->zone,
                        'Province' => $shipment->province_name,
                        'Class' => ($shipment->origin_city_id != $shipment->destination_city_id)
                            ? ['ClassA', 'ClassB', 'ClassC', 'ClassD'][$shipment->class] ?? 'Unknown'
                            : 'Local',
                        'Shipping Mode' => $shipment->shipping_mode,
                        'Collection Amount' => number_format($shipment->p_collection_amount ?: $shipment->d_collection_amount ?: $shipment->s_collection_amount, 2),
                        'Actual Weight' => number_format($shipment->actual_weight, 2),
                        'Chargeable Weight' => number_format($shipment->chargeable_weight, 2),
                        'Weight Charges' => number_format($shipment->weight_charges, 2),
                        'Cash Handling Charges' => number_format($shipment->cash_handling_charges ?? 0, 2),
                        'Insurance Charges' => number_format($shipment->insurance_charges, 2),
                        'Packaging Charges' => number_format($shipment->packaging_material_charges, 2),
                        'Fuel Surcharge' => number_format($shipment->fuel_surcharge, 2),
                        'Return Charges' => number_format($shipment->return_charges, 2),
                        'Fintech Charges' => number_format($shipment->fintech_amount, 2),
                        'Fintech Revenue' => $shipment->fintech_amount !== null
                            ? optional(FintechPaymentDetails::where('trax_pay_id', optional(TraxPayTransaction::where('shipment_id', $shipment->shipment_id)->first())->id)->first())->revenue ?? '-'
                            : '-',
                        'Replacement Charges' => number_format($shipment->replacement_charges, 2),
                        'Packing Charges' => number_format($shipment->packaging_charges, 2),
                        'Try & Buy Charges' => number_format($shipment->try_and_buy_charges, 2),
                        'Reverse Pickup Charges' => number_format($shipment->reverse_pickup_charges, 2),
                        'NSA/OSA Charges' => number_format($shipment->nsa_osa_charges, 2),
                        'Intercept Charges' => number_format($shipment->intercept_charges, 2),
                        'GST' => number_format(
                            ($shipment->account_type_id == 1)
                                ? (($shipment->p_gst ?? 0) + ($shipment->d_gst ?? 0))
                                : (($shipment->pis_gst ?? 0) + ($shipment->is_gst ?? 0))
                            , 2),
                        'SMS Charges' => number_format(
                            ($shipment->account_type_id == 1)
                                ? (($shipment->pps_sms_charges ?? 0) + ($shipment->dps_sms_charges ?? 0))
                                : (($shipment->pis_sms_charges ?? 0) + ($shipment->is_sms_charges ?? 0))
                            , 2),
                        'WHT' => number_format(
                            ($shipment->account_type_id == 1)
                                ? (($shipment->pps_wht ?? 0) + ($shipment->dps_wht ?? 0))
                                : (($shipment->pis_wht ?? 0) + ($shipment->is_wht ?? 0))
                            , 2),
                        'COD SST' => number_format(
                            ($shipment->account_type_id == 1)
                                ? (($shipment->pps_cod_sst ?? 0) + ($shipment->dps_cod_sst ?? 0))
                                : (($shipment->pis_cod_sst ?? 0) + ($shipment->is_cod_sst ?? 0))
                            , 2),
                        'Total Charges' => number_format(($shipment->p_total_charges ?? 0) + ($shipment->d_total_charges ?? 0) + $shipment->fintech_amount, 2),
                        'Estimated Charges' => number_format(
                            array_sum([
                                $shipment->weight_charges ?? 0,
                                $shipment->faf_charges ?? 0,
                                $shipment->cash_handling_charges ?? 0,
                                $shipment->insurance_charges ?? 0,
                                $shipment->return_charges ?? 0,
                                $shipment->replacement_charges ?? 0,
                                $shipment->fuel_surcharge ?? 0,
                                $shipment->try_and_buy_charges ?? 0,
                                $shipment->packaging_material_charges ?? 0,
                                $shipment->intercept_charges ?? 0,
                            ]), 2),
                        'FAF Charges' => number_format($shipment->faf_charges ?? 0, 2),
                        'Net Payable' => number_format(($shipment->p_net_payable ?? 0) + ($shipment->d_net_payable ?? 0), 2),
                        'Delivered / Returned Date' => in_array($shipment->shipment_status, [14, 20, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46, 25])
                            ? $shipment->delivered_or_returned
                            : '',
                    ];


                });


                if (count($sales) > 0) {
                    $data = $sales->toArray();  // Assuming $sales is your mapped collection

                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();

                    $headings = array_keys($data[0]); // Get headings from the first row keys
                    $sheet->fromArray($headings, null, 'A1');

                    $sheet->fromArray($data, null, 'A2');

                    $writer = new Xlsx($spreadsheet);
                    $tempFile = tempnam(sys_get_temp_dir(), 'revenue_excel') . '.xlsx';
                    $writer->save($tempFile);

                    $filePath = 'revenue_excel/' . $u_id . '/revenue_excel_' . $year . '.xlsx'; // Fixed file name
                    Storage::disk('public')->put($filePath, file_get_contents($tempFile));

                    unlink($tempFile);

                    echo $filePath;
                }

            }
        }


    }
}
