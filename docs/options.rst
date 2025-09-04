.. _json5_options:

Options
#######

.. highlight:: php

``\Arokettu\Json5\Options``

An object to control generic options.
All options are exposed as both constructor arguments and fields::

    <?php

    use Arokettu\Json5\Options;
    use Arokettu\Json5\Options\BareKeys;

    $options = new Options(bareKeys: BareKeys::Unicode);

    // same as

    $options = new Options();
    $options->bareKeys = BareKeys::Unicode;

    // apply options

    echo Json5Encoder::encode($value, $options);

String options
==============

``bareKeys``
------------

| Default: ``BareKeys::Ascii``.
| Supported by encoders: JSON5 only.

Controls rendering of unquoted keys, value is an enum ``\Arokettu\Json5\Options\BareKeys``

Possible values:

``BareKeys::None``
    All keys are quoted.
``BareKeys::Ascii``
    Allows only ``$``, ``_``, ASCII letters and digits in unquoted keys, maximizes readability and compatibility.
``BareKeys::Unicode``
    Leaves any ES 5.1 IdentifierName_ keys unquoted.
    This allows unquoted Unicode keys and may be incompatible with some parsers.
    It also depends on what version of Unicode is implemented in your PCRE engine.

.. _IdentifierName: https://262.ecma-international.org/5.1/#sec-7.6

::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\Options;
    use Arokettu\Json5\Options\BareKeys;

    $value = [
        'ascii' => 'unquoted in Ascii and Unicode',
        'ユニコード' => 'unquoted in Unicode',
        'contains space' => 'always quoted',
    ];

    echo Json5Encoder::encode($value, new Options(bareKeys: BareKeys::None));
    echo Json5Encoder::encode($value, new Options(bareKeys: BareKeys::Ascii));
    echo Json5Encoder::encode($value, new Options(bareKeys: BareKeys::Unicode));

.. code-block:: json5

    // None
    {
        'ascii': "unquoted in Ascii and Unicode",
        'ユニコード': "unquoted in Unicode",
        'contains space': "always quoted",
    }
    // Ascii
    {
        ascii: "unquoted in Ascii and Unicode",
        'ユニコード': "unquoted in Unicode",
        'contains space': "always quoted",
    }
    // Unicode
    {
        ascii: "unquoted in Ascii and Unicode",
        ユニコード: "unquoted in Unicode",
        'contains space': "always quoted",
    }

``keyQuotes``, ``valueQuotes``
------------------------------

.. versionchanged:: 2.2 ``keyQuotes`` default changed from ``Single`` to ``Double``

| Default: ``keyQuotes = Quotes::Double``, ``valueQuotes = Quotes::Double``.
| Supported by encoders: JSON5 only.

Controls rendering of strings and quoted keys. The value is an enum ``\Arokettu\Json5\Options\Quotes``::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\Options;
    use Arokettu\Json5\Options\Quotes;

    $value = [
        'some key' => 'some value',
    ];

    echo Json5Encoder::encode($value, new Options(
        keyQuotes: Quotes::Double,
        valueQuotes: Quotes::Single,
    ));

.. code-block:: json5

    {
        "some key": 'some value',
    }

``tryOtherQuotes``
------------------

| Default: ``true``.
| Supported by encoders: JSON5 only.

Overrides ``keyQuotes`` / ``valueQuotes`` for readability for some strings.
In case a string contains target quotes but does not contain the other type, the quote type switches::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\Options;
    use Arokettu\Json5\Options\Quotes;

    $value = [
        'default key quotes' => 'default value quotes',
        "that's a key" => 'a so called "value"',
        "both here: '\"" => "both here: '\"",
    ];

    echo Json5Encoder::encode($value, new Options(keyQuotes: Quotes::Single, tryOtherQuotes: false));
    echo Json5Encoder::encode($value, new Options(keyQuotes: Quotes::Single, tryOtherQuotes: true));

.. code-block:: json5

    // Disabled
    {
        'default key quotes': "default value quotes",
        'that\'s a key': "a so called \"value\"",
        'both here: \'"': "both here: '\"",
    }
    // Enabled
    {
        'default key quotes': "default value quotes",
        "that's a key": 'a so called "value"', // obviously more readable
        'both here: \'"': "both here: '\"", // we don't try to guess here
    }

``multilineStrings``
--------------------

| Default: ``false``.
| Supported by encoders: JSON5 only.

Renders multiline values on multiple lines.
Multiline support is poor in both JSON and JSON5 only.
(It's better in JSON6 but neither JSON6 is widely used nor I like the standard in general)
This rendering mode tries to make multiline values look somewhat better
by rendering them in heredoc style by postfixing lines with ``"\n\"``::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\Options;

    $value = [
        'limerick' => <<<TEXT
            The limerick packs laughs anatomical
            Into space that is quite economical.
            But the good ones I’ve seen
            So seldom are clean
            And the clean ones so seldom are comical.
            TEXT,
        'author' => 'unknown',
        'take some newlines with you' => "\n\n\n\n", // won't become a multiline
    ];

    echo Json5Encoder::encode($value, new Options(multilineStrings: true));

.. code-block:: json5

    {
        limerick: "\
    The limerick packs laughs anatomical\n\
    Into space that is quite economical.\n\
    But the good ones I’ve seen\n\
    So seldom are clean\n\
    And the clean ones so seldom are comical.",
        author: "unknown",
        "take some newlines with you": "\n\n\n\n",
    }

Float options
=============

``preserveZeroFraction``
------------------------

| Default: ``false``.
| Supported by encoders: JSON5, JSONC, JSON.

.. note:: https://www.php.net/manual/en/json.constants.php#constant.json-preserve-zero-fraction

Applies ``JSON_PRESERVE_ZERO_FRACTION`` to float values, ensuring that they are always encoded as a float value::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\Options;

    $value = [
        'int' => 123,
        'float' => (float)123,
        'surely_float' => 1.23,
    ];

    echo Json5Encoder::encode($value, new Options(preserveZeroFraction: true));

.. code-block:: json5

    {
        int: 123,
        float: 123.0, // would be 123 by default
        surely_float: 1.23,
    }

Formatting options
==================

``indent``
----------

| Default: ``'    '`` (4 spaces).
| Supported by encoders: JSON5, JSONC, JSON.

A pretty print indentation.
Must contain only JSON5/JSON ignorable whitespace, usually spaces and tabs::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\Options;

    $value = [
        'key' => 'value',
        'array' => ['item1', 'item2'],
    ];

    echo Json5Encoder::encode($value, new Options(indent: "\t"));


.. code-block:: json5

    {
            key: "value",
            array: [
                    "item1",
                    "item2",
            ],
    }

``inlineArrayPadding``, ``inlineObjectPadding``
-----------------------------------------------

.. versionadded:: 1.1
.. versionchanged:: 2.0 ``inlineListPadding`` renamed to ``inlineArrayPadding``.

| Default: ``inlineArrayPadding = false``, ``inlineObjectPadding = true``.
| Supported by encoders: JSON5, JSONC, JSON.

An option to pad inline container structures with spaces::

    <?php

    use Arokettu\Json5\Json5Encoder;
    use Arokettu\Json5\Options;
    use Arokettu\Json5\Values\InlineArray;
    use Arokettu\Json5\Values\InlineObject;

    $data = [
        new InlineArray([1,2,3]),
        new InlineObject(['a' => 'b', 'x' => 'y'])
    ];

    echo Json5Encoder::encode($data);
    echo Json5Encoder::encode($data, new Options(
        inlineArrayPadding: false,
        inlineObjectPadding: false,
    ));
    echo Json5Encoder::encode($data, new Options(
        inlineArrayPadding: true,
        inlineObjectPadding: true,
    ));

.. code-block:: json5

    // default
    [
        [1, 2, 3],
        { a: "b", x: "y" },
    ]
    // no padding
    [
        [1, 2, 3],
        {a: "b", x: "y"},
    ]
    // full padding
    [
        [ 1, 2, 3 ],
        { a: "b", x: "y" },
    ]
