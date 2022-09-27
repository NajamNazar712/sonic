<?php

namespace App\Http\Models\International\Wholesale;

use Illuminate\Database\Eloquent\Model;

class WholesaleInvoiceHistory extends Model
{
    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'updated_by', 'id');
    }

    public function shipper() {
        return $this->belongsTo('App\Http\Models\International\Wholesale\WholesaleUser', 'wholesale_user_id', 'id');
    }
}
