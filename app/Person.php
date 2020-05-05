<?php

namespace App;

use App\Services\Pytlewski\Pytlewski;
use App\Wielcy;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
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

    protected $dates = [
        'birth_date_from',
        'birth_date_to',
        'death_date_from',
        'death_date_to',
        'funeral_date_from',
        'funeral_date_to',
        'burial_date_from',
        'burial_date_to',
    ];

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
                            ->orderBy('birth_date_from', 'asc')
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
                            ->orderBy('birth_date_from', 'asc')
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
                            ->orderBy('birth_date_from', 'asc')
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
                        ->orderBy('birth_date_from', 'asc')
                        ->get();
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
            return null;
        }

        [$to_from, $to_to] = is_array($to) ? $to : [$to, $to];

        $either = $this->birth_date_to->diffInYears($to_from);
        $or = $this->birth_date_from->diffInYears($to_to);

        if($either == $or) {
            return $either;
        } else {
            return $raw ? $either : $either . '-' . $or;
        }
    }

    public function currentAge($raw = false)
    {
        return $this->age(Carbon::now(), $raw);
    }

    public function ageAtDeath($raw = false)
    {
        if (! $this->death_date) {
            return null;
        }

        return $this->age([$this->death_date_from, $this->death_date_to], $raw);
    }

    // public function estimatedBirthDate()
    // {
    //     $interval = self::generationInterval;
    //     $prediction = collect();

    //     $mother_age = optional($this->mother)->birth_year;
    //     $father_age = optional($this->father)->birth_year;
    //     if ($mother_age && $father_age) {
    //         $prediction->put('parents', (($mother_age + $father_age) / 2) + $interval);
    //     } elseif ($mother_age || $father_age) {
    //         $prediction->put('parents', $mother_age + $father_age + $interval);
    //     }

    //     $prediction->put('children',
    //         $this->children->avg->birth_year ? $this->children->avg->birth_year - $interval : null
    //     );

    //     $prediction->put('partners',
    //         $this->marriages
    //             ->map->partner($this)
    //             ->avg->birth_year
    //     );

    //     $prediction->put('siblings',
    //         $this->siblings
    //             ->merge($this->siblings_mother)
    //             ->merge($this->siblings_father)
    //             ->avg->birth_year
    //     );

    //     return $prediction->avg() ? round($prediction->avg()) : null;
    // }

    // public function estimatedBirthDateError(): ?int
    // {
    //     if (! $this->estimatedBirthDate() || ! $this->birth_year) {
    //         return null;
    //     }
    //     return abs($this->estimatedBirthDate() - $this->birth_year);
    // }

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
