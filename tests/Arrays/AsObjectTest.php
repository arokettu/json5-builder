<?php

declare(strict_types=1);

namespace Arokettu\Json5\Tests\Arrays;

use Arokettu\Json5\Json5Encoder;
use PHPUnit\Framework\TestCase;

class AsObjectTest extends TestCase
{
    public function testStrings(): void
    {
        // arrays with string keys naturally become objects
        $obj = [
            'key' => 'value',
            'other_key' => 'other value',
        ];

        self::assertEquals(<<<JSON5
            {
                key: "value",
                other_key: "other value",
            }

            JSON5, Json5Encoder::encode($obj));
    }

    public function testInt(): void
    {
        // non-sequential arrays become objects too
        $obj = [1 => 'a', 2 => 'b'];

        self::assertEquals(<<<JSON5
            {
                '1': "a",
                '2': "b",
            }

            JSON5, Json5Encoder::encode($obj));
    }
}
