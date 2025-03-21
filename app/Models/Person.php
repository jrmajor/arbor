<?php

namespace App\Models;

use App\Enums\Sex;
use App\Models\Relations\Children;
use App\Models\Relations\HalfSiblings;
use App\Models\Relations\Marriages;
use App\Models\Relations\Siblings;
use App\Models\Traits\HasDateRanges;
use App\Models\Traits\TapsActivity;
use App\Services\Age;
use App\Services\Pytlewski\Pytlewski;
use App\Services\Pytlewski\PytlewskiFactory;
use App\Services\Sources\SourcesCast;
use App\Services\Wielcy\Wielcy;
use Carbon\Carbon;
use Database\Factories\PersonFactory;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Psl\Str;
use Psl\Vec;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property ?Carbon $birth_date_from
 * @property ?Carbon $birth_date_to
 * @property ?Carbon $death_date_from
 * @property ?Carbon $death_date_to
 * @property ?Carbon $funeral_date_from
 * @property ?Carbon $funeral_date_to
 * @property ?Carbon $burial_date_from
 * @property ?Carbon $burial_date_to
 * @property-read ?string $birth_date
 * @property-read ?int $birth_year
 * @property-read ?string $death_date
 * @property-read ?int $death_year
 * @property-read ?string $funeral_date
 * @property-read ?int $funeral_year
 * @property-read ?string $burial_date
 * @property-read ?int $burial_year
 * @property-read ?Pytlewski $pytlewski
 * @property-read EloquentCollection<int, Person> $siblings
 * @property-read EloquentCollection<int, Person> $siblings_father
 * @property-read EloquentCollection<int, Person> $siblings_mother
 * @property-read EloquentCollection<int, Marriage> $marriages
 * @property-read EloquentCollection<int, Person> $children
 */
class Person extends Model
{
    use HasDateRanges;

    /** @use HasFactory<PersonFactory> */
    use HasFactory;

    use LogsActivity;
    use SoftDeletes;
    use TapsActivity;

    protected $guarded = ['id', 'visibility', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'sex' => Sex::class,
        'dead' => 'boolean',
        'sources' => SourcesCast::class,
        'visibility' => 'boolean',
    ];

    /** @var list<string> */
    protected static array $dateRanges = [
        'birth_date',
        'death_date',
        'funeral_date',
        'burial_date',
    ];

    protected ?Wielcy $wielcy = null;

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

    public function getAgeAttribute(): Age
    {
        return new Age($this);
    }

    /**
     * @return BelongsTo<self, $this>
     */
    public function mother(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    /**
     * @return BelongsTo<self, $this>
     */
    public function father(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    /**
     * @return Attribute<?Wielcy, never>
     */
    public function wielcy(): Attribute
    {
        /** @phpstan-ignore return.type */
        return Attribute::get(function (): ?Wielcy {
            if (! $this->id_wielcy) {
                return null;
            }

            if (! $this->wielcy) {
                $this->wielcy = new Wielcy($this->id_wielcy);
            }

            return $this->wielcy;
        });
    }

    /**
     * @return Attribute<?Pytlewski, never>
     */
    public function pytlewski(): Attribute
    {
        /** @phpstan-ignore return.type */
        return Attribute::get(fn () => app(PytlewskiFactory::class)->for($this));
    }

    public function siblings(): Siblings
    {
        return new Siblings($this);
    }

    public function siblings_father(): HalfSiblings
    {
        return new HalfSiblings($this, Sex::Male);
    }

    public function siblings_mother(): HalfSiblings
    {
        return new HalfSiblings($this, Sex::Female);
    }

    public function marriages(): Marriages
    {
        return new Marriages($this);
    }

    public function children(): Children
    {
        return new Children($this);
    }

    public function formatSimpleDates(): ?string
    {
        $dates = Vec\filter_nulls([
            $this->birth_year ? "∗{$this->birth_year}" : null,
            $this->death_year ? "✝{$this->death_year}" : null,
        ]);

        return $dates === [] ? null : Str\join($dates, ', ');
    }

    public function formatSimpleName(): string
    {
        $name = $this->name . ' ';

        $name .= $this->last_name
            ? "{$this->last_name} ({$this->family_name})"
            : $this->family_name;

        return $name;
    }

    /**
     * @param 'family'|'last' $type
     *
     * @return Collection<int, object{letter: string, count: int}>
     */
    public static function letters(string $type): Collection
    {
        $nameQuery = match ($type) {
            'family' => 'family_name',
            'last' => 'ifnull(last_name, family_name)',
        };

        return Cache::rememberForever(
            "letters_{$type}",
            fn () => DB::table('people')
                ->selectRaw(<<<SQL
                    substr({$nameQuery}, 1, 1)
                    /* collate utf8mb4_0900_as_ci */
                    as letter,
                    count(*) as count
                    SQL)
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
