<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CrmAgentAutoAssignBusSeg extends Model
{
   public function business_types(){
       return $this->belongsTo('App\Http\Models\Admin\Segment', 'business_segment_id', 'id');

   }

}
