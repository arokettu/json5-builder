<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Formatting;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\Values\CompactArray;
use Arokettu\Json5\Values\CompactObject;
use Arokettu\Json5\Values\InlineArray;
use Arokettu\Json5\Values\InlineObject;
use Arokettu\Json5\Values\ObjectValue;
use PHPUnit\Framework\TestCase;
use stdClass;

class EmptyTest extends TestCase
{
    public function testEmptyList(): void
    {
        self::assertEquals("[]\n", Json5Encoder::encode([]));
        self::assertEquals("[]\n", Json5Encoder::encode(new InlineArray([])));
        self::assertEquals("[]\n", Json5Encoder::encode(new CompactArray([])));
    }

    public function testEmptyObject(): void
    {
        self::assertEquals("{}\n", Json5Encoder::encode(new ObjectValue([])));
        self::assertEquals("{}\n", Json5Encoder::encode(new InlineObject([])));
        self::assertEquals("{}\n", Json5Encoder::encode(new CompactObject([])));
    }

    public function testSecondLevelEmptyList(): void
    {
        self::assertEquals("[\n    [],\n]\n", Json5Encoder::encode([[]]));
        self::assertEquals("[\n    [],\n]\n", Json5Encoder::encode([new InlineArray([])]));
        self::assertEquals("[\n    [],\n]\n", Json5Encoder::encode([new CompactArray([])]));
    }

    public function testSecondLevelEmptyObject(): void
    {
        self::assertEquals("[\n    {},\n]\n", Json5Encoder::encode([new stdClass()]));
        self::assertEquals("[\n    {},\n]\n", Json5Encoder::encode([new InlineObject(new stdClass())]));
        self::assertEquals("[\n    {},\n]\n", Json5Encoder::encode([new CompactObject(new stdClass())]));
    }
}
