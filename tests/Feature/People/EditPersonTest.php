<?php

namespace Tests\Feature\People;

use App\Enums\Sex;
use App\Models\Activity;
use App\Models\Person;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class EditPersonTest extends TestCase
{
    private Person $person;

    /** @var list<string> */
    private array $dates = [
        'birth_date_from', 'death_date_from', 'funeral_date_from', 'burial_date_from',
        'birth_date_to', 'death_date_to', 'funeral_date_to', 'burial_date_to',
    ];

    /** @var array<string, mixed> */
    private array $oldAttributes;

    /** @var array<string, mixed> */
    private array $newAttributes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->oldAttributes = [
            'id_wielcy' => 'psb.6305.3',
            'id_pytlewski' => 2115,
            'sex' => 'xx',
            'name' => 'Maria',
            'middle_name' => 'Henryka',
            'family_name' => 'Stecher de Sebenitz',
            'last_name' => 'Gąsiorowska',
            'birth_date_from' => '1854-01-12',
            'birth_date_to' => '1854-01-12',
            'birth_place' => 'Zaleszczyki, Ukraina',
            'dead' => true,
            'death_date_from' => '1918-01-02',
            'death_date_to' => '1918-01-02',
            'death_place' => 'Załuż k/Sanoka, Polska',
            'death_cause' => 'rak',
            'funeral_date_from' => '1918-01-05',
            'funeral_date_to' => '1918-01-05',
            'funeral_place' => 'Załuż k/Sanoka, Polska',
            'burial_date_from' => '1918-01-05',
            'burial_date_to' => '1918-01-05',
            'burial_place' => 'Załuż k/Sanoka, Polska',
            'sources' => [
                '[Henryk Gąsiorowski](https://pl.wikipedia.org/wiki/Henryk_G%C4%85siorowski) w Wikipedii, wolnej encyklopedii, dostęp 2020-06-06',
                'Ignacy Płażewski, *Spojrzenie w przeszłość polskiej fotografii*, Warszawa, PIW, 1982, ISBN 83-06-00100-1',
            ],
        ];

        $this->newAttributes = [
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
            'sources' => [
                'Witold Jakóbczyk, *Czesław Czypicki* w Wielkopolskim słowniku biograficznym, Warszawa-Poznań, PWN, 1981, ISBN 83-01-02722-3',
                'Ignacy Płażewski, *Spojrzenie w przeszłość polskiej fotografii*, Warszawa, PIW, 1982, ISBN 83-06-00100-1',
            ],
        ];

        $this->person = Person::factory()->create($this->oldAttributes);
    }

    #[TestDox('guests are asked to log in when attempting to view edit person form')]
    public function testFormGuest(): void
    {
        $this->get("people/{$this->person->id}/edit")
            ->assertFound()
            ->assertRedirect('login');
    }

    #[TestDox('guests are asked to log in when attempting to view edit form for nonexistent person')]
    public function testFormGuestNonexistent(): void
    {
        $this->get('people/2137/edit')
            ->assertFound()
            ->assertRedirect('login');
    }

    #[TestDox('users without permissions cannot view edit person form')]
    public function testFormPermissions(): void
    {
        $this->withPermissions(1)
            ->get("people/{$this->person->id}/edit")
            ->assertForbidden();
    }

    #[TestDox('users with permissions can view edit person form')]
    public function testForm(): void
    {
        Http::fake();

        $this->withPermissions(2)
            ->get("people/{$this->person->id}/edit")
            ->assertOk();
    }

    #[TestDox('guests cannot edit person')]
    public function testGuest(): void
    {
        $this->put("people/{$this->person->id}", $this->newAttributes)
            ->assertFound()
            ->assertRedirect('login');

        $this->person->refresh();

        $attributesToCheck = Arr::except($this->oldAttributes, [
            'sex', 'sources', ...$this->dates,
        ]);

        foreach ($attributesToCheck as $key => $attribute) {
            $this->assertSame($attribute, $this->person->{$key});
        }
    }

    #[TestDox('users without permissions cannot edit person')]
    public function testPermissions(): void
    {
        $this->withPermissions(1)
            ->put("people/{$this->person->id}", $this->newAttributes)
            ->assertForbidden();

        $this->person->refresh();

        $attributesToCheck = Arr::except($this->oldAttributes, [
            'sex', 'sources', ...$this->dates,
        ]);

        foreach ($attributesToCheck as $key => $attribute) {
            $this->assertSame($attribute, $this->person->{$key});
        }
    }

    #[TestDox('users with permissions can edit person')]
    public function testOk(): void
    {
        $this->withPermissions(2)
            ->put("people/{$this->person->id}", $this->newAttributes)
            ->assertFound()
            ->assertRedirect("people/{$this->person->id}");

        $this->person->refresh();

        $attributesToCheck = Arr::except($this->newAttributes, [
            'sex', 'sources', ...$this->dates,
        ]);

        foreach ($attributesToCheck as $key => $attribute) {
            $this->assertSame($attribute, $this->person->{$key});
        }

        $this->assertSame(Sex::from($this->newAttributes['sex']), $this->person->sex);

        $this->assertCount(2, $this->person->sources);
        $this->assertSame(
            $this->newAttributes['sources'],
            $this->person->sources->map->raw()->all(),
        );

        foreach ($this->dates as $date) {
            $this->assertSame($this->newAttributes[$date], $this->person->{$date}->toDateString());
        }
    }

    #[TestDox('data is validated using appropriate form request')]
    public function testFormRequest(): void
    {
        $this->assertActionUsesFormRequest(
            [\App\Http\Controllers\PersonController::class, 'update'],
            \App\Http\Requests\StorePerson::class,
        );
    }

    #[TestDox('person edition is logged')]
    public function testLogging(): void
    {
        $this->travel(5)->minutes();

        $this->withPermissions(2)
            ->put("people/{$this->person->id}", $this->newAttributes);

        $this->travelBack();

        $this->person->refresh();

        $log = Activity::newest();
        $this->assertSame('people', $log->log_name);
        $this->assertSame('updated', $log->description);
        $this->assertSameModel($this->person, $log->subject);

        $oldToCheck = Arr::except($this->oldAttributes, [
            'dead', 'death_cause', 'sources', ...$this->dates,
        ]);

        foreach ($oldToCheck as $key => $value) {
            $m = "Failed asserting that old attribute {$key} has the same value in log.";

            $this->assertSame($value, $log->properties['old'][$key], $m);
        }

        $newToCheck = Arr::except($this->newAttributes, [
            'dead', 'death_cause', 'sources', ...$this->dates,
        ]);

        foreach ($newToCheck as $key => $value) {
            $m = "Failed asserting that attribute {$key} has the same value in log.";

            $this->assertSame($value, $log->properties['attributes'][$key], $m);
        }

        $this->assertDoesNotHaveKeys(
            ['dead', 'death_cause', 'created_at', 'updated_at'],
            $log->properties['old'],
        );

        $this->assertDoesNotHaveKeys(
            ['dead', 'death_cause', 'created_at', 'updated_at'],
            $log->properties['attributes'],
        );

        $this->assertSame((string) $this->person->updated_at, (string) $log->created_at);

        $this->assertSame($this->oldAttributes['sources'], $log->properties['old']['sources']);
        $this->assertSame($this->newAttributes['sources'], $log->properties['attributes']['sources']);

        foreach ($this->dates as $date) {
            $this->assertSame($this->oldAttributes[$date], $log->properties['old'][$date]);
            $this->assertSame($this->newAttributes[$date], $log->properties['attributes'][$date]);
        }
    }
}
