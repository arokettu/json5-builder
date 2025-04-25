<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Strings;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\Options;
use PHPUnit\Framework\TestCase;

class StringValuesTest extends TestCase
{
    public function testStrings(): void
    {
        $singleQuotes = new Options(valueQuotes: Options\Quotes::Single);
        $doubleQuotes = new Options(valueQuotes: Options\Quotes::Double);

        self::assertEquals("'abcd'\n", Json5Encoder::encode('abcd', $singleQuotes));
        self::assertEquals("\"abcd\"\n", Json5Encoder::encode('abcd', $doubleQuotes));

        // special characters

        self::assertEquals("'\u0000\u0001\\r'\n", Json5Encoder::encode("\0\1\r", $singleQuotes));
        self::assertEquals("\"\u0000\u0001\\r\"\n", Json5Encoder::encode("\0\1\r", $doubleQuotes));
    }

    public function testAutodetectQuotes(): void
    {
        $singleQuotesNoDetect = new Options(valueQuotes: Options\Quotes::Single, tryOtherQuotes: false);
        $singleQuotesDetect   = new Options(valueQuotes: Options\Quotes::Single, tryOtherQuotes: true);
        $doubleQuotesNoDetect = new Options(valueQuotes: Options\Quotes::Double, tryOtherQuotes: false);
        $doubleQuotesDetect   = new Options(valueQuotes: Options\Quotes::Double, tryOtherQuotes: true);

        $strings = [
            'simple',
            "that's a quote here",
            'a so called "quote"',
            'both \' and " here',
        ];

        self::assertEquals(<<<JSON5
            [
                'simple',
                'that\'s a quote here',
                'a so called "quote"',
                'both \' and " here',
            ]

            JSON5, Json5Encoder::encode($strings, $singleQuotesNoDetect));
        self::assertEquals(<<<JSON5
            [
                'simple',
                "that's a quote here",
                'a so called "quote"',
                'both \' and " here',
            ]

            JSON5, Json5Encoder::encode($strings, $singleQuotesDetect));
        self::assertEquals(<<<JSON5
            [
                "simple",
                "that's a quote here",
                "a so called \"quote\"",
                "both ' and \" here",
            ]

            JSON5, Json5Encoder::encode($strings, $doubleQuotesNoDetect));
        self::assertEquals(<<<JSON5
            [
                "simple",
                "that's a quote here",
                'a so called "quote"',
                "both ' and \" here",
            ]

            JSON5, Json5Encoder::encode($strings, $doubleQuotesDetect));
    }
}
