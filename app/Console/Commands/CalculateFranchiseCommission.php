<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\RetailFranchiseCommission;
use App\Http\Models\RetailUserCommission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        // $this->user_commission_view();
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

            $retail_franchise_commission = RetailFranchiseCommission::create([
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
                'commission' => $commission,
            ]);
        }

        // store summed data
        
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
            'retail_shipments.created_at as shipment_month',
            'retail_shipments.total_charges_without_gst as total_charges_without_gst',
            'retail_shipments.gst as gst_amount',
            'retail_shipments.total_charges as total_charges',
            'retail_shipments.weight_charges as weight_charges',

            'retail_users.id as retail_user_id',

            'retail_trax_centers.code as trax_center_code',
            'retail_users.name as trax_center_name',
            'retail_trax_centers.cnic as trax_center_cnic',
            'retail_trax_centers.phone_no as trax_center_phone',

            'retail_users.address as franchise_address',

            'retail_user_product_percentages.product_percentage as trax_center_product_percentage',

            'retail_shipping_modes.id as retail_shipping_mode_id',
            'retail_shipping_modes.name as retail_shipping_mode_name',

            DB::raw('COUNT(retail_shipments.id) as shipment_count')
        ])
        ->groupBy([
            'shipment_month',
            'retail_user_id',
            'retail_shipping_mode_id'
        ])
        ->get();

        foreach ($shipments as $shipment) {

            $shipmentCount = $shipment->shipment_count;
            $product_percentage_amount = $shipment->trax_center_product_percentage / 100 ;
            $net_commission = $product_percentage_amount * $shipment->weight_charges;

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
                'number_of_shipments' => $shipmentCount,
                'total_charges_without_gst' => $shipment->total_charges_without_gst,
                'net_commission' => $net_commission,
                'commission' => $shipment->trax_center_product_percentage,
                'franchise_gst_amount' => $shipment->gst_amount,
                'total_charges' => $shipment->total_charges,
                'weight_charges' => $shipment->weight_charges,
                // 'product_percentage',
                // 'total_charges_with_gst',
                // 'gst_percentage',
            ]);
        }
    }
}
