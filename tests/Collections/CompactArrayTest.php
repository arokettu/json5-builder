<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Collections;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonCEncoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Values\CompactArray;
use PHPUnit\Framework\TestCase;
use stdClass;

class CompactArrayTest extends TestCase
{
    public function testArrayAccepted(): void
    {
        $list = new CompactArray(['a' => 1, 2, 8 => 3, 4]); // keys are ignored

        self::assertStringEqualsFile(__DIR__ . '/data/compact_array/compact_array.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/compact_array/compact_array.json', JsonEncoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/compact_array/compact_array.json', JsonCEncoder::encode($list));
    }

    public function testStdClassAccepted(): void
    {
        $object = new stdClass();
        $object->a = 1;
        $object->z = 2;
        $object->x = 3;
        $object->{'4'} = 4;

        $list = new CompactArray($object);

        self::assertStringEqualsFile(__DIR__ . '/data/compact_array/compact_array.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/compact_array/compact_array.json', JsonEncoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/compact_array/compact_array.json', JsonCEncoder::encode($list));
    }

    public function testIterableAccepted(): void
    {
        $i = function () {
            yield 1;
            yield 2;
            yield 3;
            yield 4;
        };

        $list = new CompactArray($i());
        self::assertStringEqualsFile(__DIR__ . '/data/compact_array/compact_array.json5', Json5Encoder::encode($list));

        $list = new CompactArray($i());
        self::assertStringEqualsFile(__DIR__ . '/data/compact_array/compact_array.json', JsonEncoder::encode($list));

        $list = new CompactArray($i());
        self::assertStringEqualsFile(__DIR__ . '/data/compact_array/compact_array.json', JsonCEncoder::encode($list));
    }

    public function testCompactArrayOfObjects(): void
    {
        $list = new CompactArray([
            ['a' => 1, 'b' => 2],
            ['abc' => '123', 'xyz' => '456'],
            ['key1' => 'value1', 'key2' => 'value2'],
            ['list' => [1], 'obj' => ['k' => 'v']],
        ]);

        self::assertStringEqualsFile(
            __DIR__ . '/data/compact_array/compact_array_of_objects.json5',
            Json5Encoder::encode($list),
        );
        self::assertStringEqualsFile(
            __DIR__ . '/data/compact_array/compact_array_of_objects.json',
            JsonEncoder::encode($list),
        );
        self::assertStringEqualsFile(
            __DIR__ . '/data/compact_array/compact_array_of_objects.json',
            JsonCEncoder::encode($list),
        );
    }

    public function testObjectOfCompactArrays(): void
    {
        $obj = [
            'list1' => new CompactArray([1,2,3]),
            'list2' => new CompactArray(['a', 'b', 'c']),
            'list3' => new CompactArray([[1,2], ['a' => 'b', 'c' => 'd']]),
        ];

        self::assertStringEqualsFile(
            __DIR__ . '/data/compact_array/object_of_compact_arrays.json5',
            Json5Encoder::encode($obj),
        );
        self::assertStringEqualsFile(
            __DIR__ . '/data/compact_array/object_of_compact_arrays.json',
            JsonEncoder::encode($obj),
        );
        self::assertStringEqualsFile(
            __DIR__ . '/data/compact_array/object_of_compact_arrays.json',
            JsonCEncoder::encode($obj),
        );
    }

    public function testJsonTransparency(): void
    {
        // array that is not a list
        $list1 = ['a' => 1, 'b' => 2, 'c' => 3];
        self::assertEquals('[1,2,3]', json_encode(new CompactArray($list1)));

        // iterable
        $list2 = fn () => yield from $list1;
        self::assertEquals('[1,2,3]', json_encode(new CompactArray($list2())));
    }
}
