<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Spatie\Activitylog\Contracts\Activity;

trait TapsActivity
{
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName == 'updated') {
            $this->tapUpdatedActivity($activity);
        }

        if ($eventName == 'deleted') {
            $this->tapDeletedActivity($activity);
        }
    }

    protected function tapUpdatedActivity(Activity $activity)
    {
        $old = $activity->properties['old'];
        $attributes = $activity->properties['attributes'];

        foreach (static::$dateTuples as $date) {
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

        $activity->properties = collect([
            'old' => $old,
            'attributes' => $attributes,
        ]);
    }

    protected function tapDeletedActivity(Activity $activity)
    {
        $attributes = $activity->properties['attributes'];

        $activity->properties = collect([
            'attributes' => Arr::only($attributes, 'deleted_at'),
        ]);
    }
}
