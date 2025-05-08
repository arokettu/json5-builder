<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Formatting;

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\Values\Comment;
use PHPUnit\Framework\TestCase;
use TypeError;

class CommentTest extends TestCase
{
    public function testNotAllowedAsRoot(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage('Unsupported type: Arokettu\Json5\Values\Comment');

        Json5Encoder::encode(new Comment(''));
    }

    // TODO
}
