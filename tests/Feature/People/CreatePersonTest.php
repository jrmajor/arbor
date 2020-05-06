<?php

namespace Tests\Feature\People;

use App\Person;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class CreatePersonTest extends TestCase
{
    use RefreshDatabase;

    private function validAttributes($overrides = [])
    {
        return array_merge([
            'id_wielcy' => 'psb.6305.1',
            'id_pytlewski' => 2137,
            'sex' => 'xy',
            'name' => 'Henryk',
            'middle_name' => 'Erazm',
            'family_name' => 'Gąsiorowski',
            'last_name' => 'Jakże to',
            'birth_date_from' => '1878-04-01',
            'birth_place' => 'Zaleszczyki, Polska',
            'dead' => true,
            'death_date_from' => '1947-01-17',
            'death_place' => 'Grudziądz, Polska',
            'death_cause' => 'rak',
            'funeral_date_from' => '1947-01-21',
            'funeral_place' => 'Grudziądz, Polska',
            'burial_date_from' => '1947-01-21',
            'burial_place' => 'Grudziądz, Polska',
            // 'visibility' => true,
        ], $overrides);
    }

    public function testGuestAreAskedToLogInWhenAttemptingToViewAddPersonForm()
    {
        $response = $this->get('people/create');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function testUsersWithoutPermissionsCannotViewAddPersonForm()
    {
        $user = factory(User::class)->create([
            'permissions' => 1,
        ]);

        $response = $this->actingAs($user)->get('people/create');

        $response->assertStatus(403);
    }

    public function testUsersWithPermissionsCanViewAddPersonForm()
    {
        $user = factory(User::class)->create([
            'permissions' => 2,
        ]);

        $response = $this->actingAs($user)->get('people/create');

        $response->assertStatus(200);
    }

    public function testGuestCannotAddValidPerson()
    {
        $response = $this->post('people', $this->validAttributes());

        $response->assertStatus(302);
        $response->assertRedirect('login');
        $this->assertEquals(0, Person::count());
    }

    public function testUsersWithoutPermissionsCannotAddValidPerson()
    {
        $user = factory(User::class)->create([
            'permissions' => 1,
        ]);

        $response = $this->actingAs($user)->post('people', $this->validAttributes());

        $response->assertStatus(403);
        $this->assertEquals(0, Person::count());
    }

    public function testUsersWithPermissionsCanAddValidPerson()
    {
        $user = factory(User::class)->create([
            'permissions' => 2,
        ]);

        $mother = factory(Person::class)->state('woman')->create();
        $father = factory(Person::class)->state('man')->create();

        $validAttributes = $this->validAttributes([
            'mother_id' => $mother->id,
            'father_id' => $father->id,
        ]);

        $response = $this->actingAs($user)->post('people', $validAttributes);

        $response->assertStatus(302);

        $person = Person::orderBy('id', 'desc')->first();

        $response->assertRedirect("people/$person->id");
        $this->assertEquals(3, Person::count());

        $attributesToCheck = Arr::except($validAttributes, [
            'birth_date_from', 'death_date_from', 'funeral_date_from', 'burial_date_from',
            'birth_date_to', 'death_date_to', 'funeral_date_to', 'burial_date_to',
        ]);

        foreach ($attributesToCheck as $key => $attribute) {
            $this->assertEquals($attribute, $person->fresh()[$key]);
        }

        $this->assertTrue($validAttributes['birth_date_from'] == $person->fresh()['birth_date_from']->toDateString());
        $this->assertTrue($validAttributes['birth_date_from'] == $person->fresh()['birth_date_to']->toDateString());

        $this->assertTrue($validAttributes['death_date_from'] == $person->fresh()['death_date_from']->toDateString());
        $this->assertTrue($validAttributes['death_date_from'] == $person->fresh()['death_date_to']->toDateString());

        $this->assertTrue($validAttributes['funeral_date_from'] == $person->fresh()['funeral_date_from']->toDateString());
        $this->assertTrue($validAttributes['funeral_date_from'] == $person->fresh()['funeral_date_to']->toDateString());

        $this->assertTrue($validAttributes['burial_date_from'] == $person->fresh()['burial_date_from']->toDateString());
        $this->assertTrue($validAttributes['burial_date_from'] == $person->fresh()['burial_date_to']->toDateString());
    }

    public function testYouCanPassParentsToFormByGetRequestParameters()
    {
        $user = factory(User::class)->create([
            'permissions' => 2,
        ]);

        // ensure that parents don't have obvious ids
        factory(Person::class, rand(11, 19))->create();

        $mother = factory(Person::class)->state('woman')->create();
        $father = factory(Person::class)->state('man')->create();

        $response =  $this->actingAs($user)->get("people/create?mother=$mother->id&father=$father->id");

        $response->assertStatus(200);
        $response->assertSee($mother->id);
        $response->assertSee($father->id);
    }

    public function testDataIsValidatedUsingAppropriateFormRequest()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PersonController::class,
            'store',
            \App\Http\Requests\StorePerson::class
        );
    }

    public function testPersonCreationIsLogged()
    {
        $person = factory(Person::class)->state('dead')->create();

        $log = $this->latestLog();

        $this->assertEquals('people', $log->log_name);
        $this->assertEquals('created', $log->description);
        $this->assertTrue($person->is($log->subject));

        $attributesToCheck = Arr::except($person->getAttributes(), [
            'id', 'created_at', 'updated_at',
            'birth_date_from', 'death_date_from', 'funeral_date_from', 'burial_date_from',
            'birth_date_to', 'death_date_to', 'funeral_date_to', 'burial_date_to',
        ]);

        foreach ($attributesToCheck as $key => $value) {
            $this->assertEquals(
                $value,
                $log->properties['attributes'][$key],
                'Failed asserting that attribute '.$key.' has the same value in log.'
            );
        }

        $this->assertEquals($person->created_at, $log->created_at);
        $this->assertEquals($person->updated_at, $log->created_at);

        $this->assertEquals(
            $person->created_at,
            Carbon::create($log->properties['attributes']['created_at'])
        );
        $this->assertEquals(
            $person->updated_at,
            Carbon::create($log->properties['attributes']['updated_at'])
        );

        $this->assertEquals(
            $person->birth_date_from->format('Y-m-d'),
            $log->properties['attributes']['birth_date_from']
        );
        $this->assertEquals(
            $person->birth_date_to->format('Y-m-d'),
            $log->properties['attributes']['birth_date_to']
        );

        $this->assertEquals(
            $person->death_date_from->format('Y-m-d'),
            $log->properties['attributes']['death_date_from']
        );
        $this->assertEquals(
            $person->death_date_to->format('Y-m-d'),
            $log->properties['attributes']['death_date_to']
        );

        $this->assertEquals(
            $person->funeral_date_from->format('Y-m-d'),
            $log->properties['attributes']['funeral_date_from']
        );
        $this->assertEquals(
            $person->funeral_date_to->format('Y-m-d'),
            $log->properties['attributes']['funeral_date_to']
        );

        $this->assertEquals(
            $person->burial_date_from->format('Y-m-d'),
            $log->properties['attributes']['burial_date_from']
        );
        $this->assertEquals(
            $person->burial_date_to->format('Y-m-d'),
            $log->properties['attributes']['burial_date_to']
        );
    }
}
