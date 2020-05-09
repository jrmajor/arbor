<?php

namespace App;

use App\Services\Pytlewski\Pytlewski;
use App\Traits\HasDateTuples;
use App\Traits\TapsActivity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Spatie\Activitylog\ActivityLogger;
use Spatie\Activitylog\Traits\LogsActivity;

class Person extends Model
{
    use HasDateTuples,
        SoftDeletes,
        LogsActivity,
        TapsActivity;

    const generationInterval = 32;

    protected static $logName = 'people';
    protected static $logOnlyDirty = true;
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['id', 'visibility'];
    protected static $submitEmptyLogs = false;

    protected $guarded = ['id', 'visibility', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'dead' => 'boolean',
        'visibility' => 'boolean',
    ];

    protected $dateTuples = [
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

    public function canBeViewedBy(?User $user): bool
    {
        return optional($user)->canRead() || $this->isVisible();
    }

    public function changeVisibility($visibility)
    {
        if ($this->visibility === $visibility) {
            return false;
        }

        $this->visibility = $visibility;

        $saved = $this->save();

        if ($saved) {
            $description = $this->getDescriptionForEvent('changed-visibility');

            $logName = $this->getLogNameToUse('changed-visibility');

            if ($description == '') {
                return;
            }

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
        return $this->belongsTo('App\Person');
    }

    public function father(): BelongsTo
    {
        return $this->belongsTo('App\Person');
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

    public function siblings(): HasMany
    {
        return $this->hasMany('App\Person', 'id', 'id')
                    ->where('id', '!=', $this->id)
                    ->orWhere(fn ($q) =>
                        $this->mother_id && $this->father_id
                        ? $q->where('mother_id', $this->mother_id)
                            ->where('father_id', $this->father_id)
                            ->where('id', '!=', $this->id)
                        : $q->whereRaw('false')
                    )->orderBy('birth_date_from', 'asc');
    }

    public function siblings_mother(): HasMany
    {
        return $this->hasMany('App\Person', 'id', 'id')
                    ->where('id', '!=', $this->id)
                    ->orWhere(fn ($q) =>
                        $this->mother_id
                        ? $q->where('mother_id', $this->mother_id)
                            ->where(fn ($q) =>
                                $q->where('father_id', '!=', $this->father_id)
                                    ->orWhereNull('father_id')
                            )->where('id', '!=', $this->id)
                        : $q->whereRaw('false')
                    )->orderBy('birth_date_from', 'asc');
    }

    public function siblings_father(): HasMany
    {
        return $this->hasMany('App\Person', 'id', 'id')
                    ->where('id', '!=', $this->id)
                    ->orWhere(fn ($q) =>
                        $this->father_id
                        ? $q->where('father_id', $this->father_id)
                            ->where(fn ($q) =>
                                $q->where('mother_id', '!=', $this->mother_id)
                                    ->orWhereNull('mother_id')
                            )->where('id', '!=', $this->id)
                        : $q->whereRaw('false')
                    )->orderBy('birth_date_from', 'asc');
    }

    public function marriages(): HasMany
    {
        return $this->hasMany('App\Marriage', 'id', 'id')
                    ->where('id', '!=', $this->id)
                    ->orWhere(fn ($q) =>
                        $q->where('woman_id', $this->id)
                            ->orWhere('man_id', $this->id)
                    )->orderBy($this->sex == 'xx' ? 'woman_order' : 'man_order');
    }

    public function children(): HasMany
    {
        return $this->hasMany('App\Person', 'id', 'id')
                    ->where('id', '!=', $this->id)
                    ->orWhere(fn ($q) =>
                        $q->where('mother_id', $this->id)
                            ->orwhere('father_id', $this->id)
                    )->orderBy('birth_date_from', 'asc');
    }

    public function getBirthYearAttribute(): ?int
    {
        if (optional($this->birth_date_from)->year == optional($this->birth_date_to)->year) {
            return optional($this->birth_date_from)->year;
        }

        return null;
    }

    public function getDeathYearAttribute(): ?int
    {
        if (optional($this->death_date_from)->year == optional($this->death_date_to)->year) {
            return optional($this->death_date_from)->year;
        }

        return null;
    }

    public function getBirthDateAttribute(): ?string
    {
        if ($this->birth_date_from && $this->birth_date_to) {
            return format_date_from_period($this->birth_date_from, $this->birth_date_to);
        }

        return null;
    }

    public function getDeathDateAttribute(): ?string
    {
        if ($this->death_date_from && $this->death_date_to) {
            return format_date_from_period($this->death_date_from, $this->death_date_to);
        }

        return null;
    }

    public function getFuneralDateAttribute(): ?string
    {
        if ($this->funeral_date_from && $this->funeral_date_to) {
            return format_date_from_period($this->funeral_date_from, $this->funeral_date_to);
        }

        return null;
    }

    public function getBurialDateAttribute(): ?string
    {
        if ($this->burial_date_from && $this->burial_date_to) {
            return format_date_from_period($this->burial_date_from, $this->burial_date_to);
        }

        return null;
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
        } else {
            return $either == $or ? $either : $either.'-'.$or;
        }
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

    public function estimatedBirthDate()
    {
        $interval = self::generationInterval;
        $prediction = collect();

        $mother_year = optional($this->mother)->birth_year;
        $father_year = optional($this->father)->birth_year;

        if ($mother_year && $father_year) {
            $prediction->put('parents', (($mother_year + $father_year) / 2) + $interval);
        } elseif ($mother_year || $father_year) {
            $prediction->put('parents', $mother_year + $father_year + $interval);
        }

        $prediction->put('children',
            $this->children->avg->birth_year ? $this->children->avg->birth_year - $interval : null
        );

        $prediction->put('partners',
            $this->marriages
                ->map->partner($this)
                ->avg->birth_year
        );

        $prediction->put('siblings',
            $this->siblings
                ->merge($this->siblings_mother)
                ->merge($this->siblings_father)
                ->avg->birth_year
        );

        return $prediction->avg() ? round($prediction->avg()) : null;
    }

    public function estimatedBirthDateError(): ?int
    {
        if (! $this->estimatedBirthDate() || ! $this->birth_year) {
            return null;
        }
        return abs($this->estimatedBirthDate() - $this->birth_year);
    }

    public function formatName(): string
    {
        $name = $this->name.' ';

        if (! $this->last_name) {
            $name .= $this->family_name;
        } else {
            $name .= "{$this->last_name} (z d. {$this->family_name})";
        }

        if ($this->birth_year && $this->death_year) {
            $name .= " (∗$this->birth_year, ✝$this->death_year)";
        } elseif ($this->birth_year) {
            $name .= " (∗{$this->birth_year})";
        } elseif ($this->death_year) {
            $name .= " (✝{$this->death_year})";
        }

        return $name .= " [№{$this->id}]";
    }

    public static function findByPytlewskiId($id): ?self
    {
        return self::where('id_pytlewski', $id)->first();
    }

    public static function letters($type): Collection
    {
        if (! in_array($type, ['family', 'last'])) {
            throw new InvalidArgumentException('Type must be "family" or "last".');
        }

        return Cache::rememberForever(
            "letters_$type",
            fn () => DB::table('people')
                    ->selectRaw(
                        'left('.($type == 'family' ? 'family_name' : 'ifnull(last_name, family_name)').', 1)
                        collate utf8mb4_0900_as_ci as letter,
                        count(*) as total'
                    )->groupBy('letter')
                    ->orderBy('letter')
                    ->whereNull('deleted_at')
                    ->get()
        );
    }
}
