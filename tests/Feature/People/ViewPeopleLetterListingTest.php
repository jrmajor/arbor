<?php

namespace Tests\Feature\People;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

use function Pest\Laravel\get;

final class ViewPeopleLetterListingTest extends TestCase
{
    #[TestDox('it works with no people')]
    public function testEmpty(): void
    {
        $this->get('/people/f/H')
            ->assertStatus(404);
    }

    #[TestDox('it works with people')]
    public function testOk(): void
    {
        $this->withPermissions(1);

        Person::factory()->create([
            'family_name' => 'Zbyrowski',
            'last_name' => null,
        ]);

        Person::factory()->create([
            'family_name' => 'Ziobro',
            'last_name' => 'Mikke',
        ]);

        get('/people/f/Z')
            ->assertStatus(200)
            ->assertSeeText('Ziobro')
            ->assertSeeText('Mikke');

        get('/people/l/Z')
            ->assertStatus(200)
            ->assertSeeText('Zbyrowski')
            ->assertDontSeeText('Mikke');

        get('/people/l/M')
            ->assertStatus(200)
            ->assertDontSeeText('Zbyrowski')
            ->assertSeeText('Mikke');
    }

    #[TestDox('it hides sensitive data to guests')]
    public function testGuest(): void
    {
        Person::factory()->alive()->create([
            'family_name' => 'Ziobro',
            'last_name' => 'Mikke',
        ]);

        get('/people/f/Z')
            ->assertStatus(200)
            ->assertSeeText('[hidden]')
            ->assertDontSeeText('Ziobro')
            ->assertDontSeeText('Mikke');
    }
}
