<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Formatting;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\JsonCEncoder;
use Arokettu\Json5\Values\Comment;
use Arokettu\Json5\Values\CommentDecorator;
use Arokettu\Json5\Values\InlineObject;
use PHPUnit\Framework\TestCase;

/**
 * No need to test it for JSON
 */
final class NestedCommentTest extends TestCase
{
    public function testNoReplacementInFullSize(): void
    {
        $obj = [
            'k1' => new CommentDecorator('v1', '/* begin */', '/* end */'),
            new Comment('/* standalone */'),
        ];

        self::assertEquals(<<<JSON5
            {
                // /* begin */
                k1: "v1", // /* end */
                // /* standalone */
            }

            JSON5, Json5Encoder::encode($obj));
        self::assertEquals(<<<JSONC
            {
                // /* begin */
                "k1": "v1" // /* end */
                // /* standalone */
            }

            JSONC, JsonCEncoder::encode($obj));
    }

    public function testReplacementInFullSize(): void
    {
        $obj = [
            'k1' => new CommentDecorator('v1', '/* begin */', '/* end */'),
            new Comment('/* standalone */'),
        ];

        self::assertEquals(<<<JSON5
            { /* /* begin *\u{200b}/ */ k1: "v1" /* /* end *\u{200b}/ */, /* /* standalone *\u{200b}/ */ }

            JSON5, Json5Encoder::encode(new InlineObject($obj)));
        self::assertEquals(<<<JSONC
            { /* /* begin *\u{200b}/ */ "k1": "v1" /* /* end *\u{200b}/ */ /* /* standalone *\u{200b}/ */ }

            JSONC, JsonCEncoder::encode(new InlineObject($obj)));
    }
}
