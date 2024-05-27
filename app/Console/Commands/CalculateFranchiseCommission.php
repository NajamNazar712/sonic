<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\RetailFranchiseCommission;
use App\Http\Models\RetailUserCommission;
use Carbon\Carbon;

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
        $month = Carbon::now()->subMonth()->month;

        $baseQuery = RetailShipment::query()
        ->leftJoin('retail_users', 'retail_shipments.category_id', '=', 'retail_users.category_id')
        ->leftJoin('retail_franchises', 'retail_users.category_id', '=', 'retail_franchises.id')
        ->leftJoin('retail_franchise_product_percentages', function($join) {
            $join->on('retail_franchises.id', '=', 'retail_franchise_product_percentages.franchise_id')
                ->on('retail_shipments.shipping_mode', '=', 'retail_franchise_product_percentages.retail_shipping_mode_id');
        })
        ->leftJoin('retail_franchise_charges', function($join) {
            $join->on('retail_franchises.id', '=', 'retail_franchise_charges.franchise_id');
        })
        ->leftJoin('retail_shipping_modes', 'retail_shipments.shipping_mode', '=', 'retail_shipping_modes.id');

        $shipments = $baseQuery
        ->whereIn('retail_shipments.category_id', $user_ids)
        ->whereMonth('retail_shipments.created_at', $month)
        ->groupBy('retail_shipments.shipment_id')
        ->select([
            'retail_shipments.*',
            'retail_shipments.created_at as shipment_month',
            'retail_users.*',
            'retail_franchises.code',
            'retail_franchise_product_percentages.product_percentage',
            'retail_franchise_charges.franchise_gst',
            'retail_franchise_charges.franchise_withholding',
            'retail_franchise_charges.franchise_deduction',
            'retail_shipping_modes.name as shipping_mode_name',
        ])
        ->get();
        foreach ($shipments as $shipment) {
            $shipmentCounts = $shipments->where('category_id', $shipment->category_id)
            ->where('shipping_mode', $shipment->shipping_mode)
            ->count();
            $shipment->shipmentCounts = $shipmentCounts;
            // Calculate commission percentage
            if ($shipment->product_percentage !== null) {
                $product_percentage = $shipment->product_percentage / 100;
                $commission = $product_percentage * $shipment->total_charges_without_gst;

            } else {
                $commission = '-';
                $shipment->product_percentage = '-';
            }

            // Calculate GST
            if ($shipment->franchise_gst !== null &&  $commission !== null) {
                $franchise_gst = $shipment->franchise_gst / 100;
                $gst = $commission * $franchise_gst;
                $charges_with_gst = $gst + $commission; 
            } else {
                $gst = '-';
                $charges_with_gst = '-';
                $shipment->franchise_gst = '-';
            }
        
            // Calculate withholding
            if ($shipment->franchise_withholding !== null) {
                $franchise_withholding_amount = $shipment->franchise_withholding;
                $franchise_withholding = $shipment->franchise_withholding / 100;
                $withholding = $charges_with_gst !== null ? $charges_with_gst * $franchise_withholding : null;
                $charges_without_withholding = $charges_with_gst - $withholding;
            } else {
                $withholding = '-';
                $charges_without_withholding = '-';
                $shipment->franchise_withholding = '-';
            }
        
            // Calculate deduction
            if ($shipment->franchise_deduction !== null) {
                $franchise_deduction_percentage = $shipment->franchise_deduction;
                $franchise_deduction = $shipment->franchise_deduction / 100;
                $deduction = $charges_without_withholding !== null ? $charges_without_withholding * $franchise_deduction : null;
                $net_commission = $charges_without_withholding - $deduction;
            } else {
                $deduction = '-';
                $net_commission = '-';
                $shipment->franchise_deduction = '-';
            }

            RetailFranchiseCommission::create([
                'franchise_id' => $shipment->retail_user_id,
                'month' => $month,
                'retail_shipping_mode_id' => $shipment->shipping_mode,
                'franchise_code' => $shipment->code,
                'number_of_shipments' => $shipmentCounts,
                'total_charges_without_gst' => $shipment->total_charges_without_gst,
                'product_percentage' => $shipment->product_percentage,
                'commission' => $commission,
                'gst_percentage' => $shipment->franchise_gst,
                'franchise_gst_amount' => $gst,
                'total_charges_with_gst' => $charges_with_gst,
                'franchise_withholding_percentage' => $shipment->franchise_withholding,
                'franchise_withholding_amount' => $withholding,
                'charges_without_withholding' => $charges_without_withholding,
                'deduction_percentage' => $shipment->franchise_deduction,
                'deduction_amount' => $deduction,
                'net_commission' => $net_commission,
            ]);
        }
    }

    private function user_commission_view()
    {
        $franchise_users = RetailUser::where('category', 2)->get();
        $user_ids = $franchise_users->pluck('id')->toArray();   
        $month = Carbon::now()->subMonth()->month;

        $baseQuery = RetailShipment::query()
        ->leftJoin('retail_users', 'retail_shipments.retail_user_id', '=', 'retail_users.id')
        ->leftJoin('retail_franchises', 'retail_users.category_id', '=', 'retail_franchises.id')
        ->leftJoin('retail_franchise_product_percentages', function($join) {
            $join->on('retail_franchises.id', '=', 'retail_franchise_product_percentages.franchise_id')
                ->on('retail_shipments.shipping_mode', '=', 'retail_franchise_product_percentages.retail_shipping_mode_id');
        })
        ->leftJoin('retail_franchise_charges', function($join) {
            $join->on('retail_franchises.id', '=', 'retail_franchise_charges.franchise_id');
        })
        ->leftJoin('retail_shipping_modes', 'retail_shipments.shipping_mode', '=', 'retail_shipping_modes.id')
        ->leftJoin('retail_user_product_percentages', 'retail_shipments.retail_user_id', '=', 'retail_user_product_percentages.retail_user_id');

        $shipments = $baseQuery
        ->whereIn('retail_shipments.retail_user_id', $user_ids)
        ->whereMonth('retail_shipments.created_at', $month)
        ->groupBy('retail_shipments.shipment_id')
        ->select([
            'retail_shipments.*',
            'retail_shipments.created_at as shipment_month',
            'retail_users.*',
            'retail_franchises.code',
            'retail_franchise_product_percentages.product_percentage',
            'retail_franchise_charges.franchise_gst',
            'retail_franchise_charges.franchise_withholding',
            'retail_franchise_charges.franchise_deduction',
            'retail_shipping_modes.name as shipping_mode_name',
            'retail_user_product_percentages.product_percentage as retail_user_product_percentages'
        ])
        ->get();
        
        foreach ($shipments as $shipment) {
            $shipmentCounts = $shipments->where('category_id', $shipment->category_id)
            ->where('shipping_mode', $shipment->shipping_mode)
            ->count();
            $shipment->shipmentCounts = $shipmentCounts;
            // Calculate commission percentage
            if ($shipment->retail_user_product_percentages !== null) {
                $product_percentage = $shipment->retail_user_product_percentages / 100;
                $commission = $product_percentage * $shipment->total_charges_without_gst;
            } else {
                $commission = '-';
                $shipment->retail_user_product_percentages = '-';
            }

            // Calculate GST
            if ($shipment->franchise_gst !== null &&  $commission !== null) {
                $franchise_gst = $shipment->franchise_gst / 100;
                $gst = $commission * $franchise_gst;
                $charges_with_gst = $gst + $commission; 
            } else {
                $gst = '-';
                $charges_with_gst = '-';
                $shipment->franchise_gst = '-';
            }

            // Calculate withholding
            if ($shipment->franchise_withholding !== null) {
                $franchise_withholding_amount = $shipment->franchise_withholding;
                $franchise_withholding = $shipment->franchise_withholding / 100;
                $withholding = $charges_with_gst !== null ? $charges_with_gst * $franchise_withholding : null;
                $charges_without_withholding = $charges_with_gst - $withholding;
            } else {
                $franchise_withholding_amount = '-';
                $withholding = '-';
                $charges_without_withholding = '-';
                $shipment->franchise_withholding = '-';
            }

            // Calculate deduction
            if ($shipment->franchise_deduction !== null) {
                $franchise_deduction_percentage = $shipment->franchise_deduction;
                $franchise_deduction = $shipment->franchise_deduction / 100;
                $deduction = $charges_without_withholding !== null ? $charges_without_withholding * $franchise_deduction : null;
                $net_commission = $charges_without_withholding - $deduction;
            } else {
                $franchise_deduction_percentage = '-';
                $deduction = '-';
                $net_commission = '-';
                $shipment->franchise_deduction = '-';
            }

            RetailUserCommission::create([
                'franchise_id' => $shipment->retail_user_id,
                'month' => $month,
                'retail_shipping_mode_id' => $shipment->shipping_mode,
                'franchise_code' => $shipment->code,
                'number_of_shipments' => $shipmentCounts,
                'total_charges_without_gst' => $shipment->total_charges_without_gst,
                // 'product_percentage' => $shipment->product_percentage,
                'product_percentage' => $shipment->retail_user_product_percentages,
                'commission' => $commission,
                'gst_percentage' => $shipment->franchise_gst,
                'franchise_gst_amount' => $gst,
                'total_charges_with_gst' => $charges_with_gst,
                'franchise_withholding_percentage' => $shipment->franchise_withholding,
                'franchise_withholding_amount' => $withholding,
                'charges_without_withholding' => $charges_without_withholding,
                'deduction_percentage' => $shipment->franchise_deduction,
                'deduction_amount' => $deduction,
                'net_commission' => $net_commission,
            ]);
        }
    }
}
