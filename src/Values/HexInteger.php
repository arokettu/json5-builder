<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values;

use JsonSerializable;

final class HexInteger implements RawJson5Serializable, JsonSerializable
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

    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
