.. _json5_objects:

Helper Objects
##############

.. highlight:: php

The helper objects allow you control how specific values are rendered.
They are also designed to be JSON-transparent so you can get an equivalent JSON file by using ``json_encode()``.
This compatibility may be broken by some planned objects.

Scalar Decorators
=================

``HexInteger``
--------------

.. versionadded:: 1.1 ``$padding``

Renders an integer in a hexadecimal form::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\JsonEncoder;
    use Arokettu\Json5\Values\HexInteger;

    $value = [
        'hex1' => new HexInteger(0xdeadbeef),
        'hex2' => new HexInteger(0xbeef),
        'hex3' => new HexInteger(0xbeef, 8), // optional padding
    ];

    echo Json5Encoder::encode($value);
    echo JsonEncoder::encode($value);
    echo json_encode($value, JSON_PRETTY_PRINT);


.. tabs::

    .. code-tab:: json5

        {
            hex1: 0xDEADBEEF,
            hex2: 0xBEEF,
            hex3: 0x0000BEEF,
        }

    .. code-tab:: json JSON (JsonEncoder)

        {
            "hex1": 3735928559,
            "hex2": 48879,
            "hex3": 48879
        }

    .. code-tab:: json JSON (json_encode)

        {
            "hex1": 3735928559,
            "hex2": 48879,
            "hex3": 48879
        }

Container Decorators
====================

For arrays and objects.

``ArrayValue`` and ``ObjectValue``
----------------------------------

.. versionadded:: 1.1

``\Arokettu\Json5\Values\ArrayValue``

``\Arokettu\Json5\Values\ObjectValue``

These two decorators wrap any ``iterable`` or ``stdClass`` to be forced to render as either a array or an object::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\JsonEncoder;
    use Arokettu\Json5\Values\ArrayValue;
    use Arokettu\Json5\Values\ObjectValue;

    $generator = (fn () => yield from range(0, 3));
    $value = [
        'array' => new ArrayValue([1 => 2, 3 => 4]), // no need for consecutive keys
        'object' => new ObjectValue([1, 2, 3, 4]), // list becomes object
        'iterable' => new ArrayValue($generator()), // try a generator
    ];

    echo Json5Encoder::encode($value);
    $value['iterable'] = new ArrayValue($generator()); // can't traverse a generator twice
    echo JsonEncoder::encode($value);
    $value['iterable'] = new ArrayValue($generator()); // can't traverse a generator twice
    echo json_encode($value, JSON_PRETTY_PRINT);

.. tabs::

    .. code-tab:: json5

        {
            array: [
                2,
                4,
            ],
            object: {
                '0': 1,
                '1': 2,
                '2': 3,
                '3': 4,
            },
            iterable: [
                0,
                1,
                2,
                3,
            ],
        }

    .. code-tab:: json JSON (JsonEncoder)

        {
            "array": [
                2,
                4
            ],
            "object": {
                "0": 1,
                "1": 2,
                "2": 3,
                "3": 4
            },
            "iterable": [
                0,
                1,
                2,
                3
            ]
        }

    .. code-tab:: json JSON (json_encode)

        {
            "array": [
                2,
                4
            ],
            "object": {
                "0": 1,
                "1": 2,
                "2": 3,
                "3": 4
            },
            "iterable": [
                0,
                1,
                2,
                3
            ]
        }

.. note::
    If an iterable wrapped by an instance of ``ObjectValue`` (and similar object wrappers) has duplicate keys,
    your JSON5 file will have duplicate keys too.

``InlineArray`` and ``InlineObject``
------------------------------------

``\Arokettu\Json5\Values\InlineArray``

``\Arokettu\Json5\Values\InlineObject``

These wrappers act similar to ``ArrayValue`` and ``ObjectValue`` but intended for small arrays and objects
that can be written in a single line::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\JsonEncoder;
    use Arokettu\Json5\Values\InlineArray;
    use Arokettu\Json5\Values\InlineObject;

    $value = [
        'tinyArray' => new InlineArray([1, 2, 3, 4]),
        'tinyObject' => new InlineObject(['key' =>  'value']),
    ];

    echo Json5Encoder::encode($value);
    echo JsonEncoder::encode($value);
    echo json_encode($value, JSON_PRETTY_PRINT);

.. tabs::

    .. code-tab:: json5

        // Compact and nice
        {
            tinyArray: [1, 2, 3, 4],
            tinyObject: { key: "value" },
        }

    .. code-tab:: json JSON (JsonEncoder)

        // Compact and nice too
        {
            "tinyArray": [1, 2, 3, 4],
            "tinyObject": { "key": "value" }
        }

    .. code-tab:: json JSON (json_encode)

        // Quite wasteful
        {
            "tinyArray": [
                1,
                2,
                3,
                4
            ],
            "tinyObject": {
                "key": "value"
            }
        }

Nesting container structures is also fine::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\JsonEncoder;
    use Arokettu\Json5\Values\InlineArray;
    use Arokettu\Json5\Values\InlineObject;

    $value = [
        'authors' => new InlineArray([
            ['name' => 'Andy Gutmans', 'email' => 'example@example.com', 'role' => 'co-founder'],
            ['name' => 'Zeev Suraski', 'email' => 'example@example.com', 'role' => 'co-founder'],
        ]),
        'repositories' => [
            new InlineObject(['type' => 'vcs', 'url' => 'http://localhost/php.git']),
            new InlineObject(['type' => 'vcs', 'url' => 'http://localhost/zend.git']),
        ],
    ];

    echo Json5Encoder::encode($value);
    echo JsonEncoder::encode($value);
    echo json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

.. tabs::

    .. code-tab:: json5

        {
            authors: [{
                name: "Andy Gutmans",
                email: "example@example.com",
                role: "co-founder",
            }, {
                name: "Zeev Suraski",
                email: "example@example.com",
                role: "co-founder",
            }],
            repositories: [
                { type: "vcs", url: "http://localhost/php.git" },
                { type: "vcs", url: "http://localhost/zend.git" },
            ],
        }

    .. code-tab:: json JSON (JsonEncoder)

        {
            "authors": [{
                "name": "Andy Gutmans",
                "email": "example@example.com",
                "role": "co-founder"
            }, {
                "name": "Zeev Suraski",
                "email": "example@example.com",
                "role": "co-founder"
            }],
            "repositories": [
                { "type": "vcs", "url": "http://localhost/php.git" },
                { "type": "vcs", "url": "http://localhost/zend.git" }
            ]
        }

    .. code-tab:: json JSON (json_encode)

        {
            "authors": [
                {
                    "name": "Andy Gutmans",
                    "email": "example@example.com",
                    "role": "co-founder"
                },
                {
                    "name": "Zeev Suraski",
                    "email": "example@example.com",
                    "role": "co-founder"
                }
            ],
            "repositories": [
                {
                    "type": "vcs",
                    "url": "http://localhost/php.git"
                },
                {
                    "type": "vcs",
                    "url": "http://localhost/zend.git"
                }
            ]
        }

``CompactArray`` and ``CompactObject``
--------------------------------------

``\Arokettu\Json5\Values\CompactArray``

``\Arokettu\Json5\Values\CompactObject``

A middle ground between normal and inline structures best used with a manual newline using :ref:`json5_objects_eol`,
also notice various comment types behavior::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\JsonEncoder;
    use Arokettu\Json5\Values\Comment;
    use Arokettu\Json5\Values\CommentDecorator;
    use Arokettu\Json5\Values\CompactArray;
    use Arokettu\Json5\Values\CompactObject;
    use Arokettu\Json5\Values\EndOfLine;

    $value = [
        'tinyArray' => new CompactArray([1, 2, new EndOfLine(), 3, 4]),
        'tinyObject' => new CompactObject(['key1' =>  'value1', 'key2' =>  'value2']),
        'comments' => new CompactArray([
            new Comment('Standalone comment is a line comment'),
            new CommentDecorator('become', 'Decorator comments', 'inline comments'),
        ]),
    ];

    echo Json5Encoder::encode($value);
    echo JsonEncoder::encode($value);
    echo json_encode($value, JSON_PRETTY_PRINT);

.. tabs::

    .. code-tab:: json5

        {
            tinyArray: [
                1, 2,
                3, 4,
            ],
            tinyObject: {
                key1: "value1", key2: "value2",
            },
            comments: [
                // Standalone comment is a line comment
                /* Decorator comments */ "become" /* inline comments */,
            ],
        }

    .. code-tab:: json JSON (JsonEncoder)

        {
            "tinyArray": [
                1, 2,
                3, 4
            ],
            "tinyObject": {
                "key1": "value1", "key2": "value2"
            },
            "comments": [
                "become"
            ]
        }

    .. code-tab:: json JSON (json_encode)

        {
            "tinyArray": [
                1,
                2,
                {},
                3,
                4
            ],
            "tinyObject": {
                "key1": "value1",
                "key2": "value2"
            },
            "comments": [
                {
                    "comment": "Standalone comment is a line comment"
                },
                "become"
            ]
        }

Common Decorators
=================

``CommentDecorator``
--------------------

Renders a value with comments. The ``commentBefore`` may be multiline, the ``commentAfter`` must be a single line::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\JsonEncoder;
    use Arokettu\Json5\Values\CommentDecorator;

    $value = new CommentDecorator([ // root level supported too
        'g' => new CommentDecorator(6.6743e-11, commentBefore: <<<TEXT
            This is the Gravitational constant
            Changing it may collapse the Universe
            TEXT, commentAfter: 'Universe is safe'),
    ], commentBefore: 'This time this comment is really rendered by the lib');

    echo Json5Encoder::encode($value);
    echo JsonEncoder::encode($value);
    echo json_encode($value, JSON_PRETTY_PRINT);

.. tabs::

    .. code-tab:: json5

        // This time this comment is really rendered by the lib
        {
            // This is the Gravitational constant
            // Changing it may collapse the Universe
            g: 6.6743e-11, // Universe is safe
        }

    .. code-tab:: json JSON (JsonEncoder)

        {
            "g": 6.6743e-11
        }

    .. code-tab:: json JSON (json_encode)

        {
            "g": 6.6743e-11
        }

Comments will be rendered as inline comments in compact and inline modes::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\JsonEncoder;
    use Arokettu\Json5\Values\CommentDecorator;
    use Arokettu\Json5\Values\InlineArray;

    $value = new InlineArray([
        new CommentDecorator('value', 'inline before', 'inline after'),
    ]);

    echo Json5Encoder::encode($value);
    echo JsonEncoder::encode($value);
    echo json_encode($value, JSON_PRETTY_PRINT);

.. tabs::

    .. code-tab:: json5

        [/* inline before */ "value" /* inline after */]

    .. code-tab:: json JSON (JsonEncoder)

        ["value"]

    .. code-tab:: json JSON (json_encode)

        [
            "value"
        ]

Interfaces
==========

``JsonSerializable``
--------------------

.. note:: https://www.php.net/manual/en/class.jsonserializable.php

``ext-json``'s ``JsonSerializable`` works with this builder just like it works with ``json_encode``.

``Json5Serializable``
---------------------

``\Arokettu\Json5\Values\Json5Serializable``.

Like ``JsonSerializable`` but it's specific to this library.

Formatting Objects
==================

.. note:: Formatting Objects are not transparent for the ``json_encode`` and will be encoded as regular objects, see examples.

.. note:: Formatting Objects cannot be encoded as root objects and cannot be returned in ``json5Serialize()`` and ``jsonSerialize()`` methods.

``Comment``
-----------

``\Arokettu\Json5\Values\Comment``

A standalone comment. Rendered as a line comment in regular and compact modes and as an inline comment in inline mode::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\JsonEncoder;
    use Arokettu\Json5\Values\Comment;
    use Arokettu\Json5\Values\CompactArray;
    use Arokettu\Json5\Values\InlineArray;

    require __DIR__ . '/../vendor/autoload.php';

    $value = [
        'normal' => [new Comment('Normal mode'), 'value1', 'value2', 'value3'],
        'compact' => new CompactArray([
            new Comment('Unlike decorator, standalone comment is rendered on its own line here'),
            'value1',
            'value2',
            new Comment('JsonEncoder will leave EOL here'),
            'value3',
        ]),
        'inline' => new InlineArray([new Comment('Inline mode'), 'value1', 'value2', 'value3']),
    ];

    echo Json5Encoder::encode($value);
    echo JsonEncoder::encode($value);
    echo json_encode($value, JSON_PRETTY_PRINT);

.. tabs::

    .. code-tab:: json5

        {
            normal: [
                // Normal mode
                "value1",
                "value2",
                "value3",
            ],
            compact: [
                // Unlike decorator, standalone comment is rendered on its own line here
                "value1", "value2",
                // JsonEncoder will leave EOL here
                "value3",
            ],
            inline: [/* Inline mode */ "value1", "value2", "value3"],
        }

    .. code-tab:: json JSON (JsonEncoder)

        {
            "normal": [
                "value1",
                "value2",
                "value3"
            ],
            "compact": [
                "value1", "value2",
                "value3"
            ],
            "inline": ["value1", "value2", "value3"]
        }

    .. code-tab:: json JSON (json_encode)

        {
            "normal": [
                {
                    "comment": "Normal mode"
                },
                "value1",
                "value2",
                "value3"
            ],
            "compact": [
                {
                    "comment": "Unlike decorator, standalone comment is rendered on its own line here"
                },
                "value1",
                "value2",
                {
                    "comment": "JsonEncoder will leave EOL here"
                },
                "value3"
            ],
            "inline": [
                {
                    "comment": "Inline mode"
                },
                "value1",
                "value2",
                "value3"
            ]
        }

.. _json5_objects_eol:

``EndOfLine``
-------------

``\Arokettu\Json5\Values\EndOfLine``

Inserts a newline character::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\JsonEncoder;
    use Arokettu\Json5\Values\CompactArray;
    use Arokettu\Json5\Values\EndOfLine;
    use Arokettu\Json5\Values\InlineArray;

    $value = [
        'regular' => [1, 2, new EndOfLine(), 3, 4],
        'inline'  => new InlineArray([1, 2, new EndOfLine(), 3, 4]),
        'compact' => new CompactArray([1, 2, new EndOfLine(), 3, 4]),
    ];

    echo Json5Encoder::encode($value);
    echo JsonEncoder::encode($value);
    echo json_encode($value, JSON_PRETTY_PRINT);

.. tabs::

    .. code-tab:: json5

        {
            regular: [
                1,
                2,

                3,
                4,
            ],
            inline: [1, 2,
                3, 4],
            compact: [
                1, 2,
                3, 4,
            ],
        }

    .. code-tab:: json JSON (JsonEncoder)

        {
            "regular": [
                1,
                2,

                3,
                4
            ],
            "inline": [1, 2,
                3, 4],
            "compact": [
                1, 2,
                3, 4
            ]
        }

    .. code-tab:: json JSON (json_encode)

        {
            "regular": [
                1,
                2,
                {}, // not transparent
                3,
                4
            ],
            "inline": [
                1,
                2,
                {}, // not transparent
                3,
                4
            ],
            "compact": [
                1,
                2,
                {}, // not transparent
                3,
                4
            ]
        }
