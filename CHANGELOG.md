# Changelog

## 2.x

### 2.1.0

*June 6, 2025*

* ``JsonCEncoder`` to encode JSON with Comments.
* Fixed a trailing comma appearing in JSON on two or more trailing comment or eol objects.

### 2.0.0

*May 27, 2025*

Forked from 1.1.1

* ``JsonEncoder`` to encode plain JSON with the supported subset of formatting options
* ``Json5Encoder::encodeToStream()`` to write data directly to the stream
* List classes renamed to Array to properly align them with JSON terminology
  * ``ListValue`` -> ``ArrayValue``
  * ``InlineList`` -> ``InlineArray``
  * ``CompactList`` -> ``CompactArray``
* Iterable wrappers
  (``ArrayValue``, ``ObjectValue``, ``InlineArray``, ``InlineObject``, ``CompactArray``, ``CompactObject``)
  no longer resolve ``JsonSerializable`` and ``Json5Serializable`` automatically.
  You need to use new named constructors for that:
  * ``fromSerializable()`` supports both ``JsonSerializable`` and ``Json5Serializable``.
  * ``fromJsonSerializable()`` supports only ``JsonSerializable``.

## 1.x

### 1.1.1

*May 13, 2025*

* Fixed indent being written to an empty structure

### 1.1.0

*May 12, 2025*

* Custom lists and objects:
  * `ListValue` converts any iterable into a list
  * `ObjectValue` converts any iterable into an object
  * `InlineList`, `InlineObject`, `CompactList`, `CompactObject` render compact representations
* Formatting objects:
  * `EndOfLine` forces a newline
  * `Comment` a comment object not tied to a value
* `HexInteger` now has `$padding` parameter to specify a minimal render length
* Options: `$inlineListPadding`, `$inlineObjectPadding`

### 1.0.1

*Apr 27, 2025*

* Encode an empty list as `[]` and an empty object as `{}`
* Fix no check for indent validity if set by a property

### 1.0.0

*Apr 26, 2025*

* Initial release
