<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Collections;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Options;
use Arokettu\Json5\Values\InlineObject;
use PHPUnit\Framework\TestCase;
use stdClass;

class InlineObjectTest extends TestCase
{
    private const DATA_DIR = __DIR__ . '/data/inline_object';

    public function testArrayAccepted(): void
    {
        $list = new InlineObject([1, 2, 3, 4]); // even if list

        self::assertStringEqualsFile(self::DATA_DIR . '/object_value_list.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(self::DATA_DIR . '/object_value_list.json', JsonEncoder::encode($list));
    }

    public function testStdClassAccepted(): void
    {
        $object = new stdClass();
        $object->a = 1;
        $object->z = 2;
        $object->x = 3;
        $object->{'4'} = 4;

        $objobj = new InlineObject($object);

        self::assertStringEqualsFile(self::DATA_DIR . '/object_value_stdclass.json5', Json5Encoder::encode($objobj));
        self::assertStringEqualsFile(self::DATA_DIR . '/object_value_stdclass.json', JsonEncoder::encode($objobj));
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
        self::assertStringEqualsFile(self::DATA_DIR . '/object_value_iterable.json5', Json5Encoder::encode($list));

        $list = new InlineObject($i());
        self::assertStringEqualsFile(self::DATA_DIR . '/object_value_iterable.json', JsonEncoder::encode($list));
    }

    public function testInlineObjectOfArrays(): void
    {
        $obj = new InlineObject([
            'list1' => [1,2,3],
            'list2' => ['a', 'b', 'c'],
            'list3' => [[1,2], ['a' => 'b']],
        ]);

        self::assertStringEqualsFile(self::DATA_DIR . '/inline_object_of_arrays.json5', Json5Encoder::encode($obj));
        self::assertStringEqualsFile(self::DATA_DIR . '/inline_object_of_arrays.json', JsonEncoder::encode($obj));
    }

    public function testArrayOfInlineObjects(): void
    {
        $list = [
            new InlineObject([1,2,3]),
            new InlineObject(['a' => 'b', 'c' => 'd']),
            new InlineObject(['list' => [1,2], 'obj' => ['a' => 123, 'b' => 456]]),
        ];

        self::assertStringEqualsFile(self::DATA_DIR . '/array_of_inline_objects.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(self::DATA_DIR . '/array_of_inline_objects.json', JsonEncoder::encode($list));
    }

    public function testNoExtraSpaces(): void
    {
        $list = new InlineObject([1, 2, 3, 4]); // even if list
        $options = new Options(inlineObjectPadding: false);

        self::assertStringEqualsFile(
            self::DATA_DIR . '/object_value_list_no_extra_spaces.json5',
            Json5Encoder::encode($list, $options),
        );
        self::assertStringEqualsFile(
            self::DATA_DIR . '/object_value_list_no_extra_spaces.json',
            JsonEncoder::encode($list, $options),
        );
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
