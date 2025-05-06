<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Json5;

use ValueError;

/**
 * @property string $indent
 */
final class Options
{
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
        public string $indent = '    ' {
            set {
                if (!preg_match('/^[\x20\x09\x0a\x0d]*$/', $value)) {
                    throw new ValueError('Indent must contain only whitespace characters');
                }
                $this->indent = $value;
            }
        },
        public bool $inlineArrayPadding = false,
        public bool $inlineObjectPadding = true,
    ) {
    }
}
