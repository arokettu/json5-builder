<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Arrays;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonEncoder;
use PHPUnit\Framework\TestCase;

class AsArrayTest extends TestCase
{
    public function testArray(): void
    {
        // lists become json arrays
        $list1 = [1,2,3,4];
        $list2 = ['a','b','c','d'];

        self::assertStringEqualsFile(__DIR__ . '/data/array1.json5', Json5Encoder::encode($list1));
        self::assertStringEqualsFile(__DIR__ . '/data/array2.json5', Json5Encoder::encode($list2));

        self::assertStringEqualsFile(__DIR__ . '/data/array1.json', JsonEncoder::encode($list1));
        self::assertStringEqualsFile(__DIR__ . '/data/array2.json', JsonEncoder::encode($list2));
    }

    public function testArrayInArray(): void
    {
        $list = [
            [1,2,3,4],
            ['a','b','c','d'],
            null,
        ];

        self::assertStringEqualsFile(__DIR__ . '/data/array_in_array.json5', Json5Encoder::encode($list));

        self::assertStringEqualsFile(__DIR__ . '/data/array_in_array.json', JsonEncoder::encode($list));
    }
}
