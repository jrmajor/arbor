<?php

namespace Tests\Feature\People;

use App\Person;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditPersonTest extends TestCase
{
    use RefreshDatabase;

    private function oldAttributes($overrides = [])
    {
        return array_merge([
            'id_wielcy' => 'psb.6305.3',
            'id_pytlewski' => 2115,
            'sex' => 'xx',
            'name' => 'Maria',
            'middle_name' => 'Henryka',
            'family_name' => 'Stecher de Sebenitz',
            'last_name' => 'Gąsiorowska',
            'birth_date' => '1854-01-12',
            'birth_place' => 'Zaleszczyki, Ukraina',
            'dead' => true,
            'death_date' => '1918-01-02',
            'death_place' => 'Załuż k/Sanoka, Polska',
            'death_cause' => 'rak',
            'funeral_date' => '1918-01-05',
            'funeral_place' => 'Załuż k/Sanoka, Polska',
            'burial_date' => '1918-01-05',
            'burial_place' => 'Załuż k/Sanoka, Polska',
            // 'visibility' => false,
        ], $overrides);
    }

    private function newAttributes($overrides = [])
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

    public function testGuestsAreAskedToLogInWhenAttemptingToViewEditPersonForm()
    {
        $person = factory(Person::class)->create();

        $response = $this->get("people/$person->id/edit");

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function testGuestsAreAskedToLogInWhenAttemptingToViewEditFormForNonexistentPerson()
    {
        $response = $this->get("people/1/edit");

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function testUsersWithoutPermissionsCannotViewEditPersonForm()
    {
        $person = factory(Person::class)->create();

        $user = factory(User::class)->create([
            'permissions' => 1,
        ]);

        $response = $this->actingAs($user)->get("people/$person->id/edit");

        $response->assertStatus(403);
    }

    public function testUsersWithPermissionsCanViewEditPersonForm()
    {
        $person = factory(Person::class)->create();

        $user = factory(User::class)->create([
            'permissions' => 2,
        ]);

        $response = $this->actingAs($user)->get("people/$person->id/edit");

        $response->assertStatus(200);
    }

    public function testGuestsCannotEditPerson()
    {
        $person = factory(Person::class)->create($this->oldAttributes());

        $response = $this->put("people/$person->id", $this->newAttributes());

        $response->assertStatus(302);
        $response->assertRedirect('login');

        foreach ($this->oldAttributes() as $key => $attribute) {
            $this->assertEquals($attribute, $person->fresh()[$key]);
        }
    }

    public function testUsersWithoutPermissionsCannotEditPerson()
    {
        $person = factory(Person::class)->create($this->oldAttributes());

        $user = factory(User::class)->create([
            'permissions' => 1,
        ]);

        $response = $this->actingAs($user)->put("people/$person->id", $this->newAttributes());

        $response->assertStatus(403);

        foreach ($this->oldAttributes() as $key => $attribute) {
            $this->assertEquals($attribute, $person->fresh()[$key]);
        }
    }

    public function testUsersWithPermissionsCanEditPerson()
    {
        $person = factory(Person::class)->create($this->oldAttributes());

        $user = factory(User::class)->create([
            'permissions' => 2,
        ]);

        $mother = factory(Person::class)->state('woman')->create();
        $father = factory(Person::class)->state('man')->create();

        $newAttributes = $this->newAttributes([
            'mother_id' => $mother->id,
            'father_id' => $father->id,
        ]);

        $response = $this->actingAs($user)->put("people/$person->id", $newAttributes);

        $response->assertStatus(302);
        $response->assertRedirect("people/$person->id");

        foreach ($newAttributes as $key => $attribute) {
            $this->assertEquals($attribute, $person->fresh()[$key]);
        }
    }

    public function testDataIsValidatedUsingAppropriateFormRequest()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PersonController::class,
            'update',
            \App\Http\Requests\StorePerson::class
        );
    }
}

