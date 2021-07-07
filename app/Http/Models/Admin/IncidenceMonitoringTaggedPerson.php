<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class IncidenceMonitoringTaggedPerson extends Model
{
    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin');
    }

    public function incidence_monitoring() {
        return $this->hasMany('App\Http\Models\Admin\IncidenceMonitoring');
    }
}
