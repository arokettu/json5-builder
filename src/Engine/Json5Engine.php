<?php

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
use Arokettu\Json5\Values\Internal\RawJson5Serializable;
use Arokettu\Json5\Values\Json5Serializable;
use Arokettu\Json5\Values\ObjectValue;
use ArrayObject;
use JsonSerializable;
use stdClass;
use TypeError;

/**
 * @internal
 */
final class Json5Engine
{
    use Helpers\RenderCommentTrait;

    // IdentifierName patterns
    // UnicodeEscapeSequence is also allowed but ignore it for simplicity
    private const UNICODE_LETTER = '\p{Lu}\p{Ll}\p{Lt}\p{Lm}\p{Lo}\p{Nl}';
    private const UNICODE_COMBINING_MARK = '\p{Mn}\p{Mc}';
    private const UNICODE_DIGIT = '\p{Nd}';
    private const UNICODE_CONNECTOR_PUNCTUATION = '\p{Pc}';
    private const ZWNJ = '\x{200c}';
    private const ZWJ = '\x{200d}';
    private const UNICODE_IDENTIFIER_START = '$_' . self::UNICODE_LETTER;
    private const UNICODE_IDENTIFIER_PART = self::UNICODE_IDENTIFIER_START . self::UNICODE_COMBINING_MARK .
        self::UNICODE_DIGIT . self::UNICODE_CONNECTOR_PUNCTUATION . self::ZWNJ . self::ZWJ;
    private const UNICODE_PATTERN =
        '/^[' . self::UNICODE_IDENTIFIER_START . '][' . self::UNICODE_IDENTIFIER_PART . ']*$/u';
    private const ASCII_IDENTIFIER_START = '$_' . 'a-zA-Z';
    private const ASCII_IDENTIFIER_PART = self::ASCII_IDENTIFIER_START . '0-9';
    private const ASCII_PATTERN =
        '/^[' . self::ASCII_IDENTIFIER_START . '][' . self::ASCII_IDENTIFIER_PART . ']*$/';

    private const STATE_START = 0;
    private const STATE_AFTER_EOL = 1;
    private const STATE_AFTER_VALUE = 2;
    private const STATE_AFTER_COMMENT = 3;

    /**
     * @param resource $resource
     */
    public function __construct(
        private readonly mixed $value,
        private readonly Options $options,
        private $resource,
    ) {
    }

    public function encode(): void
    {
        $value = $this->value;

        if ($value instanceof CommentDecorator) {
            $this->renderComment($value->commentBefore, '');
        }
        $this->encodeValue($value, '');
        if ($value instanceof CommentDecorator) {
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
            fwrite($this->resource, match (true) {
                is_nan($value) => 'NaN',
                is_infinite($value) => $value > 0 ? 'Infinity' : '-Infinity',
                default => Helpers\JsonHelper::encode(
                    $value,
                    $this->options->preserveZeroFraction ? JSON_PRESERVE_ZERO_FRACTION : 0
                ),
            });
            return;
        }

        if (\is_string($value)) {
            $this->encodeString($value, $this->options->valueQuotes, $this->options->multilineStrings);
            return;
        }

        // arrays

        if (\is_array($value)) {
            $this->encodeContainer($value, !array_is_list($value), $indent);
            return;
        }

        // objects & unknown values

        match (true) {
            // very special serializable
            $value instanceof RawJson5Serializable,
                => fwrite($this->resource, $value->json5SerializeRaw()),
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
            $value instanceof Json5Serializable,
                => $this->encodeValue($value->json5Serialize(), $indent),
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

    private function encodeKey(string $key): void
    {
        $pattern = match ($this->options->bareKeys) {
            Options\BareKeys::None => false,
            Options\BareKeys::Ascii => self::ASCII_PATTERN,
            Options\BareKeys::Unicode => self::UNICODE_PATTERN,
        };

        if ($pattern && preg_match($pattern, $key)) {
            fwrite($this->resource, $key);
            return;
        }

        $this->encodeString($key, $this->options->keyQuotes, false);
    }

    private function encodeString(string $string, Options\Quotes $quotes, bool $multilineStrings): void
    {
        // check if changing quotes may result in unescaped quotes
        if ($this->options->tryOtherQuotes) {
            $hasSingleQuotes = str_contains($string, "'");
            $hasDoubleQuotes = str_contains($string, '"');
            $preferrableQuotes = ($hasSingleQuotes xor $hasDoubleQuotes);

            if ($preferrableQuotes) {
                if ($hasSingleQuotes && $quotes === Options\Quotes::Single) {
                    $quotes = Options\Quotes::Double;
                } elseif ($hasDoubleQuotes && $quotes === Options\Quotes::Double) {
                    $quotes = Options\Quotes::Single;
                }
            }
        }

        $encoded = Helpers\JsonHelper::encode($string, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($multilineStrings) {
            $hasLineEndings = str_contains($string, "\n");
            $hasOnlyLineEndings = $hasLineEndings && preg_match('/^\n*$/', $string);

            if ($hasLineEndings && !$hasOnlyLineEndings) {
                $encoded = str_replace("\\n", "\\n\\\n", $encoded);
                $encoded = "\"\\\n" . substr($encoded, 1);

                // do not output a newline for a final newline
                if (str_ends_with($string, "\n")) {
                    $encoded = substr($encoded, 0, -3);
                    $encoded .= '"';
                }
            }
        }

        if ($quotes === Options\Quotes::Single) {
            $encoded = substr($encoded, 1, -1); // remove quotes
            $encoded = str_replace(['\\"', "'"], ['"', "\\'"], $encoded); // unescape " and escape '
            $encoded = "'{$encoded}'"; // add single quotes
        }

        fwrite($this->resource, $encoded);
    }

    private function encodeContainer(iterable $container, bool $object, string $indent): void
    {
        $indent2 = $indent . $this->options->indent;

        fwrite($this->resource, $object ? '{' : '[');
        $state = self::STATE_START;

        foreach ($container as $key => $value) {
            if ($state === self::STATE_START) {
                fwrite($this->resource, "\n");
            }

            if ($value instanceof EndOfLine) {
                fwrite($this->resource, "\n");
                $state = self::STATE_AFTER_EOL;
                continue;
            }

            if ($value instanceof Comment) {
                $this->renderComment($value->comment, $indent2);
                $state = self::STATE_AFTER_COMMENT;
                continue;
            }

            if ($value instanceof CommentDecorator) {
                $this->renderComment($value->commentBefore, $indent2);
            }
            fwrite($this->resource, $indent2);
            if ($object) {
                $this->encodeKey((string)$key);
                fwrite($this->resource, ': ');
            }
            $this->encodeValue($value, $indent2);
            fwrite($this->resource, ',');
            if ($value instanceof CommentDecorator) {
                $this->renderCommentLine($value->commentAfter, ' ');
            }
            fwrite($this->resource, "\n");

            $state = self::STATE_AFTER_VALUE;
        }

        if ($state !== self::STATE_START) {
            fwrite($this->resource, $indent);
        }
        fwrite($this->resource, $object ? '}' : ']');
    }

    private function encodeCompactContainer(iterable $container, bool $object, string $indent): void
    {
        $indent2 = $indent . $this->options->indent;

        fwrite($this->resource, $object ? '{' : '[');
        $state = self::STATE_START;

        foreach ($container as $key => $value) {
            switch ($state) {
                case self::STATE_START:
                    fwrite($this->resource, "\n");
                    break;

                case self::STATE_AFTER_VALUE:
                    fwrite($this->resource, ',');
                    break;
            }

            if ($value instanceof EndOfLine) {
                fwrite($this->resource, "\n");
                $state = self::STATE_AFTER_EOL;
                continue;
            }

            if ($value instanceof Comment) {
                if ($state !== self::STATE_START && $state !== self::STATE_AFTER_COMMENT) {
                    fwrite($this->resource, "\n");
                }
                $this->renderComment($value->comment, $indent2);
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

            if ($value instanceof CommentDecorator) {
                $this->renderInlineComment($value->commentBefore, '', ' ');
            }
            if ($object) {
                $this->encodeKey((string)$key);
                fwrite($this->resource, ': ');
            }
            $this->encodeValue($value, $indent2);
            if ($value instanceof CommentDecorator) {
                $this->renderInlineComment($value->commentAfter, ' ', '');
            }

            $state = self::STATE_AFTER_VALUE;
        }

        if ($state === self::STATE_AFTER_VALUE) {
            fwrite($this->resource, ',');
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

        fwrite($this->resource, $object ? '{' : '[');
        $state = self::STATE_START;

        foreach ($container as $key => $value) {
            if ($state === self::STATE_AFTER_VALUE) {
                fwrite($this->resource, ',');
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
                case self::STATE_AFTER_COMMENT:
                    fwrite($this->resource, ' ');
                    break;

                case self::STATE_AFTER_EOL:
                    fwrite($this->resource, $indent);
                    fwrite($this->resource, $this->options->indent);
                    break;
            }

            if ($value instanceof Comment) {
                $this->renderInlineComment($value->comment, '', '');
                $state = self::STATE_AFTER_COMMENT;
                continue;
            }

            if ($value instanceof CommentDecorator) {
                $this->renderInlineComment($value->commentBefore, '', ' ');
            }
            if ($object) {
                $this->encodeKey((string)$key);
                fwrite($this->resource, ': ');
            }
            $this->encodeValue($value, $indent);
            if ($value instanceof CommentDecorator) {
                $this->renderInlineComment($value->commentAfter, ' ', '');
            }

            $state = self::STATE_AFTER_VALUE;
        }

        if ($extraPadding && $state !== self::STATE_START && $state !== self::STATE_AFTER_EOL) {
            fwrite($this->resource, ' ');
        }
        fwrite($this->resource, $object ? '}' : ']');
    }
}
