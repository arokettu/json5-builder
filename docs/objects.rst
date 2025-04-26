.. _json5_objects:

Helper Objects
##############

.. highlight:: php

The helper objects allow you control how specific values are rendered.
They are also designed to be JSON-transparent so you can get an equivalent JSON file by using ``json_encode()``.
This compatibility may be broken by some planned objects.

``HexInteger``
==============

Renders an integer in a hexadecimal form::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\Values\HexInteger;

    $value = [
        'hex' => new HexInteger(0xdeadbeef),
    ];

    echo Json5Encoder::encode($value);
    echo json_encode($value, JSON_PRETTY_PRINT);

.. code-block:: json5

    // JSON5
    {
        hex: 0xDEADBEEF,
    }
    // JSON
    {
        "hex": 3735928559
    }

``CommentDecorator``
====================

Renders a value with comments. The ``commentBefore`` may be multiline, the ``commentAfter`` must be a single line::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\Values\CommentDecorator;

    require __DIR__ . '/../vendor/autoload.php';

    $value = new CommentDecorator([ // root level supported too
        'g' => new CommentDecorator(6.6743e-11, commentBefore: <<<TEXT
            This is the Gravitational constant
            Changing it may collapse the Universe
            TEXT, commentAfter: 'Universe is safe'),
    ], commentBefore: 'JSON5. This time this comment is really rendered by the lib');

    echo Json5Encoder::encode($value);
    echo json_encode($value, JSON_PRETTY_PRINT);


.. code-block:: json5

    // JSON5. This time this comment is really rendered by the lib
    {
        // This is the Gravitational constant
        // Changing it may collapse the Universe
        g: 6.6743e-11, // Universe is safe
    }
    // JSON
    {
        "g": 6.6743e-11
    }

``JsonSerializable``
====================

.. note:: https://www.php.net/manual/en/class.jsonserializable.php

``ext-json``'s ``JsonSerializable`` works with this builder just like it works with ``json_encode``.

``Json5Serializable``
=====================

``\Arokettu\Json5\Values\Json5Serializable``.

Like ``JsonSerializable`` but it's specific to this library.

Planned
=======

* Compact objects and lists
* Force newline
* Standalone comments
