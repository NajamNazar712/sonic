<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RetailFranchiseCharge extends Model
{
    protected $fillable = [
        'franchise_id',
        'franchise_gst',
        'franchise_withholding',
        'franchise_deduction',
        'security_deposit',
        'license_fees'
    ];
}
