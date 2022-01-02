<?php

namespace App\Services;

use App\Models\Person;
use Carbon\Carbon;

final class Age
{
    public const GENERATION_INTERVAL = 32;

    public function __construct(
        private Person $person,
    ) { }

    /**
     * @param Carbon|array{Carbon, Carbon} $time
     */
    public function at(array|Carbon $time): ?int
    {
        if (! $this->person->birth_date) {
            return null;
        }

        [$toFrom, $toTo] = is_array($time) ? $time : [$time, $time];

        $either = $this->person->birth_date_to->diffInYears($toFrom);
        $or = $this->person->birth_date_from->diffInYears($toTo);

        return $or;
    }

    /**
     * @param Carbon|array{Carbon, Carbon} $time
     */
    public function prettyAt(array|Carbon $time): ?string
    {
        if (! $this->person->birth_date) {
            return null;
        }

        [$toFrom, $toTo] = is_array($time) ? $time : [$time, $time];

        $either = $this->person->birth_date_to->diffInYears($toFrom);
        $or = $this->person->birth_date_from->diffInYears($toTo);

        return $either === $or ? $either : "{$either}-{$or}";
    }

    public function current(): ?int
    {
        return $this->at(now());
    }

    public function prettyCurrent(): ?string
    {
        return $this->prettyAt(now());
    }

    public function atDeath(): ?int
    {
        if (! $this->person->death_date) {
            return null;
        }

        return $this->at([
            $this->person->death_date_from,
            $this->person->death_date_to,
        ]);
    }

    public function prettyAtDeath(): ?string
    {
        if (! $this->person->death_date) {
            return null;
        }

        return $this->prettyAt([
            $this->person->death_date_from,
            $this->person->death_date_to,
        ]);
    }

    public function estimatedBirthDate(): ?int
    {
        $interval = self::GENERATION_INTERVAL;
        $prediction = collect();

        $motherYear = $this->person->mother?->birth_year;
        $fatherYear = $this->person->father?->birth_year;

        if ($motherYear && $fatherYear) {
            $prediction->put('parents', (($motherYear + $fatherYear) / 2) + $interval);
        } elseif ($motherYear || $fatherYear) {
            $prediction->put('parents', $motherYear + $fatherYear + $interval);
        }

        $prediction->put('children',
            $this->person->children->avg('birth_year') ? $this->person->children->avg('birth_year') - $interval : null,
        );

        $prediction->put('partners',
            $this->person->marriages
                ->map->partner($this->person)
                ->avg('birth_year'),
        );

        $prediction->put('siblings',
            $this->person->siblings
                ->merge($this->person->siblings_mother)
                ->merge($this->person->siblings_father)
                ->avg('birth_year'),
        );

        return $prediction->avg() ? (int) round($prediction->avg()) : null;
    }

    public function estimatedBirthDateError(): ?int
    {
        if (! $this->estimatedBirthDate() || ! $this->person->birth_year) {
            return null;
        }

        return abs($this->estimatedBirthDate() - $this->person->birth_year);
    }
}
