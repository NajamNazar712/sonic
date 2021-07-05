<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class IncidenceMonitoringStatusHistory extends Model
{
    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin');
    }

    public function incidence_monitoring() {
        return $this->belongsTo('App\Http\Model\IncidenceMonitoring');
    }

    public function status() {
        return $this->belongsTo('App\Http\Models\IncidenceMonitoringStatus', 'status_id');
    }
}
