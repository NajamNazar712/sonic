<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TotalSumFranchiseCommission extends Model
{
    protected $fillable = [
        'franchise_id',
        'franchise_code',
        'franchise_name',
        'sum_of_all_shipments',
        'sum_of_total_charges',
        'sum_of_gst',
        'sum_of_weight_charges',
        'sum_of_commission',
        'withholding_tax_percent',
        'commission_gst_deduction_percent',
        'net_commission',
    ]; 
}