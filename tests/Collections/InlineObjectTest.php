<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Collections;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\Options;
use Arokettu\Json5\Values\InlineObject;
use Arokettu\Json5\Values\Json5Serializable;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use SplFixedArray;
use stdClass;

class InlineObjectTest extends TestCase
{
    public function testArrayAccepted(): void
    {
        $list = new InlineObject([1, 2, 3, 4]); // even if list

        self::assertEquals(<<<JSON5
            { '0': 1, '1': 2, '2': 3, '3': 4 }

            JSON5, Json5Encoder::encode($list));
    }

    public function testStdClassAccepted(): void
    {
        $object = new stdClass();
        $object->a = 1;
        $object->z = 2;
        $object->x = 3;
        $object->{'4'} = 4;

        $list = new InlineObject($object);

        self::assertEquals(<<<JSON5
            { a: 1, z: 2, x: 3, '4': 4 }

            JSON5, Json5Encoder::encode($list));
    }

    public function testIterableAccepted(): void
    {
        $i = function () {
            yield 'a' => 1;
            yield 'b' => 2;
            yield 'c' => 3;
            yield 'b' => 4; // this is allowed but discouraged
        };

        $list = new InlineObject($i());

        self::assertEquals(<<<JSON5
            { a: 1, b: 2, c: 3, b: 4 }

            JSON5, Json5Encoder::encode($list));
    }

    public function testSupportJsonSerializable(): void
    {
        $arr = new SplFixedArray(4);
        $arr[0] = 1;
        $arr[1] = 2;
        $arr[2] = 3;
        $arr[3] = 4;

        $list = new InlineObject($arr);

        self::assertEquals(<<<JSON5
            { '0': 1, '1': 2, '2': 3, '3': 4 }

            JSON5, Json5Encoder::encode($list));
    }

    public function testSupportJson5Serializable(): void
    {
        $class = new class implements JsonSerializable, Json5Serializable {
            public function json5Serialize(): array // takes precedence
            {
                return [1,2,3];
            }

            public function jsonSerialize(): array
            {
                return [4,5];
            }
        };

        self::assertEquals(<<<JSON5
            { '0': 1, '1': 2, '2': 3 }

            JSON5, Json5Encoder::encode(new InlineObject($class)));
    }

    public function testInlineObjectOfArrays(): void
    {
        $obj = new InlineObject([
            'list1' => [1,2,3],
            'list2' => ['a', 'b', 'c'],
            'list3' => [[1,2], ['a' => 'b']],
        ]);

        self::assertEquals(<<<JSON5
            { list1: [
                1,
                2,
                3,
            ], list2: [
                "a",
                "b",
                "c",
            ], list3: [
                [
                    1,
                    2,
                ],
                {
                    a: "b",
                },
            ] }

            JSON5, Json5Encoder::encode($obj));
    }

    public function testArrayOfInlineObjects(): void
    {
        $list = [
            new InlineObject([1,2,3]),
            new InlineObject(['a' => 'b', 'c' => 'd']),
            new InlineObject(['list' => [1,2], 'obj' => ['a' => 123, 'b' => 456]]),
        ];

        self::assertEquals(<<<JSON5
            [
                { '0': 1, '1': 2, '2': 3 },
                { a: "b", c: "d" },
                { list: [
                    1,
                    2,
                ], obj: {
                    a: 123,
                    b: 456,
                } },
            ]

            JSON5, Json5Encoder::encode($list));
    }

    public function testNoExtraSpaces(): void
    {
        $list = new InlineObject([1, 2, 3, 4]); // even if list

        self::assertEquals(<<<JSON5
            {'0': 1, '1': 2, '2': 3, '3': 4}

            JSON5, Json5Encoder::encode($list, new Options(inlineObjectPadding: false)));
    }

    public function testJsonTransparency(): void
    {
        // array that is not a list
        $list1 = [1, 2, 3];
        self::assertEquals('{"0":1,"1":2,"2":3}', json_encode(new InlineObject($list1)));

        // iterable
        $list2 = fn () => yield from $list1;
        self::assertEquals('{"0":1,"1":2,"2":3}', json_encode(new InlineObject($list2())));
    }
}
