<?php

declare(strict_types=1);

namespace Arokettu\Json5\Engine;

use Arokettu\Json5\Options;
use Arokettu\Json5\Values\CommentDecorator;
use Arokettu\Json5\Values\Json5Serializable;
use Arokettu\Json5\Values\RawJson5Serializable;
use ArrayObject;
use JsonSerializable;
use stdClass;
use TypeError;

/**
 * @internal
 */
final class Encoder
{
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
        if ($value === null) {
            fwrite($this->resource, 'null');
            return;
        }

        if (\is_bool($value)) {
            fwrite($this->resource, $value ? 'true' : 'false');
            return;
        }

        if (\is_int($value)) {
            fwrite($this->resource, json_encode($value));
            return;
        }

        if (\is_float($value)) {
            fwrite($this->resource, match (true) {
                is_nan($value) => 'NaN',
                is_infinite($value) => $value > 0 ? 'Infinity' : '-Infinity',
                default => json_encode($value, $this->options->preserveZeroFraction ? JSON_PRESERVE_ZERO_FRACTION : 0),
            });
            return;
        }

        if (\is_string($value)) {
            $this->encodeString($value, $this->options->valueQuotes, $this->options->multilineStrings);
            return;
        }

        if ($value instanceof RawJson5Serializable) {
            fwrite($this->resource, $value->json5SerializeRaw());
            return;
        }

        if ($value instanceof Json5Serializable) {
            $this->encodeValue($value->json5Serialize(), $indent);
            return;
        }

        if ($value instanceof JsonSerializable) {
            $this->encodeValue($value->jsonSerialize(), $indent);
            return;
        }

        if (\is_array($value)) {
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

        throw new TypeError('Unsupported type: ' . get_debug_type($value));
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

        $encoded = json_encode($string, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

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

    private function encodeList(iterable $list, string $indent): void
    {
        $indent2 = $indent . $this->options->indent;

        fwrite($this->resource, "[\n");

        foreach ($list as $value) {
            if ($value instanceof CommentDecorator) {
                $this->renderComment($value->commentBefore, $indent2);
            }
            fwrite($this->resource, $indent2);
            $this->encodeValue($value, $indent2);
            fwrite($this->resource, ",");
            if ($value instanceof CommentDecorator) {
                $this->renderCommentLine($value->commentAfter, ' ');
            }
            fwrite($this->resource, "\n");
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
            if ($value instanceof CommentDecorator) {
                $this->renderComment($value->commentBefore, $indent2);
            }
            fwrite($this->resource, $indent2);
            $this->encodeKey((string)$key);
            fwrite($this->resource, ": ");
            $this->encodeValue($value, $indent2);
            fwrite($this->resource, ",");
            if ($value instanceof CommentDecorator) {
                $this->renderCommentLine($value->commentAfter, ' ');
            }
            fwrite($this->resource, "\n");
        }

        fwrite($this->resource, $indent);
        fwrite($this->resource, "}");
    }

    private function renderComment(string|null $comment, string $indent): void
    {
        if ($comment === null) {
            return;
        }

        $lines = explode("\n", $comment);

        foreach ($lines as $line) {
            $this->renderCommentLine($line, $indent);
            fwrite($this->resource, "\n");
        }
    }

    private function renderCommentLine(string|null $commentLine, string $indent): void
    {
        if ($commentLine === null) {
            return;
        }

        fwrite($this->resource, $indent);
        fwrite($this->resource, '//');
        if ($commentLine !== '') {
            fwrite($this->resource, ' ');
            fwrite($this->resource, $commentLine);
        }
    }
}
