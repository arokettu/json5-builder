<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Objects;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonCEncoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Values\Internal\RawJson5Serializable;
use Arokettu\Json5\Values\Json5Serializable;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use TypeError;

final class SerializableTest extends TestCase
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

        self::assertEquals("5426\n", JsonEncoder::encode($obj(5426)));
        self::assertEquals("\"5426\"\n", JsonEncoder::encode($obj('5426')));

        self::assertEquals("5426\n", JsonCEncoder::encode($obj(5426)));
        self::assertEquals("\"5426\"\n", JsonCEncoder::encode($obj('5426')));
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

        // but not supported in json
        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: Arokettu\Json5\Values\Json5Serializable@anonymous');
        JsonEncoder::encode($obj(null));
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

        // but not supported in json
        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: Arokettu\Json5\Values\Internal\RawJson5Serializable@anonymous');
        JsonEncoder::encode($obj(null));
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
        self::assertEquals("\"json\"\n", JsonEncoder::encode($raw)); // only JsonSerializable is supported
        self::assertEquals("\"json\"\n", JsonCEncoder::encode($raw)); // only JsonSerializable is supported

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
        self::assertEquals("\"json\"\n", JsonEncoder::encode($json5)); // only JsonSerializable is supported
        self::assertEquals("\"json\"\n", JsonCEncoder::encode($json5)); // only JsonSerializable is supported
    }
}
