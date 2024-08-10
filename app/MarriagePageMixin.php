<?php

namespace App\Http\Resources\Marriages;

use App\Models\Marriage;
use Illuminate\Support\Facades\Gate;

/**
 * @property Marriage $resource
 */
trait MarriagePageMixin
{
    /**
     * @return array<mixed>
     */
    public function marriageMixin(): array
    {
        return [
            'id' => $this->resource->id,
            'isTrashed' => $this->resource->trashed(),
            'man' => [
                'id' => $this->resource->man->id,
                'name' => $this->resource->man->name,
                'familyName' => $this->resource->man->family_name,
                'isDead' => $this->resource->man->dead,
            ],
            'woman' => [
                'id' => $this->resource->man->id,
                'name' => $this->resource->man->name,
                'familyName' => $this->resource->man->family_name,
                'isDead' => $this->resource->man->dead,
            ],
            'perm' => [
                'viewHistory' => Gate::allows('viewHistory', $this->resource),
            ],
        ];
    }
}
