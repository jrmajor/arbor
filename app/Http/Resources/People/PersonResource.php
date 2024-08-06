<?php

namespace App\Http\Resources\People;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

/**
 * @property Person $resource
 */
final class PersonResource extends JsonResource
{
    public int $recursiveWithParents = 0;

    /**
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'visible' => $visible = Gate::allows('view', $this->resource),
            $this->mergeWhen($visible, [
                'name' => $this->resource->name,
                'familyName' => $this->resource->family_name,
                'lastName' => $this->resource->last_name,
                'isDead' => $this->resource->dead,
                'birthYear' => $this->resource->birth_year,
                'deathYear' => $this->resource->death_year,
            ]),
            'pytlewskiUrl' => $this->resource->pytlewski?->url,
            // @phpstan-ignore property.protected
            'wielcyUrl' => $this->resource->wielcy?->url,
            $this->mergeWhen($this->recursiveWithParents > 0, function () {
                $father = new self($this->resource->father);
                $father->recursiveWithParents = $this->recursiveWithParents - 1;
                $mother = new self($this->resource->mother);
                $mother->recursiveWithParents = $this->recursiveWithParents - 1;

                return ['father' => $father, 'mother' => $mother];
            }),
            'perm' => [
                'update' => Gate::allows('update', $this->resource),
            ],
        ];
    }
}
