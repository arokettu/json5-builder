Simple Use
##########

.. highlight:: php

Encode
======

``\Arokettu\Json5\Json5Encoder::encode(mixed $value, Options $options = default)``

The method aims to be compatible with a simple |json_encode|_ dump.
Important shared features:

.. |json_encode| replace:: ``json_encode($value)``
.. _json_encode: https://www.php.net/manual/en/function.json-encode.php

* |JsonSerializable|_ support.
* Float precision depends on |serialize_precision|_ ini config.

.. |JsonSerializable| replace:: ``JsonSerializable``
.. _JsonSerializable: https://www.php.net/manual/en/class.jsonserializable.php

.. |serialize_precision| replace:: ``serialize_precision``
.. _serialize_precision: https://www.php.net/manual/en/ini.core.php#ini.serialize-precision

The main differences:

* ``NAN``, ``INF``, ``-INF`` float values are supported.
* ``\Arokettu\Json5\Values\Json5Serializable`` interface that takes precedence
  in case you need different behavior for JSON and JSON5.
* No generic object serialization is supported.
  An object must be an instance of ``stdClass`` or ``ArrayObject``,
  or implement ``JsonSerializable`` or ``Json5Serializable``.
  You can replicate the ``json_encode()`` behavior by wrapping an object with ``get_object_vars()``.
* The document is always pretty-printed.
* Trailing commas are always used.

Use :ref:`json5_options` and :ref:`json5_objects` to customize your output.

How to prettify JSON/JSON5
==========================

For JSON5 you also need a parser, I will use `colinodell/json5 <colinodell_json5_>`_.
For JSON use a built-in ``json_decode``.
Just parse and dump it::

    <?php

    // adjust to your layout
    require __DIR__ . '/vendor/autoload.php';

    echo \Arokettu\Json5\Json5Encoder::encode(
        json5_decode(
            file_get_contents("php://stdin")
        )
    );

.. _colinodell_json5: https://packagist.org/packages/colinodell/json5

Run the script:

.. code-block:: bash

    php prettify.php < composer.json > composer.json5

JSON Encoder
============

.. versionadded:: 2.0

``\Arokettu\Json5\JsonEncoder::encode(mixed $value, Options $options = default)``

A JSON encoder that partially supports the same options and helper objects.
Like in JSON5 encoder, the document is always pretty-printed.

When to use over a native ``json_encode``:

* You used non-JSON-transparent helpers to format your config file but want to be able to generate a simple JSON too
  without modifying your code.
* You want to pretty format your config with helpers like ``InlineArray`` and ``EndOfLine``.
* You need some other feature not supported by the native encoder, like customizable indent.

Important features:

* Does not resolve ``Json5Serializable``.
* Transparently ignores tools that would break strict JSON (comments, custom quotes, trailing commas, etc)

JSONC Encoder
=============

.. versionadded:: 2.1

``\Arokettu\Json5\JsonCEncoder::encode(mixed $value, Options $options = default)``

A JSONC encoder that partially supports the same options and helper objects.
Like in JSON5 encoder, the document is always pretty-printed.
For maximum interoperability the encoder does not produce trailing commas despite some implementations allowing them.

Important features:

* Like JSON, does not resolve ``Json5Serializable``.
* Renders comments and comment decorators.
* Transparently ignores tools that would break JSONC (custom quotes, trailing commas, etc)
