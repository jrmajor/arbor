<?php

namespace Tests\Feature\People;

use App\Enums\Sex;
use App\Models\Activity;
use App\Models\Person;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class CreatePersonTest extends TestCase
{
    /** @var list<string> */
    private array $dates = [
        'birth_date_from', 'death_date_from', 'funeral_date_from', 'burial_date_from',
        'birth_date_to', 'death_date_to', 'funeral_date_to', 'burial_date_to',
    ];

    /** @var array<string, mixed> */
    private array $validAttributes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validAttributes = [
            'id_wielcy' => 'psb.6305.1',
            'id_pytlewski' => 2137,
            'sex' => 'xy',
            'name' => 'Henryk',
            'middle_name' => 'Erazm',
            'family_name' => 'Gąsiorowski',
            'last_name' => 'Jakże to',
            'birth_date_from' => '1878-04-01',
            'birth_date_to' => '1878-04-01',
            'birth_place' => 'Zaleszczyki, Polska',
            'dead' => true,
            'death_date_from' => '1947-01-17',
            'death_date_to' => '1947-01-17',
            'death_place' => 'Grudziądz, Polska',
            'death_cause' => 'rak',
            'funeral_date_from' => '1947-01-21',
            'funeral_date_to' => '1947-01-21',
            'funeral_place' => 'Grudziądz, Polska',
            'burial_date_from' => '1947-01-21',
            'burial_date_to' => '1947-01-21',
            'burial_place' => 'Grudziądz, Polska',
            'mother_id' => Person::factory()->female()->create()->id,
            'father_id' => Person::factory()->male()->create()->id,
            'sources' => [
                '[Henryk Gąsiorowski](https://pl.wikipedia.org/wiki/Henryk_G%C4%85siorowski) w Wikipedii, wolnej encyklopedii, dostęp 2020-06-06',
                'Ignacy Płażewski, *Spojrzenie w przeszłość polskiej fotografii*, Warszawa, PIW, 1982, ISBN 83-06-00100-1',
            ],
        ];
    }

    #[TestDox('guest are asked to log in when attempting to view add person form')]
    public function testFormGuest(): void
    {
        $this->get('people/create')
            ->assertFound()
            ->assertRedirect('login');
    }

    #[TestDox('users without permissions cannot view add person form')]
    public function testFormPermissions(): void
    {
        $this->withPermissions(1)
            ->get('people/create')
            ->assertForbidden();
    }

    #[TestDox('users with permissions can view add person form')]
    public function testForm(): void
    {
        $this->withPermissions(2)
            ->get('people/create')
            ->assertOk();
    }

    #[TestDox('guest cannot add valid person')]
    public function testGuest(): void
    {
        $count = Person::count();

        $this->post('people', $this->validAttributes)
            ->assertFound()
            ->assertRedirect('login');

        $this->assertSame($count, Person::count());
    }

    #[TestDox('users without permissions cannot add valid person')]
    public function testPermissions(): void
    {
        $count = Person::count();

        $this->withPermissions(1)
            ->post('people', $this->validAttributes)
            ->assertForbidden();

        $this->assertSame($count, Person::count());
    }

    #[TestDox('users with permissions can add valid person')]
    public function testOk(): void
    {
        $count = Person::count();

        $this->travel(5)->minutes();

        $this->withPermissions(2)
            ->post('people', $this->validAttributes)
            ->assertFound()
            ->assertRedirect('people/' . Person::latest()->first()->id);

        $this->travelBack();

        $this->assertSame($count + 1, Person::count());

        $person = Person::latest()->first();

        $attributesToCheck = Arr::except($this->validAttributes, [
            'sex', 'sources', ...$this->dates,
        ]);

        foreach ($attributesToCheck as $key => $attribute) {
            $this->assertSame($attribute, $person->{$key});
        }

        $this->assertSame(Sex::from($this->validAttributes['sex']), $person->sex);

        $this->assertCount(2, $person->sources);
        $this->assertSame(
            $this->validAttributes['sources'],
            $person->sources->map->raw()->all(),
        );

        foreach ($this->dates as $date) {
            $this->assertSame($this->validAttributes[$date], $person->{$date}->toDateString());
        }
    }

    #[TestDox('user can pass parent ids to form by get request parameters')]
    public function testParentParams(): void
    {
        $mother = Person::factory()->female()->create();
        $father = Person::factory()->male()->create();

        $this->withPermissions(2)
            ->get("people/create?mother={$mother->id}&father={$father->id}")
            ->assertOk()
            ->assertSee((string) $mother->id)
            ->assertSee((string) $father->id);
    }

    #[TestDox('data is validated using appropriate form request')]
    public function testFormRequest(): void
    {
        $this->assertActionUsesFormRequest(
            [\App\Http\Controllers\PersonController::class, 'store'],
            \App\Http\Requests\StorePerson::class,
        );
    }

    #[TestDox('person creation is logged')]
    public function testLogging(): void
    {
        $count = Person::count();

        $this->travel(5)->minutes();

        $this->withPermissions(2)
            ->post('people', $this->validAttributes);

        $this->travelBack();

        $this->assertSame($count + 1, Person::count());

        $person = Person::latest()->first();

        $log = Activity::newest();
        $this->assertSame('people', $log->log_name);
        $this->assertSame('created', $log->description);
        $this->assertSameModel($person, $log->subject);

        $attributesToCheck = Arr::except($this->validAttributes, [
            'sources', ...$this->dates,
        ]);

        foreach ($attributesToCheck as $key => $value) {
            $m = "Failed asserting that attribute {$key} has the same value in log.";

            $this->assertSame($value, $log->properties['attributes'][$key], $m);
        }

        $this->assertSame((string) $person->created_at, (string) $log->created_at);
        $this->assertSame((string) $person->updated_at, (string) $log->created_at);

        $this->assertDoesNotHaveKeys(['created_at', 'updated_at'], $log->properties['attributes']);

        $this->assertSame($this->validAttributes['sources'], $log->properties['attributes']['sources']);

        foreach ($this->dates as $date) {
            $this->assertSame($person->{$date}->format('Y-m-d'), $log->properties['attributes'][$date]);
        }
    }
}
