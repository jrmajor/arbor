<?php

namespace Tests\Unit\Wielcy;

use App\Models\Person;
use App\Services\Wielcy\WielcyScraper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Psl\File;
use Psl\Str;
use Tests\TestCase;

final class ScraperTest extends TestCase
{
    use UsesWielcyDataset;

    private WielcyScraper $scraper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scraper = $this->app->make(WielcyScraper::class);
    }

    #[TestDox('it can make proper url')]
    public function testUrl(): void
    {
        $this->assertSame(
            'https://www.sejm-wielki.pl/s/?m=NG&t=PN&n=psb.6305.1',
            WielcyScraper::url('psb.6305.1'),
        );
    }

    #[TestDox('it requests source from sejm-wielki.pl')]
    public function testSourceRequest(): void
    {
        Http::fake();

        $this->scraper->find('psb.6305.1');

        Http::assertSent(fn ($request) => $request->url() === WielcyScraper::url('psb.6305.1'));
    }

    /**
     * @param array<string, mixed> $attributes
     */
    #[DataProvider('provideScrapeCases')]
    #[TestDox('it properly scrapes sejm-wielki.pl')]
    public function testScrape(string $id, string $source, array $attributes): void
    {
        Http::fake([WielcyScraper::url($id) => Http::response($source)]);

        $wielcy = $this->scraper->find($id);

        $this->assertSame($id, $wielcy->id);
        $this->assertSame(WielcyScraper::url($id), $wielcy->url);

        $keysToCheck = [
            'name', 'middleName', 'surname', 'sex',
            'birthDate', 'birthPlace',
            'deathDate', 'deathPlace', 'burialPlace',
            'photo',
        ];

        foreach (Arr::only($attributes, $keysToCheck) as $key => $value) {
            $this->assertSame($value, $wielcy->{$key}, "Value of {$key} does not match.");
        }
    }

    #[TestDox('it returns null for unknown person')]
    public function testNotFound(): void
    {
        $source = File\read(__DIR__ . '/../../Datasets/Wielcy/xx.999999999.html');
        Http::fake([WielcyScraper::url('xx.999999999') => Http::response($source)]);

        $this->assertNull($this->scraper->find('xx.999999999'));
    }

    #[TestDox("it returns null when it can't scrape received response")]
    public function testScrapeError(): void
    {
        Http::fake();

        $this->assertNull($this->scraper->find('psb.6305.1'));
    }

    #[TestDox('it tolerates missing facts')]
    public function testMissingFacts(): void
    {
        $source = File\read(__DIR__ . '/../../Datasets/Wielcy/psb.6305.1.html');
        $source = Str\replace($source, '<div class="lewa-szpalta">', '<div>');
        Http::fake([WielcyScraper::url('psb.6305.1') => Http::response($source)]);

        $wielcy = $this->scraper->find('psb.6305.1');

        $this->assertSame('Gąsiorowski', $wielcy->surname);
        $this->assertNull($wielcy->birthDate);
        $this->assertNull($wielcy->burialPlace);
    }

    #[TestDox('it returns null when receives error response')]
    public function testErrorResponse(): void
    {
        Http::fake([WielcyScraper::url('psb.6305.1') => Http::response(status: 500)]);

        $this->assertNull($this->scraper->find('psb.6305.1'));
    }

    #[TestDox('it returns null when connection fails')]
    public function testConnectionError(): void
    {
        Http::fake([WielcyScraper::url('psb.6305.1') => Http::failedConnection()]);

        $this->assertNull($this->scraper->find('psb.6305.1'));
    }

    #[TestDox('it caches source from sejm-wielki.pl')]
    public function testCache(): void
    {
        Http::fake();

        Cache::shouldReceive('flexible')
            ->once()
            ->with('wielcy.psb.6305.1', Mockery::any(), Mockery::any())
            ->andReturn('');

        $this->scraper->find('psb.6305.1');

        Http::assertSentCount(0);
    }

    #[TestDox('it does not request anything for person without id')]
    public function testPersonWithoutId(): void
    {
        $person = Person::factory()->make(['id_wielcy' => null]);

        $this->assertNull($this->scraper->for($person));
    }
}
