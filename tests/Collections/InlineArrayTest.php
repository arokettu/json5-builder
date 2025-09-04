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
use Arokettu\Json5\Options;
use Arokettu\Json5\Values\InlineArray;
use PHPUnit\Framework\TestCase;
use stdClass;

final class InlineArrayTest extends TestCase
{
    public function testArrayAccepted(): void
    {
        $list = new InlineArray(['a' => 1, 2, 8 => 3, 4]); // keys are ignored

        self::assertStringEqualsFile(__DIR__ . '/data/inline_array/inline_array.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/inline_array/inline_array.json', JsonEncoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/inline_array/inline_array.json', JsonCEncoder::encode($list));
    }

    public function testStdClassAccepted(): void
    {
        $object = new stdClass();
        $object->a = 1;
        $object->z = 2;
        $object->x = 3;
        $object->{'4'} = 4;

        $list = new InlineArray($object);

        self::assertStringEqualsFile(__DIR__ . '/data/inline_array/inline_array.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/inline_array/inline_array.json', JsonEncoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/inline_array/inline_array.json', JsonCEncoder::encode($list));
    }

    public function testIterableAccepted(): void
    {
        $i = static function () {
            yield 1;
            yield 2;
            yield 3;
            yield 4;
        };

        $list = new InlineArray($i());
        self::assertStringEqualsFile(__DIR__ . '/data/inline_array/inline_array.json5', Json5Encoder::encode($list));

        $list = new InlineArray($i());
        self::assertStringEqualsFile(__DIR__ . '/data/inline_array/inline_array.json', JsonEncoder::encode($list));

        $list = new InlineArray($i());
        self::assertStringEqualsFile(__DIR__ . '/data/inline_array/inline_array.json', JsonCEncoder::encode($list));
    }

    public function testInlineArrayOfObjects(): void
    {
        $list = new InlineArray([
            ['a' => 1, 'b' => 2],
            ['abc' => '123', 'xyz' => '456'],
            ['key1' => 'value1', 'key2' => 'value2'],
            ['list' => [1], 'obj' => ['k' => 'v']],
        ]);

        self::assertStringEqualsFile(
            __DIR__ . '/data/inline_array/inline_array_of_objects.json5',
            Json5Encoder::encode($list),
        );
        self::assertStringEqualsFile(
            __DIR__ . '/data/inline_array/inline_array_of_objects.json',
            JsonEncoder::encode($list),
        );
        self::assertStringEqualsFile(
            __DIR__ . '/data/inline_array/inline_array_of_objects.json',
            JsonCEncoder::encode($list),
        );
    }

    public function testObjectOfInlineArrays(): void
    {
        $obj = [
            'list1' => new InlineArray([1,2,3]),
            'list2' => new InlineArray(['a', 'b', 'c']),
            'list3' => new InlineArray([[1,2], ['a' => 'b', 'c' => 'd']]),
        ];

        self::assertStringEqualsFile(
            __DIR__ . '/data/inline_array/object_of_inline_arrays.json5',
            Json5Encoder::encode($obj),
        );
        self::assertStringEqualsFile(
            __DIR__ . '/data/inline_array/object_of_inline_arrays.json',
            JsonEncoder::encode($obj),
        );
        self::assertStringEqualsFile(
            __DIR__ . '/data/inline_array/object_of_inline_arrays.json',
            JsonCEncoder::encode($obj),
        );
    }

    public function testExtraSpaces(): void
    {
        $list = new InlineArray(['a' => 1, 2, 8 => 3, 4]);

        self::assertStringEqualsFile(
            __DIR__ . '/data/inline_array/inline_array_pad.json5',
            Json5Encoder::encode($list, new Options(inlineArrayPadding: true)),
        );
        self::assertStringEqualsFile(
            __DIR__ . '/data/inline_array/inline_array_pad.json',
            JsonEncoder::encode($list, new Options(inlineArrayPadding: true)),
        );
        self::assertStringEqualsFile(
            __DIR__ . '/data/inline_array/inline_array_pad.json',
            JsonCEncoder::encode($list, new Options(inlineArrayPadding: true)),
        );
    }

    public function testJsonTransparency(): void
    {
        // array that is not a list
        $list1 = ['a' => 1, 'b' => 2, 'c' => 3];
        self::assertEquals('[1,2,3]', json_encode(new InlineArray($list1)));

        // iterable
        $list2 = static fn () => yield from $list1;
        self::assertEquals('[1,2,3]', json_encode(new InlineArray($list2())));
    }
}
