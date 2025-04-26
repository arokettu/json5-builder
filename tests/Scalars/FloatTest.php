<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Scalars;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\Options;
use PHPUnit\Framework\TestCase;

class FloatTest extends TestCase
{
    public function testFloat(): void
    {
        self::assertEquals("123.45\n", Json5Encoder::encode(123.45));
        self::assertEquals("-123.45\n", Json5Encoder::encode(-123.45));

        // big & small
        self::assertEquals("1.234e+32\n", Json5Encoder::encode(123400000000000000000000000000000.0));
        self::assertEquals("1.234e-30\n", Json5Encoder::encode(0.000000000000000000000000000001234));
    }

    public function testExtendedFloat(): void
    {
        // values supported by JSON5 but not JSON
        self::assertEquals("NaN\n", Json5Encoder::encode(NAN));
        self::assertEquals("Infinity\n", Json5Encoder::encode(+INF));
        self::assertEquals("-Infinity\n", Json5Encoder::encode(-INF));
    }

    public function testZeroFraction(): void
    {
        // default
        self::assertEquals("123\n", Json5Encoder::encode(123));
        self::assertEquals("123\n", Json5Encoder::encode((float)123));

        $options = new Options(preserveZeroFraction: true);

        self::assertEquals("123\n", Json5Encoder::encode(123, $options));
        self::assertEquals("123.0\n", Json5Encoder::encode((float)123, $options));
    }
}
