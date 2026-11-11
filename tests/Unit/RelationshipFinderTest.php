<?php

namespace Tests\Unit;

use App\Models\Marriage;
use App\Models\Person;
use App\Services\Relationship\RelationshipFinder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class RelationshipFinderTest extends TestCase
{
    /** @var array<string, Person> */
    private array $family;

    protected function setUp(): void
    {
        parent::setUp();

        $man = fn (?Person $father = null, ?Person $mother = null) => Person::factory()->male()
            ->create(['father_id' => $father?->id, 'mother_id' => $mother?->id]);
        $woman = fn (?Person $father = null, ?Person $mother = null) => Person::factory()->female()
            ->create(['father_id' => $father?->id, 'mother_id' => $mother?->id]);

        $f = [];
        $f['grandfather'] = $man();
        $f['grandmother'] = $woman();
        $f['father'] = $man($f['grandfather'], $f['grandmother']);
        $f['uncle'] = $man($f['grandfather'], $f['grandmother']);
        $f['aunt'] = $woman($f['grandfather'], $f['grandmother']);
        $f['mother'] = $woman();
        $f['me'] = $man($f['father'], $f['mother']);
        $f['sister'] = $woman($f['father'], $f['mother']);
        $f['halfBrother'] = $man($f['father'], $woman());
        $f['nephew'] = $man(null, $f['sister']);
        $f['paternalCousin'] = $man($f['uncle']);
        $f['auntsDaughter'] = $woman(null, $f['aunt']);
        $f['cousinsSon'] = $man($f['paternalCousin']);
        $f['son'] = $man($f['me']);
        $f['wife'] = $woman();
        $f['wifesMother'] = $woman();
        $f['wifesBrother'] = $man(null, $f['wifesMother']);
        $f['wifesSister'] = $woman(null, $f['wifesMother']);
        $f['wifesSistersHusband'] = $man();
        $f['wife']->update(['mother_id' => $f['wifesMother']->id]);
        $f['daughterInLaw'] = $woman();
        $f['stranger'] = $man();

        Marriage::factory()->create(['man_id' => $f['me']->id, 'woman_id' => $f['wife']->id]);
        Marriage::factory()->create(['man_id' => $f['son']->id, 'woman_id' => $f['daughterInLaw']->id]);
        Marriage::factory()->create(['man_id' => $f['wifesSistersHusband']->id, 'woman_id' => $f['wifesSister']->id]);

        $this->family = $f;
    }

    /**
     * @param array<string, int|string|bool> $kinship
     */
    #[DataProvider('relationships')]
    #[TestDox('it names the relationship to $to')]
    public function testKinship(string $to, bool $blood, array $kinship): void
    {
        $relationship = new RelationshipFinder()->find($this->family['me'], $this->family[$to]);

        $this->assertNotNull($relationship);
        $this->assertSame($blood, $relationship->blood);
        $this->assertSame($kinship, array_intersect_key($relationship->kinship, $kinship));
    }

    /**
     * @return array<string, array{string, bool, array<string, int|string|bool>}>
     */
    public static function relationships(): array
    {
        return [
            'father' => ['father', true, ['type' => 'parent', 'sex' => 'xy']],
            'son' => ['son', true, ['type' => 'child', 'sex' => 'xy']],
            'grandmother' => ['grandmother', true, ['type' => 'grandparent', 'sex' => 'xx', 'greats' => 0]],
            'sister' => ['sister', true, ['type' => 'sibling', 'sex' => 'xx', 'half' => false]],
            'half-brother' => ['halfBrother', true, ['type' => 'sibling', 'sex' => 'xy', 'half' => true]],
            'uncle' => ['uncle', true, ['type' => 'uncle_aunt', 'sex' => 'xy', 'greats' => 0, 'side' => 'paternal']],
            'aunt' => ['aunt', true, ['type' => 'uncle_aunt', 'sex' => 'xx']],
            'nephew' => ['nephew', true, ['type' => 'nephew_niece', 'sex' => 'xy', 'greats' => 0, 'via' => 'xx']],
            'paternal cousin' => ['paternalCousin', true, [
                'type' => 'cousin', 'degree' => 1, 'removed' => 0, 'line' => 'stryjeczny',
            ]],
            "aunt's daughter" => ['auntsDaughter', true, [
                'type' => 'cousin', 'sex' => 'xx', 'degree' => 1, 'removed' => 0, 'line' => 'cioteczny',
            ]],
            "cousin's son" => ['cousinsSon', true, ['type' => 'cousin', 'degree' => 1, 'removed' => 1, 'older' => false]],
            'wife' => ['wife', false, ['type' => 'spouse', 'sex' => 'xx']],
            "wife's mother" => ['wifesMother', false, ['type' => 'parent_in_law', 'sex' => 'xx', 'fromSex' => 'xy']],
            "wife's brother" => ['wifesBrother', false, [
                'type' => 'sibling_in_law', 'sex' => 'xy', 'kind' => 'spouse_sibling', 'fromSex' => 'xy',
            ]],
            "wife's sister's husband" => ['wifesSistersHusband', false, [
                'type' => 'co_sibling_in_law', 'sex' => 'xy', 'via' => 'xx', 'fromSex' => 'xy',
            ]],
            "son's wife" => ['daughterInLaw', false, ['type' => 'child_in_law', 'sex' => 'xx']],
        ];
    }

    #[TestDox('it goes through the closest common ancestors')]
    public function testCommonAncestors(): void
    {
        $relationship = new RelationshipFinder()->find($this->family['me'], $this->family['paternalCousin']);

        $this->assertSame(
            [$this->family['me']->id, $this->family['father']->id, $this->family['grandfather']->id, $this->family['uncle']->id, $this->family['paternalCousin']->id],
            array_column($relationship->steps, 'id'),
        );
        $this->assertSame([null, 'parent', 'parent', 'child', 'child'], array_column($relationship->steps, 'step'));
        $this->assertEqualsCanonicalizing(
            [$this->family['grandfather']->id, $this->family['grandmother']->id],
            $relationship->commonAncestors,
        );
    }

    #[TestDox("it doesn't treat parents of the same child as blood relatives")]
    public function testCoParents(): void
    {
        $relationship = new RelationshipFinder()->find($this->family['father'], $this->family['mother']);

        $this->assertFalse($relationship->blood);
        $this->assertSame('in_law', $relationship->kinship['type']);
    }

    #[TestDox('it returns null for unrelated people')]
    public function testUnrelated(): void
    {
        $this->assertNull(new RelationshipFinder()->find($this->family['me'], $this->family['stranger']));
    }
}
