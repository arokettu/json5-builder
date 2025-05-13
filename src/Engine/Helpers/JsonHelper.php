<?php

declare(strict_types=1);

namespace Arokettu\Json5\Engine\Helpers;

use JsonException;
use ValueError;

/**
 * @internal
 */
final class JsonHelper
{
    public static function encode(mixed $value, int $options = 0): string
    {
        try {
            return json_encode($value, $options | JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ValueError('Unable to encode value: ' . $e->getMessage(), previous: $e);
        }
    }
}
