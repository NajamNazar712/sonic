<?php

namespace App\Http\Models\Excel_reports;

use Illuminate\Database\Eloquent\Model;

class QaReportPettyCash extends Model
{
    public function hub() {
        return $this->belongsTo('App\Http\Models\City', 'hub_id', 'id');
    }
}
