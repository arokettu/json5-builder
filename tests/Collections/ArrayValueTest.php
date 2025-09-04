<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Collections;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonCEncoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Values\ArrayValue;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ArrayValueTest extends TestCase
{
    public function testArrayAccepted(): void
    {
        $list = new ArrayValue(['a' => 1, 2, 8 => 3, 4]); // keys are ignored

        self::assertStringEqualsFile(__DIR__ . '/data/array_value/array_value.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/array_value/array_value.json', JsonEncoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/array_value/array_value.json', JsonCEncoder::encode($list));
    }

    public function testStdClassAccepted(): void
    {
        $object = new stdClass();
        $object->a = 1;
        $object->z = 2;
        $object->x = 3;
        $object->{'4'} = 4;

        $list = new ArrayValue($object);

        self::assertStringEqualsFile(__DIR__ . '/data/array_value/array_value.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/array_value/array_value.json', JsonEncoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/array_value/array_value.json', JsonCEncoder::encode($list));
    }

    public function testIterableAccepted(): void
    {
        $i = static function () {
            yield 1;
            yield 2;
            yield 3;
            yield 4;
        };

        $list = new ArrayValue($i());
        self::assertStringEqualsFile(__DIR__ . '/data/array_value/array_value.json5', Json5Encoder::encode($list));

        $list = new ArrayValue($i());
        self::assertStringEqualsFile(__DIR__ . '/data/array_value/array_value.json', JsonEncoder::encode($list));

        $list = new ArrayValue($i());
        self::assertStringEqualsFile(__DIR__ . '/data/array_value/array_value.json', JsonCEncoder::encode($list));
    }

    public function testJsonTransparency(): void
    {
        // array that is not a list
        $list1 = ['a' => 1, 'b' => 2, 'c' => 3];
        self::assertEquals('[1,2,3]', json_encode(new ArrayValue($list1)));

        // iterable
        $list2 = static fn () => yield from $list1;
        self::assertEquals('[1,2,3]', json_encode(new ArrayValue($list2())));
    }
}
