<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values;

use JsonSerializable;
use ValueError;

final class HexInteger implements Internal\RawJson5Serializable, JsonSerializable
{
    public function __construct(
        public readonly int $value,
        public readonly int $padding = 0,
    ) {
        if ($this->padding < 0) {
            throw new ValueError('Padding must be a non-negative integer');
        }
    }

    public function json5SerializeRaw(): string
    {
        $sign = '';
        $value = $this->value;

        if ($value === PHP_INT_MIN) {
            $sign = '-';
            // negating PHP_INT_MIN makes it float,
            // but signed and unsigned values of PHP_INT_MIN are equal in absolute value
        } elseif ($value < 0) {
            $sign = '-';
            $value = -$value;
        }

        return $sign . '0x' . str_pad(strtoupper(dechex($value)), $this->padding, '0', STR_PAD_LEFT);
    }

    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
