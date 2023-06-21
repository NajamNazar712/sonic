<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CrmAgentAutoAssignSubSegment extends Model
{
    public function sub_business_types(){
        return $this->belongsTo('App\Http\Models\SubCategorySegment', 'sub_segment_id', 'id');

    }
}
