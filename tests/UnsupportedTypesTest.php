<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonCEncoder;
use Arokettu\Json5\JsonEncoder;
use Exception;
use PHPUnit\Framework\TestCase;
use TypeError;

class UnsupportedTypesTest extends TestCase
{
    public function testResourceJson5(): void
    {
        $resource = fopen('php://temp', 'r+');

        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: resource (stream)');

        Json5Encoder::encode($resource);
    }

    public function testResourceJson(): void
    {
        $resource = fopen('php://temp', 'r+');

        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: resource (stream)');

        JsonEncoder::encode($resource);
    }

    public function testResourceJsonC(): void
    {
        $resource = fopen('php://temp', 'r+');

        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: resource (stream)');

        JsonCEncoder::encode($resource);
    }

    public function testUnknownObjectJson5(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: Exception');

        Json5Encoder::encode(new Exception());
    }

    public function testUnknownObjectJson(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: Exception');

        JsonEncoder::encode(new Exception());
    }

    public function testUnknownObjectJsonC(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: Exception');

        JsonCEncoder::encode(new Exception());
    }
}
