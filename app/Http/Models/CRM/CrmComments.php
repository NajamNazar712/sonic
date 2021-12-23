<?php

namespace App\Http\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class CrmComments extends Model
{
    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin','comment_by_id','id');
    }
    public function updated_by_admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin','comment_updated_by','id');
    }
    public function substitute_user() {
        return $this->belongsTo('App\Http\Models\Shipper\SubstituteUser','comment_by_id','id');
    }
    public function shipper() {
        return $this->belongsTo('App\Http\Models\Shipper\User','comment_by_id','id');
    }
    public function rider() {
        return $this->belongsTo('App\Http\Models\Rider','comment_by_id','id');
    }
}
