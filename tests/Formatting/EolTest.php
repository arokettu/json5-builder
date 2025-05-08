<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Formatting;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\Values\CompactList;
use Arokettu\Json5\Values\CompactObject;
use Arokettu\Json5\Values\EndOfLine;
use Arokettu\Json5\Values\InlineList;
use Arokettu\Json5\Values\InlineObject;
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

    public function testObject(): void
    {
        $list = [
            new EndOfLine(), // keys are ignored
            'key1' => 'value1',
            'key2' => 'value2',
            new EndOfLine(),
            'key3' => 'value3',
            'key4' => 'value4',
            new EndOfLine(),
        ];

        self::assertEquals(<<<JSON5
            {

                key1: "value1",
                key2: "value2",

                key3: "value3",
                key4: "value4",

            }

            JSON5, Json5Encoder::encode($list));
    }

    public function testCompactObject(): void
    {
        $list = [
            new EndOfLine(), // keys are ignored
            'key1' => 'value1',
            'key2' => 'value2',
            new EndOfLine(),
            'key3' => 'value3',
            'key4' => 'value4',
            new EndOfLine(),
        ];

        self::assertEquals(<<<JSON5
            {

                key1: "value1", key2: "value2",
                key3: "value3", key4: "value4",

            }

            JSON5, Json5Encoder::encode(new CompactObject($list)));
    }

    public function testInlineObject(): void
    {
        $list = [
            new EndOfLine(), // keys are ignored
            'key1' => 'value1',
            'key2' => 'value2',
            new EndOfLine(),
            'key3' => 'value3',
            'key4' => 'value4',
            new EndOfLine(),
        ];

        self::assertEquals(<<<JSON5
            {
                key1: "value1", key2: "value2",
                key3: "value3", key4: "value4",
            }

            JSON5, Json5Encoder::encode(new InlineObject($list)));
    }
}
