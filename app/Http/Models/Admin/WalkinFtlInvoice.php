<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class WalkinFtlInvoice extends Model
{
    public function ftl_request(){
        return $this->belongsTo('App\Http\Models\Admin\FtlRequest', 'ftl_request_id', 'id');

    }

   
}
