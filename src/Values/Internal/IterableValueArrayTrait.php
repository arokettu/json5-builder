<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values\Internal;

use Traversable;

/**
 * @internal
 */
trait IterableValueArrayTrait
{
    private readonly Traversable $traversable;

    public function jsonSerialize(): array
    {
        return iterator_to_array($this->traversable, false);
    }
}
