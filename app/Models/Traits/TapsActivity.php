<?php

namespace App\Models\Traits;

use App\Models\Activity;
use Illuminate\Support\Arr;

trait TapsActivity
{
    public function tapActivity(Activity $activity, string $eventName): void
    {
        match ($eventName) {
            'updated' => $this->tapUpdated($activity),
            'deleted', 'restored' => $this->tapDeletedOrRestored($activity),
            default => null,
        };
    }

    private function tapUpdated(Activity $activity): void
    {
        $attr = $activity->properties['attributes'];

        match (true) {
            array_key_exists('visibility', $attr) => $this->visibilityChanged($activity),
            array_key_exists('biography', $attr) => $this->biographyUpdated($activity),
            default => $this->modelUpdated($activity),
        };
    }

    private function visibilityChanged(Activity $activity): void
    {
        $activity->description = 'changed-visibility';
    }

    private function biographyUpdated(Activity $activity): void
    {
        $activity->description = match (null) {
            $activity->properties['old']['biography'] => 'added-biography',
            $activity->properties['attributes']['biography'] => 'deleted-biography',
            default => 'updated-biography',
        };

        $activity->properties = [
            'old' => $activity->properties['old']['biography'],
            'new' => $activity->properties['attributes']['biography'],
        ];
    }

    private function modelUpdated(Activity $activity): void
    {
        $old = $activity->properties['old'];
        $attributes = $activity->properties['attributes'];

        foreach (static::$dateRanges as $date) {
            $from = "{$date}_from";
            $to = "{$date}_to";

            if (Arr::has($attributes, $from) && ! Arr::has($attributes, $to)) {
                $old[$to] = $this->{$to}->format('Y-m-d');
                $attributes[$to] = $this->{$to}->format('Y-m-d');
            }

            if (! Arr::has($attributes, $from) && Arr::has($attributes, $to)) {
                $old[$from] = $this->{$from}->format('Y-m-d');
                $attributes[$from] = $this->{$from}->format('Y-m-d');
            }
        }

        $activity->properties = compact('old', 'attributes');
    }

    private function tapDeletedOrRestored(Activity $activity): void
    {
        $attributes = $activity->properties['attributes'];

        $activity->properties = [
            'attributes' => Arr::only($attributes, 'deleted_at'),
        ];
    }
}
