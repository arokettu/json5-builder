<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Objects;

use Arokettu\Json5\Json5Encoder;
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

        self::assertEquals(
            <<<JSON5
            {
                a: 123,
                b: false,
                c: "test",
            }

            JSON5,
            Json5Encoder::encode($obj),
        );

        // sequential array in stdClass form becomes an object anyway
        $list = (object)[1,2,3,4];

        self::assertEquals(
            <<<JSON5
            {
                '0': 1,
                '1': 2,
                '2': 3,
                '3': 4,
            }

            JSON5,
            Json5Encoder::encode($list),
        );
    }

    public static function testArrayObject(): void
    {
        $obj = new ArrayObject();

        $obj['a'] = 123;
        $obj['b'] = false;
        $obj['c'] = 'test';

        self::assertEquals(
            <<<JSON5
            {
                a: 123,
                b: false,
                c: "test",
            }

            JSON5,
            Json5Encoder::encode($obj),
        );

        // sequential array in stdClass form becomes an object anyway
        $list = new ArrayObject([1,2,3,4]);

        self::assertEquals(
            <<<JSON5
            {
                '0': 1,
                '1': 2,
                '2': 3,
                '3': 4,
            }

            JSON5,
            Json5Encoder::encode($list),
        );
    }
}
