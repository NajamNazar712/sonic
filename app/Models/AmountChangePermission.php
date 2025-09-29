<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmountChangePermission extends Model
{
    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo('App\Http\Models\Shipper\User');
    }
}
