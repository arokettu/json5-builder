<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Collections;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonCEncoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Options;
use Arokettu\Json5\Values\ObjectValue;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ObjectValueTest extends TestCase
{
    private const DATA_DIR = __DIR__ . '/data/object_value';

    public function testArrayAccepted(): void
    {
        $list = new ObjectValue([1, 2, 3, 4]); // even if list
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

        $objobj = new ObjectValue($object);
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

        $list = new ObjectValue($i());
        self::assertStringEqualsFile(self::DATA_DIR . '/object_value_iterable.json5', Json5Encoder::encode($list));

        $list = new ObjectValue($i());
        self::assertStringEqualsFile(self::DATA_DIR . '/object_value_iterable.json', JsonEncoder::encode($list));

        $list = new ObjectValue($i());
        self::assertStringEqualsFile(self::DATA_DIR . '/object_value_iterable.json', JsonCEncoder::encode($list));
    }

    public function testJsonTransparency(): void
    {
        // array that is not a list
        $list1 = [1, 2, 3];
        self::assertEquals('{"0":1,"1":2,"2":3}', json_encode(new ObjectValue($list1)));

        // iterable
        $list2 = static fn () => yield from $list1;
        self::assertEquals('{"0":1,"1":2,"2":3}', json_encode(new ObjectValue($list2())));
    }
}
