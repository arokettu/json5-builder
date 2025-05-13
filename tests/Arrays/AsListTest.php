<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Arrays;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonEncoder;
use PHPUnit\Framework\TestCase;

class AsListTest extends TestCase
{
    public function testList(): void
    {
        // sequential arrays become lists
        $list1 = [1,2,3,4];
        $list2 = ['a','b','c','d'];

        self::assertStringEqualsFile(__DIR__ . '/data/list1.json5', Json5Encoder::encode($list1));
        self::assertStringEqualsFile(__DIR__ . '/data/list2.json5', Json5Encoder::encode($list2));

        self::assertStringEqualsFile(__DIR__ . '/data/list1.json', JsonEncoder::encode($list1));
        self::assertStringEqualsFile(__DIR__ . '/data/list2.json', JsonEncoder::encode($list2));
    }

    public function testListInAList(): void
    {
        $list = [
            [1,2,3,4],
            ['a','b','c','d'],
            null,
        ];

        self::assertStringEqualsFile(__DIR__ . '/data/list_in_list.json5', Json5Encoder::encode($list));

        self::assertStringEqualsFile(__DIR__ . '/data/list_in_list.json', JsonEncoder::encode($list));
    }
}
