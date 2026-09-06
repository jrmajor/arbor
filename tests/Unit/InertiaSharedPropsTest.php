<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

use function App\flash;

final class InertiaSharedPropsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('web')->get(
            'inertia-shared-props-test',
            fn () => Inertia::render('SharedPropsTest', []),
        )->name('test.inertiaProps');
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
                    'appName' => config('app.name'),
                    'currentYear' => now()->year,
                    'currentLocale' => 'en',
                    'fallbackLocale' => 'en',
                    'activeRoute' => 'test.inertiaProps',
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
                    'appName' => config('app.name'),
                    'currentYear' => now()->year,
                    'currentLocale' => 'en',
                    'fallbackLocale' => 'en',
                    'activeRoute' => 'test.inertiaProps',
                    'user' => [
                        'username' => $user->username,
                        'email' => $user->email,
                        'canWrite' => false,
                        'isSuperAdmin' => false,
                    ],
                ], $page->toArray()['props']);
            });
    }

    #[TestDox('it sends flash data')]
    public function testFlashAfterRedirect(): void
    {
        Route::middleware('web')->post('inertia-flash-test', function () {
            flash('success', 'people.alerts.changes_have_been_saved');

            return redirect('inertia-shared-props-test');
        });

        $this->post('inertia-flash-test')->assertRedirect('inertia-shared-props-test');

        $this->get('inertia-shared-props-test')
            ->assertOk()
            ->assertInertia(function (Assert $page) {
                $page->hasFlash('notification.level', 'success')
                    ->hasFlash('notification.message', 'Changes have been saved.');
            });

        $this->get('inertia-shared-props-test')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->missingFlash('notification'));
    }
}
