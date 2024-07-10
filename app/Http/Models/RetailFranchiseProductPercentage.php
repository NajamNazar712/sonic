<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RetailFranchiseProductPercentage extends Model
{
    protected $fillable = [
        'franchise_id',
        'retail_shipping_mode_id',
        'product_percentage',
        'created_by',
        'updated_by',
    ];
}
