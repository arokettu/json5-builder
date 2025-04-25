<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Scalars;

use Arokettu\Json5\Encoder;
use PHPUnit\Framework\TestCase;

class NullTest extends TestCase
{
    public function testNull(): void
    {
        self::assertEquals("null\n", Encoder::encode(null));
    }
}
