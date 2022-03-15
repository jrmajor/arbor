<?php

namespace Tests\Feature\People;

use App\Enums\Sex;
use App\Models\Person;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

use function Pest\Laravel\get;
use function Pest\Laravel\put;
use function Tests\latestLog;

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
        get("people/{$this->person->id}/edit")
            ->assertStatus(302)
            ->assertRedirect('login');
    }

    #[TestDox('guests are asked to log in when attempting to view edit form for nonexistent person')]
    public function testFormGuestNonexistent(): void
    {
        $this->get('people/2137/edit')
            ->assertStatus(302)
            ->assertRedirect('login');
    }

    #[TestDox('users without permissions cannot view edit person form')]
    public function testFormPermissions(): void
    {
        $this->withPermissions(1)
            ->get("people/{$this->person->id}/edit")
            ->assertStatus(403);
    }

    #[TestDox('users with permissions can view edit person form')]
    public function testForm(): void
    {
        $this->withPermissions(2)
            ->get("people/{$this->person->id}/edit")
            ->assertStatus(200);
    }

    #[TestDox('guests cannot edit person')]
    public function testGuest(): void
    {
        put("people/{$this->person->id}", $this->newAttributes)
            ->assertStatus(302)
            ->assertRedirect('login');

        $this->person->refresh();

        $attributesToCheck = Arr::except($this->oldAttributes, [
            'sex', 'sources', ...$this->dates,
        ]);

        foreach ($attributesToCheck as $key => $attribute) {
            expect($this->person->{$key})->toBe($attribute);
        }
    }

    #[TestDox('users without permissions cannot edit person')]
    public function testPermissions(): void
    {
        $this->withPermissions(1)
            ->put("people/{$this->person->id}", $this->newAttributes)
            ->assertStatus(403);

        $this->person->refresh();

        $attributesToCheck = Arr::except($this->oldAttributes, [
            'sex', 'sources', ...$this->dates,
        ]);

        foreach ($attributesToCheck as $key => $attribute) {
            expect($this->person->{$key})->toBe($attribute);
        }
    }

    #[TestDox('users with permissions can edit person')]
    public function testOk(): void
    {
        $this->withPermissions(2)
            ->put("people/{$this->person->id}", $this->newAttributes)
            ->assertStatus(302)
            ->assertRedirect("people/{$this->person->id}");

        $this->person->refresh();

        $attributesToCheck = Arr::except($this->newAttributes, [
            'sex', 'sources', ...$this->dates,
        ]);

        foreach ($attributesToCheck as $key => $attribute) {
            expect($this->person->{$key})->toBe($attribute);
        }

        expect($this->person->sex)->toBe(Sex::from($this->newAttributes['sex']));

        expect($this->person->sources)->toHaveCount(2);
        expect($this->person->sources->map->raw()->all())
            ->toBe($this->newAttributes['sources']);

        foreach ($this->dates as $date) {
            expect($this->person->{$date}->toDateString())->toBe($this->newAttributes[$date]);
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

        expect($log = latestLog())
            ->log_name->toBe('people')
            ->description->toBe('updated')
            ->subject->toBeModel($this->person);

        $oldToCheck = Arr::except($this->oldAttributes, [
            'dead', 'death_cause', 'sources', ...$this->dates,
        ]);

        foreach ($oldToCheck as $key => $value) {
            expect($log->properties['old'][$key])->toBe($value);
            // 'Failed asserting that old attribute '.$key.' has the same value in log.'
        }

        $newToCheck = Arr::except($this->newAttributes, [
            'dead', 'death_cause', 'sources', ...$this->dates,
        ]);

        foreach ($newToCheck as $key => $value) {
            expect($log->properties['attributes'][$key])->toBe($value);
            // 'Failed asserting that attribute '.$key.' has the same value in log.'
        }

        expect($log->properties['old'])
            ->not->toHaveKeys(['dead', 'death_cause', 'created_at', 'updated_at']);

        expect($log->properties['attributes'])
            ->not->toHaveKeys(['dead', 'death_cause', 'created_at', 'updated_at']);

        expect((string) $log->created_at)->toBe((string) $this->person->updated_at);

        expect($log->properties['old']['sources'])->toBe($this->oldAttributes['sources']);
        expect($log->properties['attributes']['sources'])->toBe($this->newAttributes['sources']);

        foreach ($this->dates as $date) {
            expect($log->properties['old'][$date])->toBe($this->oldAttributes[$date]);
            expect($log->properties['attributes'][$date])->toBe($this->newAttributes[$date]);
        }
    }
}
