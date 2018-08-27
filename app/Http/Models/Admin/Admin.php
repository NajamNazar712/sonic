<?php

namespace App\Http\Models\Admin;

use App\Notifications\AdminResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable;
    protected $guard = 'admin';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','username', 'email', 'phone_number', 'cnic', 'role_id', 'password', 'updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function role() {
        return $this->belongsTo('App\Http\Models\Admin\Role', 'role_id', 'id');
    }

    public function hubs() {
        return $this->hasMany('App\Http\Models\Admin\AdminHub', 'admin_id', 'id');
    }
}
