<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'old_data',
        'new_data',
        'admin_id',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo('App\Http\Models\Admin\Admin');
    }
}
