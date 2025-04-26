<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests;

use Arokettu\Json5\Json5Encoder;
use Exception;
use PHPUnit\Framework\TestCase;
use TypeError;

class UnsupportedTypesTest extends TestCase
{
    public function testResource(): void
    {
        $resource = fopen('php://temp', 'r+');

        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: resource (stream)');

        Json5Encoder::encode($resource);
    }

    public function testUnknownObject(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: Exception');

        Json5Encoder::encode(new Exception());
    }
}
