<?php

namespace Tests\Feature\Marriages;

use App\Marriage;
use App\Person;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class CreateMarriageTest extends TestCase
{
    use RefreshDatabase;

    private function validAttributes($overrides = [])
    {
        return array_merge([
            'woman_order' => 1,
            'man_order' => 2,
            'rite' => 'roman_catholic',
            'first_event_type' => 'civil_marriage',
            'first_event_date_from' => '1968-04-14',
            'first_event_place' => 'Sępólno Krajeńskie, Polska',
            'second_event_type' => 'church_marriage',
            'second_event_date_from' => '1968-04-13',
            'second_event_place' => 'Sępólno Krajeńskie, Polska',
            'ended' => '1',
            'end_cause' => 'rozwód',
            'end_date_from' => '2001-10-27',
        ], $overrides);
    }

    public function testGuestAreAskedToLogInWhenAttemptingToViewAddMarriageForm()
    {
        $response = $this->get('marriages/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function testUsersWithoutPermissionsCannotViewAddMarriageForm()
    {
        $user = factory(User::class)->create([
            'permissions' => 1,
        ]);

        $response = $this->actingAs($user)->get('marriages/create');

        $response->assertStatus(403);
    }

    public function testUsersWithPermissionsCanViewAddMarriageForm()
    {
        $user = factory(User::class)->create([
            'permissions' => 2,
        ]);

        $response = $this->actingAs($user)->get('marriages/create');

        $response->assertStatus(200);
    }

    public function testGuestCannotAddValidMarriage()
    {
        $woman = factory(Person::class)->state('woman')->create();
        $man = factory(Person::class)->state('man')->create();

        $validAttributes = $this->validAttributes([
            'woman_id' => $woman->id,
            'man_id' => $man->id,
        ]);

        $response = $this->post('marriages', $validAttributes);

        $response->assertStatus(302);
        $response->assertRedirect('login');
        $this->assertEquals(0, Marriage::count());
    }

    public function testUsersWithoutPermissionsCannotAddValidMarriage()
    {
        $user = factory(User::class)->create([
            'permissions' => 1,
        ]);

        $woman = factory(Person::class)->state('woman')->create();
        $man = factory(Person::class)->state('man')->create();

        $validAttributes = $this->validAttributes([
            'woman_id' => $woman->id,
            'man_id' => $man->id,
        ]);

        $response = $this->actingAs($user)->post('marriages', $validAttributes);

        $response->assertStatus(403);
        $this->assertEquals(0, Marriage::count());
    }

    public function testUsersWithPermissionsCanAddValidMarriage()
    {
        $user = factory(User::class)->create([
            'permissions' => 2,
        ]);

        $woman = factory(Person::class)->state('woman')->create();
        $man = factory(Person::class)->state('man')->create();

        $validAttributes = $this->validAttributes([
            'woman_id' => $woman->id,
            'man_id' => $man->id,
        ]);

        $response = $this->actingAs($user)->post('marriages', $validAttributes);

        $response->assertStatus(302);

        $marriage = Marriage::orderBy('id', 'desc')->first();

        $response->assertRedirect("people/$marriage->woman_id");
        $this->assertEquals(1, Marriage::count());

        $attributesToCheck = Arr::except($validAttributes, [
            'first_event_date_from', 'second_event_date_from', 'end_date_from',
        ]);

        foreach ($attributesToCheck as $key => $attribute) {
            $this->assertEquals($attribute, $marriage->fresh()[$key]);
        }

        $this->assertTrue($validAttributes['first_event_date_from'] == $marriage->fresh()['first_event_date_from']->toDateString());
        $this->assertTrue($validAttributes['first_event_date_from'] == $marriage->fresh()['first_event_date_to']->toDateString());

        $this->assertTrue($validAttributes['second_event_date_from'] == $marriage->fresh()['second_event_date_from']->toDateString());
        $this->assertTrue($validAttributes['second_event_date_from'] == $marriage->fresh()['second_event_date_to']->toDateString());

        $this->assertTrue($validAttributes['end_date_from'] == $marriage->fresh()['end_date_from']->toDateString());
        $this->assertTrue($validAttributes['end_date_from'] == $marriage->fresh()['end_date_to']->toDateString());
    }

    public function testYouCanPassSpouseToFormByGetRequestParameters()
    {
        $user = factory(User::class)->create([
            'permissions' => 2,
        ]);

        // ensure that parents don't have obvious ids
        factory(Person::class, rand(11, 19))->create();

        $woman = factory(Person::class)->state('woman')->create();
        $man = factory(Person::class)->state('man')->create();

        $response =  $this->actingAs($user)->get("marriages/create?woman=$woman->id&man=$man->id");

        $response->assertStatus(200);
        $response->assertSee($woman->id);
        $response->assertSee($man->id);
    }

    public function testDataIsValidatedUsingAppropriateFormRequest()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\MarriageController::class,
            'store',
            \App\Http\Requests\StoreMarriage::class
        );
    }

    public function testMarriageCreationIsLogged()
    {
        $marriage = factory(Marriage::class)->state('ended')->create();

        $log = $this->latestLog();

        $this->assertEquals('marriages', $log->log_name);
        $this->assertEquals('created', $log->description);
        $this->assertTrue($marriage->is($log->subject));

        $attributesToCheck = Arr::except($marriage->getAttributes(), [
            'id', 'created_at', 'updated_at',
            'first_event_date_from', 'second_event_date_from', 'end_date_from',
            'first_event_date_to', 'second_event_date_to', 'end_date_to',
        ]);

        foreach ($attributesToCheck as $key => $value) {
            $this->assertEquals(
                $value,
                $log->properties['attributes'][$key],
                'Failed asserting that attribute '.$key.' has the same value in log.'
            );
        }

        $this->assertEquals($marriage->created_at, $log->created_at);
        $this->assertEquals($marriage->updated_at, $log->created_at);

        $this->assertEquals(
            $marriage->created_at,
            Carbon::create($log->properties['attributes']['created_at'])
        );
        $this->assertEquals(
            $marriage->updated_at,
            Carbon::create($log->properties['attributes']['updated_at'])
        );

        $this->assertEquals(
            $marriage->first_event_date_from->format('Y-m-d'),
            $log->properties['attributes']['first_event_date_from']
        );
        $this->assertEquals(
            $marriage->first_event_date_to->format('Y-m-d'),
            $log->properties['attributes']['first_event_date_to']
        );

        $this->assertEquals(
            $marriage->second_event_date_from->format('Y-m-d'),
            $log->properties['attributes']['second_event_date_from']
        );
        $this->assertEquals(
            $marriage->second_event_date_to->format('Y-m-d'),
            $log->properties['attributes']['second_event_date_to']
        );

        $this->assertEquals(
            $marriage->end_date_from->format('Y-m-d'),
            $log->properties['attributes']['end_date_from']
        );
        $this->assertEquals(
            $marriage->end_date_to->format('Y-m-d'),
            $log->properties['attributes']['end_date_to']
        );
    }
}
