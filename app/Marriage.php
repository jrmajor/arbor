<?php

namespace App;

use App\Enums\MarriageEventTypeEnum;
use App\Enums\MarriageRiteEnum;
use App\Traits\HasDateTuples;
use App\Traits\TapsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Enum\Laravel\HasEnums;

class Marriage extends Model
{
    use HasDateTuples,
        HasEnums,
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
        'ended' => 'boolean',
    ];

    protected static $dateTuples = [
        'first_event_date',
        'second_event_date',
        'end_date',
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

    public function getEndDateAttribute(): ?string
    {
        if ($this->end_date_from && $this->end_date_to) {
            return format_date_from_period($this->end_date_from, $this->end_date_to);
        }

        return null;
    }
}
