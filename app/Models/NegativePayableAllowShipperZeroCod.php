<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NegativePayableAllowShipperZeroCod extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'added_by'];

    static function isAllowed($user_id)
    {
        $record = self::where('user_id', $user_id)->first();

        if (!$record) {
            return false; 
        }

        return $record->created_at->gt(now()->subHours(48));
    }

}
