<?php

namespace Tests\Feature\People;

use App\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewPeopleIndexListingTest extends TestCase
{
    use RefreshDatabase;

    public function testItWorksWithNoPeople()
    {
        $response = $this->get('/people');

        $response->assertStatus(200);
        $response->assertSeeText('total: 0');
    }

    public function testItWorksWithPeople()
    {
        factory(Person::class)->create([
            'family_name' => 'Zbyrowski',
            'last_name' => null,
        ]);
        factory(Person::class)->create([
            'family_name' => 'Ziobro',
            'last_name' => 'Mikke',
        ]);

        $response = $this->get('/people');

        $response->assertStatus(200);
        $response->assertSeeText('Z [2]');
        $response->assertSeeText('M [1]');
        $response->assertSeeText('Z [1]');
    }
}
