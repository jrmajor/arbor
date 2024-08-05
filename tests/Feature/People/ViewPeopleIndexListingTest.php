<?php

namespace Tests\Feature\People;

use App\Models\Person;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class ViewPeopleIndexListingTest extends TestCase
{
    #[TestDox('it works with no people')]
    public function testEmpty(): void
    {
        $this->get('/people')->assertOk()->assertInertia(function (Assert $page) {
            $page->assertProps([
                'total' => 0,
                'letters' => [
                    'family' => [],
                    'last' => [],
                ],
            ])->component('People/Index');
        });
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

        $this->get('/people')->assertOk()->assertInertia(function (Assert $page) {
            $page->assertProps([
                'total' => 2,
                'letters' => [
                    'family' => [
                        ['letter' => 'Z', 'count' => 2],
                    ],
                    'last' => [
                        ['letter' => 'M', 'count' => 1],
                        ['letter' => 'Z', 'count' => 1],
                    ],
                ],
            ])->component('People/Index');
        });
    }
}
