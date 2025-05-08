<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Formatting;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\Values\CompactList;
use Arokettu\Json5\Values\EndOfLine;
use Arokettu\Json5\Values\InlineList;
use PHPUnit\Framework\TestCase;

class EolTest extends TestCase
{
    public function testList(): void
    {
        $list = [ // must still be a list
            new EndOfLine(),
            'value1',
            'value2',
            new EndOfLine(),
            'value3',
            'value4',
            new EndOfLine(),
        ];

        self::assertEquals(<<<JSON5
            [

                "value1",
                "value2",

                "value3",
                "value4",

            ]

            JSON5, Json5Encoder::encode($list));
    }

    public function testCompactList(): void
    {
        $list = [
            new EndOfLine(),
            'value1',
            'value2',
            new EndOfLine(),
            'value3',
            'value4',
            new EndOfLine(),
        ];

        self::assertEquals(<<<JSON5
            [

                "value1", "value2",
                "value3", "value4",

            ]

            JSON5, Json5Encoder::encode(new CompactList($list)));
    }

    public function testInlineList(): void
    {
        $list = [
            new EndOfLine(),
            'value1',
            'value2',
            new EndOfLine(),
            'value3',
            'value4',
            new EndOfLine(),
        ];

        self::assertEquals(<<<JSON5
            [
                "value1", "value2",
                "value3", "value4",
            ]

            JSON5, Json5Encoder::encode(new InlineList($list)));
    }
}
