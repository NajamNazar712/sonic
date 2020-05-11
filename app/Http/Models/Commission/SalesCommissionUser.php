<?php

namespace App\Http\Models\Commission;

use Illuminate\Database\Eloquent\Model;

class SalesCommissionUser extends Model
{
    public function sales_person(){
        return $this->belongsTo('App\http\Models\Admin\Admin','user_id');
    }
    public function sales_person_external(){
        return $this->belongsTo('App\http\Models\Commission\SalesCommissionExternalUser','user_id');
    }
    public function tier(){
        return $this->belongsTo('App\http\Models\Commission\SalesTier','tier_id');
    }
}
