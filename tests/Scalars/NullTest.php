<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Scalars;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonEncoder;
use PHPUnit\Framework\TestCase;

class NullTest extends TestCase
{
    public function testNull(): void
    {
        self::assertEquals("null\n", Json5Encoder::encode(null));

        self::assertEquals("null\n", JsonEncoder::encode(null));
    }
}
