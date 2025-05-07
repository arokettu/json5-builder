<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Json5\Values;

final readonly class Comment
{
    public function __construct(
        public string $comment,
    ) {
    }
}
