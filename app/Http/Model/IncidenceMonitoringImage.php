<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class IncidenceMonitoringImage extends Model
{
    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin','added_by','id');
    }
    public function incidence_monitoring() {
        return $this->belongsTo('App\Http\Model\IncidenceMonitoring');
    }
}
