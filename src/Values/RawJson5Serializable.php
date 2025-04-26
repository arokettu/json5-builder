<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values;

/**
 * @internal Use at your own risk
 */
interface RawJson5Serializable
{
    public function json5SerializeRaw(): string;
}
