<?php

namespace App\Services\Relationship;

final readonly class Relationship
{
    /**
     * Each step says how its person is reached from the previous one: `parent`, `child` or `spouse`.
     * The kinship has a `type` naming the relationship and the details needed to word it.
     *
     * @param list<array{id: int, step: ?string}> $steps
     * @param list<int> $commonAncestors
     * @param array<string, int|string|bool> $kinship
     */
    public function __construct(
        public bool $blood,
        public array $steps,
        public array $commonAncestors,
        public array $kinship,
    ) { }
}
