<?php

namespace App\Http\Resources\Dashboard;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Activity $resource
 */
final class ActivityResource extends JsonResource
{
    /**
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        $description = $this->resource->description;

        if ($description === 'changed-visibility') {
            $description = $this->resource->attribute_changes['attributes']['visibility']
                ? 'made_visible'
                : 'made_invisible';
        }

        assert($this->resource->causer === null || $this->resource->causer instanceof User);

        return [
            'id' => $this->resource->id,
            'logName' => $this->resource->log_name,
            'description' => $description,
            'subjectId' => $this->resource->subject_id,
            'causer' => $this->resource->causer?->username,
            'datetime' => $this->resource->created_at->format('Y-m-d H:i'),
        ];
    }
}
