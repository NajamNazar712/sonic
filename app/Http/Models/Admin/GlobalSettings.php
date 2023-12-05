<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class GlobalSettings extends Model
{
    protected $fillable = [
        'setting_value','type','class','text'
    ];
}
