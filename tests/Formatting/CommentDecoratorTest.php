<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Formatting;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Values\CommentDecorator;
use Arokettu\Json5\Values\CompactList;
use Arokettu\Json5\Values\CompactObject;
use Arokettu\Json5\Values\InlineList;
use Arokettu\Json5\Values\InlineObject;
use PHPUnit\Framework\TestCase;
use ValueError;

// phpcs:disable Generic.Files.LineLength.TooLong
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
        $data = new CommentDecorator('abcd');
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/root_empty.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/root.json', JsonEncoder::encode($data));

        // comment before
        $data = new CommentDecorator('abcd', commentBefore: 'Comment before');
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/root_before.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/root.json', JsonEncoder::encode($data));

        // comment after
        $data = new CommentDecorator('abcd', commentAfter: 'Comment after');
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/root_after.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/root.json', JsonEncoder::encode($data));

        // both
        $data = new CommentDecorator('abcd', 'Comment before', 'Comment after');
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/root_both.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/root.json', JsonEncoder::encode($data));
    }

    public function testListComments(): void
    {
        // no comments
        $data = [123, new CommentDecorator(234), 345];
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/list_empty.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/list.json', JsonEncoder::encode($data));

        // comment before
        $data = [123, new CommentDecorator(234, commentBefore: 'Comment before'), 345];
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/list_before.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/list.json', JsonEncoder::encode($data));

        // comment after
        $data = [123, new CommentDecorator(234, commentAfter: 'Comment after'), 345];
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/list_after.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/list.json', JsonEncoder::encode($data));

        // both
        $data = [123, new CommentDecorator(234, 'Comment before', 'Comment after'), 345];
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/list_both.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/list.json', JsonEncoder::encode($data));
    }

    public function testObjectComments(): void
    {
        // no comments
        $data = [
            'a' => 123,
            'b' => new CommentDecorator(234),
            'c' => 345
        ];
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/object_empty.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/object.json', JsonEncoder::encode($data));

        // comment before
        $data = [
            'a' => 123,
            'b' => new CommentDecorator(234, commentBefore: 'Comment before'),
            'c' => 345
        ];
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/object_before.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/object.json', JsonEncoder::encode($data));

        // comment after
        $data = [
            'a' => 123,
            'b' => new CommentDecorator(234, commentAfter: 'Comment after'),
            'c' => 345
        ];
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/object_after.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/object.json', JsonEncoder::encode($data));

        // both
        $data = [
            'a' => 123,
            'b' => new CommentDecorator(234, 'Comment before', 'Comment after'),
            'c' => 345
        ];
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/object_both.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/object.json', JsonEncoder::encode($data));
    }

    public function testInlineListComments(): void
    {
        $list = new InlineList([
            'key1' => new CommentDecorator('value1', commentBefore: 'c1'),
            'key2' => new CommentDecorator('value2', 'c2-1', 'c2-2'),
            'key3' => new CommentDecorator('value3', commentAfter: 'c3'),
        ]);

        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/inline_list.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/inline_list.json', JsonEncoder::encode($list));
    }

    public function testCompactListComments(): void
    {
        $list = new CompactList([
            'key1' => new CommentDecorator('value1', commentBefore: 'c1'),
            'key2' => new CommentDecorator('value2', 'c2-1', 'c2-2'),
            'key3' => new CommentDecorator('value3', commentAfter: 'c3'),
        ]);

        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/compact_list.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/compact_list.json', JsonEncoder::encode($list));
    }

    public function testInlineObjectComments(): void
    {
        $list = new InlineObject([
            'key1' => new CommentDecorator('value1', commentBefore: 'c1'),
            'key2' => new CommentDecorator('value2', 'c2-1', 'c2-2'),
            'key3' => new CommentDecorator('value3', commentAfter: 'c3'),
        ]);

        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/inline_object.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/inline_object.json', JsonEncoder::encode($list));
    }

    public function testCompactObjectComments(): void
    {
        $list = new CompactObject([
            'key1' => new CommentDecorator('value1', commentBefore: 'c1'),
            'key2' => new CommentDecorator('value2', 'c2-1', 'c2-2'),
            'key3' => new CommentDecorator('value3', commentAfter: 'c3'),
        ]);

        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/compact_object.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(__DIR__ . '/data/comment_decorator/compact_object.json', JsonEncoder::encode($list));
    }
}
