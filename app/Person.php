<?php

namespace App;

use App\Services\Pytlewski\Pytlewski;
use App\Wielcy;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Regex\Regex;

class Person extends Model
{
    use LogsActivity, SoftDeletes;

    const generationInterval = 32;

    protected static $logName = 'people';
    protected static $logOnlyDirty = true;
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['id', 'created_at'];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'dead' => 'boolean',
        'visibility' => 'boolean',
    ];

    protected ?Wielcy $wielcy = null;

    protected ?Pytlewski $pytlewski = null;

    public function isVisible(): bool
    {
        return $this->dead || $this->visibility > 0;
    }

    public function canBeViewedBy(?User $user): bool
    {
        return optional($user)->canRead() || $this->isVisible();
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

    public function getSiblingsAttribute(): Collection
    {
        if($this->mother_id && $this->father_id) {
            return Person::where('mother_id', $this->mother_id)
                            ->where('father_id', $this->father_id)
                            ->where('id', '!=', $this->id)
                            ->orderBy('birth_date', 'asc')
                            ->get();
        }
        return collect();
    }

    public function getSiblingsMotherAttribute(): Collection
    {
        if($this->mother_id) {
            return Person::where('mother_id', $this->mother_id)
                            ->where(function($q) {
                                $q->where('father_id', '!=', $this->father_id)
                                  ->orWhereNull('father_id');
                            })->where('id', '!=', $this->id)
                            ->orderBy('birth_date', 'asc')
                            ->get();
        }
        return collect();
    }

    public function getSiblingsFatherAttribute(): Collection
    {
        if($this->father_id) {
            return Person::where('father_id', $this->father_id)
                            ->where(function($q) {
                                $q->where('mother_id', '!=', $this->mother_id)
                                  ->orWhereNull('mother_id');
                            })->where('id', '!=', $this->id)
                            ->orderBy('birth_date', 'asc')
                            ->get();
        }
        return collect();
    }

    public function getMarriagesAttribute(): Collection
    {
        return Marriage::where('man_id', $this->id)
                            ->orWhere('woman_id', $this->id)
                            ->orderBy($this->sex == 'xx' ? 'woman_order' : 'man_order')
                            ->with('woman')
                            ->get();
    }

    public function getChildrenAttribute(): Collection
    {
        return Person::where('father_id', $this->id)
                        ->orWhere('mother_id', $this->id)
                        ->orderBy('birth_date', 'asc')
                        ->get();
    }

    public function getBirthYearAttribute(): ?int
    {
        return (int) substr($this->birth_date, 0, 4) ?: null;
    }

    public function getBirthMonthAttribute(): ?int
    {
        return (int) substr($this->birth_date, 5, 2) ?: null;
    }

    public function getBirthDayAttribute(): ?int
    {
        return (int) substr($this->birth_date, 8, 2) ?: null;
    }

    public function getDeathYearAttribute(): ?int
    {
        return (int) substr($this->death_date, 0, 4) ?: null;
    }

    public function getDeathMonthAttribute(): ?int
    {
        return (int) substr($this->death_date, 5, 2) ?: null;
    }

    public function getDeathDayAttribute(): ?int
    {
        return (int) substr($this->death_date, 8, 2) ?: null;
    }

    public function age(array $to, $raw = false)
    {
        if (! $this->birth_date) {
            return null;
        }

        if ($this->birth_month === null || $to['m'] === null) {
            return (! $raw ? '~' : '') . ($to['y'] - $this->birth_year);
        }

        if ($to['m'] > $this->birth_month) {
            return $to['y'] - $this->birth_year;
        } elseif ($to['m'] < $this->birth_month) {
            return $to['y'] - $this->birth_year - 1;
        }

        if ($this->birth_day === null || $to['d'] === null) {
            return (! $raw ? '~' : '') . ($to['y'] - $this->birth_year);
        }

        if ($to['d'] < $this->birth_day) {
            return $to['y'] - $this->birth_year - 1;
        } else {
            return $to['y'] - $this->birth_year;
        }
    }

    public function currentAge($raw = false)
    {
        $now = [
            'y' => Carbon::now()->format('Y'),
            'm' => Carbon::now()->format('m'),
            'd' => Carbon::now()->format('d'),
        ];

        return $this->age($now, $raw);
    }

    public function ageAtDeath($raw = false)
    {
        if (! $this->death_date) {
            return null;
        }

        $death = [
            'y' => $this->death_year,
            'm' => $this->death_month,
            'd' => $this->death_day,
        ];

        return $this->age($death, $raw);
    }

    public function estimatedBirthDate()
    {
        $interval = self::generationInterval;
        $prediction = collect();

        $mother_age = optional($this->mother)->birth_year;
        $father_age = optional($this->father)->birth_year;
        if ($mother_age && $father_age) {
            $prediction->put('parents', (($mother_age + $father_age) / 2) + $interval);
        } elseif ($mother_age || $father_age) {
            $prediction->put('parents', $mother_age + $father_age + $interval);
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
        $name = $this->name . ' ';

        if(! $this->last_name) {
            $name .= $this->family_name;
        } else {
            $name .= "{$this->last_name} (z d. {$this->family_name})";
        }

        if ($this->birth_year && $this->death_year){
            $name .= " (∗$this->birth_year, ✝$this->death_year)";
        } elseif ($this->birth_year) {
            $name .= " (∗{$this->birth_year})";
        } elseif($this->death_year) {
            $name .= " (✝{$this->death_year})";
        }

        return $name .= " [№{$this->id}]";
    }

    public static function findByPytlewskiId($id): ?Person
    {
        return Person::where('id_pytlewski', $id)->first();
    }

    public static function letters($type): Collection
    {
        if (! in_array($type, ['family', 'last'])) {
            throw new InvalidArgumentException('Type must be "family" or "last".');
        }

        return Cache::rememberForever(
            "letters_$type",
            fn() => DB::table('people')
                    ->selectRaw(
                        'left(' . ($type == 'family' ? 'family_name' : 'ifnull(last_name, family_name)') . ', 1)
                        collate utf8mb4_0900_as_ci as letter,
                        count(*) as total'
                    )->groupBy('letter')
                    ->orderBy('letter')
                    ->whereNull('deleted_at')
                    ->get()
        );
    }
}
