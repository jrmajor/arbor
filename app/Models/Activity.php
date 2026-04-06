<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Contracts\Activity as ActivityContract;
use Spatie\Activitylog\Enums\ActivityEvent;

class Activity extends Model implements ActivityContract
{
    protected $table = 'activity_log';

    public $guarded = [];

    /**
     * @return array{
     *     attribute_changes: 'collection',
     *     properties: 'collection',
     * }
     */
    protected function casts(): array
    {
        return [
            'attribute_changes' => 'collection',
            'properties' => 'collection',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    public function getProperty(string $propertyName, mixed $defaultValue = null): mixed
    {
        return data_get($this->properties?->toArray() ?? [], $propertyName, $defaultValue);
    }

    /**
     * @param Builder<self> $query
     * @param string|list<string> ...$logNames
     *
     * @return Builder<self>
     */
    public function scopeInLog(Builder $query, string|array ...$logNames): Builder
    {
        if (is_array($logNames[0])) {
            $logNames = $logNames[0];
        }

        return $query->whereIn('log_name', $logNames);
    }

    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public function scopeCausedBy(Builder $query, Model $causer): Builder
    {
        return $query
            ->where('causer_type', $causer->getMorphClass())
            ->where('causer_id', $causer->getKey());
    }

    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public function scopeForSubject(Builder $query, Model $subject): Builder
    {
        return $query
            ->where('subject_type', $subject->getMorphClass())
            ->where('subject_id', $subject->getKey());
    }

    /**
     * @param Builder<self> $query
     *
     * @return Builder<self>
     */
    public function scopeForEvent(Builder $query, string|ActivityEvent $event): Builder
    {
        return $query->where('event', $event instanceof ActivityEvent ? $event->value : $event);
    }

    public static function newest(): self
    {
        return self::query()->orderByDesc('id')->first();
    }
}
