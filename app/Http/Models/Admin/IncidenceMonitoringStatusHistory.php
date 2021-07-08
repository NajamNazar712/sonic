<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class IncidenceMonitoringStatusHistory extends Model
{
    protected $guarded = [];
    
    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin');
    }

    public function incidence_monitoring() {
        return $this->belongsTo('App\Http\Models\Admin\IncidenceMonitoring');
    }

    public function status() {
        return $this->belongsTo('App\Http\Models\Admin\IncidenceMonitoringStatus', 'status_id');
    }
}
