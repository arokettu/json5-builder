<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Json5;

use Error;
use ValueError;

/**
 * @property string $indent
 */
final class Options
{
    private string $indent;

    public function __construct(
        // strings
        public Options\Quotes $keyQuotes = Options\Quotes::Double,
        public Options\Quotes $valueQuotes = Options\Quotes::Double,
        public Options\BareKeys $bareKeys = Options\BareKeys::Ascii,
        public bool $tryOtherQuotes = true,
        public bool $multilineStrings = false,
        // floats
        public bool $preserveZeroFraction = false,
        // formatting
        string $indent = '    ', // todo: property hook in PHP 8.4+
        public bool $inlineArrayPadding = false,
        public bool $inlineObjectPadding = true,
    ) {
        $this->setIndent($indent);
    }

    private function setIndent(string $indent): void
    {
        if (!preg_match('/^[\x20\x09\x0a\x0d]*$/', $indent)) {
            throw new ValueError('Indent must contain only whitespace characters');
        }
        $this->indent = $indent;
    }

    public function __set(string $name, mixed $value): void
    {
        if ($name === 'indent') {
            $this->setIndent($value);
            return;
        }

        throw new Error('No such property: ' . $name);
    }

    public function __get(string $name): mixed
    {
        if ($name === 'indent') {
            return $this->indent;
        }

        throw new Error('No such property: ' . $name);
    }
}
