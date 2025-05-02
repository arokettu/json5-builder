<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values\Internal;

/**
 * @internal Use at your own risk
 */
interface RawJson5Serializable
{
    public function json5SerializeRaw(): string;
}
