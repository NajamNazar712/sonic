<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TotalSumRetailTraxCenter extends Model
{
    protected $fillable = [
        'retail_user_id',
        'trax_center_code',
        'trax_center_name',
        'sum_of_shipments',
        'sum_of_total_charges',
        'sum_of_gst',
        'sum_of_weight_charges',
        'net_commission',
    ];
}
