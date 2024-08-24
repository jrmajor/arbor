<?php

namespace App\Http\Resources;

use App\Models\Activity;
use App\Services\Sources\Source;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Psl\Dict;
use Psl\Str;

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
        $attributes = [
            'old' => $this->resource->properties['old'] ?? null,
            'attributes' => $this->resource->properties['attributes'] ?? null,
            'new' => $this->resource->properties['new'] ?? null,
        ];

        $attributes = Dict\map($attributes, function ($value) {
            return match ($this->resource->description) {
                'created', 'updated' => $this->serializeAttributes($value),
                default => $value,
            };
        });

        return [
            'description' => $this->resource->description,
            // @phpstan-ignore property.notFound
            'causer' => $this->resource->causer?->username,
            'datetime' => $this->resource->created_at->format('Y-m-d H:i'),
            ...$attributes,
        ];
    }

    /**
     * @param ?array<string, mixed> $attributes
     *
     * @return ?array<string, mixed>
     */
    private function serializeAttributes(?array $attributes): ?array
    {
        if ($attributes === null) {
            return null;
        }

        if (array_key_exists('sources', $attributes)) {
            $attributes['sources'] = array_map(
                fn (string $s) => Source::from($s)->markup(),
                $attributes['sources'],
            );
        }

        $originalKeys = array_keys($attributes);

        foreach ($originalKeys as $key) {
            if (! Str\ends_with($key, '_to')) {
                continue;
            }
            $dateKey = Str\before_last($key, '_to');
            $from = $attributes["{$dateKey}_from"];
            $to = $attributes["{$dateKey}_to"];

            if ($from === null && $to === null) {
                $attributes[$dateKey] = null;
            } elseif ($from !== null && $to !== null) {
                $attributes[$dateKey] = carbon($from)->formatPeriodTo(carbon($to));
            } else {
                throw new Exception('History entry with invalid date range.');
            }

            unset($attributes["{$dateKey}_from"], $attributes["{$dateKey}_to"]);
        }

        return $attributes;
    }
}
