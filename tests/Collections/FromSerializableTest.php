<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Collections;

use Arokettu\Json5\Values\ArrayValue;
use Arokettu\Json5\Values\CompactArray;
use Arokettu\Json5\Values\CompactObject;
use Arokettu\Json5\Values\InlineArray;
use Arokettu\Json5\Values\InlineObject;
use Arokettu\Json5\Values\Json5Serializable;
use Arokettu\Json5\Values\ObjectValue;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use stdClass;

final class FromSerializableTest extends TestCase
{
    public function testObjectsSupportJson5Serializable(): void
    {
        $class = new class implements Json5Serializable {
            public function json5Serialize(): array // takes precedence
            {
                return ['a' => 1, 'b' => 2, 'c' => 3];
            }
        };

        $object = $class->json5Serialize();

        self::assertEquals($object, iterator_to_array(ArrayValue::fromSerializable($class)));
        self::assertEquals($object, iterator_to_array(CompactArray::fromSerializable($class)));
        self::assertEquals($object, iterator_to_array(InlineArray::fromSerializable($class)));

        self::assertEquals($object, iterator_to_array(ObjectValue::fromSerializable($class)));
        self::assertEquals($object, iterator_to_array(CompactObject::fromSerializable($class)));
        self::assertEquals($object, iterator_to_array(InlineObject::fromSerializable($class)));
    }

    public function testObjectsSupportJsonSerializable(): void
    {
        $class = new class implements JsonSerializable {
            public function jsonSerialize(): array
            {
                return ['d' => 4, 'e' => 5];
            }
        };

        $object = $class->jsonSerialize();

        self::assertEquals($object, iterator_to_array(ArrayValue::fromSerializable($class)));
        self::assertEquals($object, iterator_to_array(CompactArray::fromSerializable($class)));
        self::assertEquals($object, iterator_to_array(InlineArray::fromSerializable($class)));

        self::assertEquals($object, iterator_to_array(ObjectValue::fromSerializable($class)));
        self::assertEquals($object, iterator_to_array(CompactObject::fromSerializable($class)));
        self::assertEquals($object, iterator_to_array(InlineObject::fromSerializable($class)));

        self::assertEquals($object, iterator_to_array(ArrayValue::fromJsonSerializable($class)));
        self::assertEquals($object, iterator_to_array(CompactArray::fromJsonSerializable($class)));
        self::assertEquals($object, iterator_to_array(InlineArray::fromJsonSerializable($class)));

        self::assertEquals($object, iterator_to_array(ObjectValue::fromJsonSerializable($class)));
        self::assertEquals($object, iterator_to_array(CompactObject::fromJsonSerializable($class)));
        self::assertEquals($object, iterator_to_array(InlineObject::fromJsonSerializable($class)));
    }

    public function testJson5TakesPrecedence(): void
    {
        $class = new class implements JsonSerializable, Json5Serializable {
            public function json5Serialize(): stdClass // takes precedence
            {
                return (object)['a' => 1, 'b' => 2, 'c' => 3];
            }

            public function jsonSerialize(): stdClass
            {
                return (object)['d' => 4, 'e' => 5];
            }
        };

        $object = (array)$class->jsonSerialize();
        $object5 = (array)$class->json5Serialize();

        // JSON5 Serailizable
        self::assertEquals($object5, iterator_to_array(ArrayValue::fromSerializable($class)));
        self::assertEquals($object5, iterator_to_array(CompactArray::fromSerializable($class)));
        self::assertEquals($object5, iterator_to_array(InlineArray::fromSerializable($class)));

        self::assertEquals($object5, iterator_to_array(ObjectValue::fromSerializable($class)));
        self::assertEquals($object5, iterator_to_array(CompactObject::fromSerializable($class)));
        self::assertEquals($object5, iterator_to_array(InlineObject::fromSerializable($class)));

        // not JSON5 Serailizable
        self::assertEquals($object, iterator_to_array(ArrayValue::fromJsonSerializable($class)));
        self::assertEquals($object, iterator_to_array(CompactArray::fromJsonSerializable($class)));
        self::assertEquals($object, iterator_to_array(InlineArray::fromJsonSerializable($class)));

        self::assertEquals($object, iterator_to_array(ObjectValue::fromJsonSerializable($class)));
        self::assertEquals($object, iterator_to_array(CompactObject::fromJsonSerializable($class)));
        self::assertEquals($object, iterator_to_array(InlineObject::fromJsonSerializable($class)));
    }
}
