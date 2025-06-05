<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonCEncoder;
use Arokettu\Json5\JsonEncoder;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

class EncodingFormatsTest extends TestCase
{
    public function testStream(): void
    {
        $data = ['a' => 'b'];

        $resource = tmpfile();
        Json5Encoder::encodeToStream($resource, $data);
        rewind($resource);
        self::assertEquals("{\n    a: \"b\",\n}\n", stream_get_contents($resource));
        fclose($resource);

        $resource = tmpfile();
        JsonEncoder::encodeToStream($resource, $data);
        rewind($resource);
        self::assertEquals("{\n    \"a\": \"b\"\n}\n", stream_get_contents($resource));
        fclose($resource);

        $resource = tmpfile();
        JsonCEncoder::encodeToStream($resource, $data);
        rewind($resource);
        self::assertEquals("{\n    \"a\": \"b\"\n}\n", stream_get_contents($resource));
        fclose($resource);
    }

    public function testNotResourceJson5(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage('$stream must be a writable stream');
        Json5Encoder::encodeToStream(new stdClass(), null);
    }

    public function testNotResourceJson(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage('$stream must be a writable stream');
        JsonEncoder::encodeToStream(new stdClass(), null);
    }

    public function testNotResourceJsonC(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage('$stream must be a writable stream');
        JsonCEncoder::encodeToStream(new stdClass(), null);
    }
}
