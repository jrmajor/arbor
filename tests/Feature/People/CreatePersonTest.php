<?php

namespace Tests\Feature\People;

use App\Person;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            'birth_date' => '1878-04-01',
            'birth_place' => 'Zaleszczyki, Polska',
            'dead' => true,
            'death_date' => '1947-01-17',
            'death_place' => 'Grudziądz, Polska',
            'death_cause' => 'rak',
            'funeral_date' => '1947-01-21',
            'funeral_place' => 'Grudziądz, Polska',
            'burial_date' => '1947-01-21',
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

        foreach ($validAttributes as $key => $attribute) {
            $this->assertEquals($attribute, $person->fresh()[$key]);
        }
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
}
