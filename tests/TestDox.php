<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE file:
 * https://github.com/sebastianbergmann/phpunit/blob/54ff19448afae577c0fe3de1269ecf4d3cd9c23e/LICENSE.
 */

namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class TestDox
{
    public function __construct(
        private string $text,
    ) { }

    public function text(): string
    {
        return $this->text;
    }
}
