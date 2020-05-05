<?php

namespace App;

use App\Enums\MarriageEventTypeEnum;
use App\Enums\MarriageRiteEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Enum\Laravel\HasEnums;

class Marriage extends Model
{

    use HasEnums, LogsActivity, SoftDeletes;

    protected $enums = [
        'rite' => MarriageRiteEnum::class.':nullable',
        'first_event_type' => MarriageEventTypeEnum::class.':nullable',
        'second_event_type' => MarriageEventTypeEnum::class.':nullable',
    ];

    protected static $logName = 'marriages';
    protected static $logOnlyDirty = true;
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['id', 'created_at'];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $dates = [
        'first_event_date_from',
        'first_event_date_to',
        'second_event_date_from',
        'second_event_date_to',
        'end_date_from',
        'end_date_to',
    ];

    public function woman(): BelongsTo
    {
        return $this->belongsTo('App\Person');
    }

    public function man(): BelongsTo
    {
        return $this->belongsTo('App\Person');
    }

    public function partner($person): ?Person
    {
        if ($this->man_id == $person->id) {
            return $this->woman;
        }
        if ($this->woman_id == $person->id) {
            return $this->man;
        }

        return null;
    }

    public function order($person): ?int
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
        if (
            Arr::exists($this->attributes, 'first_event_date')
            && $this->attributes['first_event_date']
        ) {
            return format_date($this->attributes['first_event_date']);
        }

        if ($this->first_event_date_from && $this->first_event_date_to) {
            return format_date_from_period($this->first_event_date_from, $this->first_event_date_to);
        }

        return null;
    }

    public function getSecondEventDateAttribute(): ?string
    {
        if (
            Arr::exists($this->attributes, 'second_event_date')
            && $this->attributes['second_event_date']
        ) {
            return format_date($this->attributes['second_event_date']);
        }

        if ($this->second_event_date_from && $this->second_event_date_to) {
            return format_date_from_period($this->second_event_date_from, $this->second_event_date_to);
        }

        return null;
    }

    public function getEndDateAttribute(): ?string
    {
        if (
            Arr::exists($this->attributes, 'end_date')
            && $this->attributes['end_date']
        ) {
            return format_date($this->attributes['end_date']);
        }

        if ($this->end_date_from && $this->end_date_to) {
            return format_date_from_period($this->end_date_from, $this->end_date_to);
        }

        return null;
    }
}
