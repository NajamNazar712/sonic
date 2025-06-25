<?php

namespace App\Http\Models\Admin\Retail;

use Illuminate\Database\Eloquent\Model;

class RetailShipperInfo extends Model
{
    protected $casts = [
        'password_reset_at' => 'datetime',
    ];

    public function bank() {
        return $this->belongsTo('App\Http\Models\BanksList', 'bank_id', 'id');
    }

    public function retail_city(){
        return $this->belongsTo('App\Http\Models\City','city_id', 'id');
    }
}
