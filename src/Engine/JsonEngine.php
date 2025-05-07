<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Json5\Engine;

use Arokettu\Json5\Options;
use Arokettu\Json5\Values\ArrayValue;
use Arokettu\Json5\Values\Comment;
use Arokettu\Json5\Values\CommentDecorator;
use Arokettu\Json5\Values\CompactArray;
use Arokettu\Json5\Values\CompactObject;
use Arokettu\Json5\Values\EndOfLine;
use Arokettu\Json5\Values\InlineArray;
use Arokettu\Json5\Values\InlineObject;
use Arokettu\Json5\Values\ObjectValue;
use ArrayObject;
use JsonSerializable;
use stdClass;
use TypeError;

/**
 * @internal
 */
final readonly class JsonEngine
{
    use Helpers\RenderCommentTrait;

    private const int STATE_START = 0;
    private const int STATE_AFTER_EOL = 1;
    private const int STATE_AFTER_VALUE = 2;
    private const int STATE_AFTER_COMMENT = 3;

    /**
     * @param resource $resource
     */
    public function __construct(
        private bool $jsonc,
        private mixed $value,
        private Options $options,
        private mixed $resource,
    ) {
    }

    public function encode(): void
    {
        $value = $this->value;

        if ($this->jsonc && $value instanceof CommentDecorator) {
            $this->renderComment($value->commentBefore, '');
        }
        $this->encodeValue($value, '');
        if ($this->jsonc && $value instanceof CommentDecorator) {
            $this->renderCommentLine($value->commentAfter, ' ');
        }
        fwrite($this->resource, "\n");
    }

    private function encodeValue(mixed $value, string $indent): void
    {
        // null

        if ($value === null) {
            fwrite($this->resource, 'null');
            return;
        }

        // scalars

        if (\is_bool($value)) {
            fwrite($this->resource, $value ? 'true' : 'false');
            return;
        }

        if (\is_int($value)) {
            fwrite($this->resource, Helpers\JsonHelper::encode($value, 0));
            return;
        }

        if (\is_float($value)) {
            fwrite($this->resource, Helpers\JsonHelper::encode(
                $value,
                $this->options->preserveZeroFraction ? JSON_PRESERVE_ZERO_FRACTION : 0,
            ));
            return;
        }

        if (\is_string($value)) {
            $this->encodeString($value);
            return;
        }

        // arrays

        if (\is_array($value)) {
            $this->encodeContainer($value, !array_is_list($value), $indent);
            return;
        }

        // objects & unknown values

        match (true) {
            // special objects
            $value instanceof ArrayValue,
                => $this->encodeContainer($value, false, $indent),
            $value instanceof InlineArray,
                => $this->encodeInlineContainer($value, false, $indent),
            $value instanceof CompactArray,
                => $this->encodeCompactContainer($value, false, $indent),
            $value instanceof ObjectValue,
                => $this->encodeContainer($value, true, $indent),
            $value instanceof InlineObject,
                => $this->encodeInlineContainer($value, true, $indent),
            $value instanceof CompactObject,
                => $this->encodeCompactContainer($value, true, $indent),
            // serializables
            $value instanceof JsonSerializable,
                => $this->encodeValue($value->jsonSerialize(), $indent),
            // other objects
            $value instanceof stdClass,
            $value instanceof ArrayObject,
                => $this->encodeContainer((array)$value, true, $indent),
            default
                => throw new TypeError('Unsupported type: ' . get_debug_type($value)),
        };
    }

    private function encodeString(string $string): void
    {
        fwrite($this->resource, Helpers\JsonHelper::encode($string, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    private function encodeContainer(iterable $container, bool $object, string $indent): void
    {
        $indent2 = $indent . $this->options->indent;
        $container = $this->iterableToKVArray($container);

        fwrite($this->resource, $object ? '{' : '[');
        $state = self::STATE_START;

        foreach ($container as $index => [$key, $value]) {
            if ($this->jsonc === false && $value instanceof Comment) {
                continue;
            }

            if ($state === self::STATE_START || $state === self::STATE_AFTER_VALUE) {
                fwrite($this->resource, "\n");
            }

            if ($value instanceof EndOfLine) {
                fwrite($this->resource, "\n");
                $state = self::STATE_AFTER_EOL;
                continue;
            }

            if ($value instanceof Comment) { // jsonc
                $this->renderComment($value->comment, $indent2);
                $state = self::STATE_AFTER_COMMENT;
                continue;
            }

            if ($this->jsonc && $value instanceof CommentDecorator) {
                $this->renderComment($value->commentBefore, $indent2);
            }
            fwrite($this->resource, $indent2);
            if ($object) {
                $this->encodeString((string)$key);
                fwrite($this->resource, ': ');
            }
            $this->encodeValue($value, $indent2);
            if (!$this->skipComma($container, $index + 1)) {
                fwrite($this->resource, ',');
            }
            if ($this->jsonc && $value instanceof CommentDecorator) {
                $this->renderCommentLine($value->commentAfter, ' ');
            }

            $state = self::STATE_AFTER_VALUE;
        }

        if ($state === self::STATE_AFTER_VALUE) {
            fwrite($this->resource, "\n");
        }
        if ($state !== self::STATE_START) {
            fwrite($this->resource, $indent);
        }
        fwrite($this->resource, $object ? '}' : ']');
    }

    private function encodeCompactContainer(iterable $container, bool $object, string $indent): void
    {
        $indent2 = $indent . $this->options->indent;
        $container = $this->iterableToKVArray($container);

        fwrite($this->resource, $object ? '{' : '[');
        $state = self::STATE_START;

        foreach ($container as $index => [$key, $value]) {
            switch ($state) {
                case self::STATE_START:
                    fwrite($this->resource, "\n");
                    break;

                case self::STATE_AFTER_VALUE:
                    if (!$this->skipComma($container, $index)) {
                        fwrite($this->resource, ',');
                    }
                    break;
            }

            if ($value instanceof EndOfLine) {
                fwrite($this->resource, "\n");
                $state = self::STATE_AFTER_EOL;
                continue;
            }

            if ($value instanceof Comment) {
                // render empty line
                if ($state !== self::STATE_START && $state !== self::STATE_AFTER_COMMENT) {
                    fwrite($this->resource, "\n");
                }
                if ($this->jsonc) {
                    $this->renderComment($value->comment, $indent2);
                }
                $state = self::STATE_AFTER_COMMENT;
                continue;
            }

            switch ($state) {
                case self::STATE_START:
                case self::STATE_AFTER_EOL:
                case self::STATE_AFTER_COMMENT:
                    fwrite($this->resource, $indent2);
                    break;

                case self::STATE_AFTER_VALUE:
                    fwrite($this->resource, ' ');
                    break;
            }

            if ($this->jsonc && $value instanceof CommentDecorator) {
                $this->renderInlineComment($value->commentBefore, '', ' ');
            }
            if ($object) {
                $this->encodeString((string)$key);
                fwrite($this->resource, ': ');
            }
            $this->encodeValue($value, $indent2);
            if ($this->jsonc && $value instanceof CommentDecorator) {
                $this->renderInlineComment($value->commentAfter, ' ', '');
            }

            $state = self::STATE_AFTER_VALUE;
        }

        // start means empty; comment already has \n
        if ($state !== self::STATE_START && $state !== self::STATE_AFTER_COMMENT) {
            fwrite($this->resource, "\n");
        }
        if ($state !== self::STATE_START) {
            fwrite($this->resource, $indent);
        }
        fwrite($this->resource, $object ? '}' : ']');
    }

    private function encodeInlineContainer(iterable $container, bool $object, string $indent): void
    {
        $extraPadding = $object ? $this->options->inlineObjectPadding : $this->options->inlineArrayPadding;
        $container = $this->iterableToKVArray($container);

        fwrite($this->resource, $object ? '{' : '[');
        $state = self::STATE_START;

        foreach ($container as $index => [$key, $value]) {
            $skipComma = $this->skipComma($container, $index);

            if ($state === self::STATE_AFTER_VALUE) {
                if (!$this->skipComma($container, $index)) {
                    fwrite($this->resource, ',');
                }
            }

            if ($value instanceof EndOfLine) {
                fwrite($this->resource, "\n");
                $state = self::STATE_AFTER_EOL;
                continue;
            }

            switch ($state) {
                case self::STATE_START:
                    if ($extraPadding) {
                        fwrite($this->resource, ' ');
                    }
                    break;

                case self::STATE_AFTER_VALUE:
                    if ($this->jsonc || !$skipComma) { // if we skipped the comma in JSON, we should also skip the space
                        fwrite($this->resource, ' ');
                    }
                    break;

                case self::STATE_AFTER_COMMENT:
                    if ($this->jsonc) {
                        fwrite($this->resource, ' ');
                    }
                    break;

                case self::STATE_AFTER_EOL:
                    fwrite($this->resource, $indent);
                    fwrite($this->resource, $this->options->indent);
                    break;
            }

            if ($value instanceof Comment) {
                if ($this->jsonc) {
                    $this->renderInlineComment($value->comment, '', '');
                }
                $state = self::STATE_AFTER_COMMENT;
                continue;
            }

            if ($this->jsonc && $value instanceof CommentDecorator) {
                $this->renderInlineComment($value->commentBefore, '', ' ');
            }
            if ($object) {
                $this->encodeString((string)$key);
                fwrite($this->resource, ': ');
            }
            $this->encodeValue($value, $indent);
            if ($this->jsonc && $value instanceof CommentDecorator) {
                $this->renderInlineComment($value->commentAfter, ' ', '');
            }

            $state = self::STATE_AFTER_VALUE;
        }

        if ($extraPadding && $state !== self::STATE_START && $state !== self::STATE_AFTER_EOL) {
            fwrite($this->resource, ' ');
        }
        fwrite($this->resource, $object ? '}' : ']');
    }

    private function iterableToKVArray(iterable $iterable): array
    {
        $result = [];
        foreach ($iterable as $key => $value) {
            $result[] = [$key, $value];
        }
        return $result;
    }

    private function skipComma(array $container, int $index): bool
    {
        for ($i = $index; $i < \count($container); ++$i) {
            $v = $container[$i][1];
            if (!$v instanceof EndOfLine && !$v instanceof Comment) {
                return false;
            }
        }

        return true;
    }
}
