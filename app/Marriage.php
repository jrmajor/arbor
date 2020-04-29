<?php

namespace App;

use App\Enums\MarriageEventTypeEnum;
use App\Enums\MarriageRiteEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
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
}
