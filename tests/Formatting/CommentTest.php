<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Formatting;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonCEncoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Values\Comment;
use Arokettu\Json5\Values\CompactArray;
use Arokettu\Json5\Values\CompactObject;
use Arokettu\Json5\Values\InlineArray;
use Arokettu\Json5\Values\InlineObject;
use PHPUnit\Framework\TestCase;
use TypeError;

class CommentTest extends TestCase
{
    private const DATA_DIR = __DIR__ . '/data/comment';

    public function testNotAllowedAsRootJson5(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: Arokettu\Json5\Values\Comment');

        Json5Encoder::encode(new Comment(''));
    }

    public function testNotAllowedAsRootJson(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: Arokettu\Json5\Values\Comment');

        JsonEncoder::encode(new Comment(''));
    }

    public function testNotAllowedAsRootJsonC(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: Arokettu\Json5\Values\Comment');

        JsonCEncoder::encode(new Comment(''));
    }

    public function testArray(): void
    {
        $array = [ // must still be a list
            new Comment('begin'),
            'value1',
            'value2',
            new Comment('middle'),
            'value3',
            'value4',
            new Comment('end'),
        ];

        self::assertStringEqualsFile(self::DATA_DIR . '/array.json5', Json5Encoder::encode($array));
        self::assertStringEqualsFile(self::DATA_DIR . '/array.json', JsonEncoder::encode($array));
        self::assertStringEqualsFile(self::DATA_DIR . '/array.jsonc', JsonCEncoder::encode($array));
    }

    public function testCompactArray(): void
    {
        $array = new CompactArray([
            new Comment('begin'),
            'value1',
            'value2',
            new Comment('middle'),
            'value3',
            'value4',
            new Comment('end'),
        ]);

        self::assertStringEqualsFile(self::DATA_DIR . '/compact_array.json5', Json5Encoder::encode($array));
        self::assertStringEqualsFile(self::DATA_DIR . '/compact_array.json', JsonEncoder::encode($array));
        self::assertStringEqualsFile(self::DATA_DIR . '/compact_array.jsonc', JsonCEncoder::encode($array));
    }

    public function testInlineArray(): void
    {
        $array = new InlineArray([
            new Comment('begin'),
            'value1',
            'value2',
            new Comment('middle'),
            'value3',
            'value4',
            new Comment('end'),
        ]);

        self::assertStringEqualsFile(self::DATA_DIR . '/inline_array.json5', Json5Encoder::encode($array));
        self::assertStringEqualsFile(self::DATA_DIR . '/inline_array.json', JsonEncoder::encode($array));
        self::assertStringEqualsFile(self::DATA_DIR . '/inline_array.jsonc', JsonCEncoder::encode($array));
    }

    public function testObject(): void
    {
        $object = [
            new Comment('begin'), // keys are ignored
            'key1' => 'value1',
            'key2' => 'value2',
            new Comment('middle'),
            'key3' => 'value3',
            'key4' => 'value4',
            new Comment('end'),
        ];

        self::assertStringEqualsFile(self::DATA_DIR . '/object.json5', Json5Encoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/object.json', JsonEncoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/object.jsonc', JsonCEncoder::encode($object));
    }

    public function testCompactObject(): void
    {
        $object = new CompactObject([
            new Comment('begin'), // keys are ignored
            'key1' => 'value1',
            'key2' => 'value2',
            new Comment('middle'),
            'key3' => 'value3',
            'key4' => 'value4',
            new Comment('end'),
        ]);

        self::assertStringEqualsFile(self::DATA_DIR . '/compact_object.json5', Json5Encoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/compact_object.json', JsonEncoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/compact_object.jsonc', JsonCEncoder::encode($object));
    }

    public function testInlineObject(): void
    {
        $object = new InlineObject([
            new Comment('begin'), // keys are ignored
            'key1' => 'value1',
            'key2' => 'value2',
            new Comment('middle'),
            'key3' => 'value3',
            'key4' => 'value4',
            new Comment('end'),
        ]);

        self::assertStringEqualsFile(self::DATA_DIR . '/inline_object.json5', Json5Encoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/inline_object.json', JsonEncoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/inline_object.jsonc', JsonCEncoder::encode($object));
    }

    public function testTwoComments(): void
    {
        $object = [
            'key1' => 'value1',
            'key2' => 'value2',
            new Comment('comment1'),
            new Comment('comment2'),
            'key3' => 'value3',
            'key4' => 'value4',
        ];

        self::assertStringEqualsFile(self::DATA_DIR . '/2comments.json5', Json5Encoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/2comments.json', JsonEncoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/2comments.jsonc', JsonCEncoder::encode($object));

        $compact = new CompactObject($object);
        self::assertStringEqualsFile(self::DATA_DIR . '/2comments_compact.json5', Json5Encoder::encode($compact));
        self::assertStringEqualsFile(self::DATA_DIR . '/2comments_compact.json', JsonEncoder::encode($compact));
        self::assertStringEqualsFile(self::DATA_DIR . '/2comments_compact.jsonc', JsonCEncoder::encode($compact));

        $inline = new InlineObject($object);
        self::assertStringEqualsFile(self::DATA_DIR . '/2comments_inline.json5', Json5Encoder::encode($inline));
        self::assertStringEqualsFile(self::DATA_DIR . '/2comments_inline.json', JsonEncoder::encode($inline));
        self::assertStringEqualsFile(self::DATA_DIR . '/2comments_inline.jsonc', JsonCEncoder::encode($inline));
    }
}
