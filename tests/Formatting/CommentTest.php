<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Formatting;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\Values\Comment;
use Arokettu\Json5\Values\CompactList;
use Arokettu\Json5\Values\CompactObject;
use Arokettu\Json5\Values\InlineList;
use Arokettu\Json5\Values\InlineObject;
use PHPUnit\Framework\TestCase;
use TypeError;

class CommentTest extends TestCase
{
    public function testNotAllowedAsRoot(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: Arokettu\Json5\Values\Comment');

        Json5Encoder::encode(new Comment(''));
    }

    public function testList(): void
    {
        $list = [ // must still be a list
            new Comment('begin'),
            'value1',
            'value2',
            new Comment('middle'),
            'value3',
            'value4',
            new Comment('end'),
        ];

        self::assertEquals(<<<JSON5
            [
                // begin
                "value1",
                "value2",
                // middle
                "value3",
                "value4",
                // end
            ]

            JSON5, Json5Encoder::encode($list));
    }

    public function testCompactList(): void
    {
        $list = [
            new Comment('begin'),
            'value1',
            'value2',
            new Comment('middle'),
            'value3',
            'value4',
            new Comment('end'),
        ];

        self::assertEquals(<<<JSON5
            [
                /* begin */ "value1", "value2", /* middle */ "value3", "value4", /* end */
            ]

            JSON5, Json5Encoder::encode(new CompactList($list)));
    }

    public function testInlineList(): void
    {
        $list = [
            new Comment('begin'),
            'value1',
            'value2',
            new Comment('middle'),
            'value3',
            'value4',
            new Comment('end'),
        ];

        self::assertEquals(<<<JSON5
            [/* begin */ "value1", "value2", /* middle */ "value3", "value4", /* end */]

            JSON5, Json5Encoder::encode(new InlineList($list)));
    }

    public function testObject(): void
    {
        $list = [
            new Comment('begin'), // keys are ignored
            'key1' => 'value1',
            'key2' => 'value2',
            new Comment('middle'),
            'key3' => 'value3',
            'key4' => 'value4',
            new Comment('end'),
        ];

        self::assertEquals(<<<JSON5
            {
                // begin
                key1: "value1",
                key2: "value2",
                // middle
                key3: "value3",
                key4: "value4",
                // end
            }

            JSON5, Json5Encoder::encode($list));
    }

    public function testCompactObject(): void
    {
        $list = [
            new Comment('begin'), // keys are ignored
            'key1' => 'value1',
            'key2' => 'value2',
            new Comment('middle'),
            'key3' => 'value3',
            'key4' => 'value4',
            new Comment('end'),
        ];

        self::assertEquals(<<<JSON5
            {
                /* begin */ key1: "value1", key2: "value2", /* middle */ key3: "value3", key4: "value4", /* end */
            }

            JSON5, Json5Encoder::encode(new CompactObject($list)));
    }

    public function testInlineObject(): void
    {
        $list = [
            new Comment('begin'), // keys are ignored
            'key1' => 'value1',
            'key2' => 'value2',
            new Comment('middle'),
            'key3' => 'value3',
            'key4' => 'value4',
            new Comment('end'),
        ];

        self::assertEquals(<<<JSON5
            { /* begin */ key1: "value1", key2: "value2", /* middle */ key3: "value3", key4: "value4", /* end */ }

            JSON5, Json5Encoder::encode(new InlineObject($list)));
    }
}
