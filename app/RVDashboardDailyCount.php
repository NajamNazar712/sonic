<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RVDashboardDailyCount extends Model
{
    protected $table = 'rv_dashboard_daily_counts';

    protected $fillable = [
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];
}
