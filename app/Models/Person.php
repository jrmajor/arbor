<?php

namespace App\Models;

use App\Services\Sources\SourcesCast;
use App\Models\Relations\Children;
use App\Models\Relations\HalfSiblings;
use App\Models\Relations\Marriages;
use App\Models\Relations\Siblings;
use App\Services\Pytlewski\Pytlewski;
use App\Services\Wielcy\Wielcy;
use App\Traits\HasDateRanges;
use App\Traits\TapsActivity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Spatie\Activitylog\ActivityLogger;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property-read Collection|Person[] $siblings
 * @property-read Collection|Marriage[] $marriages
 * @property-read Collection|Activity[] $children
 * @property-read Collection|Person[] $activities
 */
class Person extends Model
{
    use HasDateRanges,
        HasFactory,
        SoftDeletes,
        LogsActivity,
        TapsActivity;

    const generationInterval = 32;

    protected static $logName = 'people';
    protected static $logOnlyDirty = true;
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['id', 'visibility', 'created_at', 'updated_at'];
    protected static $submitEmptyLogs = false;

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

    public function isVisible(): bool
    {
        return $this->visibility === true;
    }

    public function changeVisibility(bool $visibility): bool
    {
        if ($this->visibility === $visibility) {
            return false;
        }

        $this->visibility = $visibility;

        $saved = $this->save();

        if ($saved) {
            $description = $this->getDescriptionForEvent('changed-visibility');

            $logName = $this->getLogNameToUse('changed-visibility');

            $attrs = [
                'old' => ['visibility' => ! $visibility],
                'attributes' => ['visibility' => $visibility],
            ];

            $logger = app(ActivityLogger::class)
                ->useLog($logName)
                ->performedOn($this)
                ->withProperties($attrs);

            if (method_exists($this, 'tapActivity')) {
                $logger->tap([$this, 'tapActivity'], 'changed-visibility');
            }

            $logger->log($description);
        }

        return $saved;
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

    public function age($to, $raw = false)
    {
        if (! $this->birth_date) {
            return;
        }

        [$to_from, $to_to] = is_array($to) ? $to : [$to, $to];

        $either = $this->birth_date_to->diffInYears($to_from);
        $or = $this->birth_date_from->diffInYears($to_to);

        if ($raw) {
            return (int) $or;
        }

        return $either === $or ? $either : $either.'-'.$or;
    }

    public function currentAge($raw = false)
    {
        return $this->age(Carbon::now(), $raw);
    }

    public function ageAtDeath($raw = false)
    {
        if (! $this->death_date) {
            return;
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
            return "∗$this->birth_year, ✝$this->death_year";
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

        if (! $this->last_name) {
            $name .= $this->family_name;
        } else {
            $name .= "{$this->last_name} ({$this->family_name})";
        }

        return $name;
    }

    public static function findByPytlewskiId(?int $id): ?self
    {
        return $id ? self::where('id_pytlewski', $id)->first() : null;
    }

    public static function letters($type): Collection
    {
        $nameQuery = match ($type) {
            'family' => 'family_name',
            'last' => 'ifnull(last_name, family_name)',
            default => throw new InvalidArgumentException('Type must be "family" or "last".'),
        };

        return Cache::rememberForever(
            "letters_{$type}",
            fn () => DB::table('people')
                ->selectRaw(
                    "left({$nameQuery}, 1)
                    collate utf8mb4_0900_as_ci as letter,
                    count(*) as total",
                )->groupBy('letter')
                ->orderBy('letter')
                ->whereNull('deleted_at')
                ->get(),
        );
    }
}
