<?php

namespace Tests\Feature\People;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class ViewPeopleIndexListingTest extends TestCase
{
    #[TestDox('it works with no people')]
    public function testEmpty(): void
    {
        $this->get('/people')
            ->assertOk()
            ->assertSeeText('total: 0');
    }

    #[TestDox('it works with people')]
    public function testOk(): void
    {
        Person::factory()->create([
            'family_name' => 'Zbyrowski',
            'last_name' => null,
        ]);

        Person::factory()->create([
            'family_name' => 'Ziobro',
            'last_name' => 'Mikke',
        ]);

        $this->get('/people')
            ->assertOk()
            ->assertSeeText('Z [2]')
            ->assertSeeText('M [1]')
            ->assertSeeText('Z [1]');
    }
}
