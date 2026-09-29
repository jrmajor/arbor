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
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'siblingsFather' => PersonResource::collection($this->resource->siblings_father),
            'siblingsMother' => PersonResource::collection($this->resource->siblings_mother),
            'marriages' => $this->resource->marriages->map(function (Marriage $m) {
                $m = new MarriageResource($m);
                $m->partnerFor = $this->resource;

                return $m;
            }),
            'children' => $this->resource->children->map(
                fn (Person $child) => new PersonResource($child)->withParentIds(),
            ),
            'siblingsBefore' => $this->siblingsBefore(),
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
