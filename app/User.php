<?php

namespace App;

use App\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use CausesActivity,
        LogsActivity,
        Notifiable,
        SoftDeletes;

    protected static $logName = 'users';
    protected static $logOnlyDirty = true;
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['id', 'created_at', 'remember_token'];
    protected static $submitEmptyLogs = false;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function canRead(): bool
    {
        return $this->permissions >= 1;
    }

    public function canWrite(): bool
    {
        return $this->permissions >= 2;
    }

    public function canDestroy(): bool
    {
        return $this->permissions >= 3;
    }

    public function isSuperAdmin(): bool
    {
        return $this->permissions == 4;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
