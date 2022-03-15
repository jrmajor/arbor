<?php

namespace Tests\Feature\Marriages;

use App\Models\Marriage;
use App\Models\Person;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

use function Pest\Laravel\post;
use function Tests\latestLog;

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
            ->assertStatus(302)
            ->assertRedirect('login');
    }

    #[TestDox('users without permissions cannot view add marriage form')]
    public function testFormPermissions(): void
    {
        $this->withPermissions(1)
            ->get('marriages/create')
            ->assertStatus(403);
    }

    #[TestDox('users with permissions can view add marriage form')]
    public function testForm(): void
    {
        $this->withPermissions(2)
            ->get('marriages/create')
            ->assertStatus(200);
    }

    #[TestDox('guest cannot add valid marriage')]
    public function testGuest(): void
    {
        $count = Marriage::count();

        post('marriages', $this->validAttributes)
            ->assertStatus(302)
            ->assertRedirect('login');

        expect(Marriage::count())->toBe($count);
    }

    #[TestDox('users without permissions cannot add valid marriage')]
    public function testPermissions(): void
    {
        $count = Marriage::count();

        $this->withPermissions(1)
            ->post('marriages', $this->validAttributes)
            ->assertStatus(403);

        expect(Marriage::count())->toBe($count);
    }

    #[TestDox('users with permissions can add valid marriage')]
    public function testOk(): void
    {
        $count = Marriage::count();

        $this->travel(5)->minutes();

        $this->withPermissions(2)
            ->post('marriages', $this->validAttributes)
            ->assertStatus(302)
            ->assertRedirect('people/' . Marriage::latest()->first()->woman_id);

        $this->travelBack();

        expect(Marriage::count())->toBe($count + 1);

        $marriage = Marriage::latest()->first();

        $attributesToCheck = Arr::except($this->validAttributes, array_merge($this->dates, $this->enums));

        foreach ($attributesToCheck as $key => $attribute) {
            expect($marriage->{$key})->toBe($attribute);
        }

        foreach ($this->enums as $enum) {
            expect($marriage->{$enum}?->value)->toBe($this->validAttributes[$enum]);
        }

        foreach ($this->dates as $date) {
            expect($marriage->{$date}->toDateString())->toBe($this->validAttributes[$date]);
        }
    }

    #[TestDox('user can pass spouse id to form by get request parameters')]
    public function testSpouseParam(): void
    {
        $woman = Person::factory()->female()->create();
        $man = Person::factory()->male()->create();

        $this->withPermissions(2)
            ->get("marriages/create?woman={$woman->id}&man={$man->id}")
            ->assertStatus(200)
            ->assertSee((string) $woman->id)
            ->assertSee((string) $man->id);
    }

    #[TestDox('data is validated using appropriate form request')]
    public function testFormRequest(): void
    {
        $this->assertActionUsesFormRequest(
            [\App\Http\Controllers\MarriageController::class, 'store'],
            \App\Http\Requests\StoreMarriage::class,
        );
    }

    #[TestDox('marriage creation is logged')]
    public function testLogging(): void
    {
        $count = Marriage::count();

        $this->travel('+5 minutes');

        $this->withPermissions(2)
            ->post('marriages', $this->validAttributes);

        $this->travel('back');

        expect(Marriage::count())->toBe($count + 1);

        $marriage = Marriage::latest()->first();

        expect($log = latestLog())
            ->log_name->toBe('marriages')
            ->description->toBe('created')
            ->subject->toBeModel($marriage);

        $attributesToCheck = Arr::except($this->validAttributes, $this->dates);

        foreach ($attributesToCheck as $key => $value) {
            expect($log->properties['attributes'][$key])->toBe($value);
            // 'Failed asserting that attribute '.$key.' has the same value in log.'
        }

        expect((string) $log->created_at)
            ->toBe((string) $marriage->created_at)
            ->toBe((string) $marriage->updated_at);

        expect($log->properties['attributes'])->not->toHaveKeys(['created_at', 'updated_at']);

        foreach ($this->dates as $date) {
            expect($log->properties['attributes'][$date])->toBe($marriage->{$date}->format('Y-m-d'));
        }
    }
}
