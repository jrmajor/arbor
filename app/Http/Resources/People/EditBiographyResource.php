<?php

namespace App\Http\Resources\People;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Person $resource
 */
final class EditBiographyResource extends JsonResource
{
    use PersonPageMixin;

    /**
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...$this->personMixin(),
            'biography' => $this->resource->biography,
        ];
    }
}
