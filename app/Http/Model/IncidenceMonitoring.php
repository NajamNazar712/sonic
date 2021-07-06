<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class IncidenceMonitoring extends Model
{
    public function station()
    {
        return $this->belongsTo('App\Http\Models\City','station_id', 'id');
    }

    public function monitoring_area() {
        return $this->belongsTo('App\Http\Models\IncidenceMonitoringArea');
    }

    public function case_nature() {
        return $this->belongsTo('App\Http\Models\IncidenceMonitoringCaseNature');
    }

    public function nc_level() {
        return $this->belongsTo('App\Http\Models\IncidenceMonitoringNCLevel');
    }

    public function status() {
        return $this->belongsTo('App\Http\Models\IncidenceMonitoringStatus', 'status_id', 'id');
    }

    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin');
    }

    public function tagged_persons() {
        return $this->hasMany('App\Http\Model\IncidenceMonitoringTaggedPerson');
    }

    public function status_history() {
        return $this->hasMany('App\Http\Model\IncidenceMonitoringStatusHistory');
    }

}
