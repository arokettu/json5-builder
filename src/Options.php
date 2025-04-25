<?php

declare(strict_types=1);

namespace Arokettu\Json5;

final class Options
{
    public function __construct(
        // strings
        public Options\Quotes $keyQuotes = Options\Quotes::Single,
        public Options\Quotes $valueQuotes = Options\Quotes::Double,
        public bool $avoidKeyQuotes = true,
        public bool $tryOtherQuotes = true,
        public bool $multilineStrings = false,
        // floats
        public bool $preserveZeroFraction = false,
        // formatting
        public string $indent = '    ',
    ) {
    }
}
