<?php

declare(strict_types=1);

namespace Arokettu\Json5;

final class Encoder
{
    public static function encode(mixed $value, Options $options = new Options()): string
    {
        $stream = fopen('php://temp', 'r+');
        (new Engine\Encoder($value, $options, $stream))->encode();
        rewind($stream);
        return stream_get_contents($stream);
    }
}
