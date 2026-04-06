<?php

namespace App\Models;

use App\Notifications\ResetPassword as ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Models\Concerns\HasActivity;
use Spatie\Activitylog\Support\LogOptions;

class User extends Authenticatable
{
    use HasActivity;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

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
     * @return MorphOne<Activity, self>
     */
    public function latestLogin(): MorphOne
    {
        /** @phpstan-ignore return.type */
        return $this
            ->activitiesAsCauser()
            ->one()
            ->latestOfMany()
            ->whereLogName('logins')
            ->whereDescription('logged-in');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('users')
            ->logAll()
            ->logExcept(['id', 'created_at', 'updated_at', 'remember_token'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
