<?php

namespace App\Http\Resources\People;

use App\Models\Person;
use App\Services\Sources\Source;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Person $resource
 */
final class EditPersonResource extends JsonResource
{
    use PersonPageMixin;

    /**
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...$this->personMixin(),
            'middleName' => $this->resource->middle_name,
            'wielcyId' => $this->resource->id_wielcy,
            'pytlewskiId' => $this->resource->id_pytlewski,
            'birthDateFrom' => $this->resource->birth_date_from?->format('Y-m-d'),
            'birthDateTo' => $this->resource->birth_date_to?->format('Y-m-d'),
            'birthPlace' => $this->resource->birth_place,
            'deathDateFrom' => $this->resource->death_date_from?->format('Y-m-d'),
            'deathDateTo' => $this->resource->death_date_to?->format('Y-m-d'),
            'deathPlace' => $this->resource->death_place,
            'deathCause' => $this->resource->death_cause,
            'funeralDateFrom' => $this->resource->funeral_date_from?->format('Y-m-d'),
            'funeralDateTo' => $this->resource->funeral_date_to?->format('Y-m-d'),
            'funeralPlace' => $this->resource->funeral_place,
            'burialDateFrom' => $this->resource->burial_date_from?->format('Y-m-d'),
            'burialDateTo' => $this->resource->burial_date_to?->format('Y-m-d'),
            'burialPlace' => $this->resource->burial_place,
            'fatherId' => $this->resource->father_id,
            'motherId' => $this->resource->mother_id,
            'sources' => $this->resource->sources->map(fn (Source $s) => $s->raw()),
        ];
    }
}
