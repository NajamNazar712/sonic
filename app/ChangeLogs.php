<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChangeLogs extends Model
{
    //
    protected $fillable = [
        'table_name', 'record_id', 'old_data', 'new_data', 'updated_by'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];
}
