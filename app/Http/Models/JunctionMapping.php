<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class JunctionMapping extends Model
{
    protected $fillable = [
        'origin_id','destination_id','junction_1','junction_2','receiver','updated_by'
    ];
}
