<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\TestDox;
use RuntimeException;
use Tests\TestCase;

final class PasswordResetLinkTest extends TestCase
{
    #[TestDox('reset link is sent')]
    public function testSent(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post('forgot-password', ['email' => $user->email])
            ->assertFound()
            ->assertRedirect('people');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    #[TestDox('mailer failure shows an error and does not throttle retries')]
    public function testFailure(): void
    {
        Notification::shouldReceive('send')->once()->andThrow(new RuntimeException('mailer down'));
        $user = User::factory()->create();

        $this->from('forgot-password')
            ->post('forgot-password', ['email' => $user->email])
            ->assertFound()
            ->assertRedirect('forgot-password')
            ->assertSessionHasErrors(['email' => __('passwords.failed')]);

        Notification::fake();

        $this->post('forgot-password', ['email' => $user->email])
            ->assertRedirect('people');

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
