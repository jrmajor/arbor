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
            'appName' => config('app.name'),
            'currentYear' => now()->year,
            'currentLocale' => $request->getLocale(),
            'fallbackLocale' => config('app.fallback_locale'),
            'availableLocales' => config('app.available_locales'),
            'flash' => $this->getFlash(),
            'activeRoute' => $request->route()->getName(),
            'user' => $this->getUser($request),
        ];
    }

    /**
     * @return ?array<string, string>
     */
    public function getFlash(): ?array
    {
        if (! $message = flash()->getMessage()) {
            return null;
        }

        return [
            'id' => $message->class,
            'level' => $message->level,
            'message' => $message->message,
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
            'email' => $user->email,
            'canWrite' => $user->canWrite(),
            'isSuperAdmin' => $user->isSuperAdmin(),
        ];
    }
}
