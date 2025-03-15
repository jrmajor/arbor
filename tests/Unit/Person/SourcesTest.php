<?php

namespace Tests\Unit\Person;

use App\Models\Person;
use App\Services\Sources\Source;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class SourcesTest extends TestCase
{
    #[TestDox('it casts sources to collection')]
    public function testCast(): void
    {
        $sources = Person::factory()->create([
            'sources' => null,
        ])->sources;

        $this->assertCount(0, $sources);

        $sources = Person::factory()->create([
            'sources' => [],
        ])->sources;

        $this->assertCount(0, $sources);

        $sources = Person::factory()->create([
            'sources' => [
                '[Henryk Gąsiorowski](https://pl.wikipedia.org/wiki/Henryk_G%C4%85siorowski) w Wikipedii, wolnej encyklopedii, dostęp 2020-06-06',
                'Ignacy Płażewski, *Spojrzenie w przeszłość polskiej fotografii*, Warszawa, PIW, 1982, ISBN 83-06-00100-1',
            ],
        ])->sources;

        $this->assertCount(2, $sources);
    }

    #[TestDox('sources are sanitized')]
    public function testSanitization(): void
    {
        $raw = [
            'a' => "     [Henryk    Gąsiorowski](https://pl.wikipedia.org/wiki/Henryk_G%C4%85siorowski)  \t   w Wikipedii,\nwolnej encyklopedii, dostęp 2020-06-06\r\n",
            'b' => "    \n",
            'Ignacy Płażewski, *Spojrzenie w przeszłość polskiej fotografii*, Warszawa, PIW, 1982, ISBN 83-06-00100-1',
        ];

        $sanitized = [
            '[Henryk Gąsiorowski](https://pl.wikipedia.org/wiki/Henryk_G%C4%85siorowski) w Wikipedii, wolnej encyklopedii, dostęp 2020-06-06',
            'Ignacy Płażewski, *Spojrzenie w przeszłość polskiej fotografii*, Warszawa, PIW, 1982, ISBN 83-06-00100-1',
        ];

        $person = Person::factory()->create(['sources' => $raw]);
        $sources = $person->sources->map(fn (Source $s) => $s->raw())->all();

        $this->assertSame($sanitized, $sources);
    }
}
