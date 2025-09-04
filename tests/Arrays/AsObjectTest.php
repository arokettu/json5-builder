<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Arrays;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonCEncoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Options;
use PHPUnit\Framework\TestCase;

final class AsObjectTest extends TestCase
{
    public function testStrings(): void
    {
        // arrays with string keys naturally become objects
        $obj = [
            'key' => 'value',
            'other_key' => 'other value',
        ];

        self::assertStringEqualsFile(__DIR__ . '/data/object_string.json5', Json5Encoder::encode($obj));

        self::assertStringEqualsFile(__DIR__ . '/data/object_string.json', JsonEncoder::encode($obj));

        self::assertStringEqualsFile(__DIR__ . '/data/object_string.json', JsonCEncoder::encode($obj));
    }

    public function testInt(): void
    {
        // non-sequential arrays become objects too
        $obj = [1 => 'a', 2 => 'b'];
        $options = new Options(keyQuotes: Options\Quotes::Single);

        self::assertStringEqualsFile(__DIR__ . '/data/object_int.json5', Json5Encoder::encode($obj, $options));
        self::assertStringEqualsFile(__DIR__ . '/data/object_int.json', JsonEncoder::encode($obj, $options));
        self::assertStringEqualsFile(__DIR__ . '/data/object_int.json', JsonCEncoder::encode($obj, $options));
    }
}
