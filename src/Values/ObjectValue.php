<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values;

final class ObjectValue implements IterableValueInterface
{
    use IterableValueTrait;
    use IterableValueObjectTrait;
}
