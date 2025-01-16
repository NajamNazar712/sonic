<?php

namespace App\Http\Models\Webhook;

use Illuminate\Database\Eloquent\Model;

class ApiCallLog extends Model
{
    // Format dates as 'YYYY-MM-DD HH:mm:ss'
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
