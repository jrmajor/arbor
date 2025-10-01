<?php

namespace App\Http;

use Exception;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\Http;
use Inertia\Ssr\BundleDetector;
use Inertia\Ssr\Gateway;
use Inertia\Ssr\Response;
use Tighten\Ziggy\Ziggy;

class InertiaHttpGateway implements Gateway
{
    public function __construct(
        private Vite $vite,
    ) { }

    public function dispatch(array $page): ?Response
    {
        if ($this->vite->isRunningHot()) {
            $url = file_get_contents($this->vite->hotFile()) . '/render';
        } elseif (config('inertia.ssr.enabled', true) && (new BundleDetector())->detect()) {
            $url = str_replace('/render', '', config('inertia.ssr.url', 'http://127.0.0.1:13714')) . '/render';
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
