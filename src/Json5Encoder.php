<?php

declare(strict_types=1);

namespace Arokettu\Json5;

use TypeError;

use function Arokettu\IsResource\try_get_resource_type;

final class Json5Encoder
{
    public static function encode(mixed $value, Options $options = new Options()): string
    {
        $stream = fopen('php://temp', 'r+');
        (new Engine\Json5Engine($value, $options, $stream))->encode();
        rewind($stream);
        $json5 = stream_get_contents($stream);
        fclose($stream);
        return $json5;
    }

    /**
     * @param resource $stream
     * @return resource
     */
    public static function encodeToStream($stream, mixed $value, Options $options = new Options())
    {
        if (try_get_resource_type($stream) !== 'stream') {
            throw new TypeError('$stream must be a writable stream');
        }
        (new Engine\Json5Engine($value, $options, $stream))->encode();
        return $stream;
    }
}
