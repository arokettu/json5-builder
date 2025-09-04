<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Formatting;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonCEncoder;
use Arokettu\Json5\JsonEncoder;
use Arokettu\Json5\Options;
use PHPUnit\Framework\TestCase;
use ValueError;

final class IndentTest extends TestCase
{
    public function testIndent(): void
    {
        $data = [
            'obj' => [
                'list' => ['item1', 'item2', 'item3']
            ]
        ];

        // default: 4 spaces
        self::assertStringEqualsFile(__DIR__ . '/data/indent/4spaces.json5', Json5Encoder::encode($data));
        self::assertStringEqualsFile(__DIR__ . '/data/indent/4spaces.json', JsonEncoder::encode($data));
        self::assertStringEqualsFile(__DIR__ . '/data/indent/4spaces.json', JsonCEncoder::encode($data));

        $twoSpaces = new Options(indent: '  ');
        self::assertStringEqualsFile(__DIR__ . '/data/indent/2spaces.json5', Json5Encoder::encode($data, $twoSpaces));
        self::assertStringEqualsFile(__DIR__ . '/data/indent/2spaces.json', JsonEncoder::encode($data, $twoSpaces));
        self::assertStringEqualsFile(__DIR__ . '/data/indent/2spaces.json', JsonCEncoder::encode($data, $twoSpaces));

        $tab = new Options(indent: "\t");
        self::assertStringEqualsFile(__DIR__ . '/data/indent/tabs.json5', Json5Encoder::encode($data, $tab));
        self::assertStringEqualsFile(__DIR__ . '/data/indent/tabs.json', JsonEncoder::encode($data, $tab));
        self::assertStringEqualsFile(__DIR__ . '/data/indent/tabs.json', JsonCEncoder::encode($data, $tab));

        $empty = new Options(indent: '');
        self::assertStringEqualsFile(__DIR__ . '/data/indent/empty.json5', Json5Encoder::encode($data, $empty));
        self::assertStringEqualsFile(__DIR__ . '/data/indent/empty.json', JsonEncoder::encode($data, $empty));
        self::assertStringEqualsFile(__DIR__ . '/data/indent/empty.json', JsonCEncoder::encode($data, $empty));

        // verify all possible characters
        $all = new Options(indent: $chars = "\x20\x09\x0a\x0d");
        $allCharsJson5 = <<<JSON5
            {
            {$chars}obj: {
            {$chars}{$chars}list: [
            {$chars}{$chars}{$chars}"item1",
            {$chars}{$chars}{$chars}"item2",
            {$chars}{$chars}{$chars}"item3",
            {$chars}{$chars}],
            {$chars}},
            }

            JSON5;
        $allCharsJson = <<<JSON
            {
            {$chars}"obj": {
            {$chars}{$chars}"list": [
            {$chars}{$chars}{$chars}"item1",
            {$chars}{$chars}{$chars}"item2",
            {$chars}{$chars}{$chars}"item3"
            {$chars}{$chars}]
            {$chars}}
            }

            JSON;
        self::assertEquals($allCharsJson5, Json5Encoder::encode($data, $all));
        self::assertEquals($allCharsJson, JsonEncoder::encode($data, $all));
        self::assertEquals($allCharsJson, JsonCEncoder::encode($data, $all));

        // set by property too
        $all2 = new Options();
        $all2->indent = $chars;
        self::assertEquals($allCharsJson5, Json5Encoder::encode($data, $all2));
        self::assertEquals($allCharsJson, JsonEncoder::encode($data, $all2));
        self::assertEquals($allCharsJson, JsonCEncoder::encode($data, $all2));
    }

    public function testNonWhitespace(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage('Indent must contain only whitespace characters');

        new Options(indent: 'test');
    }

    public function testInvalidWhitespace(): void
    {
        $indent = "\x0c\x0c"; // passes \s check but is invalid in JSON

        // sanity check
        self::assertMatchesRegularExpression('/^\s+$/', $indent);

        self::expectException(ValueError::class);
        self::expectExceptionMessage('Indent must contain only whitespace characters');

        new Options(indent: $indent);
    }

    public function testPropertyValidatedToo(): void
    {
        $indent = "\x0c\x0c"; // passes \s check but is invalid in JSON

        // sanity check
        self::assertMatchesRegularExpression('/^\s+$/', $indent);

        self::expectException(ValueError::class);
        self::expectExceptionMessage('Indent must contain only whitespace characters');

        $options = new Options();
        $options->indent = $indent;
    }
}
