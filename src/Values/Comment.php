<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values;

final readonly class Comment
{
    public function __construct(
        public string $comment,
    ) {
    }
}
