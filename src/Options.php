<?php

declare(strict_types=1);

namespace Arokettu\Json5;

use Error;
use ValueError;

/**
 * @property string $indent
 */
final class Options
{
    public function __construct(
        // strings
        public Options\Quotes $keyQuotes = Options\Quotes::Single,
        public Options\Quotes $valueQuotes = Options\Quotes::Double,
        public Options\BareKeys $bareKeys = Options\BareKeys::Ascii,
        public bool $tryOtherQuotes = true,
        public bool $multilineStrings = false,
        // floats
        public bool $preserveZeroFraction = false,
        // formatting
        private string $indent = '    ', // todo: property hook in PHP 8.4+
    ) {
        if (!preg_match('/^[\x20\x09\x0a\x0d]*$/', $indent)) {
            throw new ValueError('Indent must contain only whitespace characters');
        }
    }

    public function __set(string $name, mixed $value): void
    {
        if ($name === 'indent') {
            if (!preg_match('/^[\x20\x09\x0a\x0d]*$/', $value)) {
                throw new ValueError('Indent must contain only whitespace characters');
            }

            $this->indent = $value;
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
