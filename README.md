# JSON5 Builder

A tool to generate human-friendly JSON5 files.
The tool aims to generate new files, not modifying existing ones.
However, it can prettify a raw JSON a bit. 

For parsing please use other tools like [colinodell/json5](https://packagist.org/packages/colinodell/json5).

## Example

```php
<?php

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\Values\CommentDecorator;
use Arokettu\Json5\Values\HexInteger;

require __DIR__ . '/../vendor/autoload.php';

$config = new CommentDecorator(
    [
        'bareKeys' => 'Look, no quotes!',
        'value' => new CommentDecorator(new HexInteger(0xFFF), commentAfter: 'This is a very important value'),
        'notAvailableInJSON' => [NAN, INF],
        'end' => 'auto trailing comma ->'
    ],
    commentBefore: <<<TEXT
        This is my cool JSON5 config!

        TEXT
);

echo Json5Encoder::encode($config);
```

will result in

```json5
// This is my cool JSON5 config!
//
{
    bareKeys: "Look, no quotes!",
    value: 0xFFF, // This is a very important value
    notAvailableInJSON: [
        NaN,
        Infinity,
    ],
    end: "auto trailing comma ->",
}
```
