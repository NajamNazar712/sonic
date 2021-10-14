<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategorySegment extends Model
{
    public function segment(){
        return $this->belongsTo('App\Http\Models\Segment');
    }
}
