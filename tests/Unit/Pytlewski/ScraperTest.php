<?php

namespace Tests\Unit\Pytlewski;

use App\Models\Person;
use App\Services\Pytlewski\PytlewskiScraper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Psl\File;
use Tests\TestCase;

final class ScraperTest extends TestCase
{
    use UsesPytlewskiDataset;

    private PytlewskiScraper $scraper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scraper = $this->app->make(PytlewskiScraper::class);
    }

    #[TestDox('it can make proper url')]
    public function testUrl(): void
    {
        $this->assertSame(
            'https://www.pytlewski.pl/index/drzewo/index.php?view=true&id=556',
            PytlewskiScraper::url(556),
        );
    }

    #[TestDox('it requests source from pytlewski.pl')]
    public function testSourceRequest(): void
    {
        Http::fake();

        $this->scraper->find(556);

        Http::assertSent(
            fn ($request) => $request->url() === 'https://www.pytlewski.pl/index/drzewo/index.php?view=true&id=556',
        );
    }

    #[TestDox("returns null when it can't scrape received response")]
    public function testScrapeError(): void
    {
        Http::fake();

        $this->assertNull($this->scraper->find(556));
    }

    #[TestDox('it returns null when receives error response')]
    public function testErrorResponse(): void
    {
        Http::fake([PytlewskiScraper::url(556) => Http::response(status: 404)]);

        $this->assertNull($this->scraper->find(556));
    }

    #[TestDox('it returns null when connection fails')]
    public function testConnectionError(): void
    {
        Http::fake([PytlewskiScraper::url(556) => Http::failedConnection()]);

        $this->assertNull($this->scraper->find(556));
    }

    #[TestDox('it does not request anything for person without id')]
    public function testPersonWithoutId(): void
    {
        $person = Person::factory()->make(['id_pytlewski' => null]);

        $this->assertNull($this->scraper->for($person));
    }

    #[TestDox('it caches parsed attributes from pytlewski.pl')]
    public function testCache(): void
    {
        Http::fake();

        Cache::shouldReceive('flexible')
            ->once()
            ->with('pytlewski.556', Mockery::any(), Mockery::any())
            ->andReturn('');

        $this->scraper->find(556);

        Http::assertSentCount(0);
    }

    #[TestDox('it fixes windows-1250 text decoded as iso-8859-2')]
    public function testMojibake(): void
    {
        $this->fakeSource(556, [
            // windows-1250 bytes of "Świątek" decoded as iso-8859-2
            '<b>Major</b><br>Józef' => "<b>\u{8C}wi\u{161}tek</b><br>Józef",
        ]);

        $this->assertSame('Świątek', $this->scraper->find(556)->familyName);
    }

    #[TestDox('it keeps characters that are not in iso-8859-2')]
    public function testCharactersOutsideCharset(): void
    {
        $this->fakeSource(556, [
            '<b>Major</b>' => '<b>Major €😀</b>',
        ]);

        $this->assertSame('Major €😀', $this->scraper->find(556)->familyName);
    }

    #[TestDox('it tolerates names without separator')]
    public function testNamesWithoutSeparator(): void
    {
        $this->fakeSource(556, [
            '<b>Major</b><br>Józef' => '<b>Major</b> Józef',
        ]);

        $pytlewski = $this->scraper->find(556);

        $this->assertSame('Major Józef', $pytlewski->familyName);
        $this->assertNull($pytlewski->name);
    }

    #[TestDox('it skips parents when it cannot tell them apart')]
    public function testParentsWithoutSeparator(): void
    {
        $this->fakeSource(556, [
            'Gołębiowska, Jadwiga<br>Major, Jacenty' => 'Gołębiowska, Jadwiga',
        ]);

        $pytlewski = $this->scraper->find(556);

        $this->assertNull($pytlewski->mother);
        $this->assertNull($pytlewski->father);
    }

    #[TestDox('it skips relations without header')]
    public function testRelationsWithoutHeader(): void
    {
        $this->fakeSource(556, [
            '<center><b>Małżeństwa(2):</b></center>' => '',
            '<center><b>Dzieci(4):</b></center>' => '',
        ]);

        $pytlewski = $this->scraper->find(556);

        $this->assertSame([], $pytlewski->marriages);
        $this->assertSame([], $pytlewski->children);
        $this->assertCount(4, $pytlewski->siblings);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    #[DataProvider('provideScrapeCases')]
    #[TestDox('it properly scrapes pytlewski.pl')]
    public function testScrape(int $id, string $source, array $attributes): void
    {
        Http::fake([PytlewskiScraper::url($id) => Http::response($source)]);

        $pytlewski = $this->scraper->find($id);

        $keysToCheck = [
            'familyName', 'lastName', 'name', 'middleName',
            'birthDate', 'birthPlace',
            'deathDate', 'deathPlace', 'burialPlace',
            'photo', 'bio',
        ];

        foreach (Arr::only($attributes, $keysToCheck) as $key => $value) {
            $this->assertSame($value, $pytlewski->{$key}, "Value of {$key} does not match.");
        }
    }

    /**
     * Fakes the response with a dataset source altered by the replacements.
     *
     * @param array<string, string> $replacements
     */
    private function fakeSource(int $id, array $replacements): void
    {
        $source = File\read(__DIR__ . "/../../Datasets/Pytlewscy/{$id}.html");

        foreach ($replacements as $search => $replace) {
            $this->assertStringContainsString($search, $source);
        }

        Http::fake([PytlewskiScraper::url($id) => Http::response(strtr($source, $replacements))]);
    }
}
