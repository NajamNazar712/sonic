<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Models\Admin\Admin;


class HistoryTPaymentCycle extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'value', 'added_by' ,'added_at' , 'approved_by' , 'approved_at'];

    protected $casts = [
        'added_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function addedByUser()
    {
        return $this->belongsTo(Admin::class, 'added_by');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }
}
