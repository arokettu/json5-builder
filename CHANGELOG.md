# Changelog

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

### 1.0.1

*Apr 27, 2025*

* Encode an empty list as `[]` and an empty object as `{}`
* Fix no check for indent validity if set by a property

### 1.0.0

*Apr 26, 2025*

* Initial release
