<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class IncidenceMonitoring extends Model
{
    public function station()
    {
        return $this->belongsTo('App\Http\Models\City','station_id', 'id');
    }

    public function monitoring_area() {
        return $this->belongsTo('App\Http\Models\Admin\IncidenceMonitoringArea', 'area_id', 'id');
    }

    public function case_nature() {
        return $this->belongsTo('App\Http\Models\Admin\IncidenceMonitoringCaseNature', 'case_nature_id', 'id');
    }

    public function nc_level() {
        return $this->belongsTo('App\Http\Models\Admin\IncidenceMonitoringNCLevel', 'nc_level_id', 'id');
    }

    public function status() {
        return $this->belongsTo('App\Http\Models\Admin\IncidenceMonitoringStatus', 'status_id', 'id');
    }

    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin');
    }

    public function tagged_persons() {
        return $this->hasMany('App\Http\Models\Admin\IncidenceMonitoringTaggedPerson');
    }

    public function status_history() {
        return $this->hasMany('App\Http\Models\Admin\IncidenceMonitoringStatusHistory');
    }

    public function comments(){
        return $this->hasMany('App\Http\Models\Admin\IncidenceMonitoringComment');
    }

    public function images(){
        return $this->hasMany('App\Http\Models\Admin\IncidenceMonitoringImage');
    }
}
