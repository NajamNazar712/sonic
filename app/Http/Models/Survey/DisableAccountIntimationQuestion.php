<?php

namespace App\Http\Models\Survey;

use Illuminate\Database\Eloquent\Model;

class DisableAccountIntimationQuestion extends Model
{
    protected $guarded = [];

    public function created_by(){
        return $this->belongsTo('App\Http\Models\Admin\Admin','id','created_by');
    }

    public function updated_by(){
        return $this->belongsTo('App\Http\Models\Admin\Admin','id','updated_by');
    }
}
