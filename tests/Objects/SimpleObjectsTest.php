<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Objects;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonEncoder;
use ArrayObject;
use PHPUnit\Framework\TestCase;
use stdClass;

class SimpleObjectsTest extends TestCase
{
    public static function testStdClass(): void
    {
        $obj = new stdClass();

        $obj->a = 123;
        $obj->b = false;
        $obj->c = 'test';

        self::assertStringEqualsFile(__DIR__ . '/data/obj.json5', Json5Encoder::encode($obj));
        self::assertStringEqualsFile(__DIR__ . '/data/obj.json', JsonEncoder::encode($obj));

        // sequential array in stdClass form becomes an object anyway
        $list = (object)[1,2,3,4];

        self::assertStringEqualsFile(__DIR__ . '/data/list.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/list.json', JsonEncoder::encode($list));
    }

    public static function testArrayObject(): void
    {
        $obj = new ArrayObject();

        $obj['a'] = 123;
        $obj['b'] = false;
        $obj['c'] = 'test';

        self::assertStringEqualsFile(__DIR__ . '/data/obj.json5', Json5Encoder::encode($obj));
        self::assertStringEqualsFile(__DIR__ . '/data/obj.json', JsonEncoder::encode($obj));

        // sequential array in stdClass form becomes an object anyway
        $list = new ArrayObject([1,2,3,4]);

        self::assertStringEqualsFile(__DIR__ . '/data/list.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/list.json', JsonEncoder::encode($list));
    }
}
