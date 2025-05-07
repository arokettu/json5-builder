<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values;

final class InlineObject implements Internal\IterableValueInterface
{
    use Internal\IterableValueTrait;
    use Internal\IterableValueObjectTrait;
}
