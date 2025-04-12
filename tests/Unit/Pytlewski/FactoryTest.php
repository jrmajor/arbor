<?php

namespace Tests\Unit\Pytlewski;

use App\Services\Pytlewski\PytlewskiFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class FactoryTest extends TestCase
{
    use UsesPytlewskiDataset;

    private PytlewskiFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->app->make(PytlewskiFactory::class);
    }

    #[TestDox('it can make proper url')]
    public function testUrl(): void
    {
        $this->assertSame(
            'http://www.pytlewski.pl/index/drzewo/index.php?view=true&id=556',
            PytlewskiFactory::url(556),
        );
    }

    #[TestDox('it requests source from pytlewski.pl')]
    public function testSourceRequest(): void
    {
        Http::fake();

        $this->factory->find(556);

        Http::assertSent(
            fn ($request) => $request->url() === 'http://www.pytlewski.pl/index/drzewo/index.php?view=true&id=556',
        );
    }

    #[TestDox("returns null when it can't scrape received response")]
    public function testScrapeError(): void
    {
        Http::fake();

        $this->assertNull($this->factory->find(556));
    }

    #[TestDox('it returns null when receives error response')]
    public function testErrorResponse(): void
    {
        Http::fake([PytlewskiFactory::url(556) => Http::response(status: 404)]);

        $this->assertNull($this->factory->find(556));
    }

    #[TestDox('it caches parsed attributes from pytlewski.pl')]
    public function testCache(): void
    {
        Http::fake();

        Cache::shouldReceive('flexible')
            ->once()
            ->with('pytlewski.556', Mockery::any(), Mockery::any())
            ->andReturn('');

        $this->factory->find(556);

        Http::assertSentCount(0);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    #[DataProvider('provideScrapeCases')]
    #[TestDox('it properly scrapes pytlewski.pl')]
    public function testScrape(int $id, string $source, array $attributes): void
    {
        Http::fake([PytlewskiFactory::url($id) => Http::response($source)]);

        $pytlewski = $this->factory->find($id);

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
}
