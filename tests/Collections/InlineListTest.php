<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Collections;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\Options;
use Arokettu\Json5\Values\InlineList;
use Arokettu\Json5\Values\Json5Serializable;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use SplFixedArray;
use stdClass;

class InlineListTest extends TestCase
{
    public function testArrayAccepted(): void
    {
        $list = new InlineList(['a' => 1, 2, 8 => 3, 4]); // keys are ignored

        self::assertEquals(<<<JSON5
            [1, 2, 3, 4]

            JSON5, Json5Encoder::encode($list));
    }

    public function testStdClassAccepted(): void
    {
        $object = new stdClass();
        $object->a = 1;
        $object->z = 2;
        $object->x = 3;
        $object->{'4'} = 4;

        $list = new InlineList($object);

        self::assertEquals(<<<JSON5
            [1, 2, 3, 4]

            JSON5, Json5Encoder::encode($list));
    }

    public function testIterableAccepted(): void
    {
        $i = function () {
            yield 1;
            yield 2;
            yield 3;
            yield 4;
        };

        $list = new InlineList($i());

        self::assertEquals(<<<JSON5
            [1, 2, 3, 4]

            JSON5, Json5Encoder::encode($list));
    }

    public function testSupportJsonSerializable(): void
    {
        $arr = new SplFixedArray(4);
        $arr[0] = 1;
        $arr[1] = 2;
        $arr[2] = 3;
        $arr[3] = 4;

        $list = new InlineList($arr);

        self::assertEquals(<<<JSON5
            [1, 2, 3, 4]

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
            [1, 2, 3]

            JSON5, Json5Encoder::encode(new InlineList($class)));
    }

    public function testInlineListOfObjects(): void
    {
        $list = new InlineList([
            ['a' => 1, 'b' => 2],
            ['abc' => '123', 'xyz' => '456'],
            ['key1' => 'value1', 'key2' => 'value2'],
            ['list' => [1], 'obj' => ['k' => 'v']],
        ]);

        self::assertEquals(<<<JSON5
            [{
                a: 1,
                b: 2,
            }, {
                abc: "123",
                xyz: "456",
            }, {
                key1: "value1",
                key2: "value2",
            }, {
                list: [
                    1,
                ],
                obj: {
                    k: "v",
                },
            }]

            JSON5, Json5Encoder::encode($list));
    }

    public function testObjectOfInlineLists(): void
    {
        $obj = [
            'list1' => new InlineList([1,2,3]),
            'list2' => new InlineList(['a', 'b', 'c']),
            'list3' => new InlineList([[1,2], ['a' => 'b', 'c' => 'd']]),
        ];

        self::assertEquals(<<<JSON5
            {
                list1: [1, 2, 3],
                list2: ["a", "b", "c"],
                list3: [[
                    1,
                    2,
                ], {
                    a: "b",
                    c: "d",
                }],
            }

            JSON5, Json5Encoder::encode($obj));
    }

    public function testExtraSpaces(): void
    {
        $list = new InlineList(['a' => 1, 2, 8 => 3, 4]);

        self::assertEquals(<<<JSON5
            [ 1, 2, 3, 4 ]

            JSON5, Json5Encoder::encode($list, new Options(inlineListPadding: true)));
    }

    public function testJsonTransparency(): void
    {
        // array that is not a list
        $list1 = ['a' => 1, 'b' => 2, 'c' => 3];
        self::assertEquals('[1,2,3]', json_encode(new InlineList($list1)));

        // iterable
        $list2 = fn () => yield from $list1;
        self::assertEquals('[1,2,3]', json_encode(new InlineList($list2())));
    }
}
