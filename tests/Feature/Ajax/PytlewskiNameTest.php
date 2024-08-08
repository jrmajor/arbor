<?php

namespace Tests\Feature;

use App\Services\Pytlewski\PytlewskiFactory;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\TestDox;
use Psl\File;
use Tests\TestCase;

final class PytlewskiNameTest extends TestCase
{
    #[TestDox('guest can not access the endpoint')]
    public function testGuest(): void
    {
        $this->get('ajax/pytlewski-name')->assertRedirect('login');
    }

    #[TestDox('it returns null when person is not found')]
    public function testNotFound(): void
    {
        Http::fake([PytlewskiFactory::url(556) => Http::response(status: 404)]);

        $this
            ->withPermissions(1)
            ->get('ajax/pytlewski-name?id=556')
            ->assertOk()
            ->assertExactJson(['result' => null]);
    }

    #[TestDox('it returns the name when person is found')]
    public function testOk(): void
    {
        $source = File\read(__DIR__ . '/../../Datasets/Pytlewscy/543.html');
        Http::fake([PytlewskiFactory::url(543) => Http::response($source)]);

        $this
            ->withPermissions(543)
            ->get('ajax/pytlewski-name?id=543')
            ->assertOk()
            ->assertExactJson(['result' => 'Franciszek Kosela']);
    }
}
