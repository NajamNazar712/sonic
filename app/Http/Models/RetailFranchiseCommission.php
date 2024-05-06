<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RetailFranchiseCommission extends Model
{
    protected $fillable = [
        'franchise_id',
        'franchise_code',
        'month',
        'retail_shipping_mode_id',
        'product_percentage',
        'number_of_shipments',
        'total_charges_without_gst',
        'commission',
        'franchise_gst_amount',
        'total_charges_with_gst',
        'franchise_withholding_amount',
        'charges_without_withholding',
        'deduction_amount',
        'net_commission',
    ];
}
