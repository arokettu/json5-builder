<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Formatting;

use Arokettu\Json5\Json5Encoder;
use ArrayObject;
use PHPUnit\Framework\TestCase;

class EmptyTest extends TestCase
{
    public function testEmptyList(): void
    {
        self::assertEquals("[]\n", Json5Encoder::encode([]));
    }

    public function testEmptyObject(): void
    {
        self::assertEquals("{}\n", Json5Encoder::encode(new ArrayObject()));
    }
}
