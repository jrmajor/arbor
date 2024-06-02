<?php

namespace App\Models;

use App\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use CausesActivity;
    use HasFactory;
    use LogsActivity;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'username', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function canRead(): bool
    {
        return $this->permissions >= 1;
    }

    public function canWrite(): bool
    {
        return $this->permissions >= 2;
    }

    public function canViewHistory(): bool
    {
        return $this->permissions >= 3;
    }

    public function isSuperAdmin(): bool
    {
        return $this->permissions === 4;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * @return MorphOne<Activity>
     */
    public function latestLogin(): MorphOne
    {
        /** @phpstan-ignore method.notFound */
        return $this
            ->actions()
            ->one()
            ->latestOfMany()
            ->whereLogName('logins')->whereDescription('logged-in');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('users')
            ->logAll()
            ->logExcept(['id', 'created_at', 'updated_at', 'remember_token'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
