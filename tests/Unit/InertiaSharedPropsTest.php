<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class InertiaSharedPropsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('web')->get(
            'inertia-shared-props-test',
            fn () => Inertia::render('InertiaSharedPropsTest', []),
        );
    }

    #[TestDox('it shares errors and guest from request')]
    public function testShareGuest(): void
    {
        $this
            ->get('inertia-shared-props-test')
            ->assertOk()
            ->assertInertia(function (Assert $page) {
                $this->assertSame([
                    'errors' => [],
                    'user' => null,
                ], $page->toArray()['props']);
            });
    }

    #[TestDox('it shares errors and user from request')]
    public function testShareUser(): void
    {
        $this
            ->actingAs($user = User::factory()->createOne())
            ->get('inertia-shared-props-test')
            ->assertOk()
            ->assertInertia(function (Assert $page) use ($user) {
                $this->assertSame([
                    'errors' => [],
                    'user' => [
                        'username' => $user->username,
                    ],
                ], $page->toArray()['props']);
            });
    }
}
