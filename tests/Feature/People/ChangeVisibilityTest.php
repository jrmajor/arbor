<?php

use App\Models\Person;
use function Pest\Laravel\put;
use function Pest\Laravel\travel;
use function Pest\Laravel\travelBack;
use Spatie\Activitylog\Models\Activity;

beforeEach(
    fn () => $this->person = Person::factory()->create()
);

test('guests cannot change persons visibility', function () {
    put("people/{$this->person->id}/visibility")
        ->assertStatus(302)
        ->assertRedirect('login');

    expect($this->person->fresh()->isVisible())->toBeFalse();
});

test('users without permissions cannot change persons visibility', function () {
    withPermissions(3)
        ->put("people/{$this->person->id}/visibility")
        ->assertStatus(403);

    expect($this->person->fresh()->isVisible())->toBeFalse();
});

test('users with permissions can change persons visibility', function () {
    expect($this->person->isVisible())->toBeFalse();

    withPermissions(4)
        ->from('people/'.$this->person->id.'/edit')
        ->put("people/{$this->person->id}/visibility", [
            'visibility' => true,
        ])->assertStatus(302)
        ->assertRedirect('people/'.$this->person->id.'/edit');

    expect($this->person->fresh()->isVisible())->toBeTrue();
});

test('visibility change is logged', function () {
    expect($this->person->isVisible())->toBeFalse();

    $count = Activity::count();

    travel(5)->minutes();

    withPermissions(4)
        ->put("people/{$this->person->id}/visibility", [
            'visibility' => true,
        ]);

    travelBack();

    expect(Activity::count())->toBe($count + 2); // visibility change and user creation

    $log = latestLog();

    expect($log->log_name)->toBe('people');
    expect($log->description)->toBe('changed-visibility');
    expect($log->subject()->is($this->person))->toBeTrue();

    expect((string) $log->created_at)->toBe((string) $this->person->fresh()->updated_at);

    expect($log->properties)->toHaveCount(2);
    expect($log->properties['old'])->toHaveCount(1);
    expect($log->properties['attributes'])->toHaveCount(1);

    expect($log->properties['old']['visibility'])->toBeFalse();
    expect($log->properties['attributes']['visibility'])->toBeTrue();
});
