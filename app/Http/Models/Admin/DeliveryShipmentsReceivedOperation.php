<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DeliveryShipmentsReceivedOperation extends Model
{
    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'admin_id', 'id');
    }
}
