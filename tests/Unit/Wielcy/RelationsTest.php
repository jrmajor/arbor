<?php

namespace Tests\Unit\Wielcy;

use App\Models\Person;
use App\Services\Wielcy\Relative;
use App\Services\Wielcy\WielcyScraper;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Psl\File;
use Psl\Str;
use Tests\TestCase;

final class RelationsTest extends TestCase
{
    use UsesWielcyDataset;

    private WielcyScraper $scraper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scraper = $this->app->make(WielcyScraper::class);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    #[DataProvider('provideScrapeCases')]
    #[TestDox('it can load parents')]
    public function testParents(string $id, string $source, array $attributes): void
    {
        Http::fake([WielcyScraper::url($id) => Http::response($source)]);

        $wielcy = $this->scraper->find($id);

        foreach (['mother' => $wielcy->mother, 'father' => $wielcy->father] as $type => $parent) {
            if (! isset($attributes["{$type}Name"]) && ! isset($attributes["{$type}Surname"])) {
                $this->assertNull($parent);

                continue;
            }

            $this->assertInstanceOf(Relative::class, $parent);
            $this->assertSame($attributes["{$type}Id"], $parent->id);
            $this->assertSame($attributes["{$type}Name"], $parent->name);
            $this->assertSame($attributes["{$type}Surname"], $parent->surname);
            $this->assertNull($parent->person);
        }
    }

    #[TestDox('it links parents to people in arbor')]
    public function testArborPeople(): void
    {
        $source = File\read(__DIR__ . '/../../Datasets/Wielcy/psb.6305.1.html');
        Http::fake([WielcyScraper::url('psb.6305.1') => Http::response($source)]);

        [$fatherModel, $motherModel] = Person::factory(2)->sequence(
            ['id_wielcy' => 'psb.6305.2'],
            ['id_wielcy' => 'psb.6305.3'],
        )->create();

        $wielcy = $this->scraper->find('psb.6305.1');

        $this->assertSame($fatherModel->id, $wielcy->father?->person?->id);
        $this->assertSame(WielcyScraper::url('psb.6305.2'), $wielcy->father->url);
        $this->assertSame($motherModel->id, $wielcy->mother?->person?->id);
    }

    #[TestDox('it does not guess ids that were lowercased')]
    public function testLowercasedIds(): void
    {
        // without links to public pages, only lowercased ids from photo keys are available
        $source = File\read(__DIR__ . '/../../Datasets/Wielcy/cz.I017795.html');
        $source = Str\replace($source, 'href="/b/', 'href="/x/');
        Http::fake([WielcyScraper::url('cz.I017795') => Http::response($source)]);

        $father = $this->scraper->find('cz.I017795')->father;

        $this->assertNull($father->id);
        $this->assertNull($father->url);
        $this->assertSame('Zygmunt Leon', $father->name);
        $this->assertSame('hr. Komorowski z Komorowa h. Korczak', $father->surname);
    }
}
