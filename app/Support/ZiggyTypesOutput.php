<?php

namespace App\Support;

use Override;
use Stringable;
use Tighten\Ziggy\Output\Types as BaseTypesOutput;

final class ZiggyTypesOutput extends BaseTypesOutput implements Stringable
{
    #[Override]
    public function __toString(): string
    {
        $routes = $this->routes();

        return <<<JS
            declare module 'ziggy-js' {
              interface RouteList {$routes->toJson(JSON_PRETTY_PRINT)}
            }
            export { Ziggy };

            JS;
    }
}
