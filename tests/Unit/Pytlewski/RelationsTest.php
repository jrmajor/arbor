<?php

namespace Tests\Unit\Pytlewski;

use App\Models\Person;
use App\Services\Pytlewski\Marriage;
use App\Services\Pytlewski\Pytlewski;
use App\Services\Pytlewski\Relative;
use Illuminate\Support\Facades\Http;

it('can load relations', function (string $id, string $source, array $attributes) {
    Http::fake([
        Pytlewski::url($id) => Http::response($source),
    ]);

    $pytlewski = new Pytlewski($id);

    (int) $id === 704
        ? test704($pytlewski)
        : testOther($pytlewski, $attributes);
})->with('pytlewscy');

function test704(Pytlewski $pytlewski): void
{
    $relatives = Person::factory(6)->sequence(
        ['id_pytlewski' => 1420],
        ['id_pytlewski' => 637],
        ['id_pytlewski' => 705],
        ['id_pytlewski' => 706],
        ['id_pytlewski' => 707],
        ['id_pytlewski' => 678],
    )->create();

    [$mother, $father, $wife, $firstChild, $secondChild, $secondSibling] = $relatives;

    expect($pytlewski->mother)
        ->toBeInstanceOf(Relative::class)
        ->id->toBe('1420')
        ->person->id->toBe($mother->id)
        ->surname->toBe('Ptakowska')
        ->name->toBe('Maryanna');

    expect($pytlewski->father)
        ->toBeInstanceOf(Relative::class)
        ->id->toBe('637')
        ->person->id->toBe($father->id)
        ->surname->toBe('Pytlewski')
        ->name->toBe('Łukasz');

    expect($pytlewski->marriages)->toHaveCount(1);

    expect($pytlewski->marriages[0])
        ->toBeInstanceOf(Marriage::class)
        ->id->toBe('705')
        ->person->id->toBe($wife->id)
        ->name->toBe('Frankiewicz, Bronisława')
        ->date->toBe('29.09.1885')
        ->place->toBe('Sulmierzyce');

    expect($pytlewski->children)
        ->toHaveCount(6)
        ->each->toBeInstanceOf(Relative::class);

    expect($pytlewski->children[0])
        ->id->toBe('706')
        ->person->id->toBe($firstChild->id)
        ->name->toBe('Zygmunt-Stanisław');

    expect($pytlewski->children[1])
        ->id->toBe('707')
        ->person->id->toBe($secondChild->id)
        ->name->toBe('Seweryn');

    expect($pytlewski->siblings)
        ->toHaveCount(20)
        ->each->toBeInstanceOf(Relative::class);

    expect($pytlewski->siblings[0])
        ->id->toBeNull()
        ->person->toBeNull()
        ->name->toBe('Roch-Tomasz');

    expect($pytlewski->siblings[1])
        ->id->toBe('678')
        ->person->id->toBe($secondSibling->id)
        ->name->toBe('Katarzyna');
}

function testOther(Pytlewski $pytlewski, array $attributes): void
{
    if (isset($attributes['mother_surname']) || isset($attributes['mother_name'])) {
        expect($pytlewski->mother)
            ->toBeInstanceOf(Relative::class)
            ->id->toBe($attributes['mother_id'])
            ->person->toBeNull()
            ->surname->toBe($attributes['mother_surname'])
            ->name->toBe($attributes['mother_name']);
    } else {
        expect($pytlewski->mother)->toBeNull();
    }

    if (isset($attributes['father_surname']) || isset($attributes['father_name'])) {
        expect($pytlewski->father)
            ->toBeInstanceOf(Relative::class)
            ->id->toBe($attributes['father_id'])
            ->person->toBeNull()
            ->surname->toBe($attributes['father_surname'])
            ->name->toBe($attributes['father_name']);
    } else {
        expect($pytlewski->father)->toBeNull();
    }

    foreach ($attributes['marriages'] as $marriageKey => $marriageAttributes) {
        $marriage = $pytlewski->marriages[$marriageKey];

        expect($marriage)
            ->toBeInstanceOf(Marriage::class)
            ->person->toBeNull();

        foreach ($marriageAttributes as $key => $value) {
            expect($marriage->{$key})->toBe($value);
        }
    }

    foreach ($attributes['children'] as $childKey => $childAttributes) {
        $child = $pytlewski->children[$childKey];

        expect($child)
            ->toBeInstanceOf(Relative::class)
            ->person->toBeNull();

        foreach ($childAttributes as $key => $value) {
            expect($child->{$key})->toBe($value);
        }
    }

    foreach ($attributes['siblings'] as $siblingKey => $siblingAttributes) {
        $sibling = $pytlewski->siblings[$siblingKey];

        expect($sibling)
            ->toBeInstanceOf(Relative::class)
            ->person->toBeNull();

        foreach ($siblingAttributes as $key => $value) {
            expect($sibling->{$key})->toBe($value);
        }
    }
}
