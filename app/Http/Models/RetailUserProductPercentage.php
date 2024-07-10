<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RetailUserProductPercentage extends Model
{
    protected $fillable = [
        'retail_user_id',
        'retail_shipping_mode_id',
        'product_percentage',
        // 'commission_percentage',
        // 'gst_percentage',
        'created_by',
        'updated_by',
    ];
}
