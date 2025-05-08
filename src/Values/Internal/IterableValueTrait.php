<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values\Internal;

use Arokettu\Json5\Values\Json5Serializable;
use ArrayIterator;
use ArrayObject;
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

    public function __construct(iterable|stdClass|Json5Serializable|JsonSerializable $iterable)
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

        if (\is_array($iterable)) {
            $iterable = new ArrayIterator($iterable);
        } elseif ($iterable instanceof stdClass) {
            $iterable = new ArrayObject($iterable);
        }

        $this->traversable = $iterable;
    }

    public function getIterator(): Generator
    {
        yield from $this->traversable;
    }
}
