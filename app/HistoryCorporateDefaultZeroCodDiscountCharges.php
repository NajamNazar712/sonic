<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryCorporateDefaultZeroCodDiscountCharges extends Model
{
    protected $table = 'history_cor_def_zero_cod_discount_charges';

    protected $fillable = [
        'user_id','shipping_mode_id','cod_discount_per'
    ];
}
