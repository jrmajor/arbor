<?php

namespace App\Http\Resources\Marriages;

use App\Models\Marriage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Marriage $resource
 */
final class MarriagePageResource extends JsonResource
{
    use MarriagePageMixin;

    /**
     * @return array<mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->marriageMixin();
    }
}
