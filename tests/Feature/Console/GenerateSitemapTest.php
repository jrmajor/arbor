<?php

namespace Tests\Feature\Console;

use App\Models\Person;
use PHPUnit\Framework\Attributes\TestDox;
use Psl\File;
use Psl\Filesystem;
use Tests\TestCase;

final class GenerateSitemapTest extends TestCase
{
    private const SITEMAP_PATH = __DIR__ . '/../../../public/sitemap.xml';

    #[TestDox('it can generate sitemap')]
    public function testSitemap(): void
    {
        if (Filesystem\exists(self::SITEMAP_PATH)) {
            Filesystem\delete_file(self::SITEMAP_PATH);
        }

        $people = Person::factory()->createMany([
            ['name' => 'Hidden'],
            ['name' => 'Hidden'],
            ['name' => 'Kajnacy Masnorowski', 'visibility' => true],
            ['name' => 'Koxemiasz Kajnor', 'visibility' => true],
        ]);

        $this->artisan('sitemap:generate')
            ->expectsOutput('Sitemap has been generated successfully.')
            ->assertExitCode(0);

        $this->assertFileExists(self::SITEMAP_PATH);

        $sitemap = File\read(self::SITEMAP_PATH);

        foreach ([
            '<loc>http://arbor.test</loc>' => true,
            '<loc>http://arbor.test/people</loc>' => true,
            "<loc>http://arbor.test/people/{$people[0]->id}</loc>" => false,
            "<loc>http://arbor.test/people/{$people[1]->id}</loc>" => false,
            "<loc>http://arbor.test/people/{$people[2]->id}</loc>" => true,
            "<loc>http://arbor.test/people/{$people[3]->id}</loc>" => true,
        ] as $part => $present) {
            if ($present) {
                $this->assertStringContainsString($part, $sitemap);
            } else {
                $this->assertStringNotContainsString($part, $sitemap);
            }
        }
    }
}
