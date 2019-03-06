<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateReturnCharge extends Model
{
    protected $table = 'corporate_return_charges';
    protected $fillable = [
        'user_id','shipping_mode_id','local','national'
    ];
}
