<?php

namespace App\Http\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class CrmComments extends Model
{
    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin','comment_by_id','id');
    }
}
