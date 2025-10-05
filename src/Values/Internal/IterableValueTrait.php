<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Json5\Values\Internal;

use Arokettu\Json5\Values\Json5Serializable;
use ArrayIterator;
use Generator;
use JsonSerializable;
use stdClass;
use Traversable;

/**
 * @internal
 */
trait IterableValueTrait
{
    private readonly Traversable $traversable;

    public function __construct(iterable|stdClass $iterable)
    {
        // stdClass -> array
        if ($iterable instanceof stdClass) {
            $iterable = get_object_vars($iterable);
        }
        // array -> Traversable
        if (\is_array($iterable)) {
            $iterable = new ArrayIterator($iterable);
        }

        $this->traversable = $iterable;
    }

    public static function fromSerializable(iterable|stdClass|Json5Serializable|JsonSerializable $iterable): self
    {
        start:

        if ($iterable instanceof Json5Serializable) {
            $iterable = $iterable->json5Serialize();
            goto start; // restart parsing
        }

        if ($iterable instanceof JsonSerializable) {
            $iterable = $iterable->jsonSerialize();
            goto start; // restart parsing
        }

        return new self($iterable);
    }

    public static function fromJsonSerializable(iterable|stdClass|JsonSerializable $iterable): self
    {
        start:

        if ($iterable instanceof JsonSerializable) {
            $iterable = $iterable->jsonSerialize();
            goto start; // restart parsing
        }

        return new self($iterable);
    }

    public function getIterator(): Generator
    {
        yield from $this->traversable;
    }
}
