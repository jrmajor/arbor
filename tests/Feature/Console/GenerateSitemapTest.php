<?php

use App\Models\Person;
use Illuminate\Support\Str;

it('can generate sitemap', function () {
    $sitemapPath = __DIR__.'/../../../public/sitemap.xml';

    if (file_exists($sitemapPath)) {
        unlink($sitemapPath);
    }

    expect($sitemapPath)->not->toBeFile();

    $people = Person::factory(4)->sequence(
        ['name' => 'Hidden'],
        ['name' => 'Hidden'],
        ['name' => 'Kajnacy Masnorowski', 'visibility' => true],
        ['name' => 'Koxemiasz Kajnor', 'visibility' => true],
    )->create();

    $this->artisan('sitemap:generate')
        ->expectsOutput('Sitemap has been generated successfully.')
        ->assertExitCode(0);

    expect($sitemapPath)->toBeFile();

    expect(file_get_contents($sitemapPath))
        ->toContain('<loc>http://arbor.test</loc>')
        ->toContain('<loc>http://arbor.test/people</loc>')
        ->not->toContain("<loc>http://arbor.test/people/{$people[0]->id}</loc>")
        ->not->toContain("<loc>http://arbor.test/people/{$people[1]->id}</loc>")
        ->toContain("<loc>http://arbor.test/people/{$people[2]->id}</loc>")
        ->toContain("<loc>http://arbor.test/people/{$people[3]->id}</loc>");
});
