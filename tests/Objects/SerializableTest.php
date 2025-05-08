<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Objects;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\Values\Internal\RawJson5Serializable;
use Arokettu\Json5\Values\Json5Serializable;
use JsonSerializable;
use PHPUnit\Framework\TestCase;

class SerializableTest extends TestCase
{
    public function testJsonSerializable(): void
    {
        $obj = fn (mixed $value) => new class ($value) implements JsonSerializable {
            public function __construct(private mixed $value)
            {
            }

            public function jsonSerialize(): mixed
            {
                return $this->value;
            }
        };

        self::assertEquals("5426\n", Json5Encoder::encode($obj(5426)));
        self::assertEquals("\"5426\"\n", Json5Encoder::encode($obj('5426')));
    }

    public function testJson5Serializable(): void
    {
        $obj = fn (mixed $value) => new class ($value) implements Json5Serializable {
            public function __construct(private mixed $value)
            {
            }

            public function json5Serialize(): mixed
            {
                return $this->value;
            }
        };

        self::assertEquals("5426\n", Json5Encoder::encode($obj(5426)));
        self::assertEquals("\"5426\"\n", Json5Encoder::encode($obj('5426')));
    }

    public function testRawJson5Serializable(): void
    {
        $obj = fn (mixed $value) => new class ($value) implements RawJson5Serializable {
            public function __construct(private mixed $value)
            {
            }

            public function json5SerializeRaw(): string
            {
                return (string)$this->value;
            }
        };

        self::assertEquals("5426\n", Json5Encoder::encode($obj(5426)));
        self::assertEquals("5426\n", Json5Encoder::encode($obj('5426')));
    }

    public function testInterfacePrecedence(): void
    {
        $raw = new class implements JsonSerializable, Json5Serializable, RawJson5Serializable {
            public function jsonSerialize(): mixed
            {
                return 'json';
            }

            public function json5Serialize(): mixed
            {
                return 'json5';
            }

            public function json5SerializeRaw(): string
            {
                return '"raw"';
            }
        };

        self::assertEquals("\"raw\"\n", Json5Encoder::encode($raw));

        $json5 = new class implements JsonSerializable, Json5Serializable {
            public function jsonSerialize(): mixed
            {
                return 'json';
            }

            public function json5Serialize(): mixed
            {
                return 'json5';
            }
        };

        self::assertEquals("\"json5\"\n", Json5Encoder::encode($json5));
    }
}
