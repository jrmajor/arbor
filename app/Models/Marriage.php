<?php

namespace App\Models;

use App\Enums\MarriageEventTypeEnum;
use App\Enums\MarriageRiteEnum;
use App\Traits\HasDateTuples;
use App\Traits\TapsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Enum\Laravel\HasEnums;

class Marriage extends Model
{
    use HasDateTuples,
        HasEnums,
        HasFactory,
        SoftDeletes,
        LogsActivity,
        TapsActivity;

    protected $enums = [
        'rite' => MarriageRiteEnum::class.':nullable',
        'first_event_type' => MarriageEventTypeEnum::class.':nullable',
        'second_event_type' => MarriageEventTypeEnum::class.':nullable',
    ];

    protected static $logName = 'marriages';
    protected static $logOnlyDirty = true;
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['id', 'created_at', 'updated_at'];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'divorced' => 'boolean',
    ];

    protected static $dateTuples = [
        'first_event_date',
        'second_event_date',
        'divorce_date',
    ];

    public function isVisible(): bool
    {
        return $this->woman->isVisible() && $this->man->isVisible();
    }

    public function canBeViewedBy(?User $user): bool
    {
        return optional($user)->canRead() || $this->isVisible();
    }

    public function woman(): BelongsTo
    {
        return $this->belongsTo('App\\Models\\Person')->withTrashed();
    }

    public function man(): BelongsTo
    {
        return $this->belongsTo('App\\Models\\Person')->withTrashed();
    }

    public function partner(Person $person): ?Person
    {
        if ($this->man_id == $person->id) {
            return $this->woman;
        }
        if ($this->woman_id == $person->id) {
            return $this->man;
        }

        return null;
    }

    public function order(Person $person): ?int
    {
        if ($this->man_id == $person->id) {
            return $this->man_order;
        }
        if ($this->woman_id == $person->id) {
            return $this->woman_order;
        }

        return null;
    }

    public function hasFirstEvent(): bool
    {
        return $this->first_event_type || $this->first_event_date || $this->first_event_place;
    }

    public function hasSecondEvent(): bool
    {
        return $this->second_event_type || $this->second_event_date || $this->second_event_place;
    }

    public function getFirstEventDateAttribute(): ?string
    {
        if ($this->first_event_date_from && $this->first_event_date_to) {
            return format_date_from_period($this->first_event_date_from, $this->first_event_date_to);
        }

        return null;
    }

    public function getSecondEventDateAttribute(): ?string
    {
        if ($this->second_event_date_from && $this->second_event_date_to) {
            return format_date_from_period($this->second_event_date_from, $this->second_event_date_to);
        }

        return null;
    }

    public function getDivorceDateAttribute(): ?string
    {
        if ($this->divorce_date_from && $this->divorce_date_to) {
            return format_date_from_period($this->divorce_date_from, $this->divorce_date_to);
        }

        return null;
    }
}
