<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Scalars;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\Values\HexInteger;
use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase
{
    public function testInt(): void
    {
        self::assertEquals("123\n", Json5Encoder::encode(123));
        self::assertEquals("-123\n", Json5Encoder::encode(-123));
    }

    public function testHexInt(): void
    {
        self::assertEquals("0x123ACD\n", Json5Encoder::encode(new HexInteger(0x123acd)));
        self::assertEquals("-0x123ACD\n", Json5Encoder::encode(new HexInteger(-0x123acd)));

        // JSON is supported too
        self::assertEquals("1194701", json_encode(new HexInteger(0x123acd)));

        // 0
        self::assertEquals("0x0\n", Json5Encoder::encode(new HexInteger(0)));

        // PHP_INT_MAX
        $expect = '7F' . str_repeat('FF', PHP_INT_SIZE - 1);
        self::assertEquals("0x{$expect}\n", Json5Encoder::encode(new HexInteger(PHP_INT_MAX)));
        self::assertEquals("-0x{$expect}\n", Json5Encoder::encode(new HexInteger(-PHP_INT_MAX)));

        // todo: handle PHP_INT_MIN
    }
}
