<?php

namespace Tests\Unit\Pytlewski;

use App\Services\Pytlewski\Pytlewski;
use Carbon\CarbonInterval;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class ModelTest extends TestCase
{
    use UsesPytlewskiDataset;

    #[TestDox('it can make proper url')]
    public function testUrl(): void
    {
        $this->assertSame(
            'http://www.pytlewski.pl/index/drzewo/index.php?view=true&id=556',
            Pytlewski::url(556),
        );
    }

    #[TestDox('it requests source from pytlewski.pl')]
    public function testSourceRequest(): void
    {
        Http::fake();

        Pytlewski::find(556);

        Http::assertSent(
            fn ($request) => $request->url() === 'http://www.pytlewski.pl/index/drzewo/index.php?view=true&id=556',
        );
    }

    #[TestDox("returns null when it can't scrape received response")]
    public function testScrapeError(): void
    {
        Http::fake();

        $this->assertNull(Pytlewski::find(556));
    }

    #[TestDox('it returns null when receives error response')]
    public function testErrorResponse(): void
    {
        Http::fake([
            Pytlewski::url(556) => Http::response(status: 404),
        ]);

        $this->assertNull(Pytlewski::find(556));
    }

    #[TestDox('it caches parsed attributes from pytlewski.pl')]
    public function testCache(): void
    {
        Http::fake();

        Cache::shouldReceive('remember')
            ->once()
            ->with('pytlewski.556', CarbonInterval::class, Closure::class)
            ->andReturn([]);

        Pytlewski::find(556);

        Http::assertSentCount(0);
    }

    /**
     * @dataProvider provideScrapeCases
     */
    #[TestDox('it properly scrapes pytlewski.pl')]
    public function testScrape(int $id, string $source, array $attributes): void
    {
        Http::fake([
            Pytlewski::url($id) => Http::response($source),
        ]);

        $pytlewski = Pytlewski::find($id);

        $keysToCheck = [
            'family_name', 'last_name', 'name', 'middle_name',
            'birth_date', 'birth_place', 'death_date', 'death_place',
            'photo', 'bio',
        ];

        foreach (Arr::only($attributes, $keysToCheck) as $key => $value) {
            $this->assertSame($value, $pytlewski->{$key});
        }
    }

    #[TestDox('it throws an exception when a key does not exist')]
    public function testInvalidKey(): void
    {
        Cache::shouldReceive('remember')->andReturn(require __DIR__ . '/../../Datasets/Pytlewscy/556.php');

        $pytlewski = Pytlewski::find(556);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Key [nonexistentKey] does not exist.');

        /** @phpstan-ignore-next-line */
        $pytlewski->nonexistentKey;
    }
}
