PluggableUnplugged
==================

These classes contain static methods largely related to my PlugguableUnplugged
plugin.


UnpluggedStatic.php
-------------------

This class containst static methods used by the replacements for WordPress
functions in the `pluggable.php` core WordPress file.

WPCoreReplace.php
-----------------

Static methods meant to be usable in place of the core functions, but do not
replace the core functions. These methods do not support filters, at least not
at this time.

PunycodeState.php
-----------------

Methods for converting domains between ASCII punycode and UTF-8

Misc.php
--------

Miscelaneous methods.