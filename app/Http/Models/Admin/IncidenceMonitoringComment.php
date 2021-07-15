<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class IncidenceMonitoringComment extends Model
{
    public function commenter() {
        return $this->belongsTo('App\Http\Models\Admin\Admin','comment_by_id','id');
    }

    public function incidence_monitoring() {
        return $this->belongsTo('App\Http\Models\Admin\IncidenceMonitoring');
    }
}
