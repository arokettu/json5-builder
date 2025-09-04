<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Strings;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonCEncoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Options;
use PHPUnit\Framework\TestCase;

// phpcs:disable Generic.Files.LineLength.TooLong
final class StringValuesTest extends TestCase
{
    public function testStrings(): void
    {
        $singleQuotes = new Options(valueQuotes: Options\Quotes::Single);
        $doubleQuotes = new Options(valueQuotes: Options\Quotes::Double);

        self::assertEquals("'abcd'\n", Json5Encoder::encode('abcd', $singleQuotes));
        self::assertEquals("\"abcd\"\n", Json5Encoder::encode('abcd', $doubleQuotes));

        self::assertEquals("\"abcd\"\n", JsonEncoder::encode('abcd', $singleQuotes));
        self::assertEquals("\"abcd\"\n", JsonEncoder::encode('abcd', $doubleQuotes));

        self::assertEquals("\"abcd\"\n", JsonCEncoder::encode('abcd', $singleQuotes));
        self::assertEquals("\"abcd\"\n", JsonCEncoder::encode('abcd', $doubleQuotes));

        // special characters

        self::assertEquals("'\u0000\u0001\\r'\n", Json5Encoder::encode("\0\1\r", $singleQuotes));
        self::assertEquals("\"\u0000\u0001\\r\"\n", Json5Encoder::encode("\0\1\r", $doubleQuotes));

        self::assertEquals("\"\u0000\u0001\\r\"\n", JsonEncoder::encode("\0\1\r", $singleQuotes));
        self::assertEquals("\"\u0000\u0001\\r\"\n", JsonEncoder::encode("\0\1\r", $doubleQuotes));

        self::assertEquals("\"\u0000\u0001\\r\"\n", JsonCEncoder::encode("\0\1\r", $singleQuotes));
        self::assertEquals("\"\u0000\u0001\\r\"\n", JsonCEncoder::encode("\0\1\r", $doubleQuotes));
    }

    public function testAutodetectQuotes(): void
    {
        $singleQuotesNoDetect = new Options(valueQuotes: Options\Quotes::Single, tryOtherQuotes: false);
        $singleQuotesDetect   = new Options(valueQuotes: Options\Quotes::Single);
        $doubleQuotesNoDetect = new Options(valueQuotes: Options\Quotes::Double, tryOtherQuotes: false);
        $doubleQuotesDetect   = new Options(valueQuotes: Options\Quotes::Double);

        $strings = [
            'simple',
            "that's a quote here",
            'a so called "quote"',
            'both \' and " here',
        ];

        self::assertStringEqualsFile(__DIR__ . '/data/values_quotes_single_noauto.json5', Json5Encoder::encode($strings, $singleQuotesNoDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/values_quotes_single_auto.json5', Json5Encoder::encode($strings, $singleQuotesDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/values_quotes_double_noauto.json5', Json5Encoder::encode($strings, $doubleQuotesNoDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/values_quotes_double_auto.json5', Json5Encoder::encode($strings, $doubleQuotesDetect));

        // json ignores that all
        self::assertStringEqualsFile(__DIR__ . '/data/values_quotes.json', JsonEncoder::encode($strings, $singleQuotesNoDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/values_quotes.json', JsonEncoder::encode($strings, $singleQuotesDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/values_quotes.json', JsonEncoder::encode($strings, $doubleQuotesNoDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/values_quotes.json', JsonEncoder::encode($strings, $doubleQuotesDetect));

        // jsonc ignores that all
        self::assertStringEqualsFile(__DIR__ . '/data/values_quotes.json', JsonCEncoder::encode($strings, $singleQuotesNoDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/values_quotes.json', JsonCEncoder::encode($strings, $singleQuotesDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/values_quotes.json', JsonCEncoder::encode($strings, $doubleQuotesNoDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/values_quotes.json', JsonCEncoder::encode($strings, $doubleQuotesDetect));
    }
}
