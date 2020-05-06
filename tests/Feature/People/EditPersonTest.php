<?php

namespace Tests\Feature\People;

use App\Person;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
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

        $attributesToCheck = Arr::except($this->oldAttributes(), [
            'birth_date_from', 'death_date_from', 'funeral_date_from', 'burial_date_from',
            'birth_date_to', 'death_date_to', 'funeral_date_to', 'burial_date_to',
        ]);

        foreach ($attributesToCheck as $key => $attribute) {
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

        $attributesToCheck = Arr::except($this->oldAttributes(), [
            'birth_date_from', 'death_date_from', 'funeral_date_from', 'burial_date_from',
            'birth_date_to', 'death_date_to', 'funeral_date_to', 'burial_date_to',
        ]);

        foreach ($attributesToCheck as $key => $attribute) {
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

        $attributesToCheck = Arr::except($newAttributes, [
            'birth_date_from', 'death_date_from', 'funeral_date_from', 'burial_date_from',
            'birth_date_to', 'death_date_to', 'funeral_date_to', 'burial_date_to',
        ]);

        foreach ($attributesToCheck as $key => $attribute) {
            $this->assertEquals($attribute, $person->fresh()[$key]);
        }

        $this->assertTrue($newAttributes['birth_date_from'] == $person->fresh()['birth_date_from']->toDateString());
        $this->assertTrue($newAttributes['birth_date_from'] == $person->fresh()['birth_date_to']->toDateString());

        $this->assertTrue($newAttributes['death_date_from'] == $person->fresh()['death_date_from']->toDateString());
        $this->assertTrue($newAttributes['death_date_from'] == $person->fresh()['death_date_to']->toDateString());

        $this->assertTrue($newAttributes['funeral_date_from'] == $person->fresh()['funeral_date_from']->toDateString());
        $this->assertTrue($newAttributes['funeral_date_from'] == $person->fresh()['funeral_date_to']->toDateString());

        $this->assertTrue($newAttributes['burial_date_from'] == $person->fresh()['burial_date_from']->toDateString());
        $this->assertTrue($newAttributes['burial_date_from'] == $person->fresh()['burial_date_to']->toDateString());
    }

    public function testDataIsValidatedUsingAppropriateFormRequest()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PersonController::class,
            'update',
            \App\Http\Requests\StorePerson::class
        );
    }

    public function testPersonEditionIsLogged()
    {
        $person = factory(Person::class)->create($this->oldAttributes());

        sleep(1);
        $person->fill($this->newAttributes())->save();

        $person = $person->fresh();

        $log = $this->latestLog();

        $this->assertEquals('people', $log->log_name);
        $this->assertEquals('updated', $log->description);
        $this->assertTrue($person->is($log->subject));

        $oldToCheck = Arr::except($this->oldAttributes(), [
            'id', 'created_at', 'updated_at', 'dead', 'death_cause',
            'birth_date_from', 'death_date_from', 'funeral_date_from', 'burial_date_from',
            'birth_date_to', 'death_date_to', 'funeral_date_to', 'burial_date_to',
        ]);

        foreach ($oldToCheck as $key => $value) {
            $this->assertEquals(
                $value,
                $log->properties['old'][$key],
                'Failed asserting that old attribute '.$key.' has the same value in log.'
            );
        }

        $attributesToCheck = Arr::except($this->newAttributes(), [
            'id', 'created_at', 'updated_at', 'dead', 'death_cause',
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

        $this->assertArrayNotHasKey('dead', $log->properties['old']);
        $this->assertArrayNotHasKey('dead', $log->properties['attributes']);

        $this->assertArrayNotHasKey('death_cause', $log->properties['old']);
        $this->assertArrayNotHasKey('death_cause', $log->properties['attributes']);

        $this->assertArrayNotHasKey('created_at', $log->properties['old']);
        $this->assertArrayNotHasKey('created_at', $log->properties['attributes']);

        $this->assertEquals($person->updated_at, $log->created_at);

        $this->assertEquals(
            $person->updated_at,
            Carbon::create($log->properties['attributes']['updated_at'])
        );

        $this->assertEquals(
            Carbon::create($this->oldAttributes()['birth_date_from']),
            Carbon::create($log->properties['old']['birth_date_from'])
        );
        $this->assertEquals(
            Carbon::create($this->newAttributes()['birth_date_from']),
            Carbon::create($log->properties['attributes']['birth_date_from'])
        );
        $this->assertEquals(
            Carbon::create($this->oldAttributes()['birth_date_to']),
            Carbon::create($log->properties['old']['birth_date_to'])
        );
        $this->assertEquals(
            Carbon::create($this->oldAttributes()['birth_date_to']),
            Carbon::create($log->properties['attributes']['birth_date_to'])
        );

        $this->assertEquals(
            Carbon::create($this->oldAttributes()['death_date_from']),
            Carbon::create($log->properties['old']['death_date_from'])
        );
        $this->assertEquals(
            Carbon::create($this->newAttributes()['death_date_from']),
            Carbon::create($log->properties['attributes']['death_date_from'])
        );
        $this->assertEquals(
            Carbon::create($this->oldAttributes()['death_date_to']),
            Carbon::create($log->properties['old']['death_date_to'])
        );
        $this->assertEquals(
            Carbon::create($this->oldAttributes()['death_date_to']),
            Carbon::create($log->properties['attributes']['death_date_to'])
        );

        $this->assertEquals(
            Carbon::create($this->oldAttributes()['funeral_date_from']),
            Carbon::create($log->properties['old']['funeral_date_from'])
        );
        $this->assertEquals(
            Carbon::create($this->newAttributes()['funeral_date_from']),
            Carbon::create($log->properties['attributes']['funeral_date_from'])
        );
        $this->assertEquals(
            Carbon::create($this->oldAttributes()['funeral_date_to']),
            Carbon::create($log->properties['old']['funeral_date_to'])
        );
        $this->assertEquals(
            Carbon::create($this->oldAttributes()['funeral_date_to']),
            Carbon::create($log->properties['attributes']['funeral_date_to'])
        );

        $this->assertEquals(
            Carbon::create($this->oldAttributes()['burial_date_from']),
            Carbon::create($log->properties['old']['burial_date_from'])
        );
        $this->assertEquals(
            Carbon::create($this->newAttributes()['burial_date_from']),
            Carbon::create($log->properties['attributes']['burial_date_from'])
        );
        $this->assertEquals(
            Carbon::create($this->oldAttributes()['burial_date_to']),
            Carbon::create($log->properties['old']['burial_date_to'])
        );
        $this->assertEquals(
            Carbon::create($this->oldAttributes()['burial_date_to']),
            Carbon::create($log->properties['attributes']['burial_date_to'])
        );
    }
}

