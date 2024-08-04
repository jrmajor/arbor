<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'user' => $this->getUser($request),
        ];
    }

    /**
     * @return ?array<string, mixed>
     */
    private function getUser(Request $request): ?array
    {
        if (! $user = $request->user()) {
            return null;
        }

        return [
            'username' => $user->username,
        ];
    }
}
