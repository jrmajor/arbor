<?php

namespace Tests\Feature\Marriages;

use App\Marriage;
use App\Person;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditMarriageTest extends TestCase
{
    use RefreshDatabase;

    private function oldAttributes($overrides = [])
    {
        return array_merge([
            'woman_order' => 1,
            'man_order' => 2,
            'rite' => 'roman_catholic',
            'first_event_type' => 'civil_marriage',
            'first_event_date' => '1968-04-14',
            'first_event_place' => 'Sępólno Krajeńskie, Polska',
            'second_event_type' => 'church_marriage',
            'second_event_date' => '1968-04-13',
            'second_event_place' => 'Sępólno Krajeńskie, Polska',
            'ended' => true,
            'end_cause' => 'rozwód',
            'end_date' => '2001-10-27'
        ], $overrides);
    }

    private function newAttributes($overrides = [])
    {
        return array_merge([
            'woman_order' => 2,
            'man_order' => 1,
            'rite' => 'civil',
            'first_event_type' => 'concordat_marriage',
            'first_event_date' => '2000-09-02',
            'first_event_place' => 'Warszawa, Polska',
            'second_event_type' => 'civil_marriage',
            'second_event_date' => '2000-09-05',
            'second_event_place' => 'Warszawa, Polska',
            'ended' => false,
            'end_cause' => 'bo tak',
            'end_date' => '2020-03-27'
        ], $overrides);
    }

    public function testGuestsAreAskedToLogInWhenAttemptingToViewEditMarriageForm()
    {
        $marriage = factory(Marriage::class)->create();

        $response = $this->get("marriages/$marriage->id/edit");

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function testGuestsAreAskedToLogInWhenAttemptingToViewEditFormForNonexistentMarriage()
    {
        $response = $this->get("marriages/1/edit");

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }

    public function testUsersWithoutPermissionsCannotViewEditMarriageForm()
    {
        $marriage = factory(Marriage::class)->create();

        $user = factory(User::class)->create([
            'permissions' => 1,
        ]);

        $response = $this->actingAs($user)->get("marriages/$marriage->id/edit");

        $response->assertStatus(403);
    }

    public function testUsersWithPermissionsCanViewEditMarriageForm()
    {
        $marriage = factory(Marriage::class)->create();

        $user = factory(User::class)->create([
            'permissions' => 2,
        ]);

        $response = $this->actingAs($user)->get("marriages/$marriage->id/edit");

        $response->assertStatus(200);
    }

    public function testGuestsCannotEditPerson()
    {
        $oldWoman = factory(Person::class)->state('woman')->create();
        $oldMan = factory(Person::class)->state('man')->create();

        $oldAttributes = $this->oldAttributes([
            'woman_id' => $oldWoman->id,
            'man_id' => $oldMan->id,
        ]);

        $marriage = factory(Marriage::class)->create($oldAttributes);

        $newWoman = factory(Person::class)->state('woman')->create();
        $newMan = factory(Person::class)->state('man')->create();

        $newAttributes = $this->newAttributes([
            'woman_id' => $newWoman->id,
            'man_id' => $newMan->id,
        ]);

        $response = $this->put("marriages/$marriage->id", $newAttributes);

        $response->assertStatus(302);
        $response->assertRedirect('login');

        foreach ($oldAttributes as $key => $attribute) {
            $this->assertEquals($attribute, $marriage->fresh()[$key]);
        }
    }

    public function testUsersWithoutPermissionsCannotEditPerson()
    {
        $oldWoman = factory(Person::class)->state('woman')->create();
        $oldMan = factory(Person::class)->state('man')->create();

        $oldAttributes = $this->oldAttributes([
            'woman_id' => $oldWoman->id,
            'man_id' => $oldMan->id,
        ]);

        $marriage = factory(Marriage::class)->create($oldAttributes);

        $newWoman = factory(Person::class)->state('woman')->create();
        $newMan = factory(Person::class)->state('man')->create();

        $newAttributes = $this->newAttributes([
            'woman_id' => $newWoman->id,
            'man_id' => $newMan->id,
        ]);

        $user = factory(User::class)->create([
            'permissions' => 1,
        ]);

        $response = $this->actingAs($user)->put("marriages/$marriage->id", $newAttributes);

        $response->assertStatus(403);

        foreach ($oldAttributes as $key => $attribute) {
            $this->assertEquals($attribute, $marriage->fresh()[$key]);
        }
    }

    public function testUsersWithPermissionsCanEditPerson()
    {
        $oldWoman = factory(Person::class)->state('woman')->create();
        $oldMan = factory(Person::class)->state('man')->create();

        $oldAttributes = $this->oldAttributes([
            'woman_id' => $oldWoman->id,
            'man_id' => $oldMan->id,
        ]);

        $marriage = factory(Marriage::class)->create($oldAttributes);

        $newWoman = factory(Person::class)->state('woman')->create();
        $newMan = factory(Person::class)->state('man')->create();

        $newAttributes = $this->newAttributes([
            'woman_id' => $newWoman->id,
            'man_id' => $newMan->id,
        ]);

        $user = factory(User::class)->create([
            'permissions' => 2,
        ]);

        $response = $this->actingAs($user)->put("marriages/$marriage->id", $newAttributes);
        $marriage = $marriage->fresh();

        $response->assertStatus(302);
        $response->assertRedirect("people/$marriage->woman_id");

        foreach ($newAttributes as $key => $attribute) {
            $this->assertEquals($attribute, $marriage[$key]);
        }
    }

    public function testDataIsValidatedUsingAppropriateFormRequest()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\MarriageController::class,
            'update',
            \App\Http\Requests\StoreMarriage::class
        );
    }
}

