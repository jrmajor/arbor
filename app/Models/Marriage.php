<?php

namespace App\Models;

use App\Enums\MarriageEventType;
use App\Enums\MarriageRite;
use App\Models\Traits\HasDateRanges;
use App\Models\Traits\TapsActivity;
use Carbon\Carbon;
use Database\Factories\MarriageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use InvalidArgumentException;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property-read ?Carbon $first_event_date_from
 * @property-read ?Carbon $first_event_date_to
 * @property-read ?Carbon $second_event_date_from
 * @property-read ?Carbon $second_event_date_to
 * @property-read ?Carbon $divorce_date_from
 * @property-read ?Carbon $divorce_date_to
 * @property-read string $first_event_date
 * @property-read string $second_event_date
 * @property-read string $divorce_date
 * @property-read ?int $first_event_year
 * @property-read ?int $second_event_year
 * @property-read ?int $divorce_year
 */
class Marriage extends Model
{
    use HasDateRanges;

    /** @use HasFactory<MarriageFactory> */
    use HasFactory;

    use LogsActivity;
    use SoftDeletes;
    use TapsActivity;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'divorced' => 'boolean',
        'rite' => MarriageRite::class,
        'first_event_type' => MarriageEventType::class,
        'second_event_type' => MarriageEventType::class,
    ];

    /** @var list<string> */
    protected static array $dateRanges = [
        'first_event_date',
        'second_event_date',
        'divorce_date',
    ];

    public function isVisible(): bool
    {
        return $this->woman->isVisible() && $this->man->isVisible();
    }

    /**
     * @return BelongsTo<Person, $this>
     */
    public function woman(): BelongsTo
    {
        return $this->belongsTo(Person::class)->withTrashed();
    }

    /**
     * @return BelongsTo<Person, $this>
     */
    public function man(): BelongsTo
    {
        return $this->belongsTo(Person::class)->withTrashed();
    }

    public function partner(Person $person): Person
    {
        return match ($person->id) {
            $this->man_id => $this->woman,
            $this->woman_id => $this->man,
            default => throw new InvalidArgumentException(),
        };
    }

    public function order(Person $person): ?int
    {
        return match ($person->id) {
            $this->man_id => $this->man_order,
            $this->woman_id => $this->woman_order,
            default => throw new InvalidArgumentException(),
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('marriages')
            ->logAll()
            ->logExcept(['id', 'created_at', 'updated_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
