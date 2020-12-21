<?php

namespace App\http\Models\Admin\Lead;

use Illuminate\Database\Eloquent\Model;

class LeadRemark extends Model
{
    public function admin(){
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'updated_by', 'id');
    }
}
