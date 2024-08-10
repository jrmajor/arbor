<?php

namespace App\Http\Resources\Marriages;

use App\Models\Marriage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Marriage $resource
 */
final class EditMarriageResource extends JsonResource
{
    use MarriagePageMixin;

    /**
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...$this->marriageMixin(),
            'womanOrder' => $this->resource->woman_order,
            'manOrder' => $this->resource->man_order,
            'rite' => $this->resource->rite,
            'firstEventType' => $this->resource->first_event_type,
            'firstEventDateFrom' => $this->resource->first_event_date_from?->format('Y-m-d'),
            'firstEventDateTo' => $this->resource->first_event_date_to?->format('Y-m-d'),
            'firstEventPlace' => $this->resource->first_event_place,
            'secondEventType' => $this->resource->second_event_type,
            'secondEventDateFrom' => $this->resource->second_event_date_from?->format('Y-m-d'),
            'secondEventDateTo' => $this->resource->second_event_date_to?->format('Y-m-d'),
            'secondEventPlace' => $this->resource->second_event_place,
            'divorced' => $this->resource->divorced,
            'divorceDateFrom' => $this->resource->divorce_date_from?->format('Y-m-d'),
            'divorceDateTo' => $this->resource->divorce_date_to?->format('Y-m-d'),
            'divorcePlace' => $this->resource->divorce_place,

        ];
    }
}
