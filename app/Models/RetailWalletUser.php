<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailWalletUser extends Model
{
    use HasFactory;

    static function wallet_create($data = array())
    {
        self::insert($data);
        return true;
    }
}
