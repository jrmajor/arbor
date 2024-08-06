<?php

namespace App\Http\Resources\Marriages;

use App\Http\Resources\People\PersonResource;
use App\Models\Marriage;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

/**
 * @property Marriage $resource
 */
final class MarriageResource extends JsonResource
{
    public Person $partnerFor;

    /**
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'order' => $this->resource->order($this->partnerFor),
            'rite' => $this->resource->rite,
            'firstEvent' => $this->when($this->resource->hasFirstEvent(), fn () => [
                'type' => $this->resource->first_event_type,
                'date' => $this->resource->first_event_date,
                'place' => $this->resource->first_event_place,
            ]),
            'secondEvent' => $this->when($this->resource->hasSecondEvent(), fn () => [
                'type' => $this->resource->second_event_type,
                'date' => $this->resource->second_event_date,
                'place' => $this->resource->second_event_place,
            ]),
            'divorced' => $this->resource->divorced,
            'divorceDate' => $this->resource->divorce_date,
            'divorcePlace' => $this->resource->divorce_place,
            'partner' => new PersonResource($this->resource->partner($this->partnerFor)),
            'perm' => [
                'view' => Gate::allows('view', $this->resource),
                'update' => Gate::allows('update', $this->resource),
            ],
        ];
    }
}
