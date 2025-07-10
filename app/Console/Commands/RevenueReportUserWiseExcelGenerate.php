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

        $users = [15151,15150];
        foreach ($users as $u_id) {
            $years = [2023, 2024, 2025];
            foreach ($years as $year) {
                $start = Carbon::create($year)->startOfYear()->format('Y-m-d 00:00:00');
                $end = Carbon::create($year)->endOfYear()->format('Y-m-d 23:59:59');

                $query = DB::table('shipments')
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
                    ->leftJoin('shipment_status AS ss', 'ss.id', '=', 'shipments.shipper_status_id')
                    ->leftJoin('booking_types AS bt', 'bt.id', '=', 'shipments.booking_type_id')
                    ->leftJoin('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
                    ->leftJoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
                    ->leftJoin('zones AS z', 'z.id', '=', 'oc.zone_id')
                    ->leftJoin('user_shipping_infos AS rsi', 'shipments.return_address_id', '=', 'rsi.id')
                    ->leftJoin('cities AS rc', 'rsi.city_id', '=', 'rc.id')
                    ->leftJoin('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
                    ->leftJoin('cities AS h', 'dc.hub_id', '=', 'h.id')
                    ->leftJoin('business_categories AS bc', 'shipments.business_category_id', '=', 'bc.id')
                    ->leftJoin('zone_class_cities AS zcc', function ($join) {
                        $join->on('z.id', '=', 'zcc.zone_id')
                            ->on('dc.id', '=', 'zcc.city_id');
                        // You'll need to handle zone_classification_id logic outside the query or use a raw expression
                    })
                    ->leftJoin('shipping_modes AS sm', 'sm.id', '=', 'shipments.shipping_mode_id')
                    ->leftJoin('shipment_payment_status AS sps', 'shipments.payment_status_id', '=', 'sps.id')
                    ->leftJoin('delivery_note_shipments AS ds', function ($join) {
                        $join->on('ds.shipment_id', '=', 'shipments.id');
                        // For the subquery in the join, you'll need to write a raw join or fetch the MAX(delivery_note_id) before
                    })
                    ->leftJoin('delivery_note_station_deposit_notes AS dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
                    ->leftJoin('shipments_journey AS sj', function ($join) {
                        $join->on('sj.shipment_id', '=', 'shipments.id')->where('sj.shipper_status_id', 2);
                        // Subquery logic for getting the latest ID should be handled before the query
                    })
                    ->leftJoin('pending_payment_shipments AS pps', function ($join) {
                        $join->on('pps.shipment_id', '=', 'shipments.id')->where('pps.type', '!=', 2);
                    })
                    ->leftJoin('done_payment_shipments AS dps', function ($join) {
                        $join->on('dps.shipment_id', '=', 'shipments.id')->where('dps.type', '!=', 2);
                    })
                    ->leftJoin('pending_invoice_shipments AS pis', function ($join) {
                        $join->on('pis.shipment_id', '=', 'shipments.id')->where('pis.type', '!=', 2);
                    })
                    ->leftJoin('invoice_shipments AS inv_ship', function ($join) {
                        $join->on('inv_ship.shipment_id', '=', 'shipments.id')->where('inv_ship.type', '!=', 2);
                    })
                    ->leftJoin('shipment_additional_charges AS faf_charges', 'faf_charges.shipment_id', '=', 'shipments.id')
                    ->leftJoin('invoices', 'inv_ship.invoice_id', '=', 'invoices.id')
                    ->leftJoin('provinces', 'provinces.id', '=', 'dc.province_id')
                    ->leftJoin('shipments_journey AS dr', function ($join) {
                        $join->on('dr.shipment_id', '=', 'shipments.id')->whereIn('dr.shipper_status_id', [14, 20, 25, 30, 36, 37, 38]);
                        // Subquery for getting MAX(id) and verification = 1 needs to be handled before the query
                    })
                    ->whereNotIn('shipments.shipper_status_id', [1, 17])
                    ->whereBetween('shipments.created_at', [$start,$end])
                    ->where('u.id', 15151)
                    ->groupBy('shipments.id');

                $sales = $query->get()->map(function ($shipment) {
                    $route = route('admin.tracking.index');

                    return [
                        'shipment_id' => $shipment->shipment_id,
                        'tracking_number' => $shipment->tracking_number,
                        'fintech_charges' => number_format($shipment->fintech_amount, 2),
                        'fintech_revenue' => ($shipment->fintech_amount !== null)
                            ? optional(FintechPaymentDetails::where('trax_pay_id', optional(TraxPayTransaction::where('shipment_id', $shipment->shipment_id)->first())->id)->first())->revenue ?? '-'
                            : '-',
                        'insurance_charges' => number_format($shipment->insurance_charges, 2),
                        'invoice_number_button' => '<button class="btn btn-sm btn-outline-info align-middle">' . $shipment->invoice_number . '</button>',
                        'cash_handling_charges' => $shipment->dr_status_id == 20 ? '-' : number_format($shipment->cash_handling_charges ?? 0, 2),
                        'account_no' => str_pad($shipment->account_no, 6, '0', STR_PAD_LEFT),
                        'return_charges' => $shipment->dr_status_id != 20 ? '-' : number_format($shipment->return_charges, 2),
                        'intercept_charges' => number_format($shipment->intercept_charges, 2),
                        'weight_charges' => number_format($shipment->weight_charges, 2),
                        'fuel_surcharge' => number_format($shipment->fuel_surcharge, 2),
                        'replacement_charges' => $shipment->dr_status_id == 20 ? '-' : number_format($shipment->replacement_charges, 2),
                        'try_and_buy_charges' => $shipment->dr_status_id == 20 ? '-' : number_format($shipment->try_and_buy_charges, 2),
                        'nsa_osa_charges' => number_format($shipment->nsa_osa_charges, 2),
                        'packaging_material_charges' => number_format($shipment->packaging_material_charges, 2),
                        'p_total_charges' => number_format(($shipment->p_total_charges ?? 0) + ($shipment->d_total_charges ?? 0) + $shipment->fintech_amount, 2),
                        'p_net_payable' => number_format(($shipment->p_net_payable ?? 0) + ($shipment->d_net_payable ?? 0), 2),
                        'd_total_charges' => number_format($shipment->d_total_charges, 2),
                        'd_net_payable' => number_format($shipment->d_net_payable, 2),
                        'd_gst' => number_format($shipment->d_gst, 2),
                        'packaging_charges' => number_format($shipment->packaging_charges, 2),
                        'tracking_number_link' => "<u><a href='{$route}?tracking_number={$shipment->tracking_number}' class='tracking' target='_blank'>{$shipment->tracking_number}</a></u>",
                        's_collection_amount' => number_format($shipment->s_collection_amount, 2),
                        'd_collection_amount' => number_format($shipment->d_collection_amount, 2),
                        'shipper' => $shipment->booking_type_id == 4 ? "{$shipment->shipper} ({$shipment->poc})" : $shipment->shipper,
                        'p_collection_amount' => number_format(
                            $shipment->p_collection_amount ?: $shipment->d_collection_amount ?: $shipment->s_collection_amount, 2
                        ),
                        'p_gst' => number_format(
                            ($shipment->account_type_id == 1)
                                ? (($shipment->p_gst ?? 0) + ($shipment->d_gst ?? 0))
                                : (($shipment->pis_gst ?? 0) + ($shipment->is_gst ?? 0))
                            , 2),
                        'pps_sms_charges' => number_format(
                            ($shipment->account_type_id == 1)
                                ? (($shipment->pps_sms_charges ?? 0) + ($shipment->dps_sms_charges ?? 0))
                                : (($shipment->pis_sms_charges ?? 0) + ($shipment->is_sms_charges ?? 0))
                            , 2),
                        'pps_wht' => number_format(
                            ($shipment->account_type_id == 1)
                                ? (($shipment->pps_wht ?? 0) + ($shipment->dps_wht ?? 0))
                                : (($shipment->pis_wht ?? 0) + ($shipment->is_wht ?? 0))
                            , 2),
                        'pps_cod_sst' => number_format(
                            ($shipment->account_type_id == 1)
                                ? (($shipment->pps_cod_sst ?? 0) + ($shipment->dps_cod_sst ?? 0))
                                : (($shipment->pis_cod_sst ?? 0) + ($shipment->is_cod_sst ?? 0))
                            , 2),
                        'estimated_charges' => number_format(
                            array_sum([
                                $shipment->weight_charges ?? 0,
                                $shipment->cash_handling_charges ?? 0,
                                $shipment->insurance_charges ?? 0,
                                $shipment->return_charges ?? 0,
                                $shipment->replacement_charges ?? 0,
                                $shipment->fuel_surcharge ?? 0,
                                $shipment->try_and_buy_charges ?? 0,
                                $shipment->packaging_material_charges ?? 0,
                                $shipment->intercept_charges ?? 0,
                            ]), 2),
                        'class' => ($shipment->origin_city_id != $shipment->destination_city_id)
                            ? ['ClassA', 'ClassB', 'ClassC', 'ClassD'][$shipment->class] ?? 'Unknown'
                            : 'Local',
                        'delivered_or_returned' => in_array($shipment->shipment_status, [14, 20, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46, 25])
                            ? $shipment->delivered_or_returned
                            : '',
                        'account_type' => $shipment->account_type_id == 1 ? "Reimbursement" : "Corporate",
                        'faf_charges' => $shipment->faf_charges ?? '-',
                    ];
                });


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
