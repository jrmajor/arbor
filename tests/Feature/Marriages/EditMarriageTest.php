<?php

namespace Tests\Feature\Marriages;

use App\Models\Activity;
use App\Models\Marriage;
use App\Models\Person;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class EditMarriageTest extends TestCase
{
    private Marriage $marriage;

    /** @var list<string> */
    private array $dates = [
        'first_event_date_from', 'second_event_date_from', 'divorce_date_from',
        'first_event_date_to', 'second_event_date_to', 'divorce_date_to',
    ];

    /** @var list<string> */
    private array $enums = ['rite', 'first_event_type', 'second_event_type'];

    /** @var array<string, mixed> */
    private array $oldAttributes;

    /** @var array<string, mixed> */
    private array $newAttributes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->oldAttributes = [
            'woman_id' => Person::factory()->female()->create()->id,
            'woman_order' => 1,
            'man_id' => Person::factory()->male()->create()->id,
            'man_order' => 2,
            'rite' => 'roman_catholic',
            'first_event_type' => 'civil_marriage',
            'first_event_date_from' => '1968-04-14',
            'first_event_date_to' => '1968-04-14',
            'first_event_place' => 'Sępólno Krajeńskie, Polska',
            'second_event_type' => 'church_marriage',
            'second_event_date_from' => '1968-04-13',
            'second_event_date_to' => '1968-04-13',
            'second_event_place' => 'Sępólno Krajeńskie, Polska',
            'divorced' => false,
            'divorce_date_from' => null,
            'divorce_date_to' => null,
            'divorce_place' => null,
        ];

        $this->newAttributes = [
            'woman_order' => 2,
            'man_order' => 1,
            'rite' => 'civil',
            'first_event_type' => 'concordat_marriage',
            'first_event_date_from' => '1960-09-02',
            'first_event_date_to' => '1968-04-14',
            'first_event_place' => 'Warszawa, Polska',
            'second_event_type' => 'civil_marriage',
            'second_event_date_from' => '1960-09-05',
            'second_event_date_to' => '1960-09-05',
            'second_event_place' => 'Warszawa, Polska',
            'divorced' => true,
            'divorce_date_from' => '2001-10-27',
            'divorce_date_to' => '2001-10-27',
            'divorce_place' => 'Toruń, Polska',
        ];

        $this->marriage = Marriage::factory()->create($this->oldAttributes);
    }

    #[TestDox('guests are asked to log in when attempting to view edit marriage form')]
    public function testFormGuest(): void
    {
        $this->get("marriages/{$this->marriage->id}/edit")
            ->assertFound()
            ->assertRedirect('login');
    }

    #[TestDox('guests are asked to log in when attempting to view edit form for nonexistent marriage')]
    public function testFormGuestNonexistent(): void
    {
        $this->get('marriages/2137/edit')
            ->assertFound()
            ->assertRedirect('login');
    }

    #[TestDox('users without permissions cannot view edit marriage form')]
    public function testFormPermissions(): void
    {
        $this->withPermissions(1)
            ->get("marriages/{$this->marriage->id}/edit")
            ->assertForbidden();
    }

    #[TestDox('users with permissions can view edit marriage form')]
    public function testForm(): void
    {
        $this->withPermissions(2)
            ->get("marriages/{$this->marriage->id}/edit")
            ->assertOk();
    }

    #[TestDox('guests cannot edit marriage')]
    public function testGuest(): void
    {
        $this->put("marriages/{$this->marriage->id}", $this->newAttributes)
            ->assertFound()
            ->assertRedirect('login');

        $this->marriage->refresh();

        $attributesToCheck = Arr::except($this->oldAttributes, array_merge($this->dates, $this->enums));

        foreach ($attributesToCheck as $key => $attribute) {
            $this->assertSame($attribute, $this->marriage->{$key});
        }
    }

    #[TestDox('users without permissions cannot edit marriage')]
    public function testPermissions(): void
    {
        $this->withPermissions(1)
            ->put("marriages/{$this->marriage->id}", $this->newAttributes)
            ->assertForbidden();

        $this->marriage->refresh();

        $attributesToCheck = Arr::except($this->oldAttributes, array_merge($this->dates, $this->enums));

        foreach ($attributesToCheck as $key => $attribute) {
            $this->assertSame($attribute, $this->marriage->{$key});
        }
    }

    #[TestDox('users with permissions can edit marriage')]
    public function testOk(): void
    {
        $response = $this->withPermissions(2)
            ->put("marriages/{$this->marriage->id}", $this->newAttributes)
            ->assertFound();

        $this->marriage->refresh();

        $response->assertRedirect('people/' . $this->marriage->woman_id);

        $attributesToCheck = Arr::except($this->newAttributes, array_merge($this->dates, $this->enums));

        foreach ($attributesToCheck as $key => $attribute) {
            $this->assertSame($attribute, $this->marriage->{$key});
        }

        foreach ($this->enums as $enum) {
            $this->assertSame($this->newAttributes[$enum], $this->marriage->{$enum}?->value);
        }

        foreach ($this->dates as $date) {
            $this->assertSame($this->newAttributes[$date], $this->marriage->{$date}->toDateString());
        }
    }

    #[TestDox('data is validated using appropriate form request')]
    public function testFormRequest(): void
    {
        $this->assertActionUsesFormRequest(
            [\App\Http\Controllers\MarriageController::class, 'update'],
            \App\Http\Requests\EditMarriage::class,
        );
    }

    #[TestDox('marriage edition is logged')]
    public function testLogging(): void
    {
        $this->travel(5)->minutes();

        $this->withPermissions(2)
            ->put("marriages/{$this->marriage->id}", $this->newAttributes);

        $this->travelBack();

        $this->marriage->refresh();

        $log = Activity::newest();
        $this->assertSame('marriages', $log->log_name);
        $this->assertSame('updated', $log->description);
        $this->assertSameModel($this->marriage, $log->subject);

        $oldToCheck = Arr::except($this->oldAttributes, ['man_id', 'woman_id', ...$this->dates]);

        foreach ($oldToCheck as $key => $value) {
            $m = "Failed asserting that old attribute {$key} has the same value in log.";

            $this->assertSame($value, $log->properties['old'][$key], $m);
        }

        $newToCheck = Arr::except($this->newAttributes, ['man_id', 'woman_id', ...$this->dates]);

        foreach ($newToCheck as $key => $value) {
            $m = "Failed asserting that attribute {$key} has the same value in log.";

            $this->assertSame($value, $log->properties['attributes'][$key], $m);
        }

        $this->assertDoesNotHaveKeys(['man_id', 'woman_id', 'created_at', 'updated_at'], $log->properties['old']);
        $this->assertDoesNotHaveKeys(['man_id', 'woman_id', 'created_at', 'updated_at'], $log->properties['attributes']);

        $this->assertSame((string) $this->marriage->updated_at, (string) $log->created_at);

        foreach ($this->dates as $date) {
            $this->assertSame($this->oldAttributes[$date], $log->properties['old'][$date]);
            $this->assertSame($this->newAttributes[$date], $log->properties['attributes'][$date]);
        }
    }
}
