<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateDefaultRateRemarks extends Model
{
    public function shipper() {
        return $this->belongsTo('App\Http\Models\Shipper\User');
    }
    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'admin_id', 'id');
    }
}
