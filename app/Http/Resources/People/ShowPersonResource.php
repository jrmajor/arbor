<?php

namespace App\Http\Resources\People;

use App\Http\Resources\Marriages\MarriageResource;
use App\Http\Resources\Pytlewski\PytlewskiResource;
use App\Http\Resources\Wielcy\WielcyResource;
use App\Models\Marriage;
use App\Models\Person;
use App\Services\Pytlewski\Pytlewski;
use App\Services\Sources\Source;
use App\Services\Wielcy\Wielcy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

/**
 * @property Person $resource
 */
final class ShowPersonResource extends JsonResource
{
    use PersonPageMixin;

    public function __construct(
        Person $resource,
        private readonly ?Pytlewski $pytlewski,
        private readonly ?Wielcy $wielcy,
    ) {
        parent::__construct($resource);
    }

    /**
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        $father = new PersonResource($this->resource->father);
        $father->recursiveWithParents = 1;
        $mother = new PersonResource($this->resource->mother);
        $mother->recursiveWithParents = 1;

        return [
            ...$this->personMixin(),
            'middleName' => $this->resource->middle_name,
            'birthYear' => $this->resource->birth_year,
            'birthDate' => $this->resource->birth_date,
            'birthPlace' => $this->resource->birth_place,
            'deathYear' => $this->resource->death_year,
            'deathDate' => $this->resource->death_date,
            'deathPlace' => $this->resource->death_place,
            'deathCause' => $this->resource->death_cause,
            'funeralDate' => $this->resource->funeral_date,
            'funeralPlace' => $this->resource->funeral_place,
            'burialDate' => $this->resource->burial_date,
            'burialPlace' => $this->resource->burial_place,
            'father' => $father,
            'mother' => $mother,
            'siblings' => PersonResource::collection($this->resource->siblings),
            'siblingsFather' => $this->resource->siblings_father->map(
                fn (Person $sibling) => new PersonResource($sibling)->withParentIds(),
            ),
            'siblingsMother' => $this->resource->siblings_mother->map(
                fn (Person $sibling) => new PersonResource($sibling)->withParentIds(),
            ),
            'marriages' => $this->resource->marriages->map(function (Marriage $m) {
                $m = new MarriageResource($m);
                $m->partnerFor = $this->resource;

                return $m;
            }),
            'children' => $this->resource->children->map(
                fn (Person $child) => new PersonResource($child)->withParentIds(),
            ),
            'siblingsBefore' => $this->siblingsBefore(),
            'otherParents' => PersonResource::collection($this->otherParents()),
            'timeline' => $this->timeline(),
            'age' => [
                'current' => $this->resource->age->current(),
                'prettyCurrent' => $this->resource->age->prettyCurrent(),
                'atDeath' => $this->resource->age->atDeath(),
                'prettyAtDeath' => $this->resource->age->prettyAtDeath(),
                $this->mergeWhen(
                    ! $this->resource->birth_date || $request->user()?->isSuperAdmin(),
                    fn () => [
                        'estimatedBirthDate' => $this->resource->age->estimatedBirthDate(),
                        'estimatedBirthDateError' => $this->resource->age->estimatedBirthDateError(),
                    ],
                ),
            ],
            'pytlewskiId' => $this->resource->id_pytlewski,
            'pytlewskiUrl' => $this->resource->pytlewski_url,
            'pytlewski' => new PytlewskiResource($this->pytlewski),
            'wielcyId' => $this->resource->id_wielcy ?: null,
            'wielcyUrl' => $this->resource->wielcy_url,
            'wielcy' => new WielcyResource($this->wielcy),
            'biography' => $this->resource->biography,
            'sources' => $this->resource->sources->map(fn (Source $s) => $s->markup()),
        ];
    }

    /**
     * @return EloquentCollection<int, Person>
     */
    private function otherParents(): EloquentCollection
    {
        $person = $this->resource;

        $ids = collect()
            ->merge($person->children->map(
                fn (Person $child) => $child->father_id === $person->id ? $child->mother_id : $child->father_id,
            ))
            ->merge($person->siblings_father->pluck('mother_id'))
            ->merge($person->siblings_mother->pluck('father_id'))
            ->filter()
            ->unique();

        return $ids->isEmpty()
            ? new EloquentCollection()
            : Person::query()->whereIn('id', $ids)->get();
    }

    /**
     * Year ranges of the close family for the lifespan timeline.
     * Hidden people and marriages are left out.
     *
     * @return array{people: list<mixed>, marriages: list<mixed>}
     */
    private function timeline(): array
    {
        $person = $this->resource;

        $people = collect([$person, $person->father, $person->mother])
            ->merge($person->siblings)
            ->merge($person->siblings_father)
            ->merge($person->siblings_mother)
            ->merge($person->marriages->map(fn (Marriage $m) => $m->partner($person)))
            ->merge($person->children)
            ->filter(fn (?Person $p) => $p && Gate::allows('view', $p))
            ->unique('id')
            ->map(fn (Person $p) => [
                'id' => $p->id,
                'birth' => $this->years($p->birth_date_from, $p->birth_date_to),
                'death' => $this->years($p->death_date_from, $p->death_date_to),
                'dead' => $p->dead,
            ]);

        $marriages = $person->marriages
            ->filter(fn (Marriage $m) => Gate::allows('view', $m))
            ->map(fn (Marriage $m) => [
                'id' => $m->id,
                'start' => $m->first_event_date_from
                    ? $this->years($m->first_event_date_from, $m->first_event_date_to)
                    : $this->years($m->second_event_date_from, $m->second_event_date_to),
                'divorce' => $m->divorced
                    ? $this->years($m->divorce_date_from, $m->divorce_date_to)
                    : null,
            ]);

        return ['people' => $people->values()->all(), 'marriages' => $marriages->values()->all()];
    }

    /**
     * @return ?array{int, int}
     */
    private function years(?Carbon $from, ?Carbon $to): ?array
    {
        return $from && $to ? [$from->year, $to->year] : null;
    }

    /**
     * Siblings are ordered by birth date with unknown dates first,
     * so the siblings born before the person always form a prefix.
     */
    private function siblingsBefore(): int
    {
        $birthDate = $this->resource->birth_date_from;

        return $this->resource->siblings
            ->filter(fn (Person $sibling) => $sibling->birth_date_from === null
                || ($birthDate !== null && $sibling->birth_date_from->lte($birthDate)))
            ->count();
    }
}
