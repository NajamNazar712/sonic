<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class HighAlertShipper extends Model
{
    public function alerted_by() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'alert_by');
    }
}
