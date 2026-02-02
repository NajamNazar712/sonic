<?php

namespace App\Models;

use App\Http\Models\EmployeeNotificationHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PusherNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'push_notification_id',
        'device_token',
        'title',
        'body',
        'payload',
        'status',
    ];

    public function employeeHistories()
    {
        return $this->hasMany(EmployeeNotificationHistory::class, 'pusher_notification_id');
    }
}
