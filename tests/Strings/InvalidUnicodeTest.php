<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Strings;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonCEncoder;
use Arokettu\Json5\JsonEncoder;
use PHPUnit\Framework\TestCase;
use ValueError;

class InvalidUnicodeTest extends TestCase
{
    public function testInvalidUnicodeJson5(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage(
            'Unable to encode value: Malformed UTF-8 characters, possibly incorrectly encoded'
        );

        Json5Encoder::encode("\xff");
    }

    public function testInvalidUnicodeJson(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage(
            'Unable to encode value: Malformed UTF-8 characters, possibly incorrectly encoded'
        );

        JsonEncoder::encode("\xff");
    }

    public function testInvalidUnicodeJsonC(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage(
            'Unable to encode value: Malformed UTF-8 characters, possibly incorrectly encoded'
        );

        JsonCEncoder::encode("\xff");
    }
}
