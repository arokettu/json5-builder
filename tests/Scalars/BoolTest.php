<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Scalars;

use Arokettu\Json5\Json5Encoder;
use PHPUnit\Framework\TestCase;

class BoolTest extends TestCase
{
    public function testBool(): void
    {
        self::assertEquals("true\n", Json5Encoder::encode(true));
        self::assertEquals("false\n", Json5Encoder::encode(false));
    }
}
