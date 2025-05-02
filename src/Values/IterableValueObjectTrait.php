<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values;

use ArrayObject;
use Traversable;

/**
 * @internal
 */
trait IterableValueObjectTrait
{
    private readonly Traversable $traversable;

    public function jsonSerialize(): ArrayObject
    {
        return new ArrayObject(iterator_to_array($this->traversable, true));
    }
}
