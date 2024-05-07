<?php

namespace App\Http\Models\Admin;

use App\Http\Models\Shipper\User;
use Illuminate\Database\Eloquent\Model;

class ShipperInterceptExclude extends Model
{
    protected $fillable = [
        'exclude_shipper',
        'different_consignee',
        'same_consignee',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
