<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Json5\Values;

use JsonSerializable;
use ValueError;

final class CommentDecorator implements JsonSerializable
{
    public function __construct(
        public readonly mixed $value,
        public readonly string|null $commentBefore = null,
        public readonly string|null $commentAfter = null,
    ) {
        if ($this->commentAfter !== null && str_contains($this->commentAfter, "\n")) {
            throw new ValueError('The comment after must be a single line string');
        }
    }

    public function jsonSerialize(): mixed
    {
        return $this->value;
    }
}
