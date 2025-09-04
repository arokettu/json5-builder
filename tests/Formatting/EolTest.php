<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Formatting;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonCEncoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Values\CompactArray;
use Arokettu\Json5\Values\CompactObject;
use Arokettu\Json5\Values\EndOfLine;
use Arokettu\Json5\Values\InlineArray;
use Arokettu\Json5\Values\InlineObject;
use PHPUnit\Framework\TestCase;
use TypeError;

final class EolTest extends TestCase
{
    private const DATA_DIR = __DIR__ . '/data/eol';

    public function testNotAllowedAsRootJson5(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: Arokettu\Json5\Values\EndOfLine');

        Json5Encoder::encode(new EndOfLine());
    }

    public function testNotAllowedAsRootJson(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: Arokettu\Json5\Values\EndOfLine');

        JsonEncoder::encode(new EndOfLine());
    }

    public function testNotAllowedAsRootJsonC(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: Arokettu\Json5\Values\EndOfLine');

        JsonCEncoder::encode(new EndOfLine());
    }

    public function testArray(): void
    {
        $array = [ // must still be a list
            new EndOfLine(),
            'value1',
            'value2',
            new EndOfLine(),
            'value3',
            'value4',
            new EndOfLine(),
        ];

        self::assertStringEqualsFile(self::DATA_DIR . '/array.json5', Json5Encoder::encode($array));
        self::assertStringEqualsFile(self::DATA_DIR . '/array.json', JsonEncoder::encode($array));
        self::assertStringEqualsFile(self::DATA_DIR . '/array.json', JsonCEncoder::encode($array));
    }

    public function testCompactArray(): void
    {
        $array = new CompactArray([
            new EndOfLine(),
            'value1',
            'value2',
            new EndOfLine(),
            'value3',
            'value4',
            new EndOfLine(),
        ]);

        self::assertStringEqualsFile(self::DATA_DIR . '/compact_array.json5', Json5Encoder::encode($array));
        self::assertStringEqualsFile(self::DATA_DIR . '/compact_array.json', JsonEncoder::encode($array));
        self::assertStringEqualsFile(self::DATA_DIR . '/compact_array.json', JsonCEncoder::encode($array));
    }

    public function testInlineArray(): void
    {
        $array = new InlineArray([
            new EndOfLine(),
            'value1',
            'value2',
            new EndOfLine(),
            'value3',
            'value4',
            new EndOfLine(),
        ]);

        self::assertStringEqualsFile(self::DATA_DIR . '/inline_array.json5', Json5Encoder::encode($array));
        self::assertStringEqualsFile(self::DATA_DIR . '/inline_array.json', JsonEncoder::encode($array));
        self::assertStringEqualsFile(self::DATA_DIR . '/inline_array.json', JsonCEncoder::encode($array));
    }

    public function testObject(): void
    {
        $object = [
            new EndOfLine(), // keys are ignored
            'key1' => 'value1',
            'key2' => 'value2',
            new EndOfLine(),
            'key3' => 'value3',
            'key4' => 'value4',
            new EndOfLine(),
        ];

        self::assertStringEqualsFile(self::DATA_DIR . '/object.json5', Json5Encoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/object.json', JsonEncoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/object.json', JsonCEncoder::encode($object));
    }

    public function testCompactObject(): void
    {
        $object = new CompactObject([
            new EndOfLine(), // keys are ignored
            'key1' => 'value1',
            'key2' => 'value2',
            new EndOfLine(),
            'key3' => 'value3',
            'key4' => 'value4',
            new EndOfLine(),
        ]);

        self::assertStringEqualsFile(self::DATA_DIR . '/compact_object.json5', Json5Encoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/compact_object.json', JsonEncoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/compact_object.json', JsonCEncoder::encode($object));
    }

    public function testInlineObject(): void
    {
        $object = new InlineObject([
            new EndOfLine(), // keys are ignored
            'key1' => 'value1',
            'key2' => 'value2',
            new EndOfLine(),
            'key3' => 'value3',
            'key4' => 'value4',
            new EndOfLine(),
        ]);

        self::assertStringEqualsFile(self::DATA_DIR . '/inline_object.json5', Json5Encoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/inline_object.json', JsonEncoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/inline_object.json', JsonCEncoder::encode($object));
    }

    public function testTwoEols(): void
    {
        $object = [
            'key1' => 'value1',
            'key2' => 'value2',
            new EndOfLine(),
            new EndOfLine(),
            'key3' => 'value3',
            'key4' => 'value4',
        ];

        self::assertStringEqualsFile(self::DATA_DIR . '/2eols.json5', Json5Encoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/2eols.json', JsonEncoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/2eols.json', JsonCEncoder::encode($object));

        $compact = new CompactObject($object);
        self::assertStringEqualsFile(self::DATA_DIR . '/2eols_compact.json5', Json5Encoder::encode($compact));
        self::assertStringEqualsFile(self::DATA_DIR . '/2eols_compact.json', JsonEncoder::encode($compact));
        self::assertStringEqualsFile(self::DATA_DIR . '/2eols_compact.json', JsonCEncoder::encode($compact));

        $inline = new InlineObject($object);
        self::assertStringEqualsFile(self::DATA_DIR . '/2eols_inline.json5', Json5Encoder::encode($inline));
        self::assertStringEqualsFile(self::DATA_DIR . '/2eols_inline.json', JsonEncoder::encode($inline));
        self::assertStringEqualsFile(self::DATA_DIR . '/2eols_inline.json', JsonCEncoder::encode($inline));
    }

    public function testTwoEolsTrailing(): void
    {
        $object = [
            'key1' => 'value1',
            'key2' => 'value2',
            new EndOfLine(),
            new EndOfLine(),
        ];

        self::assertStringEqualsFile(self::DATA_DIR . '/2eols_trailing.json5', Json5Encoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/2eols_trailing.json', JsonEncoder::encode($object));
        self::assertStringEqualsFile(self::DATA_DIR . '/2eols_trailing.json', JsonCEncoder::encode($object));

        $compact = new CompactObject($object);
        self::assertStringEqualsFile(self::DATA_DIR . '/2eols_compact_trailing.json5', Json5Encoder::encode($compact));
        self::assertStringEqualsFile(self::DATA_DIR . '/2eols_compact_trailing.json', JsonEncoder::encode($compact));
        self::assertStringEqualsFile(self::DATA_DIR . '/2eols_compact_trailing.json', JsonCEncoder::encode($compact));

        $inline = new InlineObject($object);
        self::assertStringEqualsFile(self::DATA_DIR . '/2eols_inline_trailing.json5', Json5Encoder::encode($inline));
        self::assertStringEqualsFile(self::DATA_DIR . '/2eols_inline_trailing.json', JsonEncoder::encode($inline));
        self::assertStringEqualsFile(self::DATA_DIR . '/2eols_inline_trailing.json', JsonCEncoder::encode($inline));
    }
}
