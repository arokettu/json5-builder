<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Formatting;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonCEncoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Values\CompactArray;
use Arokettu\Json5\Values\CompactObject;
use Arokettu\Json5\Values\InlineArray;
use Arokettu\Json5\Values\InlineObject;
use Arokettu\Json5\Values\ObjectValue;
use PHPUnit\Framework\TestCase;
use stdClass;

final class EmptyTest extends TestCase
{
    public function testEmptyArray(): void
    {
        self::assertEquals("[]\n", Json5Encoder::encode([]));
        self::assertEquals("[]\n", Json5Encoder::encode(new InlineArray([])));
        self::assertEquals("[]\n", Json5Encoder::encode(new CompactArray([])));

        self::assertEquals("[]\n", JsonEncoder::encode([]));
        self::assertEquals("[]\n", JsonEncoder::encode(new InlineArray([])));
        self::assertEquals("[]\n", JsonEncoder::encode(new CompactArray([])));

        self::assertEquals("[]\n", JsonCEncoder::encode([]));
        self::assertEquals("[]\n", JsonCEncoder::encode(new InlineArray([])));
        self::assertEquals("[]\n", JsonCEncoder::encode(new CompactArray([])));
    }

    public function testEmptyObject(): void
    {
        self::assertEquals("{}\n", Json5Encoder::encode(new ObjectValue([])));
        self::assertEquals("{}\n", Json5Encoder::encode(new InlineObject([])));
        self::assertEquals("{}\n", Json5Encoder::encode(new CompactObject([])));

        self::assertEquals("{}\n", JsonEncoder::encode(new ObjectValue([])));
        self::assertEquals("{}\n", JsonEncoder::encode(new InlineObject([])));
        self::assertEquals("{}\n", JsonEncoder::encode(new CompactObject([])));

        self::assertEquals("{}\n", JsonCEncoder::encode(new ObjectValue([])));
        self::assertEquals("{}\n", JsonCEncoder::encode(new InlineObject([])));
        self::assertEquals("{}\n", JsonCEncoder::encode(new CompactObject([])));
    }

    public function testSecondLevelEmptyArray(): void
    {
        self::assertEquals("[\n    [],\n]\n", Json5Encoder::encode([[]]));
        self::assertEquals("[\n    [],\n]\n", Json5Encoder::encode([new InlineArray([])]));
        self::assertEquals("[\n    [],\n]\n", Json5Encoder::encode([new CompactArray([])]));

        self::assertEquals("[\n    []\n]\n", JsonEncoder::encode([[]]));
        self::assertEquals("[\n    []\n]\n", JsonEncoder::encode([new InlineArray([])]));
        self::assertEquals("[\n    []\n]\n", JsonEncoder::encode([new CompactArray([])]));

        self::assertEquals("[\n    []\n]\n", JsonCEncoder::encode([[]]));
        self::assertEquals("[\n    []\n]\n", JsonCEncoder::encode([new InlineArray([])]));
        self::assertEquals("[\n    []\n]\n", JsonCEncoder::encode([new CompactArray([])]));
    }

    public function testSecondLevelEmptyObject(): void
    {
        self::assertEquals("[\n    {},\n]\n", Json5Encoder::encode([new stdClass()]));
        self::assertEquals("[\n    {},\n]\n", Json5Encoder::encode([new InlineObject(new stdClass())]));
        self::assertEquals("[\n    {},\n]\n", Json5Encoder::encode([new CompactObject(new stdClass())]));

        self::assertEquals("[\n    {}\n]\n", JsonEncoder::encode([new stdClass()]));
        self::assertEquals("[\n    {}\n]\n", JsonEncoder::encode([new InlineObject(new stdClass())]));
        self::assertEquals("[\n    {}\n]\n", JsonEncoder::encode([new CompactObject(new stdClass())]));

        self::assertEquals("[\n    {}\n]\n", JsonCEncoder::encode([new stdClass()]));
        self::assertEquals("[\n    {}\n]\n", JsonCEncoder::encode([new InlineObject(new stdClass())]));
        self::assertEquals("[\n    {}\n]\n", JsonCEncoder::encode([new CompactObject(new stdClass())]));
    }
}
