<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values;

final readonly class CompactObject implements Internal\IterableValueInterface
{
    use Internal\IterableValueTrait;
    use Internal\IterableValueObjectTrait;
}
