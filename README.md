# JSON5 Builder

[![Packagist]][Packagist Link]
[![PHP]][Packagist Link]
[![License]][License Link]
[![Gitlab CI]][Gitlab CI Link]
[![Codecov]][Codecov Link]

[Packagist]: https://img.shields.io/packagist/v/arokettu/json5-builder.svg?style=flat-square
[PHP]: https://img.shields.io/packagist/php-v/arokettu/json5-builder.svg?style=flat-square
[License]: https://img.shields.io/packagist/l/arokettu/json5-builder.svg?style=flat-square
[Gitlab CI]: https://img.shields.io/gitlab/pipeline/sandfox/json5-builder/master.svg?style=flat-square
[Codecov]: https://img.shields.io/codecov/c/gl/sandfox/json5-builder?style=flat-square

[Packagist Link]: https://packagist.org/packages/arokettu/json5-builder
[License Link]: LICENSE.md
[Gitlab CI Link]: https://gitlab.com/sandfox/json5-builder/-/pipelines
[Codecov Link]: https://codecov.io/gl/sandfox/json5-builder/

A tool to generate human-friendly JSON5 files.
The tool aims to generate new files, not modifying existing ones.
However, it can prettify a raw JSON a bit. 

For parsing please use other tools like [colinodell/json5](https://packagist.org/packages/colinodell/json5).

The tool also supports emitting JSON and JSONC with a subset of available features.

## Installation

```bash
composer require arokettu/json5-builder
```

## Usage

```php
<?php

use Arokettu\Json5\Json5Encoder;
use Arokettu\Json5\Values\CommentDecorator;
use Arokettu\Json5\Values\HexInteger;

require __DIR__ . '/../vendor/autoload.php';

$config = new CommentDecorator(
    [
        'bareKeys' => '<- Look, no quotes!',
        'value' => new CommentDecorator(
            new HexInteger(0xFFF),
            commentAfter: 'This is a very important value'
         ),
        'notAvailableInJSON' => [NAN, INF],
        'end' => 'auto trailing comma ->'
    ],
    commentBefore: 'This is my cool JSON5 config!',
);

echo Json5Encoder::encode($config);
```

will result in

```json5
// This is my cool JSON5 config!
{
    bareKeys: "<- Look, no quotes!",
    value: 0xFFF, // This is a very important value
    notAvailableInJSON: [
        NaN,
        Infinity,
    ],
    end: "auto trailing comma ->",
}
```

## Documentation

Read the full documentation here: <https://sandfox.dev/php/json5-builder.html>

Also on Read the Docs: <https://json5-builder.readthedocs.io/>

## Support

Please file issues on our main repo at GitLab: <https://gitlab.com/sandfox/json5-builder/-/issues>

Feel free to ask any questions in our room on Gitter: <https://gitter.im/arokettu/community>

## License

The library is available as open source under the terms of the [MIT License][License Link].
