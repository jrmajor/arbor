<?php

namespace Tests\Feature\People;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

use function Pest\Laravel\get;

final class ViewPeopleIndexListingTest extends TestCase
{
    #[TestDox('it works with no people')]
    public function testEmpty(): void
    {
        $this->get('/people')
            ->assertStatus(200)
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

        get('/people')
            ->assertStatus(200)
            ->assertSeeText('Z [2]')
            ->assertSeeText('M [1]')
            ->assertSeeText('Z [1]');
    }
}
