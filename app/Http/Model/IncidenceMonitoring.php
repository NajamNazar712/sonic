<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class IncidenceMonitoring extends Model
{
    public function station()
    {
        return $this->belongsTo('App\Http\Models\City','station_id', 'id');
    }

    public function incidence_monitoring_area() {
        return $this->belongsTo('App\Http\Model\IncidenceMonitoringArea');
    }

    public function incidence_monitoring_case_nature() {
        return $this->belongsTo('App\Http\Model\IncidenceMonitoringCaseNature');
    }

    public function incidence_monitoring_n_c_level() {
        return $this->belongsTo('App\Http\Model\IncidenceMonitoringNCLevel');
    }

    public function status() {
        return $this->belongsTo('App\Http\Model\IncidenceMonitoringStatus', 'status_id', 'id');
    }

    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin');
    }

    public function tagged_persons() {
        return $this->hasMany('App\Http\Model\IncidenceMonitoringTaggedPerson');
    }

    public function status_history() {
        return $this->hasMany('App\Http\Model\IncidenceMonitoringStatusHistory');
    }

}
