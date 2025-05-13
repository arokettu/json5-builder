<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Scalars;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Values\HexInteger;
use PHPUnit\Framework\TestCase;
use ValueError;

class IntegerTest extends TestCase
{
    public function testInt(): void
    {
        self::assertEquals("123\n", Json5Encoder::encode(123));
        self::assertEquals("-123\n", Json5Encoder::encode(-123));

        self::assertEquals("123\n", JsonEncoder::encode(123));
        self::assertEquals("-123\n", JsonEncoder::encode(-123));
    }

    public function testHexInt(): void
    {
        self::assertEquals("0x123ACD\n", Json5Encoder::encode(new HexInteger(0x123acd)));
        self::assertEquals("-0x123ACD\n", Json5Encoder::encode(new HexInteger(-0x123acd)));
        self::assertEquals("0x00123ACD\n", Json5Encoder::encode(new HexInteger(0x123acd, 8)));
        self::assertEquals("-0x00123ACD\n", Json5Encoder::encode(new HexInteger(-0x123acd, 8)));

        self::assertEquals("1194701\n", JsonEncoder::encode(new HexInteger(0x123acd)));
        self::assertEquals("-1194701\n", JsonEncoder::encode(new HexInteger(-0x123acd)));
        self::assertEquals("1194701\n", JsonEncoder::encode(new HexInteger(0x123acd, 8)));
        self::assertEquals("-1194701\n", JsonEncoder::encode(new HexInteger(-0x123acd, 8)));

        // 0
        self::assertEquals("0x0\n", Json5Encoder::encode(new HexInteger(0)));
        self::assertEquals("0x00000000\n", Json5Encoder::encode(new HexInteger(0, 8)));

        self::assertEquals("0\n", JsonEncoder::encode(new HexInteger(0)));
        self::assertEquals("0\n", JsonEncoder::encode(new HexInteger(0, 8)));

        // PHP_INT_MAX
        $expectHex = '7F' . str_repeat('FF', PHP_INT_SIZE - 1);
        $expectInt = (string)PHP_INT_MAX;

        self::assertEquals("0x{$expectHex}\n", Json5Encoder::encode(new HexInteger(PHP_INT_MAX)));
        self::assertEquals("-0x{$expectHex}\n", Json5Encoder::encode(new HexInteger(-PHP_INT_MAX)));

        self::assertEquals("{$expectInt}\n", JsonEncoder::encode(new HexInteger(PHP_INT_MAX)));
        self::assertEquals("-{$expectInt}\n", JsonEncoder::encode(new HexInteger(-PHP_INT_MAX)));

        // todo: handle PHP_INT_MIN
    }

    public function testNegativePadding(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage('Padding must be a non-negative integer');

        new HexInteger(0, -4);
    }

    public function testJsonEncodeTransparency(): void
    {
        self::assertEquals('1194701', json_encode(new HexInteger(0x123acd)));
    }
}
