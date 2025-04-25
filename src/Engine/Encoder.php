<?php

declare(strict_types=1);

namespace Arokettu\Json5\Engine;

use Arokettu\Json5\Options;
use Arokettu\Json5\RawJson5Serializable;
use ArrayObject;
use JsonSerializable;
use stdClass;

/**
 * @internal
 */
final class Encoder
{
    /**
     * @param resource $resource
     */
    public function __construct(
        private mixed $value,
        private readonly Options $options,
        private $resource,
    ) {
    }

    public function encode(): void
    {
        $this->encodeValue($this->value, '');
        fwrite($this->resource, "\n");
    }

    private function encodeValue(mixed $value, string $indent): void
    {
        if ($value instanceof JsonSerializable) {
            $this->encodeValue($value->jsonSerialize(), $indent);
            return;
        }

        if (is_integer($value)) {
            fwrite($this->resource, json_encode($value));
            return;
        }

        if (is_float($value)) {
            fwrite($this->resource, match (true) {
                is_nan($value) => 'NaN',
                is_infinite($value) => (string)$value,
                default => json_encode($value),
            });
            return;
        }

        if (is_string($value)) {
            $this->encodeString($value);
            return;
        }

        if ($value instanceof RawJson5Serializable) {
            fwrite($this->resource, json_encode($value));
        }

        if (is_array($value)) {
            match (array_is_list($value)) {
                true  => $this->encodeList($value, $indent),
                false => $this->encodeObject($value, $indent),
            };

            return;
        }

        if ($value instanceof stdClass || $value instanceof ArrayObject) {
            $this->encodeObject($value, $indent);
            return;
        }

        throw new \LogicException('Unsupported type');
    }

    private function encodeKey(string $key): void
    {
        if ($this->options->avoidQuotes === false) {
            $this->encodeString($key);
            return;
        }

        if (preg_match('/^[\p{L}$_][\p{L}$_\p{N}\p{Pc}]*$/u', $key)) {
            fwrite($this->resource, $key);
            return;
        }

        $this->encodeString($key);
    }

    private function encodeString(string $string): void
    {
        fwrite($this->resource, json_encode($string));
    }

    private function encodeList(iterable $list, string $indent): void
    {
        $indent2 = $indent . $this->options->indent;

        fwrite($this->resource, "[\n");

        foreach ($list as $value) {
            fwrite($this->resource, $indent2);
            $this->encodeValue($value, $indent2);
            fwrite($this->resource, ",\n");
        }

        fwrite($this->resource, $indent);
        fwrite($this->resource, "]");
    }

    private function encodeObject(mixed $object, string $indent): void
    {
        if ($object instanceof stdClass) {
            $object = get_object_vars($object);
        }

        $indent2 = $indent . $this->options->indent;

        fwrite($this->resource, "{\n");

        foreach ($object as $key => $value) {
            fwrite($this->resource, $indent2);
            $this->encodeKey((string)$key);
            fwrite($this->resource, ": ");
            $this->encodeValue($value, $indent2);
            fwrite($this->resource, ",\n");
        }

        fwrite($this->resource, $indent);
        fwrite($this->resource, "}");
    }
}
