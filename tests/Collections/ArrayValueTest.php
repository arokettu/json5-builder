<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Collections;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Values\ArrayValue;
use Arokettu\Json5\Values\Json5Serializable;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use stdClass;

class ArrayValueTest extends TestCase
{
    public function testArrayAccepted(): void
    {
        $list = new ArrayValue(['a' => 1, 2, 8 => 3, 4]); // keys are ignored

        self::assertStringEqualsFile(__DIR__ . '/data/array_value.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/array_value.json', JsonEncoder::encode($list));
    }

    public function testStdClassAccepted(): void
    {
        $object = new stdClass();
        $object->a = 1;
        $object->z = 2;
        $object->x = 3;
        $object->{'4'} = 4;

        $list = new ArrayValue($object);

        self::assertStringEqualsFile(__DIR__ . '/data/array_value.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/array_value.json', JsonEncoder::encode($list));
    }

    public function testIterableAccepted(): void
    {
        $i = function () {
            yield 1;
            yield 2;
            yield 3;
            yield 4;
        };

        $list = new ArrayValue($i());
        self::assertStringEqualsFile(__DIR__ . '/data/array_value.json5', Json5Encoder::encode($list));

        $list = new ArrayValue($i());
        self::assertStringEqualsFile(__DIR__ . '/data/array_value.json', JsonEncoder::encode($list));
    }

    public function testSupportJsonSerializable(): void
    {
        $class = new class implements JsonSerializable {
            public function jsonSerialize(): array
            {
                return [1,2,3,4];
            }
        };

        $list = new ArrayValue($class);

        self::assertStringEqualsFile(__DIR__ . '/data/array_value.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/array_value.json', JsonEncoder::encode($list));
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

        // supported in JSON5
        self::assertStringEqualsFile(
            __DIR__ . '/data/array_value_serializable.json5',
            Json5Encoder::encode(new ArrayValue($class))
        );
        // not supported in JSON
        self::assertStringEqualsFile(
            __DIR__ . '/data/array_value_serializable.json',
            JsonEncoder::encode(new ArrayValue($class))
        );
    }

    public function testJsonTransparency(): void
    {
        // array that is not a list
        $list1 = ['a' => 1, 'b' => 2, 'c' => 3];
        self::assertEquals('[1,2,3]', json_encode(new ArrayValue($list1)));

        // iterable
        $list2 = fn () => yield from $list1;
        self::assertEquals('[1,2,3]', json_encode(new ArrayValue($list2())));
    }
}
