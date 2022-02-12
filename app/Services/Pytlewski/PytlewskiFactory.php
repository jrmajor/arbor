<?php

namespace App\Services\Pytlewski;

use App\Models\Person;
use Carbon\CarbonInterval;
use Generator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Psl\Fun;
use Psl\Html;
use Psl\Regex;
use Psl\Str;
use Psl\Type;
use Psl\Type\Exception\CoercionException;
use Psl\Vec;
use Symfony\Component\DomCrawler\Crawler;

final class PytlewskiFactory
{
    public function find(int $id): ?Pytlewski
    {
        if (null === $source = $this->getSource($id)) {
            return null;
        }

        try {
            $crawler = (new Crawler($source))->filter('#metrzyczka')->children();
        } catch (InvalidArgumentException) {
            return null;
        }

        $attributes = $this->scrapeAttributes($crawler);

        if (! $this->exists($attributes)) {
            return null;
        }

        $relations = $this->scrapeRelations($crawler);
        $relatives = $this->eagerLoadRelatives($relations);
        $relations = $this->matchRelatives($relations, $relatives);

        return new Pytlewski($id, self::url($id), ...$attributes, ...$relations);
    }

    public function for(Person $person): ?Pytlewski
    {
        if ($person->id_pytlewski === null) {
            return null;
        }

        return $this->find($person->id_pytlewski);
    }

    private function exists(array $attributes): bool
    {
        return isset($attributes['familyName'])
            || isset($attributes['lastName'])
            || isset($attributes['name']);
    }

    private function getSource(int $id): ?string
    {
        return Cache::remember(
            "pytlewski.{$id}",
            CarbonInterval::week(),
            function () use ($id): ?string {
                try {
                    $source = Http::timeout(2)->get(self::url($id));
                } catch (ConnectionException) {
                    return null;
                }

                if (! $source->ok()) {
                    return null;
                }

                return Fun\pipe(
                    fn ($s) => iconv('Windows-1250', 'UTF-8', $s) ?: '',
                    fn ($s) => Str\after($s, '<table border=0 align=center width=500><tr><td>') ?? '',
                    fn ($s) => Str\before($s, '<td background="images/spacer.gif" width="35" height="1"></td>'),
                )($source->body());
            },
        );
    }

    /**
     * @return array{
     *     familyName?: string, lastName?: string, name?: string, middleName?: string,
     *     birthDate?: string, birthPlace?: string,
     *     deathDate?: string, deathPlace?: string, burialPlace?: string,
     *     photo?: string, bio?: string
     * }
     */
    private function scrapeAttributes(Crawler $crawler): array
    {
        return Arr::trim([
            ...$this->parseNames($crawler),
            ...$this->parseDates($crawler),
            ...$this->parsePhoto($crawler),
            ...$this->parseBio($crawler),
        ]);
    }

    /**
     * @return array{familyName?: string, lastName?: ?string, name?: string, middleName?: string}
     */
    private function parseNames(Crawler $crawler): array
    {
        try {
            $names = $crawler->eq(1)
                ->children()->eq(1)
                ->children()->first()
                ->children()->first()
                ->children()->first()
                ->children()->first()
                ->children()->first()
                ->html();
        } catch (InvalidArgumentException) {
            return [];
        }

        [$surnames, $names] = Str\split($names, '<br>');

        $names = Str\split($names, '-');
        $matches = Regex\first_match($surnames, '/(.*) \\((.*)\\).*/');

        return [
            'familyName' => Html\strip_tags($matches ? $matches[2] : $surnames),
            'lastName' => $matches ? Html\strip_tags($matches[1]) : null,
            'name' => array_shift($names),
            'middleName' => Str\join($names, ' '),
        ];
    }

    /**
     * @return array{birthDate?: string, birthPlace?: string, deathDate?: string, deathPlace?: ?string, burialPlace?: ?string}
     */
    private function parseDates(Crawler $crawler): array
    {
        try {
            $dates = $crawler->eq(1)
                ->children()->eq(1)
                ->children()->first()
                ->children()->first()
                ->children()->first()
                ->children()->eq(2)
                ->html();
        } catch (InvalidArgumentException) {
            return [];
        }

        $attributes = [];

        $dates = Str\split($dates, '<br>');

        $matches = Regex\first_match($dates[0], '/ur\\. ([^ ]*) w ([^<]*)/');

        if ($matches !== null) {
            $attributes['birthDate'] = $matches[1];

            if (! Str\contains($matches[2], 'brak')) {
                $attributes['birthPlace'] = $matches[2];
            }
        }

        if (! isset($dates[1])) {
            return $attributes;
        }

        $matches = Regex\first_match(
            $dates[1],
            '/\\(zm\\. ([^ ]*)(?: w ([^<),]*)(?:,poch\\.([^<)]*)?\\)?)?)?/',
        );

        if ($matches !== null) {
            $attributes['deathDate'] = $matches[1];

            if (! Str\contains($matches[2] ?? '', 'brak')) {
                $attributes['deathPlace'] = $matches[2] ?? null;
            }

            $attributes['burialPlace'] = $matches[3] ?? null;
        }

        return $attributes;
    }

    /**
     * @return array{photo?: string}
     */
    private function parsePhoto(Crawler $crawler): array
    {
        try {
            $photo = $crawler->eq(2)->children()->eq(1)->filter('img')->attr('src');
        } catch (InvalidArgumentException) {
            return [];
        }

        return ['photo' => "http://www.pytlewski.pl/index/drzewo/{$photo}"];
    }

    /**
     * @return array{bio?: ?string}
     */
    private function parseBio(Crawler $crawler): array
    {
        try {
            $bio = $crawler->eq(5)->children()->eq(1)->text();
        } catch (InvalidArgumentException) {
            return [];
        }

        return ['bio' => $bio === 'pusto :(' ? null : $bio];
    }

    /**
     * @return array{
     *     familyName?: string, lastName?: string, name?: string, middleName?: string,
     *     birthDate?: string, birthPlace?: string,
     *     deathDate?: string, deathPlace?: string, burialPlace?: string,
     *     photo?: string, bio?: string
     * }
     */
    private function scrapeRelations(Crawler $crawler): array
    {
        return Arr::trim([
            ...$this->parseParents($crawler),
            ...$this->parseRelations($crawler),
        ]);
    }

    /**
     * @return array{motherId?: ?string, fatherId?: ?string, motherSurname?: string, motherName?: string, fatherSurname?: string, fatherName?: string}
     */
    private function parseParents(Crawler $crawler): array
    {
        try {
            $parents = $crawler->eq(1)
                ->children()->eq(1)
                ->children()->first()
                ->children()->eq(3)
                ->children()->first()
                ->children()->first()
                ->html();
        } catch (InvalidArgumentException) {
            return [];
        }

        [$mother, $father] = Str\split(Str\replace($parents, '-', ' '), '<br>');

        $attributes = [
            'motherId' => Regex\first_match($mother, '/id=([0-9]+)/')[1] ?? null,
            'fatherId' => Regex\first_match($father, '/id=([0-9]+)/')[1] ?? null,
        ];

        $mother = explode(',', Html\strip_tags($mother));

        if (count($mother) === 2) {
            [$attributes['motherSurname'], $attributes['motherName']] = $mother;
        } else {
            $attributes['motherSurname'] = Str\join($mother, ' ');
        }

        $father = explode(',', Html\strip_tags($father));

        if (count($father) === 2) {
            [$attributes['fatherSurname'], $attributes['fatherName']] = $father;
        } else {
            $attributes['fatherSurname'] = Str\join($father, ' ');
        }

        return $attributes;
    }

    private function parseRelations(Crawler $crawler): Generator
    {
        try {
            $crawler = $crawler->eq(3)
                ->children()->first()
                ->children()->first()
                ->children();
        } catch (InvalidArgumentException) {
            return;
        }

        try {
            yield 'marriages' => $this->parseMarriages(
                $crawler->eq(0)->children()->first()->html(),
            );
        } catch (InvalidArgumentException) { }

        try {
            yield 'children' => $this->parseChildrenOrSiblings(
                $crawler->eq(1)->children()->first()->html(),
            );
        } catch (InvalidArgumentException) { }

        try {
            yield 'siblings' => $this->parseChildrenOrSiblings(
                $crawler->eq(2)->children()->first()->html(),
            );
        } catch (InvalidArgumentException) { }
    }

    /**
     * @return Collection<int, array{id: ?string, name: ?string, date: ?string, place: ?string}>
     */
    private function parseMarriages(string $marriages): Collection
    {
        $marriages = Str\split($marriages, '</center>')[1];
        $marriages = Str\split($marriages, '<br>');

        /** @phpstan-ignore-next-line */
        return collect($marriages)
            ->map(fn (string $marriage) => Regex\first_match(
                $marriage,
                '/(?:<u><a href=".*id=([0-9]*)">)?([^<>(]+)(?:<\\/a><\\/u>)? ?(?:\\(.*: ?([0-9.]*)(?:(?:,| )*([^)]*))?\\))?/',
            ))
            ->reject(function (?array $result) {
                return $result === null
                    || Str\starts_with($result[2] ?? '', 'Nie zawar');
            })
            ->map(fn (array $result) => [
                'id' => $this->parseId($result[1] ?? null),
                'name' => $result[2] ?? null,
                'date' => $result[3] ?? null,
                'place' => $result[4] ?? null,
            ]);
    }

    /**
     * @return Collection<int, array{id: ?string, name: ?string}>
     */
    private function parseChildrenOrSiblings(string $children): Collection
    {
        $children = Str\split($children, '</center>')[1];
        $children = Str\split($children, '; ');

        /** @phpstan-ignore-next-line */
        return collect($children)
            ->map(fn (string $child) => Regex\first_match(
                $child,
                '/(?:<u><a href=".*id=([0-9]*)">)?([^<>]*)/',
            ))
            ->reject(function (?array $result) {
                return $result === null
                    || Str\starts_with($result[2] ?? '', 'Nie ma');
            })
            ->map(fn (array $result) => [
                'id' => $this->parseId($result[1] ?? null),
                'name' => $result[2] ?? null,
            ]);
    }

    private function parseId(?string $id): ?int
    {
        try {
            return Type\int()->coerce(Str\trim($id));
        } catch (CoercionException) {
            return null;
        }
    }

    /**
     * @return EloquentCollection<int, Person>
     */
    private function eagerLoadRelatives(array $relatives): EloquentCollection
    {
        $ids = collect([
            ...$relatives['marriages'] ?? [],
            ...$relatives['children'] ?? [],
            ...$relatives['siblings'] ?? [],
        ])->pluck('id')->concat([
            'mother' => $relatives['motherId'] ?? null,
            'father' => $relatives['fatherId'] ?? null,
        ])->filter();

        /** @phpstan-ignore-next-line */
        return $ids ? Person::whereIn('id_pytlewski', $ids)->get() : new EloquentCollection();
    }

    /**
     * @param EloquentCollection<int, Person> $relatives
     */
    private function matchRelatives(array $relations, Collection $relatives): array
    {
        $mother = isset($relations['motherSurname']) || isset($relations['motherName'])
            ? new Relative(
                id: $id = $relations['motherId'] ?? null,
                url: $id !== null ? self::url($id) : null,
                name: $relations['motherName'] ?? null,
                surname: $relations['motherSurname'] ?? null,
                person: isset($relations['motherId']) ? $relatives
                    ->where('id_pytlewski', $relations['motherId'])->first() : null,
            ) : null;

        $father = isset($relations['fatherSurname']) || isset($relations['fatherName'])
            ? new Relative(
                id: $id = $relations['fatherId'] ?? null,
                url: $id !== null ? self::url($id) : null,
                name: $relations['fatherName'] ?? null,
                surname: $relations['fatherSurname'] ?? null,
                person: isset($relations['fatherId']) ? $relatives
                    ->where('id_pytlewski', $relations['fatherId'])->first() : null,
            ) : null;

        $marriages = Vec\map($relations['marriages'] ?? [], function ($marriage) use ($relatives) {
            if (isset($marriage['id'])) {
                $marriage['person'] = $relatives->where('id_pytlewski', $marriage['id'])->first();
                $marriage['url'] = self::url($marriage['id']);
            }

            return new Marriage(...$marriage);
        });

        $children = Vec\map($relations['children'] ?? [], function ($child) use ($relatives) {
            if (isset($child['id'])) {
                $child['person'] = $relatives->where('id_pytlewski', $child['id'])->first();
                $child['url'] = self::url($child['id']);
            }

            return new Relative(...$child);
        });

        $siblings = Vec\map($relations['siblings'] ?? [], function ($sibling) use ($relatives) {
            if (isset($sibling['id'])) {
                $sibling['person'] = $relatives->where('id_pytlewski', $sibling['id'])->first();
                $sibling['url'] = self::url($sibling['id']);
            }

            return new Relative(...$sibling);
        });

        return compact('mother', 'father', 'marriages', 'children', 'siblings');
    }

    public static function url(int $id): string
    {
        return "http://www.pytlewski.pl/index/drzewo/index.php?view=true&id={$id}";
    }
}
