<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values;

final readonly class ListValue implements Internal\IterableValueInterface
{
    use Internal\IterableValueTrait;
    use Internal\IterableValueListTrait;
}
