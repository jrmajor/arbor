<?php

use App\Person;
use Spatie\Activitylog\Models\Activity;

beforeEach(
    fn () => $this->person = factory(Person::class)->create()
);

test('guests cannot change persons visibility', function () {
    put('people/'.$this->person->id.'/visibility')
        ->assertStatus(302)
        ->assertRedirect('login');

    assertFalse($this->person->fresh()->isVisible());
});

test('users without permissions cannot change persons visibility', function () {
    withPermissions(3)
        ->put('people/'.$this->person->id.'/visibility')
        ->assertStatus(403);

    assertFalse($this->person->fresh()->isVisible());
});

test('users with permissions can change persons visibility', function () {
    assertFalse($this->person->isVisible());

    withPermissions(4)
        ->from('people/'.$this->person->id.'/edit')
        ->put('people/'.$this->person->id.'/visibility', [
            'visibility' => true,
        ])->assertStatus(302)
        ->assertRedirect('people/'.$this->person->id.'/edit');

    assertTrue($this->person->fresh()->isVisible());
});

test('visibility change is logged', function () {
    assertFalse($this->person->isVisible());

    $count = Activity::count();

    travel('+1 minute');

    withPermissions(4)
        ->put('people/'.$this->person->id.'/visibility', [
            'visibility' => true,
        ]);

    travel('back');

    assertEquals($count + 2, Activity::count()); // visibility change and user creation

    $log = latestLog();

    assertEquals('people', $log->log_name);
    assertEquals('changed-visibility', $log->description);
    assertTrue($this->person->fresh()->is($log->subject));

    assertEquals($this->person->fresh()->updated_at, $log->created_at);

    assertCount(2, $log->properties);
    assertCount(1, $log->properties['old']);
    assertCount(1, $log->properties['attributes']);

    assertEquals(false, $log->properties['old']['visibility']);
    assertEquals(true, $log->properties['attributes']['visibility']);
});
