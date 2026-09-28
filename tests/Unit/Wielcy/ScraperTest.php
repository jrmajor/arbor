<?php

namespace Tests\Unit\Wielcy;

use App\Models\Person;
use App\Services\Wielcy\WielcyScraper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Mockery;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class ScraperTest extends TestCase
{
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
            'http://www.sejm-wielki.pl/s/?m=NG&t=PN&n=psb.6305.1',
            WielcyScraper::url('psb.6305.1'),
        );
    }

    #[TestDox('it scrapes name and sex')]
    public function testScrape(): void
    {
        $source = <<<'EOD'
            <meta property='og:title' content='Henryk Gąsiorowski' />
            <img src="images/male.png" width="13" height="13"
            alt="M" align=left>
            EOD;

        Http::fake([
            WielcyScraper::url('psb.6305.1') => Http::response(iconv('UTF-8', 'ISO-8859-2', $source)),
        ]);

        $wielcy = $this->scraper->find('psb.6305.1');

        $this->assertSame('psb.6305.1', $wielcy->id);
        $this->assertSame(WielcyScraper::url('psb.6305.1'), $wielcy->url);
        $this->assertSame('Henryk Gąsiorowski', $wielcy->name);
        $this->assertSame('xy', $wielcy->sex);
    }

    #[TestDox('it returns empty attributes when it cannot scrape the response')]
    public function testScrapeError(): void
    {
        Http::fake();

        $wielcy = $this->scraper->find('psb.6305.1');

        $this->assertNull($wielcy->name);
        $this->assertNull($wielcy->sex);
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

    #[TestDox('it caches source from wielcy.pl')]
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
