<?php

namespace Tests\Feature\Ajax;

use App\Services\Wielcy\WielcyScraper;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\TestDox;
use Psl\File;
use Tests\TestCase;

final class WielcyNameTest extends TestCase
{
    #[TestDox('guest can not access the endpoint')]
    public function testGuest(): void
    {
        $this->get('ajax/wielcy-name')->assertRedirect('login');
    }

    #[TestDox('it returns null when person is not found')]
    public function testNotFound(): void
    {
        $source = File\read(__DIR__ . '/../../Datasets/Wielcy/xx.999999999.html');
        Http::fake([WielcyScraper::url('xx.999999999') => Http::response($source)]);

        $this
            ->withPermissions(1)
            ->get('ajax/wielcy-name?id=xx.999999999')
            ->assertOk()
            ->assertExactJson(['result' => null]);
    }

    #[TestDox('it returns the name when person is found')]
    public function testOk(): void
    {
        $source = File\read(__DIR__ . '/../../Datasets/Wielcy/cz.I017795.html');
        Http::fake([WielcyScraper::url('cz.I017795') => Http::response($source)]);

        $this
            ->withPermissions(1)
            ->get('ajax/wielcy-name?id=cz.I017795')
            ->assertOk()
            ->assertExactJson(['result' => 'Bronisław Maria Karol hr. Komorowski z Komorowa h. Korczak']);
    }
}
