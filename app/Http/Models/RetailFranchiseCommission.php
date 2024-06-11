<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RetailFranchiseCommission extends Model
{
    protected $fillable = [
        'franchise_id',
        'franchise_code',
        'franchise_cnic',
        'franchise_phone',
        'franchise_name',
        'franchise_location',
        'franchise_address',
        'month',
        'retail_shipping_mode_id',
        'retail_shipping_mode_name',
        'number_of_shipments',
        'total_charges_without_gst',
        'product_percentage',
        'total_charges',
        'weight_charges',
        'commission',
        'gst_percentage',
        'total_charges_with_gst',
        'franchise_gst_amount',
        'franchise_withholding_percentage',
        'franchise_withholding_amount',
        'charges_without_withholding',
        'deduction_percentage',
        'deduction_amount',
        'net_commission'
    ];
    
}
