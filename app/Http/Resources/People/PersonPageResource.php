<?php

namespace App\Http\Resources\People;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

/**
 * @property Person $resource
 */
class PersonPageResource extends JsonResource
{
    /**
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'sex' => $this->resource->sex,
            'simpleName' => $this->resource->formatSimpleName(),
            'name' => $this->resource->name,
            'familyName' => $this->resource->family_name,
            'lastName' => $this->resource->last_name,
            'isDead' => $this->resource->dead,
            'isTrashed' => $this->resource->trashed(),
            'isVisible' => $this->resource->isVisible(),
            'canBeUpdated' => Gate::allows('update', $this->resource),
            'canHaveVisibilityChanged' => Gate::allows('changeVisibility', $this->resource),
            'canBeDeleted' => Gate::allows('delete', $this->resource),
            'canBeRestored' => Gate::allows('restore', $this->resource),
            'canHaveHistoryViewed' => Gate::allows('viewHistory', $this->resource),
        ];
    }
}
