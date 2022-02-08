<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class WelcomePageTest extends TestCase
{
    #[TestDox('it can be accessed by guest')]
    public function testGuest(): void
    {
        $this->get('/')->assertRedirect('people');
    }

    #[TestDox('it redirects authorized users')]
    public function testUser(): void
    {
        $this->withPermissions(0)->get('/')->assertRedirect('/people');
    }
}
