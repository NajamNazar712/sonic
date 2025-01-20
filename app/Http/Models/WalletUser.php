<?php

namespace App\Http\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletUser extends Model
{
    use HasFactory;

    static function wallet_create($data = array())
    {
        self::insert($data);
        return true;
    }

    public function wallet_user()
    {
        return $this->belongsTo('App\Http\Models\Shipper\User', 'id', 'user_id');

    }
}
