<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class SalePersonTag extends Model
{
    protected $table = 'sale_person_tags';

    public function sales_person(){
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'admin_id', 'id');
    }

}
