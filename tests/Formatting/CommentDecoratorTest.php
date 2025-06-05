<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Formatting;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonCEncoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Values\CommentDecorator;
use Arokettu\Json5\Values\CompactArray;
use Arokettu\Json5\Values\CompactObject;
use Arokettu\Json5\Values\InlineArray;
use Arokettu\Json5\Values\InlineObject;
use PHPUnit\Framework\TestCase;
use ValueError;

class CommentDecoratorTest extends TestCase
{
    private const DATA_DIR = __DIR__ . '/data/comment_decorator';

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
        self::assertStringEqualsFile(self::DATA_DIR . '/root_empty.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/root.json', JsonEncoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/root_empty.jsonc', JsonCEncoder::encode($data));

        // comment before
        $data = new CommentDecorator('abcd', commentBefore: 'Comment before');
        self::assertStringEqualsFile(self::DATA_DIR . '/root_before.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/root.json', JsonEncoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/root_before.jsonc', JsonCEncoder::encode($data));

        // comment after
        $data = new CommentDecorator('abcd', commentAfter: 'Comment after');
        self::assertStringEqualsFile(self::DATA_DIR . '/root_after.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/root.json', JsonEncoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/root_after.jsonc', JsonCEncoder::encode($data));

        // both
        $data = new CommentDecorator('abcd', 'Comment before', 'Comment after');
        self::assertStringEqualsFile(self::DATA_DIR . '/root_both.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/root.json', JsonEncoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/root_both.jsonc', JsonCEncoder::encode($data));
    }

    public function testArrayComments(): void
    {
        // no comments
        $data = [123, new CommentDecorator(234), 345];
        self::assertStringEqualsFile(self::DATA_DIR . '/array_empty.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/array.json', JsonEncoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/array_empty.jsonc', JsonCEncoder::encode($data));

        // comment before
        $data = [123, new CommentDecorator(234, commentBefore: 'Comment before'), 345];
        self::assertStringEqualsFile(self::DATA_DIR . '/array_before.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/array.json', JsonEncoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/array_before.jsonc', JsonCEncoder::encode($data));

        // comment after
        $data = [123, new CommentDecorator(234, commentAfter: 'Comment after'), 345];
        self::assertStringEqualsFile(self::DATA_DIR . '/array_after.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/array.json', JsonEncoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/array_after.jsonc', JsonCEncoder::encode($data));

        // both
        $data = [123, new CommentDecorator(234, 'Comment before', 'Comment after'), 345];
        self::assertStringEqualsFile(self::DATA_DIR . '/array_both.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/array.json', JsonEncoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/array_both.jsonc', JsonCEncoder::encode($data));
    }

    public function testObjectComments(): void
    {
        // no comments
        $data = [
            'a' => 123,
            'b' => new CommentDecorator(234),
            'c' => 345
        ];
        self::assertStringEqualsFile(self::DATA_DIR . '/object_empty.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/object.json', JsonEncoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/object_empty.jsonc', JsonCEncoder::encode($data));

        // comment before
        $data = [
            'a' => 123,
            'b' => new CommentDecorator(234, commentBefore: 'Comment before'),
            'c' => 345
        ];
        self::assertStringEqualsFile(self::DATA_DIR . '/object_before.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/object.json', JsonEncoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/object_before.jsonc', JsonCEncoder::encode($data));

        // comment after
        $data = [
            'a' => 123,
            'b' => new CommentDecorator(234, commentAfter: 'Comment after'),
            'c' => 345
        ];
        self::assertStringEqualsFile(self::DATA_DIR . '/object_after.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/object.json', JsonEncoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/object_after.jsonc', JsonCEncoder::encode($data));

        // both
        $data = [
            'a' => 123,
            'b' => new CommentDecorator(234, 'Comment before', 'Comment after'),
            'c' => 345
        ];
        self::assertStringEqualsFile(self::DATA_DIR . '/object_both.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/object.json', JsonEncoder::encode($data));
        self::assertStringEqualsFile(self::DATA_DIR . '/object_both.jsonc', JsonCEncoder::encode($data));
    }

    public function testInlineArrayComments(): void
    {
        $list = new InlineArray([
            'key1' => new CommentDecorator('value1', commentBefore: 'c1'),
            'key2' => new CommentDecorator('value2', 'c2-1', 'c2-2'),
            'key3' => new CommentDecorator('value3', commentAfter: 'c3'),
        ]);

        self::assertStringEqualsFile(self::DATA_DIR . '/inline_array.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(self::DATA_DIR . '/inline_array.json', JsonEncoder::encode($list));
        self::assertStringEqualsFile(self::DATA_DIR . '/inline_array.jsonc', JsonCEncoder::encode($list));
    }

    public function testCompactArrayComments(): void
    {
        $list = new CompactArray([
            'key1' => new CommentDecorator('value1', commentBefore: 'c1'),
            'key2' => new CommentDecorator('value2', 'c2-1', 'c2-2'),
            'key3' => new CommentDecorator('value3', commentAfter: 'c3'),
        ]);

        self::assertStringEqualsFile(self::DATA_DIR . '/compact_array.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(self::DATA_DIR . '/compact_array.json', JsonEncoder::encode($list));
        self::assertStringEqualsFile(self::DATA_DIR . '/compact_array.jsonc', JsonCEncoder::encode($list));
    }

    public function testInlineObjectComments(): void
    {
        $list = new InlineObject([
            'key1' => new CommentDecorator('value1', commentBefore: 'c1'),
            'key2' => new CommentDecorator('value2', 'c2-1', 'c2-2'),
            'key3' => new CommentDecorator('value3', commentAfter: 'c3'),
        ]);

        self::assertStringEqualsFile(self::DATA_DIR . '/inline_object.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(self::DATA_DIR . '/inline_object.json', JsonEncoder::encode($list));
        self::assertStringEqualsFile(self::DATA_DIR . '/inline_object.jsonc', JsonCEncoder::encode($list));
    }

    public function testCompactObjectComments(): void
    {
        $list = new CompactObject([
            'key1' => new CommentDecorator('value1', commentBefore: 'c1'),
            'key2' => new CommentDecorator('value2', 'c2-1', 'c2-2'),
            'key3' => new CommentDecorator('value3', commentAfter: 'c3'),
        ]);

        self::assertStringEqualsFile(self::DATA_DIR . '/compact_object.json5', Json5Encoder::encode($list));
        self::assertStringEqualsFile(self::DATA_DIR . '/compact_object.json', JsonEncoder::encode($list));
        self::assertStringEqualsFile(self::DATA_DIR . '/compact_object.jsonc', JsonCEncoder::encode($list));
    }
}
