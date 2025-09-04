<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Json5\Values\Internal;

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
