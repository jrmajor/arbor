<?php

namespace Tests\Feature\Console;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class GenerateSitemapTest extends TestCase
{
    #[TestDox('it shows correct sitemap')]
    public function testSitemap(): void
    {
        $people = Person::factory()->createMany([
            ['name' => 'Hidden'],
            ['name' => 'Hidden'],
            ['name' => 'Kajnacy Masnorowski', 'visibility' => true],
            ['name' => 'Koxemiasz Kajnor', 'visibility' => true],
        ]);

        $response = $this->get('sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/xml; charset=UTF-8');

        foreach ([
            '<loc>http://arbor.test</loc>' => true,
            '<loc>http://arbor.test/people</loc>' => true,
            "<loc>http://arbor.test/people/{$people[0]->id}</loc>" => false,
            "<loc>http://arbor.test/people/{$people[1]->id}</loc>" => false,
            "<loc>http://arbor.test/people/{$people[2]->id}</loc>" => true,
            "<loc>http://arbor.test/people/{$people[3]->id}</loc>" => true,
        ] as $part => $present) {
            if ($present) {
                $response->assertSee($part, false);
            } else {
                $response->assertDontSee($part, false);
            }
        }
    }
}
