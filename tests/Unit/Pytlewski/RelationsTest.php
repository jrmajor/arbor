<?php

use App\Models\Person;
use App\Services\Pytlewski\Marriage;
use App\Services\Pytlewski\Pytlewski;
use App\Services\Pytlewski\Relative;

it('can load relations', function ($id, $source, $attributes) {
    Http::fake([
        Pytlewski::url($id) => Http::response($source, 200),
    ]);

    $pytlewski = new Pytlewski($id);

    if ((int) $id === 704) {
        $mother = Person::factory()->create(['id_pytlewski' => 1420]);

        $father = Person::factory()->create(['id_pytlewski' => 637]);

        $wife = Person::factory()->create(['id_pytlewski' => 705]);

        $firstChild = Person::factory()->create(['id_pytlewski' => 706]);

        $secondChild = Person::factory()->create(['id_pytlewski' => 707]);

        $secondSibling = Person::factory()->create(['id_pytlewski' => 678]);

        expect($pytlewski->father)->toBeInstanceOf(Relative::class)
            ->and($pytlewski->mother->id)->toBe('1420')
            ->and($pytlewski->mother->person->id)->toBe($mother->id)
            ->and($pytlewski->mother->surname)->toBe('Ptakowska')
            ->and($pytlewski->mother->name)->toBe('Maryanna');

        expect($pytlewski->father)->toBeInstanceOf(Relative::class)
            ->and($pytlewski->father->id)->toBe('637')
            ->and($pytlewski->father->person->id)->toBe($father->id)
            ->and($pytlewski->father->surname)->toBe('Pytlewski')
            ->and($pytlewski->father->name)->toBe('Łukasz');

        expect($pytlewski->marriages)->toHaveCount(1)
            ->and($pytlewski->marriages->first())->toBeInstanceOf(Marriage::class)
            ->and($pytlewski->marriages->first()->id)->toBe('705')
            ->and($pytlewski->marriages->first()->person->id)->toBe($wife->id)
            ->and($pytlewski->marriages->first()->name)->toBe('Frankiewicz, Bronisława')
            ->and($pytlewski->marriages->first()->date)->toBe('29.09.1885')
            ->and($pytlewski->marriages->first()->place)->toBe('Sulmierzyce');

        expect($pytlewski->children)->toHaveCount(6);
        $pytlewski->children->each(fn ($child) => expect($child)->toBeInstanceOf(Relative::class));

        expect($pytlewski->children->first()->id)->toBe('706')
            ->and($pytlewski->children->first()->person->id)->toBe($firstChild->id)
            ->and($pytlewski->children->first()->name)->toBe('Zygmunt-Stanisław');

        expect($pytlewski->children->get(1)->id)->toBe('707')
            ->and($pytlewski->children->get(1)->person->id)->toBe($secondChild->id)
            ->and($pytlewski->children->get(1)->name)->toBe('Seweryn');

        expect($pytlewski->siblings)->toHaveCount(20);
        $pytlewski->siblings->each(fn ($sibling) => expect($sibling)->toBeInstanceOf(Relative::class));

        expect($pytlewski->siblings->first()->id)->toBeNull()
            ->and($pytlewski->siblings->first()->person)->toBeNull()
            ->and($pytlewski->siblings->first()->name)->toBe('Roch-Tomasz');

        expect($pytlewski->siblings->get(1)->id)->toBe('678')
            ->and($pytlewski->siblings->get(1)->person->id)->toBe($secondSibling->id)
            ->and($pytlewski->siblings->get(1)->name)->toBe('Katarzyna');
    } else {
        if (isset($attributes['mother_surname']) || isset($attributes['mother_name'])) {
            expect($pytlewski->mother)->toBeInstanceOf(Relative::class)
                ->and($pytlewski->mother->id)->toBe($attributes['mother_id'])
                ->and($pytlewski->mother->person)->toBeNull()
                ->and($pytlewski->mother->surname)->toBe($attributes['mother_surname'])
                ->and($pytlewski->mother->name)->toBe($attributes['mother_name']);
        } else {
            expect($pytlewski->mother)->toBeNull();
        }

        if (isset($attributes['father_surname']) || isset($attributes['father_name'])) {
            expect($pytlewski->father)->toBeInstanceOf(Relative::class)
                ->and($pytlewski->father->id)->toBe($attributes['father_id'])
                ->and($pytlewski->father->person)->toBeNull()
                ->and($pytlewski->father->surname)->toBe($attributes['father_surname'])
                ->and($pytlewski->father->name)->toBe($attributes['father_name']);
        } else {
            expect($pytlewski->father)->toBeNull();
        }

        foreach ($attributes['marriages'] as $marriageKey => $marriageAttributes) {
            $marriage = $pytlewski->marriages->get($marriageKey);

            expect($marriage)->toBeInstanceOf(Marriage::class)
                ->and($marriage->person)->toBeNull();

            foreach ($marriageAttributes as $key => $value) {
                expect($marriage->$key)->toBe($value);
            }
        }

        foreach ($attributes['children'] as $childKey => $childAttributes) {
            $child = $pytlewski->children->get($childKey);

            expect($child)->toBeInstanceOf(Relative::class)
                ->and($child->person)->toBeNull();

            foreach ($childAttributes as $key => $value) {
                expect($child->$key)->toBe($value);
            }
        }

        foreach ($attributes['siblings'] as $siblingKey => $siblingAttributes) {
            $sibling = $pytlewski->siblings->get($siblingKey);

            expect($sibling)->toBeInstanceOf(Relative::class)
                ->and($sibling->person)->toBeNull();

            foreach ($siblingAttributes as $key => $value) {
                expect($sibling->$key)->toBe($value);
            }
        }
    }
})->with('pytlewscy');
