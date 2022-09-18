<?php

namespace App\Http\Models\International\Wholesale;

use Illuminate\Database\Eloquent\Model;

class WholesaleUserDocument extends Model
{
    public function added_by() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'added_by', 'id');
    }

}
