<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values\Internal;

use IteratorAggregate;
use JsonSerializable;

/**
 * @internal
 */
interface IterableValueInterface extends IteratorAggregate, JsonSerializable
{
}
