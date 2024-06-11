<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RetailUserCommission extends Model
{
    protected $fillable = [
        'franchise_id',
        'franchise_code',
        'trax_center_name',
        'trax_center_cnic',
        'trax_center_phone',
        'franchise_address',
        'month',
        'retail_shipping_mode_id',
        'retail_shipping_mode_name',
        'number_of_shipments',
        'total_charges_without_gst',
        'product_percentage',
        'commission',
        'gst_percentage',
        'franchise_gst_amount',
        'net_commission',
        'total_charges',
        'weight_charges'
    ];
}
