<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values;

interface Json5Serializable
{
    public function json5Serialize(): mixed;
}
