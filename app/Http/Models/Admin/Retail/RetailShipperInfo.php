<?php

namespace App\http\Models\Admin\Retail;

use Illuminate\Database\Eloquent\Model;

class RetailShipperInfo extends Model
{
    public function bank() {
        return $this->belongsTo('App\Http\Models\BanksList', 'bank_id', 'id');
    }
}
