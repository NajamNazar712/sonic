<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLogs extends Model
{
    //
    protected $fillable = [
        'admin_id','user_id'
    ];
}
