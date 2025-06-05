Upgrade
#######

1.x to 2.0
==========

* ``*List*`` objects were renamed to ``*Array*`` to align with JSON terminology.

  * ``ListValue`` -> ``ArrayValue``
  * ``InlineList`` -> ``InlineArray``
  * ``CompactList`` -> ``CompactArray``
  * ``new Options(inlineListPadding: ...)`` -> ``new Options(inlineArrayPadding: ...)``
* Iterable wrappers (``ArrayValue``, ``ObjectValue``, ``InlineArray``, ``InlineObject``, ``CompactArray``, ``CompactObject``)
  no longer resolve ``JsonSerializable`` and ``Json5Serializable`` automatically.

  * ``ArrayValue::fromSerializable`` supports both ``JsonSerializable`` and ``Json5Serializable``.
  * ``ArrayValue::fromJsonSerializable`` supports only ``JsonSerializable``.
