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
    use Arokettu\Json5\Values\HexInteger;

    $value = [
        'hex1' => new HexInteger(0xdeadbeef),
        'hex2' => new HexInteger(0xbeef),
        'hex3' => new HexInteger(0xbeef, 8), // optional padding
    ];

    echo Json5Encoder::encode($value);
    echo json_encode($value, JSON_PRETTY_PRINT);


.. tabs::

    .. code-tab:: json5

        {
            hex1: 0xDEADBEEF,
            hex2: 0xBEEF,
            hex3: 0x0000BEEF,
        }

    .. code-tab:: json

        {
            "hex1": 3735928559,
            "hex2": 48879,
            "hex3": 48879
        }

.. error:: Known issue: ``PHP_INT_MIN`` value is not handled correctly

Container Decorators
====================

For lists and objects.

``ListValue`` and ``ObjectValue``
---------------------------------

.. versionadded:: 1.1

``\Arokettu\Json5\Values\ListValue``

``\Arokettu\Json5\Values\ObjectValue``

These two decorators wrap any ``iterable`` or ``stdClass`` to be forced to render as either a list or a dictionary::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\Values\ListValue;
    use Arokettu\Json5\Values\ObjectValue;

    $generator = (fn () => yield from range(0, 3));
    $value = [
        'list' => new ListValue([1 => 2, 3 => 4]), // no need for consecutive keys
        'object' => new ObjectValue([1, 2, 3, 4]), // list becomes object
        'iterable' => new ListValue($generator()), // try a generator
    ];

    echo Json5Encoder::encode($value);
    $value['iterable'] = $generator(); // can't traverse a generator twice
    echo json_encode($value, JSON_PRETTY_PRINT);

.. tabs::

    .. code-tab:: json5

        {
            list: [
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

    .. code-tab:: json

        {
            "list": [
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

``InlineList`` and ``InlineObject``
-----------------------------------

``\Arokettu\Json5\Values\InlineList``

``\Arokettu\Json5\Values\InlineObject``

These wrappers act similar to ``ListValue`` and ``ObjectValue`` but intended for small lists and objects
that can be written in a single line::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\Values\InlineList;
    use Arokettu\Json5\Values\InlineObject;

    $value = [
        'tinyList' => new InlineList([1, 2, 3, 4]),
        'tinyObject' => new InlineObject(['key' =>  'value']),
    ];

    echo Json5Encoder::encode($value);
    echo json_encode($value, JSON_PRETTY_PRINT);

.. tabs::

    .. code-tab:: json5

        // Compact and nice
        {
            tinyList: [1, 2, 3, 4],
            tinyObject: { key: "value" },
        }

    .. code-tab:: json

        // Quite wasteful
        {
            "tinyList": [
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
    use Arokettu\Json5\Values\InlineList;
    use Arokettu\Json5\Values\InlineObject;

    $value = [
        'authors' => new InlineList([
            ['name' => 'Andy Gutmans', 'email' => 'example@example.com', 'role' => 'co-founder'],
            ['name' => 'Zeev Suraski', 'email' => 'example@example.com', 'role' => 'co-founder'],
        ]),
        'repositories' => [
            new InlineObject(['type' => 'vcs', 'url' => 'http://localhost/php.git']),
            new InlineObject(['type' => 'vcs', 'url' => 'http://localhost/zend.git']),
        ],
    ];

    echo Json5Encoder::encode($value);
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

    .. code-tab:: json

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

``CompactList`` and ``CompactObject``
-------------------------------------

``\Arokettu\Json5\Values\CompactList``

``\Arokettu\Json5\Values\CompactObject``

A middle ground between normal and inline structures best used with a manual newline using :ref:`json5_objects_eol`::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\Values\CompactList;
    use Arokettu\Json5\Values\CompactObject;

    $value = [
        'tinyList' => new CompactList([1, 2, 3, 4]),
        'tinyObject' => new CompactObject(['key1' =>  'value1', 'key2' =>  'value2']),
    ];

    echo Json5Encoder::encode($value);
    echo json_encode($value, JSON_PRETTY_PRINT);

.. tabs::

    .. code-tab:: json5

        {
            tinyList: [
                1, 2, 3, 4,
            ],
            tinyObject: {
                key1: "value1", key2: "value2",
            },
        }

    .. code-tab:: json

        {
            "tinyList": [
                1,
                2,
                3,
                4
            ],
            "tinyObject": {
                "key1": "value1",
                "key2": "value2"
            }
        }

Common Decorators
=================

``CommentDecorator``
--------------------

Renders a value with comments. The ``commentBefore`` may be multiline, the ``commentAfter`` must be a single line::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\Values\CommentDecorator;

    $value = new CommentDecorator([ // root level supported too
        'g' => new CommentDecorator(6.6743e-11, commentBefore: <<<TEXT
            This is the Gravitational constant
            Changing it may collapse the Universe
            TEXT, commentAfter: 'Universe is safe'),
    ], commentBefore: 'This time this comment is really rendered by the lib');

    echo Json5Encoder::encode($value);
    echo json_encode($value, JSON_PRETTY_PRINT);


.. tabs::

    .. code-tab:: json5

        // This time this comment is really rendered by the lib
        {
            // This is the Gravitational constant
            // Changing it may collapse the Universe
            g: 6.6743e-11, // Universe is safe
        }

    .. code-tab:: json

        {
            "g": 6.6743e-11
        }

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

``Comment``
-----------

.. _json5_objects_eol:

``EndOfLine``
-------------

``\Arokettu\Json5\Values\EndOfLine``

Inserts a newline character::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\Values\CompactList;
    use Arokettu\Json5\Values\EndOfLine;
    use Arokettu\Json5\Values\InlineList;

    $value = [
        'regular' => [1, 2, new EndOfLine(), 3, 4],
        'inline'  => new InlineList([1, 2, new EndOfLine(), 3, 4]),
        'compact' => new CompactList([1, 2, new EndOfLine(), 3, 4]),
    ];

    echo Json5Encoder::encode($value);
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

    .. code-tab:: json

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
