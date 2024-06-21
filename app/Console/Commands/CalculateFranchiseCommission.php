<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\RetailFranchiseCommission;
use App\Http\Models\RetailUserCommission;
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
        $franchise_users = RetailUser::where('category', 1)->get();
        $user_ids = $franchise_users->pluck('category_id')->toArray();
        $month = str_pad(Carbon::now()->subMonth()->month, 2, "0", STR_PAD_LEFT);

        $baseQuery = RetailShipment::query()
            ->leftJoin('retail_users', 'retail_shipments.retail_user_id', '=', 'retail_users.id')
            ->leftJoin('retail_franchises', 'retail_users.category_id', '=', 'retail_franchises.id')
            ->leftJoin('retail_franchise_product_percentages', function($join) {
                $join->on('retail_franchise_product_percentages.franchise_id', '=', 'retail_franchises.id')
                    ->on('retail_franchise_product_percentages.retail_shipping_mode_id', '=', 'retail_shipments.shipping_mode');
            })
            ->leftJoin('retail_shipping_modes', 'retail_shipments.shipping_mode', '=', 'retail_shipping_modes.id')
            ->leftJoin('retail_franchise_charges', 'retail_franchises.id', '=', 'retail_franchise_charges.franchise_id');

        $shipments = $baseQuery
            ->whereIn('retail_shipments.category_id', $user_ids)
            ->whereMonth('retail_shipments.created_at', $month)
            ->select([
                DB::raw('COUNT(retail_shipments.id) as shipment_count'),
                DB::raw('SUM(retail_shipments.total_charges_without_gst) as total_charges_without_gst'),
                DB::raw('SUM(retail_shipments.gst) as gst_amount'),
                DB::raw('SUM(retail_shipments.total_charges) as total_charges'),
                DB::raw('SUM(retail_shipments.weight_charges) as weight_charges'),
                
                'retail_franchises.id as franchise_id',
                'retail_franchises.code as franchise_code',
                'retail_franchises.name as franchise_name',
                'retail_franchises.cnic as franchise_cnic',
                'retail_franchises.phone_no as franchise_phone',
                
                'retail_users.address as franchise_address',

                'retail_franchise_product_percentages.product_percentage as product_percentage',

                'retail_franchise_charges.franchise_deduction as franchise_deduction',
                'retail_franchise_charges.franchise_gst as gst_percentage',
                'retail_franchise_charges.franchise_withholding as withholding_percentage',

                'retail_shipping_modes.id as retail_shipping_mode_id',
                'retail_shipping_modes.name as shipping_mode_name',
            ])
            ->groupBy([
                'retail_franchises.id',
                'retail_franchises.code',
                'retail_franchises.name',
                'retail_franchises.cnic',
                'retail_franchises.phone_no',
                'retail_users.address',
                'retail_franchise_product_percentages.product_percentage',
                'retail_franchise_charges.franchise_deduction',
                'retail_franchise_charges.franchise_gst',
                'retail_franchise_charges.franchise_withholding',
                'retail_shipping_modes.id',
                'retail_shipping_modes.name'
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

        // get summed data
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
