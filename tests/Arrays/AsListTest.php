<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Arrays;

use Arokettu\Json5\Json5Encoder;
use PHPUnit\Framework\TestCase;

class AsListTest extends TestCase
{
    public function testList(): void
    {
        // sequential arrays become lists
        $list1 = [1,2,3,4];
        $list2 = ['a','b','c','d'];

        self::assertEquals(<<<JSON5
            [
                1,
                2,
                3,
                4,
            ]

            JSON5, Json5Encoder::encode($list1));
        self::assertEquals(<<<JSON5
            [
                "a",
                "b",
                "c",
                "d",
            ]

            JSON5, Json5Encoder::encode($list2));
    }

    public function testListInAList(): void
    {
        $list = [
            [1,2,3,4],
            ['a','b','c','d'],
            null,
        ];

        self::assertEquals(<<<JSON5
            [
                [
                    1,
                    2,
                    3,
                    4,
                ],
                [
                    "a",
                    "b",
                    "c",
                    "d",
                ],
                null,
            ]

            JSON5, Json5Encoder::encode($list));
    }
}
