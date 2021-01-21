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

class Marriage extends Model
{
    use HasDateTuples,
        HasFactory,
        SoftDeletes,
        LogsActivity,
        TapsActivity;

    protected static $logName = 'marriages';
    protected static $logOnlyDirty = true;
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['id', 'created_at', 'updated_at'];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'divorced' => 'boolean',
        'rite' => MarriageRiteEnum::class.':nullable',
        'first_event_type' => MarriageEventTypeEnum::class.':nullable',
        'second_event_type' => MarriageEventTypeEnum::class.':nullable',
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
        return $user?->canRead() || $this->isVisible();
    }

    public function woman(): BelongsTo
    {
        return $this->belongsTo(Person::class)->withTrashed();
    }

    public function man(): BelongsTo
    {
        return $this->belongsTo(Person::class)->withTrashed();
    }

    public function partner(Person $person): ?Person
    {
        return match ($person->id) {
            $this->man_id => $this->woman,
            $this->woman_id => $this->man,
            default => null,
        };
    }

    public function order(Person $person): ?int
    {
        return match ($person->id) {
            $this->man_id => $this->man_order,
            $this->woman_id => $this->woman_order,
            default => null,
        };
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
            return $this->first_event_date_from->formatPeriodTo($this->first_event_date_to);
        }

        return null;
    }

    public function getSecondEventDateAttribute(): ?string
    {
        if ($this->second_event_date_from && $this->second_event_date_to) {
            return $this->second_event_date_from->formatPeriodTo($this->second_event_date_to);
        }

        return null;
    }

    public function getDivorceDateAttribute(): ?string
    {
        if ($this->divorce_date_from && $this->divorce_date_to) {
            return $this->divorce_date_from->formatPeriodTo($this->divorce_date_to);
        }

        return null;
    }
}
