<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Formatting;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\Values\CommentDecorator;
use PHPUnit\Framework\TestCase;
use ValueError;

class CommentDecoratorTest extends TestCase
{
    public function testCommentAfterShouldNotBeMultiline(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage('The comment after must be a single line string');

        new CommentDecorator(null, commentAfter: "test\ntest");
    }

    public function testRootComments(): void
    {
        // no comments
        self::assertEquals(
            <<<JSON5
            "abcd"

            JSON5,
            Json5Encoder::encode(new CommentDecorator('abcd'))
        );

        // comment before
        self::assertEquals(
            <<<JSON5
            // Comment before
            "abcd"

            JSON5,
            Json5Encoder::encode(new CommentDecorator('abcd', commentBefore: 'Comment before'))
        );

        // comment after
        self::assertEquals(
            <<<JSON5
            "abcd" // Comment after

            JSON5,
            Json5Encoder::encode(new CommentDecorator('abcd', commentAfter: 'Comment after'))
        );

        // both
        self::assertEquals(
            <<<JSON5
            // Comment before
            "abcd" // Comment after

            JSON5,
            Json5Encoder::encode(new CommentDecorator('abcd', 'Comment before', 'Comment after'))
        );
    }

    public function testListComments(): void
    {
        // no comments
        self::assertEquals(
            <<<JSON5
            [
                123,
                234,
                345,
            ]

            JSON5,
            Json5Encoder::encode([123, new CommentDecorator(234), 345])
        );

        // comment before
        self::assertEquals(
            <<<JSON5
            [
                123,
                // Comment before
                234,
                345,
            ]

            JSON5,
            Json5Encoder::encode([123, new CommentDecorator(234, commentBefore: 'Comment before'), 345])
        );

        // comment after
        self::assertEquals(
            <<<JSON5
            [
                123,
                234, // Comment after
                345,
            ]

            JSON5,
            Json5Encoder::encode([123, new CommentDecorator(234, commentAfter: 'Comment after'), 345])
        );

        // both
        self::assertEquals(
            <<<JSON5
            [
                123,
                // Comment before
                234, // Comment after
                345,
            ]

            JSON5,
            Json5Encoder::encode([123, new CommentDecorator(234, 'Comment before', 'Comment after'), 345])
        );
    }

    public function testObjectComments(): void
    {
        // no comments
        self::assertEquals(
            <<<JSON5
            {
                a: 123,
                b: 234,
                c: 345,
            }

            JSON5,
            Json5Encoder::encode([
                'a' => 123,
                'b' => new CommentDecorator(234),
                'c' => 345
            ]),
        );

        // comment before
        self::assertEquals(
            <<<JSON5
            {
                a: 123,
                // Comment before
                b: 234,
                c: 345,
            }

            JSON5,
            Json5Encoder::encode([
                'a' => 123,
                'b' => new CommentDecorator(234, commentBefore: 'Comment before'),
                'c' => 345
            ]),
        );

        // comment after
        self::assertEquals(
            <<<JSON5
            {
                a: 123,
                b: 234, // Comment after
                c: 345,
            }

            JSON5,
            Json5Encoder::encode([
                'a' => 123,
                'b' => new CommentDecorator(234, commentAfter: 'Comment after'),
                'c' => 345
            ]),
        );

        // both
        self::assertEquals(
            <<<JSON5
            {
                a: 123,
                // Comment before
                b: 234, // Comment after
                c: 345,
            }

            JSON5,
            Json5Encoder::encode([
                'a' => 123,
                'b' => new CommentDecorator(234, 'Comment before', 'Comment after'),
                'c' => 345
            ]),
        );
    }
}
