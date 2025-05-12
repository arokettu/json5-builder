<?php

declare(strict_types=1);

namespace Arokettu\Json5;

final class JsonEncoder
{
    public static function encode(mixed $value, Options $options = new Options()): string
    {
        $stream = fopen('php://temp', 'r+');
        (new Engine\JsonEngine($value, $options, $stream))->encode();
        rewind($stream);
        $json5 = stream_get_contents($stream);
        fclose($stream);
        return $json5;
    }
}
