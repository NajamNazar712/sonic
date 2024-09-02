<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PendingCorporateDefaultZeroCodDiscountCharges extends Model
{
    protected $table = 'pending_cor_def_zero_cod_discount_charges';

    protected $fillable = [
        'user_id','shipping_mode_id','cod_discount_per'
    ];
}
