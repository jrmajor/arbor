<?php

namespace App\Http;

use Exception;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\Http;
use Inertia\Ssr\Gateway;
use Inertia\Ssr\Response;
use Tighten\Ziggy\Ziggy;

class InertiaHttpGateway implements Gateway
{
    public function __construct(
        private Vite $vite,
    ) { }

    /**
     * @phpstan-ignore missingType.iterableValue
     */
    public function dispatch(array $page): ?Response
    {
        if ($this->vite->isRunningHot()) {
            $url = file_get_contents($this->vite->hotFile()) . '/render';
        } else {
            return null;
        }

        $page['props']['ziggy'] = (new Ziggy())->toArray();

        try {
            $response = Http::post($url, $page)->throw()->json();
        } catch (Exception $e) {
            return null;
        }

        if ($response === null) {
            return null;
        }

        return new Response(
            implode("\n", $response['head']),
            $response['body'],
        );
    }
}
