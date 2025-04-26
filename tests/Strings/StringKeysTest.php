<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Strings;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\Options;
use PHPUnit\Framework\TestCase;

class StringKeysTest extends TestCase
{
    public function testStrings(): void
    {
        $singleQuotes = new Options(keyQuotes: Options\Quotes::Single, bareKeys: Options\BareKeys::None, indent: '');
        $doubleQuotes = new Options(keyQuotes: Options\Quotes::Double, bareKeys: Options\BareKeys::None, indent: '');

        self::assertEquals("{\n'abcd': null,\n}\n", Json5Encoder::encode(['abcd' => null], $singleQuotes));
        self::assertEquals("{\n\"abcd\": null,\n}\n", Json5Encoder::encode(['abcd' => null], $doubleQuotes));

        // special characters

        self::assertEquals("{\n'\u0000\u0001\\r': 1,\n}\n", Json5Encoder::encode(["\0\1\r" => 1], $singleQuotes));
        self::assertEquals("{\n\"\u0000\u0001\\r\": 1,\n}\n", Json5Encoder::encode(["\0\1\r" => 1], $doubleQuotes));
    }

    public function testAutodetectQuotes(): void
    {
        $singleQuotesNoDetect = new Options(keyQuotes: Options\Quotes::Single, tryOtherQuotes: false);
        $singleQuotesDetect   = new Options(keyQuotes: Options\Quotes::Single);
        $doubleQuotesNoDetect = new Options(keyQuotes: Options\Quotes::Double, tryOtherQuotes: false);
        $doubleQuotesDetect   = new Options(keyQuotes: Options\Quotes::Double);

        $strings = [
            'simple' => 1,
            'not so simple' => 2,
            "that's a quote here" => 3,
            'a so called "quote"' => 4,
            'both \' and " here' => 5,
        ];

        self::assertEquals(<<<JSON5
            {
                simple: 1,
                'not so simple': 2,
                'that\'s a quote here': 3,
                'a so called "quote"': 4,
                'both \' and " here': 5,
            }

            JSON5, Json5Encoder::encode($strings, $singleQuotesNoDetect));
        self::assertEquals(<<<JSON5
            {
                simple: 1,
                'not so simple': 2,
                "that's a quote here": 3,
                'a so called "quote"': 4,
                'both \' and " here': 5,
            }

            JSON5, Json5Encoder::encode($strings, $singleQuotesDetect));
        self::assertEquals(<<<JSON5
            {
                simple: 1,
                "not so simple": 2,
                "that's a quote here": 3,
                "a so called \"quote\"": 4,
                "both ' and \" here": 5,
            }

            JSON5, Json5Encoder::encode($strings, $doubleQuotesNoDetect));
        self::assertEquals(<<<JSON5
            {
                simple: 1,
                "not so simple": 2,
                "that's a quote here": 3,
                'a so called "quote"': 4,
                "both ' and \" here": 5,
            }

            JSON5, Json5Encoder::encode($strings, $doubleQuotesDetect));
    }

    public function testQuotelessKeys(): void
    {
        $i = 0;
        $data = [
            'simple' => ++$i,
            '2digit' => ++$i,
            'digit3' => ++$i,
            'with"quote' => ++$i,
            "with'quote" => ++$i,
            '_ok_' => ++$i,
            '$ok$' => ++$i,
            'ключ' => ++$i,
            '鍵' => ++$i,
            'cmárk⁀123' => ++$i, // combining mark, connector
            'Auf‌lage﹎क्‍ष' => ++$i, // ZWNJ, ZWJ
            'com,ma' => ++$i,
        ];

        self::assertEquals(<<<'JSON5'
            {
                simple: 1,
                '2digit': 2,
                digit3: 3,
                'with"quote': 4,
                "with'quote": 5,
                _ok_: 6,
                $ok$: 7,
                'ключ': 8,
                '鍵': 9,
                'cmárk⁀123': 10,
                'Auf‌lage﹎क्‍ष': 11,
                'com,ma': 12,
            }

            JSON5, Json5Encoder::encode($data, new Options()));

        self::assertEquals(<<<'JSON5'
            {
                simple: 1,
                '2digit': 2,
                digit3: 3,
                'with"quote': 4,
                "with'quote": 5,
                _ok_: 6,
                $ok$: 7,
                ключ: 8,
                鍵: 9,
                cmárk⁀123: 10,
                Auf‌lage﹎क्‍ष: 11,
                'com,ma': 12,
            }

            JSON5, Json5Encoder::encode($data, new Options(bareKeys: Options\BareKeys::Unicode)));
    }
}
