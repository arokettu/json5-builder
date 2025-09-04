<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Json5\Tests;

use Arokettu\Json5\Options;
use Error;
use PHPUnit\Framework\TestCase;

final class OptionsPropertiesTest extends TestCase
{
    public function testUnknownPropertySet(): void
    {
        $options = new Options();

        self::expectException(Error::class);
        self::expectExceptionMessage('No such property: unknown');

        $options->unknown = 123;
    }

    public function testUnknownPropertyGet(): void
    {
        $options = new Options();

        self::expectException(Error::class);
        self::expectExceptionMessage('No such property: unknown');

        var_dump($options->unknown);
    }
}
