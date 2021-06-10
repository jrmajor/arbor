<?php

namespace App\Models;

use App\Models\Relations\Children;
use App\Models\Relations\HalfSiblings;
use App\Models\Relations\Marriages;
use App\Models\Relations\Siblings;
use App\Models\Traits\HasDateRanges;
use App\Models\Traits\TapsActivity;
use App\Services\Pytlewski\Pytlewski;
use App\Services\Sources\SourcesCast;
use App\Services\Wielcy\Wielcy;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property-read string $birth_date
 * @property-read string $death_date
 * @property-read string $funeral_date
 * @property-read string $burial_date
 * @property-read ?int $birth_year
 * @property-read ?int $death_year
 * @property-read ?int $funeral_year
 * @property-read ?int $burial_year
 * @property-read EloquentCollection<Person> $siblings
 * @property-read EloquentCollection<Marriage> $marriages
 * @property-read EloquentCollection<Activity> $children
 * @property-read EloquentCollection<Person> $activities
 */
class Person extends Model
{
    use HasDateRanges;
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;
    use TapsActivity;

    public const generationInterval = 32;

    protected $guarded = ['id', 'visibility', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'dead' => 'boolean',
        'sources' => SourcesCast::class,
        'visibility' => 'boolean',
    ];

    protected static $dateRanges = [
        'birth_date',
        'death_date',
        'funeral_date',
        'burial_date',
    ];

    protected ?Wielcy $wielcy = null;

    protected ?Pytlewski $pytlewski = null;

    protected static function booting()
    {
        static::updating(function (self $person) {
            if ($person->isDirty('visibility') && count($person->getDirty()) > 1) {
                throw new Exception('Visibility can not be updated with other attributes.');
            }
        });
    }

    public function isVisible(): bool
    {
        return $this->visibility === true;
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function father(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function getWielcyAttribute(): ?Wielcy
    {
        if (! $this->id_wielcy) {
            return null;
        }

        if (! $this->wielcy) {
            $this->wielcy = new Wielcy($this->id_wielcy);
        }

        return $this->wielcy;
    }

    public function getPytlewskiAttribute(): ?Pytlewski
    {
        if (! $this->id_pytlewski) {
            return null;
        }

        if (! $this->pytlewski) {
            $this->pytlewski = new Pytlewski($this->id_pytlewski);
        }

        return $this->pytlewski;
    }

    public function siblings(): Siblings
    {
        return (new Siblings($this))
            ->orderBy('birth_date_from');
    }

    public function siblings_mother(): HalfSiblings
    {
        return (new HalfSiblings($this, 'mother'))
            ->orderBy('birth_date_from');
    }

    public function siblings_father(): HalfSiblings
    {
        return (new HalfSiblings($this, 'father'))
            ->orderBy('birth_date_from');
    }

    public function marriages(): Marriages
    {
        return (new Marriages($this))
            ->orderBy($this->sex === 'xx' ? 'woman_order' : 'man_order');
    }

    public function children(): Children
    {
        return (new Children($this))
            ->orderBy('birth_date_from');
    }

    /**
     * @param Carbon[]|Carbon $to
     */
    public function age(array|Carbon $to, bool $raw = false): int|string|null
    {
        if (! $this->birth_date) {
            return null;
        }

        [$to_from, $to_to] = is_array($to) ? $to : [$to, $to];

        $either = $this->birth_date_to->diffInYears($to_from);
        $or = $this->birth_date_from->diffInYears($to_to);

        if ($raw) {
            return (int) $or;
        }

        return $either === $or ? $either : $either.'-'.$or;
    }

    public function currentAge(bool $raw = false): int|string|null
    {
        return $this->age(now(), $raw);
    }

    public function ageAtDeath(bool $raw = false): int|string|null
    {
        if (! $this->death_date) {
            return null;
        }

        return $this->age([$this->death_date_from, $this->death_date_to], $raw);
    }

    public function estimatedBirthDate(): ?int
    {
        $interval = self::generationInterval;
        $prediction = collect();

        $mother_year = $this->mother?->birth_year;
        $father_year = $this->father?->birth_year;

        if ($mother_year && $father_year) {
            $prediction->put('parents', (($mother_year + $father_year) / 2) + $interval);
        } elseif ($mother_year || $father_year) {
            $prediction->put('parents', $mother_year + $father_year + $interval);
        }

        $prediction->put('children',
            $this->children->avg->birth_year ? $this->children->avg->birth_year - $interval : null,
        );

        $prediction->put('partners',
            $this->marriages
                ->map->partner($this)
                ->avg->birth_year,
        );

        $prediction->put('siblings',
            $this->siblings
                ->merge($this->siblings_mother)
                ->merge($this->siblings_father)
                ->avg->birth_year,
        );

        return $prediction->avg() ? (int) round($prediction->avg()) : null;
    }

    public function estimatedBirthDateError(): ?int
    {
        if (! $this->estimatedBirthDate() || ! $this->birth_year) {
            return null;
        }

        return abs($this->estimatedBirthDate() - $this->birth_year);
    }

    public function formatSimpleDates(): ?string
    {
        if ($this->birth_year && $this->death_year) {
            return "∗{$this->birth_year}, ✝{$this->death_year}";
        }

        if ($this->birth_year) {
            return "∗{$this->birth_year}";
        }

        if ($this->death_year) {
            return "✝{$this->death_year}";
        }

        return null;
    }

    public function formatSimpleName(): string
    {
        $name = $this->name.' ';

        $name .= $this->last_name
            ? "{$this->last_name} ({$this->family_name})"
            : $this->family_name;

        return $name;
    }

    /**
     * @param 'family'|'last' $type
     */
    public static function letters(string $type): EloquentCollection
    {
        $nameQuery = match ($type) {
            'family' => 'family_name',
            'last' => 'ifnull(last_name, family_name)',
        };

        return Cache::rememberForever(
            "letters_{$type}",
            fn () => DB::table('people')
                ->selectRaw(
                    "left({$nameQuery}, 1)
                    collate utf8mb4_0900_as_ci as letter,
                    count(*) as total",
                )
                ->groupBy('letter')
                ->orderBy('letter')
                ->whereNull('deleted_at')
                ->get(),
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('people')
            ->logAll()
            ->logExcept(['id', 'created_at', 'updated_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
