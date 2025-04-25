<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values;

interface RawJson5Serializable
{
    public function getRawJson5(): string;
}
