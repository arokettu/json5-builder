<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values;

use Arokettu\Json5\RawJson5Serializable;

final class HexInteger implements RawJson5Serializable
{
    public function __construct(
        public readonly int $value,
    ) {
    }

    public function getRawJson5(): string
    {
        if ($this->value >= 0) {
            return '0x' . strtoupper(dechex($this->value));
        } else {
            return '-0x' . strtoupper(dechex(-$this->value));
        }
    }
}
