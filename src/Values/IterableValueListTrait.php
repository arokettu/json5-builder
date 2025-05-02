<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values;

use Traversable;

/**
 * @internal
 */
trait IterableValueListTrait
{
    private readonly Traversable $traversable;

    public function jsonSerialize(): array
    {
        return iterator_to_array($this->traversable, false);
    }
}
