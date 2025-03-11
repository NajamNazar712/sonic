<?php

namespace App\Http\Models\Admin\Vigilance;

use Illuminate\Database\Eloquent\Model;

class VigilanceVerification extends Model
{
    // Format dates as 'YYYY-MM-DD HH:mm:ss'
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
