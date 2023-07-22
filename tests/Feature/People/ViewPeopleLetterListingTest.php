<?php

namespace Tests\Feature\People;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class ViewPeopleLetterListingTest extends TestCase
{
    #[TestDox('it works with no people')]
    public function testEmpty(): void
    {
        $this->get('/people/f/H')->assertNotFound();
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

        $this->get('/people/f/Z')
            ->assertOk()
            ->assertSeeText('Ziobro')
            ->assertSeeText('Mikke');

        $this->get('/people/l/Z')
            ->assertOk()
            ->assertSeeText('Zbyrowski')
            ->assertDontSeeText('Mikke');

        $this->get('/people/l/M')
            ->assertOk()
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

        $this->get('/people/f/Z')
            ->assertOk()
            ->assertSeeText('[hidden]')
            ->assertDontSeeText('Ziobro')
            ->assertDontSeeText('Mikke');
    }
}
