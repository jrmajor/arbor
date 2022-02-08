<?php

namespace Tests\Unit\Pytlewski;

use App\Models\Person;
use App\Services\Pytlewski\Marriage;
use App\Services\Pytlewski\Pytlewski;
use App\Services\Pytlewski\Relative;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class RelationsTest extends TestCase
{
    use UsesPytlewskiDataset;

    /**
     * @dataProvider provideScrapeCases
     */
    #[TestDox('it can load relations')]
    public function testRelations(int $id, string $source, array $attributes): void
    {
        Http::fake([
            Pytlewski::url($id) => Http::response($source),
        ]);

        $pytlewski = Pytlewski::find($id);

        $id === 704
            ? $this->test704($pytlewski)
            : $this->testOther($pytlewski, $attributes);
    }

    private function test704(Pytlewski $pytlewski): void
    {
        $relatives = Person::factory(6)->sequence(
            ['id_pytlewski' => 1420],
            ['id_pytlewski' => 637],
            ['id_pytlewski' => 705],
            ['id_pytlewski' => 706],
            ['id_pytlewski' => 707],
            ['id_pytlewski' => 678],
        )->create();

        [$motherModel, $fatherModel, $wifeModel, $firstChild, $secondChild, $secondSibling] = $relatives;

        $mother = $pytlewski->mother;
        $this->assertInstanceOf(Relative::class, $mother);
        $this->assertSame('1420', $mother->id);
        $this->assertSame($motherModel->id, $mother->person->id);
        $this->assertSame('Ptakowska', $mother->surname);
        $this->assertSame('Maryanna', $mother->name);

        $father = $pytlewski->father;
        $this->assertInstanceOf(Relative::class, $father);
        $this->assertSame('637', $father->id);
        $this->assertSame($fatherModel->id, $father->person->id);
        $this->assertSame('Pytlewski', $father->surname);
        $this->assertSame('Łukasz', $father->name);

        $this->assertCount(1, $pytlewski->marriages);

        $marriage = $pytlewski->marriages[0];
        $this->assertInstanceOf(Marriage::class, $marriage);
        $this->assertSame('705', $marriage->id);
        $this->assertSame($wifeModel->id, $marriage->person->id);
        $this->assertSame('Frankiewicz, Bronisława', $marriage->name);
        $this->assertSame('29.09.1885', $marriage->date);
        $this->assertSame('Sulmierzyce', $marriage->place);

        $this->assertCount(6, $pytlewski->children);
        $this->assertInstanceOf(Relative::class, $pytlewski->children[0]);

        $child = $pytlewski->children[0];
        $this->assertSame('706', $child->id);
        $this->assertSame($firstChild->id, $child->person->id);
        $this->assertSame('Zygmunt-Stanisław', $child->name);

        $child = $pytlewski->children[1];
        $this->assertSame('707', $child->id);
        $this->assertSame($secondChild->id, $child->person->id);
        $this->assertSame('Seweryn', $child->name);

        $this->assertCount(20, $pytlewski->siblings);
        $this->assertInstanceOf(Relative::class, $pytlewski->siblings[0]);

        $sibling = $pytlewski->siblings[0];
        $this->assertNull($sibling->id);
        $this->assertNull($sibling->person);
        $this->assertSame('Roch-Tomasz', $sibling->name);

        $sibling = $pytlewski->siblings[1];
        $this->assertSame('678', $sibling->id);
        $this->assertSame($secondSibling->id, $sibling->person->id);
        $this->assertSame('Katarzyna', $sibling->name);
    }

    private function testOther(Pytlewski $pytlewski, array $attributes): void
    {
        $mother = $pytlewski->mother;

        if (isset($attributes['mother_surname']) || isset($attributes['mother_name'])) {
            $this->assertInstanceOf(Relative::class, $mother);
            $this->assertSame($attributes['mother_id'], $mother->id);
            $this->assertNull($mother->person);
            $this->assertSame($attributes['mother_surname'], $mother->surname);
            $this->assertSame($attributes['mother_name'], $mother->name);
        } else {
            $this->assertNull($mother);
        }

        $father = $pytlewski->father;

        if (isset($attributes['father_surname']) || isset($attributes['father_name'])) {
            $this->assertInstanceOf(Relative::class, $father);
            $this->assertSame($attributes['father_id'], $father->id);
            $this->assertNull($father->person);
            $this->assertSame($attributes['father_surname'], $father->surname);
            $this->assertSame($attributes['father_name'], $father->name);
        } else {
            $this->assertNull($father);
        }

        foreach ($attributes['marriages'] as $marriageKey => $marriageAttributes) {
            $marriage = $pytlewski->marriages[$marriageKey];

            $this->assertInstanceOf(Marriage::class, $marriage);
            $this->assertNull($marriage->person);

            foreach ($marriageAttributes as $key => $value) {
                $this->assertSame($value, $marriage->{$key});
            }
        }

        foreach ($attributes['children'] as $childKey => $childAttributes) {
            $child = $pytlewski->children[$childKey];

            $this->assertInstanceOf(Relative::class, $child);
            $this->assertNull($child->person);

            foreach ($childAttributes as $key => $value) {
                $this->assertSame($value, $child->{$key});
            }
        }

        foreach ($attributes['siblings'] as $siblingKey => $siblingAttributes) {
            $sibling = $pytlewski->siblings[$siblingKey];

            $this->assertInstanceOf(Relative::class, $sibling);
            $this->assertNull($sibling->person);

            foreach ($siblingAttributes as $key => $value) {
                $this->assertSame($value, $sibling->{$key});
            }
        }
    }
}
