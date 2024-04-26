<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RetailFranchiseProductPerecntage extends Model
{
    protected $fillable = [
        'franchise_id',
        'created_by',
        'updated_by',
        'retail_shipping_mode_id',
        'product_percentage',
        'commission_percentage',
        'withholding_tax_percentage',
        'deduction_percentage',
        'attachment_1',
        'attachment_2',
        'attachment_3',
        'attachment_4',
        'attachment_5',
    ];
}
