<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Strings;

use Arokettu\Json5\Json5Encoder;
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
        self::assertEquals(
            <<<'JSON5'
            {
                text: "Lorem ipsum dolor sit amet,\nconsectetur adipiscing elit.\nFusce enim augue, vestibulum\nquis odio vitae, vulputate\neuismod magna.",
            }

            JSON5,
            Json5Encoder::encode(['text' => $text])
        );

        $options = new Options(multilineStrings: true);

        self::assertEquals(
            <<<'JSON5'
            {
                text: "\
            Lorem ipsum dolor sit amet,\n\
            consectetur adipiscing elit.\n\
            Fusce enim augue, vestibulum\n\
            quis odio vitae, vulputate\n\
            euismod magna.",
            }

            JSON5,
            Json5Encoder::encode(['text' => $text], $options)
        );
        // last nl should not create a newline
        self::assertEquals(
            <<<'JSON5'
            {
                text: "\
            Lorem ipsum dolor sit amet,\n\
            consectetur adipiscing elit.\n\
            Fusce enim augue, vestibulum\n\
            quis odio vitae, vulputate\n\
            euismod magna.\n",
            }

            JSON5,
            Json5Encoder::encode(['text' => $textnl], $options)
        );
        // skip this for newline-only strings
        self::assertEquals(
            <<<'JSON5'
            {
                text: "\n\n\n\n\n",
            }

            JSON5,
            Json5Encoder::encode(['text' => "\n\n\n\n\n"], $options)
        );
        // do not apply the mode to keys
        self::assertEquals(
            <<<'JSON5'
            {
                'Lorem ipsum dolor sit amet,\nconsectetur adipiscing elit.\nFusce enim augue, vestibulum\nquis odio vitae, vulputate\neuismod magna.': "\
            Lorem ipsum dolor sit amet,\n\
            consectetur adipiscing elit.\n\
            Fusce enim augue, vestibulum\n\
            quis odio vitae, vulputate\n\
            euismod magna.\n",
            }

            JSON5,
            Json5Encoder::encode([$text => $textnl], $options)
        );
    }
}
