<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Scalars;

use Arokettu\Json5\Encoder;
use Arokettu\Json5\Values\HexInteger;
use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase
{
    public function testInt(): void
    {
        self::assertEquals("123\n", Encoder::encode(123));
        self::assertEquals("-123\n", Encoder::encode(-123));
    }

    public function testHexInt(): void
    {
        self::assertEquals("0x123\n", Encoder::encode(new HexInteger(0x123)));
        self::assertEquals("-0x123\n", Encoder::encode(new HexInteger(-0x123)));
    }
}
