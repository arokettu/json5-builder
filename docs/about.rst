About the Library
#################

Project Goal
============

First, what are not the goals:

* This is not a parser, there are good implementations already.
  I recommend `colinodell/json5 <colinodell_json5_>`_.
* This project for now does not allow modifications of existing configs.
  JSON5 files, like YAML and TOML, must be written by a human,
  so any automated tool must also preserve all custom formatting when possible.
  I want to solve a smaller problem for now.

.. _colinodell_json5: https://packagist.org/packages/colinodell/json5

The main goal of this project is to create a tool that allows developers to generate a pretty initial config for their users.
A side goal is so this tool can also be used to prettify existing JSON configs.

JSON5
=====

.. note:: https://json5.org

JSON5 is a file format best suited for human readable and maintainable configs.
It is a superset of JSON and builds on its strengths, adding several useful features:

* A superset of JSON (like YAML\ [1]_)
* A superset of JSONC (non-intended but useful feature)
* A subset of JavaScript ES5.1
* Formal spec (unlike JSONC)
* Relatively easy to parse
* Supports comments (the must-have)
* Unquoted keys that declutter config a lot visually
* Trailing commas
* Multiline strings

.. [1] A feature not supported by `symfony/yaml <symfony_yaml_>`_ by the way.
.. _symfony_yaml: https://symfony.com/doc/current/components/yaml.html

JSONC
=====

.. note:: https://code.visualstudio.com/docs/languages/json#_json-with-comments

JSONC or JSON with Comments is just it, a JSON that allows comment blocks.
It is not a well-defined standard but it is widely used so it was implemented here too.
