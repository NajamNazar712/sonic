<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RunnerDetail extends Model
{
    public function runner() {
        return $this->belongsTo('App\Http\Models\Runner', 'runner_id', 'id');
    }
}
