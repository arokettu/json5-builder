<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values;

use JsonSerializable;

final readonly class HexInteger implements Internal\RawJson5Serializable, JsonSerializable
{
    public function __construct(
        public int $value,
    ) {
    }

    public function json5SerializeRaw(): string
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
