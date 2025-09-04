<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Strings;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonCEncoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Options;
use PHPUnit\Framework\TestCase;

// phpcs:disable Generic.Files.LineLength.TooLong
final class StringKeysTest extends TestCase
{
    public function testStrings(): void
    {
        $singleQuotes = new Options(keyQuotes: Options\Quotes::Single, bareKeys: Options\BareKeys::None, indent: '');
        $doubleQuotes = new Options(keyQuotes: Options\Quotes::Double, bareKeys: Options\BareKeys::None, indent: '');

        self::assertEquals("{\n'abcd': null,\n}\n", Json5Encoder::encode(['abcd' => null], $singleQuotes));
        self::assertEquals("{\n\"abcd\": null,\n}\n", Json5Encoder::encode(['abcd' => null], $doubleQuotes));

        self::assertEquals("{\n\"abcd\": null\n}\n", JsonEncoder::encode(['abcd' => null], $singleQuotes)); // ignore
        self::assertEquals("{\n\"abcd\": null\n}\n", JsonEncoder::encode(['abcd' => null], $doubleQuotes)); // ignore

        self::assertEquals("{\n\"abcd\": null\n}\n", JsonCEncoder::encode(['abcd' => null], $singleQuotes)); // ignore
        self::assertEquals("{\n\"abcd\": null\n}\n", JsonCEncoder::encode(['abcd' => null], $doubleQuotes)); // ignore

        // special characters

        self::assertEquals("{\n'\u0000\u0001\\r': 1,\n}\n", Json5Encoder::encode(["\0\1\r" => 1], $singleQuotes));
        self::assertEquals("{\n\"\u0000\u0001\\r\": 1,\n}\n", Json5Encoder::encode(["\0\1\r" => 1], $doubleQuotes));

        self::assertEquals("{\n\"\u0000\u0001\\r\": 1\n}\n", JsonEncoder::encode(["\0\1\r" => 1], $singleQuotes)); // ignore
        self::assertEquals("{\n\"\u0000\u0001\\r\": 1\n}\n", JsonEncoder::encode(["\0\1\r" => 1], $doubleQuotes)); // ignore

        self::assertEquals("{\n\"\u0000\u0001\\r\": 1\n}\n", JsonCEncoder::encode(["\0\1\r" => 1], $singleQuotes)); // ignore
        self::assertEquals("{\n\"\u0000\u0001\\r\": 1\n}\n", JsonCEncoder::encode(["\0\1\r" => 1], $doubleQuotes)); // ignore
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

        self::assertStringEqualsFile(__DIR__ . '/data/keys_quotes_single_noauto.json5', Json5Encoder::encode($strings, $singleQuotesNoDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/keys_quotes_single_auto.json5', Json5Encoder::encode($strings, $singleQuotesDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/keys_quotes_double_noauto.json5', Json5Encoder::encode($strings, $doubleQuotesNoDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/keys_quotes_double_auto.json5', Json5Encoder::encode($strings, $doubleQuotesDetect));

        // json should ignore all options
        self::assertStringEqualsFile(__DIR__ . '/data/keys_quotes.json', JsonEncoder::encode($strings, $singleQuotesNoDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/keys_quotes.json', JsonEncoder::encode($strings, $singleQuotesDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/keys_quotes.json', JsonEncoder::encode($strings, $doubleQuotesNoDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/keys_quotes.json', JsonEncoder::encode($strings, $doubleQuotesDetect));

        // jsonc should ignore all options
        self::assertStringEqualsFile(__DIR__ . '/data/keys_quotes.json', JsonCEncoder::encode($strings, $singleQuotesNoDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/keys_quotes.json', JsonCEncoder::encode($strings, $singleQuotesDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/keys_quotes.json', JsonCEncoder::encode($strings, $doubleQuotesNoDetect));
        self::assertStringEqualsFile(__DIR__ . '/data/keys_quotes.json', JsonCEncoder::encode($strings, $doubleQuotesDetect));
    }

    public function testBareKeys(): void
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

        self::assertStringEqualsFile(__DIR__ . '/data/keys_bare_ascii.json5', Json5Encoder::encode($data, new Options(keyQuotes: Options\Quotes::Single)));
        self::assertStringEqualsFile(__DIR__ . '/data/keys_bare_unicode.json5', Json5Encoder::encode($data, new Options(keyQuotes: Options\Quotes::Single, bareKeys: Options\BareKeys::Unicode)));
        self::assertStringEqualsFile(__DIR__ . '/data/keys_bare_none.json5', Json5Encoder::encode($data, new Options(keyQuotes: Options\Quotes::Single, bareKeys: Options\BareKeys::None)));

        // json ignores it all
        self::assertStringEqualsFile(__DIR__ . '/data/keys_bare.json', JsonEncoder::encode($data, new Options()));
        self::assertStringEqualsFile(__DIR__ . '/data/keys_bare.json', JsonEncoder::encode($data, new Options(bareKeys: Options\BareKeys::Unicode)));
        self::assertStringEqualsFile(__DIR__ . '/data/keys_bare.json', JsonEncoder::encode($data, new Options(bareKeys: Options\BareKeys::None)));

        // jsonc ignores it all
        self::assertStringEqualsFile(__DIR__ . '/data/keys_bare.json', JsonCEncoder::encode($data, new Options()));
        self::assertStringEqualsFile(__DIR__ . '/data/keys_bare.json', JsonCEncoder::encode($data, new Options(bareKeys: Options\BareKeys::Unicode)));
        self::assertStringEqualsFile(__DIR__ . '/data/keys_bare.json', JsonCEncoder::encode($data, new Options(bareKeys: Options\BareKeys::None)));
    }
}
