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
            'deleted', 'restored' => $this->tapDeletedOrRestoredActivity($activity),
            default => null,
        };
    }

    protected function tapUpdatedActivity(Activity $activity)
    {
        $old = $activity->properties['old'];
        $attributes = $activity->properties['attributes'];

        if (array_key_exists('biography', $attributes)) {
            $this->tapBiographyRelatedActivity($activity);

            return;
        }

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

    protected function tapBiographyRelatedActivity(Activity $activity)
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

    protected function tapDeletedOrRestoredActivity(Activity $activity)
    {
        $attributes = $activity->properties['attributes'];

        $activity->properties = [
            'attributes' => Arr::only($attributes, 'deleted_at'),
        ];
    }
}
