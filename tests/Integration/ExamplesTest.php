<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Integration;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Options;
use Arokettu\Json5\Values\Comment;
use Arokettu\Json5\Values\CommentDecorator;
use Arokettu\Json5\Values\CompactArray;
use Arokettu\Json5\Values\CompactObject;
use Arokettu\Json5\Values\EndOfLine;
use Arokettu\Json5\Values\InlineArray;
use Arokettu\Json5\Values\InlineObject;
use ArrayObject;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing] // unreliable
class ExamplesTest extends TestCase
{
    public function testExample1(): void
    {
        $data = new CommentDecorator($innerData = [
            'test' => ['ab', 'c', 'd'],
            'floats' => [
                1.0, 2, 3, INF, NAN,
            ],
            'other' => ['true' => true, 'false' => false, 'null' => null],
            'commented' => new CommentDecorator(
                123,
                "Comment before\n\nSecond line",
                'Comment after',
            ),
            new EndOfLine(),
            new Comment(<<<TEXT
                Comment comment comment
                Line!
                TEXT),
            'list' => [
                1,
                new CommentDecorator(2, 'list comment 1', 'list comment 2'),
                3
            ],
            'list2' => [
                'abc',
                new Comment('fed'),
                'def',
                new EndOfLine(),
                'ghi',
            ],
            'inlineList' => new InlineArray([new Comment('/*/*/*/*/*/*/*/*/*/*/*/'),1,2,3]),
            'inlineList2' => new InlineArray([1,2,3,4,5,6,7,new EndOfLine(), 8,9,10,new Comment('eleven'),11,12]),
            'inlineListOfObjects' => new InlineArray([new ArrayObject(['a' => 'b']), new ArrayObject(['c' => 'd'])]),
            'inlineListWithComment' => new InlineArray([1,new CommentDecorator(2, 'abc', 'efg'),3]),
            'listOfInlineObjects' => [
                new InlineObject(['a' => 'b', 'c' => 'd']),
                new InlineObject(['a' => 'b', 'c' => 'd']),
                new InlineObject(['a' => 'b', 'c' => new CommentDecorator('d', 'abc', 'def'), 'ee' => 'ff']),
                new InlineObject(['a' => 'b', new EndOfLine(), 'c' => 'd', 'f' => 'g', 'h' => 'i']),
            ],
            'compactList' => new CompactArray([1,2,3]),
            'compactList2' => new CompactArray([1,2,3,4,5,6,7,new EndOfLine(), 8,9,10,11,12]),
            'compactListOfObjects' => new CompactArray([new ArrayObject(['a' => 'b']), new ArrayObject(['c' => 'd'])]),
            'compactListWithComment' => new CompactArray([1,new CommentDecorator(2, 'abc', 'efg'),3]),
            'compactObject' => new CompactObject(['a' => 'b', new EndOfLine(), 'c' => 'd', 'f' => 'g', 'h' => 'i']),
        ], 'Body comment 1', 'Body comment 2');

        self::assertStringEqualsFile(__DIR__ . '/data/example1.json5', Json5Encoder::encode($data, new Options(
            preserveZeroFraction: true,
        )));

        unset($innerData['floats'][3]); // inf
        unset($innerData['floats'][4]); // nan

        $data2 = new CommentDecorator($innerData, '123', '456');

        self::assertStringEqualsFile(__DIR__ . '/data/example1.json', JsonEncoder::encode($data2, new Options(
            preserveZeroFraction: true,
        )));
    }
}
