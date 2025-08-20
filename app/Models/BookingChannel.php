<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingChannel extends Model
{
    use HasFactory;

    public function channel()
    {
        return $this->belongsTo('App\Models\Channel');
    }
}
