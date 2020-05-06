<?php

namespace Tests\Feature\People;

use App\Person;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewPeopleLetterListingTest extends TestCase
{
    use RefreshDatabase;

    public function testItWorksWithNoPeople()
    {
        $response = $this->get('/people/f/H');

        $response->assertStatus(404);
    }

    public function testItWorksWithPeople()
    {
        $this->actingAs(factory(User::class)->create(['permissions' => 1]));

        factory(Person::class)->create([
            'family_name' => 'Zbyrowski',
            'last_name' => null,
        ]);
        factory(Person::class)->create([
            'family_name' => 'Ziobro',
            'last_name' => 'Mikke',
        ]);

        $response = $this->get('/people/f/Z');

        $response->assertStatus(200);
        $response->assertSeeText('Ziobro');
        $response->assertSeeText('Mikke');

        $response = $this->get('/people/l/Z');

        $response->assertStatus(200);
        $response->assertSeeText('Zbyrowski');
        $response->assertDontSeeText('Mikke');

        $response = $this->get('/people/l/M');

        $response->assertStatus(200);
        $response->assertDontSeeText('Zbyrowski');
        $response->assertSeeText('Mikke');
    }

    public function testItHidesSensitiveDataToGuests()
    {
        factory(Person::class)->state('alive')->create([
            'family_name' => 'Ziobro',
            'last_name' => 'Mikke',
        ]);

        $response = $this->get('/people/f/Z');

        $response->assertStatus(200);
        $response->assertSeeText('[hidden]');
        $response->assertDontSeeText('Ziobro');
        $response->assertDontSeeText('Mikke');
    }
}
