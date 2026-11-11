<?php

namespace App\Services\Relationship;

use App\Models\Marriage;
use App\Models\Person;

/**
 * Finds how two people are related: through their closest common ancestor
 * when they share one, otherwise through the shortest chain that includes marriages.
 */
final class RelationshipFinder
{
    /** @var array<int, array{father: ?int, mother: ?int, sex: ?string}> */
    private array $people = [];

    /** @var array<int, list<int>> */
    private array $children = [];

    /** @var array<int, list<int>> */
    private array $spouses = [];

    public function find(Person $from, Person $to): ?Relationship
    {
        $this->load();

        return $this->byBlood($from->id, $to->id) ?? $this->byMarriage($from->id, $to->id);
    }

    private function load(): void
    {
        if ($this->people) {
            return;
        }

        foreach (Person::query()->toBase()->get(['id', 'father_id', 'mother_id', 'sex']) as $p) {
            $this->people[$p->id] = ['father' => $p->father_id, 'mother' => $p->mother_id, 'sex' => $p->sex];
        }

        foreach ($this->people as $id => $p) {
            foreach ([$p['father'], $p['mother']] as $parent) {
                if ($parent !== null && isset($this->people[$parent])) {
                    $this->children[$parent][] = $id;
                }
            }
        }

        foreach (Marriage::query()->toBase()->get(['man_id', 'woman_id']) as $m) {
            if (isset($this->people[$m->man_id], $this->people[$m->woman_id])) {
                $this->spouses[$m->man_id][] = $m->woman_id;
                $this->spouses[$m->woman_id][] = $m->man_id;
            }
        }
    }

    private function byBlood(int $from, int $to): ?Relationship
    {
        [$upDepth, $upPrev] = $this->ancestors($from);
        [$downDepth, $downPrev] = $this->ancestors($to);

        $common = array_intersect_key($upDepth, $downDepth);

        if (! $common) {
            return null;
        }

        $distance = fn (int $id) => $upDepth[$id] + $downDepth[$id];
        $closest = array_keys($common);
        usort($closest, fn (int $a, int $b) => [$distance($a), $upDepth[$a]] <=> [$distance($b), $upDepth[$b]]);

        $ancestor = $closest[0];

        // a couple is usually the common ancestor, so name both partners
        $ancestors = array_values(array_filter(
            $closest,
            fn (int $id) => $upDepth[$id] === $upDepth[$ancestor] && $downDepth[$id] === $downDepth[$ancestor],
        ));

        $up = [$ancestor];

        for ($id = $ancestor; $id !== $from; $id = $upPrev[$id]) {
            $up[] = $upPrev[$id];
        }

        $steps = array_map(fn (int $id) => ['id' => $id, 'step' => 'parent'], array_reverse($up));
        $steps[0]['step'] = null;

        for ($id = $ancestor; $id !== $to; $id = $downPrev[$id]) {
            $steps[] = ['id' => $downPrev[$id], 'step' => 'child'];
        }

        return new Relationship(true, $steps, $ancestors, $this->bloodKinship($steps));
    }

    /**
     * Ancestors by generations up, with each one's child on the way back.
     *
     * @return array{array<int, int>, array<int, int>}
     */
    private function ancestors(int $id): array
    {
        $depth = [$id => 0];
        $prev = [];
        $queue = [$id];

        while ($queue) {
            $current = array_shift($queue);

            foreach ([$this->people[$current]['father'] ?? null, $this->people[$current]['mother'] ?? null] as $parent) {
                if ($parent === null || ! isset($this->people[$parent]) || isset($depth[$parent])) {
                    continue;
                }

                $depth[$parent] = $depth[$current] + 1;
                $prev[$parent] = $current;
                $queue[] = $parent;
            }
        }

        return [$depth, $prev];
    }

    private function byMarriage(int $from, int $to): ?Relationship
    {
        $prev = [$from => null];
        $queue = [$from];

        while ($queue && ! array_key_exists($to, $prev)) {
            $current = array_shift($queue);
            $p = $this->people[$current];

            $neighbours = [
                ...array_map(fn (?int $id) => [$id, 'parent'], [$p['father'], $p['mother']]),
                ...array_map(fn (int $id) => [$id, 'child'], $this->children[$current] ?? []),
                ...array_map(fn (int $id) => [$id, 'spouse'], $this->spouses[$current] ?? []),
            ];

            foreach ($neighbours as [$id, $step]) {
                if ($id === null || ! isset($this->people[$id]) || array_key_exists($id, $prev)) {
                    continue;
                }

                $prev[$id] = [$current, $step];
                $queue[] = $id;
            }
        }

        if (! array_key_exists($to, $prev)) {
            return null;
        }

        $steps = [];

        for ($id = $to; $id !== $from; $id = $prev[$id][0]) {
            $steps[] = ['id' => $id, 'step' => $prev[$id][1]];
        }
        $steps[] = ['id' => $from, 'step' => null];

        $steps = array_reverse($steps);

        return new Relationship(false, $steps, [], $this->marriageKinship($steps));
    }

    /**
     * @param list<array{id: int, step: ?string}> $steps
     *
     * @return array<string, int|string|bool>
     */
    private function bloodKinship(array $steps): array
    {
        $up = count(array_filter($steps, fn (array $s) => $s['step'] === 'parent'));
        $down = count($steps) - 1 - $up;
        $last = count($steps) - 1;
        $to = $steps[$last]['id'];
        $sex = $this->sex($to);

        return match (true) {
            $up === 0 && $down === 0 => ['type' => 'self'],
            $down === 0 && $up === 1 => ['type' => 'parent', 'sex' => $sex],
            $up === 0 && $down === 1 => ['type' => 'child', 'sex' => $sex],
            $down === 0 => ['type' => 'grandparent', 'sex' => $sex, 'greats' => $up - 2],
            $up === 0 => ['type' => 'grandchild', 'sex' => $sex, 'greats' => $down - 2],
            $up === 1 && $down === 1 => [
                'type' => 'sibling',
                'sex' => $sex,
                'half' => ! $this->sameParents($steps[0]['id'], $to),
            ],
            $down === 1 => [
                'type' => 'uncle_aunt',
                'sex' => $sex,
                'greats' => $up - 2,
                // the sibling of the uncle or aunt, and the parent on whose side they are
                'via' => $this->sex($steps[$up - 1]['id']),
                'side' => $this->sex($steps[1]['id']) === 'xx' ? 'maternal' : 'paternal',
            ],
            $up === 1 => [
                'type' => 'nephew_niece',
                'sex' => $sex,
                'greats' => $down - 2,
                'via' => $this->sex($steps[2]['id']),
            ],
            default => [
                'type' => 'cousin',
                'sex' => $sex,
                'degree' => min($up, $down) - 1,
                'removed' => abs($up - $down),
                'older' => $up > $down,
                // Polish names first cousins after the parents linking them to the siblings
                'line' => match (true) {
                    $this->sex($steps[$last - 1]['id']) === 'xx' => 'cioteczny',
                    $this->sex($steps[1]['id']) === 'xx' => 'wujeczny',
                    default => 'stryjeczny',
                },
            ],
        };
    }

    /**
     * @param list<array{id: int, step: ?string}> $steps
     *
     * @return array<string, int|string|bool>
     */
    private function marriageKinship(array $steps): array
    {
        $sex = $this->sex($steps[count($steps) - 1]['id']);
        $path = implode(',', array_column(array_slice($steps, 1), 'step'));

        // old Polish names in-laws differently on the husband's and the wife's side
        $fromSex = $this->sex($steps[0]['id']);

        return match ($path) {
            'spouse' => ['type' => 'spouse', 'sex' => $sex],
            'spouse,parent' => ['type' => 'parent_in_law', 'sex' => $sex, 'fromSex' => $fromSex],
            'child,spouse' => ['type' => 'child_in_law', 'sex' => $sex],
            'spouse,parent,child' => [
                'type' => 'sibling_in_law',
                'sex' => $sex,
                'kind' => 'spouse_sibling',
                'via' => '',
                'fromSex' => $fromSex,
            ],
            'parent,child,spouse' => [
                'type' => 'sibling_in_law',
                'sex' => $sex,
                'kind' => 'sibling_spouse',
                'via' => $this->sex($steps[2]['id']),
                'fromSex' => $fromSex,
            ],
            'spouse,parent,child,spouse' => [
                'type' => 'co_sibling_in_law',
                'sex' => $sex,
                'via' => $this->sex($steps[3]['id']),
                'fromSex' => $fromSex,
            ],
            'parent,spouse' => ['type' => 'step_parent', 'sex' => $sex],
            'spouse,child' => ['type' => 'step_child', 'sex' => $sex],
            default => ['type' => 'in_law', 'sex' => $sex],
        };
    }

    private function sameParents(int $a, int $b): bool
    {
        $pa = $this->people[$a];
        $pb = $this->people[$b];

        return $pa['father'] !== null && $pa['mother'] !== null
            && $pa['father'] === $pb['father'] && $pa['mother'] === $pb['mother'];
    }

    private function sex(int $id): string
    {
        return $this->people[$id]['sex'] ?? 'unknown';
    }
}
