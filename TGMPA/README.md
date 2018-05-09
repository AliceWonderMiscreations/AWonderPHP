TGMPA Branch
============

This branch is not ready for use.

Alice Wonder Miscreations is not the original author of the code here. The code
here is GPLv2 licensed, allowing me to modify it as long as the modifications
made are also GPLv2.

TGMPA is an extremely useful tool for theme and plugin developers that provides
capabilities I believe WordPress Core should have but does not have, namely the
ability for a theme or plugin upon activation to recommend other plugins that
compliment what is being activated.

Normally a theme or plugin author includes a copy of TGMPA in their theme or
plugin code, resulting in several sometimes incompatible versions running.

My preference is to put the code into namespaced classes that can be autoloaded
by a standard PSR-4 autoloader.

The classes here are intended for use with my plugins, they are not intended
for use by other plugin developers. Of course your theme and plugin can call
them, but I will not support technical issues and neither will the upstream
developer.

If you have a need for TGMPA in your own plugin or theme, I recommend you keep
using TGMPA the way the upstream author intends.

Visit the official site:
[TGM Plugin Activation](http://tgmpluginactivation.com/) for instructions.

This personal fork breaks the many classes in a single file into many files
each with a single class and puts stand alone functions into a class of static
methods.

The classes are namespaced and should not interfere with the official release
from the upstream innovator who created TGMPA.

Please note currently my method does not even work, this is a development
branch and is not even used by me.

The original version is in the file `class-tgm-plugin-activation.php.orig` and
has *not* been modified by me.

DEV CHANGELOG
=============

__Wed May 08, 2018 at roughly 4:05 AM UTC__
-------------------------------------------

Initial splitting of classes into individual files.

The following classes were NOT included because they deal with legacy which I
do not believe is an issue due to namespacing:

* TGM_Bulk_Installer
* TGM_Bulk_Installer_Skin

The functions outside of classes have not yet been dealt with.
Porting to PSR2 is only partially done.