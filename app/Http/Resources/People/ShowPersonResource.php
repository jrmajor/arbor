<?php

namespace App\Http\Resources\People;

use App\Http\Resources\Marriages\MarriageResource;
use App\Http\Resources\Pytlewski\PytlewskiResource;
use App\Models\Marriage;
use App\Models\Person;
use App\Services\Pytlewski\PytlewskiFactory;
use App\Services\Sources\Source;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Person $resource
 */
final class ShowPersonResource extends JsonResource
{
    use PersonPageMixin;

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
            'children' => PersonResource::collection($this->resource->children),
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
            'pytlewskiId' => $pytlewskiId = $this->resource->id_pytlewski,
            'pytlewskiUrl' => $pytlewskiId ? PytlewskiFactory::url($this->resource->id_pytlewski) : null,
            'pytlewski' => new PytlewskiResource($this->resource->pytlewski),
            // @phpstan-ignore property.protected
            'wielcy' => $this->when($this->resource->wielcy !== null, fn () => [
                // @phpstan-ignore property.protected
                'id' => $this->resource->wielcy->id,
                // @phpstan-ignore property.protected
                'url' => $this->resource->wielcy->url,
                // @phpstan-ignore property.protected
                'name' => $this->resource->wielcy->name,
            ]),
            'biography' => $this->resource->biography,
            'sources' => $this->resource->sources->map(fn (Source $s) => $s->markup()),
        ];
    }
}
