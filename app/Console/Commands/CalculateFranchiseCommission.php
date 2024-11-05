<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\RetailFranchiseCommission;
use App\Http\Models\RetailUserCommission;
use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Models\TotalSumFranchiseCommission;
use App\Http\Models\TotalSumRetailTraxCenter;

class CalculateFranchiseCommission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commission:calculate_commission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates Franchise And User commissions on monthly bases';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->franchise_commission_view();
        $this->user_commission_view();
    }

    private function franchise_commission_view()
    {
        $month = str_pad(Carbon::now()->subMonth()->month, 2, "0", STR_PAD_LEFT);

        $shipments = Shipment::join('retail_shipments as rs', 'rs.shipment_id', '=', 'shipments.id')
        ->leftjoin('retail_users as ru', 'ru.id', '=', 'rs.retail_user_id')
        ->leftjoin('retail_shipper_infos as rsi', 'rsi.id', '=', 'rs.shipper_account_no')
        ->leftJoin('retail_franchises as rf', function ($join) {
            $join->on('rf.id', '=', 'ru.category_id')
            ->where('ru.category', '=', DB::raw(1));
        })
            ->leftJoin('retail_trax_centers as rc', function ($join) {
                $join->on('rc.id', '=', 'ru.category_id')
                ->where('ru.category', '=', DB::raw(2));
            })
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->join('retail_shipping_modes as rsm', 'rsm.id', '=', 'rs.shipping_mode')
            ->join('user_shipping_infos AS usi', 'rf.pickup_address_id', '=', 'usi.id')
            ->leftjoin('retail_trax_centers as rtc', 'rtc.pickup_address_id', '=', 'shipments.pickup_address_id')
             ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                ->where(
                    'sj.id',
                    '=',
                    DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)')
                );
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                ->where(
                    'dr.id',
                    '=',
                    DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37) and shipments_journey.verification = 1)')
                );
            })
            ->leftjoin('retail_franchise_charges as rfc', 'rf.id', '=', 'rfc.franchise_id')
            ->join('retail_franchise_product_percentages as rfpp', function ($join) {
                $join->on('rfpp.franchise_id', '=', 'rf.id')
                    ->on('rfpp.retail_shipping_mode_id', '=', 'rs.shipping_mode');
            })
            ->select([
                DB::raw('COUNT(rs.id) as shipment_count'),
                DB::raw('SUM(rs.total_charges_without_gst) as total_charges_without_gst'),
                DB::raw('SUM(rs.gst) as gst_amount'),
                DB::raw('SUM(rs.total_charges_without_gst + rs.gst) as total_charges'),
                DB::raw('SUM(rs.weight_charges) as weight_charges'),

                'rf.id as franchise_id',
                'rf.code as franchise_code',
                'rf.name as franchise_name',
                'rf.cnic as franchise_cnic',
                'rf.phone_no as franchise_phone',

                'usi.pickup_address as address',

               'rfpp.product_percentage as product_percentage',

                'rfc.franchise_deduction as franchise_deduction',
                'rfc.franchise_gst as gst_percentage',
                'rfc.franchise_withholding as withholding_percentage',

                'rsm.id as retail_shipping_mode_id',
                'rsm.name as shipping_mode_name',
            ])
            // ->select('p.product_name as category', 'shipments.id as shipment_id', 'shipments.tracking_number', 'shipments.tracking_number as tracking_number_link', 'ru.name as booked_by', 'ru.category as retail_category', 'ru.id as booked_by_id', 'rsi.shipper_name', 'rf.id as franchise_account_id', 'rf.name as franchise', 'rc.id as retail_account_id', 'rc.name as retail_center', 'ss.name as current_status', 'rsm.name as service_type', 'sj.created_at as arrival_date', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'oz.name as origin_zone', 'dz.name as destination_zone', 'shipments.amount as collection_amount', 'sps.name as payment_status', 'pps.amount as p_collection_amount', 'shipments.actual_weight', 'rs.weight_charges', 'rs.cash_handling_charges', 'rs.fuel_surcharge', 'rs.total_charges as total_charges', 'rs.gst as gst', 'pps.payable as p_net_payable', 'dps.amount as d_collection_amount', 'dps.payable as d_net_payable', 'dr.created_at as delivered_or_returned', 'dps.retail_done_payment_id as payment_id', 'shipments.shipper_status_id as shipment_status', 'dr.shipper_status_id as dr_status_id', 'pns.retail_pickup_note_id as pncc_id', 'rtc.name as retail_trax_center_name', 'rref.ref as retail_reference', 'rf.discount as franchise_discount', 'rf.insurance as franchise_insurance', 'rtc.discount as trax_discount', 'rtc.insurance as trax_insurance', 'rs.discount as discount_amount', 'rs.insurance_charges as insurance_charges', 'rs.packaging_charges as packaging_charges', 'si.price as product_value')
            ->whereNotIn('shipments.shipper_status_id', [1, 17])
            ->whereYear('sj.created_at',date('Y'))
            ->whereMonth('sj.created_at',$month)
            ->where('shipments.shipment_type', 2)
            ->groupBy([
                'rs.shipping_mode',
                'rf.id'
            ])
            ->get();
        foreach ($shipments as $shipment) {
            $product_percentage_amount = $shipment->product_percentage / 100;
            $weight_charges = $shipment->total_charges - $shipment->gst_amount;
            $commission = $product_percentage_amount * $weight_charges;

            RetailFranchiseCommission::create([
                'franchise_id' => $shipment->franchise_id,
                'franchise_code' => $shipment->franchise_code,
                'franchise_name' => $shipment->franchise_name,
                'franchise_cnic' => $shipment->franchise_cnic,
                'franchise_phone' => $shipment->franchise_phone,
                'franchise_address' => $shipment->franchise_address,
                'month' => $month,
                'retail_shipping_mode_id' => $shipment->retail_shipping_mode_id,
                'retail_shipping_mode_name' => $shipment->shipping_mode_name,
                'number_of_shipments' => $shipment->shipment_count,
                'total_charges_without_gst' => $shipment->total_charges_without_gst,
                'total_charges' => $shipment->total_charges,
                'weight_charges' => $weight_charges,
                'gst_percentage' => $shipment->gst_percentage,
                'franchise_gst_amount' => $shipment->gst_amount,
                'product_percentage' => $shipment->product_percentage,
                'franchise_withholding_percentage' => $shipment->withholding_percentage,
                'commission' => $commission,
                'franchise_withholding_percentage' => $shipment->withholding_percentage,
                'deduction_percentage' => $shipment->franchise_deduction,
            ]);
        }

        // // get summed data
        $summed_data = RetailFranchiseCommission::select(
            'franchise_id',
            'franchise_code',
            'franchise_name',
            DB::raw('SUM(commission) as total_commission'),
            DB::raw('SUM(total_charges) as total_charges'),
            DB::raw('SUM(franchise_gst_amount) as total_gst_amount'),
            DB::raw('SUM(weight_charges) as total_weight_charges'),
            DB::raw('SUM(number_of_shipments) as total_number_of_shipments')
        )
        ->groupBy('franchise_id', 'franchise_code', 'franchise_name')
        ->get();
        
        foreach ($summed_data as $data) {
            $franchise = RetailFranchiseCommission::where('franchise_id', $data->franchise_id)->first();
            $withholding_percentage = $franchise->franchise_withholding_percentage;
            $deduction_percentage = $franchise->deduction_percentage;
        
            // Calculate amounts
            $withholding_amount = ($withholding_percentage / 100) * $data->total_commission;
            $deduction_amount = ($deduction_percentage / 100) * $data->total_commission;
            $net_commission = $data->total_commission - ($withholding_amount + $deduction_amount);
        
            $insert_data = [
                'franchise_id' => $data->franchise_id,
                'franchise_code' => $data->franchise_code,
                'franchise_name' => $data->franchise_name,
                'sum_of_all_shipments' => $data->total_number_of_shipments,
                'sum_of_total_charges' => $data->total_charges,
                'sum_of_gst' => $data->total_gst_amount,
                'sum_of_weight_charges' => $data->total_weight_charges,
                'sum_of_commission' => $data->total_commission,
                'withholding_tax_percent' => $withholding_percentage,
                'withholding_amount' => $withholding_amount,
                'commission_gst_deduction_percent' => $deduction_percentage,
                'deduction_amount' => $deduction_amount,
                'net_commission' => $net_commission,
            ];
            TotalSumFranchiseCommission::create($insert_data);
        }
        
    }

    private function user_commission_view()
    {
        $franchise_users = RetailUser::where('category', 2)->get();
        $user_ids = $franchise_users->pluck('id')->toArray();
        $month = str_pad(Carbon::now()->subMonth()->month, 2, "0", STR_PAD_LEFT);

        $baseQuery = RetailShipment::query()
            ->leftJoin('retail_users', 'retail_shipments.retail_user_id', '=', 'retail_users.id')
            ->leftJoin('retail_trax_centers', 'retail_users.category_id', '=', 'retail_trax_centers.id')
            ->leftJoin('retail_user_product_percentages', function($join) {
                $join->on('retail_user_product_percentages.retail_user_id', '=', 'retail_shipments.retail_user_id')
                    ->on('retail_user_product_percentages.retail_shipping_mode_id', '=', 'retail_shipments.shipping_mode');
            })
            ->leftJoin('retail_shipping_modes', 'retail_shipments.shipping_mode', '=', 'retail_shipping_modes.id');

        $shipments = $baseQuery
            ->whereIn('retail_shipments.retail_user_id', $user_ids)
            ->whereMonth('retail_shipments.created_at', $month)
            ->select([
                DB::raw('COUNT(retail_shipments.id) as shipment_count'),
                DB::raw('SUM(retail_shipments.total_charges_without_gst) as total_charges_without_gst'),
                DB::raw('SUM(retail_shipments.gst) as gst_amount'),
                DB::raw('SUM(retail_shipments.total_charges) as total_charges'),
                DB::raw('SUM(retail_shipments.weight_charges) as weight_charges'),

                'retail_users.id as retail_user_id',
                'retail_trax_centers.code as trax_center_code',
                'retail_users.name as trax_center_name',
                'retail_trax_centers.cnic as trax_center_cnic',
                'retail_trax_centers.phone_no as trax_center_phone',
                'retail_users.address as franchise_address',
                'retail_user_product_percentages.product_percentage as trax_center_product_percentage',
                'retail_shipping_modes.id as retail_shipping_mode_id',
                'retail_shipping_modes.name as retail_shipping_mode_name',
            ])
            ->groupBy([
                'retail_user_id',
                'retail_shipping_mode_id'
            ])
            ->get();

        foreach ($shipments as $shipment) {
            $weight_charges = $shipment->total_charges - $shipment->gst_amount;
            $net_commission = ($shipment->trax_center_product_percentage / 100) * $weight_charges;
            RetailUserCommission::create([
                'franchise_id' => $shipment->retail_user_id,
                'franchise_code' => $shipment->trax_center_code,
                'trax_center_name' => $shipment->trax_center_name,
                'trax_center_cnic' => $shipment->trax_center_cnic,
                'trax_center_phone' => $shipment->trax_center_phone,
                'franchise_address' => $shipment->franchise_address,
                'month' => $month,
                'retail_shipping_mode_id' => $shipment->retail_shipping_mode_id,
                'retail_shipping_mode_name' => $shipment->retail_shipping_mode_name,
                'number_of_shipments' => $shipment->shipment_count,
                'total_charges_without_gst' => $shipment->total_charges_without_gst,
                'net_commission' => $net_commission,
                'commission' => $shipment->trax_center_product_percentage,
                'franchise_gst_amount' => $shipment->gst_amount,
                'total_charges' => $shipment->total_charges,
                'weight_charges' => $shipment->weight_charges,
            ]);
        }

        // get summed data
        $summed_data = RetailUserCommission::select(
            'franchise_id',
            'franchise_code',
            'trax_center_name',
            DB::raw('SUM(commission) as total_commission'),
            DB::raw('SUM(total_charges) as total_charges'),
            DB::raw('SUM(franchise_gst_amount) as total_gst_amount'),
            DB::raw('SUM(weight_charges) as total_weight_charges'),
            DB::raw('SUM(number_of_shipments) as total_number_of_shipments')
        )
        ->groupBy('franchise_id', 'franchise_code', 'trax_center_name')
        ->get();

        foreach ($summed_data as $data) {
            $insert_data = [
                'retail_user_id' => $data->franchise_id,
                'trax_center_code' => $data->franchise_code,
                'trax_center_name' => $data->trax_center_name,
                'sum_of_shipments' => $data->total_number_of_shipments,
                'sum_of_total_charges' => $data->total_charges,
                'sum_of_gst' => $data->total_gst_amount,
                'sum_of_weight_charges' => $data->total_weight_charges,
                'net_commission' => $data->total_commission,
            ];
            TotalSumRetailTraxCenter::create($insert_data);
        }
    }

}
