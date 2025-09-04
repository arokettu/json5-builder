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
use Arokettu\Json5\Values\CompactObject;
use PHPUnit\Framework\TestCase;
use stdClass;

final class CompactObjectTest extends TestCase
{
    private const DATA_DIR = __DIR__ . '/data/compact_object';

    public function testArrayAccepted(): void
    {
        $list = new CompactObject([1, 2, 3, 4]); // even if list
        $options = new Options(keyQuotes: Options\Quotes::Single);

        self::assertStringEqualsFile(
            self::DATA_DIR . '/object_value_list.json5',
            Json5Encoder::encode($list, $options),
        );
        self::assertStringEqualsFile(
            self::DATA_DIR . '/object_value_list.json',
            JsonEncoder::encode($list, $options),
        );
        self::assertStringEqualsFile(
            self::DATA_DIR . '/object_value_list.json',
            JsonCEncoder::encode($list, $options),
        );
    }

    public function testStdClassAccepted(): void
    {
        $object = new stdClass();
        $object->a = 1;
        $object->z = 2;
        $object->x = 3;
        $object->{'4'} = 4;

        $objobj = new CompactObject($object);
        $options = new Options(keyQuotes: Options\Quotes::Single);

        self::assertStringEqualsFile(
            self::DATA_DIR . '/object_value_stdclass.json5',
            Json5Encoder::encode($objobj, $options),
        );
        self::assertStringEqualsFile(
            self::DATA_DIR . '/object_value_stdclass.json',
            JsonEncoder::encode($objobj, $options),
        );
        self::assertStringEqualsFile(
            self::DATA_DIR . '/object_value_stdclass.json',
            JsonCEncoder::encode($objobj, $options),
        );
    }

    public function testIterableAccepted(): void
    {
        $i = static function () {
            yield 'a' => 1;
            yield 'b' => 2;
            yield 'c' => 3;
            yield 'b' => 4; // this is allowed but discouraged
        };

        $obj = new CompactObject($i());
        self::assertStringEqualsFile(self::DATA_DIR . '/object_value_iterable.json5', Json5Encoder::encode($obj));

        $obj = new CompactObject($i());
        self::assertStringEqualsFile(self::DATA_DIR . '/object_value_iterable.json', JsonEncoder::encode($obj));

        $obj = new CompactObject($i());
        self::assertStringEqualsFile(self::DATA_DIR . '/object_value_iterable.json', JsonCEncoder::encode($obj));
    }

    public function testCompactObjectOfArrays(): void
    {
        $obj = new CompactObject([
            'list1' => [1,2,3],
            'list2' => ['a', 'b', 'c'],
            'list3' => [[1,2], ['a' => 'b']],
        ]);

        self::assertStringEqualsFile(self::DATA_DIR . '/compact_object_of_arrays.json5', Json5Encoder::encode($obj));
        self::assertStringEqualsFile(self::DATA_DIR . '/compact_object_of_arrays.json', JsonEncoder::encode($obj));
        self::assertStringEqualsFile(self::DATA_DIR . '/compact_object_of_arrays.json', JsonCEncoder::encode($obj));
    }

    public function testArrayOfCompactObjects(): void
    {
        $list = [
            new CompactObject([1,2,3]),
            new CompactObject(['a' => 'b', 'c' => 'd']),
            new CompactObject(['list' => [1,2], 'obj' => ['a' => 123, 'b' => 456]]),
        ];
        $options = new Options(keyQuotes: Options\Quotes::Single);

        self::assertStringEqualsFile(
            self::DATA_DIR . '/array_of_compact_objects.json5',
            Json5Encoder::encode($list, $options),
        );
        self::assertStringEqualsFile(
            self::DATA_DIR . '/array_of_compact_objects.json',
            JsonEncoder::encode($list, $options),
        );
        self::assertStringEqualsFile(
            self::DATA_DIR . '/array_of_compact_objects.json',
            JsonCEncoder::encode($list, $options),
        );
    }

    public function testJsonTransparency(): void
    {
        // array that is not a list
        $obj1 = [1, 2, 3];
        self::assertEquals('{"0":1,"1":2,"2":3}', json_encode(new CompactObject($obj1)));

        // iterable
        $obj2 = static fn () => yield from $obj1;
        self::assertEquals('{"0":1,"1":2,"2":3}', json_encode(new CompactObject($obj2())));
    }
}
