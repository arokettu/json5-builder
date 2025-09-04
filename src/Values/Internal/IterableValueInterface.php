<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

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
