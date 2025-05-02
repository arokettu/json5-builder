<?php

declare(strict_types=1);

namespace Arokettu\Json5\Values;

use IteratorAggregate;
use JsonSerializable;

/**
 * @internal
 */
interface IterableValueInterface extends IteratorAggregate, JsonSerializable
{
}
