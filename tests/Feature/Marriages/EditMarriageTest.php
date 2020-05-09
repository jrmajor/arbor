<?php

namespace Tests\Feature\Marriages;

use App\Marriage;
use App\Person;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
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
            'first_event_date_from' => '1968-04-14',
            'first_event_date_to' => '1968-04-14',
            'first_event_place' => 'Sępólno Krajeńskie, Polska',
            'second_event_type' => 'church_marriage',
            'second_event_date_from' => '1968-04-13',
            'second_event_date_to' => '1968-04-13',
            'second_event_place' => 'Sępólno Krajeńskie, Polska',
            'ended' => true,
            'end_cause' => 'rozwód',
            'end_date_from' => '2001-10-27',
            'end_date_to' => '2001-10-27',
        ], $overrides);
    }

    private function newAttributes($overrides = [])
    {
        return array_merge([
            'woman_order' => 2,
            'man_order' => 1,
            'rite' => 'civil',
            'first_event_type' => 'concordat_marriage',
            'first_event_date_from' => '1960-09-02',
            'first_event_place' => 'Warszawa, Polska',
            'second_event_type' => 'civil_marriage',
            'second_event_date_from' => '1960-09-05',
            'second_event_place' => 'Warszawa, Polska',
            'ended' => false,
            'end_cause' => 'bo tak',
            'end_date_from' => '2000-03-27',
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

    public function testGuestsCannotEditMarriage()
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

        $attributesToCheck = Arr::except($oldAttributes, [
            'first_event_date_from', 'second_event_date_from', 'end_date_from',
            'first_event_date_to', 'second_event_date_to', 'end_date_to',
        ]);

        foreach ($attributesToCheck as $key => $attribute) {
            $this->assertEquals($attribute, $marriage->fresh()[$key]);
        }
    }

    public function testUsersWithoutPermissionsCannotEditMarriage()
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

        $attributesToCheck = Arr::except($oldAttributes, [
            'first_event_date_from', 'second_event_date_from', 'end_date_from',
            'first_event_date_to', 'second_event_date_to', 'end_date_to',
        ]);

        foreach ($attributesToCheck as $key => $attribute) {
            $this->assertEquals($attribute, $marriage->fresh()[$key]);
        }
    }

    public function testUsersWithPermissionsCanEditMarriage()
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

        $attributesToCheck = Arr::except($newAttributes, [
            'first_event_date_from', 'second_event_date_from', 'end_date_from',
            'first_event_date_to', 'second_event_date_to', 'end_date_to',
        ]);

        foreach ($attributesToCheck as $key => $attribute) {
            $this->assertEquals($attribute, $marriage->fresh()[$key]);
        }

        $this->assertTrue($newAttributes['first_event_date_from'] == $marriage->fresh()['first_event_date_from']->toDateString());
        $this->assertTrue($newAttributes['first_event_date_from'] == $marriage->fresh()['first_event_date_to']->toDateString());

        $this->assertTrue($newAttributes['second_event_date_from'] == $marriage->fresh()['second_event_date_from']->toDateString());
        $this->assertTrue($newAttributes['second_event_date_from'] == $marriage->fresh()['second_event_date_to']->toDateString());

        $this->assertTrue($newAttributes['end_date_from'] == $marriage->fresh()['end_date_from']->toDateString());
        $this->assertTrue($newAttributes['end_date_from'] == $marriage->fresh()['end_date_to']->toDateString());
    }

    public function testDataIsValidatedUsingAppropriateFormRequest()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\MarriageController::class,
            'update',
            \App\Http\Requests\StoreMarriage::class
        );
    }

    public function testMarriageEditionIsLogged()
    {
        $marriage = factory(Marriage::class)->create($this->oldAttributes());

        $changeTimestamp = Carbon::now()->addMinute();
        Carbon::setTestNow($changeTimestamp);
        $marriage->fill($this->newAttributes())->save();
        Carbon::setTestNow();

        $marriage = $marriage->fresh();

        $log = $this->latestLog();

        $this->assertEquals('marriages', $log->log_name);
        $this->assertEquals('updated', $log->description);
        $this->assertTrue($marriage->is($log->subject));

        $oldToCheck = Arr::except($this->oldAttributes(), [
            'id', 'created_at', 'updated_at',
            'first_event_date_from', 'second_event_date_from', 'end_date_from',
            'first_event_date_to', 'second_event_date_to', 'end_date_to',
        ]);

        foreach ($oldToCheck as $key => $value) {
            $this->assertEquals(
                $value,
                $log->properties['old'][$key],
                'Failed asserting that old attribute '.$key.' has the same value in log.'
            );
        }

        $attributesToCheck = Arr::except($this->newAttributes(), [
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

        $this->assertArrayNotHasKey('created_at', $log->properties['old']);
        $this->assertArrayNotHasKey('created_at', $log->properties['attributes']);

        $this->assertEquals($marriage->updated_at, $log->created_at);

        $this->assertEquals(
            $marriage->updated_at,
            Carbon::create($log->properties['attributes']['updated_at'])
        );

        $this->assertEquals(
            $this->oldAttributes()['first_event_date_from'],
            $log->properties['old']['first_event_date_from']
        );
        $this->assertEquals(
            $this->newAttributes()['first_event_date_from'],
            $log->properties['attributes']['first_event_date_from']
        );
        $this->assertEquals(
            $this->oldAttributes()['first_event_date_to'],
            $log->properties['old']['first_event_date_to']
        );
        $this->assertEquals(
            $this->oldAttributes()['first_event_date_to'],
            $log->properties['attributes']['first_event_date_to']
        );

        $this->assertEquals(
            $this->oldAttributes()['second_event_date_from'],
            $log->properties['old']['second_event_date_from']
        );
        $this->assertEquals(
            $this->newAttributes()['second_event_date_from'],
            $log->properties['attributes']['second_event_date_from']
        );
        $this->assertEquals(
            $this->oldAttributes()['second_event_date_to'],
            $log->properties['old']['second_event_date_to']
        );
        $this->assertEquals(
            $this->oldAttributes()['second_event_date_to'],
            $log->properties['attributes']['second_event_date_to']
        );

        $this->assertEquals(
            $this->oldAttributes()['end_date_from'],
            $log->properties['old']['end_date_from']
        );
        $this->assertEquals(
            $this->newAttributes()['end_date_from'],
            $log->properties['attributes']['end_date_from']
        );
        $this->assertEquals(
            $this->oldAttributes()['end_date_to'],
            $log->properties['old']['end_date_to']
        );
        $this->assertEquals(
            $this->oldAttributes()['end_date_to'],
            $log->properties['attributes']['end_date_to']
        );
    }
}

