<?php

namespace Tests\Feature\Marriages;

use App\Models\Activity;
use App\Models\Marriage;
use App\Models\Person;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class CreateMarriageTest extends TestCase
{
    /** @var list<string> */
    private array $dates = [
        'first_event_date_from', 'second_event_date_from', 'divorce_date_from',
        'first_event_date_to', 'second_event_date_to', 'divorce_date_to',
    ];

    /** @var list<string> */
    private array $enums = ['rite', 'first_event_type', 'second_event_type'];

    /** @var array<string, mixed> */
    private array $validAttributes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validAttributes = [
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
            'divorced' => true,
            'divorce_date_from' => '2001-10-27',
            'divorce_date_to' => '2001-10-27',
            'divorce_place' => 'Toruń, Polska',
        ];
    }

    #[TestDox('guest are asked to log in when attempting to view add marriage form')]
    public function testFormGuest(): void
    {
        $this->get('marriages/create')
            ->assertFound()
            ->assertRedirect('login');
    }

    #[TestDox('users without permissions cannot view add marriage form')]
    public function testFormPermissions(): void
    {
        $this->withPermissions(1)
            ->get('marriages/create')
            ->assertForbidden();
    }

    #[TestDox('users with permissions can view add marriage form')]
    public function testForm(): void
    {
        $this->withPermissions(2)
            ->get('marriages/create')
            ->assertOk();
    }

    #[TestDox('guest cannot add valid marriage')]
    public function testGuest(): void
    {
        $count = Marriage::count();

        $this->post('marriages', $this->validAttributes)
            ->assertFound()
            ->assertRedirect('login');

        $this->assertSame($count, Marriage::count());
    }

    #[TestDox('users without permissions cannot add valid marriage')]
    public function testPermissions(): void
    {
        $count = Marriage::count();

        $this->withPermissions(1)
            ->post('marriages', $this->validAttributes)
            ->assertForbidden();

        $this->assertSame($count, Marriage::count());
    }

    #[TestDox('users with permissions can add valid marriage')]
    public function testOk(): void
    {
        $count = Marriage::count();

        $this->travel(5)->minutes();

        $this->withPermissions(2)
            ->post('marriages', $this->validAttributes)
            ->assertFound()
            ->assertRedirect('people/' . Marriage::latest()->first()->woman_id);

        $this->travelBack();

        $this->assertSame($count + 1, Marriage::count());

        $marriage = Marriage::latest()->first();

        $attributesToCheck = Arr::except($this->validAttributes, array_merge($this->dates, $this->enums));

        foreach ($attributesToCheck as $key => $attribute) {
            $this->assertSame($attribute, $marriage->{$key});
        }

        foreach ($this->enums as $enum) {
            $this->assertSame($this->validAttributes[$enum], $marriage->{$enum}?->value);
        }

        foreach ($this->dates as $date) {
            $this->assertSame($this->validAttributes[$date], $marriage->{$date}->toDateString());
        }
    }

    #[TestDox('user can pass spouse id to form by get request parameters')]
    public function testSpouseParam(): void
    {
        $woman = Person::factory()->female()->create();
        $man = Person::factory()->male()->create();

        $this->withPermissions(2)
            ->get("marriages/create?woman={$woman->id}&man={$man->id}")
            ->assertOk()
            ->assertSee((string) $woman->id)
            ->assertSee((string) $man->id);
    }

    #[TestDox('data is validated using appropriate form request')]
    public function testFormRequest(): void
    {
        $this->assertActionUsesFormRequest(
            [\App\Http\Controllers\MarriageController::class, 'store'],
            \App\Http\Requests\CreateMarriage::class,
        );
    }

    #[TestDox('marriage creation is logged')]
    public function testLogging(): void
    {
        $count = Marriage::count();

        $this->travel(5)->minutes();

        $this->withPermissions(2)
            ->post('marriages', $this->validAttributes);

        $this->travelBack();

        $this->assertSame($count + 1, Marriage::count());

        $marriage = Marriage::latest()->first();

        $log = Activity::newest();
        $this->assertSame('marriages', $log->log_name);
        $this->assertSame('created', $log->description);
        $this->assertSameModel($marriage, $log->subject);

        $attributesToCheck = Arr::except($this->validAttributes, $this->dates);

        foreach ($attributesToCheck as $key => $value) {
            $m = "Failed asserting that attribute {$key} has the same value in log.";

            $this->assertSame($value, $log->properties['attributes'][$key], $m);
        }

        $this->assertSame((string) $marriage->created_at, (string) $log->created_at);
        $this->assertSame((string) $marriage->updated_at, (string) $log->created_at);

        $this->assertDoesNotHaveKeys(['created_at', 'updated_at'], $log->properties['attributes']);

        foreach ($this->dates as $date) {
            $this->assertSame($marriage->{$date}->format('Y-m-d'), $log->properties['attributes'][$date]);
        }
    }
}
