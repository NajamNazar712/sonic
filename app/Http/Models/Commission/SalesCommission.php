<?php

namespace App\Http\Models\Commission;

use Illuminate\Database\Eloquent\Model;

class SalesCommission extends Model
{
    public function users() {
        return $this->hasMany('App\Http\Models\Commission\SalesCommissionUser','sales_commission_id');
    }
}
