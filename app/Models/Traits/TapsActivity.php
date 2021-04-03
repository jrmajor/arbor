<?php

namespace App\Models\Traits;

use App\Models\Activity;
use Illuminate\Support\Arr;

trait TapsActivity
{
    public function tapActivity(Activity $activity, string $eventName)
    {
        match ($eventName) {
            'updated' => $this->tapUpdatedActivity($activity),
            'deleted' => $this->tapDeletedActivity($activity),
            'restored' => $this->tapRestoredActivity($activity),
            default => null,
        };
    }

    protected function tapUpdatedActivity(Activity $activity)
    {
        $old = $activity->properties['old'];
        $attributes = $activity->properties['attributes'];

        if (array_key_exists('biography', $attributes)) {
            if ($activity->properties['old']['biography'] === null) {
                $activity->description = 'added-biography';
            } elseif ($activity->properties['attributes']['biography'] === null) {
                $activity->description = 'deleted-biography';
            } else {
                $activity->description = 'updated-biography';
            }

            $activity->properties = collect([
                'old' => $activity->properties['old']['biography'],
                'new' => $activity->properties['attributes']['biography'],
            ]);

            return;
        }

        foreach (static::$dateRanges as $date) {
            if (
                Arr::has($attributes, $date.'_from')
                && ! Arr::has($attributes, $date.'_to')
            ) {
                $old[$date.'_to'] = $this->{$date.'_to'}->format('Y-m-d');
                $attributes[$date.'_to'] = $this->{$date.'_to'}->format('Y-m-d');
            }

            if (
                ! Arr::has($attributes, $date.'_from')
                && Arr::has($attributes, $date.'_to')
            ) {
                $old[$date.'_from'] = $this->{$date.'_from'}->format('Y-m-d');
                $attributes[$date.'_from'] = $this->{$date.'_from'}->format('Y-m-d');
            }
        }

        $activity->properties = compact('old', 'attributes');
    }

    protected function tapDeletedActivity(Activity $activity)
    {
        $attributes = $activity->properties['attributes'];

        $activity->properties = collect([
            'attributes' => Arr::only($attributes, 'deleted_at'),
        ]);
    }

    protected function tapRestoredActivity(Activity $activity)
    {
        $attributes = $activity->properties['attributes'];

        $activity->properties = collect([
            'attributes' => Arr::only($attributes, 'deleted_at'),
        ]);
    }
}
