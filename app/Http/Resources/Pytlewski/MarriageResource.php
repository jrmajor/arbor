<?php

namespace App\Http\Resources\Pytlewski;

use App\Services\Pytlewski\Marriage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

/**
 * @property Marriage $resource
 */
final class MarriageResource extends JsonResource
{
    /**
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->resource->name,
            'id' => $this->resource->id,
            'url' => $this->resource->url,
            'arborId' => $this->resource->person?->id,
            'canBeViewedInArbor' => Gate::allows('view', $this->resource->person),
        ];
    }
}
