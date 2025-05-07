<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values;

final class Comment
{
    public function __construct(
        public readonly string $comment,
    ) {
    }
}
