<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Formatting;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\Options;
use PHPUnit\Framework\TestCase;
use ValueError;

class IndentTest extends TestCase
{
    public function testIndent(): void
    {
        $data = [
            'obj' => [
                'list' => ['item1', 'item2', 'item3']
            ]
        ];

        // default: 4 spaces
        self::assertEquals(<<<JSON5
            {
                obj: {
                    list: [
                        "item1",
                        "item2",
                        "item3",
                    ],
                },
            }

            JSON5, Json5Encoder::encode($data));

        $twoSpaces = new Options(indent: '  ');
        self::assertEquals(<<<JSON5
            {
              obj: {
                list: [
                  "item1",
                  "item2",
                  "item3",
                ],
              },
            }

            JSON5, Json5Encoder::encode($data, $twoSpaces));

        $tab = new Options(indent: "\t");
        self::assertEquals(<<<JSON5
            {
            	obj: {
            		list: [
            			"item1",
            			"item2",
            			"item3",
            		],
            	},
            }

            JSON5, Json5Encoder::encode($data, $tab));

        $empty = new Options(indent: '');
        self::assertEquals(<<<JSON5
            {
            obj: {
            list: [
            "item1",
            "item2",
            "item3",
            ],
            },
            }

            JSON5, Json5Encoder::encode($data, $empty));

        // verify all possible characters
        $all = new Options(indent: $chars = "\x20\x09\x0a\x0d");
        self::assertEquals(<<<JSON5
            {
            {$chars}obj: {
            {$chars}{$chars}list: [
            {$chars}{$chars}{$chars}"item1",
            {$chars}{$chars}{$chars}"item2",
            {$chars}{$chars}{$chars}"item3",
            {$chars}{$chars}],
            {$chars}},
            }

            JSON5, Json5Encoder::encode($data, $all));

        // set by property too
        $all2 = new Options();
        $all2->indent = $chars;
        self::assertEquals(<<<JSON5
            {
            {$chars}obj: {
            {$chars}{$chars}list: [
            {$chars}{$chars}{$chars}"item1",
            {$chars}{$chars}{$chars}"item2",
            {$chars}{$chars}{$chars}"item3",
            {$chars}{$chars}],
            {$chars}},
            }

            JSON5, Json5Encoder::encode($data, $all2));
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
