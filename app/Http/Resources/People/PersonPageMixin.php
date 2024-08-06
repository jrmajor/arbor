<?php

namespace App\Http\Resources\People;

use App\Models\Person;
use Illuminate\Support\Facades\Gate;

/**
 * @property Person $resource
 */
trait PersonPageMixin
{
    /**
     * @return array<mixed>
     */
    public function personMixin(): array
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
            'perm' => [
                'update' => Gate::allows('update', $this->resource),
                'changeVisibility' => Gate::allows('changeVisibility', $this->resource),
                'delete' => Gate::allows('delete', $this->resource),
                'restore' => Gate::allows('restore', $this->resource),
                'viewHistory' => Gate::allows('viewHistory', $this->resource),
            ],
        ];
    }
}
