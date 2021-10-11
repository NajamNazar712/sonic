<?php

namespace App\Http\Models\Admin\Lead;

use Illuminate\Database\Eloquent\Model;

class LeadRemark extends Model
{
    public function admin(){
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'updated_by', 'id');
    }
}
