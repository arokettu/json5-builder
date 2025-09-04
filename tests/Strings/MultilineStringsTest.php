<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Strings;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Options;
use PHPUnit\Framework\TestCase;

// phpcs:disable Generic.Files.LineLength.TooLong
class MultilineStringsTest extends TestCase
{
    public function testMultiline(): void
    {
        $text = <<<TEXT
            Lorem ipsum dolor sit amet,
            consectetur adipiscing elit.
            Fusce enim augue, vestibulum
            quis odio vitae, vulputate
            euismod magna.
            TEXT;
        $textnl = $text . "\n";

        // not enabled by default
        self::assertStringEqualsFile(__DIR__ . '/data/multiline_off.json5', Json5Encoder::encode(['text' => $text]));
        self::assertStringEqualsFile(__DIR__ . '/data/multiline.json', JsonEncoder::encode(['text' => $text])); // ignored for json

        $options = new Options(keyQuotes: Options\Quotes::Single, multilineStrings: true);

        self::assertStringEqualsFile(__DIR__ . '/data/multiline.json5', Json5Encoder::encode(['text' => $text], $options));
        self::assertStringEqualsFile(__DIR__ . '/data/multiline.json', JsonEncoder::encode(['text' => $text], $options)); // ignored
        // last nl should not create a newline
        self::assertStringEqualsFile(__DIR__ . '/data/multiline_nl.json5', Json5Encoder::encode(['text' => $textnl], $options));
        self::assertStringEqualsFile(__DIR__ . '/data/multiline_nl.json', JsonEncoder::encode(['text' => $textnl], $options)); // ignored
        // skip this for newline-only strings
        self::assertStringEqualsFile(__DIR__ . '/data/newline.json5', Json5Encoder::encode(['text' => "\n\n\n\n\n"], $options));
        self::assertStringEqualsFile(__DIR__ . '/data/newline.json', JsonEncoder::encode(['text' => "\n\n\n\n\n"], $options));
        // do not apply the mode to keys
        self::assertStringEqualsFile(__DIR__ . '/data/multiline_key.json5', Json5Encoder::encode([$text => $textnl], $options));
        self::assertStringEqualsFile(__DIR__ . '/data/multiline_key.json', JsonEncoder::encode([$text => $textnl], $options));
    }
}
