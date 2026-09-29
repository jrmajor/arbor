<?php

namespace App\Http\Resources\Wielcy;

use App\Services\Wielcy\Wielcy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Wielcy $resource
 */
final class WielcyResource extends JsonResource
{
    /**
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->resource->name,
            'middleName' => $this->resource->middleName,
            'surname' => $this->resource->surname,
            'father' => new RelativeResource($this->resource->father),
            'mother' => new RelativeResource($this->resource->mother),
        ];
    }
}
