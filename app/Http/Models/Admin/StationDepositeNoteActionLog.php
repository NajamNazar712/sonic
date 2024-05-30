<?php

namespace App\HTTP\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StationDepositeNoteActionLog extends Model
{
    public function updated_by(){
        return $this->belongsTo('App\Http\Models\Admin\Admin','admin_id','id');
    }
}
