<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NegativePayableAllowShipperZeroCodLogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'added_by',    
        'added_at',
        'removed_by'
    ];
}
