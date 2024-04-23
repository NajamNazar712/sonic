<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RetailFranchiseProductPerecntage extends Model
{
    protected $fillable = [
        'franchise_id',
        'product_id',
        'product_percentage',
        'created_by',
        'updated_by',
    ];
}
