<?php

declare(strict_types=1);

namespace Arokettu\Json5;

final class Options
{
    public function __construct(
        public bool $avoidQuotes = true,
        public Options\Quotes $keyQuotes = Options\Quotes::Single,
        public Options\Quotes $valueQuotes = Options\Quotes::Double,
        public bool $multiline = true,
        public bool $trailingComma = true,
        public bool $tryOtherQuotes = true,
        public string $indent = '    ',
    ) {
    }
}
