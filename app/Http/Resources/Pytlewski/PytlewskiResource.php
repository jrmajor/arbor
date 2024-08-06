<?php

namespace App\Http\Resources\Pytlewski;

use App\Services\Pytlewski\Pytlewski;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Pytlewski $resource
 */
final class PytlewskiResource extends JsonResource
{
    /**
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->resource->name,
            'middleName' => $this->resource->middleName,
            'familyName' => $this->resource->familyName,
            'lastName' => $this->resource->lastName,
            'father' => new RelativeResource($this->resource->father),
            'mother' => new RelativeResource($this->resource->mother),
            'marriages' => MarriageResource::collection($this->resource->marriages),
            'children' => RelativeResource::collection($this->resource->children),
            'siblings' => RelativeResource::collection($this->resource->siblings),
        ];
    }
}
