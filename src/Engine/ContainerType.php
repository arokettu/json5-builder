<?php

declare(strict_types=1);

namespace Arokettu\Json5\Engine;

enum ContainerType
{
    case Regular;
    case Compact;
    case Inline;
}
